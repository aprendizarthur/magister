<?php 
require('../../functions/conn.php');
require('../../functions/validacoes.php');
//ARQUIVO EXCLUSIVO PARA FUNÇÕES DE CRUD USUÁRIOS

    //LOGIN
    function logarUsuario($mysqli){
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])){
            $email = htmlspecialchars(trim($_POST['email']));
            $senha = htmlspecialchars(trim($_POST['senha']));

            $validacaoEMAIL = existeUsuario($email, $mysqli);
            $validacaoSENHA = verificaSENHA($email, $senha, $mysqli);

            if($validacaoEMAIL == "" && $validacaoSENHA == ""){
                $_SESSION['erro-login-email'] = $validacaoEMAIL;
                $_SESSION['erro-login-senha'] = $validacaoSENHA;
                $_SESSION['valor-login-email'] = $email;

                $consulta = "SELECT nome, tipo FROM usuarios WHERE email = '$email'";

                if($resultado = $mysqli->query($consulta)){
                    $dados = $resultado->fetch_assoc();
                    
                    $_SESSION['tipo-usuario'] = $dados['tipo'];
                    $_SESSION['nome-usuario'] = $dados['nome'];
                    $_SESSION['email-usuario'] = $email;
                    header("Location: ../../pages/painel-professor/painel.php");
                    exit();
                } else {
                    $_SESSION['erro-login-email'] = "";
                    $_SESSION['erro-login-senha'] = "";
                    $_SESSION['valor-login-email'] = "";
                    header("Location: ../../pages/erros/erro-conexao.php");
                    exit();
                }
            } else {
                $_SESSION['erro-login-email'] = $validacaoEMAIL;
                $_SESSION['erro-login-senha'] = $validacaoSENHA;
                $_SESSION['valor-login-email'] = $email;
            }
        }
    }

    //REGISTRO
    function registrarUsuario($mysqli){
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])){
            $nome = htmlspecialchars(trim($_POST['nome']));
            $email = htmlspecialchars(trim($_POST['email']));
            $senha = htmlspecialchars(trim($_POST['senha']));
            $confirmaSenha = htmlspecialchars(trim($_POST['confirma-senha']));

            $tipoNome = "Usuario";
            $validacaoNOME = validarNOME($nome, $tipoNome, $mysqli);
            $validacaoEMAIL = validarEMAIL($email, $mysqli);
            $validacaoSENHA = validarSENHA($senha, $confirmaSenha);

            if($validacaoNOME == "" && $validacaoEMAIL == "" && $validacaoSENHA == ""){
                $nome = $mysqli->real_escape_string($nome);
                $email = $mysqli->real_escape_string($email);
                $senha = $mysqli->real_escape_string($senha);
                $HASHsenha = password_hash($senha, PASSWORD_DEFAULT);
                $tipo = "Professor";

                $consulta = "INSERT INTO usuarios (tipo, nome, email, senha) VALUES ('$tipo', '$nome', '$email', '$HASHsenha')";
                
                if($resultado = $mysqli->query($consulta)){
                
                } else {
                    $_SESSION['valor-registro-nome'] = "";
                    $_SESSION['valor-registro-email'] = "";
                    $_SESSION['erro-registro-nome'] = "";
                    $_SESSION['erro-registro-email'] = "";
                    $_SESSION['erro-registro-senha'] = "";
                    header("Location: ../../pages/erros/erro-conexao.php");
                    exit();
                }
                $_SESSION['valor-registro-nome'] = "";
                $_SESSION['valor-registro-email'] = "";
                $_SESSION['erro-registro-nome'] = "";
                $_SESSION['erro-registro-email'] = "";
                $_SESSION['erro-registro-senha'] = "";
                
                $_SESSION['tipo-usuario'] = $tipo;
                $_SESSION['nome-usuario'] = $nome;
                $_SESSION['email-usuario'] = $email;
                header("Location: ../../pages/painel-professor/painel.php");
                exit();
            } else {
                //retornar valores inseridos no form
                $_SESSION['valor-registro-nome'] = $nome;
                $_SESSION['valor-registro-email'] = $email;
                //retornar mensagens de erro
                $_SESSION['erro-registro-nome'] = $validacaoNOME;
                $_SESSION['erro-registro-email'] = $validacaoEMAIL;
                $_SESSION['erro-registro-senha'] = $validacaoSENHA;
            } 
        }
    }
?>