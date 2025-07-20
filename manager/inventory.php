<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
if (!isset($_SESSION['userID'])) {
    header("Location: /GOMS/index.php");
    exit;
}
include("../header&footer/settings.php");
include("../connection/db2.php");
include("../function/function.php");

$currentPage = 'inventory';

$sql = "SELECT ii.itemCode, ii.itemName, ii.itemQuantity, s.supplierName
        FROM inventoryItem ii
        LEFT JOIN supplier s ON ii.itemSupplierID = s.supplierID";
$result = $conn->query($sql);
$catQuery = $conn->query("SELECT DISTINCT itemCategory AS itemCategory FROM inventoryItem WHERE itemCategory IS NOT NULL");
$categories = $catQuery->fetch_all(MYSQLI_ASSOC);

$itemCode = $conn->insert_id;





// Handle AJAX requests
if (isset($_POST['ajax'])) {
    handleAjaxRequest($conn);
}

// Get data for page
$inventoryItems = getInventoryItems($conn);
$suppliers = getSuppliers($conn);
//$categories = getCategories($conn);

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
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="../app/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../app/plugins/overlayScrollbars/css/OverlayScrollbars.min.css"/>
  <!-- Theme style -->
  <link rel="stylesheet" href="../app/dist/css/adminlte.min.css" />
  <style>
    .content-wrapper {
      background-color: #f8fafc;
    }
    
    .inventory-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding: 0 10px;
    }
    
    .inventory-title {
      color: #2d3748;
      font-size: 1.8rem;
      font-weight: 600;
    }
    
    .add-item-btn {
      background-color: #28a745;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 4px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .add-item-btn:hover {
      background-color: #218838;
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
    
    .form-required label:after {
      content: " *";
      color: #e53e3e;
    }
    
    .supplier-info {
      background-color: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      margin-top: 20px;
    }
    
    .new-supplier-toggle {
      color: #4299e1;
      cursor: pointer;
      font-size: 0.9rem;
      margin-top: 5px;
      display: inline-block;
    }
    
    .new-supplier-toggle:hover {
      text-decoration: underline;
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
              <h1>Inventory</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Inventory</li>
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
              <div class="inventory-header">
                <h2 class="inventory-title">Inventory Records</h2>
                <button class="add-item-btn" onclick="openAddModal()">
                  <i class="fas fa-plus"></i> Add Item
                </button>
              </div>
              
              <table id="inventoryTable" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Price (RM)</th>
                    <th>Category</th>
                    <th>Supplier Name</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($inventoryItems as $item): ?>
                  <tr data-code="<?= htmlspecialchars($item['itemCode']) ?>">
                    <td><?= htmlspecialchars($item['itemCode']) ?></td>
                    <td><?= htmlspecialchars($item['itemName']) ?></td>
                    <td><?= $item['itemQuantity'] ?></td>
                    <td>RM <?= number_format($item['itemPrice'], 2) ?></td>
                    <td><?= htmlspecialchars($item['itemCategory']) ?></td>


                    <td><?= htmlspecialchars($item['supplierName']) ?></td>
                    <td>
                      <button class="action-btn edit-btn" onclick="editEntry('<?= $item['itemCode'] ?>')">
                        <i class="fas fa-pencil-alt"></i>
                      </button>

                      <button class="action-btn delete-btn" onclick="deleteEntry('<?= $item['itemCode'] ?>')">
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
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

  <!-- Add Item Modal -->
  <div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New Inventory Item</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="addForm">
            <div class="row">
              <div class="col-md-6">
                <h5>Item Information</h5>
                <div class="form-group">
                  <label>Item Code</label>
                  <div class="form-control" style="background-color: #f8f9fa;" id="displayItemCode"></div>
                </div>
                <div class="form-group form-required">
                  <label for="addItemName">Item Name</label>
                  <input type="text" class="form-control" id="addItemName" required>
                </div>
                <div class="form-group form-required">
                  <label for="addQuantity">Quantity</label>
                  <input type="number" class="form-control" id="addQuantity" required>
                </div>
                <div class="form-group form-required">
                  <label for="addPrice">Price (RM)</label>
                  <input type="number" step="0.01" class="form-control" id="addPrice" required>
                </div>
                <div class="form-group form-required">
                  <label for="addCategory">Category</label>
<select class="form-control" id="addCategory" name="itemCategory" required>
  <option value="">-- Select Category --</option>
  <?php foreach ($categories as $category): ?>
    <option value="<?= htmlspecialchars($category['itemCategory']) ?>">
      <?= htmlspecialchars($category['itemCategory']) ?>
    </option>
  <?php endforeach; ?>
</select>


                </div>
              </div>
              
              <div class="col-md-6">
                <h5>Supplier Information</h5>
                <div class="form-group">
                  <label for="existingSupplier">Supplier</label>
                  <select class="form-control" id="existingSupplier">
                    <option value="">-- Select Supplier --</option>
                    <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= $supplier['supplierID'] ?>"><?= htmlspecialchars($supplier['supplierName']) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <span class="new-supplier-toggle" onclick="toggleNewSupplier()">+ Add New Supplier</span>
                </div>
                
                <div class="supplier-info" id="newSupplierInfo" style="display: none;">
                  <div class="form-group form-required">
                    <label for="supplierName">Supplier Name</label>
                    <input type="text" class="form-control" id="supplierName">
                  </div>
                  <div class="form-group">
                    <label for="supplierPhone">Phone</label>
                    <input type="tel" class="form-control" id="supplierPhone">
                  </div>
                  <div class="form-group">
                    <label for="supplierEmail">Email</label>
                    <input type="email" class="form-control" id="supplierEmail">
                  </div>
                  <div class="form-group">
                    <label for="supplierPICName">PIC Name</label>
                    <input type="text" class="form-control" id="supplierPICName">
                  </div>
                  <div class="form-group">
                    <label for="supplierPICPhone">PIC Phone</label>
                    <input type="tel" class="form-control" id="supplierPICPhone">
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success" id="addItemBtn">
            <i class="fas fa-plus mr-1"></i> Add Item
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
          <h5 class="modal-title">Edit Inventory Item</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="editForm">
            <div class="row">
              <div class="col-md-6">
                <h5>Item Information</h5>
                <div class="form-group">
                  <label>Item Code</label>
                  <div class="form-control" style="background-color: #f8f9fa;" id="editItemCode"></div>
                </div>
                <div class="form-group form-required">
                  <label for="editItemName">Item Name</label>
                  <input type="text" class="form-control" id="editItemName" required>
                </div>
                <div class="form-group form-required">
                  <label for="editQuantity">Quantity</label>
                  <input type="number" class="form-control" id="editQuantity" required>
                </div>
                <div class="form-group form-required">
                  <label for="editPrice">Price (RM)</label>
                  <input type="number" step="0.01" class="form-control" id="editPrice" required>
                </div>
                <div class="form-group form-required">
                  <label for="editCategory">Category</label>
                  <select class="form-control" id="editCategory" required>
<?php foreach ($categories as $category): ?>
  <option value="<?= htmlspecialchars($category['itemCategory']) ?>">
    <?= htmlspecialchars($category['itemCategory']) ?>
  </option>
<?php endforeach; ?>


                  </select>
                </div>
              </div>
              
              <div class="col-md-6">
                <h5>Supplier Information</h5>
                <div class="form-group">
                  <label for="editSupplier">Supplier Name</label>
                  <select class="form-control" id="editSupplier">
                    <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= $supplier['supplierID'] ?>"><?= htmlspecialchars($supplier['supplierName']) ?></option>
                    <?php endforeach; ?>
                  </select>
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
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#inventoryTable').DataTable({
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

    // Function to toggle new supplier form
    function toggleNewSupplier() {
        const newSupplierInfo = $('#newSupplierInfo');
        const existingSupplier = $('#existingSupplier');
        
        if (newSupplierInfo.is(':visible')) {
            newSupplierInfo.hide();
            existingSupplier.val('').trigger('change');
        } else {
            newSupplierInfo.show();
            existingSupplier.val('');
            // Clear any existing supplier info
            $('#supplierName').val('');
            $('#supplierPhone').val('');
            $('#supplierEmail').val('');
            $('#picName').val('');
            $('#picPhone').val('');
        }
    }

    // Function to get next item code from server
    function getNextItemCode() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: window.location.href,
                type: 'POST',
                data: {
                    ajax: true,
                    action: 'get_next_code'
                },
                success: function(response) {
                    if (response.success) {
                        resolve(response.itemCode);
                    } else {
                        reject('Failed to get next item code');
                    }
                },
                error: function() {
                    reject('Error communicating with server');
                }
            });
        });
    }

    // Function to add new supplier
    function addNewSupplier(supplierData) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: window.location.href,
                type: 'POST',
                data: {
                    ajax: true,
                    action: 'add_supplier',
                    supplier_name: supplierData.name,
                    phone: supplierData.phone,
                    email: supplierData.email,
                    pic_name: supplierData.picName,
                    pic_phone: supplierData.picPhone
                },
                success: function(response) {
                    if (response.success) {
                        resolve({
                            id: response.supplier_id,
                            name: response.supplier_name
                        });
                    } else {
                        reject(response.message || 'Failed to add supplier');
                    }
                },
                error: function() {
                    reject('Error communicating with server');
                }
            });
        });
    }

    // Function to add inventory item
    function addInventoryItem(itemData) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: window.location.href,
                type: 'POST',
                data: {
                    ajax: true,
                    action: 'add_item',
                    itemCode: itemData.itemCode,
                    itemName: itemData.itemName,
                    itemQuantity: itemData.itemQuantity,
                    itemPrice: itemData.itemPrice,
                    itemCategory: itemData.itemCategory,
                    itemSupplierID: itemData.supplierId
                },
                success: function(response) {
                    if (response.success) {
                        resolve();
                    } else {
                        reject(response.message || 'Failed to add item');
                    }
                },
                error: function() {
                    reject('Error communicating with server');
                }
            });
        });
    }

    // Function to update inventory item
    function updateInventoryItem(itemCode, itemData) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: window.location.href,
                type: 'POST',
                data: {
                    ajax: true,
                    action: 'update_item',
                    itemCode: itemCode,
                    itemName: itemData.itemName,
                    itemQuantity: itemData.itemQuantity,
                    itemPrice: itemData.itemPrice,
                    itemCategory: itemData.itemCategory,
                    itemSupplierID: itemData.supplierId
                },
                success: function(response) {
                    if (response.success) {
                        resolve();
                    } else {
                        reject(response.message || 'Failed to update item');
                    }
                },
                error: function() {
                    reject('Error communicating with server');
                }
            });
        });
    }

    // Function to delete inventory item
    function deleteInventoryItem(itemCode) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: window.location.href,
                type: 'POST',
                data: {
                    ajax: true,
                    action: 'delete_item',
                    itemCode: itemCode
                },
                success: function(response) {
                    if (response.success) {
                        resolve();
                    } else {
                        reject(response.message || 'Failed to delete item');
                    }
                },
                error: function() {
                    reject('Error communicating with server');
                }
            });
        });
    }

    // Open add modal
    async function openAddModal() {
        try {
            const nextCode = await getNextItemCode();
            $('#displayItemCode').text(nextCode);
            $('#newSupplierInfo').hide();
            $('#addModal').modal('show');
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error,
                timer: 2000
            });
        }
    }

      // Edit entry
      function editEntry(itemCode) {
          $.ajax({
              url: window.location.href,
              type: 'POST',
              data: {
                  ajax: true,
                  action: 'get_item_data',
                  itemCode: itemCode
              },
              success: function(response) {
                  if (response.success) {
                      const item = response.data;
                      
                      // Populate the edit form
                      $('#editItemCode').text(item.itemCode);
                      $('#editItemName').val(item.itemName);
                      $('#editQuantity').val(item.itemQuantity);
                      $('#editPrice').val(item.itemPrice);
                      $('#editCategory').val(item.itemCategory);
                      
                      // Set the supplier
                      $('#editSupplier').val(item.itemSupplierID);
                      
                      // Show the modal
                      $('#editModal').modal('show');
                  } else {
                      Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: response.message || 'Failed to load item data',
                          timer: 2000
                      });
                  }
              },
              error: function() {
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: 'Error communicating with server',
                      timer: 2000
                  });
              }
          });
      }

      // Delete entry
      function deleteEntry(itemCode) {
          Swal.fire({
              title: 'Delete Inventory Item?',
              html: `Are you sure you want to delete <strong>${itemCode}</strong>?`,
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#3085d6',
              confirmButtonText: 'Yes, delete it!',
              cancelButtonText: 'Cancel'
          }).then(async (result) => {
              if (result.isConfirmed) {
                  try {
                      await deleteInventoryItem(itemCode);
                      
                      // Remove the row from DataTable
                      const rowNode = $(`#inventoryTable tbody tr[data-code="${itemCode}"]`);
                      if (rowNode.length) {
                          table.row(rowNode).remove().draw();
                      }
                      
                      Swal.fire(
                          'Deleted!',
                          'The inventory item has been deleted.',
                          'success'
                      );
                  } catch (error) {
                      Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: error,
                          timer: 2000
                      });
                  }
              }
          });
      }

      // Save changes
      $('#saveChangesBtn').click(async function() {
          if ($('#editForm')[0].checkValidity()) {
              const itemCode = $('#editItemCode').text();
              
              const itemData = {
                  itemName: $('#editItemName').val(),
                  itemQuantity: parseInt($('#editQuantity').val()),
                  itemPrice: parseFloat($('#editPrice').val()),
                  itemCategory: $('#editCategory').val(),
                  supplierId: $('#editSupplier').val()
              };

              try {
                  await updateInventoryItem(itemCode, itemData);
                  
                  // Update the DataTable row
                  const rowNode = $(`#inventoryTable tbody tr[data-code="${itemCode}"]`);
                  const row = table.row(rowNode);
                  const rowData = row.data();
                  
                  // Update the row data
                  rowData[1] = itemData.itemName;
                  rowData[2] = itemData.itemQuantity;
                  rowData[3] = 'RM ' + itemData.itemPrice.toFixed(2);
                  rowData[4] = $('#editCategory option:selected').text();
                  rowData[5] = $('#editSupplier option:selected').text();
                  
                  row.data(rowData).draw();
                  
                  Swal.fire({
                      icon: 'success',
                      title: 'Changes Saved',
                      text: 'The inventory item has been updated successfully',
                      timer: 2000,
                      showConfirmButton: false
                  });
                  
                  $('#editModal').modal('hide');
              } catch (error) {
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: error,
                      timer: 2000
                  });
              }
          } else {
              $('#editForm')[0].reportValidity();
          }
      });

      // Add item
      $('#addItemBtn').click(async function() {
          if ($('#addForm')[0].checkValidity()) {
              const itemCode = $('#displayItemCode').text();
              let supplierId = $('#existingSupplier').val();
              
              // Check if we're using an existing supplier or adding a new one
              if (!supplierId) {
                  // New supplier - validate required fields
                  if (!$('#supplierName').val()) {
                      Swal.fire({
                          icon: 'error',
                          title: 'Supplier Required',
                          text: 'Please select an existing supplier or enter new supplier information',
                          timer: 2000
                      });
                      return;
                  }
                  
                  // Create new supplier
                  try {
                      const supplierData = {
                          name: $('#supplierName').val(),
                          phone: $('#supplierPhone').val(),
                          email: $('#supplierEmail').val(),
                          picName: $('#supplierPICName').val(),
                          picPhone: $('#supplierPICPhone').val()
                      };
                      
                      const newSupplier = await addNewSupplier(supplierData);
                      supplierId = newSupplier.id;
                  } catch (error) {
                      Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: error,
                          timer: 2000
                      });
                      return;
                  }
              }

              // Create the new inventory item
              const itemData = {
                  itemCode: itemCode,
                  itemName: $('#addItemName').val(),
                  itemQuantity: parseInt($('#addQuantity').val()),
                  itemPrice: parseFloat($('#addPrice').val()),
                  itemCategory: $('#addCategory').val(),
                  supplierId: supplierId
              };

              try {
                  await addInventoryItem(itemData);
                  
                  // Add to DataTable
                  const supplierName = $('#existingSupplier option:selected').text() || $('#supplierName').val();
                  const categoryName = $('#addCategory option:selected').text();
                  
                  table.row.add([
                      itemCode,
                      itemData.itemName,
                      itemData.itemQuantity,
                      'RM ' + itemData.itemPrice.toFixed(2),
                      categoryName,
                      supplierName,
                      `
                          <button class="action-btn edit-btn" onclick="editEntry('${itemCode}')">
                              <i class="fas fa-pencil-alt"></i>
                          </button>
                          <button class="action-btn delete-btn" onclick="deleteEntry('${itemCode}')">
                              <i class="fas fa-trash"></i>
                          </button>
                      `
                  ]).draw();
                  
                  Swal.fire({
                      icon: 'success',
                      title: 'Item Added',
                      text: 'The new inventory item has been added successfully',
                      timer: 2000,
                      showConfirmButton: false
                  });
                  
                  // Reset form and close modal
                  $('#addForm')[0].reset();
                  $('#newSupplierInfo').hide();
                  $('#addModal').modal('hide');
              } catch (error) {
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: error,
                      timer: 2000
                  });
              }
          } else {
              $('#addForm')[0].reportValidity();
          }
      });

      // Make functions available globally
      window.toggleNewSupplier = toggleNewSupplier;
      window.openAddModal = openAddModal;
      window.editEntry = editEntry;
      window.deleteEntry = deleteEntry;
  });
  </script>
</body>
</html>
