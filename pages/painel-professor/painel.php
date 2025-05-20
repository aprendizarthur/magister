<?php 
session_start();
require('../../functions/CRUD/CRUDatividades.php');
require('../../functions/redirects.php');
redirecionaDeslogado();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<!--META TAGS-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<!--TÍTULO/ÍCONE/DESCRIÇÃO DA PÁGINA/COR TEMA NAVEGADOR-->
    <title>Magister - Painel do Professor</title>
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
</head>
<body>
    <div class="container">
        <main>
            <div class="row d-flex justify-content-around">
                <!--NAV LATERAL-->
                <div id="nav-principal" class="col-2 col-md-4 col-lg-3 p-4 border">
                    <section>
                        <nav>
                            <ul class="ubuntu-regular p-2">
                                <li><a class="selected" href="painel.php"><i class="fa-solid fa-list-check fa-xl"></i><span class="d-none d-md-inline">Atividades</span></a></li>
                                <li><a href="painel.php#pesquisa"><i class="fa-solid fa-magnifying-glass fa-lg"></i><span class="d-none d-md-inline">Pesquisa</span></a></li>
                                <li><a href="../forms/nova-atividade.php"><i class="fa-solid fa-plus fa-xl"></i><span class="d-none d-md-inline">Criar</span></a></li>
                                <li><a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket fa-xl"></i><span class="d-none d-md-inline">Sair</span></a></li>
                            </ul>
                        </nav>
                    </section>
                </div>

                <!--CONTEUDO PRINCIPAL-->
                <div id="conteudo-principal" class="col-10 col-md-8 col-lg-9 p-4 border">
                    <section id="atividades">
                        <?php pesquisarAtividades($mysqli); ?>              
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