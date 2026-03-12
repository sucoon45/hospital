<?php
require_once '../config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/database.php';
require_once '../includes/functions/utils.php';

checkRole(['doctor']);

$user_id = $_SESSION['user_id'];
$doctor = fetchOne("SELECT id FROM doctors WHERE user_id = ?", [$user_id]);
$doctor_id = $doctor['id'];

// Fetch today's telemedicine sessions
$today = date('Y-m-d');
$tele_sessions = fetchAll("SELECT t.*, u.full_name, u.phone, p.id as patient_id 
                           FROM telemedicine_sessions t 
                           JOIN patients p ON t.patient_id = p.id 
                           JOIN users u ON p.user_id = u.id 
                           WHERE t.doctor_id = ? AND DATE(t.scheduled_time) = ?
                           ORDER BY t.scheduled_time ASC", [$doctor_id, $today]);

$pageTitle = "Telemedicine Sessions";
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
    <!-- WebRTC Polyfill (Mocked for Demo) -->
    <script src="https://webrtc.github.io/adapter/adapter-latest.js"></script>
</head>
<body class="bg-soft-blue">

<div class="container-fluid">
    <div class="row">
        <?php include_once '../includes/components/sidebar.php'; ?>

        <main class="col-lg-10 p-4 offset-lg-2">
            <div class="d-flex justify-content-between align-items-center mb-4 p-4 glassmorphism rounded-4 bg-white shadow-sm border-0">
                <h2 class="mb-0 fw-bold">Virtual Clinic <i class="fas fa-video ms-2 text-primary"></i></h2>
                <span class="badge bg-danger-subtle text-danger p-2 px-4 rounded-pill fs-6"><i class="fas fa-circle rounded-circle me-2 animate-pulse"></i> Live Sessions Portal</span>
            </div>

            <div class="row g-4">
                <!-- Session Queue -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h5 class="fw-bold mb-0">Today's Queue</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if(empty($tele_sessions)): ?>
                                <div class="text-center py-5 px-3">
                                    <i class="fas fa-bed fs-1 text-light mb-3"></i>
                                    <p class="text-muted small">No virtual appointments scheduled for today.</p>
                                </div>
                            <?php else: ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach($tele_sessions as $ts): 
                                        $time = date('h:i A', strtotime($ts['scheduled_time']));
                                        $statusClass = $ts['status'] == 'Pending' ? 'bg-warning' : ($ts['status'] == 'Completed' ? 'bg-success' : 'bg-primary');
                                    ?>
                                        <li class="list-group-item border-0 p-3 border-bottom hover-bg-light transition-all cursor-pointer" onclick="loadSession('<?php echo $ts['meeting_link']; ?>', '<?php echo $ts['full_name']; ?>', '<?php echo $ts['patient_id']; ?>')">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="<?php echo $statusClass; ?> rounded-circle me-3" style="width: 12px; height: 12px;"></div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold"><?php echo $ts['full_name']; ?></h6>
                                                        <small class="text-muted"><?php echo $time; ?></small>
                                                    </div>
                                                </div>
                                                <button class="btn btn-sm btn-light text-primary rounded-pill px-3 shadow-sm"><i class="fas fa-phone-alt fs-7"></i></button>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Video Interface -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-dark position-relative" style="min-height: 500px;">
                        
                        <!-- Overlay State (No active call) -->
                        <div id="noCallOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark z-index-10 text-center text-white px-4">
                            <div class="bg-white bg-opacity-10 rounded-circle p-4 mb-3">
                                <i class="fas fa-video-slash fs-1 text-secondary"></i>
                            </div>
                            <h4 class="fw-bold">No Active Session</h4>
                            <p class="text-secondary small">Select a patient from the queue to initiate a secure WebRTC video room.</p>
                        </div>
                        
                        <!-- Active Call Video Placeholders -->
                        <div class="position-relative w-100 h-100 d-none" id="callInterface">
                            <!-- Remote Video (Patient) -->
                            <video id="remoteVideo" class="w-100 h-100 object-fit-cover bg-black" autoplay playsinline></video>
                            
                            <!-- Local Video (Doctor) -->
                            <div class="position-absolute bottom-0 end-0 m-4 rounded-4 overflow-hidden border border-2 border-white shadow-lg z-index-10 bg-secondary" style="width: 200px; height: 150px;">
                                <video id="localVideo" class="w-100 h-100 object-fit-cover" autoplay playsinline muted></video>
                            </div>
                            
                            <!-- Patient Name Overlay -->
                            <div class="position-absolute top-0 start-0 m-4 px-3 py-2 bg-black bg-opacity-50 text-white rounded-pill shadow-sm small fw-bold z-index-10">
                                <i class="fas fa-circle text-danger me-2 animate-pulse small"></i><span id="activePatientName">Connecting...</span>
                            </div>

                            <!-- Controls -->
                            <div class="position-absolute bottom-0 start-50 translate-middle-x mb-4 bg-black bg-opacity-75 px-4 py-3 rounded-pill shadow-lg z-index-10 d-flex gap-3 align-items-center">
                                <button class="btn btn-dark text-white rounded-circle shadow-sm tooltip-btn" title="Mute/Unmute"><i class="fas fa-microphone"></i></button>
                                <button class="btn btn-dark text-white rounded-circle shadow-sm tooltip-btn" title="Turn Camera Off"><i class="fas fa-video"></i></button>
                                <a id="emrShortcutLink" href="#" target="_blank" class="btn btn-primary-gradient text-white rounded-pill px-4 shadow-sm fw-bold mx-2">Open EMR <i class="fas fa-external-link-alt ms-1"></i></a>
                                <button class="btn btn-danger rounded-circle shadow-sm tooltip-btn" title="End Call" onclick="endCall()"><i class="fas fa-phone-slash"></i></button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    // Mocking WebRTC behavior for frontend demonstration
    const noCallOverlay = document.getElementById('noCallOverlay');
    const callInterface = document.getElementById('callInterface');
    const activePatientName = document.getElementById('activePatientName');
    const emrShortcutLink = document.getElementById('emrShortcutLink');

    function loadSession(roomId, patientName, patientId) {
        // UI Transition
        noCallOverlay.classList.add('d-none');
        callInterface.classList.remove('d-none');
        activePatientName.textContent = patientName;
        emrShortcutLink.href = 'patient-view.php?id=' + patientId + '&tele_room=' + roomId;
        
        // Mock Camera Initialization
        initMockCamera();
    }

    function endCall() {
        noCallOverlay.classList.remove('d-none');
        callInterface.classList.add('d-none');
        
        const localVideo = document.getElementById('localVideo');
        if(localVideo.srcObject) {
            localVideo.srcObject.getTracks().forEach(track => track.stop());
            localVideo.srcObject = null;
        }
    }

    async function initMockCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
            document.getElementById('localVideo').srcObject = stream;
            // In a real WebRTC app, setup peer connections here and attach remote stream to remoteVideo.
        } catch (err) {
            console.error("Camera access denied or unavailable: ", err);
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
