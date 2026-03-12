<?php
require_once '../config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/database.php';
require_once '../includes/functions/utils.php';

checkRole(['doctor']);

$user_id = $_SESSION['user_id'];
$doctor = fetchOne("SELECT id FROM doctors WHERE user_id = ?", [$user_id]);
$doctor_id = $doctor['id'];

$patient_id = $_GET['id'] ?? null;
$apt_id = $_GET['apt'] ?? null;

if (!$patient_id) { redirect('dashboard.php'); }

// Fetch Patient Info
$patient = fetchOne("SELECT p.*, u.full_name, u.email, u.phone 
                    FROM patients p 
                    JOIN users u ON p.user_id = u.id 
                    WHERE p.id = ?", [$patient_id]);

$error = '';
$success = '';

// Handle New Record Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_emr'])) {
    $diagnosis = sanitize($_POST['diagnosis']);
    $symptoms = sanitize($_POST['symptoms']);
    $notes = sanitize($_POST['notes']);
    $vitals = json_encode([
        'BP' => $_POST['v_bp'],
        'Temp' => $_POST['v_temp'],
        'Weight' => $_POST['v_weight']
    ]);

    if(addMedicalRecord($patient_id, $doctor_id, $diagnosis, $symptoms, $notes, $vitals)) {
        // Mark appointment as completed if it exists
        if($apt_id) {
            runQuery("UPDATE appointments SET status = 'Completed' WHERE id = ?", [$apt_id]);
        }
        $success = "Medical record saved successfully.";
    }
}

// Handle Lab Test Request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_lab'])) {
    $test_name = sanitize($_POST['test_name']);
    $category = sanitize($_POST['test_category']);
    
    if (runQuery("INSERT INTO lab_tests (patient_id, doctor_id, test_name, test_category) VALUES (?, ?, ?, ?)", [$patient_id, $doctor_id, $test_name, $category])) {
        $success = "Lab request sent to diagnostics center.";
    } else {
        $error = "Failed to request lab test.";
    }
}

$history = getPatientFullHistory($patient_id);
$lab_tests = fetchAll("SELECT * FROM lab_tests WHERE patient_id = ? ORDER BY requested_at DESC", [$patient_id]);
$pageTitle = "Patient EMR - " . $patient['full_name'];
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="dashboard.php" class="btn btn-light"><i class="fas fa-arrow-left me-2"></i> Dashboard</a>
                <h4 class="mb-0 fw-bold">Electronic Medical Record</h4>
                <button class="btn btn-warning fw-bold rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#requestLabModal"><i class="fas fa-vial me-2"></i> Request Lab</button>
            </div>

            <?php echo showAlert($error, 'danger'); ?>
            <?php echo showAlert($success, 'success'); ?>


            <!-- Patient Quick Info -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100">
                        <img src="../assets/images/default_user.jpg" class="rounded-circle mx-auto mb-3 border border-3 border-primary" width="80" height="80">
                        <h5 class="fw-bold mb-1"><?php echo $patient['full_name']; ?></h5>
                        <p class="small text-muted mb-3">PID: #<?php echo $patient_id; ?></p>
                        <div class="d-flex justify-content-center gap-2">
                             <span class="badge bg-danger rounded-pill px-3"><?php echo $patient['blood_group'] ?: 'N/A'; ?></span>
                             <span class="badge bg-secondary rounded-pill px-3"><?php echo $patient['genotype'] ?: 'N/A'; ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small text-muted fw-bold">Phone</label>
                                <p class="fw-medium mb-0"><?php echo $patient['phone']; ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small text-muted fw-bold">Age / Gender</label>
                                <p class="fw-medium mb-0"><?php echo calculateAge($patient['dob']); ?>Y / <?php echo $patient['gender']; ?></p>
                            </div>
                            <div class="col-12">
                                <label class="small text-muted fw-bold">Known NHIS ID</label>
                                <p class="fw-medium mb-0"><?php echo $patient['nhis_id'] ?: 'Not Enrolled'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- EMR TIMELINE -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h5 class="fw-bold mb-0">Medical History Timeline</h5>
                        </div>
                        <div class="card-body p-4 pt-0 overflow-auto" style="max-height: 600px;">
                            <?php if(empty($history)): ?>
                                <div class="text-center py-5">
                                    <p class="text-muted">No prior records for this patient.</p>
                                </div>
                            <?php else: ?>
                                <div class="timeline-v">
                                    <?php foreach($history as $rec): 
                                        $v = json_decode($rec['vitals'], true);
                                    ?>
                                        <div class="timeline-item border-start border-2 border-primary ps-4 pb-4 position-relative">
                                            <div class="position-absolute start-0 translate-middle-x bg-primary rounded-circle" style="width: 12px; height: 12px; margin-left: -1px; margin-top: 10px;"></div>
                                            <div class="card border-0 bg-light rounded-4 p-3 border-start border-4 border-primary">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="small fw-bold text-primary"><?php echo date('M d, Y', strtotime($rec['visit_date'])); ?></span>
                                                    <span class="small text-muted">By Dr. <?php echo $rec['doctor_name']; ?></span>
                                                </div>
                                                <h6 class="fw-bold mb-1">Diagnosis: <?php echo $rec['diagnosis']; ?></h6>
                                                <p class="small text-muted mb-2">Symptoms: <?php echo $rec['symptoms']; ?></p>
                                                <?php if($v): ?>
                                                    <div class="bg-white p-2 rounded-3 small mb-2 d-flex gap-3">
                                                        <span><strong>BP:</strong> <?php echo $v['BP'] ?? 'N/A'; ?></span>
                                                        <span><strong>Temp:</strong> <?php echo $v['Temp'] ?? 'N/A'; ?>°C</span>
                                                        <span><strong>Wt:</strong> <?php echo $v['Weight'] ?? 'N/A'; ?>kg</span>
                                                    </div>
                                                <?php endif; ?>
                                                <p class="small mb-0 text-secondary"><strong>Notes:</strong> <?php echo $rec['notes']; ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- LAB TESTS SECTION -->
                    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                        <div class="card-header bg-transparent border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Laboratory Diagnostics</h5>
                        </div>
                        <div class="card-body p-4 pt-2">
                            <?php if(empty($lab_tests)): ?>
                                <p class="text-muted small">No lab tests requested for this patient.</p>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach($lab_tests as $lab): 
                                        $badge = $lab['status'] == 'Completed' ? 'bg-success' : ($lab['status'] == 'In Progress' ? 'bg-primary' : 'bg-warning text-dark');
                                    ?>
                                        <div class="list-group-item border-0 border-bottom p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="fw-bold text-dark mb-0"><?php echo $lab['test_name']; ?> <span class="badge bg-light text-secondary border ms-2"><?php echo $lab['test_category']; ?></span></h6>
                                                <span class="badge <?php echo $badge; ?> rounded-pill small"><?php echo $lab['status']; ?></span>
                                            </div>
                                            <div class="small text-muted mb-2">Requested: <?php echo date('M d, Y h:i A', strtotime($lab['requested_at'])); ?></div>
                                            
                                            <?php if($lab['status'] == 'Completed'): ?>
                                                <div class="bg-light p-3 rounded-3 small text-dark fw-medium mb-2">
                                                    <strong>Result:</strong> <?php echo nl2br(htmlspecialchars($lab['result'])); ?>
                                                </div>
                                                <?php if($lab['report_file']): ?>
                                                    <a href="../assets/uploads/labs/<?php echo $lab['report_file']; ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill"><i class="fas fa-file-pdf me-1"></i> View Attached Report</a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ADD NEW RECORD -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 bg-white sticky-top" style="top: 20px;">
                        <div class="card-header bg-primary-gradient text-white p-4">
                            <h5 class="fw-bold mb-0">Record Consultation</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="" method="POST">
                                <input type="hidden" name="save_emr" value="1">
                                
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="small fw-bold">BP</label>
                                        <input type="text" name="v_bp" class="form-control form-control-sm" placeholder="120/80">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small fw-bold">Temp (°C)</label>
                                        <input type="text" name="v_temp" class="form-control form-control-sm" placeholder="36.5">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small fw-bold">Weight (kg)</label>
                                        <input type="text" name="v_weight" class="form-control form-control-sm" placeholder="70">
                                    </div>
                                    <div class="col-12">
                                        <label class="small fw-bold">Symptoms</label>
                                        <input type="text" name="symptoms" class="form-control" placeholder="Fever, cough..." required>
                                    </div>
                                    <div class="col-12">
                                        <label class="small fw-bold">Diagnosis</label>
                                        <input type="text" name="diagnosis" class="form-control" placeholder="Enter findings..." required>
                                    </div>
                                    <div class="col-12">
                                        <label class="small fw-bold">Clinical Notes</label>
                                        <textarea name="notes" class="form-control" rows="4" placeholder="Additional observations..."></textarea>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 mt-4 py-2 fw-bold rounded-pill">Save Medical Record <i class="fas fa-save ms-2"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Request Lab Modal -->
<div class="modal fade" id="requestLabModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-warning text-dark p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-vial me-2"></i> Request Diagnostic Test</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <form action="" method="POST">
                    <input type="hidden" name="request_lab" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Test Category</label>
                        <select name="test_category" class="form-select" required>
                            <option value="Hematology">Hematology</option>
                            <option value="Biochemistry">Biochemistry</option>
                            <option value="Microbiology">Microbiology</option>
                            <option value="Pathology">Pathology</option>
                            <option value="Radiology (Imaging)">Radiology (Imaging)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Exact Test Name / Instruction</label>
                        <input type="text" name="test_name" class="form-control" placeholder="e.g. Fasting Blood Sugar, Chest X-Ray..." required>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-warning w-100 rounded-pill py-3 fw-bold shadow-sm">
                            Send Request to Lab <i class="fas fa-paper-plane ms-2"></i>
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
