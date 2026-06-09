<?php
require base_path("views/partials/header.php");
require base_path("views/partials/nav.php");
?>

<div class="container">
    <dialog id="modal" open>
      <article>
        <header>
          <h1>Success</h1>
        </header>
        <p>
          <?= $page; ?>
          Please Login to proceed.
        </p>
        <a class="button" href="/login">Login</a>

      </article>
    </dialog>
</div>

<?php
require base_path("views/partials/footer.php");
?>