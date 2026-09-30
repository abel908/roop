/*
 * Contact page map — OpenStreetMap through Leaflet, bundled and self-hosted,
 * no Google Maps dependency so it works from mainland China (§5.6, §7.6).
 */
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

document.querySelectorAll('[data-osm-map]').forEach((el) => {
    const lat = parseFloat(el.dataset.lat);
    const lng = parseFloat(el.dataset.lng);
    if (Number.isNaN(lat) || Number.isNaN(lng)) return;

    const map = L.map(el, { scrollWheelZoom: false, attributionControl: true }).setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    const icon = L.divIcon({
        className: '',
        html: '<span style="display:block;width:22px;height:22px;background:#0B9444;border:4px solid #fff;border-radius:50%;box-shadow:0 0 0 2px #000"></span>',
        iconSize: [22, 22],
        iconAnchor: [11, 11],
    });

    L.marker([lat, lng], { icon, title: el.dataset.label || '' }).addTo(map);
});
