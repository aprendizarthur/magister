<?php 
//ARQUIVO EXCLUSIVO PARA REDIRECIONAMENTO DE USUÁRIOS

    //REDIRECIONA USUARIO DESLOGADO DO PAINEL PROFESSOR
    function redirecionaDeslogado(){
        if(empty($_SESSION['nome-usuario']) || empty($_SESSION['tipo-usuario']) || empty($_SESSION['email-usuario'])){
            header("Location: ../forms/login.php");
            exit();
        }
    }

    //REDIRECIONA USUARIO LOGADO PARA O PAINEL
    function redirecionaLogado(){
        if(!empty($_SESSION['nome-usuario']) || !empty($_SESSION['tipo-usuario']) || !empty($_SESSION['email-usuario'])){
            header("Location: ../painel-professor/painel.php");
            exit();        
        }
    }

    //REDIRECIONA ALUNO PARA LOGIN
    function redirecionaAluno(){
        if($_SESSION['tipo-usuario'] != "Professor"){
            header("Location: ../forms/login.php");
            exit(); 
        }
    }
?>