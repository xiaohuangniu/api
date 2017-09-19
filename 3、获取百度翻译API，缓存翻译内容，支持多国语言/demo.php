<?php
/*
 +----------------------------------------------------------------------
 + Title        : 使用DEMO
 + Author       : 小黄牛
 + Version      : V1.0.0.1
 + Initial-Time : 2017-9-18
 + Last-time    : 2017-9-19 + 小黄牛
 + Desc         : 
 +----------------------------------------------------------------------
*/
require 'vendor/Api.php';

# 使用DEMO
$obj = new Fanyi();
$res = $obj->Obtain('Little ox, I love you', '英语', '韩语');
echo '<pre>';
var_dump( $res );