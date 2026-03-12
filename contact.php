<?php
require_once 'config.php';
$pageTitle = "Contact Administration";
include_once 'includes/components/header.php';

$success = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_msg'])){
    // Usually sent via email/SMTP. Currently just showing success message
    $success = "Your message has been sent to the admin desk. We will reach out shortly.";
}
?>

<!-- CONTACT HERO -->
<section class="hero-section" style="min-height: 40vh; background: linear-gradient(rgba(0, 51, 102, 0.9), rgba(0, 123, 255, 0.7)), url('assets/images/hero_bg.jpg') center/cover no-repeat;">
    <div class="container text-center pt-5">
        <h1 class="hero-title text-white">We're Here for <span class="border-bottom border-3">You</span></h1>
        <p class="hero-subtitle text-light mb-0 mx-auto" style="max-width: 600px;">Location details, open channels, and 24/7 emergency hotlines. Reach out to NovaCare Hospital's support team.</p>
    </div>
</section>

<!-- CONTACT GRID -->
<section class="section-padding bg-soft-blue border-top">
    <div class="container">
        
        <div class="row g-4 mb-5">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 text-center bg-white feature-card">
                    <i class="fas fa-map-marker-alt fs-1 text-primary mb-3"></i>
                    <h5 class="fw-bold">Our Facility</h5>
                    <p class="text-muted small">Victoria Island, Lagos, Nigeria. Situated in the heart of the business district for easy access.</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 text-center bg-white feature-card">
                    <i class="fas fa-headset fs-1 text-primary mb-3"></i>
                    <h5 class="fw-bold">24/7 Hotlines</h5>
                    <p class="text-muted small mb-0 fw-bold">Emergency: +234 812 XXX XXXX</p>
                    <p class="text-muted small mb-0">General: +234 800 000 0000</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 text-center bg-white feature-card">
                    <i class="fas fa-envelope-open-text fs-1 text-primary mb-3"></i>
                    <h5 class="fw-bold">Email Desk</h5>
                    <p class="text-muted small fw-bold">info@kamirexhospital.com</p>
                    <p class="small text-muted">For partnerships and corporate NHIS plans.</p>
                </div>
            </div>
        </div>

        <div class="row bg-white rounded-5 shadow-lg overflow-hidden border-0">
            <!-- Google Maps iframe (Placeholder for real embed) -->
            <div class="col-lg-5 p-0 bg-light position-relative">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.717146592231!2d3.4187!3d6.428!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103bf4cc9a90?placeholder!5e0!3m2!1sen!2sng!4v1600000000000!5m2!1sen!2sng" width="100%" height="100%" style="border:0; min-height: 400px;" allowfullscreen="" loading="lazy"></iframe>
            </div>
            
            <div class="col-lg-7 p-5">
                <h3 class="fw-bold mb-4">Drop us a Message</h3>
                
                <?php if($success): ?>
                    <div class="alert alert-success border-0 rounded-3 shadow-sm mb-4 fw-bold"><i class="fas fa-check-circle me-2"></i> <?php echo $success; ?></div>
                <?php endif; ?>
                
                <form action="" method="POST" class="row g-3">
                    <input type="hidden" name="send_msg" value="1">
                    <div class="col-md-6">
                        <label class="small fw-bold">Your Name</label>
                        <input type="text" class="form-control mb-3" placeholder="John Doe" required>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">Email Address</label>
                        <input type="email" class="form-control mb-3" placeholder="john@email.com" required>
                    </div>
                    <div class="col-md-12">
                        <label class="small fw-bold">Subject</label>
                        <input type="text" class="form-control mb-3" placeholder="E.g. NHIS Registration Query" required>
                    </div>
                    <div class="col-md-12">
                        <label class="small fw-bold">How can we help?</label>
                        <textarea class="form-control mb-4" rows="4" placeholder="Type your message..." required></textarea>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary-gradient px-5 py-3 rounded-pill fw-bold w-100 shadow-sm">Send Secure Message <i class="fas fa-paper-plane ms-2"></i></button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</section>

<?php include_once 'includes/components/footer.php'; ?>
