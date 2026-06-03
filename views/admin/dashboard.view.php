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

    <div class="container setup">
        <h1>Admin setup:</h1>
        <p>To promote a user to admin status, use the CLI command:</p>
        <code>php cli/promote-admin.php &lt;user_id_or_email&gt;</code>
        <p>For more information, see <strong>ADMIN_SETUP.md</strong></p>
    </div>

<?php require base_path('views/partials/footer.php'); ?>