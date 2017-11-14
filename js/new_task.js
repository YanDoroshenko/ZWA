// name input validation
const name = document.getElementById("name");
const nameFeedback = document.getElementById("name-feedback");
name.removeAttribute("required");

name.addEventListener("blur", (e) => {
    if (name.value) {
        name.classList.add("correct");
        name.classList.remove("incorrect");
        nameFeedback.classList.add("correct");
        nameFeedback.classList.remove("incorrect");
        nameFeedback.innerHTML = "\u2714";
    }
    else {
        name.classList.add("incorrect");
        name.classList.remove("correct");
        nameFeedback.classList.add("incorrect");
        nameFeedback.classList.remove("correct");
        nameFeedback.innerHTML = "\u2716 Name should not be empty";
    }
});

// priority input validation
const priority = document.getElementById("priority");
const priorityFeedback = document.getElementById("priority-feedback");
priority.removeAttribute("required");

priority.addEventListener("blur", (e) => {
    if (priority.value) {
        if (!(parseInt(priority.value) > 0 && parseInt(priority.value) <= 10)) {
            priority.classList.add("incorrect");
            priority.classList.remove("correct");
            priorityFeedback.classList.add("incorrect");
            priorityFeedback.classList.remove("correct");
            priorityFeedback.innerHTML = "\u2716 Valid values are 1 to 10";
        }
        else {
            priority.classList.add("correct");
            priority.classList.remove("incorrect");
            priorityFeedback.classList.add("correct");
            priorityFeedback.classList.remove("incorrect");
            priorityFeedback.innerHTML = "\u2714";
        }
    }
    else {
        priority.classList.add("incorrect");
        priority.classList.remove("correct");
        priorityFeedback.classList.add("incorrect");
        priorityFeedback.classList.remove("correct");
        priorityFeedback.innerHTML = "\u2716 Priority should not be empty";
    }
});

// Entire form validation before submit
const form = document.getElementById("new_task");
form.addEventListener("submit", (e) => {
    if (!name.value) {
        e.preventDefault();
        name.classList.add("incorrect");
        name.classList.remove("correct");
        nameFeedback.classList.add("incorrect");
        nameFeedback.classList.remove("correct");
        nameFeedback.innerHTML = "\u2716 Name should not be empty";
    }
});
