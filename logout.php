<?php
require "include/session.php";
if(!empty($username)){
session_unset();
session_destroy();
}
header("Location:index.php");
?>