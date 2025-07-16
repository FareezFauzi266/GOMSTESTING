<?php
session_start();
include("../header&footer/settings.php");
//include(function/function.php);
$currentPage = 'inventory';

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
                    <th>No.</th>
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
                  <!-- Data -->
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
                    <div class="form-control" style="background-color: #f8f9fa;" id="displayItemCode">Loading...</div>
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
                  <select class="form-control" id="addCategory" required>
                    <option value="">-- Select Category --</option>
                    <option value="Supplements">Supplements</option>
                    <option value="Equipment">Equipment</option>
                    <option value="Apparel">Apparel</option>
                    <option value="Accessories">Accessories</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
              </div>
              
              <div class="col-md-6">
                <h5>Supplier Information</h5>
                <div class="form-group">
                  <label for="existingSupplier">Existing Supplier</label>
                  <select class="form-control" id="existingSupplier" onchange="fillSupplierInfo()">
                    <option value="">-- Select Supplier --</option>
                    <option value="1">Supplement King Sdn Bhd</option>
                    <option value="2">Gym Gear Malaysia</option>
                    <option value="3">Fitness Apparel Co.</option>
                    <option value="4">New Supplier (fill below)</option>
                  </select>
                </div>
                
                <div class="supplier-info">
                  <div class="form-group">
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
                    <label for="picName">PIC Name</label>
                    <input type="text" class="form-control" id="picName">
                  </div>
                  <div class="form-group">
                    <label for="picPhone">PIC Phone</label>
                    <input type="tel" class="form-control" id="picPhone">
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
                  <div class="form-control" style="background-color: #f8f9fa;" id="editItemCode">ITM001</div>
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
                    <option value="Supplements">Supplements</option>
                    <option value="Equipment">Equipment</option>
                    <option value="Apparel">Apparel</option>
                    <option value="Accessories">Accessories</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
              </div>
              
              <div class="col-md-6">
                <h5>Supplier Information</h5>
                <div class="form-group">
                  <label for="editSupplier">Supplier Name</label>
                  <select class="form-control" id="editSupplier">
                    <option value="1">Supplement King Sdn Bhd</option>
                    <option value="2">Gym Gear Malaysia</option>
                    <option value="3">Fitness Apparel Co.</option>
                    <option value="4">Other Supplier</option>
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

<!-- Add this JavaScript code to your inventory.php file -->

<script>
    $(document).ready(function() {
        // Initialize DataTable with empty data
        var table = $('#inventoryTable').DataTable({
            "data": [], // Start with empty data
            "columns": [
                { 
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { "data": "itemCode" },
                { "data": "itemName" },
                { "data": "quantity" },
                { 
                    "data": "price",
                    "render": function(data) {
                        return 'RM ' + data.toFixed(2);
                    }
                },
                { "data": "category" },
                { "data": "supplierName" },
                {
                    "data": null,
                    "render": function(data) {
                        return `
                            <button class="action-btn edit-btn" onclick="editEntry('${data.itemCode}')">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button class="action-btn delete-btn" onclick="deleteEntry('${data.itemCode}')">
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

        // Load initial data
        loadInitialData(table);

        // Save changes 
        $('#saveChangesBtn').click(function() {
            if ($('#editForm')[0].checkValidity()) {
                const itemCode = $('#editItemCode').text();
                const updatedItem = {
                    itemCode: itemCode,
                    itemName: $('#editItemName').val(),
                    quantity: parseInt($('#editQuantity').val()),
                    price: parseFloat($('#editPrice').val()),
                    category: $('#editCategory').val(),
                    supplierName: $('#editSupplier option:selected').text()
                };

                // Update the DataTable
                const data = table.data().toArray();
                const index = data.findIndex(item => item.itemCode === itemCode);
                if (index !== -1) {
                    table.row(index).data(updatedItem).draw();
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Changes Saved',
                    text: 'The inventory item has been updated successfully',
                    timer: 2000,
                    showConfirmButton: false
                });
                $('#editModal').modal('hide');
            } else {
                $('#editForm')[0].reportValidity();
            }
        });

        // Add item
        $('#addItemBtn').click(function() {
            if ($('#addForm')[0].checkValidity()) {
                // Generate a new item code (in a real app, this would come from the server)
                const newItemCode = 'ITM' + String(table.data().length + 1).padStart(3, '0');
                
                const newItem = {
                    itemCode: newItemCode,
                    itemName: $('#addItemName').val(),
                    quantity: parseInt($('#addQuantity').val()),
                    price: parseFloat($('#addPrice').val()),
                    category: $('#addCategory').val(),
                    supplierName: $('#existingSupplier').val() === "4" ? 
                        $('#supplierName').val() : $('#existingSupplier option:selected').text()
                };

                // Add to DataTable
                table.row.add(newItem).draw();

                Swal.fire({
                    icon: 'success',
                    title: 'Item Added',
                    text: 'The new inventory item has been added successfully',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Reset form
                $('#addForm')[0].reset();
                $('#addModal').modal('hide');
            } else {
                $('#addForm')[0].reportValidity();
            }
        });
    });

    function loadInitialData(table) {
        // Sample initial data
        const initialData = [
            { 
                itemCode: "ITM001", 
                itemName: "Whey Protein 5lbs", 
                quantity: 25, 
                price: 199.99, 
                category: "Supplements",
                supplierName: "Supplement King Sdn Bhd"
            },
            { 
                itemCode: "ITM002", 
                itemName: "Yoga Mat", 
                quantity: 15, 
                price: 59.90, 
                category: "Equipment",
                supplierName: "Gym Gear Malaysia"
            },
            { 
                itemCode: "ITM003", 
                itemName: "Training Gloves", 
                quantity: 30, 
                price: 45.00, 
                category: "Accessories",
                supplierName: "Fitness Apparel Co."
            },
            { 
                itemCode: "ITM004", 
                itemName: "Yoga Mat", 
                quantity: 15, 
                price: 59.90, 
                category: "Equipment",
                supplierName: "Gym Gear Malaysia"
            },
            { 
                itemCode: "ITM005", 
                itemName: "Training Gloves", 
                quantity: 30, 
                price: 45.00, 
                category: "Accessories",
                supplierName: "Fitness Apparel Co."
            }
        ];
        
        // Add all initial data
        table.rows.add(initialData).draw();
    }

    function openAddModal() {
        $('#addModal').modal('show');
    }

    function fillSupplierInfo() {
        // This would be populated from database in a real app
        var supplierId = $('#existingSupplier').val();
        if (supplierId === "1") {
            $('#supplierName').val("Supplement King Sdn Bhd");
            $('#supplierPhone').val("03-12345678");
            $('#supplierEmail').val("info@supplementking.com");
            $('#picName').val("Ali bin Ahmad");
            $('#picPhone').val("012-3456789");
        } else if (supplierId === "2") {
            $('#supplierName').val("Gym Gear Malaysia");
            $('#supplierPhone').val("03-98765432");
            $('#supplierEmail').val("sales@gymgear.com");
            $('#picName').val("Siti binti Mohd");
            $('#picPhone').val("019-8765432");
        } else if (supplierId === "3") {
            $('#supplierName').val("Fitness Apparel Co.");
            $('#supplierPhone').val("03-55556666");
            $('#supplierEmail').val("contact@fitnessapparel.com");
            $('#picName').val("Raj Kumar");
            $('#picPhone').val("011-22334455");
        } else if (supplierId === "4") {
            // Clear fields for new supplier
            $('#supplierName').val("");
            $('#supplierPhone').val("");
            $('#supplierEmail').val("");
            $('#picName').val("");
            $('#picPhone').val("");
        }
    }

    function editEntry(itemCode) {
        var table = $('#inventoryTable').DataTable();
        var data = table.data().toArray();
        var entry = data.find(item => item.itemCode === itemCode);
        
        if (entry) {
            $('#editItemCode').text(entry.itemCode);
            $('#editItemName').val(entry.itemName);
            $('#editQuantity').val(entry.quantity);
            $('#editPrice').val(entry.price);
            $('#editCategory').val(entry.category);
            $('#editSupplier').val(entry.supplierName === "Supplement King Sdn Bhd" ? "1" : 
                                  entry.supplierName === "Gym Gear Malaysia" ? "2" : 
                                  entry.supplierName === "Fitness Apparel Co." ? "3" : "4");
            
            $('#editModal').modal('show');
        }
    }

    function deleteEntry(itemCode) {
        var table = $('#inventoryTable').DataTable();
        var data = table.data().toArray();
        var item = data.find(item => item.itemCode === itemCode);
        
        if (!item) return;
        
        Swal.fire({
            title: 'Delete Inventory Item?',
            html: `Are you sure you want to delete <strong>${item.itemName}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Find and remove the item
                const index = data.findIndex(i => i.itemCode === itemCode);
                if (index !== -1) {
                    table.row(index).remove().draw();
                }
                
                Swal.fire(
                    'Deleted!',
                    'The inventory item has been deleted.',
                    'success'
                );
            }
        });
    }

    function generateItemCode() {
        const table = $('#inventoryTable').DataTable();
        const data = table.data().toArray();
        
        // If no items exist, start with ITM001
        if (data.length === 0) return 'ITM001';
        
        // Get all numeric parts of item codes
        const codes = data.map(item => {
            const match = item.itemCode.match(/\d+$/); // Get numbers at end
            return match ? parseInt(match[0]) : 0;
        });
        
        // Find highest number and increment
        const maxCode = Math.max(...codes);
        return 'ITM' + String(maxCode + 1).padStart(3, '0');
    }

    function openAddModal() {
        // Generate and display the next item code
        const nextCode = generateItemCode();
        $('#displayItemCode').text(nextCode);
        
        // Clear other form fields
        $('#addForm')[0].reset();
        
        // Show the modal
        $('#addModal').modal('show');
    }

    // Update the add item function
    $('#addItemBtn').click(function() {
        if ($('#addForm')[0].checkValidity()) {
            const table = $('#inventoryTable').DataTable();
            const newItemCode = $('#displayItemCode').text(); // Get the generated code
            
            const newItem = {
                itemCode: newItemCode,
                itemName: $('#addItemName').val(),
                quantity: parseInt($('#addQuantity').val()),
                price: parseFloat($('#addPrice').val()),
                category: $('#addCategory').val(),
                supplierName: $('#existingSupplier').val() === "4" ? 
                    $('#supplierName').val() : $('#existingSupplier option:selected').text()
            };

            // Add to DataTable
            table.row.add(newItem).draw();

            Swal.fire({
                icon: 'success',
                title: 'Item Added',
                text: 'The new inventory item has been added successfully',
                timer: 2000,
                showConfirmButton: false
            });
            
            // Reset form and close modal
            $('#addForm')[0].reset();
            $('#addModal').modal('hide');
        } else {
            $('#addForm')[0].reportValidity();
        }
    });
</script>
</body>
</html>