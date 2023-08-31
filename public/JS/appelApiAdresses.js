// Fonction appelée lorsque la fenêtre a fini de charger
window.onload = init;

// Récupération des éléments DOM
let champRue = document.getElementById("lieu_rue");
let selecteurVille = document.getElementById("lieu_ville");
let entreeRue = '';
let entreeVille = '';

// Fonction d'initialisation
function init() {

    // Écouteur d'événement pour la saisie dans le champ de rue
    champRue.addEventListener('keyup', function () {
        entreeRue = champRue.value.toLowerCase().replace(new RegExp("[^(a-z\-)]", "g"), '');
        miseajourURL();
    })
    // Écouteur d'événement pour la sélection de la ville dans le sélecteur
    selecteurVille.addEventListener("click", function () {
        entreeVille = selecteurVille.options[selecteurVille.selectedIndex].innerText.toLowerCase().replace(new RegExp("[^(a-z\-)]", "g"), '');
        miseajourURL();
    })

}

// Fonction pour mettre à jour l'URL et déclencher la requête
function miseajourURL() {
    let infoadresse;
    let url = 'https://api-adresse.data.gouv.fr/search/?q=' + entreeRue + entreeVille;
    console.log(url);
    fetch(url)
        .then(reponse => reponse.json())
        .then(
            json => {
                exploitationDuJason(json.features) // Appelle la fonction de traitement des données JSON
            }
        )
    console.log(infoadresse);
}

// Fonction pour exploiter les données JSON
function exploitationDuJason(json) {

    // document.getElementById("affichage").innerText = json;

}