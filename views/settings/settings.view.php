<?php require base_path('views/partials/header.php'); ?>
<?php require base_path('views/partials/nav.php'); ?>

<div class="container">
    <h1>Settings</h1>

    <div class="dark-mode">
        <label class="switch" title="Toggle theme">
            <input id="theme-switch" type="checkbox" role="switch" aria-label="Toggle theme">
            Dark Mode
        </label>
    </div>

    <div class="password-save-settings">
        <h2>Password save settings</h2>
        <?php $pwSettings = $_SESSION['password_settings'] ?? []; ?>
        <form method="POST" action="/set-setting">
            <fieldset>
                <label>
                    <input type="checkbox" name="12-numbers" id="12-numbers" <?php if (!empty($pwSettings['min_length']) && $pwSettings['min_length'] >= 12) echo 'checked'; ?>>
                    Minimum 12 characters
                </label>
                <label>
                    <input type="checkbox" name="uppercase-letter" id="uppercase-letter" <?php if (!empty($pwSettings['require_uppercase'])) echo 'checked'; ?>>
                    Uppercase letter
                </label>
                <label>
                    <input type="checkbox" name="special-sym" id="special-sym" <?php if (!empty($pwSettings['require_special'])) echo 'checked'; ?>>
                    Special symbols
                </label>
                <label>
                    <input type="checkbox" name="numbers" id="numbers" <?php if (!empty($pwSettings['require_numbers'])) echo 'checked'; ?>>
                    Numbers
                </label>
            </fieldset>
            <?php if (!empty($_SESSION['success'])) : ?>
                <small class="text-sm text-success"><?php echo $_SESSION['success']; ?></small>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <button type="submit">Apply</button>
        </form>
    </div>
</div>

<?php require base_path('views/partials/footer.php'); ?>