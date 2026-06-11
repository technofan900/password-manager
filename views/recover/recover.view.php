<?php
require base_path("views/partials/header.php");
require base_path("views/partials/nav.php");
?>

<div class="container">
    <h2>Recover password</h2>
    <form action="/recover" method="POST">
        <label for="email">Email
            <?php if(isset($errors['email'])) : ?>
                <input type="email" name="email" id="email" placeholder="email@address.com" aria-invalid="true" aria-describedby="code-helper" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                <small id="code-helper" class="text-red-400 text-sm"><?= $errors['email'] ?></small>
            <?php else : ?>
                <input type="email" name="email" id="email" placeholder="email@address.com" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
            <?php endif; ?>
        </label>
        <button type="submit">Submit</button>
    </form>
</div>

<?php
require base_path("views/partials/footer.php");
?>