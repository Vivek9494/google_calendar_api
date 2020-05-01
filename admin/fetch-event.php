<?php
    session_start();
    include "../connection.php";
    $user_id = $_SESSION['user']['id'];
    $user_event_sql = "SELECT * FROM events WHERE user_id='$user_id'";
    $user_event_result = mysqli_query($conn,$user_event_sql);
    $user_event_count = mysqli_num_rows($user_event_result);
    $i=0;
    $event_result = array();
    if(!empty($user_event_count)){
        while($row = mysqli_fetch_assoc($user_event_result)){
            $event_result[$i]['title'] = $row['event_name'];
            $event_result[$i]['start'] = $row['start_date'];
            $event_result[$i]['end'] = $row['end_date'];
            $event_result[$i]['backgroundColor'] = '#f39c12'; //yellow
            $event_result[$i]['borderColor']    = '#f39c12'; //yellow
            $i++;
        }
    }

    $shared_event_sql = "SELECT events.* FROM user_event_mapping
                        JOIN events ON user_event_mapping.event_id = events.id
                        WHERE user_event_mapping.user_id='$user_id'";
    $shared_event_result = mysqli_query($conn,$shared_event_sql);
    $shared_event_count = mysqli_num_rows($shared_event_result);
    if(!empty($shared_event_count)){
        while($row = mysqli_fetch_assoc($shared_event_result)){
            $event_result[$i]['title'] = $row['event_name'];
            $event_result[$i]['start'] = $row['start_date'];
            $event_result[$i]['end'] = $row['end_date'];
            $event_result[$i]['backgroundColor'] = '#0073b7'; //yellow
            $event_result[$i]['borderColor']    = '#0073b7'; //yellow
            $i++;
        }
    }

    echo json_encode($event_result);
?>