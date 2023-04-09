<?php
  include_once("handler.php");
  $address=$_GET['address'];
  $user=$app->get_user($address);
  if(!$user){
    echo '{"success":true , "balance":0}';
  }else{
    unset($user->api_key);
    unset($user->referral);
    unset($user->updated_at);
    $user->balance=strval(round($user->hashes/1000000*$app->rate, 2));
    $user->success=true;
    echo json_encode($user);
  }
  
?>
