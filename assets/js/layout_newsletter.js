/*
 * Newsletter sign-up
 *
 * Sends the form with fetch to the same admin-post handler the plain form
 * posts to, and shows the section's message for the returned status
 * (success | already | invalid | error) without leaving the page.
 */
document.addEventListener("DOMContentLoaded", function () {

    const forms = document.querySelectorAll("[data-nk-newsletter]");

    forms.forEach(function (form) {

        const input = form.querySelector("input[type='email']");
        const button = form.querySelector("[type='submit']");
        const output = form.querySelector(".newsletter__message");

        if (!input || !output) return;

        /* Our messages replace the browser's validation bubbles. */
        form.setAttribute("novalidate", "");

        let busy = false;

        const show = function (status) {

            const text = form.getAttribute("data-msg-" + status) || "";

            output.setAttribute("data-status", status);
            output.textContent = text;

            if (status === "invalid") {
                input.setAttribute("aria-invalid", "true");
                input.setAttribute("aria-describedby", output.id || (output.id = input.id + "-message"));
                input.focus();
            } else {
                input.removeAttribute("aria-invalid");
            }
        };

        input.addEventListener("input", function () {
            if (input.getAttribute("aria-invalid") === "true" && input.validity.valid) {
                input.removeAttribute("aria-invalid");
            }
        });

        form.addEventListener("submit", function (e) {

            e.preventDefault();

            if (busy) return;

            input.value = input.value.trim();

            if (!input.value || !input.validity.valid) {
                show("invalid");
                return;
            }

            busy = true;
            form.classList.add("is-busy");

            if (button) button.disabled = true;

            const data = new FormData(form);

            data.append("ajax", "1");

            fetch(form.action, {
                method: "POST",
                body: data,
                credentials: "same-origin",
                headers: { Accept: "application/json" }
            })
                .then(function (response) {
                    return response.json().catch(function () {
                        return { status: "error" };
                    });
                })
                .then(function (json) {

                    const status = json && json.status ? json.status : "error";

                    show(status);

                    if (status === "success" || status === "already") {
                        form.reset();
                    }
                })
                .catch(function () {
                    show("error");
                })
                .finally(function () {
                    busy = false;
                    form.classList.remove("is-busy");

                    if (button) button.disabled = false;
                });
        });
    });
});
