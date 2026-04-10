<?php
require base_path("views/partials/header.php");
require base_path("views/partials/nav.php");
?>
<div class="container">
    <h2>Create note</h2>
    <form method="POST" action="/passwords">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="login-data">Login data</label>
            <input type="text" name="login_data" id="login_data" value="<?= htmlspecialchars($_POST['login_data'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" value="<?= htmlspecialchars($_POST['password'] ?? '') ?>">
        </div>

        <div>
            <label for="folder_select">Folder (Optional)</label>
            <select name="folder_select" id="folder_select" aria-label="Select" required>
                <option selected value=" ">None</option>
                <?php foreach($folders as $folder) : ?>
                    <option value="<?= $folder['id'] ?>">
                        <?= htmlspecialchars($folder['folder_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <p>
            <button type="submit">Submit</button>
            <a href="/passwords" class="back-button">Back</a>
        </p>

    </form>
</div>

<?php
require base_path("views/partials/footer.php");
?>