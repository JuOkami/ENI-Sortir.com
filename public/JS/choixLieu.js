
let liste = 0;

window.onload = init;

function init(){
    premierSelect();
    blankLieux();
    document.getElementById("sortie_choixVille").addEventListener("click", miseajourlistelieux);
}
function premierSelect() {
    var passageDeValeurs = document.getElementById('passagedevaleurs');
    liste = JSON.parse(passageDeValeurs.dataset.liste);

    let blankoption = document.createElement("option");
    blankoption.innerText = "--Selectionnez une ville--"
    blankoption.value = 0;
    document.getElementById("sortie_choixVille").appendChild(blankoption);

    for (i = 0; i<liste.length; i++){
        let option = document.createElement("option");
        option.innerText = liste[i].nom;
        option.value = liste[i].id;
        document.getElementById("sortie_choixVille").appendChild(option);
    }

    console.log(liste);
}

function miseajourlistelieux() {
    document.getElementById("sortie_lieuParVille").innerHTML = "";
    blankLieux();
   let champville = document.getElementById("sortie_choixVille");
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
            document.getElementById("sortie_lieuParVille").appendChild(option);
        }

    }
}

function blankLieux(){
    let blankoption = document.createElement("option");
    blankoption.innerText = "--Selectionnez un lieu--"
    blankoption.value = 0;
    document.getElementById("sortie_lieuParVille").appendChild(blankoption);
}