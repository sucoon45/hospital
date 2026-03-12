<?php
require_once '../config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/database.php';
require_once '../includes/functions/utils.php';

checkRole(['patient']);

$user_id = $_SESSION['user_id'];
$patient = fetchOne("SELECT * FROM patients WHERE user_id = ?", [$user_id]);
$patient_id = $patient['id'];

// Mock stats for dashboard
$upcoming_appointments = fetchAll("SELECT a.*, d.full_name as doctor_name FROM appointments a 
                                   JOIN doctors doc ON a.doctor_id = doc.id 
                                   JOIN users d ON doc.user_id = d.id 
                                   WHERE a.patient_id = ? AND a.status = 'Approved' 
                                   ORDER BY a.appointment_date ASC LIMIT 5", [$patient_id]);

$recent_records = fetchAll("SELECT * FROM medical_records WHERE patient_id = ? ORDER BY visit_date DESC LIMIT 5", [$patient_id]);

$pageTitle = "Patient Dashboard";
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-soft-blue">

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <?php include_once '../includes/components/sidebar.php'; ?>

        <!-- MAIN CONTENT -->
        <main class="col-lg-10 p-4 offset-lg-2">
            <!-- Top Dashboard Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4 p-4 glassmorphism rounded-4 bg-white shadow-sm">
                <div>
                    <h2 class="mb-0 fw-bold">Dashboard</h2>
                    <p class="text-muted mb-0 small"><i class="fas fa-calendar-day me-1"></i> Today is <?php echo date('l, d M Y'); ?></p>
                </div>
                <div class="d-flex align-items-center">
                    <div class="dropdown me-3">
                        <button class="btn btn-light rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell text-primary"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-3 rounded-4">
                            <li class="dropdown-header fw-bold text-primary">Notifications</li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item small" href="#">No new notifications</a></li>
                        </ul>
                    </div>
                    <a href="profile.php" class="btn btn-outline-primary rounded-pill px-4 btn-sm fw-bold">My Profile <i class="fas fa-user-circle ms-2"></i></a>
                </div>
            </div>

            <!-- WELCOME BANNER -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-lg rounded-5 overflow-hidden text-white" style="background: linear-gradient(135deg, #007bff, #003366);">
                        <div class="card-body p-5">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3 class="fw-bold mb-3">Hello, <?php echo $_SESSION['full_name']; ?>! 👋</h3>
                                    <p class="lead mb-4 opacity-75">Your health journey with NovaCare Specialist is our top priority. Access your records, book new appointments, or consult with our experts virtually.</p>
                                    <a href="appointments.php" class="btn btn-light rounded-pill px-4 text-primary fw-bold shadow-sm">Book New Appointment <i class="fas fa-plus-circle ms-2"></i></a>
                                </div>
                                <div class="col-md-4 d-none d-md-block text-center opacity-25">
                                    <i class="fas fa-hospital-user" style="font-size: 150px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATS COUNTER -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100">
                        <div class="bg-soft-blue text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-calendar-check fs-4"></i>
                        </div>
                        <h4 class="fw-bold mb-1"><?php echo count($upcoming_appointments); ?></h4>
                        <p class="text-muted small mb-0">Upcoming Appointments</p>
                    </div>
                </div>
                <!-- Add more stat cards as needed -->
            </div>

            <!-- QUICK ACTIONS & RECENT ACTIVITY -->
            <div class="row">
                <!-- UPCOMING APPOINTMENTS -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 p-0 h-100 bg-white">
                        <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">Upcoming Appointments</h5>
                            <a href="appointments.php" class="small text-primary text-decoration-none fw-bold">View All</a>
                        </div>
                        <div class="card-body p-4 pt-0">
                            <?php if(empty($upcoming_appointments)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-times fs-1 text-light mb-3"></i>
                                    <p class="text-muted">No approved appointments yet.</p>
                                    <a href="appointments.php" class="btn btn-primary rounded-pill btn-sm">Start Booking</a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle border-0">
                                        <thead class="small text-secondary">
                                            <tr>
                                                <th class="border-0">Doctor</th>
                                                <th class="border-0">Date & Time</th>
                                                <th class="border-0">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($upcoming_appointments as $app): ?>
                                                <tr>
                                                    <td class="border-0 fw-bold">Dr. <?php echo $app['doctor_name']; ?></td>
                                                    <td class="border-0 small"><?php echo date('M d, Y | h:i A', strtotime($app['appointment_date'])); ?></td>
                                                    <td class="border-0">
                                                        <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3"><?php echo $app['status']; ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- RECENT MEDICAL HISTORY -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 p-0 h-100 bg-white">
                        <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">Medical History</h5>
                            <a href="medical-records.php" class="small text-primary text-decoration-none fw-bold">See Timeline</a>
                        </div>
                        <div class="card-body p-4 pt-0">
                            <?php if(empty($recent_records)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-folder-open fs-1 text-light mb-3"></i>
                                    <p class="text-muted">No recent medical histories found.</p>
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush border-0">
                                    <?php foreach($recent_records as $rec): ?>
                                        <div class="list-group-item px-0 py-3 border-bottom d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3 bg-soft-blue p-2 rounded-3 text-primary"><i class="fas fa-notes-medical"></i></div>
                                                <div>
                                                    <p class="mb-0 fw-bold"><?php echo $rec['diagnosis']; ?></p>
                                                    <p class="small text-muted mb-0"><?php echo date('M d, Y', strtotime($rec['visit_date'])); ?></p>
                                                </div>
                                            </div>
                                            <a href="record-view.php?id=<?php echo $rec['id']; ?>" class="btn btn-light rounded-pill btn-sm text-primary">View</a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
