<?php
/**
 * Web Service for Mobile notification push
 * User: steven
 * Date: 8/11/2017
 */

$parse_uri = getcwd();
require_once($parse_uri.'\wp-load.php');

class Webservice {
    public $post_id = '1';
    private $appUsersTB = 'gcm_debug';
    private $appUsersNotifyTB = 'gcm_notification_debug';
    private $API_key = 'AIzaSyBKCr7RAp66YQ7gLSWXtHCEs1L-kjSX78I';
    private $accessKey_ios = 'AIzaSyAYoIT39kXUb5ALFpLz0UbSPzy3cTE1yzY';
    private $accessKey_android = 'AIzaSyDz4YHoTKxnybgHA3bkJxcAxNNATiBadjk';

    function __construct(){

    }
    function db_connect(){
        $conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME,DB_USER,DB_PASSWORD);
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $conn;
    }
    function selectAllUsers(){
        $query = $this->db_connect()->prepare('SELECT * FROM '.$this->appUsersTB);
        if($query->execute()){
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
        return false;
    }
    function updateNotified($registration_id){
        $query = $this->db_connect()->prepare('SELECT * FROM '.$this->appUsersNotifyTB.' WHERE registration_id = :registration_id AND post_id = :post_id LIMIT 1');
        $query->bindValue(':post_id',$this->post_id,PDO::PARAM_INT);
        $query->bindValue(':registration_id',$registration_id,PDO::PARAM_STR);
        if($query->execute()){
            if(count($query->fetchAll(PDO::FETCH_NUM)) != 0){
                $now = date('Y-m-d H:i:s',time()+3600*8);
                $query = $this->db_connect()->prepare('UPDATE '.$this->appUsersNotifyTB.' SET flag = 1, modified_date = :now WHERE post_id = :post_id AND registration_id = :registration_id');
                $query->bindValue(':now', $now, PDO::PARAM_STR);
                $query->bindValue(':post_id', $this->post_id, PDO::PARAM_INT);
                $query->bindValue(':registration_id', $registration_id, PDO::PARAM_STR);
                return $query->execute();
            }
        }
        return false;
    }
    function insertNotified($registration_id){
        $now = date('Y-m-d H:i:s',time()+3600*8);
        $query = $this->db_connect()->prepare('INSERT INTO '.$this->appUsersNotifyTB.' (post_id,registration_id,flag,modified_date,created_date) VALUES (:post_id,:registration_id,1,:zero_date,:now)');
        $query->bindValue(':now',$now,PDO::PARAM_STR);
        $query->bindValue(':zero_date','0000-00-00 00:00:00',PDO::PARAM_STR);
        $query->bindValue(':post_id',$this->post_id,PDO::PARAM_INT);
        $query->bindValue(':registration_id',$registration_id,PDO::PARAM_STR);
        return $query->execute();
    }
    function insert_query($rows){
        $col_string = '';
        $val_string = '';
        foreach($rows as $col => $val){
            $col_string .= $col.',';
            $val_string .= '%s,';
        }
    }
    function run_output(){
        $count = 1;
        if($this->selectAllUsers()!=false){
            foreach($this->selectAllUsers() as $row){
                if($this->updateNotified($row['registration_id'])===true){
                    echo '<pre>UPDATE No. '.$count.' - Registration ID: '.$row['registration_id'].'</pre>';
                }elseif($this->insertNotified($row['registration_id'])===true){
                    echo '<pre>INSERT No. '.$count.' - Registration ID: '.$row['registration_id'].'</pre>';
                }else{
                    echo '<pre>Failed to update/insert this ID: '.$row['registration_id'].'</pre>';
                }
                $count++;
            }
//            $response = $this->push_notification($this->getAllUserIDs());
//            if($response){
//                echo 'requested... '.$response;
//            }else{
//                echo 'failed to push';
//            }
        }
    }
    function get_data(){
        $query = "SELECT id,post_author,post_content,post_title,post_name,post_type,guid FROM wp_posts WHERE id = ".$this->post_id." LIMIT 1";
        $query = $this->db_connect()->prepare($query);
        if($query->execute()){
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return array(
                'type' => 'push_notification',
                'id' => $result['id'],
                'post_author' => $result['post_author'],
                'url' => $result['guid'],
                'content' => $result['post_content'],
                'title' => $result['post_title']
            );
        }
        return false;
    }
    function getAllUserIDs(){
        $query = $this->db_connect()->prepare("SELECT registration_id FROM ".$this->appUsersTB." WHERE operating_system IN ('ios','android') AND notification_flag = 1");
        if($query->execute()){
            $result = $query->fetchAll(PDO::FETCH_NUM);
            $registration_ids = array();
            foreach($result as $row){
                $registration_ids[] = $row[0];
            }
            return $registration_ids;
        }
        return false;
    }
    function push_notification($registration_ids){
        $data = $this->get_data();
        if(!$data) return false;

        $headers = array('Authorization: key='.$this->API_key,'Content-Type: application/json');
        $notification = array(
            'title' => 'SPC Notification',
            'body' => 'You have new notification. Please click to read.',
            'icon' => 'myicon',
            'badge' => 1
        );
        $fields = array(
            'registration_ids' => $registration_ids,
            'notification' => $notification,
            'data' => $data,
        );
        $response = $this->sendToGCM($headers,$fields);
        $response = json_decode($response);
        $message = null;
        if($response!=null){
            $message = 'success: '.$response->success.', failure: '.$response->failure;
        }
        return $message;
    }
    private function sendToGCM($headers,$fields) {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,'https://gcm-http.googleapis.com/gcm/send');
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_VERBOSE,true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($fields));
        $response = curl_exec($ch);
        curl_close($ch);
        if(!$response){
            echo 'HTTP Post Request failed: '.curl_error($ch);
            return false;
        }
        return $response;
    }
}

echo 'Hello World<br>';
$output = new Webservice();
$output->run_output();