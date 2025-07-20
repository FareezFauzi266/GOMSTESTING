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
//include("../connection/connection.php");
// echo "<script>console.log('UserID: " . $_SESSION['userID'] . "');</script>";
//include(function/function.php);
$currentPage = 'dashboard';

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>
        <?php browsertitle(); ?>
    </title>
    <style>
      #lowStockItemsList ul {
        max-height: 200px;
        overflow-y: auto;
        padding-right: 10px;
    }

    #lowStockItemsList li {
        padding: 3px 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    </style>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"/>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css"/>
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="../app/plugins/overlayScrollbars/css/OverlayScrollbars.min.css"/>
    <!-- Theme style -->
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css" />
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
                <h1>Dashboard</h1>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active">Dashboard</li>
                </ol>
              </div>
            </div>
          </div>
          <!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <!-- Inventory Stock Card -->
                  <div class="card">
                      <div class="card-header">
                          <h3 class="card-title">Inventory Stock Levels</h3>
                          <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                  <i class="fas fa-minus"></i>
                              </button>
                          </div>
                      </div>
                      <div class="card-body">
                          <div class="row">
                              <div class="col-md-8">
                                <div class="chart-container" style="position: relative; height: 100%; width: 100%">
                                    <canvas id="inventoryChart"></canvas>
                                </div>
                              </div>
                              <div class="col-md-4">
                                  <div class="info-box mb-3 bg-info">
                                      <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                                      <div class="info-box-content">
                                          <span class="info-box-text">Total Inventory Items</span>
                                          <span class="info-box-number" id="totalItems">0</span>
                                          <div class="progress-description">
                                            Total Value: <span id="totalStockPrice">RM 0.00</span>
                                        </div>
                                      </div>
                                  </div>
                                  <div class="info-box mb-3 bg-success">
                                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Well-Stocked Items (above 50 quantity)</span>
                                        <span class="info-box-number" id="wellStockedCount">0 items</span>
                                        <div id="wellStockedItemsList" class="mt-2">
                                            <span class="text-muted">No well-stocked items</span>
                                        </div>
                                    </div>
                                </div>
                                  <div class="info-box mb-3 bg-warning">
                                      <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                                      <div class="info-box-content">
                                          <span class="info-box-text">Low Stock Items (under 50 quantity)</span>
                                          <!-- <span class="info-box-number" id="lowStockCount">0</span> -->
                                          <div id="lowStockItemsList" class="mt-2">
                                              <span class="text-muted">No low stock items</span>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>

                <!-- Profit Card -->
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Profit</h3>
                    <div class="card-tools">
                      <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                      </button>
                      <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                      </button> -->
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <!-- Total Profit (All Time) -->
                      <div class="col-md-6">
                        <div class="info-box bg-info">
                          <span class="info-box-icon"><i class="fas fa-calculator"></i></span>
                          <div class="info-box-content">
                            <!-- <span class="info-box-text">Profit</span> -->
                            <span class="progress-description">Total (All Time)</span>
                            <span class="info-box-number" id="totalProfit">RM 0.00</span>
                            <!-- <span class="progress-description">Total (All Time)</span> -->
                          </div>
                        </div>
                      </div>
                      <!-- This Month's Profit -->
                      <div class="col-md-6">
                        <div class="info-box bg-success">
                          <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                          <div class="info-box-content">
                            <!-- <span class="info-box-text">Profit</span> -->
                            <span class="progress-description">This Month</span>
                            <span class="info-box-number" id="monthlyProfit">RM 0.00</span>
                            <!-- <span class="progress-description">This Month</span> -->
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- <div class="card-footer">Footer</div> -->
                </div>

                <!-- Maintained Card  -->
                <div class="card mt-3">
                  <div class="card-header">
                      <h3 class="card-title">Maintenance Status</h3>
                      <div class="card-tools">
                          <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                          </button>
                      </div>
                  </div>
                  <div class="card-body">
                      <div class="row">
                          <!-- Needs Repair Column -->
                          <div class="col-md-6">
                              <div class="info-box bg-danger">
                                  <span class="info-box-icon"><i class="fas fa-tools"></i></span>
                                  <div class="info-box-content">
                                      <span class="info-box-text">Needs Repair</span>
                                      <!-- <span class="info-box-number" id="repairCount">0</span> -->
                                      <div id="needsRepairList" class="mt-2">
                                          <span class="text-muted">No items need repair</span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          
                          <!-- Replace Soon Column -->
                          <div class="col-md-6">
                              <div class="info-box bg-warning">
                                  <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                                  <div class="info-box-content">
                                      <span class="info-box-text">Need to replace soon</span>
                                      <!-- <span class="info-box-number" id="replaceCount">0</span> -->
                                      <div id="replaceSoonList" class="mt-2">
                                          <span class="text-muted">No items need replacement</span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->

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
    <!-- JS dependencies -->
    <script src="../app/plugins/jquery/jquery.min.js"></script>
    <script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../app/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../app/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../app/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../app/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../app/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="../app/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <script src="../app/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
      //when page is load auto find the daat
      $(document).ready(function() {
        loadInventoryData();
        loadProfitData();
        loadMaintenanceData();
      });

      // for load inventory
      function loadInventoryData() {
        $.ajax({
            url: 'getDashboardInventory.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Update key metrics
                $('#totalItems').text(data.totalItems);
                $('#totalStockPrice').text('RM ' + data.totalValue);
                
                // Update well-stocked items
                $('#wellStockedCount').text(data.wellStocked.count + ' items');
                updateItemList(
                    '#wellStockedItemsList', 
                    data.wellStocked.items, 
                    'No well-stocked items',
                    'success'
                );
                
                // Update low stock items
                $('#lowStockCount').text(data.lowStockCount + ' items');
                updateItemList(
                    '#lowStockItemsList', 
                    data.lowStockItems, 
                    'No low stock items',
                    'warning'
                );
                
                // Draw chart
                drawInventoryChart(data.categories, data.quantities, data.values);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching inventory data:', error);
            }
        });
      }

      // Unified function for both well-stocked and low stock items
      function updateItemList(elementId, items, emptyMessage, colorClass) {
        const container = $(elementId);
        
        if (!items || items.length === 0) {
            container.html(`<span class="text-muted">${emptyMessage}</span>`);
            return;
        }
        
        let html = '<ul class="list-unstyled">';
        items.forEach(item => {
            html += `
                <li class="mb-1">
                    <i class="fas fa-caret-right text-${colorClass} mr-2"></i>
                    <strong>${item.itemName}</strong> 
                    (Category - ${item.itemCategory}, ${item.itemQuantity} remaining)
                </li>
            `;
        });
        html += '</ul>';
        
        container.html(html);
      }

      // Update chart function to optionally show values
      function drawInventoryChart(categories, quantities, values) {
        var ctx = document.getElementById('inventoryChart').getContext('2d');
        var inventoryChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: categories,
                datasets: [{
                    label: 'Stock Quantity',
                    data: quantities,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                }, {
                    label: 'Inventory Value (RM)',
                    data: values,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    type: 'line',
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Quantity'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Value (RM)'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Product Categories'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.datasetIndex === 0) {
                                    label += context.parsed.y + ' units';
                                } else {
                                    label += 'RM ' + context.parsed.y.toFixed(2);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
      }

      // for load profit
      function loadProfitData() {
        $.ajax({
          url: 'getDashboardProfit.php',
          type: 'GET',
          dataType: 'json',
          success: function(data) {
            // console.log(data);
            // Update Total Profit
            $('#totalProfit').text('RM ' + parseFloat(data.totalProfit || 0).toFixed(2));
            
            // Update Monthly Profit
            $('#monthlyProfit').text('RM ' + parseFloat(data.monthlyProfit || 0).toFixed(2));
          },
          error: function(xhr, status, error) {
            console.error('Error fetching profit data:', error);
          }
        });
      }

      // for load maintenance 
      function loadMaintenanceData() {
        $.ajax({
            url: 'getDashboardMaintenance.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Update Needs Repair section
                $('#repairCount').text(data.repairCount);
                updateListDisplay('#needsRepairList', data.needsRepair, 'No items need repair', 'danger');
                
                // Update Replace Soon section
                $('#replaceCount').text(data.replaceCount);
                updateListDisplay('#replaceSoonList', data.replaceSoon, 'No items need replacement', 'warning');
            },
            error: function(xhr, status, error) {
                console.error('Error fetching maintenance data:', error);
                $('#needsRepairList').html('<span class="text-danger">Error loading data</span>');
                $('#replaceSoonList').html('<span class="text-danger">Error loading data</span>');
            }
        });
      }

      // Helper function to display lists
      function updateListDisplay(elementId, items, emptyMessage, textClass) {
        if (items.length === 0) {
            $(elementId).html(`<span class="text-muted">${emptyMessage}</span>`);
        } else {
            let html = '<ul class="list-unstyled">';
            items.forEach(item => {
                html += `<li><i class="fas fa-circle text-${textClass} mr-2"></i>${item}</li>`;
            });
            html += '</ul>';
            $(elementId).html(html);
        }
      }

      // Call on page load
      
    </script>

    
  </body>
</html>
