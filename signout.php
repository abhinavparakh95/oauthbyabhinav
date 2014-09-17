<?php
require 'config/OAuth_config.php';
$redirect_uri = "http://test.localhost.com";
@session_start();
session_destroy();
$signoutURL = AUTH_SERVER . CMD_SIGNOUT . "?response_type=". RESPONSE_TYPE ."&client_id=" . CLIENT_ID . "&redirect_uri=" . $redirect_uri . "&scope=". SCOPE . "&state=" . STATE;
header('Location:'.$signoutURL);
