<?php
require base_path("views/partials/header.php");
require base_path("views/partials/nav.php");
?>

<div class="container">
    <h2>Reset password</h2>
    <?php if(isset($errors['token'])): ?>
        <p class="text-red-500"><?= htmlspecialchars($errors['token']) ?></p>
    <?php endif; ?>
    <form action="/recover/reset" method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <label for="password">New password
            <input type="password" name="password" id="password" required>
        </label>
        <label for="password_confirm">Confirm password
            <input type="password" name="password_confirm" id="password_confirm" required>
        </label>
        <button type="submit">Reset password</button>
    </form>
</div>

<?php
require base_path("views/partials/footer.php");
?>
