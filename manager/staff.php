<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect if not manager or not logged in
if (!isset($_SESSION['userID']) || $_SESSION['userRole'] !== 'Manager') {
    header("Location: /GOMS/index.php");
    exit;
}

include("../header&footer/settings.php");
include("../connection/db2.php");
include("../function/function.php");

$currentPage = 'staff';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_staff'])) {
        // Add new staff member
        $userName = $conn->real_escape_string($_POST['userName']);
        $userEmail = $conn->real_escape_string($_POST['userEmail']);
        $password = password_hash($conn->real_escape_string($_POST['password']), PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (userName, userEmail, userPassword, userRole) 
                VALUES ('$userName', '$userEmail', '$password', 'Staff')";
        $conn->query($sql);
    } 
    elseif (isset($_POST['edit_staff'])) {
        // Edit staff member
        $userID = (int)$_POST['userID'];
        $userName = $conn->real_escape_string($_POST['userName']);
        $userEmail = $conn->real_escape_string($_POST['userEmail']);
        
        $sql = "UPDATE users SET userName = '$userName', userEmail = '$userEmail' 
                WHERE userID = $userID AND userRole = 'Staff'";
        $conn->query($sql);
    } 
    elseif (isset($_POST['delete_staff'])) {
        // Delete staff member
        $userID = (int)$_POST['userID'];
        $sql = "DELETE FROM users WHERE userID = $userID AND userRole = 'Staff'";
        $conn->query($sql);
    }
}

// Get all staff members
$staffMembers = $conn->query("SELECT * FROM users WHERE userRole = 'Staff' ORDER BY userName");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php browsertitle(); ?> - Staff Management</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../app/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../app/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../app/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <style>
        .staff-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 0 10px;
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
    
    .staff-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding: 0 10px;
    }
    
    .staff-title {
      color: #2d3748;
      font-size: 1.8rem;
      font-weight: 600;
    }
    
    .add-new-btn {
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
    
    .add-new-btn:hover {
      background-color: #218838;
    }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include("../navbar/managernavbar.php"); ?>
        <!-- /.navbar -->
        
        <!-- Sidebar -->
        <?php include("../sidebar/managersidebar.php"); ?>
        <!-- /.sidebar -->

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Staff Management</h1>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <div class="staff-header">
                                <h2 class="staff-title">Staff Records</h2>

                                    <button class="btn btn-success add-new-btn" data-toggle="modal" data-target="#addModal">
                                        <i class="fas fa-plus"></i> Add New Staff
                                    </button>
                                
                            </div>
                            <table id="staffTable" class="table table-bordered table-hover staff-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Staff ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($staffMembers && $staffMembers->num_rows > 0): ?>
                                        <?php $counter = 1; ?>
                                        <?php while ($staff = $staffMembers->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $counter++ ?></td>
                                                <td><?= htmlspecialchars($staff['userID']) ?></td>
                                                <td><?= htmlspecialchars($staff['userName']) ?></td>
                                                <td><?= htmlspecialchars($staff['userEmail']) ?></td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm edit-btn" 
                                                            data-toggle="modal" 
                                                            data-target="#editModal"
                                                            data-id="<?= $staff['userID'] ?>"
                                                            data-name="<?= htmlspecialchars($staff['userName']) ?>"
                                                            data-email="<?= htmlspecialchars($staff['userEmail']) ?>">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm delete-btn"
                                                            onclick="confirmDelete(<?= $staff['userID'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No staff members found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Add Staff Modal -->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Staff Member</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="addStaffName">Name</label>
                                <input type="text" class="form-control" id="addStaffName" name="userName" required>
                            </div>
                            <div class="form-group">
                                <label for="addStaffEmail">Email</label>
                                <input type="email" class="form-control" id="addStaffEmail" name="userEmail" required>
                            </div>
                            <div class="form-group">
                                <label for="addStaffPassword">Password</label>
                                <input type="password" class="form-control" id="addStaffPassword" name="password" required minlength="8">
                                <small class="form-text text-muted">Minimum 8 characters</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_staff" class="btn btn-success">Add Staff</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Staff Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Staff Member</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="userID" id="editStaffId">
                            <div class="form-group">
                                <label for="editStaffName">Name</label>
                                <input type="text" class="form-control" id="editStaffName" name="userName" required>
                            </div>
                            <div class="form-group">
                                <label for="editStaffEmail">Email</label>
                                <input type="email" class="form-control" id="editStaffEmail" name="userEmail" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" name="edit_staff" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
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
        <!-- SweetAlert2 -->
        <script src="../app/plugins/sweetalert2/sweetalert2.min.js"></script>
        <!-- AdminLTE App -->
        <script src="../app/dist/js/adminlte.min.js"></script>

        <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#staffTable').DataTable({
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

            // Edit modal handler
            $('#editModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var name = button.data('name');
                var email = button.data('email');
                
                var modal = $(this);
                modal.find('#editStaffId').val(id);
                modal.find('#editStaffName').val(name);
                modal.find('#editStaffEmail').val(email);
            });

            // Clear add modal when closed
            $('#addModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
            });
        });

        function confirmDelete(userID) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a form and submit it
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '';
                    
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'userID';
                    input.value = userID;
                    form.appendChild(input);
                    
                    var input2 = document.createElement('input');
                    input2.type = 'hidden';
                    input2.name = 'delete_staff';
                    input2.value = '1';
                    form.appendChild(input2);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        </script>
    </div>
</body>
</html>