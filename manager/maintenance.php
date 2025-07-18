<?php
session_start();
include("../header&footer/settings.php");
include("../db.php");
$currentPage = 'maintenance';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $response = ['success' => false];

    switch ($_POST['action']) {
        case 'create_schedule':
            $stmt = $conn->prepare("INSERT INTO maintenanceschedule (scheduleID, scheduleName, createdBy, scheduleDesc) VALUES (?, ?, ?, ?)");
            $sid = uniqid('SCH');
            $stmt->bind_param("ssis", $sid, $_POST['scheduleName'], $_SESSION['userID'], $_POST['scheduleDesc']);
            $response['success'] = $stmt->execute();
            break;

        case 'add_item':
            $stmt = $conn->prepare("INSERT INTO maintenanceitem (maintainedItemID, scheduleID, itemCode, frequencyDays) VALUES (?, ?, ?, ?)");
            $mid = uniqid('MID');
            $stmt->bind_param("ssii", $mid, $_POST['scheduleID'], $_POST['itemCode'], $_POST['frequencyDays']);
            $response['success'] = $stmt->execute();
            break;

        case 'log_maintenance':
            $stmt = $conn->prepare("INSERT INTO maintenancerecord (recordID, maintainedItemID, userID, maintenanceDate, itemCondition, remarks, attachmentPath) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $rid = uniqid('REC');
            $stmt->bind_param("ssissss", $rid, $_POST['maintainedItemID'], $_SESSION['userID'], $_POST['maintenanceDate'], $_POST['itemCondition'], $_POST['remarks'], $_POST['attachmentPath']);
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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Maintenance</title>
  <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../app/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <?php include("../navbar/managernavbar.php"); ?>
  <?php include("../sidebar/managersidebar.php"); ?>

  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1>Maintenance Management</h1></div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createScheduleModal">Create Schedule</button>

        <div class="card">
          <div class="card-body">
            <table class="table table-bordered">
              <thead>
                <tr><th>Schedule ID</th><th>Name</th><th>Created</th><th>Desc</th><th>Action</th></tr>
              </thead>
              <tbody>
                <?php
                $q = $conn->query("SELECT * FROM maintenanceschedule");
                while ($row = $q->fetch_assoc()): ?>
                  <tr>
                    <td><?= $row['scheduleID'] ?></td>
                    <td><?= $row['scheduleName'] ?></td>
                    <td><?= $row['createdAt'] ?></td>
                    <td><?= $row['scheduleDesc'] ?></td>
                    <td>
                      <button class='btn btn-sm btn-success' onclick="openAddItemModal('<?= $row['scheduleID'] ?>')">Add Item</button>
                      <button class='btn btn-sm btn-info' onclick="openLogModal('<?= $row['scheduleID'] ?>')">View</button>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<!-- Modals -->
<div class="modal fade" id="createScheduleModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form id="scheduleForm">
      <div class="modal-header"><h5>Create Schedule</h5></div>
      <div class="modal-body">
        <input type="hidden" name="action" value="create_schedule">
        <input class="form-control mb-2" name="scheduleName" placeholder="Schedule Name" required>
        <textarea class="form-control" name="scheduleDesc" placeholder="Description"></textarea>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </form>
  </div></div>
</div>

<div class="modal fade" id="addItemModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form id="itemForm">
      <div class="modal-header"><h5>Add Item to Schedule</h5></div>
      <div class="modal-body">
        <input type="hidden" name="action" value="add_item">
        <input type="hidden" name="scheduleID" id="scheduleIDForItem">
        <select class="form-select mb-2" name="itemCode" required>
          <option disabled selected>Select Item</option>
          <?php
          $items = $conn->query("SELECT itemCode, itemName FROM inventoryitem");
          while ($i = $items->fetch_assoc()): ?>
            <option value="<?= $i['itemCode'] ?>"><?= $i['itemName'] ?></option>
          <?php endwhile; ?>
        </select>
        <input class="form-control" name="frequencyDays" type="number" placeholder="Check Frequency (days)" required>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Add</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </form>
  </div></div>
</div>

<script>
function openAddItemModal(scheduleID) {
  document.getElementById('scheduleIDForItem').value = scheduleID;
  var modal = new bootstrap.Modal(document.getElementById('addItemModal'));
  modal.show();
}

function ajaxSubmit(formID) {
  const form = document.getElementById(formID);
  form.onsubmit = e => {
    e.preventDefault();
    const formData = new FormData(form);
    fetch('', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) location.reload();
      else alert('Error saving data.');
    });
  };
}

ajaxSubmit('scheduleForm');
ajaxSubmit('itemForm');
</script>

<script src="../app/plugins/jquery/jquery.min.js"></script>
<script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../app/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
