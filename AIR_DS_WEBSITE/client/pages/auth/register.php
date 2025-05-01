<!DOCTYPE html>

<?php
require_once __DIR__ . "/../../../config/config.php";
?>

<html>

<head>
  <link rel="stylesheet" href = "<?= BASE_URL ?>/client/styles/style.css">
</head>

<body>
  <form>
    <!-- name field -->
    <fieldset>
      <label for="name-input">Name</label>
      <input type="text" id="name-input" name="name-input" required>
      <div id="" class="error-message">Empty</div>
    </fieldset>

    <!-- surname field -->
    <fieldset>
      <label for="surname-input">Surname</label>
      <input type="text" id="surname-input" name="surname-input" required>
      <div id="" class="error-message">Empty</div>
    </fieldset>

    <!-- username field -->
    <fieldset>
      <label for="username-input">Username</label>
      <input type="text" id="username-input" name="username-input" required>
      <div id="username-input-error-message" class="error-message">Empty</div>
    </fieldset>

    <!-- password field -->
    <fieldset>
      <label for="password-input">Password</label>
      <input type="password" id="password-input" name="password-input" required>
      <div id="" class="error-message">Empty</div>
    </fieldset>

    <!-- email field -->
    <fieldset>
      <label for="email-input">Email</label>
      <input type="email" id="email-input" name="email-input" required>
      <div id="" class="error-message">Empty</div>
    </fieldset>

    <fieldset id="register-button-fieldset">
      <input type="submit" id="register-button" name="register-button" value="Purchase">
    </fieldset>
  </form>

  <script> const BASE_URL = "<?= BASE_URL ?>";</script>
  <script src="<?= BASE_URL ?>/client/scripts/checkRegistrationErrors.js"></script>
</body>

</html>