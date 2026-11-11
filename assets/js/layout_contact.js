/*
 * Contact form (CF7) enhancements
 *
 * 1. Area dropdown - the CF7 radio group becomes the XD dropdown: a button
 *    showing the chosen area, opening the group as a panel. It stays a real
 *    radio group (arrow keys, screen readers).
 * 2. Preselection - any [data-nk-bereich] link (collaboration rows, cards)
 *    selects its area; whatever was typed into the form stays.
 * 3. Area hints - the help text under "Anliegen" follows the area.
 * 4. Double sending - a second submit while one is running is ignored and
 *    the button is disabled until CF7 answers.
 */
document.addEventListener("DOMContentLoaded", function () {

    const forms = Array.from(document.querySelectorAll("form.nk-form, .nk-form form, form.wpcf7-form"))
        .filter(function (form) { return form.querySelector("[name='bereich']"); });

    if (!forms.length) return;

    forms.forEach(function (form, index) {
        const select = enhanceSelect(form, index);

        wireHint(form);
        guardDoubleSubmit(form);

        form.addEventListener("reset", function () {
            /* CF7 resets after a successful send - the button text has to follow. */
            window.setTimeout(function () {
                if (select) select.sync();
                form.querySelectorAll("[name='bereich']").forEach(function (radio) {
                    radio.dispatchEvent(new Event("nk:sync"));
                });
                updateHint(form);
            }, 0);
        });
    });

    /* 2. Preselection from call-to-action links anywhere on the page. */
    document.addEventListener("click", function (e) {

        const link = e.target.closest("[data-nk-bereich]");

        if (!link) return;

        const value = link.getAttribute("data-nk-bereich");
        const form = forms[0];
        const radio = Array.from(form.querySelectorAll("[name='bereich']"))
            .find(function (r) { return r.value === value; });

        if (!radio) return;

        /*
         * Focus is left to the anchor handler: it moves to the contact
         * section, so the next Tab lands on this very field.
         */
        radio.checked = true;
        radio.dispatchEvent(new Event("change", { bubbles: true }));
    });


    /* 1. Area dropdown */
    function enhanceSelect(form, index) {

        const field = form.querySelector("[data-nk-select]");
        const group = field ? field.querySelector(".wpcf7-radio") : null;

        if (!field || !group) return null;

        const radios = Array.from(group.querySelectorAll("input[type='radio']"));
        const placeholder = field.getAttribute("data-nk-select") || "Bereich";
        const legend = field.querySelector(".nk-form__legend");
        const wrap = field.querySelector(".wpcf7-form-control-wrap");

        const panelId = "nk-bereich-panel-" + index;
        const toggleId = "nk-bereich-toggle-" + index;

        group.setAttribute("role", "radiogroup");

        if (legend && legend.id) {
            group.setAttribute("aria-labelledby", legend.id);
        }

        wrap.id = panelId;

        const toggle = document.createElement("button");

        toggle.type = "button";
        toggle.id = toggleId;
        toggle.className = "nk-select__toggle";
        toggle.setAttribute("aria-expanded", "false");
        toggle.setAttribute("aria-controls", panelId);
        toggle.innerHTML =
            '<span class="nk-select__value is-placeholder"></span>' +
            '<span class="nk-select__arrow" aria-hidden="true">' +
            '<svg class="nk-icon" viewBox="0 0 1000 1000" width="22" height="22" focusable="false"><polyline fill="none" stroke="currentColor" stroke-width="60" points="225.37 621.03 500 346.39 774.63 621.03"/></svg>' +
            "</span>";

        const valueEl = toggle.querySelector(".nk-select__value");

        const error = document.createElement("span");

        error.className = "nk-select__error";
        error.id = "nk-bereich-error-" + index;
        error.setAttribute("aria-live", "polite");

        field.insertBefore(toggle, wrap);
        field.appendChild(error);
        field.classList.add("nk-select", "is-enhanced");

        let pointerChoice = false;

        const checked = function () {
            return radios.find(function (r) { return r.checked; }) || null;
        };

        const sync = function () {

            const current = checked();
            const label = current ? current.value : placeholder;

            valueEl.textContent = label;
            valueEl.classList.toggle("is-placeholder", !current);

            /* "Bereich *: Handel" - the label and the value in one announcement. */
            toggle.setAttribute("aria-label", current ? placeholder.replace(/\s*\*$/, "") + ": " + label : placeholder);

            if (current) clearError();
        };

        const open = function (focusChoice) {

            field.classList.add("is-open");
            toggle.setAttribute("aria-expanded", "true");

            if (focusChoice) {
                (checked() || radios[0]).focus();
            }
        };

        const close = function (returnFocus) {

            if (!field.classList.contains("is-open")) return;

            field.classList.remove("is-open");
            toggle.setAttribute("aria-expanded", "false");

            if (returnFocus) toggle.focus();
        };

        const clearError = function () {
            field.classList.remove("is-invalid");
            toggle.removeAttribute("aria-invalid");
            toggle.removeAttribute("aria-describedby");
            error.textContent = "";
        };

        toggle.addEventListener("click", function () {
            if (field.classList.contains("is-open")) {
                close(false);
            } else {
                open(true);
            }
        });

        toggle.addEventListener("keydown", function (e) {
            if (e.key === "ArrowDown" || e.key === "ArrowUp") {
                e.preventDefault();
                open(true);
            }
        });

        group.addEventListener("pointerdown", function () {
            pointerChoice = true;
        });

        radios.forEach(function (radio) {

            radio.addEventListener("change", function () {

                sync();

                /* A click picks and closes; arrow keys only move the choice. */
                if (pointerChoice) {
                    pointerChoice = false;
                    close(true);
                }
            });

            radio.addEventListener("nk:sync", sync);

            radio.addEventListener("keydown", function (e) {

                if (e.key === "Enter" || e.key === " ") {
                    /* Enter would submit the form from a radio button. */
                    e.preventDefault();

                    if (!radio.checked) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event("change", { bubbles: true }));
                    }

                    close(true);
                } else if (e.key === "Escape") {
                    e.preventDefault();
                    close(true);
                }
            });
        });

        /* Leaving the field - by click or by Tab - closes the panel. */
        document.addEventListener("click", function (e) {
            if (!field.contains(e.target)) close(false);
        });

        field.addEventListener("focusout", function (e) {
            if (e.relatedTarget && !field.contains(e.relatedTarget)) close(false);
        });

        /* CF7 marks the group invalid inside the closed panel - mirror it on the field. */
        form.closest(".wpcf7") && form.closest(".wpcf7").addEventListener("wpcf7invalid", function () {
            window.setTimeout(function () {

                const tip = field.querySelector(".wpcf7-not-valid-tip");

                if (group.classList.contains("wpcf7-not-valid") || tip) {
                    field.classList.add("is-invalid");
                    toggle.setAttribute("aria-invalid", "true");
                    toggle.setAttribute("aria-describedby", error.id);
                    error.textContent = tip ? tip.textContent : "";
                } else {
                    clearError();
                }
            }, 0);
        });

        sync();

        return { sync: sync };
    }


    /* 3. Hints */
    function hintElement(form) {
        return form.querySelector(".nk-form__hint");
    }

    function updateHint(form) {

        const hint = hintElement(form);

        if (!hint) return;

        let hints = {};

        try {
            hints = JSON.parse(hint.getAttribute("data-hints") || "{}");
        } catch (e) {
            hints = {};
        }

        const current = form.querySelector("[name='bereich']:checked");
        const text = current && hints[current.value] ? hints[current.value] : hint.getAttribute("data-default");

        if (text && hint.textContent !== text) hint.textContent = text;
    }

    function wireHint(form) {

        const hint = hintElement(form);
        const message = form.querySelector("[name='anliegen']");

        if (!hint) return;

        if (message && hint.id) {
            const described = (message.getAttribute("aria-describedby") || "").split(" ").filter(Boolean);

            if (described.indexOf(hint.id) === -1) described.push(hint.id);

            message.setAttribute("aria-describedby", described.join(" "));
        }

        form.addEventListener("change", function (e) {
            if (e.target && e.target.name === "bereich") updateHint(form);
        });

        updateHint(form);
    }


    /* 4. Double sending */
    function guardDoubleSubmit(form) {

        const submit = form.querySelector("[type='submit']");

        /* Capture phase - before CF7's own submit handler. */
        form.addEventListener("submit", function (e) {

            if (form.getAttribute("data-status") === "submitting" || form.classList.contains("submitting")) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return;
            }

            if (submit) {
                window.setTimeout(function () {
                    submit.disabled = true;
                }, 0);
            }
        }, true);

        if (!submit || !("MutationObserver" in window)) return;

        /* CF7 reports every state in data-status; anything but "submitting" frees the button. */
        new MutationObserver(function () {
            if (form.getAttribute("data-status") !== "submitting") {
                submit.disabled = false;
            }
        }).observe(form, { attributes: true, attributeFilter: ["data-status"] });
    }
});
