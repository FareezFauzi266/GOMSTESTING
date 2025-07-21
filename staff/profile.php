<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['userID'])) {
    header("Location: /GOMS/index.php");
    exit;
}

$userID = $_SESSION['userID'];

include("../connection/db2.php");
include("../header&footer/settings.php");

$currentPage = 'profile';

// Now we can query the database
$query = "SELECT userID, userName, userRole, userEmail FROM users WHERE userID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<p class='text-danger'>User not found.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>
        <?php browsertitle(); ?>
    </title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"/>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css"/>
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="../app/plugins/overlayScrollbars/css/OverlayScrollbars.min.css"/>
    <!-- Theme style -->
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css" />
    <style>
      .profile-container {
        max-width: 600px;
        margin: 0 auto;
        background-color: #fdfdfd;
        padding: 25px 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      }

      .profile-header {
        font-size: 1.6rem;
        font-weight: 700;
        color: #343a40;
        margin-bottom: 20px;
      }

      .profile-item {
        padding: 10px 0;
        border-bottom: 1px solid #e4e4e4;
      }

      .profile-item:last-child {
        border-bottom: none;
      }

      .profile-item .label {
        display: block;
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
      }

      .profile-item .value {
        color: #6c757d;
      }

      .edit-btn-container {
        text-align: right;
        margin-bottom: 20px;
      }

      .edit-btn-container .btn {
        font-weight: 500;
      }

      @media (max-width: 576px) {
        .edit-btn-container {
          text-align: center;
        }

        .profile-header {
          text-align: center;
        }
      }

      .list-group-item-action.active {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
      }
      .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
      }
      .border-bottom {
      border-bottom: 1px solid #dee2e6 !important;
    }
      .modal .form-group label {
        font-weight: 600;
        color: #343a40;
      }
      .modal .form-control {
        font-size: 0.95rem;
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
                <h1>Staff Profile</h1>
                </div>
                <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Profile</li>
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
                <!-- Profile box -->
                <div class="card">
                    <div class="card-header">
                    <h3 class="card-title">Staff Details</h3>
                    </div>
                    <div class="card-body">
                     <div class="row">
                      <!-- Left Sidebar -->
                      <div class="col-md-4">
                        <div class="list-group mb-3">
                          <a id="overviewBtn" class="list-group-item list-group-item-action active">Profile Overview</a>
                          <a id="passwordBtn" class="list-group-item list-group-item-action">Change Password</a>
                        </div>
                      </div>

                      <!-- Right Container -->
                      <div class="col-md-8">

                        <!-- Profile Overview Section -->
                        <div id="profileOverviewSection" class="card shadow-sm">
                          <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Staff Info</h5>
                            <button class="btn btn-primary btn-sm" onclick="openEditProfileModal()">
                              <i class="fas fa-edit mr-1"></i> Edit Profile
                            </button>
                          </div>
                          <div class="card-body">
                            <div class="row py-2 border-bottom">
                              <div class="col-4 font-weight-bold">User ID</div>
                              <div class="col-8" id="displayUserID"><?= htmlspecialchars($user['userID']) ?></div>
                            </div>
                            <div class="row py-2 border-bottom">
                              <div class="col-4 font-weight-bold">Username</div>
                              <div class="col-8" id="displayUserName"><?= htmlspecialchars($user['userName']) ?></div>
                            </div>
                            <div class="row py-2 border-bottom">
                              <div class="col-4 font-weight-bold">Role</div>
                              <div class="col-8" id="displayUserRole"><?= htmlspecialchars($user['userRole']) ?></div>
                            </div>
                            <div class="row py-2 border-bottom">
                              <div class="col-4 font-weight-bold">Email</div>
                              <div class="col-8" id="displayUserEmail"><?= htmlspecialchars($user['userEmail']) ?></div>
                            </div>
                          </div>
                        </div>

                        <!-- Change Password Section (hidden by default) -->
                        <div id="changePasswordSection" class="card shadow-sm d-none">
                          <div class="card-header">
                            <h5 class="mb-0">Change Password</h5>
                          </div>
                          <div class="card-body">
                            <form id="changePasswordForm">
                              <div class="form-group">
                                <label for="currentPassword">Current password</label>
                                <input type="password" class="form-control" id="currentPassword" required />
                              </div>
                              <div class="form-group">
                                <label for="newPassword">New password</label>
                                <input type="password" class="form-control" id="newPassword" required />
                              </div>
                              <div class="form-group">
                                <label for="confirmPassword">Verify password</label>
                                <input type="password" class="form-control" id="confirmPassword" required />
                              </div>
                              <button type="submit" class="btn btn-primary">Save changes</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer"></div>
                    <!-- /.card-footer-->
                </div>
                <!-- /.card -->
              </div>
            </div>
            </div>
        </section>
        <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
         
        <!-- Edit Profile Modal -->
        <div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <form id="editProfileForm">
                <div class="modal-header">
                  <h5 class="modal-title" id="editProfileLabel">Edit Profile</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                  </button>
                </div>

                <div class="modal-body">
                  <div class="form-group">
                    <label>User ID</label>
                    <input type="text" class="form-control" id="inputUserID" value="<?= htmlspecialchars($user['userID']) ?>" readonly />
                  </div>
                  <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" id="inputUserName" value="<?= htmlspecialchars($user['userName']) ?>" required />
                  </div>
                  <div class="form-group">
                    <label>Role</label>
                    <input type="text" class="form-control" id="inputUserRole" value="<?= htmlspecialchars($user['userRole']) ?>" readonly />
                  </div>
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" id="inputUserEmail" value="<?= htmlspecialchars($user['userEmail']) ?>" required />
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-success" id="saveProfileBtn">Save Changes</button>
                </div>
              </form>
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

    <!-- jQuery -->
    <script src="../app/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="../app/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../app/dist/js/adminlte.min.js"></script>
    <script>
      document.getElementById("overviewBtn").addEventListener("click", function () {
      document.getElementById("profileOverviewSection").classList.remove("d-none");
      document.getElementById("changePasswordSection").classList.add("d-none");
      this.classList.add("active");
      document.getElementById("passwordBtn").classList.remove("active");
    });

    document.getElementById("passwordBtn").addEventListener("click", function () {
      document.getElementById("profileOverviewSection").classList.add("d-none");
      document.getElementById("changePasswordSection").classList.remove("d-none");
      this.classList.add("active");
      document.getElementById("overviewBtn").classList.remove("active");
    });


    // Open modal and populate fields
    function openEditProfileModal() {
      $('#inputUserID').val($('#displayUserID').text());
      $('#inputUserName').val($('#displayUserName').text());
      $('#inputUserRole').val($('#displayUserRole').text());
      $('#inputUserEmail').val($('#displayUserEmail').text());
      $('#inputUserPassword').val('password123'); // Placeholder, not actual password

      $('#editProfileModal').modal('show');
    }

    // Save changes and submit via AJAX
    $('#saveProfileBtn').click(function () {
      const form = $('#editProfileForm')[0];
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      const user_id = $('#inputUserID').val();
      const username = $('#inputUserName').val();
      const email = $('#inputUserEmail').val();

      $.ajax({
        url: 'update_profile.php',
        type: 'POST',
        dataType: 'json',
        data: {
          user_id: user_id,
          username: username,
          email: email
        },
        success: function (response) {
          if (response.success) {
            // Only update the displayed info if the server update was successful
            $('#displayUserName').text(username);
            $('#displayUserEmail').text(email);
            $('#displayUserPassword').text('********'); // Just in case you have a password display

            $('#editProfileModal').modal('hide');

            Swal.fire({
              icon: 'success',
              title: 'Profile Updated',
              text: 'Your changes have been saved.',
              timer: 2000,
              showConfirmButton: false
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Update Failed',
              text: response.message || 'Something went wrong. Please try again.',
            });
          }
        },
        error: function (xhr, status, error) {
          console.error('AJAX Error:', xhr.responseText);
          Swal.fire({
            icon: 'error',
            title: 'Request Error',
            text: 'Failed to send update request.',
          });
        }
      });
    });

  //change password

// Simulated; in production, this is validated server-side

  function openChangePasswordModal() {
    $('#changePasswordModal').modal('show');
    $('#changePasswordForm')[0].reset();
  }

$('#changePasswordForm').submit(function (e) {
  e.preventDefault();

  const current = $('#currentPassword').val().trim();
  const newPass = $('#newPassword').val().trim();
  const confirm = $('#confirmPassword').val().trim();

  if (newPass !== confirm) {
    Swal.fire({
      icon: 'error',
      title: 'Mismatch',
      text: 'New and confirm password do not match.'
    });
    return;
  }

  $.ajax({
    url: 'change_password.php',
    type: 'POST',
    dataType: 'json',
    data: {
      current: current,
      new: newPass
    },
    success: function (res) {
      if (res.success) {
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: res.message,
          timer: 2000,
          showConfirmButton: false
        });
        $('#changePasswordForm')[0].reset();
        $('#passwordBtn').removeClass('active');
        $('#overviewBtn').addClass('active');
        $('#profileOverviewSection').removeClass('d-none');
        $('#changePasswordSection').addClass('d-none');
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: res.message
        });
      }
    },
    error: function () {
      Swal.fire({
        icon: 'error',
        title: 'Request Failed',
        text: 'Failed to communicate with server.'
      });
    }
  });
});

</script>
  </body>
</html>
