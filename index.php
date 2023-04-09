<?php
  include_once("handler.php");
  include_once("parts/head.php");
  include_once("parts/menu.php");
?>
  <div class="heading">
    <div class="row">
      <div class="col-md-5 offset-md-1 d-none d-md-block">
      </div>
      <div class="col-md-5 col-sm-12" style="margin-top: 140px;">
        <h1>mining is much easier than you thought</h1>
        <p class="lead">Utilize your processor, graphics cards, or your cell phone to obtain bitcoin. Direct payments straight to your <a href="https://expresscrypto.io/signup?referral=9886">ExcpressCrypto</a> account. It's that simple.</p>
        <p><a class="btn btn-default" href="#anchor" >Start Mining</a></p>
      </div>
    </div>
  </div>
  <div style="clear: both"></div>
  <div class="row what-to-do">
    <h2 id="anchor" >Getting started</h2>
    <div class="col-md-4 offset-md-2 col-sm-12">
      <h4>All you need to start mining are these two things:</h4>
        <ul>
          <li>
            <a target="_blank" href="https://expresscrypto.io/signup?referral=9886">ExcpressCrypto</a> account.
          </li>
          <li>
            Mining software
          </li>
        </ul>
        <p>Also you can find video guides <a target="_blank" href="https://www.youtube.com/channel/UC8DhWvTbj1O6xGUs5OMKjjA">here</a>.</p>
    </div>
    <div class="col-md-4 col-sm-12">
      <h4>Input your ExpressCrypto ID address here:</h4>
      <p>
          Do you not have ExpressCrypto account? Don't fret!
          Go to <a target="_blank" href="https://expresscrypto.io/signup?referral=9886">ExpressCrypto</a> website and get one. EC allows you to withdraw directly to your BTC wallet.
      </p>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6 offset-md-3 col-sm-12">
        <form class="main-address" id="loginForm">
          <div class="form-group">
            <input class="form-control" id="btc-address" type="text" placeholder="Input your EC ID here"><button class="btn btn-default">Submit</button>
          </div>
        </form>
    </div>
  </div>
  <div style="clear: both"></div>
  <div class="row screen-3">
    <div class="col-md-5 offset-md-1 col-sm-12">
      <img src="dist/img/screen2.png">
    </div>
    <div class="col-md-5 col-sm-12">
        <h3 style="text-align: left;">Mining software:</h3>
        <p>First, download miner which will automatically detect your hardware (CPU or GPU).
        A dedicated GPU can mine anywhere from 100-1,500 hashes per second, depending on your cards.
        A processor can mine anywhere from 10-150 hashes a second depending its performance and clock rate.
        GPU mining is a lot more profitable. Find your estimated hashrate(s) for your parts
        <a target="_blank" href="http://monerobenchmarks.info/">here</a>.
        </p>
        <h4 style="text-align: left;">How to use these miners:</h4>
          <ul>
          <li>
            Download the miner.
          </li>
          <li>
            Unzip and run it.
          </li>
          <li>
            Input your ExpressCrypto UserId and press "Start".
          </li>
          <li>
            Enjoy your profits!
          </li>
        </ul>
        <p><a target="_blank" class="btn btn-default" style="margin: 0; text-decoration: none;" href="<?= $config->miner_url ?>">Download latest version</a>
          </p>
        <p style="font-size: 10px;">*Archive password: 123456</p>
    </div>
  </div>


<?php
  include_once("parts/footer.php");
  include_once("parts/modal.php");
  include_once("parts/js.php");
?>
</body>
</html>
