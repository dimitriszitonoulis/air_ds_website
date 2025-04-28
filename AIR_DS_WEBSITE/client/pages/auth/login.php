<?php 
require_once __DIR__ . "/../../../config/config.php";
?>

<!DOCTYPE html>

<html>

<head>
  <link rel="stylesheet" href="<?= BASE_URL ?>styles/header.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>styles/footer.css">
</head>

<?php include_once BASE_PATH . "client/includes/header.php"; ?>

<body>

  <h2>Login/Register</h2>



  <!--
    TODO
    CHECKS:
    -not registered 
   -->
  <form action="">
    <!-- username field -->
    <fieldset>
      <label for="username-input">Username</label>
      <input type="text" id="username-input" name="username" required>
    </fieldset>

    <!-- password field -->
    <fieldset>
      <label for="password-input">Password</label>
      <input type="text" id="password-input" name="password" required>
    </fieldset>
  </form>

  <!-- Redirects to registration form -->
  <p>Need an account? <a href="./register.html">Register</a> </p>

<?php include_once BASE_PATH . "client/includes/footer.php"; ?>

</body>

</html>