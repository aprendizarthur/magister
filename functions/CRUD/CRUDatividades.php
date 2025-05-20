<?php 
require('../../functions/conn.php');
require('../../functions/validacoes.php');
//ARQUIVO EXCLUSIVO PARA FUNCOES DE CRUD ATIVIDADES

    //REGISTRO
    function criarAtividade($mysqli){
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])){
            //$tipoAtividade = "";
            $nome = htmlspecialchars($_POST['nome']);
            $Nquestoes = htmlspecialchars((int)$_POST['questoes']);
            $Nalternativas = htmlspecialchars((int)$_POST['alternativas']);
            $anonimo = empty($_POST['anonimo']) ? "off" : htmlspecialchars($_POST['anonimo']);

            $tipoNome = "Atividade";
            $validacaoNOME = validarNOME($nome, $tipoNome, $mysqli);
            //$tipoAtividade = "";
            $validacaoAtividade = validarAtividade($Nalternativas, $Nquestoes);

            if($validacaoNOME == "" && $validacaoAtividade == ""){
                $_SESSION['valor-registro-nome'] = "";
                $_SESSION['valor-registro-questoes'] = "";
                $_SESSION['valor-registro-alternativas'] = "";
                $_SESSION['erro-registro-nome'] = "";
                $_SESSION['erro-registro-numeros'] = "";

                //$_SESSION['registro-atividade-tipo'] = "";
                $_SESSION['registro-atividade-nome'] = $nome;
                $_SESSION['registro-atividade-questoes'] = $Nquestoes;
                $_SESSION['registro-atividade-alternativas'] = $Nalternativas;
                $_SESSION['registro-atividade-anonimo'] = $anonimo;
                header("Location: nova-atividade-questoes.php");
                exit();
            } else {
                $_SESSION['valor-registro-nome'] = $nome;
                $_SESSION['valor-registro-questoes'] = $Nquestoes;
                $_SESSION['valor-registro-alternativas'] = $Nalternativas;
                $_SESSION['erro-registro-nome'] = validarNOME($nome, $tipoNome, $mysqli);
                $_SESSION['erro-registro-numeros'] = validarAtividade($Nalternativas, $Nquestoes);
            }
        }
    }

    //FUNÇÃO QUE CRIA AS QUESTÕES DA ATIVIDADE, VALIDA E ENVIA PRO DB
    function criarQuestoes($mysqli){
        $idAtividade = criarIDatividade($mysqli);
        $Nquestoes = $_SESSION['registro-atividade-questoes'];
        $Nalternativas = $_SESSION['registro-atividade-alternativas']; 
        $nome = $_SESSION['registro-atividade-nome']; 
        $professor = $_SESSION['nome-usuario'];
        $anonimo = $_SESSION['registro-atividade-anonimo'];
        $tipoAtividade = "Multipla Escolha";

        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])){
            $verificacaoENUNCIADOS = validarEnunciados($Nquestoes);
            $verificacaoALTERNATIVAS = validarALTERNATIVAS($Nquestoes, $Nalternativas);
            
            if($verificacaoENUNCIADOS == "" && $verificacaoALTERNATIVAS == ""){
                $consulta = "INSERT INTO atividades (id, tipo, nome, professor, questoes, alternativas) VALUES ('$idAtividade', '$tipoAtividade', '$nome', '$professor', '$Nquestoes', '$Nalternativas')";

                if($resultado = $mysqli->query($consulta)){
                    $Nalternativas = (int) $Nalternativas;
                    for($i = 0; $i <= $Nquestoes - 1; $i++){
                        $j = 1;
                        //salvar enunciado e alternatvias de cada questão
                        $enunciado = $_POST['enunciado-'.($i+1)];
                        $correta = $_POST['alternativa-'.($i+1)."-".($Nalternativas)];
                        $alternativa1 = $_POST['alternativa-'.($i+1)."-".($j)];
                        $alternativa2 = $_POST['alternativa-'.($i+1)."-".($j+1)];
                        $alternativa3 = $_POST['alternativa-'.($i+1)."-".($j+2)] ?? "";
                        $alternativa4 = $_POST['alternativa-'.($i+1)."-".($j+3)] ?? "";

                        $consulta = "INSERT INTO questoes (id_atividade, anonimo, enunciado, alternativa1, alternativa2, alternativa3, alternativa4, correta) VALUES ('$idAtividade', '$anonimo', '$enunciado', '$alternativa1', '$alternativa2', '$alternativa3', '$alternativa4', '$correta')";

                        if($mysqli->query($consulta)){
                    
                        } else {
                            //não registrou alguma questão
                            //excluir atividade
                            //excluir questões anteriores
                            //encaminhar form de novo
                        }
                    }

                    //encaminhar painel
                    header("Location: ../painel-professor/painel.php");
                }else{
                    //não registrou atividade
                    header("Location: nova-atividade.php");
                    exit();
                }
            }                
        } else {
            //exibir formulário
            mostrarFormularioQuestoes($Nquestoes, $Nalternativas, $nome);
        }
    }
        //FUNÇÕES SECUNDÁRIAS PARA REGISTRAR ATIVIDADE
        function criarIDatividade($mysqli){
            $alfabeto = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
            
            do{ 
                $id = (string)rand(1000000000, 99999999999);
                for($i = 0; $i <= (mb_strlen($id) - 1); $i++){       
                    if($i % 2 == 0){$id[$i] = $alfabeto[$i];}
                }

                $consulta = "SELECT COUNT(*) AS total from atividades WHERE id = '$id'";

                if($resultado = $mysqli->query($consulta)){
                    $dados = $resultado->fetch_assoc();
                }    
            }while($dados['total'] > 0);

            return $id;
        }

        //FUNÇÃO QUE EXIBE O FORMULÁRIO PARA NOVA ATIVIDADE
        function mostrarFormularioQuestoes($Nquestoes, $Nalternativas, $nome){
                //abertura formulario
                $Nquestoes = (int)$Nquestoes;
                $Nalternativas = (int)$Nalternativas;
                echo '
                    <section id="registro" class="text-center">
                        <h1 class="ubuntu-bold">'.$nome.'</h1>
                        <p class="ubuntu-regular mt-2">Questionário com '.$Nquestoes.' questões contendo '.$Nalternativas.' alternativas</p>
                        <form id="registro-atividade" method="POST" class="form ubuntu-regular text-left mt-5">
                ';
                //formulario dinamico questões/alternativas
                for($i = 0; $i <= ($Nquestoes - 1); $i++){
                    echo '
                                <h2 class="texto-azul ubuntu-bold">Questão '.($i + 1).'</h2>
                                <small class="d-block my-2">Alternativas serão em ordem aleatória para o aluno</small>
                                <div class="form-group enunciado">
                                    <label for="enunciado-'.($i+1).'"><h3 class="ubuntu-bold"><i>Enunciado da questão</h3></i></label>
                                    <textarea required class="form-control" id="enunciado-'.($i+1).'" name="enunciado-'.($i+1).'" rows="3"></textarea>
                                </div>
                    ';

                    for($j = 0; $j <= ($Nalternativas - 1); $j ++){
                        if($j != ($Nalternativas - 1)){
                            echo '
                                <div class="form-group">
                                    <label for="alternativa-'.($i+1)."-".($j+1).'">Alternativa incorreta</label>
                                    <textarea required class="form-control" id="alternativa-'.($i+1)."-".($j+1).'" name="alternativa-'.($i+1)."-".($j+1).'" rows="3"></textarea>
                                </div>    
                            ';    
                        } else {
                            echo '
                                <div class="form-group mb-5">
                                    <label for="alternativa-'.($i+1)."-".($j+1).'">Alternativa Correta</label>
                                    <textarea required class="form-control" id="alternativa-'.($i+1)."-".($j+1).'" name="alternativa-'.($i+1)."-".($j+1).'" rows="3"></textarea>
                                </div>    
                            '; 

                            echo '
                                <hr class="d-block mb-5">
                            ';
                        }
                    }
                }
                //fechamento formulario
                echo '
                            <button class="btn btn-primary my-3 ubuntu-bold w-100" name="submit" type="submit">Criar atividade</button>
                        </form>
                            <a class="link-pequeno" href="../painel-professor/painel.php"><small>Voltar para o painel</small></a>                
                        </section> 
                ';
            }

    //FUNÇÃO PESQUISA DE ATIVIDADES
    function pesquisarAtividades($mysqli){
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])){
            $pesquisa = htmlspecialchars($_POST['pesquisa']);
            $consulta = "SELECT * FROM atividades WHERE nome LIKE '%$pesquisa%' ORDER BY registro DESC";
            
            if($resultado = $mysqli->query($consulta)){
                echo '
                        <div class="row d-flex p-1 justify-content-between">
                            <div class="mb-5 col-12 text-left">
                                <h2 class="ubuntu-regular mb-3">Pesquisa</h2>
                                <form id="pesquisar" method="POST">
                                    <input class="d-inline p-2 ubuntu-regular" placeholder="Pesquisar atividade" type="search" name="pesquisa" id="pesquisa">
                                    <button class="d-inline btn btn-primary" type="submit" name="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                                </form>
                            </div>
                            
                            <div class="col-12">
                                <h2 class="ubuntu-regular mb-3">Resultados</h2>
                            </div>
                    '; 

                while($dados = $resultado->fetch_assoc()){
                    echo '
                        <div class="card-atividade mb-3 d-flex align-items-center justify-content-center mx-3 col-11 col-lg-5 p-4 border">
                                <article class="w-100">
                                    <header>
                                        <ul class="ubuntu-light d-flex justify-content-between align-items-center">
                                            <li><small><i class="fa-solid fa-calendar-days"></i> '.$dados['registro'].'</small></li>
                                            <li><small class="mr-3"><i class="fa-solid fa-clipboard-list"></i> 0</small><small><i class="fa-solid fa-eye"></i> 0</small></li>
                                        </ul>
                                    </header>
                                    <div class="my-3">
                                        <h3 class="ubuntu-bold mb-2"> '.$dados['nome'].'</h3> 
                                        <span class="d-block ubuntu-regular">Atividade contendo '.$dados['questoes'].' questões de '.$dados['alternativas'].' alternativas.</span></span>
                                    </div>
                                    <footer>
                                        <ul class="p-2 d-flex justify-content-around">                                            
                                            <li class="resultados"><a href="atividades/resultados-atividade.php?id='.$dados['id'].'"><i class="fa-solid fa-chart-simple mr-1"></i>Resultados</a></li>
                                            <li class="editar"><a href="atividades/editar-atividade.php?id='.$dados['id'].'"><i class="fa-solid fa-pen mr-1"></i>Editar</a></li>
                                        </ul>
                                    </footer>
                                </article>
                            </div>  
                    '; 
                }
            }
        } else {
            $professor = $_SESSION['nome-usuario'];
            $consulta = "SELECT * FROM atividades WHERE professor = '$professor'";
            
            if($resultado = $mysqli->query($consulta)){
                echo '
                        <div class="row d-flex p-1 justify-content-between">
                            <div class="mb-5 col-12 text-left">
                                <h2 class="ubuntu-regular mb-3">Pesquisa</h2>
                                <form id="pesquisar" method="POST">
                                    <input class="d-inline p-2 ubuntu-regular" placeholder="Pesquisar atividade" type="search" name="pesquisa" id="pesquisa">
                                    <button class="d-inline btn btn-primary" type="submit" name="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                                </form>
                            </div>
                            
                            <div class="col-12">
                                <h2 class="ubuntu-regular mb-3">Recentes</h2>
                            </div>
                    '; 

                while($dados = $resultado->fetch_assoc()){
                    echo '
                            <div class="card-atividade mb-3 d-flex align-items-center justify-content-center mx-3 col-11 col-lg-5 p-4 border">
                                <article class="w-100">
                                    <header>
                                        <ul class="ubuntu-light d-flex justify-content-between align-items-center">
                                            <li><small><i class="fa-solid fa-calendar-days"></i> '.$dados['registro'].'</small></li>
                                            <li><small class="mr-3"><i class="fa-solid fa-clipboard-list"></i> 0</small><small><i class="fa-solid fa-eye"></i> 0</small></li>
                                        </ul>
                                    </header>
                                    <div class="my-3">
                                        <h3 class="ubuntu-bold mb-2"> '.$dados['nome'].'</h3> 
                                        <span class="d-block ubuntu-regular">Atividade contendo '.$dados['questoes'].' questões de '.$dados['alternativas'].' alternativas.</span></span>
                                    </div>
                                    <footer>
                                        <ul class="p-2 d-flex justify-content-around">                                            
                                            <li class="resultados"><a href="atividades/resultados-atividade.php?id='.$dados['id'].'"><i class="fa-solid fa-chart-simple mr-1"></i>Resultados</a></li>
                                            <li class="editar"><a href="atividades/editar-atividade.php?id='.$dados['id'].'"><i class="fa-solid fa-pen mr-1"></i>Editar</a></li>
                                        </ul>
                                    </footer>
                                </article>
                            </div>  
                    '; 
                }
            }
        }
    }
?>