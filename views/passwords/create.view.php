<?php
require base_path("views/partials/header.php");
require base_path("views/partials/nav.php");
?>
<div class="container">
    <h2>Save password</h2>
    <?php $old = $_SESSION['old'] ?? []; ?>
    <form method="POST" action="/passwords" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($old['name'] ?? $_POST['name'] ?? '') ?>">
            <?php if (!empty($errors['body'])): ?>
                <p class="error"><?= htmlspecialchars($errors['body']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="login-data">Login data</label>
            <input type="text" name="login_data" id="login_data" value="<?= htmlspecialchars($old['login_data'] ?? $_POST['login_data'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" value="<?= htmlspecialchars($old['password'] ?? $_POST['password'] ?? '') ?>">
        </div>

        <div>
            <label for="folder_select">Folder (Optional)</label>
            <select name="folder_select" id="folder_select" aria-label="Select">
                <option selected value=" ">None</option>
                <?php foreach($folders as $folder) : ?>
                    <option value="<?= $folder['id'] ?>">
                        <?= htmlspecialchars($folder['folder_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="attachment">Attachment (optional) — PDF, TXT, PNG (max 5MB)</label>
            <input type="hidden" name="MAX_FILE_SIZE" value="5242880">
            <input type="file" name="attachment" id="attachment" accept=".pdf,.txt,image/png">
            <?php if (!empty($errors['attachment'])): ?>
                <p class="text-red-400"><?= htmlspecialchars($errors['attachment']) ?></p>
            <?php endif; ?>
        </div>

        <script>
            (function(){
                var form = document.currentScript && document.currentScript.parentNode.querySelector('form') || document.querySelector('form');
                var fileInput = document.getElementById('attachment');
                var max = 5242880; // 5MB
                if (form && fileInput) {
                    form.addEventListener('submit', function(e){
                        var f = fileInput.files[0];
                        if (f && f.size > max) {
                            e.preventDefault();
                            alert('Attachment exceeds maximum size of 5MB.');
                            return false;
                        }
                    });
                }
            })();
        </script>

        <p>
            <button type="submit">Submit</button>
            <a href="/passwords" class="back-button">Back</a>
        </p>

    </form>
</div>
<?php
// clear flash errors/old after showing
unset($_SESSION['errors'], $_SESSION['old']);
require base_path("views/partials/footer.php");
?>