<?php
require_once 'config.php';
require_once 'includes/functions/database.php';
require_once 'includes/functions/utils.php';

// Safe wrapper query function mimicking our utils for this public page
$doctors = getDoctors();
$pageTitle = "Our Medical Specialists";
include_once 'includes/components/header.php';
?>

<!-- HERO SECTION -->
<section class="hero-section" style="min-height: 40vh; background: linear-gradient(rgba(0, 51, 102, 0.9), rgba(0, 123, 255, 0.7)), url('assets/images/hero_bg.jpg') center/cover no-repeat;">
    <div class="container text-center text-white pt-5">
        <h1 class="hero-title text-white">Our Master Medical <span class="border-bottom border-3">Team</span></h1>
        <p class="hero-subtitle text-light mb-0 mx-auto" style="max-width: 600px;">Meet the distinguished professionals committed to delivering flawless healthcare tailored to your needs. They stand at the forefront of medical excellence.</p>
    </div>
</section>

<!-- DOCTORS DIRECTORY -->
<section class="section-padding bg-soft-blue min-vh-100">
    <div class="container">
        
        <!-- Search Filter -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-pill p-2 bg-white">
                    <form class="d-flex align-items-center">
                        <select class="form-select border-0 border-end shadow-none bg-transparent ps-4 text-muted w-auto fw-bold" style="cursor: pointer;">
                            <option value="">All Departments</option>
                            <option value="Cardiology">Cardiology</option>
                            <option value="Pediatrics">Pediatrics</option>
                            <option value="Neurology">Neurology</option>
                            <option value="General">General Medicine</option>
                        </select>
                        <input class="form-control border-0 shadow-none ps-4 fw-medium" type="search" placeholder="Search doctor by name or specialty...">
                        <button class="btn btn-primary-gradient rounded-pill px-4" type="button"><i class="fas fa-search me-2"></i> Find</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- DOCTORS GRID -->
        <div class="row g-4">
            <?php if(empty($doctors)): ?>
                <div class="col-12 text-center py-5">
                    <h4 class="text-muted"><i class="fas fa-stethoscope d-block fs-1 mb-3"></i> Currently refining our elite directory. Please check back later.</h4>
                </div>
            <?php else: ?>
                <?php foreach($doctors as $doc): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="card doctor-card border-0 h-100 bg-white">
                            <div class="position-relative">
                                <img src="assets/images/default_user.jpg" class="doctor-img bg-light p-3" alt="Dr. <?php echo $doc['doctor_name']; ?>">
                                <span class="badge bg-primary position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill shadow-sm"><i class="fas fa-star text-warning me-1"></i> Top Rated</span>
                            </div>
                            <div class="doctor-info p-4">
                                <h5 class="fw-bold mb-1 text-dark">Dr. <?php echo $doc['doctor_name']; ?></h5>
                                <p class="text-primary fw-medium small mb-3"><?php echo $doc['specialization']; ?></p>
                                
                                <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3">
                                    <span class="small text-muted fw-bold"><i class="fas fa-hospital text-secondary me-1"></i> <?php echo $doc['dept_name']; ?></span>
                                    <a href="patient/register.php" class="btn btn-outline-primary btn-sm rounded-pill fw-bold" data-bs-toggle="tooltip" title="Book Dr. <?php echo $doc['doctor_name']; ?>">Book <i class="fas fa-calendar-check ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CALL TO ACTION -->
<section class="section-padding text-center bg-white">
    <div class="container">
        <h2>Can't find the right specialist?</h2>
        <p class="text-muted mb-4 mx-auto lead" style="max-width: 600px;">Our general consultants are available 24/7 to assess your symptoms and direct you to the exact medical unit you need.</p>
        <a href="contact.php" class="btn btn-primary-gradient btn-lg rounded-pill px-5">Contact Reception <i class="fas fa-phone-alt ms-2"></i></a>
    </div>
</section>

<?php include_once 'includes/components/footer.php'; ?>
