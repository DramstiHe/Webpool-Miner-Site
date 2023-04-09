<?
include_once("handler.php");
$hashrate_stats = $app->count_hashrate();
var_dump($hashrate_stats);
file_put_contents(dirname(__FILE__)."/hashrate_stats.json", $hashrate_stats);
