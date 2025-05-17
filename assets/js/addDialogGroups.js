const addButtonOpenGroups = document.getElementById("addButtonOpenGroups");
const addButtonCloseGroups = document.getElementById("addButtonCloseGroups");

addButtonOpenGroups.addEventListener('click', event => {
    document.getElementById("overlay").style.display = "block";
    document.getElementById("addDialogGroups").style.display = "block";
})

addButtonCloseGroups.addEventListener('click', event => {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("addDialogGroups").style.display = "none";
})

overlay.addEventListener('click', event => {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("addDialogGroups").style.display = "none";
})