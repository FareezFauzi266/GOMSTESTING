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
                    <!-- User Login Accordion -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingUser">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUser" aria-expanded="false" aria-controls="collapseUser">
                                User Login
                            </button>
                        </h2>
                        <div id="collapseUser" class="accordion-collapse collapse" aria-labelledby="headingUser" data-bs-parent="#accordionLogin">
                            <div class="accordion-body">
                                <form id="loginForm" method="post">
                                    <div class="role-selector">
                                        <div class="btn-group" role="group">
                                            <input type="radio" class="btn-check" name="userRole" id="managerRole" value="manager" autocomplete="off" checked>
                                            <label class="btn btn-outline-primary" for="managerRole">Manager</label>
                                            
                                            <input type="radio" class="btn-check" name="userRole" id="staffRole" value="staff" autocomplete="off">
                                            <label class="btn btn-outline-primary" for="staffRole">Staff</label>
                                        </div>
                                    </div>
                                    
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="User ID" name="UserID" id="userId" required>
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
                                        <div class="col-12">
                                            <div class="login-btn-container">
                                                <button class="btn text-white rounded-pill shadow-sm py-2 px-4" type="submit" name="login" id="loginBtn">Login</button>
                                            </div>
                                        </div> 
                                    </div> 
                                </form>
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

        // Form submission handler
        document.getElementById("loginForm").addEventListener("submit", function(e) {
            e.preventDefault();
            
            const userId = document.getElementById("userId").value;
            const password = document.getElementById("userPassword").value;
            const userRole = document.querySelector('input[name="userRole"]:checked').value;
            
            // Simulate login - in a real app, this would be a server-side check
            Swal.fire({
                title: 'Logging in...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    
                    // Simulate API call delay
                    setTimeout(() => {
                        Swal.close();
                        
                        // Redirect based on role
                        if (userRole === 'manager') {
                            window.location.href = 'manager/dashboard.php';
                        } else {
                            window.location.href = 'staff/dashboard.php';
                        }
                    }, 1500);
                }
            });
        });
    </script>
</body>
</html>