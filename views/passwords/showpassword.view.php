<?php
require base_path("views/partials/header.php");
require base_path("views/partials/nav.php");
?>

<div class="container">
    <div>
        <h1><?= $note['name'] ?></h1>
        <label for="login_data">Login</label>
        <input type="text" name="login_data" value="<?= $note['login_data'] ?>" aria-label="Read-only name" readonly>
        <label for="password">Password</label>
        <input type="text" name="password" value="<?= $note['password'] ?>" aria-label="Read-only name" readonly>
    </div>
    <div>
        <ul>
            <?php foreach($folders as $folder) : ?>
                <li><?= $folder['folder_name'] //why this here, change ?></li> 
            <?php endforeach; ?>            
        </ul>
    </div>
    <div class="buttons">
        <p>
            <a href="/password/edit?id=<?= $note['id'] ?>">Edit</a>
            <a href="/password/popup?id=<?= $note['id'] ?>">Delete</a>

            <a href="/passwords">Back</a>
        </p>
    </div>
</div>

<?php
require base_path("views/partials/footer.php");
?>