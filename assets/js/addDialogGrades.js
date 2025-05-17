const addButtonOpenGrades = document.getElementById("addButtonOpenGrades");
const addButtonCloseGrades = document.getElementById("addButtonCloseGrades");
const addButtonOpenSubject = document.getElementById("addButtonOpenSubject");
const addButtonCloseSubject = document.getElementById("addButtonCloseSubject");
const overlay = document.getElementById("overlay");

addButtonOpenGrades.addEventListener('click', event => {
    document.getElementById("overlay").style.display = "block";
    document.getElementById("addDialogGrades").style.display = "block";
})

addButtonCloseGrades.addEventListener('click', event => {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("addDialogGrades").style.display = "none";
})

addButtonOpenSubject.addEventListener('click', event => {
    document.getElementById("overlay").style.display = "block";
    document.getElementById("addDialogSubject").style.display = "block";
})

addButtonCloseSubject.addEventListener('click', event => {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("addDialogSubject").style.display = "none";
})

overlay.addEventListener('click', event => {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("addDialogGrades").style.display = "none";
    document.getElementById("addDialogSubject").style.display = "none";
})