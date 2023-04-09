<?php 
include_once("handler.php"); 

// Load cached data if available
$users_cache = file_get_contents(dirname(__FILE__)."/users.json");
if ($users_cache) {
    $users = $users_cache;
} else {
    $users = $app->get_users();
    file_put_contents(dirname(__FILE__)."/users.json", $users);
}

$hash_rate_cache = file_get_contents(dirname(__FILE__)."/hash_rate.json");
if ($hash_rate_cache) {
    $hash_rate = $hash_rate_cache;
} else {
    $hash_rate = $wmp->hash_rate_cli(5000000)->satoshi-$config->comission; // Increased hash rate to 5,000,000 satoshis
    file_put_contents(dirname(__FILE__)."/hash_rate.json", $hash_rate);
}

echo $hash_rate;
echo $users;

file_put_contents(dirname(__FILE__)."/users.json", $users);
file_put_contents(dirname(__FILE__)."/hash_rate.json", $hash_rate);
?>
