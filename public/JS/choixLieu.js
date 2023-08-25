
let liste = 0;

window.onload = init;

function init(){
    blankLieux();
    premierSelect();

    document.getElementById("sortie_ville").addEventListener("click", miseajourlistelieux);
}
function premierSelect() {
    var passageDeValeurs = document.getElementById('passagedevaleurs');
    document.getElementById("sortie_ville").innerHTML="";
    liste = JSON.parse(passageDeValeurs.dataset.liste);

    let blankoption = document.createElement("option");
    blankoption.innerText = "--Selectionnez une ville--"
    blankoption.value = 0;
    document.getElementById("sortie_ville").appendChild(blankoption);

    for (i = 0; i<liste.length; i++){
        let option = document.createElement("option");
        option.innerText = liste[i].nom;
        option.value = liste[i].id;
        document.getElementById("sortie_ville").appendChild(option);
    }
}

function miseajourlistelieux() {

    blankLieux();
   let champville = document.getElementById("sortie_ville");
    let valeurVille = champville.options[champville.selectedIndex].value;
    let villeActuelle;

    if (valeurVille!=0) {
        for (i = 0; i < liste.length; i++) {
            if (valeurVille == liste[i].id) {
                villeActuelle = liste[i];
            }
        }
        let lieuxassocies = villeActuelle.lieux;
        for (i = 0; i<lieuxassocies.length; i++){
            let option = document.createElement("option");
            option.innerText = lieuxassocies[i].nom;
            option.value = lieuxassocies[i].id;
            document.getElementById("sortie_lieu").appendChild(option);
        }

    }
}

function blankLieux(){
    document.getElementById("sortie_lieu").innerHTML = "";
    let blankoption = document.createElement("option");
    blankoption.innerText = "--Selectionnez un lieu--"
    blankoption.value = 0;
    document.getElementById("sortie_lieu").appendChild(blankoption);
}