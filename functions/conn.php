<?php 
//ARQUIVO EXCLUSIVO PARA CONEXÃO COM O DB
    $host = 'localhost';
    $usuario = 'root';
    $senha = '';
    $db = 'magister';

    $mysqli = new mysqli($host, $usuario, $senha, $db);

    if($mysqli->connect_errno){
        header("Location: ../pages/erros/erro-conexao.php");
        die();
    }
?>