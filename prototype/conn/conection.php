<?php
// Database credentials
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "consumption";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the database
$sql = "SELECT `Meter Reading (kWh)`, `Cumulative Reading (kWh)` FROM tablename";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
$conn->close();

// Serve JSON data for AJAX requests
header('Content-Type: application/json');
echo json_encode($data);
?>
