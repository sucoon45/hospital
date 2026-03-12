<?php
/**
 * Utility Functions for NovaCare HMS
 */

/**
 * Sanitizes input data
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(stripslashes(trim($data)));
}

/**
 * Redirects to a different page
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Formats currency to Naira
 */
function formatCurrency($amount) {
    return '₦' . number_format($amount, 2);
}

/**
 * Generates a random alphanumeric string
 */
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

/**
 * Handles file uploads
 * @return array ['success' => boolean, 'name' => string/error_msg]
 */
function uploadFile($file, $target_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'pdf', 'docx']) {
    $file_name = time() . '_' . basename($file["name"]);
    $target_file = $target_dir . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file is actual image or allowed type
    if (!in_array($file_type, $allowed_types)) {
        return ['success' => false, 'name' => "Invalid file type: " . $file_type];
    }

    // Check file size (5MB cap)
    if ($file["size"] > 5000000) {
        return ['success' => false, 'name' => "File is too large (Max 5MB)."];
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ['success' => true, 'name' => $file_name];
    } else {
        return ['success' => false, 'name' => "Failed to upload file."];
    }
}

/**
 * Displays an alert message (Bootstrap 5)
 */
function showAlert($message, $type = 'success') {
    if (empty($message)) return '';
    return "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                {$message}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
}

/**
 * Calculates Age from DOB
 */
function calculateAge($dob) {
    $birthDate = new DateTime($dob);
    $today = new DateTime('today');
    return $birthDate->diff($today)->y;
}
?>
