<?php
session_start();
include "../connection.php";

$active_menu = 'events';
if(empty($_SESSION['user'])){
    header('location:../index.php');
}

if(isset($_POST['add_event_btn'])) {
    $event_name = mysqli_real_escape_string($conn,$_POST['event']);
    $start_date = mysqli_real_escape_string($conn,$_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn,$_POST['end_date']);

    $name_validation = "/^[a-zA-Z. ]+$/";

    if(empty($event_name) || empty($start_date) || empty($end_date)){
        $error_messages['empty_fields'] = "Please fill all inputs";
    }else if(!preg_match($name_validation,$event_name)){
        $error_messages['invalid_event_name'] = "Please enter a valid Event Name";
    }else if(!preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/", $start_date)) {
        $error_messages['invalid_start_date'] = "Please enter a date in valid format (MM/DD/YYY)";
    }else if(!preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/", $end_date)) {
        $error_messages['invalid_end_date'] = "Please enter a date in valid format (MM/DD/YYY)";
    }else if(strtotime($start_date) > strtotime($end_date)){
        $error_messages['start_date_less'] = "Start Date should not be greater than End Date";
    }else if(strtotime($end_date) < strtotime($start_date)){
        $error_messages['end_date_less'] = "End Date should not be lesser than Start Date";
    }

    if(empty($error_messages)){
        $start_date = date('Y-m-d',strtotime($start_date));
        $end_date = date('Y-m-d',strtotime($end_date));
        $user_id = $_SESSION['user']['id'];

        $sql = "SELECT * FROM events WHERE user_id='$user_id' AND event_name = '$event_name' AND start_date = '$start_date' AND end_date = '$end_date' AND status = 1";
        
        $query = mysqli_query($conn,$sql);
        $email_exist = mysqli_num_rows($query);
        if(!empty($email_exist)){
            $error_messages['event_exist'] = "Event with same data is already exist";
        }else{
            $sql = "INSERT INTO events (user_id,event_name,start_date,end_date,google_calendar_event_id) VALUES ('$user_id','$event_name','$start_date','$end_date','')";
            $query = mysqli_query($conn,$sql);
            $_SESSION['user']['last_inserted_event_id'] = mysqli_insert_id($conn);
            header('Location:../quickstart.php');
        }
    }else{
        echo 'add';die;
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Event Management | Events</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php include('../css.php')?>
        <style>
            .error{color:red;}
        </style>
    </head>
    <body class="hold-transition sidebar-mini">
        <!-- Site wrapper -->
        <div class="wrapper">
            <?php include('../admin/header.php');?>

            <?php include('../admin/sidebar.php');?>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Add Event</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active"><a href="events.php">Events</a></li>
                                    <li class="breadcrumb-item">Add Event</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                   <!-- general form elements -->
                    <div class="card card-primary">
                        <!-- form start -->
                        <form role="form" name="add_event_frm" id="add_event_frm" action="add_event.php" method="post">
                            <div class="card-body">
                                <p class="text-danger error" style="margin-bottom:8px;">
                                    <?php if(!empty($error_messages['empty_fields'])){
                                        echo $error_messages['empty_fields'];
                                    }else if(!empty($error_messages['event_exist'])){
                                        echo $error_messages['event_exist'];
                                    }?>
                                </p>
                                <div class="form-group">
                                    <label>Event</label>
                                    <input type="text" class="form-control" id="event" name="event" placeholder="Enter Event Name" value="<?php echo (!empty($_POST['event'])) ? $_POST['event'] : '';?>">
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="text" class="form-control" id="start_date" name="start_date" placeholder="Start Date" value="<?php echo (!empty($_POST['start_date'])) ? $POST['start_date'] : '';?>">
                                            <?php if(!empty($error_messages['invalid_start_date'])){
                                                    echo '<p class="text-danger error" style="margin-bottom:8px;">'.$error_messages['invalid_start_date'].'</p>';
                                                }else if(!empty($error_messages['start_date_less'])){
                                                    echo '<p class="text-danger error" style="margin-bottom:8px;">'.$error_messages['start_date_less'].'</p>';
                                                }?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <input type="text" class="form-control" id="end_date" name="end_date" placeholder="End Date" value="<?php echo (!empty($_POST['end_date'])) ? $_POST['end_date'] : '';?>">
                                            <?php if(!empty($error_messages['invalid_end_date'])){
                                                    echo '<p class="text-danger error" style="margin-bottom:8px;">'.$error_messages['invalid_end_date'].'</p>';
                                                }else if(!empty($error_messages['end_date_less'])){
                                                    echo '<p class="text-danger error" style="margin-bottom:8px;">'.$error_messages['end_date_less'].'</p>';
                                                }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary" name="add_event_btn">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                    <!-- /.card -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

            <?php include('../admin/footer.php');?>
        </div>
        <!-- ./wrapper -->
        <?php include('../js.php');?>
        <script type="text/javascript">
        $(document).ready(function () {
            //Date range picker with time picker
            $('#start_date,#end_date').daterangepicker({
                minDate:new Date(),
                singleDatePicker: true,
                minYear: parseInt(moment().format('YYYY'),10),
                maxYear: parseInt(moment().format('YYYY'),10),
                locale: {
                    format: 'MM/DD/YYYY'
                }
            });

            $.validator.addMethod("DateFormat",function(value, element) {
                    // put your own logic here, this is just a (crappy) example
                    return value.match(/^\d\d?\/\d\d?\/\d\d\d\d$/);
                },
                "Please enter a date in the valid format (MM/DD/YYYY)"
            );

            $('#add_event_frm').validate({
                rules: {
                    event: {
                        required: true,
                        maxlength:50,
                        pattern: "^[a-zA-Z'.\\s]{1,40}$"
                    },
                    start_date: {
                        required: true,
                        DateFormat:true                        
                    },
                    end_date: {
                        required: true,
                        DateFormat:true
                    },
                },
                messages: {
                    event: {
                        required: "Please enter a Event Name",
                        maxlength: "Event Name allows only 50 characters",
                        pattern: "Please enter a vaild Event Name"
                    },
                    start_date: {
                        required: "Please enter a Start Date",
                        DateFormat: "Please enter a date in valid format (MM/DD/YYY)"
                    },
                    end_date: {
                        required: "Please enter a End Date",
                        DateFormat: "Please enter a date in valid format (MM/DD/YYY)"
                    },
                }
            });
        });
        </script>
    </body>
</html>