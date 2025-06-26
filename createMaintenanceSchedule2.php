<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Maintenance Log System</title>
  <!-- AdminLTE CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <style>
    .main-container {
      max-width: 1200px;
      margin: 20px auto;
      padding: 20px;
      background: #f8f9fa;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .form-section {
      background: white;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 5px;
      box-shadow: 0 0 5px rgba(0,0,0,0.05);
    }
    .log-btn {
      background: #28a745;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 3px;
      cursor: pointer;
    }
    .view-btn {
      background: #17a2b8;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 3px;
      cursor: pointer;
    }
    .action-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      border: none;
      cursor: pointer;
      margin: 0 3px;
    }
    .edit-btn {
      background: #ffc107;
      color: white;
    }
    .delete-btn {
      background: #dc3545;
      color: white;
    }
    .frequency-cell {
      white-space: nowrap;
    }
    .custom-modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.4);
    }
    .custom-modal-content {
      background-color: #fefefe;
      margin: 15% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 50%;
      border-radius: 5px;
    }
    .custom-close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }
    .custom-close:hover {
      color: black;
    }
  </style>
</head>
<body>
  <div class="main-container">
    <h1>Maintenance Log System</h1>
    
    <div class="form-section">
      <h2>Create Maintenance Table</h2>
      <div class="form-group">
        <label>Routine Name:</label>
        <input type="text" id="routineName" class="form-control">
      </div>
      <div class="form-group">
        <label>Description:</label>
        <textarea id="routineDesc" class="form-control" rows="3"></textarea>
      </div>
      <div class="form-group">
        <label>Created By:</label>
        <input type="text" id="createdBy" class="form-control">
      </div>
      <button class="btn btn-primary" onclick="createRoutine()">Create Routine</button>
    </div>
    
    <div class="form-section" id="itemSection" style="display:none">
      <h2>
        Maintenance Items
        <button class="btn btn-success btn-sm float-right" data-toggle="modal" data-target="#addItemModal">
          <i class="fas fa-plus"></i> Add Item
        </button>
      </h2>
    </div>
    
    <div id="tableSection"></div>
  </div>
  
  <!-- Add Item Modal -->
  <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Maintenance Item</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="addItemForm">
          <div class="modal-body">
            <div class="form-group">
              <label>Item Name</label>
              <select class="form-control" id="itemName" required>
                <option value="">Select Equipment</option>
                <option value="Treadmill">Treadmill</option>
                <option value="Elliptical Machine">Elliptical Machine</option>
                <option value="Stationary Bike">Stationary Bike</option>
                <option value="Rowing Machine">Rowing Machine</option>
                <option value="Weight Bench">Weight Bench</option>
                <option value="Dumbbell Set">Dumbbell Set</option>
                <option value="Barbell Set">Barbell Set</option>
                <option value="Leg Press Machine">Leg Press Machine</option>
                <option value="Chest Press Machine">Chest Press Machine</option>
                <option value="Lat Pulldown Machine">Lat Pulldown Machine</option>
                <option value="Smith Machine">Smith Machine</option>
                <option value="Multi-station Gym">Multi-station Gym</option>
                <option value="Kettlebell Set">Kettlebell Set</option>
                <option value="Resistance Bands">Resistance Bands</option>
                <option value="Yoga Mats">Yoga Mats</option>
                <option value="Foam Rollers">Foam Rollers</option>
              </select>
            </div>
            <div class="form-group">
              <label>Frequency</label>
              <select class="form-control" id="itemFreq" required>
                <option value="">Select Frequency</option>
                <option value="1">Daily</option>
                <option value="7">Weekly</option>
                <option value="14">Bi-weekly</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Item</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <!-- Edit Item Modal -->
  <div class="modal fade" id="editItemModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Maintenance Item</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="editItemForm">
          <div class="modal-body">
            <input type="hidden" id="editItemIndex">
            <div class="form-group">
              <label>Item Name</label>
              <select class="form-control" id="editItemName" required>
                <option value="">Select Equipment</option>
                <option value="Treadmill">Treadmill</option>
                <option value="Elliptical Machine">Elliptical Machine</option>
                <option value="Stationary Bike">Stationary Bike</option>
                <option value="Rowing Machine">Rowing Machine</option>
                <option value="Weight Bench">Weight Bench</option>
                <option value="Dumbbell Set">Dumbbell Set</option>
                <option value="Barbell Set">Barbell Set</option>
                <option value="Leg Press Machine">Leg Press Machine</option>
                <option value="Chest Press Machine">Chest Press Machine</option>
                <option value="Lat Pulldown Machine">Lat Pulldown Machine</option>
                <option value="Smith Machine">Smith Machine</option>
                <option value="Multi-station Gym">Multi-station Gym</option>
                <option value="Kettlebell Set">Kettlebell Set</option>
                <option value="Resistance Bands">Resistance Bands</option>
                <option value="Yoga Mats">Yoga Mats</option>
                <option value="Foam Rollers">Foam Rollers</option>
              </select>
            </div>
            <div class="form-group">
              <label>Frequency</label>
              <select class="form-control" id="editItemFreq" required>
                <option value="">Select Frequency</option>
                <option value="1">Daily</option>
                <option value="7">Weekly</option>
                <option value="14">Bi-weekly</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <!-- Custom Modal for Details -->
  <div class="custom-modal" id="customModal">
    <div class="custom-modal-content">
      <span class="custom-close" onclick="closeCustomModal()">×</span>
      <h3>Maintenance Details</h3>
      <p id="modalText"></p>
    </div>
  </div>

  <!-- JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    let routine = null;
    let items = [];
    let logs = {}; // { 'itemName_date': { technician, notes } }

    const dateHeaders = ['13/2', '14/2', '15/2', '16/2', '17/2', '18/2', '19/2', '20/2', '21/2'];

    function createRoutine() {
      const name = document.getElementById('routineName').value.trim();
      const desc = document.getElementById('routineDesc').value.trim();
      const creator = document.getElementById('createdBy').value.trim();
      const date = new Date().toLocaleDateString();

      if (!name || !desc || !creator) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Please fill all fields!',
        });
        return;
      }

      routine = { name, desc, creator, date };
      document.getElementById('itemSection').style.display = 'block';
      
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Routine created successfully.',
        showConfirmButton: false,
        timer: 1500
      });
    }

    // Handle add item form submission
    document.getElementById("addItemForm").addEventListener("submit", function(e) {
      e.preventDefault();
      addItem();
    });

    // Handle edit item form submission
    document.getElementById("editItemForm").addEventListener("submit", function(e) {
      e.preventDefault();
      saveEditedItem();
    });

    function addItem() {
      const name = document.getElementById('itemName').value;
      const freq = parseInt(document.getElementById('itemFreq').value);

      if (!name || isNaN(freq)) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Please select both item name and frequency!',
        });
        return;
      }

      items.push({ name, freq });
      $('#addItemModal').modal('hide');
      renderTable();
      
      // Reset form
      document.getElementById('itemName').selectedIndex = 0;
      document.getElementById('itemFreq').selectedIndex = 0;
    }

    function editItem(index) {
      const item = items[index];
      document.getElementById('editItemIndex').value = index;
      document.getElementById('editItemName').value = item.name;
      document.getElementById('editItemFreq').value = item.freq;
      $('#editItemModal').modal('show');
    }

    function saveEditedItem() {
      const index = document.getElementById('editItemIndex').value;
      const name = document.getElementById('editItemName').value;
      const freq = parseInt(document.getElementById('editItemFreq').value);

      if (!name || isNaN(freq)) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Please select both item name and frequency!',
        });
        return;
      }

      items[index] = { name, freq };
      $('#editItemModal').modal('hide');
      renderTable();
      
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Item updated successfully.',
        showConfirmButton: false,
        timer: 1500
      });
    }

    function deleteItem(index) {
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
          items.splice(index, 1);
          renderTable();
          Swal.fire(
            'Deleted!',
            'The item has been deleted.',
            'success'
          );
        }
      });
    }

    function renderTable() {
      if (!routine) return;

      let html = `
        <div class="form-section">
          <h2>Maintenance Table: ${routine.name}</h2>
          <p><strong>Description:</strong> ${routine.desc}<br>
          <strong>Created By:</strong> ${routine.creator} on ${routine.date}</p>
          <table class="table">
            <thead>
              <tr>
                <th>Item</th>
                <th>Frequency</th>
                <th>Action</th>
                ${dateHeaders.map(d => `<th>${d}</th>`).join('')}
              </tr>
            </thead>
            <tbody>
      `;

      items.forEach((item, index) => {
        html += `
          <tr>
            <td>${item.name}</td>
            <td class="frequency-cell">Every ${item.freq} days</td>
            <td>
              <button class="action-btn edit-btn" onclick="editItem(${index})" title="Edit">
                <i class="fas fa-pencil-alt"></i>
              </button>
              <button class="action-btn delete-btn" onclick="deleteItem(${index})" title="Delete">
                <i class="fas fa-trash"></i>
              </button>
            </td>
        `;
        
        dateHeaders.forEach(date => {
          const key = `${item.name}_${date}`;
          if (logs[key]) {
            html += `<td><button class="view-btn" onclick="viewDetails('${item.name}', '${date}')">View</button></td>`;
          } else {
            html += `<td><button class="log-btn" onclick="logMaintenance('${item.name}', '${date}', this)">Log</button></td>`;
          }
        });
        html += `</tr>`;
      });

      html += `</tbody></table></div>`;
      document.getElementById('tableSection').innerHTML = html;
    }

    function logMaintenance(itemName, date, btn) {
      const tech = prompt(`Enter technician name for ${itemName} on ${date}:`);
      const notes = prompt(`Enter maintenance notes:`);

      if (!tech || !notes) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Maintenance not logged. Both fields required!',
        });
        return;
      }

      logs[`${itemName}_${date}`] = { technician: tech, notes };
      renderTable();
    }

    function viewDetails(itemName, date) {
      const log = logs[`${itemName}_${date}`];
      document.getElementById('modalText').innerHTML = `
        <strong>Item:</strong> ${itemName}<br>
        <strong>Date:</strong> ${date}<br>
        <strong>Technician:</strong> ${log.technician}<br>
        <strong>Notes:</strong> ${log.notes}
      `;
      document.getElementById('customModal').style.display = 'block';
    }

    function closeCustomModal() {
      document.getElementById('customModal').style.display = 'none';
    }

    window.onclick = function(e) {
      if (e.target == document.getElementById('customModal')) {
        closeCustomModal();
      }
    }
  </script>
</body>
</html>