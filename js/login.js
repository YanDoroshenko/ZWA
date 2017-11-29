// Login input validation
const login = document.getElementById("login");
const loginFeedback = document.getElementById("login-feedback");
login.removeAttribute("required");
login.removeAttribute("pattern");

login.addEventListener("blur", (e) => {
    if (login.value.search(/^[a-zA-Z]+[a-zA-Z0-9]*$/) != -1) {
        login.classList.add("correct");
        login.classList.remove("incorrect");
        loginFeedback.classList.add("correct");
        loginFeedback.classList.remove("incorrect");
        loginFeedback.innerHTML = "\u2714";
    }
    else {
        login.classList.add("incorrect");
        login.classList.remove("correct");
        loginFeedback.classList.add("incorrect");
        loginFeedback.classList.remove("correct");
        loginFeedback.innerHTML = "\u2716 Login should start with a letter and consist only of letters and digits";
    }
});

// Password validation
const password = document.getElementById("password");
const passwordFeedback = document.getElementById("password-feedback");
password.removeAttribute("required");
password.removeAttribute("pattern");

password.addEventListener("blur", (e) => {
    if (password.value.search(/.{5,}/) != -1) {
        password.classList.add("correct");
        password.classList.remove("incorrect");
        passwordFeedback.classList.add("correct");
        passwordFeedback.classList.remove("incorrect");
        passwordFeedback.innerHTML = "\u2714";
    }
    else {
        password.classList.add("incorrect");
        password.classList.remove("correct");
        passwordFeedback.classList.add("incorrect");
        passwordFeedback.classList.remove("correct");
        passwordFeedback.innerHTML = "\u2716 Your password must be at least 5 characters long";
    }
});

// Entire form validation before submit
const form = document.getElementById("form-login");
form.addEventListener("submit", (e) => {
    if (login.value.search(/^[a-zA-Z]+[a-zA-Z0-9]*/) == -1) {
        e.preventDefault();
        login.classList.add("incorrect");
        login.classList.remove("correct");
        loginFeedback.classList.add("incorrect");
        loginFeedback.classList.remove("correct");
        loginFeedback.innerHTML = "\u2716 Login should start with a letter and consist only of letters and digits";
    }
    if (password.value.search(/.{5,}/) == -1) {
        e.preventDefault();
        password.classList.add("incorrect");
        password.classList.remove("correct");
        passwordFeedback.classList.add("incorrect");
        passwordFeedback.classList.remove("correct");
        passwordFeedback.innerHTML = "\u2716 Your password must be at least 5 characters long";
    }
});
