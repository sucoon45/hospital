<?php
require_once 'config.php';
require_once 'includes/functions/auth.php';
require_once 'includes/functions/utils.php';

redirectIfLoggedIn();

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    $login = loginUser($email, $password);
    if ($login === true) {
        $dashboard = $_SESSION['role'] . "/dashboard.php";
        if ($_SESSION['role'] == 'lab_tech') $dashboard = "admin/lab.php";
        if ($_SESSION['role'] == 'pharmacist') $dashboard = "admin/pharmacy.php";
        if ($_SESSION['role'] == 'receptionist') $dashboard = "admin/appointments.php";
        
        redirect($dashboard);
    } else {
        $error = $login;
    }
}

$pageTitle = "Login";
include_once 'includes/components/header.php';
?>

<div class="section-padding bg-soft-blue min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="bg-primary-gradient p-4 text-center text-white">
                        <i class="fas fa-heartbeat fs-1 mb-3"></i>
                        <h3>Welcome Back</h3>
                        <p class="mb-0 opacity-75">Access your medical portal securely</p>
                    </div>
                    <div class="card-body p-5">
                        <?php echo showAlert($error, 'danger'); ?>
                        
                        <form action="login.php" method="POST">
                            <div class="mb-4">
                                <label class="form-label text-secondary small fw-bold">Email Address</label>
                                <div class="input-group border-bottom border-2">
                                    <span class="input-group-text bg-transparent border-0 text-primary"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control bg-transparent border-0 shadow-none ps-0" placeholder="your@email.com" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label text-secondary small fw-bold">Password</label>
                                <div class="input-group border-bottom border-2">
                                    <span class="input-group-text bg-transparent border-0 text-primary"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control bg-transparent border-0 shadow-none ps-0" placeholder="••••••••" required>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember">
                                    <label class="form-check-label small text-muted" for="remember">Remember me</label>
                                </div>
                                <a href="forgot-password.php" class="small text-primary text-decoration-none fw-bold">Forgot Password?</a>
                            </div>
                            
                            <button type="submit" class="btn btn-primary-gradient w-100 py-3 rounded-pill fw-bold shadow-sm">
                                Login Now <i class="fas fa-sign-in-alt ms-2"></i>
                            </button>
                        </form>
                        
                        <div class="text-center mt-5">
                            <p class="text-muted small">Don't have an account? <a href="patient/register.php" class="text-primary fw-bold text-decoration-none">Register as Patient</a></p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="index.php" class="text-secondary text-decoration-none small"><i class="fas fa-arrow-left me-2"></i> Back to Homepage</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/components/footer.php'; ?>
