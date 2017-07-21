<?php

//header("Location: ./index.php?g=Mobile&m=Trade&a=wxPayNotice");
$_GET['m'] = "Home";
$_GET['c'] = "public";
$_GET['a'] = "notify";
require './index.php';