<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "client/includes/start_session.php"
  ?>

<!DOCTYPE html>

<html>

<head>
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/header.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/form.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/footer.css">
</head>

<body>

  <?php include_once BASE_PATH . "client/includes/header.php"; ?>

  <main>

    <h2>Login/Register</h2>

    <form action="">
      <fieldset>
        <!-- username field -->
        <div class="field">
          <label for="username-input">Username</label>
          <input type="text" id="username-input" name="username" required>
          <div id="username-input-error-message" class="error-message">Empty</div>
        </div>

        <!-- password field -->
        <div class="field">
          <label for="password-input">Password</label>
          <input type="password" id="password-input" name="password" required>
          <div id="password-input-error-message" class="error-message">Empty</div>
        </div>

      </fieldset>
      
      <div id="login-button-fieldset" class="field">
      <input type="submit" id="login-button" name="login-button" value="Login">
      <div id="login-button-error-message" class="error-message">Empty</div>
    </div>
    </form>


    <!-- Redirects to registration form -->
    <p>Need an account? <a href="./register.php">Register</a> </p>
  
  </main>

  <?php include_once BASE_PATH . "client/includes/footer.php"; ?>

  <script>const BASE_URL = "<?= BASE_URL ?>";</script>
  <script src="<?= BASE_URL ?>client/scripts/hamburgerMenu.js"></script>
  <script type="module" src="<?= BASE_URL ?>client/scripts/auth/checkLoginErrors.js"></script>
</body>

</html>