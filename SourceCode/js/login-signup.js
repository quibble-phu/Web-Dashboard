const container = document.getElementById('container');
const signUpBtn = document.getElementById('signUp');
const signInBtn = document.getElementById('signIn');

const navbarLoginBtn = document.getElementById('navbarLogin');
const navbarSignupBtn = document.getElementById('navbarSignup');

signUpBtn.addEventListener('click', () => {
    container.classList.add("right-panel-active");
});

signInBtn.addEventListener('click', () => {
    container.classList.remove("right-panel-active");
});


 navbarLoginBtn.addEventListener('click', () => {
    container.classList.remove("right-panel-active");
});

navbarSignupBtn.addEventListener('click', () => {
    container.classList.add("right-panel-active");
});

document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const action = urlParams.get('action');
    const container = document.getElementById('container');

    if (action === 'signup') {
        container.classList.add('right-panel-active');
    } else {
        container.classList.remove('right-panel-active');
    }

    const signUpBtn = document.getElementById('signUp');
    const signInBtn = document.getElementById('signIn');

    signUpBtn.addEventListener('click', () => {
        container.classList.add("right-panel-active");
    });

    signInBtn.addEventListener('click', () => {
        container.classList.remove("right-panel-active");
    });
});
