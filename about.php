<?php
require_once 'config.php';
$pageTitle = "About Us";
include_once 'includes/components/header.php';
?>

<!-- ABOUT HERO -->
<section class="hero-section" style="min-height: 40vh; background: linear-gradient(rgba(0, 51, 102, 0.9), rgba(0, 51, 102, 0.8)), url('assets/images/hero_bg.jpg') center/cover no-repeat;">
    <div class="container text-center pt-5">
        <h1 class="hero-title text-white">Our Heritage of <span class="text-primary border-bottom border-3">Healing</span></h1>
        <p class="hero-subtitle text-light mb-0 mx-auto" style="max-width: 700px;">From humble beginnings to Lagos' premier automated multi-specialist hospital. Learn about our commitment to excellence, integrity, and global healthcare standards.</p>
    </div>
</section>

<!-- STORY HIGHLIGHTS -->
<section class="section-padding bg-soft-blue">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="assets/images/hero_bg.jpg" alt="Kamirex Advanced Facility" class="img-fluid rounded-4 shadow-lg border border-3 border-white">
            </div>
            <div class="col-lg-6 px-lg-5">
                <h2 class="mb-4 text-dark fw-bold">Redefining Nigerian Healthcare</h2>
                <p class="text-muted lead mb-4">Founded with a vision to bridge the gap in quality medical care, Kamirex Specialist Hospital merges top-tier medical expertise with cutting-edge technology.</p>
                
                <ul class="list-unstyled mb-4">
                    <li class="mb-3 d-flex align-items-start">
                        <i class="fas fa-check-circle text-primary fs-4 me-3 mt-1"></i>
                        <div>
                            <h5 class="fw-bold mb-1">State-of-the-Art EMR System</h5>
                            <p class="small text-muted mb-0">Completely paperless medical records ensuring speed, accuracy, and absolute patient confidentiality.</p>
                        </div>
                    </li>
                    <li class="mb-3 d-flex align-items-start">
                        <i class="fas fa-check-circle text-primary fs-4 me-3 mt-1"></i>
                        <div>
                            <h5 class="fw-bold mb-1">24/7 Telemedicine & AI Support</h5>
                            <p class="small text-muted mb-0">Break geographical barriers with encrypted virtual consultations and smart symptom checkers.</p>
                        </div>
                    </li>
                    <li class="mb-3 d-flex align-items-start">
                        <i class="fas fa-check-circle text-primary fs-4 me-3 mt-1"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Transparent Billing via Paystack</h5>
                            <p class="small text-muted mb-0">Seamlessly clear your medical bills online using secure Nigerian payment gateways.</p>
                        </div>
                    </li>
                </ul>
                <a href="departments.php" class="btn btn-primary-gradient rounded-pill px-4 btn-lg">View Our Specialties</a>
            </div>
        </div>
    </div>
</section>

<!-- ACHIEVEMENTS & STATS -->
<section class="section-padding bg-whtie border-top">
    <div class="container text-center">
        <h2 class="mb-5">Our Impact by the Numbers</h2>
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <h1 class="text-primary fw-bold" style="font-size: 4rem;">15+</h1>
                <p class="text-muted fw-bold text-uppercase small">Specialized Depts.</p>
            </div>
            <div class="col-md-3 col-6">
                <h1 class="text-primary fw-bold" style="font-size: 4rem;">5k+</h1>
                <p class="text-muted fw-bold text-uppercase small">Treated Yearly</p>
            </div>
            <div class="col-md-3 col-6">
                <h1 class="text-primary fw-bold" style="font-size: 4rem;">50+</h1>
                <p class="text-muted fw-bold text-uppercase small">Elite Specialists</p>
            </div>
            <div class="col-md-3 col-6">
                <h1 class="text-primary fw-bold" style="font-size: 4rem;">24/7</h1>
                <p class="text-muted fw-bold text-uppercase small">Round the clock care</p>
            </div>
        </div>
    </div>
</section>

<?php include_once 'includes/components/footer.php'; ?>
