<?php
session_start();
include("../db.php");
include("../header&footer/settings.php");
$currentPage = 'finance';

if (!isset($_SESSION['userID'])) {
    die("Please login first.");
}
$userID = $_SESSION['userID'];

// Handle POST AJAX request for payment processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['items']) || !is_array($data['items']) || count($data['items']) === 0) {
        echo json_encode(['status' => 'error', 'message' => 'No items provided']);
        exit;
    }

    $paymentMethod = $conn->real_escape_string($data['paymentMethod']);
    $discount = intval($data['discount']);
    $items = $data['items'];
    $total = 0;

    $conn->begin_transaction();

    try {
        foreach ($items as $item) {
            $itemCode = intval($item['itemCode']);
            $qty = intval($item['qty']);

            $res = $conn->query("SELECT itemQuantity, itemPrice, itemCategory FROM inventoryItem WHERE itemCode = $itemCode FOR UPDATE");
            if ($res->num_rows === 0) {
                throw new Exception("Item code $itemCode not found.");
            }
            $row = $res->fetch_assoc();

            if ($row['itemQuantity'] < $qty) {
                throw new Exception("Insufficient stock for item code $itemCode. Available: {$row['itemQuantity']}");
            }

            $total += $row['itemPrice'] * $qty;
        }

        $discountedTotal = $total * ((100 - $discount) / 100);

        $stmt = $conn->prepare("INSERT INTO logPayment (userID, paymentAmount, paymentMethod, transactionType, discount) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $categories = array_unique(array_map(fn($i) => $i['category'] ?? '', $items));
        $transactionType = implode(", ", $categories);

        $stmt->bind_param("idssi", $userID, $discountedTotal, $paymentMethod, $transactionType, $discount);
        $stmt->execute();

        if ($stmt->affected_rows !== 1) {
            throw new Exception("Failed to insert payment log.");
        }
        $paymentID = $stmt->insert_id;
        $stmt->close();

        foreach ($items as $item) {
            $itemCode = intval($item['itemCode']);
            $qty = intval($item['qty']);
            $updateRes = $conn->query("UPDATE inventoryItem SET itemQuantity = itemQuantity - $qty WHERE itemCode = $itemCode");
            if (!$updateRes) {
                throw new Exception("Failed to update stock for item code $itemCode.");
            }
        }

        $conn->commit();

        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

$query = "SELECT itemCode, itemName, itemPrice, itemQuantity, itemCategory FROM inventoryItem 
          WHERE itemCategory IN ('Membership', 'Supplements', 'Merchandise')";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php browsertitle(); ?></title>
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css" />
    <!-- Bootstrap -->
    <link rel="stylesheet" href="../app/plugins/bootstrap/css/bootstrap.min.css" />
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="../app/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
    <!-- AdminLTE -->
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css" />
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../app/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css" />

    <style>
        .content-wrapper { background-color: #f8fafc; }
        .ledger-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 0 10px; }
        .ledger-title { color: #2d3748; font-size: 1.8rem; font-weight: 600; }
        .btn-lightblue { background-color: #cce5ff; color: #004085; }
        .add-discount-btn { font-size: 0.875rem; padding: 5px 10px; background-color: #cce5ff; color: #004085; border: none; border-radius: 4px; }
        .add-discount-btn:hover { background-color: #b8daff; }
        table th, table td { vertical-align: middle !important; }
        .delete-btn { cursor: pointer; color: red; font-weight: bold; }
        .form-section { margin-bottom: 20px; }
        .totals { text-align: right; }
        .confirm-btn { float: right; margin-top: 20px; }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php include("../navbar/managernavbar.php"); ?>
    <?php include("../sidebar/managersidebar.php"); ?>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"><h1>Log Payment</h1></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Log Payment</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">

                        <div class="ledger-header"><h2 class="ledger-title">Payment Entry</h2></div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <div class="card-header btn-lightblue text-black">Add Item</div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Item Code</label>
                                            <select class="form-control" id="itemDropdown" onchange="onSelectItem()">
                                                <option value="">-- Select Item --</option>
                                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                                    <option
                                                        value="<?= $row['itemCode'] ?>"
                                                        data-name="<?= htmlspecialchars($row['itemName']) ?>"
                                                        data-price="<?= $row['itemPrice'] ?>"
                                                        data-quantity="<?= $row['itemQuantity'] ?>"
                                                        data-category="<?= $row['itemCategory'] ?>"
                                                    >
                                                        <?= $row['itemCode'] . " - " . htmlspecialchars($row['itemName']) ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <p class="text-center font-weight-bold text-primary">OR</p>

                                        <div class="form-group">
                                            <label>Item Name</label>
                                            <input type="text" class="form-control" id="itemName" oninput="autoFillCode()">
                                        </div>

                                        <div class="form-group">
                                            <label>Quantity</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="itemQty" min="1">
                                                <div class="input-group-append">
                                                    <button class="btn btn-lightblue" onclick="addItem()">Add</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <table class="table table-bordered" id="itemTable">
                                    <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Name</th>
                                        <th>Qty</th>
                                        <th>P.P.U</th>
                                        <th>Price (RM)</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                                <div class="totals">
                                    <p>Discount: <span id="discountValue">RM 0.00</span></p>
                                    <h5>Total: <span id="totalValue">RM 0.00</span></h5>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <label><strong>Payment Method:</strong></label><br />
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment" value="Credit/Debit Card">
                                <label class="form-check-label">Card</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment" value="E-Wallet">
                                <label class="form-check-label">E-Wallet</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment" value="Cash">
                                <label class="form-check-label">Cash</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment" value="Bank Transfer">
                                <label class="form-check-label">Bank Transfer</label>
                            </div>
                        </div>

                        <div class="form-section">
                            <label>Discount (%)</label>
                            <input type="number" id="discountInput" class="form-control d-inline w-25" min="0" max="100">
                            <button class="btn btn-warning btn-sm ml-2" onclick="applyDiscount()">Add Discount</button>
                            <button class="btn btn-secondary btn-sm ml-2" id="undoBtn" style="display:none;" onclick="undoDiscount()">Undo</button>
                        </div>

                        <button class="btn btn-primary confirm-btn" onclick="confirmPayment()">Confirm Payment</button>

                    </div>
                </div>
            </div>
        </section>
    </div>

    <footer class="main-footer">
        <strong><?php copyright(); ?></strong>
    </footer>
</div>

<script src="../app/plugins/jquery/jquery.min.js"></script>
<script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../app/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="../app/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>

<script>
    // Store items added
    let items = [];
    let discountPercent = 0;

    // Pull from dropdown
    function onSelectItem() {
        const dropdown = document.getElementById('itemDropdown');
        const selectedOption = dropdown.options[dropdown.selectedIndex];
        if (!selectedOption.value) return;

        document.getElementById('itemName').value = selectedOption.getAttribute('data-name');
        document.getElementById('itemQty').value = '';
    }

    // Auto fill code from name (not full autocomplete, just a helper)
    function autoFillCode() {
        const nameInput = document.getElementById('itemName').value.trim().toLowerCase();
        const dropdown = document.getElementById('itemDropdown');
        for (let i = 0; i < dropdown.options.length; i++) {
            if (dropdown.options[i].getAttribute('data-name').toLowerCase() === nameInput) {
                dropdown.selectedIndex = i;
                return;
            }
        }
        dropdown.selectedIndex = 0; // reset if no match
    }

    // Add item to table and list
    function addItem() {
        const dropdown = document.getElementById('itemDropdown');
        const selectedOption = dropdown.options[dropdown.selectedIndex];
        const qtyInput = document.getElementById('itemQty').value;
        const qty = parseInt(qtyInput);

        if (!selectedOption.value) {
            alert("Please select an item.");
            return;
        }
        if (!qty || qty <= 0) {
            alert("Please enter a valid quantity.");
            return;
        }

        const availableStock = parseInt(selectedOption.getAttribute('data-quantity'));
        if (qty > availableStock) {
            alert(`Not enough stock. Available quantity: ${availableStock}`);
            return;
        }

        const itemCode = selectedOption.value;
        const itemName = selectedOption.getAttribute('data-name');
        const itemPrice = parseFloat(selectedOption.getAttribute('data-price'));
        const itemCategory = selectedOption.getAttribute('data-category');

        // Check if item already in list, increase qty if so
        const existingIndex = items.findIndex(i => i.itemCode === itemCode);
        if (existingIndex >= 0) {
            if (items[existingIndex].qty + qty > availableStock) {
                alert(`Cannot add ${qty} more. Stock limit exceeded.`);
                return;
            }
            items[existingIndex].qty += qty;
        } else {
            items.push({itemCode, itemName, qty, pricePerUnit: itemPrice, category: itemCategory});
        }
        updateTable();

        // Clear inputs
        dropdown.selectedIndex = 0;
        document.getElementById('itemName').value = '';
        document.getElementById('itemQty').value = '';
    }

    function deleteItem(index) {
        items.splice(index, 1);
        updateTable();
    }

    function applyDiscount() {
        const input = parseFloat(document.getElementById('discountInput').value);
        if (!isNaN(input) && input >= 0 && input <= 100) {
            discountPercent = input;
            document.getElementById('undoBtn').style.display = 'inline-block';
            updateTable();
        }
    }

    function undoDiscount() {
        discountPercent = 0;
        document.getElementById('discountInput').value = '';
        document.getElementById('undoBtn').style.display = 'none';
        updateTable();
    }

    function updateTable() {
        const tbody = document.querySelector('#itemTable tbody');
        tbody.innerHTML = '';
        let total = 0;

        items.forEach((item, index) => {
            const price = item.qty * item.pricePerUnit;
            total += price;
            tbody.innerHTML += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.itemName}</td>
                    <td>${item.qty}</td>
                    <td>RM ${item.pricePerUnit.toFixed(2)}</td>
                    <td>RM ${price.toFixed(2)}</td>
                    <td><span class="delete-btn" onclick="deleteItem(${index})">🗑️</span></td>
                </tr>`;
        });

        const discountAmount = total * (discountPercent / 100);
        document.getElementById('discountValue').textContent = `RM ${discountAmount.toFixed(2)}`;
        document.getElementById('totalValue').textContent = `RM ${(total - discountAmount).toFixed(2)}`;
    }

    function confirmPayment() {
        if (items.length === 0) {
            Swal.fire('Error', 'Please add at least one item before confirming payment.', 'error');
            return;
        }
        const paymentMethod = document.querySelector('input[name="payment"]:checked');
        if (!paymentMethod) {
            Swal.fire('Error', 'Please select a payment method.', 'error');
            return;
        }

        const discount = parseInt(document.getElementById('discountInput').value) || 0;

        const payload = {
            items,
            paymentMethod: paymentMethod.value,
            discount,
        };

        fetch('logPayment.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Payment Confirmed',
                    text: 'The payment has been successfully logged!',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'An error occurred', 'error');
            }
        })
        .catch(() => {
            Swal.fire('Error', 'Server error. Please try again later.', 'error');
        });
    }
</script>
</body>
</html>