<?php 
include('conn.php');
//ARQUIVO EXCLUSIVO PARA FUNÇÕES DE VALIDAÇÃO / VERIFICAÇÃO DADOS FORMS
    
    //VALIDANDO NOME USUARIO/ATIVIDADE
    function validarNOME($nome, $tipoNome, $mysqli){
        switch($tipoNome){
            case "Usuario":
                $letrasInvalidas = "&\'\/\|;:,#?@";
                $mensagem = (strpbrk($nome, $letrasInvalidas)) !== false ? "Contém caracteres inválidos." : "";
                if($mensagem == "Contém caracteres inválidos."){return $mensagem;}
                
                $consulta = "SELECT COUNT(*) AS total FROM usuarios WHERE nome = '$nome'";

                if($resultado = $mysqli->query($consulta)){
                    $dados = $resultado->fetch_assoc();
                    
                    if($dados['total'] != 0){    
                        $mensagem = "Nome já registrado.";
                        return $mensagem;
                    }
                }
            break;

            case "Atividade":
                $letrasInvalidas = "&\'\/\|;:,#?@";
                $mensagem = (strpbrk($nome, $letrasInvalidas)) !== false ? "Contém caracteres inválidos." : "";
                if($mensagem == "Contém caracteres inválidos."){return $mensagem;}
        }
        
    }

    //VALIDANDO EMAIL USUARIO
    function validarEMAIL($email, $mysqli){
        $mensagem = (filter_var($email, FILTER_VALIDATE_EMAIL) ? "" : "Insira um e-mail válido.");
        if($mensagem == "Insira um e-mail válido."){return $mensagem;}

        $consulta = "SELECT COUNT(*) AS total FROM usuarios WHERE email = '$email'";

        if($resultado = $mysqli->query($consulta)){
            $dados = $resultado->fetch_assoc();

            if($dados['total'] != 0){
                $mensagem = "E-mail já registrado.";
                return $mensagem;
            }
        }
    }

    //VALIDANDO SENHA USUARIO
    function validarSENHA($senha, $confirmaSenha){
        $mensagem = ($senha === $confirmaSenha) ? "" : "As senhas não coincidem.";
        return $mensagem;
    }

    //VERIFICANDO SE EXISTE O E-MAIL NO DB
    function existeUsuario($email, $mysqli){
        $email = $mysqli->real_escape_string($email);
        $consulta = "SELECT COUNT(*) AS total FROM usuarios WHERE email = '$email'";

        if($resultado = $mysqli->query($consulta)){
            $dados = $resultado->fetch_assoc();
            $mensagem = $dados['total'] != 0 ? "" : "E-mail não registrado.";
            return $mensagem;
        } else {
            header("Location: ../erros/erro-conexao.php");
            exit();
        }
    }

    //VERIFICANDO SE A SENHA DO LOGIN ESTÁ CORRETA
    function verificaSENHA($email, $senha, $mysqli){
        $email = $mysqli->real_escape_string($email);
        $senha = $mysqli->real_escape_string($senha);

        $consulta = "SELECT senha FROM usuarios WHERE email = '$email'";
        
        if($resultado = $mysqli->query($consulta)){
            $dados = $resultado->fetch_assoc();
            if(empty($dados['senha'])){$mensagem = "Usuário não encontrado."; return $mensagem;}
            $mensagem = password_verify($senha, $dados['senha']) ? "" : "Senha incorreta.";
            return $mensagem;
        } else {
            header("Location: ../erros/erro-conexao.php");
            exit();
        }
    }

    //VERIFICANDO SE O Nº QUESTOES/ALTERNATIVAS É UM NÚMERO
    function validarAtividade($Nalternativas, $Nquestoes){
        $mensagem = (!(int)($Nalternativas) || !(int)($Nquestoes)) ? "Insira um número." : "";
        return $mensagem;
    }

    //VERIFICANDO ENUNCIADOS DE ATIVIDADES
    function validarEnunciados($Nquestoes){
        for($i = 0; $i <= ($Nquestoes - 1); $i++){
            $letrasInvalidas = "&#@";
            $mensagem = (strpbrk(htmlspecialchars($_POST['enunciado-'.($i+1).'']), $letrasInvalidas)) !== false ? "Contém caracteres inválidos." : "";          
            if($mensagem == "Contém caracteres inválidos."){
                return $mensagem;
                exit();
            }
        }
    }

    //VERIFICANDO ALTERNATIVAS DE ATIVIDADES
    function validarALTERNATIVAS($Nquestoes, $Nalternativas){
        $Nalternativas = (int)$Nalternativas;
        for($i = 0; $i <= ($Nquestoes - 1); $i++){
            for($j = 0; $j <= ($Nalternativas -1); $j++){
                $letrasInvalidas = "&#@";
                $chave = 'alternativa-'.($i+1)."-".($j+1).'';
                $mensagem = strpbrk(htmlspecialchars($_POST[$chave]), $letrasInvalidas) !== false ? "Contém caracteres inválidos." : "";          
                if($mensagem == "Contém caracteres inválidos."){
                    return $mensagem;
                    exit();
                }    
            }
        }
    }

    //VALIDANDO SE ID DA ATIVIDADE EXISTE
    function atividadeExiste($mysqli){
        $id = htmlspecialchars($_GET['id'] ?? "vazio");
        $id = $mysqli->real_escape_string($id);

        $consulta = "SELECT COUNT(*) AS total FROM atividades WHERE id = '$id'";

        if($resultado = $mysqli->query($consulta)){
            $dados = $resultado->fetch_assoc();
            if($dados['total'] == 0){
                header("Location: ../../pages/erros/atividade-inexistente.php");
                exit();
            }
        } else {
            header("Location: ../../pages/erros/erro-conexao.php");
            exit();
        }
    }

    //VERIFICANDO SE IP+USER_AGENT JÁ REALIZOU ATIVIDADE
    function realizouAtividade($mysqli){
        if(empty($_SESSION['tipo-conta']) || $_SESSION['tipo-conta'] != "Professor"){
            $idAtividade = $mysqli->real_escape_string($_GET['id']);
            $ip = $_SERVER['REMOTE_ADDR'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'];

            $consulta = "SELECT COUNT(*) AS total FROM resultados WHERE id_atividade = '$idAtividade' AND ip = '$ip' AND user_agent = '$userAgent'";

            if($resultado = $mysqli->query($consulta)){
                $dados = $resultado->fetch_assoc();
                if($dados['total'] > 0){
                    header("Location: ../../pages/erros/atividade-realizada.php");
                    exit();
                }
            } else {
                header("Location: ../../pages/erros/erro-conexao.php");
                exit();
            }    
        }
    }
?>