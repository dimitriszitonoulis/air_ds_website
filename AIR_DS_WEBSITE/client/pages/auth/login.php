<?php 
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "client/includes/start_session.php"

?>

<!DOCTYPE html>

<html>

<head>
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/header.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/footer.css">
</head>

<body>

  <?php include_once BASE_PATH . "client/includes/header.php"; ?>
  
  
  <h2>Login/Register</h2>

  <form action="">
    <!-- username field -->
    <fieldset>
      <label for="username-input">Username</label>
      <input type="text" id="username-input" name="username" required>
      <div id="username-input-error-message" class="error-message">Empty</div>
    </fieldset>

    <!-- password field -->
    <fieldset>
      <label for="password-input">Password</label>
      <input type="text" id="password-input" name="password" required>
      <div id="password-input-error-message" class="error-message">Empty</div>
    </fieldset>

    <fieldset id="login-button-fieldset">
      <input type="submit" id="login-button" name="login-button" value="Login">
      <div id="login-button-error-message" class="error-message">Empty</div>
    </fieldset>
  </form>

  <!-- Redirects to registration form -->
  <p>Need an account? <a href="./register.php">Register</a> </p>


  <?php include_once BASE_PATH . "client/includes/footer.php"; ?>

<script>const BASE_URL = "<?= BASE_URL ?>";</script>
<script type="module" src="<?= BASE_URL ?>/client/scripts/auth/checkLoginErrors.js"></script>
</body>

</html>