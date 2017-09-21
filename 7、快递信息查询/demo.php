<?php
/*
 +----------------------------------------------------------------------
 + Title        : 使用DEMO
 + Author       : 小黄牛
 + Version      : V1.0.0.1
 + Initial-Time : 2017-9-20
 + Last-time    : 2017-9-21 + 小黄牛
 + Desc         : 
 +----------------------------------------------------------------------
*/
require 'vendor/Api.php';

# 使用DEMO
$obj = new Express();
$num = $obj->Obtain('536625023920');
echo '<pre>';
var_dump( $num );