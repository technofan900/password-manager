<?php

/**
 * Admin Dashboard
 * Only accessible to users with admin status (is_admin = 1)
 */

use Core\App;
use Core\Database;

// Get basic statistics
$db = App::resolve(Database::class);

$totalUsers = $db->query("SELECT COUNT(*) as count FROM login")->find()['count'] ?? 0;
$totalAdmins = $db->query("SELECT COUNT(*) as count FROM login WHERE is_admin = 1")->find()['count'] ?? 0;
$totalPasswords = $db->query("SELECT COUNT(*) as count FROM passwords")->find()['count'] ?? 0;
$totalFolders = $db->query("SELECT COUNT(*) as count FROM folders")->find()['count'] ?? 0;

// Get recent users
$recentUsers = $db->query("SELECT id, username, email, created_at, is_admin FROM login ORDER BY created_at DESC LIMIT 5")->get();

$siteMessage = get_site_message();

view('admin/dashboard.view.php', [
    'totalUsers' => $totalUsers,
    'totalAdmins' => $totalAdmins,
    'totalPasswords' => $totalPasswords,
    'totalFolders' => $totalFolders,
    'recentUsers' => $recentUsers,
    'siteMessage' => $siteMessage
]);
