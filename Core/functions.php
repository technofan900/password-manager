<?php 

function urlIs($value) {
    return $_SERVER['REQUEST_URI'] === $value;
}

function abort($code = Core\Responses::NOT_FOUND) {
    http_response_code($code);
    require base_path("views/{$code}.php");
    die();
}

function authorize ($condition, $status = Core\Responses::FORBIDDEN){
    if (! $condition) {
        abort(core\Responses::FORBIDDEN);
    }
}

function base_path($path) {
    return BASE_PATH . $path;
}

function view($path, $attributes = [] ) {
    extract($attributes);
    require base_path("views/{$path}");
}

function login ($user) {
    $_SESSION['user'] = [
        'id' => $user['id'],
        'email' => $user['email']
    ];
    session_regenerate_id(true);
}

function logout() {
    $_SESSION = [];
    session_destroy();

    $params = session_get_cookie_params();
    setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']); 
}

function redirect($path) {
    header("location: {$path}");
    exit;
}

function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

// --- File encryption helpers ---
function get_encryption_key() {
    $config = require base_path('config.php');
    $val = $config['app']['encryption_key'] ?? null;
    if (! $val) {
        throw new Exception('Encryption key not configured in config.php');
    }
    if (strpos($val, 'base64:') === 0) {
        return base64_decode(substr($val, 7));
    }
    return $val;
}

function encrypt_uploaded_file($srcPath, $destPath) {
    $key = get_encryption_key();
    $cipher = 'aes-256-cbc';
    $ivLen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivLen);
    $data = file_get_contents($srcPath);
    $ciphertext = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    if ($ciphertext === false) return false;
    return file_put_contents($destPath, $iv . $ciphertext) !== false;
}

function decrypt_file_to_temp($encryptedPath) {
    $key = get_encryption_key();
    $cipher = 'aes-256-cbc';
    $ivLen = openssl_cipher_iv_length($cipher);
    $fp = fopen($encryptedPath, 'rb');
    if (! $fp) return false;
    $iv = fread($fp, $ivLen);
    $ciphertext = stream_get_contents($fp);
    fclose($fp);
    $data = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    if ($data === false) return false;
    $tmp = tempnam(sys_get_temp_dir(), 'dec_');
    file_put_contents($tmp, $data);
    return $tmp;
}

function decrypt_file_to_string($encryptedPath) {
    $tmp = decrypt_file_to_temp($encryptedPath);
    if (! $tmp) return false;
    $data = file_get_contents($tmp);
    @unlink($tmp);
    return $data;
}
