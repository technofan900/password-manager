<?php require base_path('views/partials/header.php'); ?>
<?php require base_path('views/partials/nav.php'); ?>

<div class="container grid">
    <div class="grid-card">
        <h2>Total Users:</h2>
        <?= $totalUsers; ?>
    </div>
    <div class="grid-card">
        <h2>Total Admins:</h2>
        <?= $totalAdmins; ?>
    </div>
    <div class="grid-card">
        <h2>Total Passwords:</h2>
        <?= $totalPasswords; ?>
    </div>
    <div class="grid-card">
        <h2>Total Folders:</h2>
        <?= $totalFolders; ?>
    </div>
</div>

<div class="container table-recent" >
    <h1>Recent Users</h1>
    <?php if(count($recentUsers) > 0 ): ?>
    <table>
        <thead>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Status</th>
            <th>Joined</th>
        </thead>
        <tbody>
            <?php foreach($recentUsers as $user) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <?php if($user['is_admin']): ?>
                            <span>ADMIN</span>
                        <?php else: ?>
                            <span>User</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo date('d.m.Y', strtotime($user['created_at'])) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>
</div>

    <div class="container admin-message-section">
        <h1>Site-wide announcement</h1>
        <p>Admin messages are shown as a popup to all users.</p>

        <form action="/admin/message" method="post">
            <label for="site_message">Message</label>
            <textarea id="site_message" name="site_message" rows="5" required style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #ccc;"><?php echo htmlspecialchars($siteMessage ?? ''); ?></textarea>
            <div style="margin-top: 1rem; display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                <button type="submit" style="padding: 0.75rem 1.25rem; border: none; background: #0172AD; color: #fff; border-radius: 6px; cursor: pointer;">Save Announcement</button>
                <button type="submit" name="site_message" value="" style="padding: 0.75rem 1.25rem; border: none; background: #e74c3c; color: #fff; border-radius: 6px; cursor: pointer;">Clear Announcement</button>
            </div>
        </form>

        <?php if (! empty($siteMessage)): ?>
            <div style="padding: 1rem;">
                <strong>Current announcement:</strong>
                <p style="margin: 0.75rem 0 0 0;"><?php echo nl2br(htmlspecialchars($siteMessage)); ?></p>
            </div>
        <?php endif; ?>
    </div>

<?php require base_path('views/partials/footer.php'); ?>