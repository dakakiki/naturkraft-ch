// assets/js/gsap.js - scroll reveals (bundled with GSAP by esbuild)

import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

window.gsap = gsap;

const reduceMotionQuery = window.matchMedia("(prefers-reduced-motion: reduce)");

function prefersReducedMotion() {
    return reduceMotionQuery.matches;
}

/**
 * Prepared reveals that have not played yet - swept after a tab or bfcache
 * restore, when ScrollTrigger may hold stale positions.
 */
const pendingReveals = [];

/**
 * One reveal.
 *
 * Start state via gsap.set(), reveal via a plain gsap.to() from a one-shot
 * trigger: a gsap.from() that owns a ScrollTrigger is reverted on every
 * refresh, which can push already visible content back to opacity 0.
 */
function createReveal(targets, trigger, from, options) {

    const opts = options || {};
    const items = Array.isArray(targets) ? targets : [targets];

    if (!items.length || prefersReducedMotion()) return;

    gsap.set(items, Object.assign({ autoAlpha: 0 }, from));

    let played = false;

    const entry = { trigger: trigger, play: null };

    const play = function () {

        if (played) return;

        played = true;

        const index = pendingReveals.indexOf(entry);

        if (index > -1) pendingReveals.splice(index, 1);

        gsap.to(items, {
            autoAlpha: 1,
            x: 0,
            y: 0,
            duration: opts.duration || 0.9,
            stagger: opts.stagger || 0,
            ease: "power2.out",
            overwrite: "auto",
            clearProps: "opacity,visibility,transform"
        });
    };

    entry.play = play;

    if (opts.playNow) {
        play();
        return;
    }

    pendingReveals.push(entry);

    ScrollTrigger.create({
        trigger: trigger,
        start: opts.start || "top bottom-=60",
        once: true,
        onEnter: play
    });
}

function revealVisiblePending() {

    if (!pendingReveals.length) return;

    const viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    pendingReveals.slice().forEach(function (entry) {

        if (!entry.trigger || !entry.trigger.isConnected) {
            entry.play();
            return;
        }

        if (entry.trigger.getBoundingClientRect().top < viewportHeight) {
            entry.play();
        }
    });
}

window.revealVisiblePending = revealVisiblePending;

/**
 * .fade-up items reveal on their own; inside a .group-fade-up they reveal
 * together, staggered, when the group enters.
 */
window.initFadeUpAnimations = function (scope, animateNow) {

    scope = scope || document;

    const name = "fade-up";
    const groupName = "group-fade-up";
    const initializedClass = name + "-initialized";
    const itemSelector = "." + name + ":not(." + initializedClass + ")";

    const groups = Array.from(scope.querySelectorAll("." + groupName));

    if (scope.classList && scope.classList.contains(groupName)) groups.unshift(scope);

    groups.forEach(function (group) {

        const items = Array.from(group.querySelectorAll(itemSelector))
            .filter(function (item) { return item.closest("." + groupName) === group; });

        if (!items.length) return;

        items.forEach(function (item) { item.classList.add(initializedClass); });

        createReveal(items, group, { y: 32 }, { stagger: 0.15, playNow: animateNow });
    });

    const singles = Array.from(scope.querySelectorAll(itemSelector));

    if (scope.matches && scope.matches(itemSelector)) singles.unshift(scope);

    singles.forEach(function (item) {
        item.classList.add(initializedClass);
        createReveal([item], item, { y: 32 }, { playNow: animateNow });
    });
};

/**
 * Image rows: the children reveal one after the other.
 */
window.initCardReveal = function (scope, animateNow) {

    scope = scope || document;

    const grids = Array.from(scope.querySelectorAll("[data-gsap-cards]"));

    grids.forEach(function (grid) {

        const cards = Array.from(grid.children).filter(function (card) {
            return !card.classList.contains("cards-initialized");
        });

        if (!cards.length) return;

        cards.forEach(function (card) { card.classList.add("cards-initialized"); });

        createReveal(cards, grid, { y: 32 }, {
            duration: 0.7,
            stagger: 0.12,
            playNow: animateNow
        });
    });
};

function initializeScrollAnimations() {

    /* Above the fold: play at once instead of waiting for a trigger. */
    document.querySelectorAll("[data-animate-now]").forEach(function (scope) {
        window.initFadeUpAnimations(scope, true);
    });

    window.initFadeUpAnimations(document, false);
    window.initCardReveal(document, false);

    ScrollTrigger.refresh();

    /* Only now may the CSS keep hiding things - the bundle ran to the end. */
    window.__animationsReady = true;

    revealVisiblePending();
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initializeScrollAnimations);
} else {
    initializeScrollAnimations();
}

/* Positions are measured before images and fonts settle - re-measure. */
window.addEventListener("load", function () {
    ScrollTrigger.refresh();
    revealVisiblePending();
});

if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(function () {
        ScrollTrigger.refresh();
        revealVisiblePending();
    });
}

function recoverAfterRestore() {

    if (document.hidden) return;

    ScrollTrigger.refresh();

    requestAnimationFrame(function () {
        requestAnimationFrame(function () {
            ScrollTrigger.refresh();
            revealVisiblePending();
        });
    });
}

document.addEventListener("visibilitychange", recoverAfterRestore);
window.addEventListener("pageshow", recoverAfterRestore);
window.addEventListener("orientationchange", recoverAfterRestore);
