
function setUpMap(lat, lon) {
    var map = L.map('map').setView([lat, lon], 15);
    console.log(lat+" et "+lon);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var marker = L.marker([lat, lon]).addTo(map);
}

