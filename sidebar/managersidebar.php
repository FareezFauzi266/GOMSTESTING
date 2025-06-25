<!-- Main Sidebar Container -->
      <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="../../index3.html" class="brand-link">
          <img
            src="../images/gym2.png"
            alt="GOMS Logo"
            class="brand-image img-circle elevation-3"
            style="opacity: 0.8"
          />
          <span class="brand-text font-weight-light">GOMS</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
          <!-- Sidebar user panel (optional) -->
          <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <!--<div class="image">
              <img
                src="app/dist/img/user2-160x160.jpg"
                class="img-circle elevation-2"
                alt="User Image"
              />
            </div> -->
            <div class="info">
              <a href="#" class="d-block">Manager</a>
            </div>
          </div>

          <!-- Sidebar Menu -->
          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
              <li class="nav-item">
                <a href="dashboard.php" class="nav-link <?php if($currentPage == 'dashboard') echo 'active'; ?>">
                    <img src="../images/dashboard.png" alt="Dashboard Icon" class="nav-icon" />
                    <p>Dashboard</p>
                </a>
                </li>
                <li class="nav-item">
                <a href="inventory.php" class="nav-link <?php if($currentPage == 'inventory') echo 'active'; ?>">
                    <i class="nav-icon fas fa-box"></i>
                    <p>Inventory</p>
                </a>
                </li>
                <li class="nav-item">
                <a href="maintenance.php" class="nav-link <?php if($currentPage == 'maintenance') echo 'active'; ?>">
                    <i class="nav-icon fas fa-cogs"></i>
                    <p>Maintenance</p>
                </a>
                </li>
                <li class="nav-item">
                <a href="finance.php" class="nav-link <?php if($currentPage == 'finance') echo 'active'; ?>">
                    <i class="nav-icon fas fa-dollar-sign"></i>
                    <p>Finance</p>
                </a>
                </li>
            </ul>
          </nav>
          <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->

        <!-- /.sidebar -->
      </aside>