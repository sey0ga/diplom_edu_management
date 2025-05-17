const addButtonOpenStudents = document.getElementById("addButtonOpenStudents");
const addButtonCloseStudents = document.getElementById("addButtonCloseStudents");

addButtonOpenStudents.addEventListener('click', event => {
    document.getElementById("overlay").style.display = "block";
    document.getElementById("addDialogStudents").style.display = "block";
})

addButtonCloseStudents.addEventListener('click', event => {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("addDialogStudents").style.display = "none";
})

overlay.addEventListener('click', event => {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("addDialogStudents").style.display = "none";
})