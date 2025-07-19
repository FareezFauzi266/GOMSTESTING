<?php
session_start();
include("../header&footer/settings.php");
include("../connection/connection.php");
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
                  <label for="existingSupplier">Supplier</label>
                  <select class="form-control" id="existingSupplier">
                    <option value="">-- Select Supplier --</option>
                    <!-- Options will be populated by JavaScript -->
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
                  <select class="form-control" id="editSupplier"></select>
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
    // Global array to track all suppliers
    let allSuppliers = [
        { id: "1", name: "Supplement King Sdn Bhd", phone: "03-12345678", email: "info@supplementking.com", picName: "Ali bin Ahmad", picPhone: "012-3456789" },
        { id: "2", name: "Gym Gear Malaysia", phone: "03-98765432", email: "sales@gymgear.com", picName: "Siti binti Mohd", picPhone: "019-8765432" },
        { id: "3", name: "Fitness Apparel Co.", phone: "03-55556666", email: "contact@fitnessapparel.com", picName: "Raj Kumar", picPhone: "011-22334455" },
        { id: "4", name: "Protein Power Sdn Bhd", phone: "03-11112222", email: "sales@proteinpower.com", picName: "Ahmad Farhan", picPhone: "012-9988776" },
        { id: "5", name: "Iron Strong Equipment", phone: "03-22223333", email: "support@ironstrong.com", picName: "Sarah Lim", picPhone: "017-5544332" },
        { id: "6", name: "Healthy Living Nutrition", phone: "03-33334444", email: "info@healthyliving.com", picName: "David Wong", picPhone: "019-8877665" },
        { id: "7", name: "Elite Fitness Supplies", phone: "03-44445555", email: "orders@elitefitness.com", picName: "Nurul Hasanah", picPhone: "011-2233556" },
        { id: "8", name: "Muscle Pro International", phone: "03-55556666", email: "my@musclepro.com", picName: "Jason Tan", picPhone: "016-7788990" },
        { id: "9", name: "Gym Wear Fashion", phone: "03-66667777", email: "sales@gymwearfashion.com", picName: "Emily Chong", picPhone: "013-4455667" },
        { id: "10", name: "Supplement World", phone: "03-77778888", email: "contact@supplementworld.com", picName: "Kevin Raj", picPhone: "018-1122334" },
        { id: "11", name: "Power Lift Equipment", phone: "03-88889999", email: "info@powerlift.com", picName: "Lisa Koh", picPhone: "014-5566778" },
        { id: "12", name: "NutriMax Solutions", phone: "03-99990000", email: "support@nutrimax.com", picName: "Daniel Lee", picPhone: "011-3344556" },
        { id: "13", name: "Titan Fitness Gear", phone: "03-10101010", email: "sales@titanfitness.com", picName: "Amirul Hakim", picPhone: "017-8899001" }
    ];

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

    // Function to generate next item code
    function generateItemCode(table) {
        const data = table.data().toArray();
        if (data.length === 0) return 'ITM001';
        
        const codes = data.map(item => {
            const match = item.itemCode.match(/ITM(\d+)/);
            return match ? parseInt(match[1]) : 0;
        });
        
        const maxCode = Math.max(...codes);
        return 'ITM' + String(maxCode + 1).padStart(3, '0');
    }

    // Function to populate supplier dropdown
    function populateSupplierDropdown() {
        const dropdown = $('#existingSupplier');
        const editDropdown = $('#editSupplier');
        
        // Clear and add default option
        dropdown.empty().append('<option value="">-- Select Supplier --</option>');
        editDropdown.empty();
        
        // Sort suppliers alphabetically by name
        allSuppliers.sort((a, b) => a.name.localeCompare(b.name));
        
        // Add suppliers to both dropdowns
        allSuppliers.forEach(supplier => {
            dropdown.append(`<option value="${supplier.id}">${supplier.name}</option>`);
            editDropdown.append(`<option value="${supplier.id}">${supplier.name}</option>`);
        });
    }

    // Function to fill supplier info when existing supplier is selected
    function fillSupplierInfo() {
        const supplierId = $('#existingSupplier').val();
        const supplier = allSuppliers.find(s => s.id === supplierId);
        
        if (supplier) {
            // Hide new supplier form if showing
            $('#newSupplierInfo').hide();
            
            // Fill the supplier info fields
            $('#supplierName').val(supplier.name);
            $('#supplierPhone').val(supplier.phone);
            $('#supplierEmail').val(supplier.email);
            $('#picName').val(supplier.picName);
            $('#picPhone').val(supplier.picPhone);
        }
    }

    // Function to add new supplier
    function addNewSupplier(supplierData) {
        // Generate new ID (find max existing ID + 1)
        const newId = allSuppliers.length > 0 
            ? String(Math.max(...allSuppliers.map(s => parseInt(s.id))) + 1)
            : "1";
        
        const newSupplier = {
            id: newId,
            name: supplierData.name || 'New Supplier',
            phone: supplierData.phone || '',
            email: supplierData.email || '',
            picName: supplierData.picName || '',
            picPhone: supplierData.picPhone || ''
        };
        
        // Add to global suppliers array
        allSuppliers.push(newSupplier);
        
        // Update dropdowns
        populateSupplierDropdown();
        
        return newSupplier;
    }

    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#inventoryTable').DataTable({
            "data": [],
            "columns": [
                { "data": "itemCode" },
                { "data": "itemName" },
                { "data": "quantity" },
                { 
                    "data": "price",
                    "render": function(data) {
                        return 'RM ' + parseFloat(data).toFixed(2);
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
        populateSupplierDropdown();

        // Clear supplier info when "Select Supplier" is chosen
        $('#existingSupplier').change(function() {
            if (!this.value) {
                $('#supplierName').val('');
                $('#supplierPhone').val('');
                $('#supplierEmail').val('');
                $('#picName').val('');
                $('#picPhone').val('');
            } else {
                fillSupplierInfo();
            }
        });

        // Save changes 
        $('#saveChangesBtn').click(function() {
            if ($('#editForm')[0].checkValidity()) {
                const itemCode = $('#editItemCode').text();
                const supplierId = $('#editSupplier').val();
                const supplier = allSuppliers.find(s => s.id === supplierId);
                
                const updatedItem = {
                    itemCode: itemCode,
                    itemName: $('#editItemName').val(),
                    quantity: parseInt($('#editQuantity').val()),
                    price: parseFloat($('#editPrice').val()),
                    category: $('#editCategory').val(),
                    supplierName: supplier ? supplier.name : 'Unknown Supplier',
                    supplierId: supplierId
                };

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
                const table = $('#inventoryTable').DataTable();
                const newItemCode = $('#displayItemCode').text();
                let supplierName, supplierId = $('#existingSupplier').val();
                
                // Check if we're using an existing supplier or adding a new one
                if (supplierId) {
                    // Existing supplier
                    const supplier = allSuppliers.find(s => s.id === supplierId);
                    supplierName = supplier.name;
                } else {
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
                    
                    // Create new supplier object
                    const newSupplier = {
                        name: $('#supplierName').val(),
                        phone: $('#supplierPhone').val(),
                        email: $('#supplierEmail').val(),
                        picName: $('#picName').val(),
                        picPhone: $('#picPhone').val()
                    };
                    
                    // Add to suppliers array and get the new ID
                    const addedSupplier = addNewSupplier(newSupplier);
                    supplierName = addedSupplier.name;
                    supplierId = addedSupplier.id;
                }

                // Create the new inventory item
                const newItem = {
                    itemCode: newItemCode,
                    itemName: $('#addItemName').val(),
                    quantity: parseInt($('#addQuantity').val()),
                    price: parseFloat($('#addPrice').val()),
                    category: $('#addCategory').val(),
                    supplierName: supplierName,
                    supplierId: supplierId
                };

                // Add to table
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
                $('#newSupplierInfo').hide();
                $('#addModal').modal('hide');
            } else {
                $('#addForm')[0].reportValidity();
            }
        });
    });

    function loadInitialData(table) {
        const initialData = [
        { 
            itemCode: "ITM001", 
            itemName: "Whey Protein 5lbs", 
            quantity: 25, 
            price: 199.99, 
            category: "Supplements",
            supplierName: "Supplement King Sdn Bhd",
            supplierId: "1"
        },
        { 
            itemCode: "ITM002", 
            itemName: "Yoga Mat", 
            quantity: 15, 
            price: 59.90, 
            category: "Equipment",
            supplierName: "Gym Gear Malaysia",
            supplierId: "2"
        },
        { 
            itemCode: "ITM003", 
            itemName: "Training Gloves", 
            quantity: 30, 
            price: 45.00, 
            category: "Accessories",
            supplierName: "Fitness Apparel Co.",
            supplierId: "3"
        },
        { 
            itemCode: "ITM004", 
            itemName: "Mass Gainer 10lbs", 
            quantity: 18, 
            price: 249.99, 
            category: "Supplements",
            supplierName: "Protein Power Sdn Bhd",
            supplierId: "4"
        },
        { 
            itemCode: "ITM005", 
            itemName: "Adjustable Dumbbell Set", 
            quantity: 8, 
            price: 599.00, 
            category: "Equipment",
            supplierName: "Iron Strong Equipment",
            supplierId: "5"
        },
        { 
            itemCode: "ITM006", 
            itemName: "Organic Protein Bars (Box of 12)", 
            quantity: 42, 
            price: 89.90, 
            category: "Supplements",
            supplierName: "Healthy Living Nutrition",
            supplierId: "6"
        },
        { 
            itemCode: "ITM007", 
            itemName: "Resistance Band Set", 
            quantity: 35, 
            price: 65.50, 
            category: "Equipment",
            supplierName: "Elite Fitness Supplies",
            supplierId: "7"
        },
        { 
            itemCode: "ITM008", 
            itemName: "Pre-Workout Powder", 
            quantity: 22, 
            price: 119.50, 
            category: "Supplements",
            supplierName: "Muscle Pro International",
            supplierId: "8"
        },
        { 
            itemCode: "ITM009", 
            itemName: "Compression Tights", 
            quantity: 28, 
            price: 79.90, 
            category: "Apparel",
            supplierName: "Gym Wear Fashion",
            supplierId: "9"
        },
        { 
            itemCode: "ITM010", 
            itemName: "BCAA Powder", 
            quantity: 31, 
            price: 109.00, 
            category: "Supplements",
            supplierName: "Supplement World",
            supplierId: "10"
        },
        { 
            itemCode: "ITM011", 
            itemName: "Olympic Barbell", 
            quantity: 5, 
            price: 399.00, 
            category: "Equipment",
            supplierName: "Power Lift Equipment",
            supplierId: "11"
        },
        { 
            itemCode: "ITM012", 
            itemName: "Multivitamin Pack", 
            quantity: 50, 
            price: 69.90, 
            category: "Supplements",
            supplierName: "NutriMax Solutions",
            supplierId: "12"
        },
        { 
            itemCode: "ITM013", 
            itemName: "Weightlifting Belt", 
            quantity: 14, 
            price: 89.00, 
            category: "Accessories",
            supplierName: "Titan Fitness Gear",
            supplierId: "13"
        },
        { 
            itemCode: "ITM014", 
            itemName: "Kettlebells (20kg)", 
            quantity: 12, 
            price: 129.00, 
            category: "Equipment",
            supplierName: "Iron Strong Equipment",
            supplierId: "5"
        },
        { 
            itemCode: "ITM015", 
            itemName: "Training Tank Top", 
            quantity: 37, 
            price: 49.90, 
            category: "Apparel",
            supplierName: "Gym Wear Fashion",
            supplierId: "9"
        }
    ];
        
        table.rows.add(initialData).draw();
    }

    function openAddModal() {
        const table = $('#inventoryTable').DataTable();
        const nextCode = generateItemCode(table);
        $('#displayItemCode').text(nextCode);
        populateSupplierDropdown();
        $('#newSupplierInfo').hide();
        $('#addModal').modal('show');
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
            $('#editSupplier').val(entry.supplierId);
            
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
</script>
</body>
</html>