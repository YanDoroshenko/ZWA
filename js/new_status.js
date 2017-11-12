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

const iconUpload = document.getElementById("iconUpload");
const iconUploadFeedback = document.getElementById("iconUpload-feedback");
iconUpload.addEventListener("change", (e) => {
    if (iconUpload.value) {
        if (/(\.jpg|\.jpeg|\.bmp|\.gif|\.png)$/i.exec(iconUpload.value)) {
            iconUpload.classList.remove("incorrect");
            iconUpload.classList.add("correct");
            iconUploadFeedback.classList.remove("incorrect");
            iconUploadFeedback.classList.add("correct");
            iconUploadFeedback.innerHTML = "\u2714";
        }
        else {
            iconUpload.classList.remove("correct");
            iconUpload.classList.add("incorrect");
            iconUploadFeedback.classList.remove("correct");
            iconUploadFeedback.classList.add("incorrect");
            iconUploadFeedback.innerHTML = "\u2716 File is not supported";
        }
    }
});


// Entire form validation before submit
const form = document.getElementById("new_status");
form.addEventListener("submit", (e) => {
    if (!title.value) {
        e.preventDefault();
        title.classList.add("incorrect");
        title.classList.remove("correct");
        titleFeedback.classList.add("incorrect");
        titleFeedback.classList.remove("correct");
        titleFeedback.innerHTML = "\u2716 Title should not be empty";
    }
    if (iconUpload.value) {
        if (/(\.jpg|\.jpeg|\.bmp|\.gif|\.png)$/i.exec(iconUpload.value)) {
            iconUpload.classList.remove("incorrect");
            iconUpload.classList.add("correct");
            iconUploadFeedback.classList.remove("incorrect");
            iconUploadFeedback.classList.add("correct");
            iconUploadFeedback.innerHTML = "\u2714";
        }
        else {
            iconUpload.classList.remove("correct");
            iconUpload.classList.add("incorrect");
            iconUploadFeedback.classList.remove("correct");
            iconUploadFeedback.classList.add("incorrect");
            iconUploadFeedback.innerHTML = "\u2716 File is not supported";
        }
    }
});
