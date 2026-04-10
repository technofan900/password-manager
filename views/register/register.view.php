<?php
require base_path ("views/partials/header.php");
require base_path ("views/partials/nav.php");
?>
    <div class="container">
      <h2>Register</h2>
      <form method="POST" action="/register">
        <label>
          Username
          <?php if (isset($errors['username'])) : ?>
            <input name="username" type="text" placeholder="" aria-invalid="true" aria-describedby="username-helper" value="<?= htmlspecialchars($old['username'] ?? '') ?>" required>
            <small id="username-helper" class="text-red-400 text-sm"><?= $errors['username'] ?></small>
          <?php else: ?>
            <input name="username" type="text" placeholder="" value="<?= htmlspecialchars($old['username'] ?? '') ?>" required>
          <?php endif; ?>
        </label>

        <label>
          E-pasts
          <?php if (isset($errors['email'])) : ?>
            <input name="email" type="email" placeholder="epasts@example.com" aria-invalid="true" aria-describedby="email-helper" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
            <small id="email-helper" class="text-red-400 text-sm"><?= $errors['email'] ?></small>
          <?php else: ?>
            <input name="email" type="email" placeholder="epasts@example.com" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
          <?php endif; ?>
        </label>

        <label>
          Password
          <?php if (isset($errors['password'])) : ?>
            <fieldset role="group">
              <input id="password" name="password" type="password" placeholder="******" aria-invalid="true" aria-describedby="password-helper" required>
              <button id="togglePassword" type="button">Show</button>
            </fieldset>
            <small id="password-helper" class="text-red-400 text-sm"><?= $errors['password'] ?></small>
          <?php else: ?>
            <fieldset role="group">
              <input id="password" name="password" type="password" placeholder="******" required>
              <button id="togglePassword" type="button">Show</button> 
            </fieldset>
          <?php endif; ?>
        </label>

        <button type="submit" name="submit">Nosūtīt</button>
        
      </form>
    </div>
    <div>
      <form method="GET" action="/login">
        <div class="btn-center">
          <h2 class="text-center">Log In</h2>
          <button type="submit" name="log-in">Login</button>
        </div>
      </form>
    </div>


<script>
    (function show_password(){
      const toggle = document.getElementById('togglePassword');
      const pwd = document.getElementById('password');
      if (!toggle || !pwd) return;
      toggle.addEventListener('click', function(){
        if (pwd.type === 'password'){
          pwd.type = 'text';
          this.textContent = 'Hide';
        } else {
          pwd.type = 'password';
          this.textContent = 'Show';
        }
      });
    })();
</script>

<?php
require base_path("views/partials/footer.php");
?>