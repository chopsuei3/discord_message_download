<?php


function call_url($url) {
	$authkey = '[ENTER YOUR USER OAUTH2 KEY HERE]';

	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	#  CURLOPT_ENCODING => "",
	#  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	#  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_HTTPHEADER => array(
	    "authorization: $authkey"),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	$cleanResponse = json_decode($response, true);
	#var_dump($cleanResponse);

	return $cleanResponse;
}


#foreach($cleanResponse as $value) {
#var_dump($value['id']);
#}

$messageArray = [];

$channelid = '257348386603139073';
$discordMsgsUrl = "https://discordapp.com/api/channels/$channelid/messages?limit=100";

$response = call_url($discordMsgsUrl);
$i = 0;

while(!empty($response)) {
	foreach($response as $message) {
		array_push($messageArray, $message['id']);
		$pokemon_text = explode(" ",$message['embeds'][0]['title']);
		$pokemon_description = explode("\n",$message['embeds'][0]['description']);
		preg_match('/(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)/', $message['embeds'][0]['url'], $pokemon_location_url);		
		$pokemon_time = date('n/j/Y H:i:s',strtotime($message['timestamp']));
		$pokemon = $pokemon_text[1];
		$pokemon_iv = $pokemon_text[0];
		$pokemon_coords = $pokemon_location_url[0];
		$pokemon_address = $pokemon_description[1];
		$pokemon_moves = $pokemon_description[2];
		$pokemon_rating = $pokemon_description[3];

		$csv_data = "\"" . $pokemon_time . "\",\"" . $pokemon . "\",\"" . $pokemon_iv . "\",\"" . $pokemon_rating . "\",\"" . $pokemon_moves . "\",\"" . $pokemon_coords . "\",\"" . $pokemon_address . "\"\n";
		print $csv_data;
		file_put_contents('sanjose_export.csv',$csv_data,FILE_APPEND);
	}

	$nextId = end($messageArray);
	$nextUrl = "https://discordapp.com/api/channels/$channelid/messages?limit=100&before=$nextId";
	
	sleep(1);	
	$response = call_url($nextUrl);
#	print $i++ . " - " . $nextUrl . " | ";	
}


#	$nextResponse = call_url($nextUrl);

#	while(!empty($nextResponse)) {
#		foreach($nextResponse as $message) {
#			array_push($messageArray, $message['id']);
#		}	
#	}
#}
#else {
#	print "Response empty";
#}

#var_dump($messageArray);

?>
