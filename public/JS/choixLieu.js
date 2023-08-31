let liste = 0;

window.onload = init;

// Fonction d'initialisation
function init() {
    blankLieux(); // Initialiser la liste des lieux
    var passageDeValeurs = document.getElementById('passagedevaleurs');
    creationListe(JSON.parse(passageDeValeurs.dataset.liste)); // Créer la liste à partir des données importées
    premierSelect();

    // Écouteur d'événement pour le clic sur le bouton de sortie de ville
    document.getElementById("sortie_ville").addEventListener("click", miseajourlistelieux);
}

// Fonction pour créer la liste à partir des données importées
function creationListe(listeimportee) {
    liste = listeimportee;
}

// Fonction pour remplir le premier sélecteur de ville
function premierSelect() {

    document.getElementById("sortie_ville").innerHTML = "";
    let blankoption = document.createElement("option");
    blankoption.innerText = "--Selectionnez une ville--"
    blankoption.value = 0;
    document.getElementById("sortie_ville").appendChild(blankoption);

    for (i = 0; i < liste.length; i++) {
        let option = document.createElement("option");
        option.innerText = liste[i].nom;
        option.value = liste[i].id;
        document.getElementById("sortie_ville").appendChild(option);
    }
}

// Fonction pour mettre à jour la liste des lieux en fonction de la ville sélectionnée
function miseajourlistelieux() {

    blankLieux();
    let champville = document.getElementById("sortie_ville");
    let valeurVille = champville.options[champville.selectedIndex].value;
    let villeActuelle;

    if (valeurVille != 0) {
        for (i = 0; i < liste.length; i++) {
            if (valeurVille == liste[i].id) {
                villeActuelle = liste[i];
            }
        }
        let lieuxassocies = villeActuelle.lieux;
        for (i = 0; i < lieuxassocies.length; i++) {
            let option = document.createElement("option");
            option.innerText = lieuxassocies[i].nom;
            option.value = lieuxassocies[i].id;
            document.getElementById("sortie_lieu").appendChild(option);
        }

    }
}

// Fonction pour réinitialiser la liste des lieux
function blankLieux() {
    document.getElementById("sortie_lieu").innerHTML = "";
    let blankoption = document.createElement("option");
    blankoption.innerText = "--Selectionnez un lieu--"
    blankoption.value = 0;
    document.getElementById("sortie_lieu").appendChild(blankoption);
}