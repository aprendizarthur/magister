<?php 
session_start();
require('../../functions/CRUD/CRUDusuarios.php');
require('../../functions/redirects.php');
registrarUsuario($mysqli);
redirecionaLogado();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<!--META TAGS-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<!--TÍTULO/ÍCONE/DESCRIÇÃO DA PÁGINA/COR TEMA NAVEGADOR-->
    <title>Magister - Registro</title>
    <link rel="icon" type="image/x-icon" href="../../images/assets/icon.ico">
    <meta name="description" content="">
    <meta name="theme-color" content="##249EF0">
    
<!--BOOTSTRAP CSS-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<!--FOLHA CSS-->
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
<!--FONTAWESOME JS-->
    <script src="https://kit.fontawesome.com/6afdaad939.js" crossorigin="anonymous">      </script>
<!--FONTES GOOGLE -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <main>
            <div class="row my-5 d-flex justify-content-center">
                <div class="col-11 col-md-6 col-lg-5 p-4 border">
                    <section id="registro" class="text-center">
                        <span class="ubuntu-bold d-block mb-4"><i class="fa-solid fa-graduation-cap fa-xl"></i><span>MAGISTER <sup class="ubuntu-light">®</sup></span></span>
                        <h1 class="ubuntu-bold texto-azul">Registre-se</h1>
                        <form method="POST" class="form ubuntu-regular text-left">
                            <div class="form-group">
                                <label for="nome">Nome </label>
                                <input value="<?php if(isset($_SESSION['valor-registro-nome'])){echo $_SESSION['valor-registro-nome']; unset($_SESSION['valor-registro-nome']);}?>" class="form-control" maxlength="100" type="text" autocomplete="nome" name="nome" id="nome">
                                <?php if(isset($_SESSION['erro-registro-nome'])){echo '<small class="erro">'. $_SESSION['erro-registro-nome'].'</small>'; unset($_SESSION['erro-registro-nome']);} ?>
                            </div>
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input value="<?php if(isset($_SESSION['valor-registro-email'])){echo $_SESSION['valor-registro-email']; unset($_SESSION['valor-registro-email']);}?>" class="form-control" type="email" autocomplete="on" name="email" id="email">
                                <?php if(isset($_SESSION['erro-registro-email'])){echo '<small class="erro">'. $_SESSION['erro-registro-email'].'</small>'; unset($_SESSION['erro-registro-email']);} ?>
                            </div>
                            <div class="form-group">
                                <label for="senha">Senha</label>
                                <input class="form-control" type="password" autocomplete="new-password" name="senha" id="senha">
                                <?php if(isset($_SESSION['erro-registro-senha'])){echo '<small class="erro">'. $_SESSION['erro-registro-senha'].'</small>'; unset($_SESSION['erro-registro-senha']);} ?>
                            </div>
                            <div class="form-group">
                                <label for="confirma-senha">Confirmar senha</label>
                                <input class="form-control" type="password"  name="confirma-senha" id="confirma-senha">
                            </div>

                            <button class="btn btn-primary my-3 ubuntu-bold w-100" name="submit" type="submit">Registrar</button>
                        </form>
                            <a class="link-pequeno" href="login.php"><small>Já tenho uma conta</small></a>
                    </section>
                </div>
            </div>
        </main>
        <!--JQUERY, POPPER E BOOTSTRAP JS-->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>   
    </div>
</body>
</html>