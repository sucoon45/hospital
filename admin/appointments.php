<?php
require_once '../config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/database.php';
require_once '../includes/functions/utils.php';

checkRole(['admin', 'receptionist']);

// Optional Status Update
if(isset($_POST['update_status']) && isset($_POST['apt_id']) && isset($_POST['status'])) {
    $apt_id = (int)$_POST['apt_id'];
    $status = sanitize($_POST['status']);
    runQuery("UPDATE appointments SET status = ? WHERE id = ?", [$status, $apt_id]);
}

// Fetch All Appointments
$appointments = fetchAll("
    SELECT a.*, u.full_name as patient_name, u.phone as patient_phone, d_user.full_name as doctor_name, dept.name as dept_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN users u ON p.user_id = u.id
    JOIN doctors d ON a.doctor_id = d.id
    JOIN users d_user ON d.user_id = d_user.id
    JOIN departments dept ON d.dept_id = dept.id
    ORDER BY a.appointment_date DESC, a.created_at DESC
");

$pageTitle = "Hospital Appointments";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-soft-blue">

<div class="container-fluid">
    <div class="row">
        <?php include_once '../includes/components/sidebar.php'; ?>

        <main class="col-lg-10 p-4 offset-lg-2">
            <div class="d-flex justify-content-between align-items-center mb-4 p-4 glassmorphism rounded-4 bg-white shadow-sm border-0">
                <div>
                   <h2 class="mb-0 fw-bold">All Appointments</h2>
                   <p class="text-muted small mb-0">Centralized view of all hospital bookings and schedules.</p>
                </div>
                <div>
                    <!-- Could add an export button here later -->
                    <button class="btn btn-outline-primary rounded-pill px-4 shadow-sm fw-bold"><i class="fas fa-download me-2"></i> Export CSV</button>
                    <button class="btn btn-primary-gradient rounded-pill px-4 shadow-sm fw-bold ms-2" data-bs-toggle="modal" data-bs-target="#newAptModal"><i class="fas fa-plus me-2"></i> Walk-in Booking</button>
                </div>
            </div>

            <!-- APPOINTMENTS TABLE -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-0 mb-0">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="p-4 border-0">Apt ID</th>
                                    <th class="p-4 border-0">Patient Details</th>
                                    <th class="p-4 border-0">Assigned Doctor</th>
                                    <th class="p-4 border-0">Date & Time</th>
                                    <th class="p-4 border-0 text-center">Status</th>
                                    <th class="p-4 border-0 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($appointments)): ?>
                                    <tr><td colspan="6" class="text-center py-5 text-muted"><i class="fas fa-calendar-times fs-2 mb-3 d-block text-secondary"></i> No appointments found.</td></tr>
                                <?php else: ?>
                                    <?php foreach($appointments as $apt): 
                                        $statusClass = 'bg-warning text-dark';
                                        if($apt['status'] == 'Confirmed') $statusClass = 'bg-primary-subtle border-primary text-primary';
                                        if($apt['status'] == 'Completed') $statusClass = 'bg-success-subtle border-success text-success';
                                        if($apt['status'] == 'Cancelled') $statusClass = 'bg-danger-subtle border-danger text-danger';
                                    ?>
                                        <tr>
                                            <td class="p-4 border-0 fw-bold text-muted small">#<?php echo str_pad($apt['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                            <td class="p-4 border-0">
                                                <div class="fw-bold text-dark"><?php echo $apt['patient_name']; ?></div>
                                                <div class="small text-muted"><i class="fas fa-phone-alt me-1 fs-7"></i> <?php echo $apt['patient_phone']; ?></div>
                                            </td>
                                            <td class="p-4 border-0">
                                                <div class="fw-bold text-secondary">Dr. <?php echo $apt['doctor_name']; ?></div>
                                                <div class="small text-muted"><?php echo $apt['dept_name']; ?></div>
                                            </td>
                                            <td class="p-4 border-0 fw-medium">
                                                <?php echo date('M d, Y', strtotime($apt['appointment_date'])); ?><br>
                                                <small class="text-primary fw-bold"><i class="far fa-clock me-1"></i> <?php echo date('h:i A', strtotime($apt['appointment_date'])); ?></small>
                                            </td>
                                            <td class="p-4 border-0 text-center">
                                                <span class="badge <?php echo $statusClass; ?> border rounded-pill px-3 py-2 small shadow-sm"><?php echo $apt['status']; ?></span>
                                            </td>
                                            <td class="p-4 border-0 text-center">
                                                <form action="" method="POST" class="d-inline">
                                                    <input type="hidden" name="update_status" value="1">
                                                    <input type="hidden" name="apt_id" value="<?php echo $apt['id']; ?>">
                                                    
                                                    <div class="dropdown">
                                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm text-primary" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v px-1"></i></button>
                                                        <ul class="dropdown-menu border-0 shadow-lg rounded-3 text-small py-2">
                                                            <li><button type="submit" name="status" value="Confirmed" class="dropdown-item small text-primary fw-medium"><i class="fas fa-check-circle me-2"></i> Confirm</button></li>
                                                            <li><button type="submit" name="status" value="Completed" class="dropdown-item small text-success fw-medium"><i class="fas fa-calendar-check me-2"></i> Mark Completed</button></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li><button type="submit" name="status" value="Cancelled" class="dropdown-item small text-danger fw-medium"><i class="fas fa-times-circle me-2"></i> Cancel</button></li>
                                                        </ul>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal for Walk-in Booking (UI Only snippet) -->
<div class="modal fade" id="newAptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary-gradient text-white p-4">
                <h5 class="modal-title fw-bold">Schedule Walk-in</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fas fa-info-circle fs-1 text-primary mb-3"></i>
                <p class="text-muted">Walk-in functionality requires directly linking an external patient ID. Do you want to proceed to the registration terminal?</p>
                <div class="mt-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <a href="patients.php" class="btn btn-primary rounded-pill px-4 shadow-sm">Go to Patients</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
