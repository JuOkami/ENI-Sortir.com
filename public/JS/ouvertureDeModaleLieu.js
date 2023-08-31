var modalLieu = document.getElementById("modaleLieu");

// Get the button that opens the modal
var btnLieu = document.getElementById("boutonLieu");

// Get the <span> element that closes the modal
var spanLieu = document.getElementById("closeLieu");

document.getElementById("boutonpersoLieu").addEventListener('click', envoiduformulaireLieu);


// When the user clicks on the button, open the modal
btnLieu.onclick = function () {
    console.log("Cela marche ti'il seulement");
    modalLieu.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
spanLieu.onclick = function () {
    modalLieu.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
    if (event.target == modalLieu) {
        modalLieu.style.display = "none";
    }
}

function envoiduformulaireLieu() {

    const nom = document.getElementById("lieu_nom").value;
    const rue = document.getElementById("lieu_rue").value;
    const latitude = document.getElementById("lieu_latitude").value;
    const longitude = document.getElementById("lieu_longitude").value;
    const ville = document.getElementById("lieu_ville").options[document.getElementById("lieu_ville").selectedIndex].value;

    let data = {
        nom: nom,
        rue: rue,
        latitude: latitude,
        longitude: longitude,
        ville: ville
    }

    fetch('http://localhost:8000/enregistrerLieu', {
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

    modalLieu.style.display = "none";

}