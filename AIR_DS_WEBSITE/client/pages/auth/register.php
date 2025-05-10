<!DOCTYPE html>

<?php
require_once __DIR__ . "/../../../config/config.php";
?>

<html>

<head>
  <link rel="stylesheet" href = "<?= BASE_URL ?>client/styles/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/header.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/footer.css">
</head>

<body>

<?php include_once BASE_PATH . "client/includes/header.php"; ?>

  <!-- <form id="registration-form" action="<?= BASE_URL ?>/server/services/auth/check_registration_errors.php"> -->
  <form id="registration-form">
    <!-- name field -->
    <fieldset>
      <label for="name-input">Name</label>
      <input type="text" id="name-input" name="name" required>
      <div id="name-input-error-message" class="error-message">Empty</div>
    </fieldset>

    <!-- surname field -->
    <fieldset>
      <label for="surname-input">Surname</label>
      <input type="text" id="surname-input" name="surname" required>
      <div id="surname-input-error-message" class="error-message">Empty</div>
    </fieldset>

    <!-- username field -->
    <fieldset>
      <label for="username-input">Username</label>
      <input type="text" id="username-input" name="username" required>
      <div id="username-input-error-message" class="error-message">Empty</div>
    </fieldset>

    <!-- password field -->
    <fieldset>
      <label for="password-input">Password</label>
      <input type="password" id="password-input" name="password" required>
      <div id="password-input-error-message" class="error-message">Empty</div>
    </fieldset>

    <!-- email field -->
    <fieldset>
      <label for="email-input">Email</label>
      <input type="email" id="email-input" name="email" required>
      <div id="email-input-error-message" class="error-message">Empty</div>
    </fieldset>

    <fieldset id="register-button-fieldset">
      <input type="submit" id="register-button" name="register-button" value="Register">
    </fieldset>
  </form>

  <?php include_once BASE_PATH . "client/includes/footer.php" ?>

  <script> const BASE_URL = "<?= BASE_URL ?>";</script>
  <script type="module" src="<?= BASE_URL ?>client/scripts/auth/checkRegistrationErrors.js"></script>
</body>

</html>