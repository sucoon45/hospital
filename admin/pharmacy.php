<?php
require_once '../config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/database.php';
require_once '../includes/functions/utils.php';

checkRole(['admin', 'pharmacist']);

$error = '';
$success = '';

// Add Medicine
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_medicine'])) {
    $name = sanitize($_POST['name']);
    $category = sanitize($_POST['category']);
    $quantity = sanitize($_POST['stock_quantity']);
    $price = sanitize($_POST['price']);
    $expiry = sanitize($_POST['expiry_date']);
    $threshold = sanitize($_POST['low_stock_threshold']);
    
    $sql = "INSERT INTO medicines (name, category, stock_quantity, price, expiry_date, low_stock_threshold) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    if (runQuery($sql, [$name, $category, $quantity, $price, $expiry, $threshold])) {
        $success = "Medicine added to inventory successfully.";
    } else {
        $error = "Failed to add medicine.";
    }
}

// Fetch Inventory
$medicines = fetchAll("SELECT * FROM medicines ORDER BY name ASC");

$pageTitle = "Pharmacy Inventory";
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
                <h2 class="mb-0 fw-bold">Pharmacy & Diagnostics Center</h2>
                <button class="btn btn-primary-gradient rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addMedsModal">
                    Add Medicine Stock <i class="fas fa-pills ms-2"></i>
                </button>
            </div>

            <?php echo showAlert($error, 'danger'); ?>
            <?php echo showAlert($success, 'success'); ?>

            <!-- Low Stock Alerts -->
            <?php 
            $lowStock = array_filter($medicines, function($m) { return $m['stock_quantity'] <= $m['low_stock_threshold']; });
            if(count($lowStock) > 0): 
            ?>
                <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
                    <h5 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i> Low Stock Warning</h5>
                    <ul class="mb-0 ps-3 small">
                        <?php foreach($lowStock as $ls): ?>
                            <li><strong><?php echo $ls['name']; ?></strong> is running low (Remaining: <span class="text-danger fw-bold"><?php echo $ls['stock_quantity']; ?></span>)</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0">Current Inventory</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-0 mb-0">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="p-4 border-0">Medicine Name</th>
                                    <th class="p-4 border-0">Category</th>
                                    <th class="p-4 border-0 text-center">Stock Level</th>
                                    <th class="p-4 border-0 text-center">Unit Price</th>
                                    <th class="p-4 border-0 text-center">Expiry Date</th>
                                    <th class="p-4 border-0 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($medicines)): ?>
                                    <tr><td colspan="6" class="text-center py-5 text-muted"><i class="fas fa-prescription-bottle-alt fs-2 mb-3 d-block text-secondary"></i> Inventory is empty.</td></tr>
                                <?php else: ?>
                                    <?php foreach($medicines as $med): 
                                        $stockClass = ($med['stock_quantity'] <= $med['low_stock_threshold']) ? 'text-danger' : 'text-success';
                                    ?>
                                        <tr>
                                            <td class="p-4 border-0 fw-bold"><?php echo $med['name']; ?></td>
                                            <td class="p-4 border-0 text-muted small"><?php echo $med['category']; ?></td>
                                            <td class="p-4 border-0 text-center fw-bold <?php echo $stockClass; ?>">
                                                <?php echo $med['stock_quantity']; ?>
                                            </td>
                                            <td class="p-4 border-0 text-center fw-medium"><?php echo formatCurrency($med['price']); ?></td>
                                            <td class="p-4 border-0 text-center small"><?php echo date('M Y', strtotime($med['expiry_date'])); ?></td>
                                            <td class="p-4 border-0 text-end">
                                                <button class="btn btn-light btn-sm text-primary rounded-circle shadow-sm"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-light btn-sm text-success rounded-circle shadow-sm ms-2"><i class="fas fa-plus"></i></button>
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

<!-- Add Medicine Modal -->
<div class="modal fade" id="addMedsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary-gradient text-white p-4">
                <h5 class="modal-title fw-bold">Add Medicine Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <form action="" method="POST">
                    <input type="hidden" name="add_medicine" value="1">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Medicine Name</label>
                            <input type="text" name="name" class="form-control mb-3" placeholder="e.g. Paracetamol 500mg" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Category</label>
                            <select name="category" class="form-select mb-3" required>
                                <option value="Painkiller">Painkiller</option>
                                <option value="Antibiotic">Antibiotic</option>
                                <option value="Antimalarial">Antimalarial</option>
                                <option value="Supplement">Supplement</option>
                                <option value="Liquid Syrup">Liquid Syrup</option>
                                <option value="Injection">Injection</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Initial Stock Qty</label>
                            <input type="number" name="stock_quantity" class="form-control mb-3" placeholder="100" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Alert Threshold</label>
                            <input type="number" name="low_stock_threshold" class="form-control" placeholder="10" min="1" value="10" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Unit Price (₦)</label>
                            <input type="number" name="price" class="form-control mb-3" min="50" step="50" placeholder="150" required>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Expiry Date</label>
                            <input type="date" name="expiry_date" class="form-control border-danger border-2" required>
                            <div class="form-text small text-danger">Strictly cross-check expiry before entry.</div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-primary-gradient px-5 py-3 w-100 rounded-pill fw-bold shadow-sm">
                            Save to Inventory <i class="fas fa-box-open ms-2"></i>
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
