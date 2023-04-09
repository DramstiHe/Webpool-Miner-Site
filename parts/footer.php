<footer <?php  if( basename($_SERVER['PHP_SELF']) == "account.php" && !isset($_COOKIE['btc_address']) ) {
  echo " style='position: fixed; bottom: 0px; '";
} else {
  //Show content
} ?>>
  <nav class="navbar navbar-expand-md navbar-dark">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <p>	© <a target="_blank" href="https://webminepool.com" style="color: #9ba8fe;">WebMinePool.com</a> 2017-2019.</p>
      </li>
      </ul>
      <div class="nav-bar-right">
        <ul class="navbar-nav">     
          <li class="nav-item">
            <a target="_blank" class="nav-link" href="https://www.youtube.com/channel/UC8DhWvTbj1O6xGUs5OMKjjA"><i class="fa fa-youtube-play"></i> Video guides</a>
          </li>
        </ul> 
      </div>
  </nav>
</footer> 