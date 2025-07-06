document.addEventListener("DOMContentLoaded", () => {
    const togglePassword = document.getElementById("togglePassword");
    if (togglePassword) {
        togglePassword.addEventListener("click", function () {
            const passwordInput = document.getElementById("password2");
            const eyeIcon = document.getElementById("eyeIcon");

            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";

            eyeIcon.classList.toggle("bi-eye");
            eyeIcon.classList.toggle("bi-eye-slash");
        });
    }

    const toggleOldPassword = document.getElementById("toggleOldPassword");
    if (toggleOldPassword) {
        toggleOldPassword.addEventListener("click", function () {
            const passwordInput = document.getElementById("old_password");
            const eyeIcon = document.getElementById("eyeIconOld");

            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";
            eyeIcon.classList.toggle("bi-eye");
            eyeIcon.classList.toggle("bi-eye-slash");
        });
    }

    const toggleNewPassword = document.getElementById("toggleNewPassword");
    if (toggleNewPassword) {
        toggleNewPassword.addEventListener("click", function () {
            const passwordInput = document.getElementById("new_password");
            const eyeIcon = document.getElementById("eyeIconNew");

            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";
            eyeIcon.classList.toggle("bi-eye");
            eyeIcon.classList.toggle("bi-eye-slash");
        });
    }

    const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");
    if (toggleConfirmPassword) {
        toggleConfirmPassword.addEventListener("click", function () {
            const passwordInput = document.getElementById("confirm_password");
            const eyeIcon = document.getElementById("eyeIconConfirm");

            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";
            eyeIcon.classList.toggle("bi-eye");
            eyeIcon.classList.toggle("bi-eye-slash");
        });
    }
});
