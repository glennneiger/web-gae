<?php
$fp = fopen("gs://image.minyanville.com/FTP/bucketFile.csv", 'w');

$file="Apr0113033502.mp3";
$source="gs://image.minyanville.com/radio/";
$des = "/home/sites/radio/processed/";
$type = "radio";

$fileName = 'bucketFile.csv';

$arr['file'] = $file;
$arr['source'] = $source;
$arr['des'] = $des;
$arr['type'] = $type;

foreach($arr as $line){
    fputcsv($fp, $val,',','"');
}
fclose($fp);
?>