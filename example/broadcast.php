<?php

include_once('Nicolive.php');

$id = "foo@example.com";
$password = "bar";

$nico = new Nicolive($id, $password);
$nico->Login($id, $password);

$lv = $nico->getLiveLV();
$xml = $nico->getFMEProfile($lv);

$stream = "";
if(preg_match("#<url>(.*?)</url>#s", $xml, $match)){
	$stream =  $match[1];  
}

passthru("ffmpeg  -f alsa -ac 1 -i pulse -acodec nellymoser  -f x11grab -s 1366x768  -i :0.0+1366,0 -r 10 -vcodec libx264 -crf 26 -keyint_min 0  -bufsize 600k  -r 3 -pix_fmt yuv420p -ar 44100 -b:a 96k -b:v 300k -f flv \"$stream/$lv flashVer=FMLE/3.0\20(compatible;\20FMSc/1.0) swfUrl=$stream/$lv\"\n");


?>
