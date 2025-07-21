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
include("../db.php");
$currentPage = 'maintenance';

// Helper function to generate next formatted ID
function getNextFormattedId($conn, $table, $col, $prefix, $pad = 3) {
    $result = $conn->query("SELECT $col FROM $table WHERE $col LIKE '$prefix%' ORDER BY $col DESC LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        $num = intval(substr($row[$col], strlen($prefix))) + 1;
    } else {
        $num = 1;
    }
    return $prefix . str_pad($num, $pad, '0', STR_PAD_LEFT);
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $response = ['success' => false];

    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    switch ($action) {
        case 'log_maintenance':
            $rid = getNextFormattedId($conn, 'maintenancerecord', 'recordID', 'R');
            $attachmentPath = null;
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../uploads/maintenance/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                $filename = 'ATT_' . $rid . ($ext ? ('.' . strtolower($ext)) : '');
                $targetPath = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
                    $attachmentPath = '/uploads/maintenance/' . $filename;
                }
            } else if (isset($_POST['attachmentPath'])) {
                $attachmentPath = $_POST['attachmentPath'];
            }
            $stmt = $conn->prepare("INSERT INTO maintenancerecord (recordID, maintainedItemID, userID, maintenanceDate, itemCondition, remarks, attachmentPath) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssissss", $rid, $_POST['maintainedItemID'], $_SESSION['userID'], $_POST['maintenanceDate'], $_POST['itemCondition'], $_POST['remarks'], $attachmentPath);
            $response['success'] = $stmt->execute();
            break;
    }
    echo json_encode($response);
    exit;
}
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
    .maintenance-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding: 0 10px;
    }
    .maintenance-title {
      color: #2d3748;
      font-size: 1.8rem;
      font-weight: 600;
    }
    .add-btn {
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
    .add-btn:hover {
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
    .nested-table {
      background: #f8f9fa;
      margin: 0 0 20px 0;
      border-radius: 8px;
      padding: 10px;
    }
    .day-circle {
      display: inline-block;
      margin: 0 4px;
      font-weight: bold;
    }
    .day-circle input[type="checkbox"] {
      display: none;
    }
    .day-circle span {
      background: #e0e0e0;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: inline-block;
      text-align: center;
      line-height: 40px;
      font-size: 1.2em;
      cursor: pointer;
      transition: background 0.2s, color 0.2s;
    }
    .day-circle input[type="checkbox"]:checked + span {
      background: #28a745;
      color: #fff;
    }
    .today-col {
      background: #ffe082 !important;
      font-weight: bold;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <?php include("../navbar/managernavbar.php"); ?>
  <?php include("../sidebar/managersidebar.php"); ?>
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Maintenance Management</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Maintenance</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-body">
            <div class="maintenance-header">
              <h2 class="maintenance-title">Maintenance Schedules</h2>
            </div>
            <table id="scheduleTable" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>Schedule ID</th>
                  <th>Name</th>
                  <th>Created By</th>
                  <th>Created At</th>
                  <th>Description</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <!-- Data loaded via AJAX -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </div>
  <!-- Add Record Modal -->
  <div class="modal fade" id="addRecordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form id="addRecordForm" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title">Log Maintenance Record</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="addRecordMaintainedItemID" name="maintainedItemID">
            <div class="form-group form-required">
              <label for="maintenanceDate">Maintenance Date</label>
              <input type="date" class="form-control" id="maintenanceDate" name="maintenanceDate" required>
            </div>
            <div class="form-group form-required">
              <label for="itemCondition">Item Condition</label>
              <select class="form-control" id="itemCondition" name="itemCondition" required>
                <option value="OK">OK</option>
                <option value="Needs Repair">Needs Repair</option>
                <option value="Replace Soon">Replace Soon</option>
              </select>
            </div>
            <div class="form-group">
              <label for="remarks">Remarks</label>
              <textarea class="form-control" id="remarks" name="remarks"></textarea>
            </div>
            <div class="form-group">
              <label for="attachment">Attachment</label>
              <input type="file" class="form-control" id="attachment" name="attachment" accept="image/*,application/pdf">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Log Record</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Add this after the other modals -->
  <div class="modal fade" id="viewScheduleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewScheduleTitle">Schedule Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="viewScheduleBody">
          <!-- Content loaded by JS -->
        </div>
      </div>
    </div>
  </div>

  <!-- Add this after the other modals -->
  <div class="modal fade" id="pastLogsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Past Maintenance Logs</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="pastLogsBody">
          <!-- Content loaded by JS -->
        </div>
      </div>
    </div>
  </div>

  <!-- Add this after the other modals -->
  <div class="modal fade" id="viewRecordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Maintenance Record Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="viewRecordBody">
          <!-- Content loaded by JS -->
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer">
    <div class="float-right d-none d-sm-block"></div>
    <strong> <?php copyright(); ?> </strong>
  </footer>
</div>

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

<script>
// --- DataTables and AJAX logic ---
let scheduleTable;
let currentMatrixWeekDate = null;
let lastViewedScheduleID = null;

// Global functions that can be called from HTML onclick attributes
function openAddScheduleModal() {
  $('#addScheduleForm')[0].reset();
  $('#addScheduleModal').modal('show');
}

function openAddItemModal(scheduleID) {
  // Close the view modal first
  $('#viewScheduleModal').modal('hide');
  lastViewedScheduleID = scheduleID;
  setTimeout(function() {
    $('#addItemForm')[0].reset();
    $('#addItemScheduleID').val(scheduleID);
    $('#addItemModal').modal('show');
  }, 400); // Wait for modal to close
}

function openAddRecordModal(maintainedItemID, date) {
  $('#viewScheduleModal').modal('hide');
  setTimeout(function() {
    $('#addRecordForm')[0].reset();
    $('#addRecordMaintainedItemID').val(maintainedItemID);
    if (date) $('#maintenanceDate').val(date);
    $('#addRecordModal').modal('show');
  }, 400);
}

function loadSchedules() {
  $.ajax({
    url: 'maintenance_data.php',
    type: 'GET',
    dataType: 'json',
    success: function(data) {
      if (scheduleTable) scheduleTable.destroy();
      let tbody = '';
      data.forEach(function(sch) {
        tbody += `<tr>
          <td>${sch.scheduleID}</td>
          <td>${sch.scheduleName}</td>
          <td>${sch.createdByName}</td>
          <td>${sch.createdAt}</td>
          <td>${sch.scheduleDesc}</td>
          <td>
            <button class="action-btn" onclick="openViewScheduleModal('${sch.scheduleID}')"><i class="fas fa-eye"></i></button>
          </td>
        </tr>`;
      });
      $('#scheduleTable tbody').html(tbody);
      scheduleTable = $('#scheduleTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "pageLength": 10,
        "ordering": false,
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
    }
  });
}

function openViewScheduleModal(scheduleID, weekDate) {
  if (!weekDate) {
    currentMatrixWeekDate = new Date();
  } else {
    currentMatrixWeekDate = new Date(weekDate);
  }
  // Format weekOf as YYYY-MM-DD
  const weekOf = currentMatrixWeekDate.toISOString().slice(0, 10);
  // Find the schedule data from the last AJAX call (or fetch again)
  $.ajax({
    url: 'maintenance_data.php',
    type: 'GET',
    dataType: 'json',
    success: function(data) {
      const sch = data.find(s => s.scheduleID === scheduleID);
      if (!sch) return;
      $('#viewScheduleTitle').text(`Schedule: ${sch.scheduleName}`);
      // Fetch matrix data for selected week
      $.ajax({
        url: 'maintenance_matrix_data.php',
        type: 'GET',
        data: { scheduleID: scheduleID, weekOf: weekOf },
        dataType: 'json',
        success: function(matrixData) {
          let html = `<div><strong>Schedule ID:</strong> ${sch.scheduleID}<br>
            <strong>Created By:</strong> ${sch.createdByName}<br>
            <strong>Created At:</strong> ${sch.createdAt}<br>
            <strong>Description:</strong> ${sch.scheduleDesc || ''}
            <hr>
            <div class='d-flex justify-content-between align-items-center mb-2'>
              <h5>Maintenance Matrix (Week of ${matrixData.weekDates[0]})</h5>
              <div>
                <button class='btn btn-outline-secondary btn-sm mr-2' onclick='changeMatrixWeek(-1, "${scheduleID}")'>&lt; Previous Week</button>
                <button class='btn btn-outline-secondary btn-sm' onclick='changeMatrixWeek(1, "${scheduleID}")'>Next Week &gt;</button>
              </div>
            </div>`;
          // Render matrix table
          html += `<div class='table-responsive'><table class='table table-bordered table-sm mb-2'>`;
          // Table header: days
          const today = new Date();
          const todayStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
          html += `<thead><tr><th>Item Name</th>`;
          matrixData.weekDates.forEach(date => {
            const d = new Date(date);
            const isToday = date === todayStr;
            html += `<th${isToday ? ' class="today-col"' : ''}>${d.toLocaleDateString('en-GB', { weekday: 'short', day: '2-digit', month: '2-digit' })}</th>`;
          });
          html += `</tr></thead><tbody>`;
          // Table body: items x days
          if (matrixData.items.length === 0) {
            html += `<tr><td colspan='8'>No items in this schedule.</td></tr>`;
          } else {
            matrixData.items.forEach(item => {
              html += `<tr><td>${item.itemName}</td>`;
              item.matrix.forEach((cell, idx) => {
                const dayChar = item.daysOfWeek ? item.daysOfWeek[idx] : '-';
                const isToday = matrixData.weekDates[idx] === todayStr;
                let tdClass = isToday ? ' class="today-col"' : '';
                if (dayChar !== '-' && cell.record) {
                  html += `<td${tdClass}><button class='btn btn-info btn-sm' onclick=\"openViewRecordModal('${cell.record.recordID}')\">View</button></td>`;
                } else if (dayChar !== '-' && cell.due) {
                  html += `<td${tdClass}><button class='btn btn-danger btn-sm' onclick=\"openAddRecordModal('${item.maintainedItemID}', '${cell.date}')\">Log</button></td>`;
                } else {
                  html += `<td${tdClass}></td>`;
                }
              });
              html += `</tr>`;
            });
          }
          html += `</tbody></table></div>`;
          html += `</div>`;
          html += `<div class='mt-3'><button class='btn btn-secondary btn-sm' onclick="openPastLogsModal('${sch.scheduleID}')"><i class='fas fa-history'></i> View Past Logs</button></div>`;
          $('#viewScheduleBody').html(html);
          $('#viewScheduleModal').modal('show');
        }
      });
    }
  });
}

function changeMatrixWeek(offset, scheduleID) {
  if (!currentMatrixWeekDate) currentMatrixWeekDate = new Date();
  currentMatrixWeekDate.setDate(currentMatrixWeekDate.getDate() + offset * 7);
  openViewScheduleModal(scheduleID, currentMatrixWeekDate);
}

function openViewRecordModal(recordID) {
  $.ajax({
    url: 'maintenance_record_data.php',
    type: 'GET',
    data: { recordID: recordID },
    dataType: 'json',
    success: function(rec) {
      let html = `<table class='table table-bordered'>`;
      html += `<tr><th>Record ID</th><td>${rec.recordID}</td></tr>`;
      html += `<tr><th>Item Name</th><td>${rec.itemName}</td></tr>`;
      html += `<tr><th>Maintenance Date</th><td>${rec.maintenanceDate}</td></tr>`;
      html += `<tr><th>Item Condition</th><td>${rec.itemCondition}</td></tr>`;
      html += `<tr><th>Remarks</th><td>${rec.remarks || ''}</td></tr>`;
      if (rec.attachmentPath) {
        const ext = rec.attachmentPath.split('.').pop().toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
          html += `<tr><th>Attachment</th><td><img src="${rec.attachmentPath}" class="img-fluid" style="max-width: 300px;"></td></tr>`;
        } else if (ext === 'pdf') {
          html += `<tr><th>Attachment</th><td><embed src="${rec.attachmentPath}" width="100%" height="400px"></td></tr>`;
        } else {
          html += `<tr><th>Attachment</th><td><a href="${rec.attachmentPath}" target="_blank">View Attachment</a></td></tr>`;
        }
      }
      html += `</table>`;
      $('#viewRecordBody').html(html);
      $('#viewRecordModal').modal('show');
    }
  });
}

function openPastLogsModal(scheduleID) {
  $.ajax({
    url: 'maintenance_data.php',
    type: 'GET',
    dataType: 'json',
    success: function(data) {
      const sch = data.find(s => s.scheduleID === scheduleID);
      if (!sch) return;
      let html = `<h6>Past Maintenance Logs for: ${sch.scheduleName}</h6>`;
      if (sch.items && sch.items.length > 0) {
        sch.items.forEach(item => {
          if (item.records && item.records.length > 0) {
            html += `<div class="nested-table"><strong>${item.itemName}</strong><br>`;
            html += `<table class="table table-sm table-bordered">`;
            html += `<thead><tr><th>Date</th><th>Condition</th><th>Remarks</th><th>Action</th></tr></thead><tbody>`;
            item.records.forEach(record => {
              html += `<tr><td>${record.maintenanceDate}</td><td>${record.itemCondition}</td><td>${record.remarks || ''}</td>`;
              html += `<td><button class="btn btn-info btn-sm" onclick="openViewRecordModal('${record.recordID}')">View</button></td></tr>`;
            });
            html += `</tbody></table></div>`;
          }
        });
      } else {
        html += `<p>No past logs found.</p>`;
      }
      $('#pastLogsBody').html(html);
      $('#pastLogsModal').modal('show');
    }
  });
}

// Document ready function for form handlers and event listeners
$(document).ready(function() {
  // Load schedules
  loadSchedules();
  
  // Add Record
  $('#addRecordForm').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('action', 'log_maintenance');
    $.ajax({
      url: '',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function(res) {
        if (res.success) {
          $('#addRecordModal').modal('hide');
          Swal.fire('Success', 'Maintenance record logged!', 'success');
          loadSchedules();
        } else {
          Swal.fire('Error', 'Failed to log record.', 'error');
        }
      }
    });
  });

  // After adding item or record, re-open the view modal for the same schedule
  $('#addItemModal').on('hidden.bs.modal', function () {
    if (lastViewedScheduleID) {
      setTimeout(function() {
        openViewScheduleModal(lastViewedScheduleID);
        lastViewedScheduleID = null;
      }, 400);
    }
  });

  $('#addRecordModal').on('hidden.bs.modal', function () {
    if (lastViewedScheduleID) {
      setTimeout(function() {
        openViewScheduleModal(lastViewedScheduleID);
        lastViewedScheduleID = null;
      }, 400);
    }
  });
});
</script>

</body>
</html>