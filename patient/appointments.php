<?php
require_once '../config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/database.php';
require_once '../includes/functions/utils.php';

checkRole(['patient']);

$user_id = $_SESSION['user_id'];
$patient = fetchOne("SELECT * FROM patients WHERE user_id = ?", [$user_id]);
$patient_id = $patient['id'];

$error = '';
$success = '';

// Handling New Appointment Booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_appointment'])) {
    $doctor_id = sanitize($_POST['doctor_id']);
    $date = sanitize($_POST['date']);
    $time = sanitize($_POST['time']);
    $reason = sanitize($_POST['reason']);
    
    // Combining date and time for MySQL DATETIME
    $full_datetime = $date . " " . $time;
    
    // Fetch dept_id for the selected doctor
    $doc_info = fetchOne("SELECT dept_id FROM doctors WHERE id = ?", [$doctor_id]);
    $dept_id = $doc_info['dept_id'];
    
    $sql = "INSERT INTO appointments (patient_id, doctor_id, dept_id, appointment_date, reason, status) 
            VALUES (?, ?, ?, ?, ?, 'Pending')";
    
    if (runQuery($sql, [$patient_id, $doctor_id, $dept_id, $full_datetime, $reason])) {
        $success = "Your appointment request has been submitted and is pending approval.";
    } else {
        $error = "Failed to book appointment. Please try again.";
    }
}

// Fetch existing appointments
$appointments = fetchAll("SELECT a.*, u.full_name as doctor_name, dep.name as dept_name 
                          FROM appointments a 
                          JOIN doctors d ON a.doctor_id = d.id 
                          JOIN users u ON d.user_id = u.id 
                          JOIN departments dep ON a.dept_id = dep.id 
                          WHERE a.patient_id = ? 
                          ORDER BY a.appointment_date DESC", [$patient_id]);

$doctors = getDoctors();
$pageTitle = "My Appointments";
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
            <div class="d-flex justify-content-between align-items-center mb-4 p-4 glassmorphism rounded-4 bg-white shadow-sm border-0">
                <h2 class="mb-0 fw-bold">My Appointments</h2>
                <button class="btn btn-primary-gradient rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#bookingModal">
                    New Appointment <i class="fas fa-plus ms-2"></i>
                </button>
            </div>

            <?php echo showAlert($error, 'danger'); ?>
            <?php echo showAlert($success, 'success'); ?>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-0 mb-0">
                            <thead class="bg-light text-secondary small text-uppercase fw-bold">
                                <tr>
                                    <th class="p-4 border-0">Doctor / Department</th>
                                    <th class="p-4 border-0 text-center">Date & Time</th>
                                    <th class="p-4 border-0 text-center">Status</th>
                                    <th class="p-4 border-0 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($appointments)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i class="fas fa-calendar-times fs-1 text-light mb-3"></i>
                                            <p class="text-muted">No appointments found. Start by booking one today!</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($appointments as $app): 
                                        $statusClass = [
                                            'Pending' => 'bg-warning-subtle text-warning border-warning',
                                            'Approved' => 'bg-success-subtle text-success border-success',
                                            'Cancelled' => 'bg-danger-subtle text-danger border-danger',
                                            'Rescheduled' => 'bg-info-subtle text-info border-info',
                                            'Completed' => 'bg-secondary-subtle text-secondary border-secondary'
                                        ];
                                        $class = $statusClass[$app['status']] ?? 'bg-light';
                                    ?>
                                        <tr class="border-bottom">
                                            <td class="p-4 border-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-soft-blue p-2 rounded-3 text-primary me-3"><i class="fas fa-user-md fs-4"></i></div>
                                                    <div>
                                                        <p class="mb-0 fw-bold">Dr. <?php echo $app['doctor_name']; ?></p>
                                                        <p class="small text-muted mb-0"><?php echo $app['dept_name']; ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-4 border-0 text-center fw-medium small">
                                                <?php echo date('M d, Y', strtotime($app['appointment_date'])); ?><br>
                                                <span class="text-muted"><?php echo date('h:i A', strtotime($app['appointment_date'])); ?></span>
                                            </td>
                                            <td class="p-4 border-0 text-center">
                                                <span class="badge border rounded-pill px-3 py-2 <?php echo $class; ?>"><?php echo $app['status']; ?></span>
                                            </td>
                                            <td class="p-4 border-0 text-end">
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                                        <li><a class="dropdown-item small" href="#"><i class="fas fa-eye me-2"></i> View Details</a></li>
                                                        <?php if($app['status'] == 'Pending'): ?>
                                                            <li><a class="dropdown-item small text-danger" href="#"><i class="fas fa-times me-2"></i> Cancel</a></li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
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

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary-gradient text-white p-4">
                <h5 class="modal-title fw-bold">Book an Appointment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5 bg-white">
                <form action="appointments.php" method="POST">
                    <input type="hidden" name="book_appointment" value="1">
                    
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Choose Specialist</label>
                            <select name="doctor_id" class="form-select border-start-0 border-end-0 border-top-0 border-bottom-2 border-primary rounded-0 px-0" required>
                                <option value="">Select a Doctor</option>
                                <?php foreach($doctors as $doc): ?>
                                    <option value="<?php echo $doc['id']; ?>">Dr. <?php echo $doc['doctor_name']; ?> (<?php echo $doc['dept_name']; ?> - <?php echo $doc['specialization']; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Preferred Date</label>
                            <input type="date" name="date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Preferred Time</label>
                            <input type="time" name="time" class="form-control" required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-bold small">Reason for Visit</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="Briefly describe your symptoms or reason..." required></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-5 text-center">
                        <button type="submit" class="btn btn-primary-gradient px-5 py-3 rounded-pill fw-bold shadow-sm">
                            Submit Request <i class="fas fa-check-circle ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
