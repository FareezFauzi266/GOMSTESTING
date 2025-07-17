<?php
session_start();
include("../header&footer/settings.php");
$currentPage = 'finance';
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
        .content-wrapper {
            background-color: #f8fafc;
        }

        .ledger-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 0 10px;
        }

        .ledger-title {
            color: #2d3748;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .btn-lightblue {
            background-color: #cce5ff;
            color: #004085;
        }

        .add-discount-btn {
            font-size: 0.875rem;
            padding: 5px 10px;
            background-color: #cce5ff;
            color: #004085;
            border: none;
            border-radius: 4px;
        }

        .add-discount-btn:hover {
            background-color: #b8daff;
        }

        table th,
        table td {
            vertical-align: middle !important;
        }

        .delete-btn {
            cursor: pointer;
            color: red;
            font-weight: bold;
        }

        .form-section {
            margin-bottom: 20px;
        }

        .totals {
            text-align: right;
        }

        .confirm-btn {
            float: right;
            margin-top: 20px;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include("../navbar/managernavbar.php"); ?>

        <!-- Sidebar -->
        <?php include("../sidebar/managersidebar.php"); ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Page Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Log Payment</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Log Payment</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <!-- Section Header (like ledger-header) -->
                            <div class="ledger-header">
                                <h2 class="ledger-title">Payment Entry</h2>
                            </div>

                            <!-- Add Item & Table Row -->
                            <div class="row">
                                <!-- Add Item Form -->
                                <div class="col-md-4">
                                    <div class="card mb-4">
                                        <div class="card-header btn-lightblue text-black">
                                            Add Item
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>Item Code</label>
                                                <select class="form-control" id="itemCode" onchange="autoFillName()">
                                                    <option value="">-- Select --</option>
                                                    <option value="A001">A001 - Mineral Water</option>
                                                    <option value="A002">A002 - Protein Shake</option>
                                                    <option value="A003">A003 - Energy Bar</option>
                                                    <option value="A004">A004 - Whey Protein Sachet</option>
                                                    <option value="A005">A005 - Isotonic Drink</option>
                                                    <option value="A006">A006 - Vitamin Water</option>
                                                    <option value="A007">A007 - Granola Bar</option>
                                                    <option value="A008">A008 - Gym Towel</option>
                                                    <option value="A009">A009 - Shaker Bottle</option>
                                                    <option value="A010">A010 - Weightlifting Gloves</option>
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


                                <!-- Table -->
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

                            <!-- Payment Method -->
                            <div class="form-section">
                                <label><strong>Payment Method:</strong></label><br />
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="payment" value="Card">
                                    <label class="form-check-label">Card</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="payment" value="QR">
                                    <label class="form-check-label">QR</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="payment" value="Cash">
                                    <label class="form-check-label">Cash</label>
                                </div>
                            </div>

                            <!-- Discount -->
                            <div class="form-section">
                                <label>Discount (%)</label>
                                <input type="number" id="discountInput" class="form-control d-inline w-25" min="0" max="100">
                                <button class="btn btn-warning btn-sm ml-2" onclick="applyDiscount()">Add Discount</button>
                                <button class="btn btn-secondary btn-sm ml-2" id="undoBtn" style="display:none;" onclick="undoDiscount()">Undo</button>
                            </div>

                            <!-- Confirm Payment -->
                            <button class="btn btn-primary confirm-btn" onclick="confirmPayment()">Confirm Payment</button>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong><?php copyright(); ?></strong>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="../app/plugins/jquery/jquery.min.js"></script>
    <script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../app/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="../app/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <script src="../app/dist/js/adminlte.min.js"></script>

    <!-- Log Payment JS -->
    <script>
        const itemData = {
            "A001": {
                name: "Mineral Water",
                price: 1.50
            },
            "A002": {
                name: "Protein Shake",
                price: 8.00
            },
            "A003": {
                name: "Energy Bar",
                price: 3.00
            },
            "A004": {
                name: "Whey Protein Sachet",
                price: 9.50
            },
            "A005": {
                name: "Isotonic Drink",
                price: 4.50
            },
            "A006": {
                name: "Vitamin Water",
                price: 3.80
            },
            "A007": {
                name: "Granola Bar",
                price: 2.80
            },
            "A008": {
                name: "Gym Towel",
                price: 15.00
            },
            "A009": {
                name: "Shaker Bottle",
                price: 12.00
            },
            "A010": {
                name: "Weightlifting Gloves",
                price: 25.00
            }
        };

        const nameToCode = {};
        Object.entries(itemData).forEach(([code, data]) => {
            nameToCode[data.name.toLowerCase()] = code;
        });

        let items = [];
        let discountPercent = 0;

        function autoFillName() {
            const code = document.getElementById('itemCode').value;
            document.getElementById('itemName').value = itemData[code]?.name || '';
        }

        function autoFillCode() {
            const name = document.getElementById('itemName').value.trim().toLowerCase();
            document.getElementById('itemCode').value = nameToCode[name] || '';
        }

        function addItem() {
            const code = document.getElementById('itemCode').value;
            const qty = parseInt(document.getElementById('itemQty').value);
            if (!code || isNaN(qty) || qty <= 0) return;

            const item = itemData[code];
            items.push({
                name: item.name,
                qty,
                pricePerUnit: item.price
            });
            updateTable();

            document.getElementById('itemCode').value = '';
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
                        <td>${item.name}</td>
                        <td>${item.qty}</td>
                        <td>${item.pricePerUnit.toFixed(2)}</td>
                        <td>${price.toFixed(2)}</td>
                        <td><span class="delete-btn" onclick="deleteItem(${index})">🗑️</span></td>
                    </tr>`;
            });

            const discountAmount = total * (discountPercent / 100);
            document.getElementById('discountValue').textContent = `RM ${discountAmount.toFixed(2)}`;
            document.getElementById('totalValue').textContent = `RM ${(total - discountAmount).toFixed(2)}`;
        }

        function confirmPayment() {
            Swal.fire({
                icon: 'success',
                title: 'Payment Confirmed',
                text: 'The payment has been successfully logged!',
                timer: 2000,
                showConfirmButton: false
            }).then(() => location.reload());
        }
    </script>

</body>

</html>