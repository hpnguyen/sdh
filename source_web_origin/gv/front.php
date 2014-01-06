<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
// Include the main TCPDF library (search for installation path).
require_once('./app/libs/tcpdf/examples/tcpdf_include.php');
// Include mPDF library.
require_once("./app/libs/mpdf57/mpdf.php");
//Add auto loader
require_once ('./app/libs/res/auto_loader.php');
//Add route map
include './app/libs/res/route.php';
//Add front end class
include './app/libs/res/front.php';
//Add template object
include './app/template/index.php';
//Add base table
include './app/model/base/basetable.php';
//Add helper static class
include './app/libs/helper/helper.php';
$route = new Route();