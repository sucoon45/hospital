<?php
/**
 * Database Functions using PDO
 */

function getDBConnection() {
    $host = 'localhost';
    $db   = 'kamirex_hms';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        // Log error and show a user-friendly message
        error_log("Database Connection Error: " . $e->getMessage());
        die("System Error: Could not connect to the database. Join us later.");
    }
}

/**
 * Executes a SELECT query and returns all results
 */
function fetchAll($sql, $params = []) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Executes a SELECT query and returns a single row
 */
function fetchOne($sql, $params = []) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

/**
 * Executes an INSERT, UPDATE, or DELETE query
 */
function runQuery($sql, $params = []) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

/**
 * Returns all active departments
 */
function getDepartments() {
    return fetchAll("SELECT * FROM departments ORDER BY name ASC");
}

/**
 * Returns all active doctors with their department details
 */
function getDoctors() {
    return fetchAll("SELECT d.id, u.full_name as doctor_name, u.email, d.specialization, dp.name as dept_name 
                    FROM doctors d 
                    JOIN users u ON d.user_id = u.id 
                    JOIN departments dp ON d.dept_id = dp.id 
                    WHERE u.role = 'doctor' AND u.status = 1 
                    ORDER BY u.full_name ASC");
}

/**
 * Returns count of rows in a table with an optional WHERE clause
 */
function countRows($table, $where = "", $params = []) {
    $sql = "SELECT COUNT(*) as count FROM $table" . ($where ? " WHERE $where" : "");
    $res = fetchOne($sql, $params);
    return (int)$res['count'];
}

/**
 * Returns total revenue from payments
 */
function getTotalRevenue() {
    $sql = "SELECT SUM(amount) as total FROM payments WHERE payment_status = 'Success'";
    $res = fetchOne($sql);
    return (float)$res['total'] ?? 0.00;
}

/**
 * Get Full Patient History
 */
function getPatientFullHistory($patient_id) {
    return fetchAll("SELECT mr.*, u.full_name as doctor_name 
                    FROM medical_records mr 
                    JOIN doctors d ON mr.doctor_id = d.id 
                    JOIN users u ON d.user_id = u.id 
                    WHERE mr.patient_id = ? 
                    ORDER BY mr.visit_date DESC", [$patient_id]);
}

/**
 * Add Medical Record
 */
function addMedicalRecord($patient_id, $doctor_id, $diagnosis, $symptoms, $notes, $vitals = null) {
    $sql = "INSERT INTO medical_records (patient_id, doctor_id, visit_date, diagnosis, symptoms, notes, vitals) 
            VALUES (?, ?, CURDATE(), ?, ?, ?, ?)";
    return runQuery($sql, [$patient_id, $doctor_id, $diagnosis, $symptoms, $notes, $vitals]);
}

/**
 * Returns the last inserted ID
 */
function lastInsertId() {
    return getDBConnection()->lastInsertId();
}
?>
