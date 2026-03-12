<?php
require_once '../config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/database.php';
require_once '../includes/functions/utils.php';

checkRole(['doctor']);

$user_id = $_SESSION['user_id'];
$doctor = fetchOne("SELECT * FROM doctors WHERE user_id = ?", [$user_id]);
$doctor_id = $doctor['id'];

// Stats
$today = date('Y-m-d');
$todayApps = fetchAll("SELECT a.*, u.full_name as patient_name, u.phone, p.blood_group 
                       FROM appointments a 
                       JOIN patients p ON a.patient_id = p.id 
                       JOIN users u ON p.user_id = u.id 
                       WHERE a.doctor_id = ? AND DATE(a.appointment_date) = ? AND a.status != 'Cancelled' 
                       ORDER BY a.appointment_date ASC", [$doctor_id, $today]);

$totalConsultations = countRows('appointments', "doctor_id = ? AND status = 'Completed'", [$doctor_id]);
$pendingRequests = countRows('appointments', "doctor_id = ? AND status = 'Pending'", [$doctor_id]);

$pageTitle = "Doctor Dashboard";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . " | " . APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-soft-blue">

<div class="container-fluid">
    <div class="row">
        <?php include_once '../includes/components/sidebar.php'; ?>

        <main class="col-lg-10 p-4 offset-lg-2">
            <!-- Header Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4 p-4 glassmorphism rounded-4 bg-white shadow-sm">
                <div>
                    <h2 class="mb-0 fw-bold">Dr. <?php echo $_SESSION['full_name']; ?></h2>
                    <p class="text-muted mb-0 small"><i class="fas fa-stethoscope me-1 text-primary"></i> Medical Professional Portal</p>
                </div>
                <div class="text-end">
                   <div class="small fw-bold text-secondary">Department</div>
                   <span class="badge bg-primary rounded-pill px-3 py-2 small"><?php echo $doctor['specialization'] ?? 'General Practitioner'; ?></span>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100 border-bottom border-4 border-primary">
                        <i class="fas fa-calendar-alt fs-2 text-primary mb-3"></i>
                        <h3 class="fw-bold mb-1"><?php echo count($todayApps); ?></h3>
                        <p class="text-muted small mb-0 fw-bold uppercase">Scheduled Today</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100 border-bottom border-4 border-warning">
                        <i class="fas fa-spinner fs-2 text-warning mb-3"></i>
                        <h3 class="fw-bold mb-1"><?php echo $pendingRequests; ?></h3>
                        <p class="text-muted small mb-0 fw-bold uppercase">Pending Approvals</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100 border-bottom border-4 border-success">
                        <i class="fas fa-check-circle fs-2 text-success mb-3"></i>
                        <h3 class="fw-bold mb-1"><?php echo $totalConsultations; ?></h3>
                        <p class="text-muted small mb-0 fw-bold uppercase">Total Consultations</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- TODAY'S SCHEDULE -->
                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                        <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Today's Appointment Schedule</h5>
                            <span class="small text-muted fw-bold"><?php echo date('M d, Y'); ?></span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border-0 mb-0">
                                    <thead class="bg-light text-secondary small text-uppercase fw-bold">
                                        <tr>
                                            <th class="p-4 border-0">Time</th>
                                            <th class="p-4 border-0">Patient Name</th>
                                            <th class="p-4 border-0 text-center">B. Group</th>
                                            <th class="p-4 border-0 text-center">Mobile</th>
                                            <th class="p-4 border-0 text-center">Status</th>
                                            <th class="p-4 border-0 text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($todayApps)): ?>
                                            <tr><td colspan="6" class="text-center py-5"><i class="fas fa-calendar-day fs-1 text-light mb-3"></i><p class="text-muted mb-0">Relax! No more appointments for today.</p></td></tr>
                                        <?php else: ?>
                                            <?php foreach($todayApps as $app): ?>
                                                <tr>
                                                    <td class="p-4 border-0 fw-bold text-primary"><?php echo date('h:i A', strtotime($app['appointment_date'])); ?></td>
                                                    <td class="p-4 border-0">
                                                        <div class="fw-bold text-dark"><?php echo $app['patient_name']; ?></div>
                                                        <div class="small text-muted">Apt-ID: #<?php echo $app['id']; ?></div>
                                                    </td>
                                                    <td class="p-4 border-0 text-center fw-bold text-danger"><?php echo $app['blood_group'] ?: 'N/A'; ?></td>
                                                    <td class="p-4 border-0 text-center small"><?php echo $app['phone']; ?></td>
                                                    <td class="p-4 border-0 text-center">
                                                        <span class="badge border bg-success-subtle text-success rounded-pill px-3 py-2 small"><?php echo $app['status']; ?></span>
                                                    </td>
                                                    <td class="p-4 border-0 text-end">
                                                        <a href="patient-view.php?id=<?php echo $app['patient_id']; ?>&apt=<?php echo $app['id']; ?>" class="btn btn-primary-gradient btn-sm rounded-pill px-3 fw-bold">Consult <i class="fas fa-arrow-right ms-2 fs-7"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
