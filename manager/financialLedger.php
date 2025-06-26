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
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../app/plugins/overlayScrollbars/css/OverlayScrollbars.min.css"/>
  <!-- Theme style -->
  <link rel="stylesheet" href="../app/dist/css/adminlte.min.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f0f0;
    }

    .container {
      background: white;
      padding: 20px;
      border: 2px solid #003366;
      max-width: 1200px;
      margin: auto;
      position: relative;
    }

    h2 {
      color: #003366;
    }

    .download-icon {
      position: absolute;
      top: 20px;
      right: 20px;
      font-size: 24px;
      cursor: pointer;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      border: 1px solid #003366;
      padding: 8px;
      text-align: center;
    }

    .delete-btn {
      color: red;
      cursor: pointer;
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      justify-content: center;
      align-items: center;
      z-index: 999;
    }

    .modal-content {
      background: white;
      padding: 20px;
      border-radius: 8px;
      width: 600px;
      max-height: 90vh;
      overflow-y: auto;
    }

    .modal-header {
      font-size: 18px;
      margin-bottom: 10px;
    }

    .modal select,
    .modal button {
      margin-top: 10px;
    }

    .modal table {
      margin-top: 20px;
    }

    .print-btn {
      padding: 8px 16px;
    }

    .modal-buttons {
      margin-top: 15px;
      text-align: right;
    }

    .modal-buttons button {
      padding: 8px 16px;
      margin-left: 10px;
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
              <!--<h1>Financial Ledger</h1>-->
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Financial Ledger</li>
              </ol>
            </div>
          </div>
        </div>
        <!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="container">
            <h2>Financial Ledger</h2>
            <div class="download-icon" onclick="openModal()">📥</div>

            <table id="ledgerTable">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Ledger ID</th>
                  <th>Ledger Name</th>
                  <th>Payment ID</th>
                  <th>Date</th>
                  <th>Type</th>
                  <th>Amount (RM)</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <!-- Ledger data goes here -->
              </tbody>
            </table>
          </div>
        </div>
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Export Modal -->
    <div class="modal" id="exportModal">
      <div class="modal-content">
        <div class="modal-header">Export Ledger</div>
        <label for="periodSelect">Select Accounting Period:</label>
        <select id="periodSelect" onchange="filterExportTable()">
          <option value="">-- Select --</option>
          <option value="daily">Daily</option>
          <option value="monthly">Monthly</option>
          <option value="yearly">Yearly</option>
        </select>

        <div id="filteredTableContainer"></div>

        <div class="modal-buttons">
          <button onclick="closeModal()">Cancel</button>
          <button class="print-btn" onclick="window.print()">Print to PDF</button>
        </div>
      </div>
    </div>

    <footer class="main-footer">
      <div class="float-right d-none d-sm-block"></div>
      <strong> <?php copyright(); ?> </strong>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->

  <script>
    const ledgerData = [
      { ledgerID: "L001", ledgerName: "Jan Membership", paymentID: "P001", date: "2025-01-02", type: "payment", amount: 100.00 },
      { ledgerID: "L002", ledgerName: "Protein Purchase", paymentID: "P002", date: "2025-01-03", type: "expense", amount: 80.00 },
      { ledgerID: "L003", ledgerName: "Feb Maintenance", paymentID: "P003", date: "2025-02-10", type: "expense", amount: 150.00 },
      { ledgerID: "L004", ledgerName: "March Income", paymentID: "P004", date: "2025-03-15", type: "income", amount: 200.00 },
      { ledgerID: "L005", ledgerName: "Jan Walk-In", paymentID: "P005", date: "2025-01-05", type: "payment", amount: 50.00 }
    ];

    function populateMainTable() {
      const tbody = document.querySelector("#ledgerTable tbody");
      tbody.innerHTML = "";
      ledgerData.forEach((item, index) => {
        const row = `<tr>
          <td>${index + 1}</td>
          <td>${item.ledgerID}</td>
          <td>${item.ledgerName}</td>
          <td>${item.paymentID}</td>
          <td>${item.date}</td>
          <td>${item.type}</td>
          <td>${item.amount.toFixed(2)}</td>
          <td><span class="delete-btn" onclick="deleteRow(${index})">🗑️</span></td>
        </tr>`;
        tbody.innerHTML += row;
      });
    }

    function deleteRow(index) {
      if (confirm("Delete this ledger entry?")) {
        ledgerData.splice(index, 1);
        populateMainTable();
      }
    }

    function openModal() {
      document.getElementById("exportModal").style.display = "flex";
    }

    function closeModal() {
      document.getElementById("exportModal").style.display = "none";
    }

    function filterExportTable() {
      const selected = document.getElementById("periodSelect").value;
      const container = document.getElementById("filteredTableContainer");
      let filtered = [];

      if (selected === "daily") {
        filtered = ledgerData.filter(l => l.date === "2025-01-05");
      } else if (selected === "monthly") {
        filtered = ledgerData.filter(l => l.date.startsWith("2025-01"));
      } else if (selected === "yearly") {
        filtered = ledgerData.filter(l => l.date.startsWith("2025"));
      }

      if (filtered.length === 0) {
        container.innerHTML = "<p>No transaction detected.</p>";
        return;
      }

      let table = `<table>
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
      table += "</tbody></table>";
      container.innerHTML = table;
    }

    window.onload = populateMainTable;
    window.onclick = function(e) {
      if (e.target.classList.contains("modal")) {
        closeModal();
      }
    };
  </script>
  <!-- jQuery -->
    <script src="../app/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="../app/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../app/dist/js/adminlte.min.js"></script>
</body>
</html>