<!DOCTYPE html>

<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "client\includes\start_session.php";
?>

<html>

<head>
  <link rel="stylesheet" href = "<?= BASE_URL ?>client/styles/style.css">
  <link rel="stylesheet" href = "<?= BASE_URL ?>client/styles/form.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/header.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/footer.css">
</head>

<body>

  <?php include BASE_PATH . "client/includes/header.php"; ?>

  <main>

    <h2> Register </h2>
  
    <form id="registration-form">
      <fieldset>
        <!-- name field -->
        <div class="field">
          <label for="name-input">Name</label>
          <input type="text" id="name-input" name="name" required>
          <div id="name-input-error-message" class="error-message">Empty</div>
        </div>

        <!-- surname field -->
        <div class="field">
          <label for="surname-input">Surname</label>
          <input type="text" id="surname-input" name="surname" required>
          <div id="surname-input-error-message" class="error-message">Empty</div>
        </div class="field">

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

        <!-- email field -->
        <div class="field">
          <label for="email-input">Email</label>
          <input type="email" id="email-input" name="email" required>
          <div id="email-input-error-message" class="error-message">Empty</div>
        </div>

        <div id="register-button-fieldset" class="field">
          <input type="submit" id="register-button" name="register-button" value="Register">
          <div id="registration-button-error-message" class="error-message"></div>
        </div>
      </fieldset>
      
    </form>
  </main>

  <?php include_once BASE_PATH . "client/includes/footer.php" ?>

  <script> const BASE_URL = "<?= BASE_URL ?>";</script>
  <script type="module" src="<?= BASE_URL ?>client/scripts/auth/checkRegistrationErrors.js"></script>
</body>

</html>