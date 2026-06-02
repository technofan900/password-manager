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

// --- String encryption helpers for DB fields ---
function encrypt_string_for_storage($plaintext) {
    if ($plaintext === null) return null;
    $key = get_encryption_key();
    $cipher = 'aes-256-cbc';
    $ivLen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivLen);
    $ciphertext = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    if ($ciphertext === false) return false;
    // store as base64(iv + ciphertext)
    return base64_encode($iv . $ciphertext);
}

function decrypt_string_from_storage($b64) {
    if ($b64 === null) return null;
    $raw = base64_decode($b64, true);
    if ($raw === false) return false;
    $key = get_encryption_key();
    $cipher = 'aes-256-cbc';
    $ivLen = openssl_cipher_iv_length($cipher);
    if (strlen($raw) <= $ivLen) return false;
    $iv = substr($raw, 0, $ivLen);
    $ciphertext = substr($raw, $ivLen);
    $plaintext = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    if ($plaintext === false) return false;
    return $plaintext;
}


    function randomPassword($secSettings) {

        if (!empty($_SESSION['generated_password'])) {
            unset($_SESSION['generated_password']);
        }

        $rules = $secSettings ?? [];

        $min = (int) ($rules['min_length'] ?? 12);
        $reqUpper = !empty($rules['require_uppercase']);
        $reqLower = isset($rules['require_lowercase']) ? (bool) $rules['require_lowercase'] : true;
        $reqNums = !empty($rules['require_numbers']);
        $reqSpec = !empty($rules['require_special']);

        // If only lowercase is required (other requirements disabled),
        // promote to full-strength rules and enforce a minimum length of 12.
        if ($reqLower && !$reqUpper && !$reqNums && !$reqSpec) {
            $reqUpper = true;
            $reqNums = true;
            $reqSpec = true;
            $min = max($min, 12);
        }

        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lower = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*()-_=+[]{}?,.'; // reasonable safe set

        // Build character pool and ensure required characters
        $pool = '';
        $required = [];

        if ($reqUpper) {
            $pool .= $upper;
            $required[] = $upper[random_int(0, strlen($upper) - 1)];
        }

        if ($reqLower) {
            $pool .= $lower;
            $required[] = $lower[random_int(0, strlen($lower) - 1)];
        }

        if ($reqNums) {
            $pool .= $numbers;
            $required[] = $numbers[random_int(0, strlen($numbers) - 1)];
        }

        if ($reqSpec) {
            $pool .= $special;
            $required[] = $special[random_int(0, strlen($special) - 1)];
        }

        // If no specific rules were enabled, use a strong default pool
        if ($pool === '') {
            $pool = $lower . $upper . $numbers . $special;
            // still ensure at least one lower-case
            $required[] = $lower[random_int(0, strlen($lower) - 1)];
        }

        // Ensure length is at least 12 and covers required categories
        $length = max(12, $min, count($required));

        $passwordChars = $required;

        // Fill the rest from the pool
        for ($i = count($required); $i < $length; $i++) {
            $passwordChars[] = $pool[random_int(0, strlen($pool) - 1)];
        }

        // Shuffle securely (Fisher-Yates)
        $n = count($passwordChars);
        for ($i = $n - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            $tmp = $passwordChars[$i];
            $passwordChars[$i] = $passwordChars[$j];
            $passwordChars[$j] = $tmp;
        }

        return implode('', $passwordChars);
    }