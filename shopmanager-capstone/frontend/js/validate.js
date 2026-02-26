/* =============================================================================
   js/validate.js  –  Client-side form validation
   Runs after DOMContentLoaded.  All validation is SUPPLEMENTARY to the
   server-side checks in PHP — never rely on JS alone.
   ============================================================================= */
(function () {
    'use strict';

    // ── Helpers ─────────────────────────────────────────────────────────────

    /** Show an error message below an input */
    function showError(input, message) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');

        let feedback = input.nextElementSibling;
        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            input.parentNode.insertBefore(feedback, input.nextSibling);
        }
        feedback.textContent = message;
    }

    /** Clear the error state on an input */
    function clearError(input) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = '';
        }
    }

    /** Basic email regex (RFC 5322 simplified) */
    const EMAIL_RE = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;

    // ── Live-clear errors on input ──────────────────────────────────────────
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('is-invalid')) {
            clearError(e.target);
        }
    });

    // ── Login form ──────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {

        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function (e) {
                let valid = true;

                const email    = loginForm.querySelector('#loginEmail');
                const password = loginForm.querySelector('#loginPassword');

                if (!email.value.trim() || !EMAIL_RE.test(email.value.trim())) {
                    showError(email, 'Please enter a valid email address.');
                    valid = false;
                }
                if (!password.value) {
                    showError(password, 'Password is required.');
                    valid = false;
                }

                if (!valid) e.preventDefault();
            });
        }

        // ── Register form ───────────────────────────────────────────────────
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', function (e) {
                let valid = true;

                const name     = registerForm.querySelector('#regName');
                const email    = registerForm.querySelector('#regEmail');
                const password = registerForm.querySelector('#regPassword');
                const confirm  = registerForm.querySelector('#regConfirm');

                if (name.value.trim().length < 2) {
                    showError(name, 'Name must be at least 2 characters.');
                    valid = false;
                }
                if (!EMAIL_RE.test(email.value.trim())) {
                    showError(email, 'Please enter a valid email address.');
                    valid = false;
                }
                if (password.value.length < 6) {
                    showError(password, 'Password must be at least 6 characters.');
                    valid = false;
                }
                if (password.value !== confirm.value) {
                    showError(confirm, 'Passwords do not match.');
                    valid = false;
                }

                if (!valid) e.preventDefault();
            });
        }

        // ── Product form (create & edit) ────────────────────────────────────
        const productForm = document.getElementById('productForm');
        if (productForm) {
            productForm.addEventListener('submit', function (e) {
                let valid = true;

                const name     = productForm.querySelector('#productName');
                const price    = productForm.querySelector('#productPrice');
                const stock    = productForm.querySelector('#productStock');
                const category = productForm.querySelector('#productCategory');

                // Name: 3–200 chars
                if (!name.value.trim() || name.value.trim().length < 3 || name.value.trim().length > 200) {
                    showError(name, 'Product name must be between 3 and 200 characters.');
                    valid = false;
                }

                // Price: positive number
                const priceVal = parseFloat(price.value);
                if (isNaN(priceVal) || priceVal <= 0) {
                    showError(price, 'Price must be a positive number.');
                    valid = false;
                }

                // Stock: non-negative integer
                const stockVal = parseInt(stock.value, 10);
                if (isNaN(stockVal) || stockVal < 0) {
                    showError(stock, 'Stock must be 0 or a positive whole number.');
                    valid = false;
                }

                // Category: must be selected
                if (!category.value) {
                    showError(category, 'Please select a category.');\n                    valid = false;
                }

                // Image URL: if provided, must look like a URL
                const imageInput = productForm.querySelector('#productImage');
                if (imageInput && imageInput.value.trim() !== '') {
                    try {
                        new URL(imageInput.value.trim());
                    } catch (_) {
                        showError(imageInput, 'Please enter a valid URL, or leave blank.');
                        valid = false;
                    }
                }

                if (!valid) e.preventDefault();
            });
        }
    });

})();
