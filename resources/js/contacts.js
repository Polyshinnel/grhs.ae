const loadMapbox = () => {
    if (window.mapboxgl) {
        return Promise.resolve(window.mapboxgl);
    }

    if (!window.contactMapboxPromise) {
        const stylesheet = document.createElement('link');
        stylesheet.rel = 'stylesheet';
        stylesheet.href = 'https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css';
        document.head.append(stylesheet);

        window.contactMapboxPromise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js';
            script.async = true;
            script.onload = () => resolve(window.mapboxgl);
            script.onerror = reject;
            document.head.append(script);
        });
    }

    return window.contactMapboxPromise;
};

const mapContainer = document.querySelector('[data-contact-map]');

if (mapContainer) {
    loadMapbox()
        .then((mapboxgl) => {
            mapboxgl.accessToken = mapContainer.dataset.mapboxToken;

            const latitude = Number(mapContainer.dataset.latitude);
            const longitude = Number(mapContainer.dataset.longitude);
            const map = new mapboxgl.Map({
                container: mapContainer,
                style: 'mapbox://styles/mapbox/streets-v12',
                center: [longitude, latitude],
                zoom: 15,
                cooperativeGestures: true,
            });

            map.addControl(new mapboxgl.NavigationControl(), 'top-right');
            map.addControl(new mapboxgl.ScaleControl());

            const marker = document.createElement('img');
            marker.src = mapContainer.dataset.markerImage;
            marker.alt = '';
            marker.width = 45;
            marker.height = 45;

            new mapboxgl.Marker({ element: marker, anchor: 'bottom' })
                .setLngLat([longitude, latitude])
                .addTo(map);

            map.once('load', () => map.resize());
        })
        .catch(() => {
            mapContainer.classList.add('flex', 'items-center', 'justify-center', 'px-6', 'text-center');
            mapContainer.textContent = 'The office map is currently unavailable. Please contact us for directions.';
        });
}
