// gsap.js

import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

window.gsap = gsap;

const reduceMotionQuery = window.matchMedia("(prefers-reduced-motion: reduce)");

function prefersReducedMotion() {
    return reduceMotionQuery.matches;
}

const pendingReveals = [];

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
    document.querySelectorAll("[data-animate-now]").forEach(function (scope) {
        window.initFadeUpAnimations(scope, true);
    });

    window.initFadeUpAnimations(document, false);
    window.initCardReveal(document, false);

    ScrollTrigger.refresh();

    window.__animationsReady = true;

    revealVisiblePending();
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initializeScrollAnimations);
} else {
    initializeScrollAnimations();
}

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
