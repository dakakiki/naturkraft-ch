// layout_contact.js – layout_contact

document.addEventListener("DOMContentLoaded", function () {
    const AREA = "[name='bereich[]']";

    const forms = Array.from(document.querySelectorAll("form.nk-form, .nk-form form, form.wpcf7-form"))
        .filter(function (form) { return form.querySelector(AREA); });

    if (!forms.length) return;

    forms.forEach(function (form, index) {
        const select = enhanceSelect(form, index);

        wireHint(form);
        guardDoubleSubmit(form);

        form.addEventListener("reset", function () {
            window.setTimeout(function () {
                if (select) select.sync();
                updateHint(form);
            }, 0);
        });
    });

    document.addEventListener("click", function (e) {
        const link = e.target.closest("[data-nk-bereich]");

        if (!link) return;

        const value = link.getAttribute("data-nk-bereich");
        const form = forms[0];
        const box = Array.from(form.querySelectorAll(AREA))
            .find(function (b) { return b.value === value; });

        if (!box || box.checked) return;

        box.checked = true;
        box.dispatchEvent(new Event("change", { bubbles: true }));
    });

    function enhanceSelect(form, index) {
        const field = form.querySelector("[data-nk-select]");
        const group = field ? field.querySelector(".wpcf7-checkbox") : null;

        if (!field || !group) return null;

        const boxes = Array.from(group.querySelectorAll("input[type='checkbox']"));
        const placeholder = field.getAttribute("data-nk-select") || "Bereich";
        const name = placeholder.replace(/\s*\*$/, "");
        const legend = field.querySelector(".nk-form__legend");
        const wrap = field.querySelector(".wpcf7-form-control-wrap");

        const panelId = "nk-bereich-panel-" + index;
        const toggleId = "nk-bereich-toggle-" + index;

        group.setAttribute("role", "group");

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

        const chosen = function () {
            return boxes.filter(function (b) { return b.checked; });
        };

        const isOpen = function () {
            return field.classList.contains("is-open");
        };

        const sync = function () {
            const values = chosen().map(function (b) { return b.value; });
            const label = values.length ? values.join(", ") : placeholder;

            valueEl.textContent = label;
            valueEl.classList.toggle("is-placeholder", !values.length);

            toggle.setAttribute("aria-label", values.length ? name + ": " + label : placeholder);

            if (values.length) clearError();
        };

        const open = function (focusChoice) {
            field.classList.add("is-open");
            toggle.setAttribute("aria-expanded", "true");

            if (focusChoice) {
                (chosen()[0] || boxes[0]).focus();
            }
        };

        const close = function (returnFocus) {
            if (!isOpen()) return;

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
            if (isOpen()) {
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

        boxes.forEach(function (box, i) {
            box.addEventListener("change", sync);

            box.addEventListener("keydown", function (e) {
                let next = null;

                if (e.key === "ArrowDown" || e.key === "ArrowRight") {
                    next = boxes[(i + 1) % boxes.length];
                } else if (e.key === "ArrowUp" || e.key === "ArrowLeft") {
                    next = boxes[(i - 1 + boxes.length) % boxes.length];
                } else if (e.key === "Home") {
                    next = boxes[0];
                } else if (e.key === "End") {
                    next = boxes[boxes.length - 1];
                } else if (e.key === "Enter") {
                    e.preventDefault();
                    close(true);
                    return;
                }

                if (next) {
                    e.preventDefault();
                    next.focus();
                }
            });
        });

        field.addEventListener("keydown", function (e) {
            if (e.key === "Escape" && isOpen()) {
                e.preventDefault();
                close(true);
            }
        });

        document.addEventListener("click", function (e) {
            if (!field.contains(e.target)) close(false);
        });

        field.addEventListener("focusout", function (e) {
            const next = e.relatedTarget;

            if (next && !field.contains(next) && !next.contains(field)) close(false);
        });

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

        const fallback = hint.getAttribute("data-default");
        const texts = [];

        form.querySelectorAll(AREA + ":checked").forEach(function (box) {
            const text = hints[box.value] || fallback;

            if (text && texts.indexOf(text) === -1) texts.push(text);
        });

        const text = texts.length ? texts.join(" ") : fallback;

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
            if (e.target && e.target.matches(AREA)) updateHint(form);
        });

        updateHint(form);
    }

    function guardDoubleSubmit(form) {
        const submit = form.querySelector("[type='submit']");

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

        new MutationObserver(function () {
            if (form.getAttribute("data-status") !== "submitting") {
                submit.disabled = false;
            }
        }).observe(form, { attributes: true, attributeFilter: ["data-status"] });
    }
});
