// title input validation
const title = document.getElementById("title");
const titleFeedback = document.getElementById("title-feedback");
title.removeAttribute("required");

title.addEventListener("blur", (e) => {
    if (title.value) {
        title.classList.add("correct");
        title.classList.remove("incorrect");
        titleFeedback.classList.add("correct");
        titleFeedback.classList.remove("incorrect");
        titleFeedback.innerHTML = "\u2714";
    }
    else {
        title.classList.add("incorrect");
        title.classList.remove("correct");
        titleFeedback.classList.add("incorrect");
        titleFeedback.classList.remove("correct");
        titleFeedback.innerHTML = "\u2716 Title should not be empty";
    }
});

// Entire form validation before submit
const form = document.getElementById("new_status");
form.addEventListener("submit", (e) => {
    if (title.value) {
        e.preventDefault();
        title.classList.add("incorrect");
        title.classList.remove("correct");
        titleFeedback.classList.add("incorrect");
        titleFeedback.classList.remove("correct");
        titleFeedback.innerHTML = "\u2716 Title should not be empty";
    }
});
