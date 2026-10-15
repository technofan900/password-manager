<?php
require base_path("views/partials/header.php");
require base_path("views/partials/nav.php");
?>
<div class="container">
    <h2>Edit note</h2>
    <?php $old = $_SESSION['old'] ?? []; ?>
    <?php $errors = $_SESSION['errors'] ?? []; ?>
    <form method="POST" action="/passwords">
        <input type="hidden" name="_method" value="PATCH">
        <input type="hidden" name="id" value="<?= htmlspecialchars($note['id']) ?>">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($old['name'] ?? $_POST['name'] ?? $note['name']) ?>">
        </div>

        <div class="form-group">
            <label for="login-data">Login data</label>
            <input type="text" name="login_data" id="login_data" value="<?= htmlspecialchars($old['login_data'] ?? $_POST['login_data'] ?? $note['login_data']) ?>">
        </div>

        <div class="form-group">
            <?php if(!empty($errors['password'])) : ?>
                <label for="password">Password</label>
                <fieldset role="group">
                    <input type="password" name="password" id="password" aria-invalid="true" aria-describedby="password-helper" value="<?= htmlspecialchars($old['password'] ?? $_POST['password'] ?? $note['password']) ?>">
                    <button id="togglePassword" type="button">Show</button>
                </fieldset>
                <small id="password-helper" class="text-red-400 text-sm"><?= htmlspecialchars($errors['password']) ?></small>
                <?php if (!empty($requirements)) : ?>
                    <ul class="text-sm text-gray-600" style="margin:0.5rem 0 0; padding-left:1.2em">
                        <?php foreach ($requirements as $req) : ?>
                            <li><?= htmlspecialchars($req) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php else : ?>
                <label for="password">Password</label>
                <fieldset role="group">
                    <input type="password" name="password" id="password" value="<?= htmlspecialchars($old['password'] ?? $_POST['password'] ?? $note['password']) ?>">
                    <button id="togglePassword" type="button">Show</button>
                </fieldset>
                <?php if (!empty($requirements)) : ?>
                    <small id="password-helper">
                        <ul class="text-sm text-gray-600" style="margin:0; padding-left:1.2em">
                            <?php foreach ($requirements as $req) : ?>
                                <li><?= htmlspecialchars($req) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </small>
                <?php endif; ?>
            <?php endif; ?>
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
unset($_SESSION['errors'], $_SESSION['old']);
require base_path("views/partials/footer.php");