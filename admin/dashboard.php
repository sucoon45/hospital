<?php
require_once '../config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/database.php';
require_once '../includes/functions/utils.php';

checkRole(['admin']);

// Fetch Analytics
$totalDoctors = countRows('doctors');
$totalPatients = countRows('patients');
$totalAppointments = countRows('appointments', "status != 'Cancelled'");
$totalRevenue = getTotalRevenue();

// Recent Appointments
$recentApps = fetchAll("SELECT a.*, u.full_name as patient_name, doc_u.full_name as doctor_name 
                        FROM appointments a 
                        JOIN patients p ON a.patient_id = p.id 
                        JOIN users u ON p.user_id = u.id 
                        JOIN doctors d ON a.doctor_id = d.id 
                        JOIN users doc_u ON d.user_id = doc_u.id 
                        ORDER BY a.created_at DESC LIMIT 6");

$pageTitle = "Admin Dashboard";
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
        <?php include_once '../includes/components/sidebar.php'; ?>

        <main class="col-lg-10 p-4 offset-lg-2">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 p-4 glassmorphism rounded-4 bg-white shadow-sm">
                <div>
                    <h2 class="mb-0 fw-bold">Admin Central Control</h2>
                    <p class="text-muted mb-0 small"><i class="fas fa-chart-line me-1"></i> Real-time hospital analytics</p>
                </div>
            </div>

            <!-- Dashboard Stats -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100">
                        <div class="bg-soft-blue text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                            <i class="fas fa-user-md fs-4"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?php echo $totalDoctors; ?></h3>
                        <p class="text-muted small mb-0">Total Doctors</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100">
                        <div class="bg-soft-blue text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                            <i class="fas fa-hospital-user fs-4"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?php echo $totalPatients; ?></h3>
                        <p class="text-muted small mb-0">Total Patients</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100">
                        <div class="bg-soft-blue text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                            <i class="fas fa-calendar-check fs-4"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?php echo $totalAppointments; ?></h3>
                        <p class="text-muted small mb-0">Total Bookings</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100 border-start border-4 border-success">
                        <div class="bg-success-subtle text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                            <i class="fas fa-money-bill-wave fs-4"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?php echo formatCurrency($totalRevenue); ?></h3>
                        <p class="text-muted small mb-0">Total Revenue</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- CHART -->
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-4">
                        <h5 class="fw-bold mb-4">Patient Flow Overview</h5>
                        <canvas id="patientChart" height="250"></canvas>
                    </div>
                </div>

                <!-- NOTIFICATIONS / FEED -->
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-4">
                        <h5 class="fw-bold mb-4">Quick Links</h5>
                        <div class="d-grid gap-3">
                            <a href="manage-doctors.php" class="btn btn-outline-primary rounded-pill p-3 text-start small fw-bold border-2"><i class="fas fa-user-md me-3"></i> Add New Doctor</a>
                            <a href="manage-departments.php" class="btn btn-outline-primary rounded-pill p-3 text-start small fw-bold border-2"><i class="fas fa-hospital me-3"></i> Set Departments</a>
                            <a href="pharmacy.php" class="btn btn-outline-primary rounded-pill p-3 text-start small fw-bold border-2"><i class="fas fa-capsules me-3"></i> Stock Pharmacy</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RECENT APPOINTMENTS TABLE -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h5 class="fw-bold mb-0">Recent Appointment Requests</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border-0 mb-0">
                                    <thead class="bg-light text-secondary small text-uppercase">
                                        <tr>
                                            <th class="p-4 border-0">Patient</th>
                                            <th class="p-4 border-0">Specialist</th>
                                            <th class="p-4 border-0 text-center">Date</th>
                                            <th class="p-4 border-0 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($recentApps)): ?>
                                            <tr><td colspan="4" class="text-center py-5">No recent appointments.</td></tr>
                                        <?php else: ?>
                                            <?php foreach($recentApps as $app): ?>
                                                <tr>
                                                    <td class="p-4 border-0">
                                                        <div class="fw-bold"><?php echo $app['patient_name']; ?></div>
                                                        <div class="small text-muted">ID: #<?php echo $app['patient_id']; ?></div>
                                                    </td>
                                                    <td class="p-4 border-0 fw-medium">Dr. <?php echo $app['doctor_name']; ?></td>
                                                    <td class="p-4 border-0 text-center small"><?php echo date('M d, Y', strtotime($app['appointment_date'])); ?></td>
                                                    <td class="p-4 border-0 text-center">
                                                        <span class="badge border rounded-pill px-3 py-2 bg-light text-dark"><?php echo $app['status']; ?></span>
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

<script>
// Mock Chart Data
const ctx = document.getElementById('patientChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Admissions',
            data: [65, 59, 80, 81, 56, 55],
            borderColor: '#007bff',
            tension: 0.4,
            fill: true,
            backgroundColor: 'rgba(0,123,255,0.05)'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
            x: { grid: { display: false } }
        }
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
