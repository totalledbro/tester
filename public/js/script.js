const navbarMenu = document.querySelector(".navbar .links");
const hamburgerBtn = document.querySelector(".hamburger-btn");
const hideMenuBtn = navbarMenu.querySelector(".close-btn");
const showPopupBtn = document.querySelector(".login-btn");
const formPopup = document.querySelector(".form-popup");
const hidePopupBtn = formPopup.querySelector(".close-btn");
const signupLoginLink = formPopup.querySelectorAll(".bottom-link a");
const forgotPasswordLink = formPopup.querySelector(".forgot-pass-link");

// Show mobile menu
hamburgerBtn.addEventListener("click", () => {
    navbarMenu.classList.toggle("show-menu");
});

// Hide mobile menu
hideMenuBtn.addEventListener("click", () => hamburgerBtn.click());

// Show login popup
showPopupBtn.addEventListener("click", () => {
    document.body.classList.toggle("show-popup");
});

// Hide login popup
hidePopupBtn.addEventListener("click", () => showPopupBtn.click());

// Show or hide signup form
signupLoginLink.forEach(link => {
    link.addEventListener("click", (e) => {
        e.preventDefault();
        if (link.id === 'signup-link') {
            formPopup.classList.add("show-signup");
            formPopup.classList.remove("show-forgot-password");
        } else if (link.id === 'login-link') {
            formPopup.classList.remove("show-signup");
            formPopup.classList.remove("show-forgot-password");
        }
    });
});

// Show or hide forgot password form
forgotPasswordLink.addEventListener("click", (e) => {
    e.preventDefault();
    formPopup.classList.add("show-forgot-password");
    formPopup.classList.remove("show-signup");
});
