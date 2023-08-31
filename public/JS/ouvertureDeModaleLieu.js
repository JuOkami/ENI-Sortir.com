var modalLieu = document.getElementById("modaleLieu");

// Get the button that opens the modal
var btnLieu = document.getElementById("boutonLieu");

// Get the <span> element that closes the modal
var spanLieu = document.getElementById("closeLieu");

document.getElementById("boutonpersoLieu").addEventListener('click', envoiduformulaireLieu);


// Lorsque l'utilisateur clique sur le bouton, ouvrir la modale
btnLieu.onclick = function () {
    console.log("Cela marche ti'il seulement");
    modalLieu.style.display = "block";
}

// Lorsque l'utilisateur clique sur <span> (x), fermer la modale
spanLieu.onclick = function () {
    modalLieu.style.display = "none";
}

// Lorsque l'utilisateur clique n'importe où en dehors de la modale, la fermer
window.onclick = function (event) {
    if (event.target == modalLieu) {
        modalLieu.style.display = "none";
    }
}

// Fonction pour envoyer les données du formulaire de lieu
function envoiduformulaireLieu() {

    const nom = document.getElementById("lieu_nom").value;
    const rue = document.getElementById("lieu_rue").value;
    const latitude = document.getElementById("lieu_latitude").value;
    const longitude = document.getElementById("lieu_longitude").value;
    const ville = document.getElementById("lieu_ville").options[document.getElementById("lieu_ville").selectedIndex].value;

    // Création d'un objet data pour les données à envoyer
    let data = {
        nom: nom,
        rue: rue,
        latitude: latitude,
        longitude: longitude,
        ville: ville
    }

    // Envoi de la requête POST avec les données au serveur
    fetch('http://localhost:8000/enregistrerLieu', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    }).then(reponse => reponse.json())
        .then(nouvelleListe => {
            // Mise à jour de la liste des lieux après enregistrement
            creationListe(nouvelleListe);
            premierSelect();
        });

    // Fermeture de la modale
    modalLieu.style.display = "none";

}