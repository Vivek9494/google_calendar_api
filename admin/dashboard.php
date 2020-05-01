<?php
session_start();
include "../connection.php";

$active_menu = 'dashboard';
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Event Management</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php include('../css.php')?>
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
                                <h1>Dashboard</h1>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Events</h3>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table class="table table-bordered" id="events_table">
                                            <thead>                  
                                                <tr>
                                                    <th style="width:60%">Event</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $user_id = $_SESSION['user']['id'];
                                                $user_event_sql = "SELECT * FROM events WHERE user_id='$user_id' ORDER BY start_date DESC";
                                                $user_event_result = mysqli_query($conn,$user_event_sql);
                                                $user_event_count = mysqli_num_rows($user_event_result);
                                                if(!empty($user_event_count)){
                                                    while($row = mysqli_fetch_assoc($user_event_result)){ ?>
                                                        <tr>
                                                            <td><?php echo $row['event_name']?></td>
                                                            <td><?php echo date('m/d/Y',strtotime($row['start_date']));?></td>
                                                            <td><?php echo date('m/d/Y',strtotime($row['end_date']));?></td>
                                                        </tr>
                                                    <?php }
                                                }else{ ?>
                                                    <tr>
                                                        <td colspan="3">No Events</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Shared Events</h3>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table class="table table-bordered" id="shared_events">
                                            <thead>                  
                                                <tr>
                                                    <th style="width:60%">Event</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php 
                                                $shared_event_sql = "SELECT events.* FROM user_event_mapping
                                                                    JOIN events ON user_event_mapping.event_id = events.id
                                                                    WHERE user_event_mapping.sharer_id='$user_id' OR user_event_mapping.user_id='$user_id' ORDER BY events.start_date DESC";
                                                $shared_event_result = mysqli_query($conn,$shared_event_sql);
                                                $shared_event_count = mysqli_num_rows($shared_event_result);
                                                if(!empty($shared_event_count)){
                                                    while($row = mysqli_fetch_assoc($shared_event_result)){ ?>
                                                        <tr>
                                                            <td><?php echo $row['event_name']?></td>
                                                            <td><?php echo date('m/d/Y',strtotime($row['start_date']));?></td>
                                                            <td><?php echo date('m/d/Y',strtotime($row['end_date']));?></td>
                                                        </tr>
                                                    <?php }
                                                }else{ ?>
                                                    <tr>
                                                        <td colspan="3">No Shared Events</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content-wrapper -->

            <?php include('../admin/footer.php');?>
        </div>
        <!-- ./wrapper -->
        <?php include('../js.php');?>
        <script>
            $(function () {
                $("#events_table,#shared_events").DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "pageLength": 10,
                    "searching": false,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                });
            });
        </script>
    </body>
</html>