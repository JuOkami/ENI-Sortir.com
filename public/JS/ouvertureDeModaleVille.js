var modalVille = document.getElementById("modaleVille");

// Get the button that opens the modal
var btnVille = document.getElementById("boutonVille");

// Get the <span> element that closes the modal
var spanVille = document.getElementById("closeVille");

document.getElementById("boutonpersoVille").addEventListener('click', envoiduformulaireVille);


// When the user clicks on the button, open the modal
btnVille.onclick = function () {
    modalVille.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
spanVille.onclick = function () {
    modalVille.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
    if (event.target == modalVille) {
        modalVille.style.display = "none";
    }
}

function envoiduformulaireVille() {

    const nom = document.getElementById("ville_nom").value;
    const codePostal = document.getElementById("ville_codePostal").value;

    let data = {
        nom: nom,
        codePostal: codePostal
    }

    fetch('http://localhost:8000/enregistrerVille', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    }).then(reponse => reponse.json())
        .then(nouvelleListe => {
            creationListe(nouvelleListe);
            premierSelect();
        });

    modalVille.style.display = "none";

}