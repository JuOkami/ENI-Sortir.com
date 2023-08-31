// Fonction pour configurer et afficher la carte
function setUpMap(lat, lon) {
    // Créer une carte Leaflet et la centrer sur les coordonnées [lat, lon] avec un niveau de zoom de 15
    var map = L.map('map').setView([lat, lon], 15);
    console.log(lat + " et " + lon);

    // Ajouter une couche de tuiles OpenStreetMap à la carte avec les options spécifiées
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);


    // Ajouter un marqueur à la carte aux coordonnées [lat, lon]
    var marker = L.marker([lat, lon]).addTo(map);
}

