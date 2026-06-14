<?php
require base_path("views/partials/header.php");
?>

<div class="container">
    <div class="deactivate-final">
        <h2>Last Chance!</h2>
        <?php if(isset($errors['token'])) : ?>
            <p class="text-red-400"><?= htmlspecialchars($errors['token']) ?></p>
        <?php endif; ?>
        <p>Are you <strong>absolutely</strong> sure you want to deactivate <?= isset($email) ? htmlspecialchars($email) : 'this account' ?>?</p>
        <form action="/deactivate/complete" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">
            <button type="submit">Deactivate</button>
        </form>
    </div>
</div>

<?php
require base_path("views/partials/footer.php");
?>
