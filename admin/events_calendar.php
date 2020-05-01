<?php
session_start();
include "../connection.php";

$active_menu = 'events';
if(empty($_SESSION['user'])){
    header('location:../index.php');
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
        
        <!-- fullCalendar -->
        <link rel="stylesheet" href="../assets/css/main.min.css">
        <link rel="stylesheet" href="../assets/css/daygrid.main.min.css">
        <link rel="stylesheet" href="../assets/css/timegrid.main.min.css">
        <link rel="stylesheet" href="../assets/css/fullcalendar.main.min.css">
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
                                <h1>Calendar</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active"><a href="events.php">Events</a></li>
                                    <li class="breadcrumb-item">Calendar</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="sticky-top mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <!-- the events -->
                                            <div>
                                                <div class="external-event bg-warning">User Events</div>
                                                <div class="external-event bg-info">Shared Events</div>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-md-9">
                                <div class="card card-primary">
                                <div class="card-body p-0">
                                    <!-- THE CALENDAR -->
                                    <div id="calendar"></div>
                                </div>
                                <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                    </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

            <?php include('../admin/footer.php');?>
        </div>
        <!-- ./wrapper -->
         <!-- jQuery -->
        <script src="../assets/js/jquery.min.js"></script>
        <!-- Bootstrap 4 -->
        <script src="../assets/js/bootstrap.bundle.min.js"></script>
        <!-- jQuery UI -->
        <script src="../assets/js/jquery-ui.min.js"></script>
        <!-- fullCalendar 2.2.5 -->
        <script src="../assets/js/moment.min.js"></script>
        <script src="../assets/js/main.min.js"></script>
        <script src="../assets/js/daygrid.main.min.js"></script>
        <script src="../assets/js/timegrid.main.min.js"></script>
        <script src="../assets/js/interaction.main.min.js"></script>
        <script src="../assets/js/fullcalendar.main.min.js"></script>
        <!-- Page specific script -->
    <script>
        $(function () {
            /* initialize the external events
            -----------------------------------------------------------------*/
            function ini_events(ele) {
                ele.each(function () {

                    // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                    // it doesn't need to have a start or end
                    var eventObject = {
                        title: $.trim($(this).text()) // use the element's text as the event title
                    }

                    // store the Event Object in the DOM element so we can get to it later
                    $(this).data('eventObject', eventObject)

                    // make the event draggable using jQuery UI
                    $(this).draggable({
                        zIndex        : 1070,
                        revert        : true, // will cause the event to go back to its
                        revertDuration: 0  //  original position after the drag
                    })
                })
            }

            ini_events($('#external-events div.external-event'))

            /* initialize the calendar
            -----------------------------------------------------------------*/
            //Date for the calendar events (dummy data)
            var date = new Date()
            var d    = date.getDate(),
            m    = date.getMonth(),
            y    = date.getFullYear()

            var Calendar = FullCalendar.Calendar;
            var Draggable = FullCalendarInteraction.Draggable;

            var checkbox = document.getElementById('drop-remove');
            var calendarEl = document.getElementById('calendar');

            // initialize the external events
            // -----------------------------------------------------------------


            var calendar = new Calendar(calendarEl, {
                plugins: [ 'bootstrap', 'interaction', 'dayGrid', 'timeGrid' ],
                header    : {
                    left  : 'prev,next today',
                    center: 'title',
                    right : 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                'themeSystem': 'bootstrap',
                events    : 'fetch-event.php',
            });
            calendar.render();
            $('#add-new-event').click(function (e) {
                e.preventDefault()
                //Get value and make sure it is not null
                var val = $('#new-event').val()
                if (val.length == 0) {
                    return
                }

                //Create events
                var event = $('<div />')
                event.css({
                    'background-color': currColor,
                    'border-color'    : currColor,
                    'color'           : '#fff'
                }).addClass('external-event')
                event.html(val)
                $('#external-events').prepend(event)

                //Add draggable funtionality
                ini_events(event)

                //Remove event from text input
                $('#new-event').val('')
            })
        })
        </script>
    </body>
</html>