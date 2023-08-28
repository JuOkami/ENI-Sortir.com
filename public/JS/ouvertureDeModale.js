

    var modal = document.getElementById("myModal");

// Get the button that opens the modal
    var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    document.getElementById("boutonperso").addEventListener('click', envoiduformulaire);



// When the user clicks on the button, open the modal
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function envoiduformulaire() {

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
        ville : ville
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

    modal.style.display = "none";

}