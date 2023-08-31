window.onload = init;

let champRue = document.getElementById("lieu_rue");
let selecteurVille = document.getElementById("lieu_ville");
let entreeRue = '';
let entreeVille = '';


function init() {

    champRue.addEventListener('keyup', function () {
        entreeRue = champRue.value.toLowerCase().replace(new RegExp("[^(a-z\-)]", "g"), '');
        miseajourURL();
    })
    selecteurVille.addEventListener("click", function () {
        entreeVille = selecteurVille.options[selecteurVille.selectedIndex].innerText.toLowerCase().replace(new RegExp("[^(a-z\-)]", "g"), '');
        miseajourURL();
    })

}

function miseajourURL() {
    let infoadresse;
    let url = 'https://api-adresse.data.gouv.fr/search/?q=' + entreeRue + entreeVille;
    console.log(url);
    fetch(url)
        .then(reponse => reponse.json())
        .then(
            json => {
                exploitationDuJason(json.features)
            }
        )
    console.log(infoadresse);
}

function exploitationDuJason(json) {

    // document.getElementById("affichage").innerText = json;

}