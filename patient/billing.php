<?php
require_once '../config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/database.php';
require_once '../includes/functions/utils.php';

checkRole(['patient']);

$user_id = $_SESSION['user_id'];
$patient = fetchOne("SELECT id FROM patients WHERE user_id = ?", [$user_id]);
$patient_id = $patient['id'];

// Mock Billing Items
$invoices = fetchAll("SELECT * FROM invoices WHERE patient_id = ? ORDER BY created_at DESC", [$patient_id]);
$payments = fetchAll("SELECT p.*, i.id as invoice_num 
                      FROM payments p 
                      JOIN invoices i ON p.invoice_id = i.id 
                      WHERE p.patient_id = ? 
                      ORDER BY p.payment_date DESC", [$patient_id]);

$pageTitle = "Billing & Payments";
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
    <!-- Paystack Inline JS -->
    <script src="https://js.paystack.co/v1/inline.js"></script>
</head>
<body class="bg-soft-blue">

<div class="container-fluid">
    <div class="row">
        <?php include_once '../includes/components/sidebar.php'; ?>

        <main class="col-lg-10 p-4 offset-lg-2">
            <h2 class="mb-4 fw-bold">Billing & Financials</h2>

            <div class="row">
                <!-- INVOICES -->
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h5 class="fw-bold mb-0">Unpaid Invoices</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border-0 mb-0">
                                    <thead class="bg-light text-secondary small text-uppercase fw-bold">
                                        <tr>
                                            <th class="p-4 border-0">Invoice #</th>
                                            <th class="p-4 border-0">Items Summary</th>
                                            <th class="p-4 border-0 text-center">Amount</th>
                                            <th class="p-4 border-0 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($invoices)): ?>
                                            <tr><td colspan="4" class="text-center py-5"><p class="text-muted">You have no pending invoices.</p></td></tr>
                                        <?php else: ?>
                                            <?php foreach($invoices as $inv): 
                                                if($inv['status'] == 'Paid') continue;
                                            ?>
                                                <tr>
                                                    <td class="p-4 border-0 fw-bold text-primary">INV-<?php echo str_pad($inv['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                                    <td class="p-4 border-0 small text-muted">Consultation fees & medicine...</td>
                                                    <td class="p-4 border-0 text-center fw-bold"><?php echo formatCurrency($inv['total_amount']); ?></td>
                                                    <td class="p-4 border-0 text-center">
                                                        <button class="btn btn-primary-gradient btn-sm rounded-pill px-4 fw-bold shadow-sm" onclick="payWithPaystack(<?php echo $inv['total_amount']; ?>, <?php echo $inv['id']; ?>)">Pay Online</button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- PAYMENT HISTORY -->
                    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mt-4">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h5 class="fw-bold mb-0">Recent Payment History</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border-0 mb-0">
                                    <thead class="bg-light text-secondary small text-uppercase">
                                        <tr>
                                            <th class="p-4 border-0">ID</th>
                                            <th class="p-4 border-0">Method</th>
                                            <th class="p-4 border-0 text-center">Date</th>
                                            <th class="p-4 border-0 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($payments)): ?>
                                            <tr><td colspan="4" class="text-center py-5">No payment history found.</td></tr>
                                        <?php else: ?>
                                            <?php foreach($payments as $pay): ?>
                                                <tr>
                                                    <td class="p-4 border-0 small">TX-<?php echo substr($pay['transaction_ref'], 0, 8); ?>...</td>
                                                    <td class="p-4 border-0 small fw-bold"><?php echo $pay['payment_method']; ?></td>
                                                    <td class="p-4 border-0 text-center small"><?php echo date('M d, Y', strtotime($pay['payment_date'])); ?></td>
                                                    <td class="p-4 border-0 text-center">
                                                        <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-2 small">Success</span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QUICK ACTIONS -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                        <h5 class="fw-bold mb-4">Financial Support</h5>
                        <div class="alert bg-soft-blue border-0 rounded-4 p-4 mb-4">
                            <div class="d-flex mb-3 align-items-center">
                                <i class="fas fa-shield-virus text-primary fs-3 me-3"></i>
                                <h6 class="mb-0 fw-bold">NHIS Coverage</h6>
                            </div>
                            <p class="small text-muted mb-0">If you are under NHIS, please present your card at the reception for verification before payment.</p>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-outline-secondary p-3 text-start small border-2 rounded-4 fw-bold"><i class="fas fa-file-pdf me-2"></i> Download All Statement</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
  function payWithPaystack(amount, invoice_id) {
    let handler = PaystackPop.setup({
      key: 'pk_test_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', // Test Key Placeholder
      email: '<?php echo $_SESSION['email']; ?>',
      amount: amount * 100, // Amount in kobo
      currency: 'NGN', 
      ref: 'TX_' + Math.floor((Math.random() * 1000000000) + 1), 
      onClose: function(){
        alert('Transaction was not completed.');
      },
      callback: function(response){
        // In real world, send reference to a verification script
        alert('Payment Successful! Reference: ' + response.reference);
        window.location.reload(); 
      }
    });

    handler.openIframe();
  }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
