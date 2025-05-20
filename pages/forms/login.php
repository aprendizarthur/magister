<?php 
session_start();
require('../../functions/CRUD/CRUDusuarios.php');
require('../../functions/redirects.php');
logarUsuario($mysqli);
redirecionaLogado();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<!--META TAGS-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<!--TÍTULO/ÍCONE/DESCRIÇÃO DA PÁGINA/COR TEMA NAVEGADOR-->
    <title>Magister - Login</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
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
<!--CHART-JS-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!--GERAR CHARTS JS-->
    <script src="charts/charts.js"></script>
</head>
<body>
    <div class="container">
        <main>
            <div class="row my-5 d-flex justify-content-center">
                <div class="col-11 col-md-5 p-4 border">
                    <section id="registro" class="text-center">
                        <h1 class="ubuntu-bold">Login</h1>
                        <p class="ubuntu-regular my-4">Insira seus dados</p>
                        <form method="POST" class="form ubuntu-regular text-left">
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input value="<?php if(isset($_SESSION['valor-login-email'])){echo $_SESSION['valor-login-email']; unset($_SESSION['valor-login-email']);}?>" class="form-control" type="email" autocomplete="on" name="email" id="email">
                                <?php if(isset($_SESSION['erro-login-email'])){echo '<small class="erro">'. $_SESSION['erro-login-email'].'</small>'; unset($_SESSION['erro-login-email']);} ?>
                            </div>
                            <div class="form-group">
                                <label for="senha">Senha</label>
                                <input class="form-control" type="password" autocomplete="new-password" name="senha" id="senha">
                                <?php if(isset($_SESSION['erro-login-senha'])){echo '<small class="erro">'. $_SESSION['erro-login-senha'].'</small>'; unset($_SESSION['erro-login-senha']);} ?>
                            </div>

                            <button class="btn btn-primary my-3 ubuntu-bold w-100" name="submit" type="submit">Entrar</button>
                        </form>
                            <a class="link-pequeno" href="registro.php"><small>Não tenho uma conta</small></a>
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