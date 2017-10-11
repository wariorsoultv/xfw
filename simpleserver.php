<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$access_token = "EAAM6SypAsyYBABnNN9yVcQw2BIZCFbOxPz7GGSUMNLYDfbFjZAVU6tz9gkf6vZA3umaVy9x40s4HWajmBXT4uTM1KGMT6nTfB3JzbDYNcOZCa1kaagRh9jhMhyWkXwh7qjBMlQGseoRRxgZCXwQpNwdpq9XZB01uKbrbi9zFElwAZDZD";
$verify_token = "blondiebytes";
$hub_verify_token = null;
if(isset($_REQUEST['hub_mode']) && $_REQUEST['hub_mode']=='subscribe'){
	$challenge = $_REQUEST['hub_challenge'];
	$hub_verify_token = $_REQUEST['hub_verify_token'];
	if($hub_verify_token===$verify_token){
		header('HTTP/1.1 200 OK');
		echo $challenge;
		die;
	}
}
$input = json_decode(file_get_contents('php://input'),true);
$sender = $input['entry'][0]['messaging'][0]['sender']['id'];
$message = isset($input['entry'][0]['messaging'][0]['message']['text'])? $input['entry'][0]['messaging'][0]['message']['text'] : '';
if($message){
        //you can change your logic here to reply what you want
        // if user sends hi then we will reply like this...
        if($message=="hi"){
               $message_to_reply = "Hello !!! How can I help you?";
			   $jsonData = formatText($sender,$message_to_reply);
        }
		else if($message=='slider'){
			$jsonData = getSlider($sender);
		}
        else{
			$message_to_reply = "This is the message to send back to the user";
			$jsonData = formatText($sender,$message_to_reply);
        }
	
	$url = "https://graph.facebook.com/v2.6/me/messages?access_token=".$access_token;
	
	
	$ch = curl_init($url);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$jsonData);
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	$result = curl_exec($ch);  file_put_contents('11.txt',$jsonData);
	curl_close($ch);
						
}
function formatText($sender,$message){
	$jsonData = '{"recipient":{"id":"'.$sender.'"},
	             "message":{"text":"'.$message.'"}
				 }';
	return $jsonData;
}
function getSlider($sender){
	$items = array();
	for($i=0;$i<5;$i++){
		$items[] = array(
			'title'=>"Title ".$i,
			'item_url'=>"http://www.twitter.com/",
			'image_url'=>"http://solarviews.com/raw/earth/earthafr.jpg",
			'subtitle'=>"This is the subtitle",
			'buttons'=>array(
				array(
					'type'=>"web_url",
					'url'=>"http://www.twitter.com",
					'title'=>"Demo Button"
				),
				array(
					'type'=>"postback",
					'payload'=>"Thank you for clicking this button",
					'title'=>"Another Button"
				)
			),
		);
	}
	$itemJson = json_encode($items);
	$output = '{
					"attachment":{
						"type":"template",
						"payload":{
							"template_type":"generic",
							"elements":'
							. $itemJson .
						'}
					}
				}';
	$jsonData = '{"recipient":{"id":"'.$sender.'"},
				 "message":'.$output.'
				 }';
	return $jsonData;
}
?>
