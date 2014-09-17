<?php
@session_start();

require 'config/OAuth_config.php';
require 'libs/OAuth.php';

$oauth = new OAuth();
$oauth->init();
if($oauth->authCode){
$_SESSION['authcode'] = $oauth->authCode;
}
if($oauth->user['loggedIn']){
  print_r($oauth->user);
  echo "<a href='signout.php'>Sign Out</a> " ;
}
else {
  echo "<a href='$oauth->signinURL'>Sign In</a> "  ;
}
