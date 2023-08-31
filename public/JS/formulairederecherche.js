// Récupération de l'élément DOM pour la barre de recherche
let barre = document.getElementById("zonederecherche");
let etat = false;
document.getElementById("formulairederecherche").style.display = "none";
console.log('bienvenue dans le script');
console.log(barre);

window.onload = init;

// Fonction d'initialisation
function init() {
    console.log("init")
    // Ajout d'un écouteur de clic sur l'élément de la barre de recherche
    barre.addEventListener('click', affichage);
}

// Fonction pour gérer l'affichage/masquage du formulaire
function affichage() {
    console.log("bienvenue dans la méthode")
    if (!etat) { // Affichage du formulaire et ajustement de la taille
        document.getElementById("formulairederecherche").style.display = "block";
        document.getElementById("formulairederecherche").style.scale = "(1, 1)";
        etat = true;
    } else { // Masquage du formulaire
        document.getElementById("formulairederecherche").style.display = "none";
        etat = false;
    }
}