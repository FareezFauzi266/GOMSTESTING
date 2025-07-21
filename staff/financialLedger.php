<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
if (!isset($_SESSION['userID'])) {
    header("Location: /gomstesting/GOMSTESTING/index.php");
    exit;
}
include("../header&footer/settings.php");
include("../connection/connection.php"); // uses $dbh from PDO
$currentPage = 'finance';

// AJAX: Update ledger entry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Get the values from request
    $paymentID = $data['paymentID'];
    $type = strtolower($data['type']);
    $paymentMethod = $data['paymentMethod'];
    $amount = $data['amount'];
    $discount = $data['discount'];

    // Prepare and execute update query
    $stmt = $dbh->prepare("UPDATE logPayment SET transactionType = ?, paymentMethod = ?, paymentAmount = ?, discount = ? WHERE paymentID = ?");
    $success = $stmt->execute([$type, $paymentMethod, $amount, $discount, $paymentID]);

    header('Content-Type: application/json');

    echo json_encode(['success' => $success]);
    exit;
}
// Fetch all logPayment records joined with users
$payments = [];
try {
    $stmt = $dbh->prepare("
        SELECT l.*, u.userName 
        FROM logPayment l 
        JOIN users u ON l.userID = u.userID 
        ORDER BY l.createdAt DESC
    ");
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php browsertitle(); ?></title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css" />
    <!-- DataTables -->
    <link rel="stylesheet" href="../app/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../app/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../app/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="../app/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
    <!-- Theme style -->
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css" />
    <style>
        /* Your existing styles remain unchanged */
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

        .export-btn {
            background-color:
                #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .export-btn:hover {
            background-color: #218838;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            cursor: pointer;
            margin: 0 2px;
        }

        .edit-btn {
            background-color: #4299e1;
            color: white;
        }

        .edit-btn:hover {
            background-color: #3182ce;
        }

        .delete-btn {
            background-color: #f56565;
            color: white;
        }

        .delete-btn:hover {
            background-color: #e53e3e;
        }

        table.dataTable tbody tr:hover {
            background-color: #ebf8ff !important;
        }

        .modal-content {
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            font-size: 1.25rem;
            font-weight: 600;
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 16px 20px;
        }

        .readonly-section {
            padding-bottom: 15px;
            margin-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .readonly-field {
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .form-required label:after {
            content: " *";
            color: #e53e3e;
        }

        .disabled-btn {
            background-color: #cbd5e0 !important;
            /* gray-400 */
            color: #718096 !important;
            /* gray-600 */
            cursor: not-allowed;
            opacity: 0.7;
            border: none;
            pointer-events: none;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Navbar -->
        <?php include("../navbar/managernavbar.php"); ?>
        <!-- /.navbar -->
        <!-- Sidebar -->
        <?php include("../sidebar/staffsidebar.php"); ?>
        <!-- /.sidebar -->
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Financial Ledger</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Financial Ledger</li>
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
                            <div class="ledger-header">
                                <h2 class="ledger-title">Financial Records</h2>
                                <button class="export-btn" onclick="openModal()">
                                    <i class="fas fa-calendar-alt"></i> Select Accounting Period
                                </button>

                            </div>

                            <table id="ledgerTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Date</th>
                                        <th>Payment Amount</th>
                                        <th>Payment Method</th>
                                        <th>Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data loaded dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <footer class="main-footer">
            <div class="float-right d-none d-sm-block"></div>
            <strong> <?php copyright(); ?> </strong>
        </footer>
    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Ledger</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="periodSelect">Select Accounting Period:</label>
                        <select id="periodSelect" class="form-control" onchange="toggleDateInputs()">
                            <option value="">-- Select --</option>
                            <option value="daily">Daily</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                        <div id="dateInputs" class="mb-3">
                            <input type="date" id="dailyInput" class="form-control" style="display:none;" />
                            <input type="month" id="monthInput" class="form-control" style="display:none;" />
                            <input type="number" id="yearInput" class="form-control" min="2000" max="2099" placeholder="Enter year (e.g. 2025)" style="display:none;" />
                        </div>
                        <button onclick="filterExportTable()" class="btn btn-primary">Filter</button>
                    </div>
                    <div id="filteredTableContainer" class="mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="../app/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="../app/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../app/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../app/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../app/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="../app/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="../app/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../app/dist/js/adminlte.min.js"></script>

    <script>
        const ledgerData = <?php echo json_encode(array_map(function ($p) {
                                return [
                                    'paymentID' => (string) $p['paymentID'],  // convert to string
                                    'date' => $p['createdAt'],
                                    'amount' => floatval($p['paymentAmount']),
                                    'paymentMethod' => $p['paymentMethod'],
                                    'type' => ucfirst($p['transactionType']),
                                    'discount' => isset($p['discount']) ? floatval($p['discount']) : 0,
                                    'createdBy' => $p['userName']
                                ];
                            }, $payments)); ?>;

        $(document).ready(function() {
            const table = $('#ledgerTable').DataTable({
                data: ledgerData,
                columns: [{
                        data: 'paymentID'
                    },
                    {
                        data: 'date'
                    },
                    {
                        data: 'amount',
                        render: data => 'RM ' + data.toFixed(2)
                    },
                    {
                        data: 'paymentMethod'
                    },
                    {
                        data: 'type'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function() {
                            return `
      <button class="action-btn disabled-btn" title="Editing Disabled">
        <i class="fas fa-pencil-alt"></i>
      </button>
      <button class="action-btn disabled-btn" title="Deletion Disabled">
        <i class="fas fa-trash"></i>
      </button>
    `;
                        }
                    }

                ]
            });
        });
        $('#saveChangesBtn').click(function() {
            if ($('#editForm')[0].checkValidity()) {
                // Collect form data
                const paymentID = $('#editPaymentId').text();
                const type = $('#editType').val();
                const paymentMethod = $('#editPaymentMethod').val();
                const amount = parseFloat($('#editAmount').val());
                let discount = parseFloat($('#editDiscount').val());
                if (isNaN(discount)) discount = 0;

                $.ajax({
                    url: 'financialLedger.php?action=update',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        paymentID,
                        type,
                        paymentMethod,
                        amount,
                        discount
                    }),
                    success: function(response) {
                        if (response.success) {
                            // Update local ledgerData
                            const entryIndex = ledgerData.findIndex(item => item.paymentID === paymentID);
                            if (entryIndex > -1) {
                                ledgerData[entryIndex].type = capitalize(type);
                                ledgerData[entryIndex].paymentMethod = capitalizePaymentMethod(paymentMethod);
                                ledgerData[entryIndex].amount = amount;
                                ledgerData[entryIndex].discount = discount;

                                // Refresh DataTable row
                                const table = $('#ledgerTable').DataTable();
                                table.clear().rows.add(ledgerData).draw();
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Changes Saved',
                                text: 'The ledger entry has been updated successfully',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $('#editModal').modal('hide');
                        } else {
                            Swal.fire('Error', 'Failed to save changes.', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'An error occurred: ' + xhr.responseText, 'error');
                    }
                });
            } else {
                $('#editForm')[0].reportValidity();
            }
        });

        // Helper functions
        function capitalize(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
        }

        function capitalizePaymentMethod(str) {
            if (!str) return '';
            // Handle known cases (your payment methods)
            switch (str.toLowerCase()) {
                case 'cash':
                    return 'Cash';
                case 'card':
                    return 'Credit/Debit Card';
                case 'transfer':
                    return 'Bank Transfer';
                case 'ewallet':
                    return 'E-Wallet';
                default:
                    return capitalize(str);
            }
        }
        console.log('Editing payment method:', entry.paymentMethod);

        function openModal() {
            $('#exportModal').modal('show');
        }

        function toggleDateInputs() {
            const selected = document.getElementById("periodSelect").value;
            document.getElementById("dailyInput").style.display = "none";
            document.getElementById("monthInput").style.display = "none";
            document.getElementById("yearInput").style.display = "none";

            if (selected === "daily") {
                document.getElementById("dailyInput").style.display = "block";
            } else if (selected === "monthly") {
                document.getElementById("monthInput").style.display = "block";
            } else if (selected === "yearly") {
                document.getElementById("yearInput").style.display = "block";
            }
        }
        document.addEventListener("DOMContentLoaded", toggleDateInputs);


        function filterExportTable() {
            const selected = document.getElementById("periodSelect").value;
            const container = document.getElementById("filteredTableContainer");
            let filtered = [];

            if (selected === "daily") {
                const inputDate = document.getElementById("dailyInput").value;
                //print_r(inputDate);
                if (!inputDate) return container.innerHTML = "<p class='text-danger'>Please select a date.</p>";
                filtered = ledgerData.filter(l => l.date.substring(0, 10) === inputDate);
            } else if (selected === "monthly") {
                const inputMonth = document.getElementById("monthInput").value;
                if (!inputMonth) return container.innerHTML = "<p class='text-danger'>Please select a month.</p>";
                filtered = ledgerData.filter(l => l.date.startsWith(inputMonth));

            } else if (selected === "yearly") {
                const inputYear = document.getElementById("yearInput").value;
                //dd(inputYear)
                if (!inputYear) return container.innerHTML = "<p class='text-danger'>Please enter a year.</p>";
                filtered = ledgerData.filter(l => l.date.startsWith(inputYear));
            }

            if (filtered.length === 0) {
                container.innerHTML = "<p class='text-muted'>No transactions found for selected period.</p>";
                return;
            }

            let table = `<div class="table-responsive"><table class="table table-bordered">
    <thead><tr>
      <th>No.</th><th>Payment ID</th><th>Date</th><th>Type</th><th>Payment Method</th><th>Amount (RM)</th>
    </tr></thead><tbody>`;

            filtered.forEach((item, i) => {
                table += `<tr>
      <td>${i + 1}</td>
      <td>${item.paymentID}</td>
      <td>${item.date}</td>
      <td>${item.type}</td>
      <td>${item.paymentMethod}</td>
      <td>${item.amount.toFixed(2)}</td>
    </tr>`;
            });

            table += "</tbody></table></div>";
            container.innerHTML = table;
        }
    </script>
</body>

</html>