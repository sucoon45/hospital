<?php
require_once '../config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/database.php';
require_once '../includes/functions/utils.php';

checkRole(['admin', 'lab_tech', 'doctor']);

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$error = '';
$success = '';

// Handle updating test results
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_test'])) {
    $test_id = (int)$_POST['test_id'];
    $result = sanitize($_POST['result']);
    
    // Check if a report file was uploaded
    $report_file = null;
    if (isset($_FILES['report_file']) && $_FILES['report_file']['size'] > 0) {
        $upload_dir = '../assets/uploads/labs/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $upload_res = uploadFile($_FILES['report_file'], $upload_dir);
        if ($upload_res['success']) {
            $report_file = $upload_res['name'];
        } else {
            $error = $upload_res['name'];
        }
    }

    if (empty($error)) {
        if ($report_file) {
            $sql = "UPDATE lab_tests SET status = 'Completed', result = ?, report_file = ?, completed_at = NOW(), lab_tech_id = ? WHERE id = ?";
            $params = [$result, $report_file, $user_id, $test_id];
        } else {
            $sql = "UPDATE lab_tests SET status = 'Completed', result = ?, completed_at = NOW(), lab_tech_id = ? WHERE id = ?";
            $params = [$result, $user_id, $test_id];
        }
        
        if (runQuery($sql, $params)) {
             $success = "Test result updated and saved successfully.";
        } else {
             $error = "Failed to update test result.";
        }
    }
}

// Handle Manual Lab Request (Walk-in/Admin direct)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_test'])) {
    $pat_id = (int)$_POST['patient_id'];
    $doc_id = (int)$_POST['doctor_id'];
    $test_name = sanitize($_POST['test_name']);
    $category = sanitize($_POST['test_category']);
    
    if (runQuery("INSERT INTO lab_tests (patient_id, doctor_id, test_name, test_category) VALUES (?, ?, ?, ?)", [$pat_id, $doc_id, $test_name, $category])) {
        $success = "Lab test requested successfully.";
    } else {
        $error = "Failed to request lab test.";
    }
}

// Fetch all tests
$tests = fetchAll("
    SELECT lt.*, u_pat.full_name as patient_name, u_doc.full_name as doctor_name 
    FROM lab_tests lt 
    JOIN patients p ON lt.patient_id = p.id 
    JOIN users u_pat ON p.user_id = u_pat.id
    JOIN doctors d ON lt.doctor_id = d.id 
    JOIN users u_doc ON d.user_id = u_doc.id
    ORDER BY lt.requested_at DESC
");

// Pre-load data for Request Modal
$all_patients = fetchAll("SELECT p.id, u.full_name FROM patients p JOIN users u ON p.user_id = u.id ORDER BY u.full_name ASC");
$all_doctors = fetchAll("SELECT d.id, u.full_name FROM doctors d JOIN users u ON d.user_id = u.id ORDER BY u.full_name ASC");

$pageTitle = "Diagnostics & Laboratory";
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
                   <h2 class="mb-0 fw-bold">Laboratory Control Center</h2>
                   <p class="text-muted small mb-0">Manage test requests, diagnostic workflows, and patient reports.</p>
                </div>
                <div>
                    <button class="btn btn-primary-gradient rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#newRequestModal"><i class="fas fa-plus me-2"></i> Log New Request</button>
                </div>
            </div>

            <?php echo showAlert($error, 'danger'); ?>
            <?php echo showAlert($success, 'success'); ?>
            
            <!-- STATS ROW -->
            <div class="row g-4 mb-4">
                 <?php 
                    $pendingCount = count(array_filter($tests, function($t) { return $t['status'] == 'Requested' || $t['status'] == 'In Progress'; }));
                    $completedCount = count(array_filter($tests, function($t) { return $t['status'] == 'Completed'; }));
                 ?>
                 <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-warning">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted fw-bold mb-1">Pending/In-Progress Tests</h6>
                                <h3 class="mb-0 fw-bold"><?php echo $pendingCount; ?></h3>
                            </div>
                            <div class="bg-warning text-white rounded-circle p-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px;">
                                <i class="fas fa-hourglass-half fs-4"></i>
                            </div>
                        </div>
                    </div>
                 </div>
                 <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-success">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted fw-bold mb-1">Completed Diagnostics</h6>
                                <h3 class="mb-0 fw-bold"><?php echo $completedCount; ?></h3>
                            </div>
                            <div class="bg-success text-white rounded-circle p-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px;">
                                <i class="fas fa-check-double fs-4"></i>
                            </div>
                        </div>
                    </div>
                 </div>
            </div>

            <!-- QUEUE TABLE -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0">Test Queue</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-0 mb-0">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="p-4 border-0">Patient Info</th>
                                    <th class="p-4 border-0">Requested By</th>
                                    <th class="p-4 border-0">Test Details</th>
                                    <th class="p-4 border-0 text-center">Date</th>
                                    <th class="p-4 border-0 text-center">Status</th>
                                    <th class="p-4 border-0 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($tests)): ?>
                                    <tr><td colspan="6" class="text-center py-5 text-muted"><i class="fas fa-vial fs-1 mb-3 d-block text-secondary"></i> All clear. No lab tests currently queued.</td></tr>
                                <?php else: ?>
                                    <?php foreach($tests as $test): 
                                        $statusClass = 'bg-warning text-dark';
                                        if($test['status'] == 'In Progress') $statusClass = 'bg-primary-subtle border-primary text-primary';
                                        if($test['status'] == 'Completed') $statusClass = 'bg-success-subtle border-success text-success';
                                    ?>
                                        <tr>
                                            <td class="p-4 border-0">
                                                <div class="fw-bold text-dark"><?php echo $test['patient_name']; ?></div>
                                                <div class="small text-muted">PID: #<?php echo $test['patient_id']; ?></div>
                                            </td>
                                            <td class="p-4 border-0 fw-bold text-secondary">
                                                Dr. <?php echo $test['doctor_name']; ?>
                                            </td>
                                            <td class="p-4 border-0">
                                                <div class="fw-bold text-primary"><?php echo $test['test_name']; ?></div>
                                                <span class="badge bg-light text-dark shadow-sm border small"><?php echo $test['test_category']; ?></span>
                                            </td>
                                            <td class="p-4 border-0 text-center">
                                                <div class="small fw-bold"><?php echo date('M d, Y', strtotime($test['requested_at'])); ?></div>
                                                <div class="small text-muted"><?php echo date('h:i A', strtotime($test['requested_at'])); ?></div>
                                            </td>
                                            <td class="p-4 border-0 text-center">
                                                <span class="badge <?php echo $statusClass; ?> border rounded-pill px-3 py-2 small shadow-sm"><?php echo $test['status']; ?></span>
                                            </td>
                                            <td class="p-4 border-0 text-end">
                                                <?php if($test['status'] != 'Completed'): ?>
                                                    <button class="btn btn-primary-gradient btn-sm rounded-pill px-3 fw-bold shadow-sm" 
                                                            data-bs-toggle="modal" data-bs-target="#uploadResultModal"
                                                            onclick="document.getElementById('upload_test_id').value='<?php echo $test['id']; ?>'; document.getElementById('display_test_name').innerText='<?php echo addslashes($test['test_name']); ?>';">
                                                        Update Result <i class="fas fa-upload ms-1"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-light btn-sm rounded-pill px-3 shadow-sm border" data-bs-toggle="modal" data-bs-target="#viewResultModal<?php echo $test['id']; ?>">
                                                        View Result <i class="fas fa-eye ms-1"></i>
                                                    </button>
                                                    
                                                    <!-- Modal to View Completed Result -->
                                                    <div class="modal fade text-start" id="viewResultModal<?php echo $test['id']; ?>" tabindex="-1">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                                                <div class="modal-header bg-success text-white p-4">
                                                                    <h5 class="modal-title fw-bold">Lab Result: <?php echo $test['test_name']; ?></h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body p-4 bg-white">
                                                                    <div class="mb-3">
                                                                        <label class="small text-muted fw-bold">Patient Name</label>
                                                                        <div class="fw-bold fs-5"><?php echo $test['patient_name']; ?></div>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="small text-muted fw-bold">Result Notes</label>
                                                                        <div class="p-3 bg-light rounded-3 small">
                                                                            <?php echo nl2br(htmlspecialchars($test['result'])); ?>
                                                                        </div>
                                                                    </div>
                                                                    <?php if($test['report_file']): ?>
                                                                        <div class="mb-3 d-grid">
                                                                            <a href="../assets/uploads/labs/<?php echo $test['report_file']; ?>" target="_blank" class="btn btn-outline-primary border-2 fw-bold">
                                                                                <i class="fas fa-file-pdf me-2"></i> Download Attached Report
                                                                            </a>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
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

<!-- Upload/Update Result Modal -->
<div class="modal fade" id="uploadResultModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary-gradient text-white p-4">
                <h5 class="modal-title fw-bold">Log Test Findings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                 <div class="alert bg-soft-blue border-0 rounded-3 mb-4 text-center">
                     <span class="small fw-bold">Submitting Results for:</span>
                     <h6 id="display_test_name" class="fw-bold text-primary mb-0 mt-1">...</h6>
                 </div>

                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="update_test" value="1">
                    <input type="hidden" name="test_id" id="upload_test_id" value="">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Lab Technician Notes / Findings</label>
                        <textarea name="result" class="form-control bg-light" rows="4" placeholder="Enter numerical values, observations, and conclusions..." required></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Attach Official Scan/Report (Optional)</label>
                        <input type="file" name="report_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.docx">
                        <div class="form-text small">Accepted formats: PDF, JPG, PNG. Max 5MB.</div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary-gradient px-5 py-3 w-100 rounded-pill fw-bold shadow-sm">
                            Mark as Completed & Send to Doctor <i class="fas fa-paper-plane ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Log New Request Modal (Admin override) -->
<div class="modal fade" id="newRequestModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4">
                <h5 class="modal-title fw-bold">Manual Lab Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <form action="" method="POST">
                    <input type="hidden" name="request_test" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Patient</label>
                        <select name="patient_id" class="form-select" required>
                            <option value="">Select Patient...</option>
                            <?php foreach($all_patients as $p): ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo $p['full_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Referring Doctor</label>
                        <select name="doctor_id" class="form-select" required>
                            <option value="">Select Doctor...</option>
                            <?php foreach($all_doctors as $d): ?>
                                <option value="<?php echo $d['id']; ?>">Dr. <?php echo $d['full_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Category</label>
                            <select name="test_category" class="form-select" required>
                                <option value="Hematology">Hematology</option>
                                <option value="Biochemistry">Biochemistry</option>
                                <option value="Microbiology">Microbiology</option>
                                <option value="Pathology">Pathology</option>
                                <option value="Radiology (Imaging)">Radiology</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Exact Test Name</label>
                            <input type="text" name="test_name" class="form-control" placeholder="e.g. Full Blood Count" required>
                        </div>
                    </div>
                    
                    <div class="text-center mt-2">
                        <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 fw-bold shadow-sm">
                            Queue Test Request
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
