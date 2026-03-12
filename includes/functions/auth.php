<?php
/**
 * User Authentication & Role Management
 */

/**
 * Registers a new patient
 */
function registerPatient($full_name, $email, $password, $phone = '', $dob = '', $gender = '', $address = '') {
    $pdo = getDBConnection();
    try {
        $pdo->beginTransaction();
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt_user = $pdo->prepare("INSERT INTO users (full_name, email, password, role, phone) VALUES (?, ?, ?, 'patient', ?)");
        $stmt_user->execute([$full_name, $email, $hashed_password, $phone]);
        $user_id = $pdo->lastInsertId();
        
        $stmt_patient = $pdo->prepare("INSERT INTO patients (user_id, dob, gender, address) VALUES (?, ?, ?, ?)");
        $stmt_patient->execute([$user_id, $dob, $gender, $address]);
        
        $pdo->commit();
        return $user_id;
        
    } catch (\PDOException $e) {
        $pdo->rollBack();
        if ($e->getCode() == 23000) {
            return "An account with this email already exists.";
        }
        return "Registration failed: " . $e->getMessage();
    }
}

/**
 * Logins a user
 */
function loginUser($email, $password) {
    if (empty($email) || empty($password)) {
        return "Please fill all fields.";
    }

    $user = fetchOne("SELECT * FROM users WHERE email = ? AND status = 1", [$email]);

    if ($user && password_verify($password, $user['password'])) {
        // Regenerate session for security
        session_regenerate_id();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];

        // Update last login
        runQuery("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);
        return true;
    }
    return "Invalid email or password.";
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Logout the user
 */
function logoutUser() {
    session_unset();
    session_destroy();
    header("Location: " . APP_URL . "/login.php");
    exit();
}

/**
 * Middleware: Redirect if not authorized for role
 */
function checkRole($allowed_roles = []) {
    if (!isLoggedIn()) {
        header("Location: " . APP_URL . "/login.php");
        exit();
    }

    if (!in_array($_SESSION['role'], $allowed_roles)) {
        header("Location: " . APP_URL . "/unauthorized.php");
        exit();
    }
}

/**
 * Middleware: Redirect if already logged in (for login/register pages)
 */
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        $dashboard = $_SESSION['role'] . "/dashboard.php";
        header("Location: " . APP_URL . "/" . $dashboard);
        exit();
    }
}
?>
