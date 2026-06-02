<?php
require base_path("views/partials/header.php");
require base_path("views/partials/nav.php");
?>
<div class="container">
    <h2>Save password</h2>
    <?php $old = $_SESSION['old'] ?? []; ?>
    <?php $errors = $_SESSION['errors'] ?? []; ?>
    <?php $randPassword = $_SESSION['generated_password'] ?? '';?>
    <?php unset($_SESSION['generated_password']); ?>
    <form method="POST" action="/passwords" enctype="multipart/form-data">
        <div class="form-group">
            <?php if(!empty($errors['name'])) : ?>
                <label for="name">Name</label>
                <input type="text" name="name" id="name" aria-invalid="true" aria-describedby="password-helper" value="<?= htmlspecialchars($old['name'] ?? $_POST['name'] ?? '') ?>">
                <small id="password-helper" class="text-red-400 text-sm"><?= htmlspecialchars($errors['name']) ?></small>
            <?php else : ?>
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($old['name'] ?? $_POST['name'] ?? '') ?>">
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="login-data">Login data</label>
            <input type="text" name="login_data" id="login_data" value="<?= htmlspecialchars($old['login_data'] ?? $_POST['login_data'] ?? '') ?>">
        </div>
 
        <div class="form-group">
            <?php if(!empty($errors['password'])) : ?>
                <label for="password">Password -- <a href="/password/generate">Generate password</a></label>
                <fieldset role="group">
                    <input type="password" name="password" id="password" aria-invalid="true" aria-describedby="password-helper" value="<?= htmlspecialchars($old['password']?? $randPassword  ?? $_POST['password'] ?? '') ?>">
                    <button id="togglePassword" type="button">Show</button>
                </fieldset>
                <small id="password-helper" class="text-red-400 text-sm"><?= htmlspecialchars($errors['password']) ?></small>
            <?php else : ?>
                <label for="password">Password -- <a href="/password/generate">Generate password</a></label>
                <fieldset role="group">
                    <input type="password" name="password" id="password" aria-describedby="password-helper" value="<?= htmlspecialchars($old['password'] ?? $randPassword ?? $_POST['password'] ?? '') ?>">
                    <button id="togglePassword" type="button">Show</button>
                </fieldset>

                <small id="password-helper">
                    <?php if (!empty($requirements)) : ?>
                        <ul class="text-sm text-gray-600" style="margin:0; padding-left:1.2em">
                            <?php foreach ($requirements as $req) : ?>
                                <li><?= htmlspecialchars($req) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </small>
            <?php endif; ?>
        </div>

        <div>
            <label for="folder_select">Folder (Optional)</label>
            <select name="folder_select" id="folder_select" aria-label="Select">
                <option value=" " <?= empty($old['folder_select']) || $old['folder_select'] === ' ' ? 'selected' : '' ?>>None</option>
                <?php foreach($folders as $folder) : ?>
                    <option value="<?= $folder['id'] ?>" <?= isset($old['folder_select']) && $old['folder_select'] == $folder['id'] ? 'selected' : '' ?>>
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
// clear flash errors/old after showing
unset($_SESSION['errors'], $_SESSION['old']);
require base_path("views/partials/footer.php");
?>