<?php require base_path('views/partials/header.php'); ?>
<?php require base_path('views/partials/nav.php'); ?>
<div class="container">
  <h1>Welcome to Password Manager</h1>
  <p>
    Store, organize, and manage your passwords securely in one place.
    Our password manager helps you protect your accounts with strong security and easy access to your credentials.
  </p>

  <?php if ($siteMessage = get_site_message()): ?>
    <section class="announcement-card" style="margin: 1.5rem 0; padding: 1rem; border-radius: 10px; background: rgba(30, 144, 255, 0.1); border: 1px solid rgba(30, 144, 255, 0.2);">
      <h2 style="margin-top: 0;">Announcement</h2>
      <p style="margin: 0.5rem 0 0 0; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($siteMessage)); ?></p>
    </section>
  <?php endif; ?>

  <?php if ($_SESSION['user'] ?? false) : ?>
  <?php else : ?>
  <a role="button" href="/login">Log In</a>
  <?php endif; ?>
</div>
<?php require base_path('views/partials/footer.php'); ?>
