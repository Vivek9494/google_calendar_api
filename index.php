<?php
session_start();
include "connection.php";

if(!empty($_SESSION['user'])){
    header('location:admin/dashboard.php');
}

if(isset($_POST['btn_login'])) {
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $password = mysqli_real_escape_string($conn,$_POST['password']);

    if(!empty($email) && !empty($password)){
        $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
	    $query = mysqli_query($conn,$sql);
        $row = mysqli_fetch_array($query);

        if(!empty($row)){
            $_SESSION['user']['name'] = $row['name'];
            $_SESSION['user']['id'] = $row['id'];

            header('Location:admin/dashboard.php');
        }else{
           $error_messages['invalid_credentials'] = "Login credentials are invalid";
        }
    }else{
        if(empty($email)){
            $error_messages['email_error'] = "Please enter valid email";
        }
        if(empty($password)){
            $error_messages['password_error'] = "Please enter password";
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Event Management | Log in</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="assets/css/all.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <!-- icheck bootstrap -->
        <link rel="stylesheet" href="assets/css/icheck-bootstrap.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="assets/css/adminlte.min.css">
        <!-- Google Font: Source Sans Pro -->
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
                <a href="index.php"><b>Kaushalam - Login</a>
            </div>
            <!-- /.login-logo -->
            <div class="card">
                <div class="card-body login-card-body">
                    <form action="index.php" id="login_frm" name="login_frm" method="post">
                        <p class="text-danger error" style="margin-bottom:8px;">
                            <?php 
                            if(!empty($error_messages['invalid_credentials'])){ 
                                echo $error_messages['invalid_credentials']; 
                            }else if(!empty($error_messages['email_error'])){
                                echo $error_messages['email_error'];
                            } ?>
                        </p>
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        <p class="text-danger error" style="margin-bottom:8px;">
                            <?php 
                            if(!empty($error_messages['password_error'])){
                                echo $error_messages['password_error'];
                            } ?>
                        </p>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-8">
                           
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" id="btn_login" name="btn_login" class="btn btn-primary btn-block">Log In</button>
                        </div>
                        <!-- /.col -->
                        </div>
                    </form>

                    <div class="social-auth-links text-center mb-3">
                        <p>- OR -</p>
                        <a href="register.php" class="btn btn-block btn-primary">
                        Register a new user
                        </a>
                    </div>
                </div>
                <!-- /.login-card-body -->
            </div>
        </div>
        <!-- /.login-box -->

        <!-- jQuery -->
        <script src="js/jquery.min.js"></script>
        <!-- Bootstrap 4 -->
        <script src="js/bootstrap.bundle.min.js"></script>
        <!-- AdminLTE App -->
        <script src="js/adminlte.min.js"></script>
    </body>
</html>
