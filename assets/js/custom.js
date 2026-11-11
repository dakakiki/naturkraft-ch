/*
 * naturKRAFT - site behaviour
 *
 * 1. In-page anchors: smooth scroll that lands under the sticky header
 * 2. Navigation panel (below the desktop breakpoint)
 * 3. Header shadow, back-to-top button, active menu item
 */


/* ------------------------------------------------------------------
 * 1. In-page anchors
 *
 * Every link to a #hash on this page scrolls there; a link to a #hash on
 * the front page from another page goes there and then scrolls. Lazy images
 * further down move the target while the page settles, so the jump waits
 * until the document height stops changing and then corrects itself for a
 * moment (from the KurtFreyAG starter, where this was measured).
 * ------------------------------------------------------------------ */
document.addEventListener("DOMContentLoaded", function () {

    const STORAGE_KEY = "nkPendingAnchor";

    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

    const headerOffset = function (target) {

        /* A target's own scroll-margin-top wins - the CSS sets it to the header height. */
        if (target) {
            const declared = parseFloat(window.getComputedStyle(target).scrollMarginTop);

            if (declared > 0) return declared;
        }

        const header = document.querySelector(".site-header");

        return header ? header.getBoundingClientRect().height : 0;
    };

    /* Where the target will rest - minus any reveal transform still applied. */
    const restingTop = function (el) {

        const rect = el.getBoundingClientRect();
        const transform = window.getComputedStyle(el).transform;

        if (!transform || transform === "none") return rect.top;

        const values = transform.match(/matrix3?d?\(([^)]+)\)/);

        if (!values) return rect.top;

        const parts = values[1].split(",").map(parseFloat);
        const ty = parts.length === 16 ? parts[13] : parts[5];

        return isNaN(ty) ? rect.top : rect.top - ty;
    };

    const wantedY = function (hash) {

        let target = null;

        try {
            target = document.querySelector(hash);
        } catch (e) {
            return null;
        }

        if (!target) return null;

        if (target === document.body || hash === "#top") return 0;

        const doc = document.documentElement;
        const max = Math.max(0, doc.scrollHeight - window.innerHeight);

        return Math.min(
            Math.max(0, restingTop(target) + window.pageYOffset - headerOffset(target)),
            max
        );
    };

    const smoothScrollTo = function (targetY, duration) {

        if (reduceMotion.matches) {
            window.scrollTo(0, targetY);
            return;
        }

        const startY = window.pageYOffset;
        const distance = targetY - startY;
        let startTime = null;

        const ease = function (t) {
            return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
        };

        const step = function (now) {

            if (!startTime) startTime = now;

            const progress = Math.min((now - startTime) / duration, 1);

            window.scrollTo(0, startY + distance * ease(progress));

            if (progress < 1) requestAnimationFrame(step);
        };

        requestAnimationFrame(step);
    };

    let holdToken = 0;

    /*
     * Scroll, then hold the position while late content (lazy images, fonts)
     * still moves the target. Any user scroll cancels the hold.
     */
    const scrollToHash = function (hash, smooth) {

        if (!hash || hash === "#") return;

        holdToken++;

        const myToken = holdToken;
        let userScrolled = false;

        const noteUserScroll = function () {
            userScrolled = true;
        };

        requestAnimationFrame(function () {
            /* pointerdown covers dragging the scrollbar, which fires none of the others. */
            ["wheel", "touchstart", "keydown", "pointerdown"].forEach(function (type) {
                window.addEventListener(type, noteUserScroll, { once: true, passive: true });
            });
        });

        const first = wantedY(hash);

        if (first === null) return;

        const duration = smooth ? Math.min(900, 300 + Math.abs(first - window.pageYOffset) * 0.15) : 0;

        if (smooth && duration > 0) {
            smoothScrollTo(first, duration);
        } else {
            window.scrollTo(0, first);
        }

        let checks = 0;

        const correct = function () {

            if (userScrolled || myToken !== holdToken || checks > 15) return;

            checks++;

            const wanted = wantedY(hash);

            if (wanted !== null && Math.abs(wanted - window.pageYOffset) > 2) {
                window.scrollTo(0, wanted);
            }

            window.setTimeout(correct, 120);
        };

        window.setTimeout(correct, duration + 60);
    };

    window.nkScrollToHash = scrollToHash;

    const samePath = function (url) {
        const here = window.location.pathname.replace(/\/+$/, "") || "/";
        const there = url.pathname.replace(/\/+$/, "") || "/";

        return url.origin === window.location.origin && here === there;
    };

    document.addEventListener("click", function (e) {

        if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

        const link = e.target.closest("a[href*='#']");

        if (!link || link.target === "_blank" || link.classList.contains("skip-link")) return;

        const href = link.getAttribute("href");
        let url;

        try {
            url = new URL(href, window.location.href);
        } catch (err) {
            return;
        }

        const hash = url.hash;

        if (!hash || hash === "#") return;

        if (samePath(url)) {

            let target = null;

            try {
                target = document.querySelector(hash);
            } catch (err) {
                return;
            }

            if (!target) return;

            e.preventDefault();

            if (window.location.hash !== hash) {
                history.pushState(null, "", hash);
            }

            scrollToHash(hash, true);

            /* Keyboard users continue from the target, not from the menu. */
            if (hash !== "#top" && !target.hasAttribute("tabindex")) {
                target.setAttribute("tabindex", "-1");
            }

            if (hash === "#top") {
                const skip = document.querySelector(".skip-link");

                if (skip) skip.focus({ preventScroll: true });
            } else {
                target.focus({ preventScroll: true });
            }

            return;
        }

        /* Another page's anchor: remember it, so the jump runs once that page has settled. */
        if (url.origin === window.location.origin) {
            try {
                sessionStorage.setItem(STORAGE_KEY, hash);
            } catch (err) {
                /* Storage blocked - the browser's own jump still works. */
            }
        }
    });

    const initialHash = (function () {
        try {
            const stored = sessionStorage.getItem(STORAGE_KEY);

            sessionStorage.removeItem(STORAGE_KEY);

            if (stored && stored === window.location.hash) return stored;
        } catch (err) {
            /* ignore */
        }

        return window.location.hash;
    })();

    if (initialHash && initialHash.length > 1) {

        const run = function () {
            scrollToHash(initialHash, false);
        };

        if (document.readyState === "complete") {
            requestAnimationFrame(run);
        } else {
            window.addEventListener("load", run, { once: true });
        }
    }

    window.addEventListener("popstate", function () {
        if (window.location.hash) scrollToHash(window.location.hash, true);
    });
});


/* ------------------------------------------------------------------
 * 2. Navigation panel
 * ------------------------------------------------------------------ */
document.addEventListener("DOMContentLoaded", function () {

    const toggle = document.querySelector(".site-header__toggle");
    const panel = document.getElementById("site-nav");

    if (!toggle || !panel) return;

    const desktop = window.matchMedia("(min-width: 1100px)");

    const FOCUSABLE = "a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex='-1'])";

    let open = false;

    const setOpen = function (state, returnFocus) {

        open = state;

        panel.classList.toggle("is-open", state);
        document.body.classList.toggle("has-menu-open", state);

        toggle.setAttribute("aria-expanded", state ? "true" : "false");
        toggle.setAttribute("aria-label", state ? toggle.dataset.labelClose : toggle.dataset.labelOpen);

        if (state) {
            const first = panel.querySelector(FOCUSABLE);

            if (first) first.focus({ preventScroll: true });
        } else if (returnFocus) {
            toggle.focus({ preventScroll: true });
        }
    };

    toggle.addEventListener("click", function () {
        setOpen(!open, true);
    });

    /* Following a link closes the panel; the anchor handler does the scrolling. */
    panel.addEventListener("click", function (e) {
        if (open && e.target.closest("a")) setOpen(false, false);
    });

    document.addEventListener("keydown", function (e) {

        if (!open) return;

        if (e.key === "Escape") {
            setOpen(false, true);
            return;
        }

        if (e.key !== "Tab") return;

        /* Keep focus inside the panel and its toggle while it covers the page. */
        const items = [toggle].concat(Array.from(panel.querySelectorAll(FOCUSABLE)));
        const first = items[0];
        const last = items[items.length - 1];

        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    });

    /* Crossing to the desktop layout with the panel open. */
    desktop.addEventListener("change", function (e) {
        if (e.matches && open) setOpen(false, false);
    });
});


/* ------------------------------------------------------------------
 * 3. Header shadow, back to top, active menu item
 * ------------------------------------------------------------------ */
document.addEventListener("DOMContentLoaded", function () {

    const header = document.querySelector(".site-header");
    const footer = document.querySelector(".site-footer");
    const toTop = document.querySelector(".to-top");

    let ticking = false;

    const update = function () {

        ticking = false;

        const y = window.pageYOffset;

        if (header) header.classList.toggle("is-scrolled", y > 4);

        if (!toTop) return;

        toTop.classList.toggle("is-visible", y > window.innerHeight * 0.8);

        /* Once the footer scrolls in, the button rides above it instead of covering its links. */
        if (footer) {
            const overlap = window.innerHeight - footer.getBoundingClientRect().top;

            /* margin, not transform - the fade-in already animates transform. */
            toTop.style.marginBottom = overlap > 0 ? overlap + "px" : "";
        }
    };

    window.addEventListener("resize", update, { passive: true });

    window.addEventListener("scroll", function () {
        if (!ticking) {
            ticking = true;
            requestAnimationFrame(update);
        }
    }, { passive: true });

    update();

    /* Active menu item: the section whose band covers the line under the header. */
    const links = Array.from(document.querySelectorAll(".site-nav__list a[href*='#']"));

    if (!links.length || !("IntersectionObserver" in window)) return;

    const sections = [];

    links.forEach(function (link) {

        let url;

        try {
            url = new URL(link.getAttribute("href"), window.location.href);
        } catch (e) {
            return;
        }

        const here = window.location.pathname.replace(/\/+$/, "") || "/";
        const there = url.pathname.replace(/\/+$/, "") || "/";

        if (here !== there || !url.hash) return;

        const section = document.getElementById(decodeURIComponent(url.hash.slice(1)));

        if (section) sections.push({ section: section, item: link.closest("li") });
    });

    if (!sections.length) return;

    const setActive = function (item) {
        sections.forEach(function (entry) {
            entry.item.classList.toggle("is-active", entry.item === item);

            const a = entry.item.querySelector("a");

            if (entry.item === item) {
                a.setAttribute("aria-current", "location");
            } else {
                a.removeAttribute("aria-current");
            }
        });
    };

    const observer = new IntersectionObserver(function (entries) {

        entries.forEach(function (entry) {

            if (!entry.isIntersecting) return;

            const match = sections.find(function (s) { return s.section === entry.target; });

            if (match) setActive(match.item);
        });

        /* Above the first tracked section nothing is active. */
        const first = sections[0].section.getBoundingClientRect().top;

        if (first > window.innerHeight * 0.4) setActive(null);

    }, { rootMargin: "-40% 0px -55% 0px" });

    sections.forEach(function (s) { observer.observe(s.section); });
});
