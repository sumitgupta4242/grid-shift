<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link rel="stylesheet" href="css/style1.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
  <div class="container">
    <div class="form-box box">

      <?php
      include "connection.php";

      if (isset($_GET['token'])) {

        $token = $_GET['token'];

        $sql = "SELECT * FROM users WHERE token = '$token'";

        $res = mysqli_query($conn, $sql);

        if (mysqli_num_rows($res) > 0) {

          $row = mysqli_fetch_assoc($res);

          if (isset($_POST['reset'])) {

            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if ($password == $confirm_password) {

              $hashed_password = password_hash($password, PASSWORD_DEFAULT);

              $sql = "UPDATE users SET password = '$hashed_password', token = '' WHERE email = '$row[email]'";

              mysqli_query($conn, $sql);

              echo "<div class='message'>
                      <p>Password reset successfully </p>
                      </div><br>";

              echo "<a href='login.php'><button class='btn'>Login Now</button></a>";

            } else {
              echo "<div class='message'>
                      <p>Passwords do not match.</p>
                      </div><br>";
            }

          } else {
            ?>

            <header>Reset Password</header>
            <hr>
            <form action="#" method="POST">

              <div class="form-box">

                <div class="input-container">
                  <i class="fa fa-lock icon"></i>
                  <input class="input-field" type="password" placeholder="New Password" name="password" required>
                </div>

                <div class="input-container">
                  <i class="fa fa-lock icon"></i>
                  <input class="input-field" type="password" placeholder="Confirm Password" name="confirm_password" required>
                </div>

              </div>

              <center><input type="submit" name="reset" id="submit" value="Reset Password" class="btn"></center>

            </form>
            <?php
          }

        } else {
          echo "<div class='message'>
                  <p>Invalid token.</p>
                  </div><br>";
        }

      } else {
        echo "<div class='message'>
                <p>No token provided.</p>
                </div><br>";
      }
      ?>
    </div>
  </div>
</body>

</html>