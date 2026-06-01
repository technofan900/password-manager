<?php

use Core\Validator;

// Require authenticated user (route already uses middleware)

$values = $_POST ?? [];

// Save settings to session via Validator helper
Validator::setSavedPasswordStrength($values);

// Provide user feedback and redirect back to settings
$_SESSION['success'] = 'Settings saved.';
redirect('/settings');

