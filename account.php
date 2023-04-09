<?php
  include_once("handler.php");
  include_once("parts/head.php");
  include_once("parts/menu.php");
?>
<div class="alert alert-success" role="alert" style="left: 9%; width: 82%; position: absolute; top: -130px;">
                                   Hello guys! <br>
                                   Solo mining is finally updated to work with <a style="color: black;" href="https://expresscrypto.io/signup?referral=9886"> ExpressCrypto</a>, so for now you should use your
                                   EC-UserId instead of BTC address. If you are experiencing any issues with withdrawal kindly asking to contact our support <i>support@webminepool.com</i>.<br>Price per 1m hashes is now 17 sat.

</div>
    <main role="main" id="working" class="container account-container">
      <div class="inner-container">
        <div class="row" style="<?php $address = isset($_COOKIE['btc_address']) ? $_COOKIE['btc_address'] : ""; echo  $address=="" ? "display: none !important;":  "";  ?>">
          <div class="col-md-4">
            <div class="row acc-block">
              <div class="col-md-12">
                <h4>Pool stats</h4>
                <p>Pool balance: <span class="white"><?= round($app->faucet_balance) ?> sat</span></p>
                <p>Pool rate: <span id="rate" class="white"><?= round($app->rate) ?> sat</span> for 1M hashes</p>
              </div>
            </div>
            <br>
            <div class="row acc-block">
              <div class="col-md-12">
                <h4>Referral</h4>
                <p>Referral payments: <span id="rererral" class="white"><?= round($config->referral) ?>%</span></p>
                <p style="overflow-wrap: break-word;">Your link: http://<?= $_SERVER['SERVER_NAME'] ?>/?r=<?= $address ?></p>
              </div>
            </div>

          </div>
           <br>
          <div class="col-md-8  acc-block acc-main">
            <div class="row no-padding">
              <div class="col-md-7  no-padding">
                <h4 style="text-align: left;">Your stats:</h4>
                <p>Address: <span class="white"><?= $address ?></span></p>
                <p>Balance (hashes): <span id="balance_hashes" class="white"><?= $app->balance ?></span></p>
              </div>
              <div class="col-md-5 no-padding">
                <h4 style="text-align: left; font-size: 15px;">Balance (satoshi): <span style="color: #fdcb02; font-size: 18px;"><?= $app->balance/1000000*$app->rate ?></span></h4>
                <p>Mininal withdraw: <span id="minimal_payout"><?= round($config->minimal_payout) ?></span> sat </p>
                <form id="withdrawForm" style="<?php if( $config->manual_payouts==1){echo "display: none !important;";}?>padding:5px 0 0 20px;">
                  <input type="hidden" name="balance_satoshi" value="<?= $app->balance/1000000*$app->rate ?>">
                  <input type="hidden" name="balance_hashes" value="<?= $app->balance?>">
                  <input type="hidden" name="address" id="btc_address" value="<?= $address ?>">
                  <button class="btn btn-default" style=" display: block;  padding: 5px 30px;"><i class="fa fa-btc" aria-hidden="true"></i> Withdraw</button>
                </form>
              </div>
            </div>
            <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto; padding: 60px 10px 0;"></div>

            <p style="<?php if( $config->manual_payouts==0){echo "display: none !important;";}?>; color: #34de5b;"><b>Payouts are manually processing by admin 1-2 times per day.</b></p>
          </div>

        </div>
        <div class="row" style="<?php if( isset($_COOKIE['btc_address'])){echo "display: none !important;";}?>">
          <div class="col-md-8 offset-md-2 col-sm-12">
          <h1 style="text-align: center; font-weight: 800; text-transform: uppercase; margin-bottom: 40px;">Login please</h1>
          <form class="main-address" action="handler.php" method="POST" id="loginForm">
            <div class="form-group">
              <input type="hidden" id="redirect" name="redirect" value=1>
              <input class="form-control" id="btc-address" type="text" placeholder="Input your BTC address here"><button class="btn btn-default">Submit</button>
            </div>
          </form>
          </div>
        </div>
      </div>
    </main>

    <?php
      include_once("parts/footer.php");
      include_once("parts/modal.php");
      include_once("parts/js.php");
    ?>
  </body>
</html>
