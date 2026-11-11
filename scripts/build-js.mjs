import fs from "fs";
import path from "path";
import { minify } from "terser";
import { build } from "esbuild";

const isProd = process.env.NODE_ENV === "production";
const themeRoot = process.cwd();

/**
 * JS whitelist - source → output.
 *
 * Layout scripts are enqueued only on pages that use the layout, see
 * inc/enqueue.php. Add a new entry here and there together.
 */
export const entries = [
  { in: "assets/js/custom.js", out: "dist/js/custom.min.js", bundle: false },
  { in: "assets/js/layout_contact.js", out: "dist/js/layout_contact.min.js", bundle: false },
  { in: "assets/js/layout_newsletter.js", out: "dist/js/layout_newsletter.min.js", bundle: false },

  { in: "assets/js/gsap.js", out: "dist/js/gsap.min.js", bundle: true },
];

function ensureDir(filePath) {
  fs.mkdirSync(path.dirname(filePath), { recursive: true });
}

async function buildWithTerser(inputRel, outputRel) {
  const inputPath = path.join(themeRoot, inputRel);
  const outputPath = path.join(themeRoot, outputRel);

  if (!fs.existsSync(inputPath)) {
    console.warn(`[skip] ${inputRel} not found`);
    return;
  }

  const code = fs.readFileSync(inputPath, "utf8");

  const result = await minify(code, {
    compress: isProd
      ? {
          passes: 2,
          drop_console: ["log", "info"],
          drop_debugger: true,
          dead_code: true,
        }
      : {
          passes: 1,
          dead_code: true,
        },
    mangle: {
      safari10: true,
    },
    format: {
      comments: false,
    },
  });

  if (!result.code) {
    throw new Error(`Minification failed for ${inputRel}`);
  }

  ensureDir(outputPath);
  fs.writeFileSync(outputPath, result.code, "utf8");

  console.log(`[js:${isProd ? "prod" : "dev"}] ${inputRel} → ${outputRel}`);
}

async function buildWithEsbuild(inputRel, outputRel) {
  const inputPath = path.join(themeRoot, inputRel);
  const outputPath = path.join(themeRoot, outputRel);

  if (!fs.existsSync(inputPath)) {
    console.warn(`[skip] ${inputRel} not found`);
    return;
  }

  ensureDir(outputPath);

  await build({
    entryPoints: [inputPath],
    outfile: outputPath,
    bundle: true,
    minify: true,
    sourcemap: !isProd,
    target: ["es2018"],
    format: "iife",
    platform: "browser",
    logLevel: "silent",
  });

  console.log(`[js:${isProd ? "prod" : "dev"}][bundle] ${inputRel} → ${outputRel}`);
}

async function run() {
  for (const entry of entries) {
    if (entry.bundle) {
      await buildWithEsbuild(entry.in, entry.out);
    } else {
      await buildWithTerser(entry.in, entry.out);
    }
  }
}

/* Only build when run directly, not when watch-js imports the entry list. */
if (process.argv[1] && path.resolve(process.argv[1]) === path.resolve(themeRoot, "scripts/build-js.mjs")) {
  run().catch((err) => {
    console.error(err);
    process.exit(1);
  });
}
