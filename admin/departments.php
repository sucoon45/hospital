<?php
require_once '../config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/database.php';
require_once '../includes/functions/utils.php';

checkRole(['admin']);

$error = '';
$success = '';

// Handling New Department Addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_dept'])) {
    $name = sanitize($_POST['name']);
    $desc = sanitize($_POST['description']);
    
    if (runQuery("INSERT INTO departments (name, description) VALUES (?, ?)", [$name, $desc])) {
        $success = "Department added successfully!";
    } else {
        $error = "Failed to add department.";
    }
}

$departments = getDepartments();
$pageTitle = "Hospital Departments";
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
                <h2 class="mb-0 fw-bold">Departmental Structure</h2>
                <button class="btn btn-primary-gradient rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#deptModal">
                    Add New Dept <i class="fas fa-plus ms-2"></i>
                </button>
            </div>

            <?php echo showAlert($error, 'danger'); ?>
            <?php echo showAlert($success, 'success'); ?>

            <div class="row g-4">
                <?php if(empty($departments)): ?>
                    <div class="col-12 text-center py-5">
                       <i class="fas fa-hospital-alt fs-1 text-light mb-3"></i>
                       <p class="text-muted">No departments configured yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($departments as $dept): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div class="bg-soft-blue p-3 rounded-4 text-primary"><i class="fas fa-hospital fs-3"></i></div>
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></button>
                                            <ul class="dropdown-menu border-0 shadow-lg rounded-3">
                                                <li><a class="dropdown-item small" href="#"><i class="fas fa-edit me-2"></i> Edit</a></li>
                                                <li><a class="dropdown-item small text-danger" href="#"><i class="fas fa-trash-alt me-2"></i> Delete</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <h5 class="fw-bold mb-2"><?php echo $dept['name']; ?></h5>
                                    <p class="small text-muted mb-0"><?php echo $dept['description'] ?: 'No description provided.'; ?></p>
                                </div>
                                <div class="card-footer bg-transparent border-top p-3 text-center small text-secondary">
                                    <i class="fas fa-user-md me-2"></i> Manage Staff & Doctors
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="deptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary-gradient text-white p-4">
                <h5 class="modal-title fw-bold">Add New Department</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="" method="POST">
                    <input type="hidden" name="add_dept" value="1">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Department Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Cardiology" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Short Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief overview of services..."></textarea>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary-gradient px-5 py-2 rounded-pill fw-bold">Save Department</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
