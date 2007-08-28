<?php
$dir = realpath(dirname(__FILE__)."/../../../..");
set_include_path("$dir/library" . PATH_SEPARATOR . "$dir/incubator/library" . PATH_SEPARATOR . get_include_path());
require_once "Zend/OpenId/Consumer.php";
require_once "Zend/OpenId/Extension/Sreg.php";

if (isset($_POST['openid_action']) &&
    $_POST['openid_action'] == "login" &&
    !empty($_POST['openid_identifier'])) {

    $consumer = new Zend_OpenId_Consumer();
    $props = array();
    foreach (Zend_OpenId_Extension_Sreg::getSregProperties() as $prop) {
        if (isset($_POST[$prop])) {
            if ($_POST[$prop] === "required") {
                $props[$prop] = true;
            } else if ($_POST[$prop] === "optional") {
                $props[$prop] = false;
            }
        }
    }
    $sreg = new Zend_OpenId_Extension_Sreg($props, null, 1.1);
    $ret = $consumer->login($_POST['openid_identifier'], null, null, $sreg);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Zend OpenID Consumer Example</title>
<style>
input.openid_login {
    background: url(login-bg.gif) no-repeat;
    background-color: #fff;
    background-position: 0 50%;
    color: #000;
    padding-left: 18px;
    width: 220px;
    margin-right: 10px;
}
</style>
</head>
<body>
<?php
if (isset($_POST['openid_action']) &&
    $_POST['openid_action'] == "login" &&
    !empty($_POST['openid_identifier'])) {
    if ($ret !== true) {
        echo "OpenID login failed because of internal error.<br>";
    }
} else if (isset($_GET['openid_mode']) &&
    $_GET['openid_mode'] == "id_res") {
    $consumer = new Zend_OpenId_Consumer();
    if (!$consumer->verify($_GET)) {
        if (isset($_GET["openid_claimed_id"])) {
            echo "INVALID ".$_GET["openid_claimed_id"]."<br>";
        } else {
            echo "INVALID ".$_GET["openid_identity"]."<br>";
        }
    } else {
        if (isset($_GET["openid_claimed_id"])) {
            echo "VALID ".$_GET["openid_claimed_id"]."<br>";
        } else {
            echo "VALID ".$_GET["openid_identity"]."<br>";
        }
    }
} else if (isset($_GET['openid_mode']) &&
    $_GET['openid_mode'] == "cancel") {
    echo "CANCELED<br>";
}
?>
<div>
<form action="<?php echo Zend_OpenId::selfUrl(); ?>"
    method="post" onsubmit="this.login.disabled=true;">
<fieldset id="openid">
<legend>OpenID Login</legend>
<input type="hidden" name="openid_action" value="login">
<div>
<input type="text" name="openid_identifier" class="openid_login" value="<?php 
if (isset($_GET["openid_claimed_id"])) {
    echo $_GET["openid_claimed_id"];
} else if (isset($_GET["openid_identity"])){
    echo $_GET["openid_identity"];
} else {
    echo "";
}
?>">
<input type="submit" name="login" value="login">
<table border="0" cellpadding="2" cellspacing="2">
<tr><td>&nbsp;</td><td>requird</td><td>optional</td><td>none</td><td>&nbsp</td></tr>
<?php
    $sreg = new Zend_OpenId_Extension_Sreg();
    $sreg->parseResponse($_GET);
    $data = $sreg->getProperties();
    foreach (Zend_OpenId_Extension_Sreg::getSregProperties() as $prop) {
        $val = isset($data[$prop]) ? $data[$prop] : "";
        echo <<<EOF
<tr><td>$prop</td>
<td>
  <input type="radio" name="$prop" value="required">
</td><td>
  <input type="radio" name="$prop" value="optional">
</td><td>
  <input type="radio" name="$prop" value="none" checked="1">
</td><td>
  $val
</td></tr>
EOF;
    }
?>
</table>
<br>
<a href="<?php echo dirname(Zend_OpenId::selfUrl()); ?>/test_server.php?openid.action=register">register</a>
</div>
</fieldset>
</form>
</div>
</body>
</html>
