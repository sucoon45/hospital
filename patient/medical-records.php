<?php
require_once '../config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/database.php';
require_once '../includes/functions/utils.php';

checkRole(['patient']);

$user_id = $_SESSION['user_id'];
$patient = fetchOne("SELECT id FROM patients WHERE user_id = ?", [$user_id]);
$patient_id = $patient['id'];

// Fetch EMR History
$history = getPatientFullHistory($patient_id);

// Fetch Lab Test History
$lab_tests = fetchAll("SELECT lt.*, d_u.full_name as doctor_name 
                      FROM lab_tests lt 
                      JOIN doctors d ON lt.doctor_id = d.id 
                      JOIN users d_u ON d.user_id = d_u.id 
                      WHERE lt.patient_id = ? 
                      ORDER BY lt.requested_at DESC", [$patient_id]);

$pageTitle = "My Medical Records";
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
                   <h2 class="mb-0 fw-bold">My Medical Records</h2>
                   <p class="text-muted small mb-0">Your complete health history, consultation remarks, and diagnostic results.</p>
                </div>
            </div>

            <div class="row g-4">
                <!-- DIAGNOSTICS & LABS -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                        <div class="card-header bg-transparent border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-microscope me-2 text-primary"></i> Lab Results & Diagnostics</h5>
                        </div>
                        <div class="card-body p-4 pt-3 overflow-auto" style="max-height: 500px;">
                            <?php if(empty($lab_tests)): ?>
                                <div class="text-center py-4">
                                    <p class="text-muted small">No diagnostic records found on your profile.</p>
                                </div>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach($lab_tests as $lab): 
                                        $badge = $lab['status'] == 'Completed' ? 'bg-success' : ($lab['status'] == 'In Progress' ? 'bg-primary' : 'bg-warning text-dark');
                                    ?>
                                        <div class="list-group-item border-0 border-bottom p-3 mb-2 bg-light rounded-4 shadow-sm border border-light">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="fw-bold text-dark mb-0"><?php echo $lab['test_name']; ?></h6>
                                                <span class="badge <?php echo $badge; ?> rounded-pill small"><?php echo $lab['status']; ?></span>
                                            </div>
                                            <p class="small text-muted mb-2"><i class="fas fa-user-md me-1"></i> Ordered by Dr. <?php echo $lab['doctor_name']; ?> on <?php echo date('M d, Y', strtotime($lab['requested_at'])); ?></p>
                                            
                                            <?php if($lab['status'] == 'Completed'): ?>
                                                <div class="bg-white p-3 rounded-3 small text-dark border border-light mb-2">
                                                    <strong>Findings:</strong><br> <?php echo nl2br(htmlspecialchars($lab['result'])); ?>
                                                </div>
                                                <?php if($lab['report_file']): ?>
                                                    <a href="../assets/uploads/labs/<?php echo $lab['report_file']; ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill"><i class="fas fa-download me-1"></i> Download Report</a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                 <div class="bg-white p-2 rounded-3 small text-muted text-center border border-light border-dashed">
                                                    Result pending. You will be notified when it's ready.
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- CLINICAL TIMELINE -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                        <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-clipboard-list me-2 text-primary"></i> Clinical Visit History</h5>
                        </div>
                        <div class="card-body p-4 pt-0 overflow-auto" style="max-height: 500px;">
                            <?php if(empty($history)): ?>
                                <div class="text-center py-5">
                                    <p class="text-muted small">You haven't had any consultations yet.</p>
                                </div>
                            <?php else: ?>
                                <div class="timeline-v">
                                    <?php foreach($history as $rec): 
                                        $v = json_decode($rec['vitals'], true);
                                    ?>
                                        <div class="timeline-item border-start border-2 border-primary ps-4 pb-4 position-relative">
                                            <div class="position-absolute start-0 translate-middle-x bg-primary rounded-circle shadow-sm" style="width: 14px; height: 14px; margin-left: -1px; margin-top: 5px;"></div>
                                            <div class="card border-0 bg-light rounded-4 p-4 border-start border-4 border-primary shadow-sm hover-shadow transition-all">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="small fw-bold text-primary"><?php echo date('M d, Y', strtotime($rec['visit_date'])); ?></span>
                                                    <span class="small text-muted fw-bold"><i class="fas fa-user-md me-1"></i> Dr. <?php echo $rec['doctor_name']; ?></span>
                                                </div>
                                                <h6 class="fw-bold mb-2 text-dark">Diagnosis: <?php echo $rec['diagnosis']; ?></h6>
                                                
                                                <?php if($v): ?>
                                                    <div class="bg-white p-2 rounded-3 small mb-3 d-flex gap-3 border shadow-sm border-light">
                                                        <span><i class="fas fa-heartbeat text-danger me-1"></i> <strong>BP:</strong> <?php echo $v['BP'] ?? 'N/A'; ?></span>
                                                        <span><i class="fas fa-thermometer-half text-warning me-1"></i> <strong>Temp:</strong> <?php echo $v['Temp'] ?? 'N/A'; ?>°C</span>
                                                        <span><i class="fas fa-weight text-success me-1"></i> <strong>Wt:</strong> <?php echo $v['Weight'] ?? 'N/A'; ?>kg</span>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <p class="small text-muted mb-1"><strong>Symptoms:</strong> <?php echo $rec['symptoms']; ?></p>
                                                <p class="small mb-0 text-secondary border-top pt-2 mt-2"><strong>Doctor's Remarks:</strong> <?php echo $rec['notes']; ?></p>
                                            </div>
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
</body>
</html>
