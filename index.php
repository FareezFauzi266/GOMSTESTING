<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>GOMS</title>
    <link rel="icon" href="#">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        
        .login-logo a {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            text-decoration: none;
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
            background: linear-gradient(90deg, #28a745, #218838);
            border: none;
            transition: all 0.3s ease-in-out;
        }
        
        button[type="submit"]:hover {
            background: linear-gradient(90deg, #1e7e34, #155d27);
            transform: translateY(-2px);
            filter: brightness(1.2);
        }
        
        .accordion-button:not(.collapsed) {
            background-color: #f8f9fa;
            color: #000;
        }
        
        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(0,0,0,.125);
        }
    </style>
</head> 

<body class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="index.php"><b>GOMS</b></a>
        </div> 
        <div class="card card-wider">
            <div class="card-body login-card-body">
                <img src="#" class="card-img-top" alt="GOMS" style="width: 79%; margin-left:10%; margin-right:5%; margin-bottom: 5%;">
                <p class="login-box-msg">Login to start your session</p>

                <div class="accordion" id="accordionLogin">

                    <!-- User Login Accordion -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingAdmin">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdmin" aria-expanded="false" aria-controls="collapseAdmin">
                                Admin Login
                            </button>
                        </h2>
                        <div id="collapseAdmin" class="accordion-collapse collapse" aria-labelledby="headingAdmin" data-bs-parent="#accordionLogin">
                            <div class="accordion-body">
                                <form method="post" action="#"> 
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Admin ID" name="UserID" required>
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
                                        <div class="col-4">
                                            <div class="d-grid gap-2">
                                                <button class="btn text-white rounded-pill shadow-sm py-2 px-4" type="submit" name="login" id="click">Login</button>
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