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
const password1 = document.getElementById("password1");
const password1Feedback = document.getElementById("password1-feedback");
password1.removeAttribute("required");
password1.removeAttribute("pattern");

password1.addEventListener("blur", (e) => {
    if (password1.value.search(/.{5,}/) != -1) {
        password1.classList.add("correct");
        password1.classList.remove("incorrect");
        password1Feedback.classList.add("correct");
        password1Feedback.classList.remove("incorrect");
        password1Feedback.innerHTML = "\u2714";
    }
    else {
        password1.classList.add("incorrect");
        password1.classList.remove("correct");
        password1Feedback.classList.add("incorrect");
        password1Feedback.classList.remove("correct");
        password1Feedback.innerHTML = "\u2716 Your password must be at least 5 characters long";
    }
});

// Password confirmation input validation
const password2 = document.getElementById("password2");
const password2Feedback = document.getElementById("password2-feedback");
password2.removeAttribute("required");
password2.removeAttribute("pattern");

password2.addEventListener("blur", (e) => {
    if (password2.value != password1.value) {
        password2.classList.add("incorrect");
        password2.classList.remove("correct");
        password2Feedback.classList.add("incorrect");
        password2Feedback.classList.remove("correct");
        password2Feedback.innerHTML = "\u2716 Confirmation does not match";
    }
    else {
        password2.classList.add("correct");
        password2.classList.remove("incorrect");
        password2Feedback.classList.add("correct");
        password2Feedback.classList.remove("incorrect");
        password2Feedback.innerHTML = "\u2714";
    }
});

// Entire form validation before submit
const form = document.getElementById("form-signup");
form.addEventListener("submit", (e) => {
    if (login.value.search(/^[a-zA-Z]+[a-zA-Z0-9]*/) == -1) {
        e.preventDefault();
        login.classList.add("incorrect");
        login.classList.remove("correct");
        loginFeedback.classList.add("incorrect");
        loginFeedback.classList.remove("correct");
        loginFeedback.innerHTML = "\u2716 Login should start with a letter and consist only of letters and digits";
    }
    if (password1.value.search(/.{5,}/) == -1) {
        e.preventDefault();
        password1.classList.add("incorrect");
        password1.classList.remove("correct");
        password1Feedback.classList.add("incorrect");
        password1Feedback.classList.remove("correct");
        password1Feedback.innerHTML = "\u2716 Your password must be at least 5 characters long";
    }
    if (password1.value != password2.value) {
        e.preventDefault();
        password2.classList.add("incorrect");
        password2.classList.remove("correct");
        password2Feedback.classList.add("incorrect");
        password2Feedback.classList.remove("correct");
        password2Feedback.innerHTML = "\u2716 Confirmation does not match";
    }
});
