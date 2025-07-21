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
            <div class="info">
                <a href="#" class="d-block">Manager</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link <?php if($currentPage == 'dashboard') echo 'active'; ?>">
                        <img src="../images/dashboard.png" alt="Dashboard Icon" class="nav-icon" />
                        <p>Dashboard</p>
                    </a>
                </li>
                
                <!-- Inventory -->
                <li class="nav-item">
                    <a href="inventory.php" class="nav-link <?php if($currentPage == 'inventory') echo 'active'; ?>">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Inventory</p>
                    </a>
                </li>
                
                <!-- Maintenance -->
                <li class="nav-item">
                    <a href="maintenance.php" class="nav-link <?php if($currentPage == 'maintenance') echo 'active'; ?>">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Maintenance</p>
                    </a>
                </li>
                
                <!-- Finance with submenu -->
                <li class="nav-item <?php if($currentPage == 'logpayment' || $currentPage == 'ledger') echo 'menu-open'; ?>">
                    <a href="#" class="nav-link <?php if($currentPage == 'finance' || $currentPage == 'logpayment' || $currentPage == 'ledger') echo 'active'; ?>">
                        <i class="nav-icon fas fa-dollar-sign"></i>
                        <p>
                            Finance
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="logpayment.php" class="nav-link <?php if($currentPage == 'logpayment') echo 'active'; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Log Payment</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="financialLedger.php" class="nav-link <?php if($currentPage == 'ledger') echo 'active'; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ledger</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Staff Management -->
                <li class="nav-item">
                    <a href="../manager/staff.php" class="nav-link <?php if($currentPage == 'staff') echo 'active'; ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Staff Management</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>