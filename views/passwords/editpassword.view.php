<?php
require base_path("views/partials/header.php");
require base_path("views/partials/nav.php");
?>
<div class="container">
    <h2>Edit note</h2>
    <form method="POST" action="/passwords">
        <input type="hidden" name="_method" value="PATCH">
        <input type="hidden" name="id" value="<?= htmlspecialchars($note['id']) ?>">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($_POST['name'] ?? $note['name']) ?>">
        </div>

        <div class="form-group">
            <label for="login-data">Login data</label>
            <input type="text" name="login_data" id="login_data" value="<?= htmlspecialchars($_POST['login_data'] ?? $note['login_data']) ?>">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" value="<?= htmlspecialchars($_POST['password'] ?? $note['password']) ?>">
        </div>

        <div class="form-group">
            <label for="folder_id">Folder</label>
            <select name="folder_id" id="folder_id">
                <option value="">None</option>
                <?php foreach($folders as $folder) : ?>
                    <option value="<?= htmlspecialchars($folder['id']) ?>" <?= (isset($note['folder_id']) && $note['folder_id'] == $folder['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($folder['folder_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <p>
            <button type="submit">Submit</button>
            <a onclick="history.back()">Back</a>
        </p>

    </form>
</div>

<?php
require base_path("views/partials/footer.php");