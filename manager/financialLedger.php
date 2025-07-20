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
      background-color: #4299e1;
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
      background-color: #3182ce;
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
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <!-- Site wrapper -->
  <div class="wrapper">
    <!-- Navbar -->
    <?php include("../navbar/managernavbar.php"); ?>
    <!-- /.navbar -->
    <!-- Sidebar -->
    <?php include("../sidebar/managersidebar.php"); ?>
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
                  <i class="fas fa-download"></i> Export
                </button>
              </div>

              <table id="ledgerTable" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>No.</th>
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
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="generateExportLetter()">
            <i class="fas fa-file-pdf mr-1"></i> Print to PDF
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Ledger Entry</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="readonly-section row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Payment ID</label>
                <div class="readonly-field" id="editPaymentId">P001</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Created Date</label>
                <div class="readonly-field" id="editCreatedDate">2025-01-02</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Created By</label>
                <div class="readonly-field" id="editCreatedBy">Manager Name</div>
              </div>
            </div>
          </div>

          <form id="editForm">
            <div class="row">

              <div class="col-md-6">
                <div class="form-group form-required">
                  <label for="editType">Type</label>
                  <select class="form-control" id="editType" required>
                    <option value="membership">Membership</option>
                    <option value="supplements">Supplements</option>
                    <option value="merchandise">Merchandise</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group form-required">
                  <label for="editPaymentMethod">Payment Method</label>
                  <select id="editPaymentMethod" class="form-control" name="paymentMethod" required>
                    <option value="Cash">Cash</option>
                    <option value="Credit/Debit Card">Credit/Debit Card</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="E-Wallet">E-Wallet</option>
                  </select>

                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group form-required">
                  <label for="editAmount">Amount (RM)</label>
                  <input type="number" step="0.01" class="form-control" id="editAmount" required>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="editDiscount">Discount (RM)</label>
                  <input type="number" step="0.01" class="form-control" id="editDiscount">
                </div>
              </div>
            </div>

          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="saveChangesBtn">
            <i class="fas fa-save mr-1"></i> Save Changes
          </button>
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
            data: null,
            render: (data, type, row, meta) => meta.row + 1
          },
          {
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
            render: function(data) {
              return `
              <button class="action-btn edit-btn" onclick="editEntry('${data.paymentID}')">
                <i class="fas fa-pencil-alt"></i>
              </button>
              <button class="action-btn delete-btn" onclick="deleteEntry('${data.paymentID}', '${data.paymentID}')">
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

    function editEntry(paymentID) {
      const entry = ledgerData.find(item => item.paymentID === paymentID);
      if (!entry) return;

      $('#editPaymentId').text(entry.paymentID);
      $('#editCreatedDate').text(entry.date);
      $('#editCreatedBy').text(entry.createdBy);
      $('#editType').val(entry.type.toLowerCase());

      $('#editPaymentMethod').val(entry.paymentMethod);


      $('#editAmount').val(entry.amount);
      $('#editDiscount').val(entry.discount || 0);

      $('#editModal').modal('show');
    }


    function deleteEntry(paymentID, paymentName) {
      Swal.fire({
        title: 'Delete Ledger Entry?',
        html: `Are you sure you want to delete <strong>${paymentName}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          // You can add AJAX here to delete from DB
          Swal.fire(
            'Deleted!',
            'The ledger entry has been deleted.',
            'success'
          );
        }
      });
    }

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

    // Custom export letter function
    function generateExportLetter() {
      // Get selected period label
      const selected = document.getElementById("periodSelect").value;
      let periodLabel = "";
      if (selected === "daily") {
        periodLabel = "Financial Report for " + document.getElementById("dailyInput").value;
      } else if (selected === "monthly") {
        periodLabel = "Financial Report for " + document.getElementById("monthInput").value;
      } else if (selected === "yearly") {
        periodLabel = "Financial Report for " + document.getElementById("yearInput").value;
      } else {
        periodLabel = "Financial Report";
      }

      // Get the filtered table HTML
      const tableHTML = document.getElementById("filteredTableContainer").innerHTML;

      // Build the letter HTML
      const letterHTML = `
        <html>
        <head>
          <title>${periodLabel}</title>
          <style>
            body { font-family: Arial, sans-serif; margin: 40px; font-size: 13px; }
            .header { display: flex; align-items: center; border-bottom: 2px solid #222; padding-bottom: 16px; margin-bottom: 24px; }
            .logo { width: 80px; height: 80px; margin-right: 24px; }
            .company-info { font-size: 1.1em; }
            .company-info strong { font-size: 1.3em; }
            .contact { margin-top: 8px; }
            .topic { font-size: 1.2em; font-weight: bold; margin: 24px 0 16px 0; }
            table { width: 100%; border-collapse: collapse; margin-top: 16px; }
            th, td { border: 1px solid #888; padding: 8px; text-align: left; }
            th { background: #f0f0f0; }
          </style>
        </head>
        <body>
          <div class="header">
            <img src="../images/spartan-logo.png" class="logo" alt="Spartan Gym and Fitness Logo">
            <div class="company-info">
              <strong>Spartan Gym and Fitness Sdn. Bhd.</strong><br>
              C-2-07, Tingkat 2, Blok C Park Avenue, Jalan Pju 1, Damansara Damai,<br>
              47830, Petaling Jaya, Selangor.<br>
              <div class="contact">
                Tel: +603-61439387<br>
                Email: spartangymandfitness@gmail.com
              </div>
            </div>
          </div>
          <div class="topic">${periodLabel}</div>
          ${tableHTML}
        </body>
        </html>
      `;

      // Open in new window and print
      const printWindow = window.open('', '_blank');
      printWindow.document.write(letterHTML);
      printWindow.document.close();
      printWindow.focus();
      printWindow.print();
    }
  </script>
</body>

</html>