<?php
session_start();
include "Common.php";
$common = new Common();
$common->logout();
header("Location: index.php");