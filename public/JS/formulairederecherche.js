let barre = document.getElementById("zonederecherche");
let etat = false;
document.getElementById("formulairederecherche").style.display = "none";
console.log('bienvenue dans le script');
console.log(barre);

window.onload = init;

function init(){
    console.log("init")
    barre.addEventListener('click', affichage);
}

function affichage() {
    console.log("bienvenue dans la méthode")
    if (!etat){
        document.getElementById("formulairederecherche").style.display = "block";
        document.getElementById("formulairederecherche").style.scale = "(1, 1)";
        etat = true;
    } else {
        document.getElementById("formulairederecherche").style.display = "none";
        etat = false;
    }
}