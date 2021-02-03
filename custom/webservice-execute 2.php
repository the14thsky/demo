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
    private $server_key = 'AAAAqRjv3Yc:APA91bFjHLIzd7PBPe_ZeJysUBTgf2fIg95N32u39lo8YIhl1AOGqgoOK75zoqMLi-S2axL5rdJ6SFQN_sqcv8dVz2o_do-YU73ys2wHdmE5oN1deb83B_Ws59k9zcGwuUptIo7ClKYR';
    private $API_key = 'AIzaSyCJ1MLmVn7tEp-_oG_qbCWAgY3xa0XC_KU';
    private $accessKey_ios = 'AIzaSyBu5YeejeXoILUc4QiixzWYRJn3VrHYrTs';
    private $accessKey_android = 'AIzaSyDz4YHoTKxnybgHA3bkJxcAxNNATiBadjk';
    public $steven_phone_id = 'lTalWhMrnq8:APA91bFfQ_arX7kbKuQM6E5uuLY9zG2bI1087WHQeUL4cm6cs9jd8n1pDxXpWBP29-zd942tDM6buQYsc8LQ0psZ9Ln7oNmJdWmFfir3mZ0FL_HTyvQ0nnQkhaI1KxdL7UKSMZwwWciX';

    function __construct(){

    }
    function db_connect(){
        $conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME,DB_USER,DB_PASSWORD);
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $conn;
    }
    function selectAllUsers(){
        $query = $this->db_connect()->prepare('SELECT DISTINCT registration_id,operating_system,notification_flag,modified_date,created_date FROM '.$this->appUsersTB.' WHERE notification_flag = 1');
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
    function insert_query($table,$field_cols,$data_rows){
        $fields = '('.implode(',',$field_cols).')';
        $values = '';
        if(is_array($data_rows[0])) {
	        if(count($field_cols)!=count($data_rows[0])) return false;
	        foreach ( $data_rows as $row ) {
		        $row_string = '("' . implode( '","', $row ) . '")';
		        $values .= $row_string . ',';
	        }
	        $values = substr($values,0,-1);
        }elseif(is_array($data_rows)){
	        if(count($field_cols)!=count($data_rows)) return false;
	        $row_string = '("' . implode( '","', $data_rows ) . '")';
	        $values = $row_string;
        }
        $query = 'INSERT INTO '.$table.' '.$fields.' VALUES '.$values;
	    $query = $this->db_connect()->prepare($query);
	    return $query->execute();
    }
    function mysql_cleanup(){
        $query = 'INSERT INTO mobile_users (registration_id,operating_system,notification_flag,modified_date,created_date) SELECT DISTINCT registration_id,operating_system,notification_flag,modified_date,created_date FROM gcm_debug';
        $query = $this->db_connect()->prepare($query);
        return $query->execute();
    }
    function find_data(){
	    $query = 'SELECT DISTINCT registration_id,operating_system,notification_flag,modified_date,created_date FROM gcm_debug';
	    $query = $this->db_connect()->prepare($query);
	    if($query->execute()) {
		    $result = $query->fetchAll( PDO::FETCH_ASSOC );
		    return $result;
	    }
	    return false;
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
            $response = $this->push_notification($this->getAllUserIDs());
            if($response){
                echo 'Response: '.$response;
            }else{
                echo 'failed to push';
            }
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
        $query = $this->db_connect()->prepare("SELECT DISTINCT registration_id FROM ".$this->appUsersTB." WHERE operating_system IN ('ios','android') AND notification_flag = 1 LIMIT 100 OFFSET 0");
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
        $response = $this->sendToFCM($fields);
        echo $response;
	    if($response){
	    	$response = json_decode($response);
	    	$string = $response->multicast_id;
		    return 'Response: '.printf("%.1f",$string);
	    }else{
		    return 'failed to push';
	    }
    }
    private function sendtoFCM($fields){
        $ch = curl_init();
        $headers = array('Content-Type:application/json','Authorization:key='.$this->server_key);
        curl_setopt($ch,CURLOPT_URL,'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($fields));
        $response = curl_exec($ch);
        curl_close($ch);
        if(!$response){
            return 'HTTP Post Request failed: '.curl_error($ch);
        }
        return $response;
    }
}

echo '<h1>WebService</h1>';
$output = new Webservice();
print_r($output->getAllUserIDs());

