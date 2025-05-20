<?php 
//ARQUIVO EXCLUSIVO PARA REALIZAR LOGOUT
session_start();
session_unset();
session_destroy();
header("Location: ../forms/login.php");
?>