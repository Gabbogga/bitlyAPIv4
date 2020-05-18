<?php

$file='longlinks.txt';

$apiv4url = 'https://api-ssl.bitly.com/v4/bitlinks';
$AccessToken = '';

if(file_exists('bitlypos.txt')) {
	$lastpos=(int)file_get_contents('bitlypos.txt');
}
else $lastpos=0;

$nowpos=0;
$fp=fopen($file,'rb');
$fp2=fopen('shortlinks.txt','ab');
while(!feof($fp)) {
	$longlink=trim(fgets($fp));
	if($nowpos>=$lastpos) {
		if(!empty($longlink)) {
			echo 'long:'.$longlink."\n";
			$data = array(
				'long_url' => $longlink
			);
			$payload = json_encode($data);

			$header = array(
				'Authorization: Bearer ' . $AccessToken,
				'Content-Type: application/json',
				'Content-Length: ' . strlen($payload)
			);

			$ch = curl_init(apiv4url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			$result = curl_exec($ch);


			$shortlink = json_decode($result, true);
			if(!empty($shortlink["link"])) {
				echo 'short:'.$shortlink["link"]."\n";
				fwrite($fp2,$shortlink["link"]."\n");
			}
			else {
				print_r($result);
				die();
			}
		}
	}
	$nowpos++;
	$fp1=fopen('bitlypos.txt','wb');
	fwrite($fp1,$nowpos);
	fclose($fp1);
	break;
}
fclose($fp);
fclose($fp2);
?>
