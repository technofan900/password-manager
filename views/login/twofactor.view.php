<?php
/** @var string|null $email */
require base_path("views/partials/header.php");
require base_path("views/partials/nav.php");
?>
    <div class="container">
      <h2>Two-Factor Authentication</h2>
      <p>A verification code was sent to <strong><?= htmlspecialchars($email) ?></strong>. Enter it below to complete login.</p>
      <form method="POST" action="/login/2fa">
        <label>
          Authentication code
          <?php if (isset($errors['code'])) : ?>
            <input name="code" type="text" placeholder="123456" aria-invalid="true" aria-describedby="code-helper" value="<?= htmlspecialchars($old['code'] ?? '') ?>" required>
            <small id="code-helper" class="text-red-400 text-sm"><?= $errors['code'] ?></small>
          <?php else: ?>
            <input name="code" type="text" placeholder="123456" value="<?= htmlspecialchars($old['code'] ?? '') ?>" required>
          <?php endif; ?>
        </label>

        <button type="submit">Verify code</button>
      </form>
    </div>

<?php
require base_path("views/partials/footer.php");
?>
