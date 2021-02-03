<?php /* Template Name: Page - Web Services - Pump Price */ ?>
<?php

$parse_uri = explode( 'wp-content', $_SERVER['DOCUMENT_ROOT'] );
require_once( $parse_uri[0] . '\wp-load.php' );

pump_price();

function pump_price() {

    global $wpdb;

    $result = array();
    $counter = 0;
    $result['pump_list'] = array ();

    if( have_rows('pump_price_list', 'options') ):
        while ( have_rows('pump_price_list', 'options') ) : the_row();
                $inner_result = array (
                    'pump_name' => get_sub_field('pump_type'),
                    'pump_price' => get_sub_field('pump_price')
                );

                array_push($result['pump_list'], $inner_result);
        endwhile;
    endif;

    $result['latest_update_date'] = get_field('latest_update', 'options');
    $result['latest_update_time'] = get_field('latest_update_time', 'options');

    // echo '<pre>' . json_encode($result, JSON_PRETTY_PRINT) . '</pre>';
    echo json_encode($result, JSON_PRETTY_PRINT);

}

function sendGoogleCloudMessage($result) {

    define( 'API_ACCESS_KEY', 'AIzaSyBs8r168A67JsGRrXQPL5CO8p5fMwsms3Y' );

    $registrationIds = array('f6KoakBDVKI:APA91bEttGegAtg6Tr8xKEGQGsXowFYKsxOwsnm1VO0d8h1O3ljuR868Lbq-eyHsqz0k8Ha-gMxjP3kz2Nze9t9qlOsADAE5tmLZdA08VpqhSGs6LJL1tE-J4rMAJ3iHmVAv2HuzKcN3');

    $headers = array (
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json'
    );

    $fields = array (
        'registration_ids'  => $registrationIds,
        'data'              => $result
    );


    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    $results = curl_exec($ch );
    curl_close( $ch );

    echo $results;
}

?>