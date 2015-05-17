<?php

include_once('Nicolive.php');

$id = "foo@example.com";
$password = "bar";


$nico = new Nicolive($id, $password);
$nico->Login($id, $password);

$lv = $nico->getLiveLV();
$gps = $nico->getPlayerStatus($lv);

$xml = simplexml_load_string($gps);

$url =  $xml->rtmp->url;
$url = str_replace("rtmp:rtmp", "rtmp", $url);
$id = $xml->stream->id;
$ticket = $xml->rtmp->ticket;
$cont = $xml->stream->contents_list->contents;
$cont = str_replace("rtmp:rtmp", "rtmp", $cont);



echo $cmd = "rtmpdump -vr \"$url/$id\" -C S:\"$ticket\" -N \"$cont\" -p \"http://live.nicovideo.jp/watch/$lv\" -o - | vlc -";



passthru($cmd);

?>
