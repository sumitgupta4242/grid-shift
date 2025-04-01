<?php
session_start();
$servername = "localhost:3307";
$username = "root"; // Update with your DB username
$password = ""; // Update with your DB password
$dbname = "solar_panel_db";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle user sign-up
if (isset($_POST['signup'])) {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (email, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);

    if ($stmt->execute()) {
        echo "<div class='message success'>Sign-up successful. Please log in.</div>";
    } else {
        echo "<div class='message error'>Error: " . $stmt->error . "</div>";
    }
}

// Handle user login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
        } else {
            echo "<div class='message error'>Invalid password.</div>";
        }
    } else {
        echo "<div class='message error'>User not found.</div>";
    }
}

// Handle solar panel data submission
if (isset($_POST['save_data'])) {
    $user_id = $_SESSION['user_id'];
    $num_panels = $_POST['num_panels'];
    $panel_capacity = $_POST['panel_capacity'];
    $panel_area = $_POST['panel_area'];

    $sql = "REPLACE INTO solar_data (user_id, num_panels, panel_capacity, panel_area) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiid", $user_id, $num_panels, $panel_capacity, $panel_area);

    if ($stmt->execute()) {
        echo "<div class='message success'>Data saved successfully.</div>";
    } else {
        echo "<div class='message error'>Error: " . $stmt->error . "</div>";
    }
}

// Fetch existing data for logged-in user
$solar_data = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT num_panels, panel_capacity, panel_area FROM solar_data WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $solar_data = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solar Panel Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            color: #0056b3;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input, button {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #0056b3;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #004494;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
        }
        .logout {
            text-align: center;
            margin-top: 20px;
        }
        .logout a {
            color: #0056b3;
            text-decoration: none;
            font-weight: bold;
        }
        .logout a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <h2>Enter meter id</h2>
            <form method="POST">
                <input type="email" name="email" placeholder="Meter_Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Enter</button>
            </form>

            <h2>create meter id</h2>
            <form method="POST">
                <input type="email" name="email" placeholder="Meter_Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="signup">Enter</button>
            </form>
        <?php else: ?>
            <h2>Solar Panel Data</h2>
            <form method="POST">
                <label>Number of Panels:</label>
                <input type="number" name="num_panels" value="<?= $solar_data['num_panels'] ?? '' ?>" required>
                <label>Panel Capacity (kW):</label>
                <input type="number" step="0.1" name="panel_capacity" value="<?= $solar_data['panel_capacity'] ?? '' ?>" required>
                <label>Panel Area (m²):</label>
                <input type="number" step="0.1" name="panel_area" value="<?= $solar_data['panel_area'] ?? '' ?>" required>
                <button type="submit" name="save_data">Save Data</button>
            </form>
            <div class="logout">
                <a href="logout.php">Logout</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>  