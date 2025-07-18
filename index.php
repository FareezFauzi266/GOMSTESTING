<?php
session_start();
include("db.php"); // Your DB connection file

// If already logged in, redirect
if (isset($_SESSION['userID'])) {
    if ($_SESSION['userRole'] == 'Manager') {
        header("Location: manager/dashboard.php");
        exit;
    } else if ($_SESSION['userRole'] == 'Staff') {
        header("Location: staff/dashboard.php");
        exit;
    }
}

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = trim($_POST['UserID'] ?? '');
    $password = $_POST['userPassword'] ?? '';
    $userRole = strtolower($_POST['userRole'] ?? '');

    if ($userID && $password && ($userRole == 'manager' || $userRole == 'staff')) {
        // Prepare query
        $stmt = $conn->prepare("SELECT userID, userName, userRole, userPassword FROM users WHERE userID = ? AND LOWER(userRole) = ?");
        $stmt->bind_param("is", $userID, $userRole);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if ($password === $user['userPassword']) {
                // Login success: save session
                $_SESSION['userID'] = $user['userID'];
                $_SESSION['userName'] = $user['userName'];
                $_SESSION['userRole'] = ucfirst($user['userRole']); // e.g., Manager or Staff

                // Redirect based on role
                if ($_SESSION['userRole'] == 'Manager') {
                    header("Location: manager/dashboard.php");
                    exit;
                } else {
                    header("Location: staff/dashboard.php");
                    exit;
                }
            } else {
                $login_error = 'Invalid password.';
            }
        } else {
            $login_error = 'User not found or role mismatch.';
        }

        $stmt->close();
    } else {
        $login_error = 'Please fill all fields correctly.';
    }
}
?>

<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>GOMS</title>
    <link rel="icon" href="#">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.2.min.js"></script>
    <style>
        body.login-page {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .login-box {
            width: 360px;
            margin: 0 auto;
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 1.5rem; 
            padding: 0 1rem; 
        }
        
        .login-logo a {
            font-size: 24px;
            font-weight: 400;
            color: #000;
            text-decoration: none;
            display: inline-block; 
            line-height: 1.4; 
        }
        
        .card-wider {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .login-card-body {
            padding: 20px;
        }
        
        .login-box-msg {
            margin: 0;
            padding: 0 0 20px;
            text-align: center;
            font-size: 16px;
            color: #6c757d;
        }
        
        button[type="submit"] {
            background: #007bff;
            border: none;
            transition: all 0.3s ease-in-out;
        }
        
        button[type="submit"]:hover {
            background: #007bff !important;
            opacity: 0.9;
        }
        
        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(0,0,0,.125);
        }
        
        .gym-icon {
            font-size: 4rem;
            color: #007bff;
            margin-bottom: 1rem;
            display: block;
            text-align: center;
        }
        
        .role-selector {
            margin-bottom: 1rem;
        }
        
        .role-selector .btn-group {
            width: 100%;
        }
        
        .role-selector .btn {
            flex: 1;
        }
        
        .login-btn-container {
            display: flex;
            justify-content: flex-end;
        }
    </style>
</head> 

<body class="login-page">

<div class="login-box">
    <div class="login-logo">
        <a>Gym Operational Management System (GOMS)</a>
    </div>
    <div class="card card-wider">
        <div class="card-body login-card-body">
            <i class="bi bi-dumbbell gym-icon"></i>
            <p class="login-box-msg">Login to start your session</p>

            <div class="accordion" id="accordionLogin">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingUser">
                        <button class="accordion-button <?php if (!$login_error) echo 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUser" aria-expanded="<?php echo $login_error ? 'true' : 'false'; ?>" aria-controls="collapseUser">
                            User Login
                        </button>
                    </h2>
                    <div id="collapseUser" class="accordion-collapse collapse <?php if ($login_error) echo 'show'; ?>" aria-labelledby="headingUser" data-bs-parent="#accordionLogin">
                        <div class="accordion-body">
                            <form id="loginForm" method="post" novalidate>
                                <div class="role-selector">
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check" name="userRole" id="managerRole" value="manager" autocomplete="off" <?php if (!isset($_POST['userRole']) || $_POST['userRole'] == 'manager') echo 'checked'; ?>>
                                        <label class="btn btn-outline-primary" for="managerRole">Manager</label>

                                        <input type="radio" class="btn-check" name="userRole" id="staffRole" value="staff" autocomplete="off" <?php if (isset($_POST['userRole']) && $_POST['userRole'] == 'staff') echo 'checked'; ?>>
                                        <label class="btn btn-outline-primary" for="staffRole">Staff</label>
                                    </div>
                                </div>

                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="User ID" name="UserID" id="userId" required value="<?php echo htmlspecialchars($_POST['UserID'] ?? ''); ?>">
                                    <div class="input-group-text">
                                        <span class="bi bi-person"></span>
                                    </div>
                                </div>
                                <div class="input-group mb-3">
                                    <input type="password" class="form-control" placeholder="Password" name="userPassword" id="userPassword" required>
                                    <div class="input-group-text">
                                        <span class="bi bi-eye-slash" id="togglePassword" title="Show Password" style="cursor: pointer;"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 login-btn-container">
                                        <button class="btn text-white rounded-pill shadow-sm py-2 px-4" type="submit" name="login" id="loginBtn">Login</button>
                                    </div>
                                </div>
                            </form>
                            <?php if ($login_error): ?>
                                <script>
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Login Failed',
                                        text: '<?php echo addslashes($login_error); ?>',
                                        timer: 3000,
                                        showConfirmButton: false
                                    });
                                </script>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Password toggle functionality
    document.getElementById("togglePassword").addEventListener("click", function () {
        const passwordField = document.getElementById("userPassword");
        const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
        passwordField.setAttribute("type", type);

        this.classList.toggle("bi-eye");
        this.classList.toggle("bi-eye-slash");
    });
</script>

</body>
</html>