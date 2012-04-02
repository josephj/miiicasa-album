<?php
define("API_KEY",      "");
define("SECRET_KEY",   "");
define("REDIRECT_URL", "");
define("AUTH_URL",     "http://api.miiicasa.com/oauth/authorize");
define("TOKEN_URL",    "http://api.miiicasa.com/oauth/access_token");
define("API_URL",      "http://api.miiicasa.com/op/");
define("TOKEN_FILE",   "/tmp/miiicasa_token");

if ( ! API_KEY || ! SECRET_KEY || ! REDIRECT_URL)
{
    echo "ERROR - You must provide API_KEY, SECRET_KEY, and REDIRECT_URL constant values in config.php";
    exit;
}
?>
