<?php

class Main{
    public $config;
    public $wmp;
    public $ec;

    public $balance;
    public $rate;
    public $faucet_balance;
    public $referral;
    public $admin=0;
    public $users;
    public $hashrate_history;

    public function __construct($config, $wmp, $ec){
        $this->config = $config;
        $this->wmp    = $wmp;
        $this->ec     = $ec;
        $this->users  = $this->load_users_from_file();
        $this->hashrate_stats  = $this->load_hashrate_stats_from_file();

        switch(true){
            case $this->is_admin():
                $this->admin         = true;
                $this->faucet_balance= 50000000;
                $this->rate          = file_get_contents(dirname(__FILE__)."/../hash_rate.json");

                return;
                break;

            case !isset($_COOKIE['btc_address']):
                $this->rate          = file_get_contents(dirname(__FILE__)."/../hash_rate.json");
                return;
                break;

            case isset($_COOKIE['btc_address'])&&!isset($_COOKIE['rate']):
                $this->faucet_balance= $this->ec->getBalance("BTC")['balance'];
                $this->rate          = file_get_contents(dirname(__FILE__)."/../hash_rate.json");
                $user                = $this->get_user($_COOKIE['btc_address']);
                if($user!==false){
                    $this->balance   = $user->hashes; // $balance_request->success==true ? $balance_request->hashes : 0;
                    $this->referral  = $user->referral!=null ? $user->referral : "none";
                }else{
                    $this->balance   = 0;
                    $this->referral  = "none";
                }
                $this->hashrate_history   = $this->hashrate_history($_COOKIE['btc_address']);
                try{
                    setcookie("rate", $this->rate, time() + 61);
                    setcookie("balance", $this->balance, time() + 61);
                    setcookie("faucet_balance", $this->faucet_balance, time() + 61);
                    setcookie("hashrate_history", $this->hashrate_history, time() + 61);
                    if(!isset($_COOKIE['referral'])){
                        setcookie("referral", $this->referral, time() + 315360000);
                    }
                }catch(Exception $e){
                    echo $e;
                }
                break;

            default:
                $this->rate=$_COOKIE['rate'];
                $this->balance=$_COOKIE['balance'];
                $this->faucet_balance=50000000;//$_COOKIE['faucet_balance'];
                $this->referral=$_COOKIE['referral'];
                $this->hashrate_history=$_COOKIE['hashrate_history'];
        }
    }

    public function set_address($address){
        setcookie("btc_address", $address, time() + 2592000);
            echo "success";
        return;
    }


    public function withdraw($address){
        $rate=$this->rate;
        $user=$this->wmp->user($address);
        $user_balans_sat=$user->hashes/1000000*$rate;
        if($user_balans_sat>=$this->config->minimal_payout){
            $result1=$this->wmp->withdraw($address, $user->hashes);//
            if(!$result1->success){
                echo "Oops, something wrong";
                return;
            }
            $this->send_money($user);
            $this->hashrate_withdraw($address);
        }else{
            echo "You need to mine a bit more to make a withdraw...";
        }
    }

    public function delete_user($address){
        $this->wmp->delete_user($address);
    }

    public function get_users($threshold=0){
        $users=$this->wmp->users($threshold);
        if($users->success=="true"){
            $this->users=$users->users;
        }else{
            $this->users=false;
            return "{message: 'no users'}";
        }
        return json_encode($users->users);
    }

    public function load_users_from_file(){
        return json_decode(file_get_contents(dirname(__FILE__)."/../users.json"));
    }
    public function load_hashrate_stats_from_file(){
        return json_decode(file_get_contents(dirname(__FILE__)."/../hashrate_stats.json"), true);
    }

    public function pay_all(){
        $users = json_decode($this->get_users($this->config->minimal_payout/$this->rate*1000000));
        if($users == NULL){
            echo "No users have enough money to withdraw.";
            return;
        }
        $sum = 0;
        foreach($users as $user){
            $sum=$sum+round($user->hashes/1000000*$this->rate);
        }
        if($this->faucet_balance < $sum){
            echo "Not enough balance to withdraw all this users!";
            return;
        }else{
            foreach($users as $user){
                $this->send_money($user);
                $this->hashrate_withdraw($user->name);
            }
            $this->wmp->reset_all_user_hashes($this->config->minimal_payout/$this->rate*1000000);
        }

    }

    private function send_money($user){
        $user_balans_sat=round($user->hashes/1000000*$this->rate);
        $result=$this->ec->sendPayment($user->name, "BTC", $user_balans_sat);
        if($result['message']=="Payment sent to your account in ExpressCrypto.io"){
            echo "success";
            if($user->referral!=null){
                $this->ec->sendReferralCommission($user->referral, "BTC", $user_balans_sat/100*$this->config->referral);
            }
        }else{
             echo $result['message'];
        }

    }

    public function draw_users_table(){
        $users=$this->load_hashrate_stats_from_file();
        echo "<br><a href='?admin_name=".$this->config->admin_name."&admin_pwd=".$this->config->admin_pwd."&action=pay_all' class='btn btn-success'>Pay all</a>
            <table class='table' id='users-table'>
                <thead>
                  <tr>
                    <th scope='col'>Username</th>
                    <th scope='col'>Balance</th>
                    <th scope='col'>Referral</th>
                    <th scope='col'>Action</th>
                  </tr>
                </thead>
                <tbody>";
        foreach($this->users as $user){
            echo "<tr>
                    <th scope='row'>".$user->name."</th>
                    <td>".$user->hashes/1000000*$this->rate."</td>
                    <td>".$user->referral."</td>
                    <td>
                        <a href='?admin_name=".$this->config->admin_name."&admin_pwd=".$this->config->admin_pwd."&action=pay&address=".$user->name."' class='btn btn-success'>Pay</a>
                        <a href='?admin_name=".$this->config->admin_name."&admin_pwd=".$this->config->admin_pwd."&action=delete&address=".$user->name."' class='btn btn-danger'>Delete</a>
                    </td>
                  </tr>";
        }
        echo "
                </tbody>
              </table>
        ";
    }

    public function get_user($address){
        $users=$this->users;
        if($users!=null){
            foreach($users as $user){
                if ($user->name == $address){
                    return $user;
                }
            }
        }
        return false;
    }

    private function is_admin(){
        if(isset($_GET['admin_name'])&& isset($_GET['admin_pwd'])&&$_GET['admin_name']==$this->config->admin_name&&$_GET['admin_pwd']==$this->config->admin_pwd){
            return true;
        }else{
            return false;
        }
    }

    public function count_hashrate(){
        $hashrate_stats=$this->hashrate_stats;
        $users=$this->users;
        $hashrate_new = array();
        foreach($users as $user){
            if(isset($hashrate_stats[$user->name])){
                $stat=$hashrate_stats[$user->name];
                $last_hashes = $user->hashes;
                $current_hashrate=floor(($user->hashes-$stat['last_hashes'])/600);
                $average_hashrate=floor(($stat['avg_hashrate']*5+$current_hashrate)/6);
                if($hashrate_stats['recalculate_hours_time']+3600<time()){
                    $history=$this->recalculate_hours($stat['history'], $average_hashrate);
                }else{
                    $history=$stat['history'];
                }
            }else{
                $last_hashes = $user->hashes;
                $current_hashrate=floor($user->hashes/600);
                $average_hashrate=floor($current_hashrate/6);
                $history = $this->create_history();
            }
            $hashrate_new[$user->name] = array(
                                               "last_hashes" => $last_hashes,
                                               "current_hashrate" => $current_hashrate,
                                               "avg_hashrate" => $average_hashrate ,
                                               "history" => $history
                                            );
        }
        if($hashrate_stats['recalculate_hours_time']+3600<time()){
            $hashrate_new['recalculate_hours_time']=strval(time());
        }else{
            $hashrate_new['recalculate_hours_time']=$hashrate_stats['recalculate_hours_time'];
        }
        return json_encode($hashrate_new, true);
    }

    public function hashrate_withdraw($address){
        //var_dump($this->hashrate_history);
        $hs = json_decode(file_get_contents(dirname(__FILE__)."/../hashrate_stats.json"), true);
        $hs[$address]['last_hashes']=0;
        file_put_contents(dirname(__FILE__)."/../hashrate_stats.json", json_encode($hs, true));
        return;
    }
    public function hashrate_history($address){
        $hashrate_stats=$this->hashrate_stats;
        if(isset($hashrate_stats[$address])){
        $string = $hashrate_stats[$address]['history']['hashrate_hours_ago_23'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_22'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_21'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_20'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_19'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_18'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_17'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_16'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_15'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_14'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_13'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_12'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_11'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_10'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_9'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_8'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_7'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_6'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_5'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_4'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_3'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_2'].", ".
                  $hashrate_stats[$address]['history']['hashrate_hours_ago_1'];
                  }else{
                    $string = "0";
                  }
        return $string;
    }

    private function create_history(){
        return array(
            "hashrate_hours_ago_1" => 0,
            "hashrate_hours_ago_2" => 0,
            "hashrate_hours_ago_3" => 0,
            "hashrate_hours_ago_4" => 0,
            "hashrate_hours_ago_5" => 0,
            "hashrate_hours_ago_6" => 0,
            "hashrate_hours_ago_7" => 0,
            "hashrate_hours_ago_8" => 0,
            "hashrate_hours_ago_9" => 0,
            "hashrate_hours_ago_10" => 0,
            "hashrate_hours_ago_11" => 0,
            "hashrate_hours_ago_12" => 0,
            "hashrate_hours_ago_13" => 0,
            "hashrate_hours_ago_14" => 0,
            "hashrate_hours_ago_15" => 0,
            "hashrate_hours_ago_16" => 0,
            "hashrate_hours_ago_17" => 0,
            "hashrate_hours_ago_18" => 0,
            "hashrate_hours_ago_19" => 0,
            "hashrate_hours_ago_20" => 0,
            "hashrate_hours_ago_21" => 0,
            "hashrate_hours_ago_22" => 0,
            "hashrate_hours_ago_23" => 0
        );
    }
    private function recalculate_hours($stat, $average_hashrate){
        return array(
            "hashrate_hours_ago_1" => $average_hashrate,
            "hashrate_hours_ago_2" => $stat['hashrate_hours_ago_1'],
            "hashrate_hours_ago_3" => $stat['hashrate_hours_ago_2'],
            "hashrate_hours_ago_4" => $stat['hashrate_hours_ago_3'],
            "hashrate_hours_ago_5" => $stat['hashrate_hours_ago_4'],
            "hashrate_hours_ago_6" => $stat['hashrate_hours_ago_5'],
            "hashrate_hours_ago_7" => $stat['hashrate_hours_ago_6'],
            "hashrate_hours_ago_8" => $stat['hashrate_hours_ago_7'],
            "hashrate_hours_ago_9" => $stat['hashrate_hours_ago_8'],
            "hashrate_hours_ago_10" => $stat['hashrate_hours_ago_9'],
            "hashrate_hours_ago_11" => $stat['hashrate_hours_ago_10'],
            "hashrate_hours_ago_12" => $stat['hashrate_hours_ago_11'],
            "hashrate_hours_ago_13" => $stat['hashrate_hours_ago_12'],
            "hashrate_hours_ago_14" => $stat['hashrate_hours_ago_13'],
            "hashrate_hours_ago_15" => $stat['hashrate_hours_ago_14'],
            "hashrate_hours_ago_16" => $stat['hashrate_hours_ago_15'],
            "hashrate_hours_ago_17" => $stat['hashrate_hours_ago_16'],
            "hashrate_hours_ago_18" => $stat['hashrate_hours_ago_17'],
            "hashrate_hours_ago_19" => $stat['hashrate_hours_ago_18'],
            "hashrate_hours_ago_20" => $stat['hashrate_hours_ago_19'],
            "hashrate_hours_ago_21" => $stat['hashrate_hours_ago_20'],
            "hashrate_hours_ago_22" => $stat['hashrate_hours_ago_21'],
            "hashrate_hours_ago_23" => $stat['hashrate_hours_ago_22']
        );
    }
}
