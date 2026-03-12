<?php
require_once '../config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/database.php';
require_once '../includes/functions/utils.php';

checkRole(['admin']);

$error = '';
$success = '';

// Process new doctor addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_doctor'])) {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $dept_id = sanitize($_POST['dept_id']);
    $specialization = sanitize($_POST['specialization']);
    $fee = sanitize($_POST['consultation_fee']);
    
    $pdo = getDBConnection();
    try {
        $pdo->beginTransaction();
        
        // 1. Create User
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt_user = $pdo->prepare("INSERT INTO users (full_name, email, password, role, phone) VALUES (?, ?, ?, 'doctor', ?)");
        $stmt_user->execute([$full_name, $email, $hashed_password, $phone]);
        $user_id = $pdo->lastInsertId();
        
        // 2. Create Doctor Profile
        $stmt_doc = $pdo->prepare("INSERT INTO doctors (user_id, dept_id, specialization, consultation_fee) VALUES (?, ?, ?, ?)");
        $stmt_doc->execute([$user_id, $dept_id, $specialization, $fee]);
        
        $pdo->commit();
        $success = "Dr. $full_name has been successfully added to the system.";
    } catch (\PDOException $e) {
        $pdo->rollBack();
        if ($e->getCode() == 23000) {
            $error = "A user with this email already exists.";
        } else {
            $error = "Failed to add doctor. Error: " . $e->getMessage();
        }
    }
}

// Fetch lists
$doctorsList = getDoctors();
$departments = getDepartments();
$pageTitle = "Manage Doctors";
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
            <!-- Header Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4 p-4 glassmorphism rounded-4 bg-white shadow-sm border-0">
                <h2 class="mb-0 fw-bold">Doctor Directory & Management</h2>
                <button class="btn btn-primary-gradient rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
                    Add New Specialist <i class="fas fa-user-plus ms-2"></i>
                </button>
            </div>

            <?php echo showAlert($error, 'danger'); ?>
            <?php echo showAlert($success, 'success'); ?>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0">Registered Specialists</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-0 mb-0">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="p-4 border-0">Doctor Name & Contact</th>
                                    <th class="p-4 border-0">Department</th>
                                    <th class="p-4 border-0">Specialization</th>
                                    <th class="p-4 border-0 text-center">Status</th>
                                    <th class="p-4 border-0 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($doctorsList)): ?>
                                    <tr><td colspan="5" class="text-center py-5 text-muted">No doctors found. Please add a new specialist.</td></tr>
                                <?php else: ?>
                                    <?php foreach($doctorsList as $doc): ?>
                                        <tr>
                                            <td class="p-4 border-0">
                                                <div class="d-flex align-items-center">
                                                    <img src="../assets/images/default_user.jpg" width="45" height="45" class="rounded-circle border border-2 border-primary me-3 pt-1 px-1">
                                                    <div>
                                                        <span class="fw-bold text-dark d-block">Dr. <?php echo $doc['doctor_name']; ?></span>
                                                        <span class="small text-muted"><?php echo $doc['email']; ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-4 border-0 fw-medium text-secondary"><?php echo $doc['dept_name']; ?></td>
                                            <td class="p-4 border-0 small"><?php echo $doc['specialization']; ?></td>
                                            <td class="p-4 border-0 text-center">
                                                <span class="badge bg-success-subtle text-success py-2 px-3 rounded-pill border border-success">Active</span>
                                            </td>
                                            <td class="p-4 border-0 text-end">
                                                <button class="btn btn-light btn-sm text-primary rounded-circle shadow-sm"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-light btn-sm text-danger rounded-circle shadow-sm ms-2"><i class="fas fa-trash-alt"></i></button>
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

<!-- Add Doctor Modal -->
<div class="modal fade" id="addDoctorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary-gradient text-white p-4">
                <h5 class="modal-title fw-bold">Register New Doctor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <form action="" method="POST">
                    <input type="hidden" name="add_doctor" value="1">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Full Name (without Dr. title)</label>
                            <input type="text" name="full_name" class="form-control mb-3" placeholder="e.g. Jane Doe" required>
                            
                            <label class="form-label small fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control mb-3" placeholder="doctor@kamirex.com" required>
                            
                            <label class="form-label small fw-bold">Phone Number</label>
                            <input type="tel" name="phone" class="form-control mb-3" placeholder="+234..." required>
                            
                            <label class="form-label small fw-bold">Account Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Create secure password" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Department</label>
                            <select name="dept_id" class="form-select mb-3" required>
                                <option value="">Select Department</option>
                                <?php foreach($departments as $dept): ?>
                                    <option value="<?php echo $dept['id']; ?>"><?php echo $dept['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                            <label class="form-label small fw-bold">Specialization</label>
                            <input type="text" name="specialization" class="form-control mb-3" placeholder="e.g. Pediatric Surgeon" required>
                            
                            <label class="form-label small fw-bold">Consultation Fee (₦)</label>
                            <input type="number" name="consultation_fee" class="form-control mb-3" step="500" value="5000" min="0" required>
                        </div>
                    </div>
                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-primary-gradient px-5 py-3 rounded-pill fw-bold shadow-sm w-100">
                            Create Doctor Profile <i class="fas fa-check-circle ms-2"></i>
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
