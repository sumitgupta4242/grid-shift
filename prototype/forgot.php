<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="css/style1.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
  <div class="container">
    <div class="form-box box">

      <?php
      include "connection.php";

      if (isset($_POST['forgot'])) {

        $email = $_POST['email'];

        $sql = "SELECT * FROM users WHERE email = '$email'";

        $res = mysqli_query($conn, $sql);

        if (mysqli_num_rows($res) > 0) {

          $row = mysqli_fetch_assoc($res);

          $token = bin2hex(random_bytes(16));

          $sql = "UPDATE users SET token = '$token' WHERE email = '$email'";

          mysqli_query($conn, $sql);

          $subject = "Password Reset Request";
          $body = "Click on the link to reset your password: <a href='http://localhost/reset.php?token=$token'>Reset Password</a>";

          $headers = "From: your-email@example.com\r\n";
          $headers .= "Content-type: text/html\r\n";

          mail($email, $subject, $body, $headers);

          echo "<div class='message'>
                  <p>Password reset link sent to your email.</p>
                  </div><br>";

          echo "<a href='login.php'><button class='btn'>Go Back</button></a>";

        } else {
          echo "<div class='message'>
                  <p>Email not found.</p>
                  </div><br>";

          echo "<a href='forgot.php'><button class='btn'>Try Again</button></a>";
        }

      } else {
        ?>

        <header>Forgot Password</header>
        <hr>
        <form action="#" method="POST">

          <div class="form-box">

            <div class="input-container">
              <i class="fa fa-envelope icon"></i>
              <input class="input-field" type="email" placeholder="Email Address" name="email">
            </div>

          </div>

          <center><input type="submit" name="forgot" id="submit" value="Send Reset Link" class="btn"></center>

          <div class="links">
            Remember your password? <a href="login.php">Login Now</a>
          </div>

        </form>
      </div>
      <?php
      }
      ?>
  </div>
</body>

</html>