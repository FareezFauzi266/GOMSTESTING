<?php
session_start();
include("../header&footer/settings.php");
//include(function/function.php);
$currentPage = 'finance';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php browsertitle(); ?></title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"/>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css"/>
  <!-- DataTables -->
  <link rel="stylesheet" href="../app/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../app/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../app/plugins/overlayScrollbars/css/OverlayScrollbars.min.css"/>
  <!-- Theme style -->
  <link rel="stylesheet" href="../app/dist/css/adminlte.min.css" />
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
      border-radius: 4px;
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
    
    /* DataTable hover effect */
    table.dataTable tbody tr:hover {
      background-color: #ebf8ff !important;
    }
    
    /* Modal styling */
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
                    <th>Ledger ID</th>
                    <th>Ledger Name</th>
                    <th>Payment ID</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Amount (RM)</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Data will be loaded via DataTables -->
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
            <select id="periodSelect" class="form-control" onchange="filterExportTable()">
              <option value="">-- Select --</option>
              <option value="daily">Daily</option>
              <option value="monthly">Monthly</option>
              <option value="yearly">Yearly</option>
            </select>
          </div>
          <div id="filteredTableContainer" class="mt-3"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-file-pdf mr-1"></i> Print to PDF
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
  <!-- overlayScrollbars -->
  <script src="../app/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../app/dist/js/adminlte.min.js"></script>

  <script>
    $(document).ready(function() {
      // Initialize DataTable
      $('#ledgerTable').DataTable({
        "data": [
          { ledgerID: "L001", ledgerName: "Jan Membership", paymentID: "P001", date: "2025-01-02", type: "payment", amount: 100.00 },
          { ledgerID: "L002", ledgerName: "Protein Purchase", paymentID: "P002", date: "2025-01-03", type: "expense", amount: 80.00 },
          { ledgerID: "L003", ledgerName: "Feb Maintenance", paymentID: "P003", date: "2025-02-10", type: "expense", amount: 150.00 },
          { ledgerID: "L004", ledgerName: "March Income", paymentID: "P004", date: "2025-03-15", type: "income", amount: 200.00 },
          { ledgerID: "L005", ledgerName: "Jan Walk-In", paymentID: "P005", date: "2025-01-05", type: "payment", amount: 50.00 }
        ],
        "columns": [
          { 
            "data": null,
            "render": function(data, type, row, meta) {
              return meta.row + 1;
            }
          },
          { "data": "ledgerID" },
          { "data": "ledgerName" },
          { "data": "paymentID" },
          { "data": "date" },
          { "data": "type" },
          { 
            "data": "amount",
            "render": function(data) {
              return 'RM ' + data.toFixed(2);
            }
          },
          {
            "data": null,
            "render": function(data) {
              return `
                <button class="action-btn edit-btn" onclick="editEntry('${data.ledgerID}')">
                  <i class="fas fa-pencil-alt"></i>
                </button>
                <button class="action-btn delete-btn" onclick="deleteEntry('${data.ledgerID}')">
                  <i class="fas fa-trash"></i>
                </button>
              `;
            },
            "orderable": false
          }
        ],
        "responsive": true,
        "autoWidth": false,
        "pageLength": 10,
        "language": {
          "search": "_INPUT_",
          "searchPlaceholder": "Search...",
          "lengthMenu": "Show _MENU_ entries",
          "info": "Showing _START_ to _END_ of _TOTAL_ entries",
          "paginate": {
            "previous": "<i class='fas fa-chevron-left'></i>",
            "next": "<i class='fas fa-chevron-right'></i>"
          }
        }
      });
    });

    function editEntry(ledgerID) {
      alert('Editing entry: ' + ledgerID);
      // Implement your edit functionality here
    }

    function deleteEntry(ledgerID) {
      if (confirm('Are you sure you want to delete this ledger entry?')) {
        alert('Deleted entry: ' + ledgerID);
        // Implement your delete functionality here
      }
    }

    function openModal() {
      $('#exportModal').modal('show');
    }

    function filterExportTable() {
      const selected = document.getElementById("periodSelect").value;
      const container = document.getElementById("filteredTableContainer");
      let filtered = [];

      if (selected === "daily") {
        filtered = $('#ledgerTable').DataTable().data().toArray().filter(l => l.date === "2025-01-05");
      } else if (selected === "monthly") {
        filtered = $('#ledgerTable').DataTable().data().toArray().filter(l => l.date.startsWith("2025-01"));
      } else if (selected === "yearly") {
        filtered = $('#ledgerTable').DataTable().data().toArray().filter(l => l.date.startsWith("2025"));
      }

      if (filtered.length === 0) {
        container.innerHTML = "<p class='text-muted'>No transactions found for selected period.</p>";
        return;
      }

      let table = `<div class="table-responsive"><table class="table table-bordered">
        <thead><tr>
          <th>No.</th><th>Ledger ID</th><th>Name</th><th>Payment ID</th><th>Date</th><th>Type</th><th>Amount (RM)</th>
        </tr></thead><tbody>`;
      
      filtered.forEach((item, i) => {
        table += `<tr>
          <td>${i + 1}</td>
          <td>${item.ledgerID}</td>
          <td>${item.ledgerName}</td>
          <td>${item.paymentID}</td>
          <td>${item.date}</td>
          <td>${item.type}</td>
          <td>${item.amount.toFixed(2)}</td>
        </tr>`;
      });
      
      table += "</tbody></table></div>";
      container.innerHTML = table;
    }
  </script>
</body>
</html>