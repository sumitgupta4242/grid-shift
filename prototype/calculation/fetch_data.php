<?php
// Database configuration
$servername = "localhost:3307";
$username = "root"; // Replace with your MySQL username
$password = "";     // Replace with your MySQL password
$dbname = "login_pbl";  // Database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch all rows ordered by datetime
$sql = "SELECT datetime, Generation_Energy, Predict_Generation_Energy, Consumption_Energy, Predict_Consumption_Energy 
        FROM tableName
        ORDER BY datetime ASC";

$result = $conn->query($sql);

$data = [];

// Check if the table has data
if ($result->num_rows > 0) {
    // Fetch rows one by one
    while ($row = $result->fetch_assoc()) {
        // Calculate surplus energy
        $surplus_energy_month1 = $row['Generation_Energy'] - $row['Consumption_Energy'];
        $predicted_surplus_energy = $row['Predict_Generation_Energy'] - $row['Predict_Consumption_Energy'];

        // Calculate energy to sell
        $energy_to_sell = $surplus_energy_month1 - $predicted_surplus_energy;

        // Append processed data to the array
        $data[] = [
            "datetime" => $row['datetime'],
            "generation_month1" => $row['Generation_Energy'],
            "predicted_generation" => $row['Predict_Generation_Energy'],
            "consumption_month1" => $row['Consumption_Energy'],
            "predicted_consumption" => $row['Predict_Consumption_Energy'],
            "surplus_energy_month1" => $surplus_energy_month1,
            "predicted_surplus_energy" => $predicted_surplus_energy,
            "energy_to_sell" => $energy_to_sell,
        ];
    }

    // Return the data as JSON
    echo json_encode($data);
} else {
    echo json_encode(["error" => "No data found in the table."]);
}

// Close the connection
$conn->close();
?>
