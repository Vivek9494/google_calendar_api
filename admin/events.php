<?php
session_start();
include "../connection.php";

$active_menu = 'events';
if(empty($_SESSION['user'])){
    header('location:../index.php');
}

if(isset($_POST['share_btn'])){
    $event_id = mysqli_real_escape_string($conn,$_POST['modal_event_id']);
    $user_id = mysqli_real_escape_string($conn,$_POST['user_id']);

    $event_shared_sql = "SELECT * FROM user_event_mapping WHERE user_id='$user_id' AND event_id='$event_id'";
    $event_shared_result = mysqli_query($conn,$event_shared_sql);
    $rows = mysqli_num_rows($event_shared_result);

    if(empty($rows)){
        $sharer_id = $_SESSION['user']['id'];
        $insert_sql = "INSERT INTO user_event_mapping (sharer_id,user_id,event_id) VALUES ('$sharer_id','$user_id','$event_id')";
        $insert_query = mysqli_query($conn,$insert_sql);

        $_SESSION['user']['share_user_id'] = $user_id;
        $_SESSION['user']['last_inserted_event_id'] = $event_id;
        
        header('Location:../quickstart.php');
    }else{
        echo '<script> alert("Event is already shared with you.")</script>';
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
                                <h1>Events</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Events</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-2">
                                    <a href="add_event.php"><button type="button" class="btn btn-block btn-primary">Add Event</button></a>
                                </div>
                                <div class="col-md-2">
                                    <a href="events_calendar.php"><button type="button" class="btn btn-block btn-primary">Calendar View</button></a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <table id="event_tbl" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:40%;">Event</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $user_id = $_SESSION['user']['id'];
                                    $sql = "SELECT * FROM events WHERE user_id='$user_id' ORDER BY start_date DESC";
                                    $query = mysqli_query($conn,$sql);
                                    while ($row = mysqli_fetch_assoc($query)) { ?>
                                        <tr>
                                            <td><?php echo $row["event_name"]; ?></td>
                                            <td><?php echo date('m/d/Y',strtotime($row["start_date"])); ?></td>
                                            <td><?php echo date('m/d/Y',strtotime($row["end_date"])); ?></td>
                                            <td>
                                                <?php if($row['status'] == 1){
                                                    echo '<span class="right badge badge-success">Active</span>';
                                                }else{
                                                    echo '<span class="right badge badge-danger">Inactive</span>';
                                                } ?>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" id="<?php echo $row['id'];?>" class="btn btn-app share_event" data-toggle="modal" data-target="#shareModal" data-event_name="<?php echo $row['event_name'];?>">
                                                    <i class="fas fa-envelope"></i> Share
                                                </a>
                                                <?php if($row['status'] == 0){ ?>
                                                    <a href="event_status.php?event_id=<?php echo $row['id']; ?>&action=1" id="1" name="active_event" class="btn btn-app status_action">
                                                        <i class="fas fa-toggle-on"></i> Active
                                                    </a>
                                                <?php } else { ?>
                                                    <a href="event_status.php?event_id=<?php echo $row['id']; ?>&action=0" id="0" name="inactive_event" class="btn btn-app status_action">
                                                        <i class="fas fa-toggle-off"></i> Inactive
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>                        
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </section>
                <!-- /.content -->
                <div class="modal fade" id="shareModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Share Event</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="events.php" method="post" id="share_event_frm" name="share_event_frm">
                                <div class="modal-body">
                                    <h3 class="card-title" id="event_name_modal"></h3>
                                    <input type="hidden" id="modal_event_id" name="modal_event_id" value="" />
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>User List</label>
                                                <select class="form-control" name="user_id" id="user_id">
                                                    <option value="">Select User</option>
                                                    <?php 
                                                        $users_sql = "SELECT * FROM users";
                                                        $result = mysqli_query($conn,$users_sql);
                                                        while($row = mysqli_fetch_array($result)){
                                                            echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <input type="submit" class="btn btn-primary" id="share_btn" name="share_btn" value="Share">
                                </div>
                            </form>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /.modal -->
            </div>
            <!-- /.content-wrapper -->

            <?php include('../admin/footer.php');?>
        </div>
        <!-- ./wrapper -->
        <?php include('../js.php');?>

        <script>
            $(function () {
                $("#event_tbl").DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "pageLength": 10,
                    "searching": false,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                });

                $('#shareModal').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget);
                    var id = $(button).attr('id');
                    var event_name = $(button).data('event_name');
                    $('#modal_event_id').val(id);
                    $('#event_name_modal').text(event_name);
                });
            });
        </script>
    </body>
</html>