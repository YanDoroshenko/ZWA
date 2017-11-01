const login = document.getElementById("login");
login.removeAttribute("required");
login.removeAttribute("pattern");

login.addEventListener("blur", (e) => {
    if (login.value.search(/^[a-zA-Z]+[a-zA-Z0-9]*/) != -1) {
        login.classList.add("correct");
        login.classList.remove("incorrect");
        // Do feedback stuff
    }
    else {
        login.classList.add("incorrect");
        login.classList.remove("correct");
        // Do feedback stuff
    }
});

const password1 = document.getElementById("password1");
password1.removeAttribute("required");
password1.removeAttribute("pattern");

password1.addEventListener("blur", (e) => {
    if (password1.value.length > 4 && password1.value.search(/^[a-zA-Z]+[a-zA-Z0-9]*/) != -1) {
        password1.classList.add("correct");
        password1.classList.remove("incorrect");
        // Do feedback stuff
    }
    else {
        password1.classList.add("incorrect");
        password1.classList.remove("correct");
        // Do feedback stuff
    }
});

password2.addEventListener("blur", (e) => {
    if (password2.value != password1.value) {
        password2.classList.add("incorrect");
        password2.classList.remove("correct");
        // Do feedback stuff
    }
});

const form = document.getElementById("form");
form.addEventListener("submit", (e) => {
    if (login.value.search(/^[a-zA-Z]+[a-zA-Z0-9]*/) == -1) {
        e.preventDefault();
        // Do feedback stuff
    }
    else if (password1.value.length > 4 && password1.value.search(/^[a-zA-Z]+[a-zA-Z0-9]*/) == -1) {
        e.preventDefault();
        // Do feedback stuff
    }
    else if (password1.value != password2.value) {
        e.preventDefault();
        // Do feedback stuff
    }
});
