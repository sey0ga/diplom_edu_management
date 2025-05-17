const addButtonOpenOrders = document.getElementById("addButtonOpenOrders");
const addButtonCloseOrders = document.getElementById("addButtonCloseOrders");
const addButtonOpenMove = document.getElementById("addButtonOpenMove");
const addButtonCloseMove = document.getElementById("addButtonCloseMove");

addButtonOpenMove.addEventListener('click', event => {
    document.getElementById("overlay").style.display = "block";
    document.getElementById("addDialogMove").style.display = "block";
})

addButtonCloseMove.addEventListener('click', event => {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("addDialogMove").style.display = "none";
})

addButtonOpenOrders.addEventListener('click', event => {
    document.getElementById("overlay").style.display = "block";
    document.getElementById("addDialogOrders").style.display = "block";
})

addButtonCloseOrders.addEventListener('click', event => {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("addDialogOrders").style.display = "none";
})

overlay.addEventListener('click', event => {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("addDialogMove").style.display = "none";
    document.getElementById("addDialogOrders").style.display = "none";
})