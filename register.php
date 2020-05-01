<?php
error_reporting(E_ALL);
session_start();
include "connection.php";

if(!empty($_SESSION['user'])){
    header('location:admin/dashboard.php');
}

if(isset($_POST['btn_register'])) {
    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $password = mysqli_real_escape_string($conn,$_POST['password']);
    $retype_password = mysqli_real_escape_string($conn,$_POST['retype_password']);

    $name_validation = "/^[a-zA-Z ]+$/";
    $email_validation = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9]+(\.[a-z]{2,4})$/";
    
    if(empty($name) || empty($email) || empty($password) || empty($retype_password)){
        $error_messages['empty_fields'] = "Please fill all inputs";
    }else if(!preg_match($name_validation,$name)){
        $error_messages['invalid_name'] = "Please enter valid Name";
    }else if(!preg_match($email_validation,$email)){
        $error_messages['invalid_email'] = "Please enter valid Email";
    }else if($password != $retype_password){
        $error_messages['password_mismatch'] = "Password doesn't match with Retype Password";
    }else{
        
    }
    
    if(empty($error_messages)){
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $query = mysqli_query($conn,$sql);
        $email_exist = mysqli_num_rows($query);
        if(!empty($email_exist)){
            $error_messages['email_exist'] = "User with same email is already exist";
        }else{
            $sql = "INSERT INTO users (name,email,password) VALUES ('$name','$email','$password')";
            $query = mysqli_query($conn,$sql);
            $_SESSION['user']["id"] = mysqli_insert_id($conn);
            $_SESSION['user']["name"] = $name;
            header('Location:admin/dashboard.php');
        }
    }
    
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Event Management | Register</title>
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
                <a href="index.php"><b>Kaushalam - Registration</a>
            </div>
            <!-- /.login-logo -->
            <div class="card">
                <div class="card-body register-card-body">
                    <form action="register.php" method="post" id="register_frm" name="register_frm">
                        <p class="text-danger error" style="margin-bottom:8px;">
                            <?php 
                            if(!empty($error_messages['empty_fields'])){ 
                                echo $error_messages['empty_fields']; 
                            }else if(!empty($error_messages['invalid_name'])){
                                echo $error_messages['invalid_name'];
                            } ?>
                        </p>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Name" id="name" name="name" value="<?php echo (!empty($_POST['name'])) ? $_POST['name'] : '';?>">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <p class="text-danger error" style="margin-bottom:8px;">
                            <?php 
                            if(!empty($error_messages['invalid_email'])){
                                echo $error_messages['invalid_email'];
                            } else if(!empty($error_messages['email_exist'])){
                                echo $error_messages['email_exist'];
                            } ?>
                        </p>
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Email" id="email" name="email" <?php echo (!empty($_POST['email'])) ? $_POST['email'] : '';?>>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        <p class="text-danger error" style="margin-bottom:8px;">
                            <?php 
                            if(!empty($error_messages['password_mismatch'])){
                                echo $error_messages['password_mismatch'];
                            } ?>
                        </p>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" placeholder="Password" id="password" name="password" value="<?php echo (!empty($_POST['password'])) ? $_POST['password'] : '';?>">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" placeholder="Retype password" id="retype_password" name="retype_password">
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
                                <button type="submit" class="btn btn-primary btn-block" name="btn_register" id="btn_register">Register</button>
                            </div>
                            <!-- /.col -->
                        </div>
                    </form>
                    <div class="social-auth-links text-center mb-3">
                        <p>- OR -</p>
                        <a href="index.php" class="btn btn-block btn-primary">
                        I already have a Login credentials
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
