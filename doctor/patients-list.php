<?php
require_once '../config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/database.php';
require_once '../includes/functions/utils.php';

checkRole(['doctor']);

$user_id = $_SESSION['user_id'];
$doctor = fetchOne("SELECT id FROM doctors WHERE user_id = ?", [$user_id]);
$doctor_id = $doctor['id'];

// Fetch patients this doctor has consulted or has appointments with
$sql = "
    SELECT DISTINCT p.*, u.full_name, u.phone, u.email 
    FROM patients p 
    JOIN users u ON p.user_id = u.id 
    JOIN medical_records mr ON mr.patient_id = p.id 
    WHERE mr.doctor_id = ? 
    UNION 
    SELECT DISTINCT p.*, u.full_name, u.phone, u.email 
    FROM patients p 
    JOIN users u ON p.user_id = u.id 
    JOIN appointments a ON a.patient_id = p.id 
    WHERE a.doctor_id = ?
";
$myPatients = fetchAll($sql, [$doctor_id, $doctor_id]);

$pageTitle = "My Patients";
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
                <h2 class="mb-0 fw-bold">My Patient Roster</h2>
                <div class="input-group w-25 shadow-sm rounded-pill overflow-hidden">
                    <input type="search" class="form-control border-0 bg-light px-4" placeholder="Find patient in roster...">
                    <button class="btn btn-primary bg-primary-gradient border-0 px-3"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-0 mb-0">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="p-4 border-0">Patient Details</th>
                                    <th class="p-4 border-0 text-center">Age / Gender</th>
                                    <th class="p-4 border-0 text-center">Blood Profile</th>
                                    <th class="p-4 border-0 text-end">Electronic Medical Record</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($myPatients)): ?>
                                    <tr><td colspan="4" class="text-center py-5 text-muted"><i class="fas fa-user-injured fs-1 mb-3 d-block text-light"></i> No patients linked to your profile yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach($myPatients as $pat): ?>
                                        <tr>
                                            <td class="p-4 border-0">
                                                <div class="d-flex align-items-center">
                                                    <img src="../assets/images/default_user.jpg" width="45" height="45" class="rounded-circle border border-2 border-primary me-3 pt-1 px-1">
                                                    <div>
                                                        <span class="fw-bold text-dark d-block"><?php echo $pat['full_name']; ?></span>
                                                        <span class="small text-muted"><i class="fas fa-phone-alt me-1 fs-7"></i> <?php echo $pat['phone']; ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-4 border-0 text-center fw-medium text-secondary">
                                                <?php echo calculateAge($pat['dob']); ?> Yrs<br>
                                                <small class="text-dark fw-bold"><?php echo $pat['gender']; ?></small>
                                            </td>
                                            <td class="p-4 border-0 text-center">
                                                <span class="badge bg-danger rounded-pill shadow-sm px-3"><?php echo $pat['blood_group'] ?: 'N/A'; ?></span><br>
                                            </td>
                                            <td class="p-4 border-0 text-end">
                                                <a href="patient-view.php?id=<?php echo $pat['id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">Review File <i class="fas fa-arrow-right ms-2"></i></a>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
