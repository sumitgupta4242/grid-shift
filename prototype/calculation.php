<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Energy Calculation & Location Finder</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/background.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        /* Shared Styles */
        header {
            text-align: center;
            background-color: rgba(0, 123, 255, 0.8);
            color: white;
            padding: 15px;
            font-size: 24px;
            font-weight: bold;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin: 20px auto;
            max-width: 1200px;
        }

        /* Energy Calculation Section */
        .energy-calculation {
            flex: 1 1 45%;
            padding: 20px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .energy-calculation h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .energy-calculation .section {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 15px;
            background: #f9f9f9;
        }

        .energy-calculation .section img {
            height: 50px;
            margin-right: 15px;
        }

        .energy-calculation label {
            font-weight: bold;
            flex: 1;
        }

        .energy-calculation input {
            flex: 2;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .result-box {
            text-align: center;
            margin-top: 20px;
            font-size: 20px;
            font-weight: bold;
        }

        .result-box .negative {
            color: red;
        }

        .result-box .positive {
            color: green;
        }

        /* Map Section */
        .map-section {
            flex: 1 1 45%;
            padding: 20px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .map-section h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        #map {
            height: 300px;
            border: 2px solid #ccc;
            border-radius: 10px;
        }

        #controls {
            margin: 20px 0;
            text-align: center;
        }

        #controls input,
        #controls button {
            padding: 10px;
            font-size: 16px;
            margin: 5px;
            border-radius: 10px;
            border: 1px solid #ccc;
        }

        #controls button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }

        #controls button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        // Energy Calculation Functions
        function generateRandomNumber() {
            return (Math.random() * 99 + 1).toFixed(4); // Random decimal between 1.00 and 100.00
        }

        function calculateEnergy() {
            const homeEnergy = parseFloat(document.getElementById('home_energy').value) || 0;
            const energyGeneration = parseFloat(document.getElementById('energy_generation').value) || 0;
            const result = parseFloat((energyGeneration - homeEnergy).toFixed(2));

            const resultBox = document.getElementById('result');
            if (result < 0) {
                // resultBox.innerHTML = `<span class='negative'>${result} kWh</span>`;
            } else {
                resultBox.innerHTML = `<span class='positive'>${result} kWh</span>`;
            }
        }



        function autoGenerateValues() {
            const homeEnergyInput = document.getElementById('home_energy');
            const energyGenerationInput = document.getElementById('energy_generation');

            setInterval(() => {
                const homeEnergy = generateRandomNumber();
                const energyGeneration = generateRandomNumber();

                homeEnergyInput.value = homeEnergy;
                energyGenerationInput.value = energyGeneration;

                calculateEnergy();
            }, 1000); // Update every second
        }

        // Map Functions
        let map;
        let marker;

        function initMap(lat = 0, lng = 0) {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat, lng },
                zoom: 2,
            });
        }

        function showCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        updateMap(lat, lng, "Your Current Location");
                    },
                    () => alert("Geolocation failed. Please allow location access.")
                );
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        function searchPlace() {
            const placeName = document.getElementById('place-name').value.trim();
            if (!placeName) {
                alert("Please enter a place name.");
                return;
            }

            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ address: placeName }, (results, status) => {
                if (status === "OK") {
                    const location = results[0].geometry.location;
                    updateMap(location.lat(), location.lng(), results[0].formatted_address);
                } else {
                    alert("Place not found: " + status);
                }
            });
        }

        function updateMap(lat, lng, title) {
            const location = { lat, lng };
            map.setCenter(location);
            map.setZoom(10);

            if (marker) {
                marker.setMap(null);
            }

            marker = new google.maps.Marker({
                position: location,
                map: map,
                title: title,
            });
        }

        window.onload = () => {
            autoGenerateValues(); // Start auto-generating energy values
            initMap(); // Initialize map
        };
    </script>
    <script
        src="https://maps.gomaps.pro/maps/api/js?key=AlzaSyBM00zvEVURIfmu36fpikpqSQO_IBFhnTK&libraries=places"></script>
</head>

<body>
    <header>
        <h1>Energy Calculater</h1>
    </header>
    <div class="container">
        <!-- Energy Calculation Section -->
        <div class="energy-calculation">
            <h2>Energy Calculater</h2>
            <div class="section">
                <img src="images/home_energy_icon.jpg" alt="Home Energy Icon">
                <label for="home_energy">Home Energy Use (kWh)</label>
                <input type="number" id="home_energy" oninput="calculateEnergy()">
            </div>
            <div class="section">
                <img src="images/energy_generation_icon.jpg" alt="Energy Generation Icon">
                <label for="energy_generation">Energy Generation (kWh)</label>
                <input type="number" id="energy_generation" oninput="calculateEnergy()">
            </div>
            <div class="result-box">
                <span id="result">Enter values to calculate</span>
            </div>
        </div>

        <!-- Map Section -->
        <div class="map-section">
            <div id="controls">
                <button onclick="showCurrentLocation()">📍 My Current Location</button>
                <input type="text" id="place-name" placeholder="Enter a place name (e.g., Eiffel Tower)">
                <button onclick="searchPlace()">🔍 Search Place</button>
            </div>
            <div id="map"></div>
        </div>
    </div>
</body>

</html>