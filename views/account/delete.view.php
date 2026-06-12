<?php
require base_path("views/partials/header.php");
require base_path("views/partials/nav.php");
?>

<div class="container">
    <h2>Deactivate account</h2>
    <p><strong>Are you sure you want to delete your account? All stored passwords and vault data will be permanently deleted.</strong></p>
    <?php if(isset($errors['success'])) : ?>
        <p class="text-success"><?= htmlspecialchars($errors['success']) ?></p>
    <?php endif; ?>
    <form action="/deactivate" method="POST">
        <label for="password">Enter password
            <?php if(isset($errors['password'])) : ?>
                <input type="password" name="password" id="password" aria-invalid="true" aria-describedby="code-helper" required>
                <small id="code-helper" class="text-red-400 text-sm"><?= $errors['password'] ?></small>
            <?php else : ?>
                <input type="password" name="password" id="password" required>
            <?php endif; ?>
        </label>
        <fieldset>
            <label>
                <input type="checkbox" name="deactivate-confirm" id="deactivate-confirm">
                Confirm deactivation
            </label>
            <?php if(isset($errors['confirmation'])) : ?>
                <small class="text-red-400 text-sm"><?= htmlspecialchars($errors['confirmation']) ?></small>
            <?php endif; ?>
        </fieldset>
        <button type="submit">Submit</button>
    </form>
</div>

<?php
require base_path("views/partials/footer.php");
?>
