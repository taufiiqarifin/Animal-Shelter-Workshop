<!DOCTYPE html>
<html>
<head>
    <title>Add Report</title>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 400px;
            width: 100%;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <h2>Add New Report</h2>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label>Latitude:</label>
        <input type="text" id="latitude" name="latitude" required readonly><br><br>

        <label>Longitude:</label>
        <input type="text" id="longitude" name="longitude" required readonly><br><br>

        <!-- Leaflet Map -->
        <div id="map"></div>

        <label>Address:</label>
        <input type="text" name="address" required><br><br>

        <label>City:</label>
        <input type="text" name="city" required><br><br>

        <label>State:</label>
        <input type="text" name="state" required><br><br>

        <label>Status:</label>
        <input type="text" name="report_status" required><br><br>

        <label>Description:</label>
        <textarea name="description"></textarea><br><br>

        <label>Upload Photo:</label>
        <input type="file" name="image" accept="image/*"><br><br>

        <button type="submit">Submit Report</button>
    </form>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Initialize the map
        var map = L.map('map').setView([2.1896, 102.2501], 13); // Default: Melaka area

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var marker;

        // Handle map click event
        map.on('click', function(e) {
            var lat = e.latlng.lat.toFixed(6);
            var lng = e.latlng.lng.toFixed(6);

            // Update input fields
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            // Remove existing marker
            if (marker) {
                map.removeLayer(marker);
            }

            // Add new marker
            marker = L.marker([lat, lng]).addTo(map)
                .bindPopup("Selected location:<br>Lat: " + lat + "<br>Lng: " + lng)
                .openPopup();
        });
    </script>
</body>
</html>
