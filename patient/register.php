<?php
require_once '../config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/utils.php';

redirectIfLoggedIn();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $phone = sanitize($_POST['phone']);
    $dob = sanitize($_POST['dob']);
    $gender = sanitize($_POST['gender']);
    $address = sanitize($_POST['address']);
    
    $register = registerPatient($full_name, $email, $password, $phone, $dob, $gender, $address);
    if (is_numeric($register)) {
        $success = "Registration successful! You can now login.";
    } else {
        $error = $register;
    }
}

$pageTitle = "Patient Registration";
// Using absolute link for CSS/JS since this is in a subfolder
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

<div class="section-padding py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="bg-primary-gradient p-5 text-center text-white">
                        <i class="fas fa-user-plus fs-1 mb-3"></i>
                        <h2>Create Your Patient Portal Account</h2>
                        <p class="mb-0 opacity-75">Join Kamirex Specialist Hospital's digital ecosystem</p>
                    </div>
                    
                    <div class="card-body p-5 bg-white">
                        <?php echo showAlert($error, 'danger'); ?>
                        <?php echo showAlert($success, 'success'); ?>
                        
                        <?php if(!$success): ?>
                        <form action="register.php" method="POST" class="row g-4">
                            <h5 class="text-primary border-start border-4 border-primary ps-3 mb-3">Personal Information</h5>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Full Name</label>
                                <input type="text" name="full_name" class="form-control" placeholder="John Doe" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+234 812 XXX XXXX" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Date of Birth</label>
                                <input type="date" name="dob" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Gender</label>
                                <select name="gender" class="form-select" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Secure Password" required>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label fw-bold small">Residential Address</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Your street address in Lagos..." required></textarea>
                            </div>
                            
                            <div class="col-12 mt-5">
                                <button type="submit" class="btn btn-primary-gradient btn-lg w-100 rounded-pill shadow-sm py-3 fw-bold">
                                    Complete Registration <i class="fas fa-check-circle ms-2"></i>
                                </button>
                            </div>
                        </form>
                        <?php endif; ?>
                        
                        <div class="text-center mt-5">
                            <p class="text-muted">Already have an account? <a href="../login.php" class="text-primary fw-bold text-decoration-none">Login Here</a></p>
                        </div>

                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="../index.php" class="text-secondary text-decoration-none small"><i class="fas fa-arrow-left me-2"></i> Back to Homepage</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
