<?php
session_start();
include "connection.php";
require 'vendor/autoload.php';

/*if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}*/
if(!empty($_SESSION['user']) && !empty($_SESSION['user']['last_inserted_event_id'])){
    
    $client = new Google_Client();
    $client->setAuthConfig('client_secret.json');
    $client->addScope(Google_Service_Calendar::CALENDAR);

    $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
    $client->setHttpClient($guzzleClient);

    $rurl = "http://localhost/kaushalam_event_mngt_task/quickstart.php";
    $client->setRedirectUri($rurl);
    if (!isset($_GET['code'])) {
        $auth_url = $client->createAuthUrl();
        $filtered_url = filter_var($auth_url, FILTER_SANITIZE_URL);
        header('Location:'.$filtered_url);
    } else {
        $client->authenticate($_GET['code']);
        $_SESSION['access_token'] = $client->getAccessToken();
        header('Location:admin/events.php');
    }

    if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        $event_id = $_SESSION['user']['last_inserted_event_id'];
        $event_sql = "SELECT * FROM events WHERE id='$event_id'";
        $query = mysqli_query($conn,$event_sql);
        $event_details = mysqli_fetch_array($query);

        $client->setAccessToken($_SESSION['access_token']);
        $service = new Google_Service_Calendar($client);
        $calendarId = 'primary';
        
        if(!empty($_SESSION['user']['share_user_id'])){
            
            $event = $service->events->get('primary', $event_details['google_calendar_event_id']);

            $user_id = $_SESSION['user']['share_user_id'];
            $user_details_sql = "SELECT * FROM users WHERE id='$user_id'";
            $user_query = mysqli_query($conn,$user_details_sql);
            $user_details = mysqli_fetch_array($user_query);

            $event->setAttendees(array(array('displayName' => $user_details['name'],'email' => $user_details['email'])));
            
            $updatedEvent = $service->events->update('primary', $event->getId(), $event,['sendUpdates' => 'all']);
            $_SESSION['calendar'] = 'edit';
        }else{
            $event = new Google_Service_Calendar_Event([
                'summary' => $event_details['event_name'],
                'start' => ['date' => $event_details['start_date']],
                'end' => ['date' => $event_details['end_date']]
            ]);
            $results = $service->events->insert($calendarId, $event,['sendUpdates' => 'all']);
            $update_event_sql = "UPDATE events SET google_calendar_event_id='$results->id' WHERE id='$event_id'";
            $query = mysqli_query($conn,$update_event_sql);
            $_SESSION['calendar'] = 'add';
        }

        if (!$results) {
            echo 'Something went wrong';
        }
        unset($_SESSION['user']['share_user_id']);
        unset($_SESSION['user']['last_inserted_event_id']);
        unset($_SESSION['user']['event_action']);
        header('Location:admin/events.php');
    }
}else{
    header('Location:index.php');
}
?>