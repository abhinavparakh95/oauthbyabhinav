<?php
class OAuth {
  function __construct() {
    $this->redirect_uri = rtrim("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", $_SERVER['QUERY_STRING']);
    $this->redirect_uri = rtrim($this->redirect_uri, "/?");
    $this->authCode = $_GET['authorization_code'];
    $this->accessToken = $_SESSION['access_token'];
  }

  function init() {
    if($this->isLoggedIn_client()){
      $this->generateAccessReq();
      $this->getUser();
    $this->signoutURL = AUTH_SERVER . CMD_SIGNOUT . "?response_type=". RESPONSE_TYPE ."&client_id=" . CLIENT_ID . "&redirect_uri=" . $this->redirect_uri . "&scope=". SCOPE . "&state=" . STATE;
    }
    else if($this->isLoggedIn_server()){
      $this->generateTokenReq();
      $this->getToken();
      $_SESSION['access_token'] = $this->accessToken;
      $this->redirectTo($this->redirect_uri);
    }
    else {
      $this->generateAuthReq();
      if(PRIVATE_SITE) {
        $this->redirectTo($this->authURL);
      }
      else {
        $this->signinURL = $this->authURL;
      }
    }
  }

  function isLoggedIn_client() {
    return $this->hasAccessToken();
  }

  function isLoggedIn_server() {
    return $this->hasAuthCode();
  }


  function generateAuthReq() {
    $this->authURL = AUTH_SERVER . CMD_AUTHORIZE . "?response_type=". RESPONSE_TYPE ."&client_id=" . CLIENT_ID . "&redirect_uri=" . $this->redirect_uri . "&scope=". SCOPE . "&state=" . STATE;

  }

  function generateTokenReq() {
    $this->tokenURL = AUTH_SERVER . CMD_REQUEST_TOKEN . "?client_id=" . CLIENT_ID . "&client_secret=" . CLIENT_SECRET . "&grant_type=" . GRANT_TYPE . "&redirect_uri=" . $this->redirect_uri . "&authorization_code=". $this->authCode;

  }

  function generateAccessReq() {
    $this->accessURL = AUTH_SERVER . CMD_REQUEST_ACCESS . "?client_id=" . CLIENT_ID . "&client_secret=" . CLIENT_SECRET . "&access_token=" .$this->accessToken;
  }

  function getToken() {
    $contents = file_get_contents($this->tokenURL);
    $contents = json_decode($contents,true);
    $this->accessToken = $contents['access_token'];
  }

  function getAccessToken() {
    $this->accessToken = $_SESSION['access_token'];
  }

  function hasAccessToken() {
    if($this->accessToken) {
      return 1;
    }
    return 0;
  }

  function hasAuthCode() {
    if($this->authCode) {
      return 1;
    }
    return 0;
  }

  function redirectTo($url) {
    header('Location:'.$url);
  }
  function getUser() {
    $contents = file_get_contents($this->accessURL);
    $this->user = json_decode($contents, true);
  }
}
?>
