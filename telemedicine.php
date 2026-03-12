<?php
require_once 'config.php';
$pageTitle = "Telemedicine Center";
include_once 'includes/components/header.php';
?>

<!-- TELEMEDICINE HERO -->
<section class="hero-section" style="min-height: 40vh; background: linear-gradient(rgba(0, 51, 102, 0.9), rgba(32, 201, 151, 0.7)), url('assets/images/hero_bg.jpg') center/cover no-repeat;">
    <div class="container text-center pt-5">
        <h1 class="hero-title text-white">Digital Hospital <span class="border-bottom border-3">Consultations</span></h1>
        <p class="hero-subtitle text-light mb-4 mx-auto" style="max-width: 600px;">Experience top-tier medical care from the comfort of your home. WebRTC encrypted video sessions with our specialists.</p>
        <a href="patient/register.php" class="btn btn-primary bg-white text-primary rounded-pill px-5 py-3 fw-bold fs-5 shadow-lg">Start Virtual Visit <i class="fas fa-video ms-2"></i></a>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="section-padding bg-soft-blue">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="fw-bold text-dark">How Telemedicine Works</h2>
                <p class="text-muted lead">Skip the waiting room. Three simple steps to connect directly with a doctor.</p>
            </div>
        </div>
        
        <div class="row g-4 text-center">
            <!-- Step 1 -->
            <div class="col-lg-4">
                <div class="card border-0 h-100 shadow-sm rounded-4 bg-white feature-card p-5">
                    <div class="bg-primary-subtle text-primary rounded-circle d-inline-block p-4 mx-auto mb-4" style="width: 100px; height: 100px;">
                        <i class="fas fa-calendar-alt fs-1 mt-1"></i>
                    </div>
                    <h5 class="fw-bold">1. Book a Session</h5>
                    <p class="text-muted small">Select the "Telemedicine" option from your patient portal when scheduling your appointment.</p>
                </div>
            </div>
            
            <!-- Step 2 -->
            <div class="col-lg-4">
                <div class="card border-0 h-100 shadow-sm rounded-4 bg-white feature-card p-5">
                    <div class="bg-primary-subtle text-primary rounded-circle d-inline-block p-4 mx-auto mb-4" style="width: 100px; height: 100px;">
                        <i class="fas fa-credit-card fs-1 mt-1"></i>
                    </div>
                    <h5 class="fw-bold">2. Pre-Consultation Payment</h5>
                    <p class="text-muted small">Securely settle your consultation fee via Paystack to unlock your dedicated video room link.</p>
                </div>
            </div>
            
            <!-- Step 3 -->
            <div class="col-lg-4">
                <div class="card border-0 h-100 shadow-sm rounded-4 bg-white feature-card p-5">
                    <div class="bg-primary-subtle text-primary rounded-circle d-inline-block p-4 mx-auto mb-4" style="width: 100px; height: 100px;">
                        <i class="fas fa-user-md fs-1 mt-1"></i>
                    </div>
                    <h5 class="fw-bold">3. Meet Your Doctor</h5>
                    <p class="text-muted small">Join your secure, encrypted WebRTC video chat at the scheduled time. Prescriptions are added to your EMR.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES HIGHLIGHT -->
<section class="section-padding bg-primary-gradient text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="fw-bold mb-4">Why Choose Virtual Care?</h2>
                <ul class="list-unstyled">
                    <li class="mb-3"><i class="fas fa-check-circle text-accent me-3 fs-5"></i> Immediate access to your Electronic Medical Records (EMR).</li>
                    <li class="mb-3"><i class="fas fa-check-circle text-accent me-3 fs-5"></i> End-to-end encrypted video and P2P text chat.</li>
                    <li class="mb-3"><i class="fas fa-check-circle text-accent me-3 fs-5"></i> Zero travel time, ideal for follow-ups and minor symptoms.</li>
                    <li class="mb-3"><i class="fas fa-check-circle text-accent me-3 fs-5"></i> Digital prescriptions sent directly to your linked pharmacy.</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <!-- Placeholder for Video Call Interface Image -->
                <div class="bg-white rounded-4 shadow-lg p-3 position-relative text-center" style="height: 350px; overflow: hidden;">
                    <div class="bg-dark rounded-3 h-100 w-100 d-flex align-items-center justify-content-center text-white flex-column position-relative">
                        <i class="fas fa-video fs-1 mb-3 text-muted"></i>
                        <h5 class="text-secondary fw-bold">Live Session Interface</h5>
                        <p class="small text-muted mb-0">Powered by WebRTC technology</p>
                        
                        <!-- Floating mock UI -->
                        <div class="position-absolute bottom-0 start-50 translate-middle-x mb-4 bg-black bg-opacity-50 p-2 rounded-pill">
                            <button class="btn btn-sm btn-danger rounded-circle mx-1"><i class="fas fa-phone-slash"></i></button>
                            <button class="btn btn-sm btn-light rounded-circle mx-1"><i class="fas fa-microphone"></i></button>
                            <button class="btn btn-sm btn-light rounded-circle mx-1"><i class="fas fa-video"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once 'includes/components/footer.php'; ?>
