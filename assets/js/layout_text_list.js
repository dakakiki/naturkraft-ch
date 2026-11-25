// layout_text_list.js – layout_text_list

document.addEventListener("DOMContentLoaded", function () {
    const lists = document.querySelectorAll("[data-nk-accordion]");

    if (!lists.length) return;

    let refreshTimer = null;

    function refreshTriggers() {
        window.clearTimeout(refreshTimer);

        refreshTimer = window.setTimeout(function () {
            const globals = window.gsap && window.gsap.core && window.gsap.core.globals ? window.gsap.core.globals() : null;

            if (globals && globals.ScrollTrigger) globals.ScrollTrigger.refresh();
            if (typeof window.revealVisiblePending === "function") window.revealVisiblePending();
        }, 400);
    }

    lists.forEach(function (list) {
        list.querySelectorAll(".text-list__item").forEach(function (item) {
            const toggle = item.querySelector(".text-list__toggle");
            const panel = toggle ? document.getElementById(toggle.getAttribute("aria-controls")) : null;

            if (!toggle || !panel) return;

            toggle.addEventListener("click", function () {
                const open = toggle.getAttribute("aria-expanded") !== "true";

                toggle.setAttribute("aria-expanded", open ? "true" : "false");
                item.classList.toggle("is-open", open);

                refreshTriggers();
            });
        });

        list.classList.add("is-accordion");
    });
});
