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
        $Nquestoes = htmlspecialchars($_SESSION['registro-atividade-questoes']);
        $Nalternativas = htmlspecialchars($_SESSION['registro-atividade-alternativas']); 
        $nome = htmlspecialchars($_SESSION['registro-atividade-nome']); 
        $professor = htmlspecialchars($_SESSION['nome-usuario']);
        $anonimo = htmlspecialchars($_SESSION['registro-atividade-anonimo']);
        $tipoAtividade = "Multipla Escolha";

        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])){
            $verificacaoENUNCIADOS = validarEnunciados($Nquestoes);
            $verificacaoALTERNATIVAS = validarALTERNATIVAS($Nquestoes, $Nalternativas);
            
            if($verificacaoENUNCIADOS == "" && $verificacaoALTERNATIVAS == ""){
                $consulta = "INSERT INTO atividades (id, tipo, anonimo, nome, professor, questoes, alternativas) VALUES ('$idAtividade', '$tipoAtividade', '$anonimo', '$nome', '$professor', '$Nquestoes', '$Nalternativas')";

                if($resultado = $mysqli->query($consulta)){
                    $Nalternativas = (int) $Nalternativas;
                    for($i = 0; $i <= $Nquestoes - 1; $i++){
                        $j = 1;
                        //salvar enunciado e alternatvias de cada questão
                        $enunciado = $mysqli->real_escape_string($_POST['enunciado-'.($i+1)]);
                        $correta = $mysqli->real_escape_string($_POST['alternativa-'.($i+1)."-".($Nalternativas)]);
                        $alternativa1 = $mysqli->real_escape_string($_POST['alternativa-'.($i+1)."-".($j)]);
                        $alternativa2 = $mysqli->real_escape_string($_POST['alternativa-'.($i+1)."-".($j+1)]);
                        $alternativa3 = $mysqli->real_escape_string($_POST['alternativa-'.($i+1)."-".($j+2)] ?? "");
                        $alternativa4 = $mysqli->real_escape_string($_POST['alternativa-'.($i+1)."-".($j+3)] ?? "");

                        $consulta = "INSERT INTO questoes (id_atividade, enunciado, alternativa1, alternativa2, alternativa3, alternativa4, correta) VALUES ('$idAtividade', '$enunciado', '$alternativa1', '$alternativa2', '$alternativa3', '$alternativa4', '$correta')";

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
            mostrarFormularioQuestoes($Nquestoes, $Nalternativas, $professor, $nome);
        }
    }
        //FUNÇÕES SECUNDÁRIAS PARA REGISTRAR ATIVIDADE
        function criarIDatividade($mysqli){
            $alfabeto = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
            $count = 0;
            do{ 
                $id = (string)rand(1000000000, 9999999999);
                for($i = 0; $i <= (mb_strlen($id) - 1); $i++){       
                    if($i % 2 == 0){
                        $count++;
                        $id[$i] = $alfabeto[$i];

                        if($count % 3 == 0){
                            $count = 0;
                            $id[$i] = (int)rand(0,9);
                        }
                    }
                }

                $consulta = "SELECT COUNT(*) AS total from atividades WHERE id = '$id'";

                if($resultado = $mysqli->query($consulta)){
                    $dados = $resultado->fetch_assoc();
                }    
            }while($dados['total'] > 0);

            return $id;
        }

        //FUNÇÃO QUE EXIBE O FORMULÁRIO PARA NOVA ATIVIDADE
        function mostrarFormularioQuestoes($Nquestoes, $Nalternativas, $professor, $nome){
                //abertura formulario
                $Nquestoes = (int)$Nquestoes;
                $Nalternativas = (int)$Nalternativas;
                echo '
                    <section id="registro" class="text-center">
                        <span class="ubuntu-bold d-block mb-4"><i class="fa-solid fa-graduation-cap fa-xl"></i><span>MAGISTER <sup class="ubuntu-light">®</sup></span></span>
                        <h1 class="ubuntu-bold texto-azul">'.$nome.'</h1>
                        <p class="ubuntu-regular mt-2 mb-4">Professor(a) '.$professor.' preencha a atividade</p>
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
            $pesquisa = $mysqli->real_escape_string($_POST['pesquisa']);
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
                    $id = $dados['id'];
                    $dadosAtividade = dadosAtividadepainel($mysqli, $id);
                    echo '
                        <div class="card-atividade mb-3 d-flex align-items-center justify-content-center mx-3 col-11 col-lg-5 p-4 border">
                                <article class="w-100">
                                    <header>
                                        <ul class="ubuntu-light d-flex justify-content-between align-items-center">
                                            <li><small><i class="fa-solid fa-calendar-days"></i> '.$dados['registro'].'</small></li>
                                            <li><small class="mr-3"><i class="fa-solid fa-clipboard-list mr-1"></i>'.$dadosAtividade[0].'</small><small><i class="fa-solid fa-eye mr-1"></i>'.$dadosAtividade[1].'</small></li>
                                        </ul>
                                    </header>
                                    <div class="my-3">
                                        <a href="atividade.php?id='.$dados['id'].'">
                                            <h3 class="ubuntu-bold mb-2"> '.$dados['nome'].'</h3> 
                                            <span class="d-block ubuntu-regular">Atividade contendo '.$dados['questoes'].' questões de '.$dados['alternativas'].' alternativas.</span></span>
                                        </a>
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
            $professor = $mysqli->real_escape_string($_SESSION['nome-usuario']);
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
                    $id = $dados['id'];
                    $dadosAtividade = dadosAtividadepainel($mysqli, $id);
                    echo '
                            <div class="card-atividade mb-3 d-flex align-items-center justify-content-center mx-3 col-11 col-lg-5 p-4 border">
                                <article class="w-100">
                                
                                    <header>
                                        <ul class="ubuntu-light d-flex justify-content-between align-items-center">
                                            <li><small><i class="fa-solid fa-calendar-days"></i> '.$dados['registro'].'</small></li>
                                            <li><small class="mr-3"><i class="fa-solid fa-clipboard-list mr-1"></i>'.$dadosAtividade[0].'</small><small><i class="fa-solid fa-eye mr-1"></i>'.$dadosAtividade[1].'</small></li>
                                        </ul>
                                    </header>
                                    <div class="my-3">
                                        <a href="atividade.php?id='.$dados['id'].'">
                                            <h3 class="ubuntu-bold mb-2"> '.$dados['nome'].'</h3> 
                                            <span class="d-block ubuntu-regular">Atividade contendo '.$dados['questoes'].' questões de '.$dados['alternativas'].' alternativas.</span></span>
                                        </a>
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

    //FUNÇÃO QUE EXIBE A ATIVIDADE PARA O ALUNO
    function exibirAtividade($mysqli){
        $id = htmlspecialchars($_GET['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])){
            $id = $mysqli->real_escape_string($id);
            $nome = $mysqli->real_escape_string($_POST['nome']);
            $ip = $_SERVER['REMOTE_ADDR'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'];

            $consulta = "SELECT questoes FROM atividades WHERE id = '$id'";

            if($resultado = $mysqli->query($consulta)){
                $dados = $resultado->fetch_assoc();
                
                $respostas = [''];
                for($i = 0; $i <= 4; $i++){
                    if(!empty($_POST['resposta-'.$i+1])){
                        $respostas[$i] = htmlspecialchars($_POST['resposta-'.$i+1]);
                    }
                }
            }
            
            $acertos = corrigirRespostas($mysqli, $id, $respostas);
            $consulta = "INSERT INTO resultados (id_atividade, ip, user_agent, acertos, nome_aluno) VALUES ('$id', '$ip', '$userAgent', '$acertos', '$nome')";

            if($resultado = $mysqli->query($consulta)){
                $_SESSION['envio-atividade-nome-usuario'] = $nome;
                $_SESSION['envio-atividade-acertos'] =  $acertos;

                header("Location: atividade-enviada.php?id='".$id);
            }

            echo $acertos;
        } else {
            $id = $mysqli->real_escape_string($id);

            $consulta = "SELECT anonimo FROM atividades WHERE id = '$id'";

            if($resultado = $mysqli->query($consulta)){
                $dados = $resultado->fetch_assoc();

                switch($dados['anonimo']){
                    case "off":
                        //exibir input nome, questoes, alternativas e opcao enviar
                        $consulta = "SELECT * FROM atividades WHERE id = '$id'";

                        if($resultado = $mysqli->query($consulta)){
                            $dados = $resultado->fetch_assoc();

                            $nome = $dados['nome'];
                            $Nquestoes = (int)$dados['questoes'];
                            $Nalternativas = (int)$dados['alternativas'];
                            $professor = $dados['professor'];
                            $data = $dados['registro'];

                            //abertura formulário
                            echo '
                                <section id="registro" class="text-center">
                                    <span class="ubuntu-bold d-block mb-4"><i class="fa-solid fa-graduation-cap fa-xl"></i><span>MAGISTER <sup class="ubuntu-light">®</sup></span></span>
                                    <h1 class="ubuntu-bold texto-azul">'.$nome.'</h1>
                                    <p class="ubuntu-regular mt-3 mb-4">Atividade com '.$Nquestoes.' questões criada por '.$professor.' em  <i>'.$data.'</i></p>
                                    
                                    <form id="registro-atividade" method="POST" class="form ubuntu-regular text-left mt-5">
                            '; 
                            //nome
                            echo '
                                <div class="form-group mb-5">
                                    <label for="nome" style="color: #249EF0 !important;"><h3 class="ubuntu-">Nome</h3></label>
                                    <input required name="nome" id="nome" placeholder="Insira seu nome completo" class="form-control ubuntu-regular" type="text" maxlength="100">
                                </div>
                                <hr><br><br>
                            ';

                            //questões e alternativas geradas de forma dinâmica
                            $consulta = "SELECT * FROM questoes WHERE id_atividade = '$id'";
                            $i = 0;

                            if($resultado = $mysqli->query($consulta)){
                                while($dados = $resultado->fetch_assoc()){
                                    $alternativas = [$dados['alternativa1'], $dados['alternativa2'], $dados['alternativa3'], $dados['alternativa4']];   
                                    shuffle($alternativas);
                                    $i++;

                                    echo '
                                        <div class="ubuntu-regular questao mb-5">
                                            <h3 class="texto-azul d-inline mr-2">Questão '.$i.'</h3><span class="ml-2 ubuntu-regular">'.$nome.'</span>
                                            <p class="ubuntu-bold mt-3">'.$dados['enunciado'].'</p>

                                            <ul class="ubuntu-regular mt-4">
                                    ';
                                        
                                    for($j = 0; $j <= 3; $j++){
                                        if(!empty($alternativas[$j])){
                                            echo '<li><p>'.$alternativas[$j].'</p></li>';
                                        }
                                    }

                                    echo '</ul>
                                        
                                    ';

                                    echo'
                                        <div class="form-group mt-4">
                                            <select required class="form-control" name="resposta-'.$i.'" id="resposta-'.$i.'">
                                            <option selected value="">Selecionar</option>
                                            ';
                                        
                                    for($j = 0; $j<= 3; $j++){
                                        if(!empty($alternativas[$j])){
                                            echo '<option value="'.$alternativas[$j].'">'.$alternativas[$j].'</option>';
                                        }
                                    }        

                                    echo'   </select>
                                            </div>
                                        </div>
                                        <hr><br><br>
                                    ';
                                }
                            }

                            //fechamento formulário
                            echo '
                                        <button class="btn btn-primary my-3 ubuntu-bold w-100" name="submit" type="submit">Enviar respostas</button>
                                    </form>
                                </section> 
                            ';
                        } else {
                            header("Location: ../../pages/erros/erro-conexao.php");
                            exit();
                        }
                    break;

                    case "on":
                        //exibir input nome, questoes, alternativas e opcao enviar
                        $consulta = "SELECT * FROM atividades WHERE id = '$id'";

                        if($resultado = $mysqli->query($consulta)){
                            $dados = $resultado->fetch_assoc();

                            $nome = $dados['nome'];
                            $Nquestoes = (int)$dados['questoes'];
                            $Nalternativas = (int)$dados['alternativas'];
                            $professor = $dados['professor'];
                            $data = $dados['registro'];

                            //abertura formulário
                            echo '
                                <section id="registro" class="text-center">
                                    <span class="ubuntu-bold d-block mb-4"><i class="fa-solid fa-graduation-cap fa-xl"></i><span>MAGISTER <sup class="ubuntu-light">®</sup></span></span>
                                    <h1 class="ubuntu-bold texto-azul">'.$nome.'</h1>
                                    <p class="ubuntu-regular mt-3 mb-4">Atividade com '.$Nquestoes.' questões criada por '.$professor.' em  <i>'.$data.'</i></p>
                                    
                                    <form id="registro-atividade" method="POST" class="form ubuntu-regular text-left mt-5">
                            '; 

                            //questões e alternativas geradas de forma dinâmica
                            $consulta = "SELECT * FROM questoes WHERE id_atividade = '$id'";
                            $i = 0;

                            if($resultado = $mysqli->query($consulta)){
                                while($dados = $resultado->fetch_assoc()){
                                    $alternativas = [$dados['alternativa1'], $dados['alternativa2'], $dados['alternativa3'], $dados['alternativa4']];   
                                    shuffle($alternativas);
                                    $i++;

                                    echo '
                                        <div class="ubuntu-regular questao mb-5">
                                            <h3 class="texto-azul d-inline mr-2">Questão '.$i.'</h3><span class="ml-2 ubuntu-regular">'.$nome.'</span>
                                            <p class="ubuntu-bold mt-3">'.$dados['enunciado'].'</p>

                                            <ul class="ubuntu-regular mt-4">
                                    ';
                                        
                                    for($j = 0; $j <= 3; $j++){
                                        if(!empty($alternativas[$j])){
                                            echo '<li><p>'.$alternativas[$j].'</p></li>';
                                        }
                                    }

                                    echo '</ul>
                                        
                                    ';

                                    echo'
                                        <div class="form-group mt-4">
                                            <select required class="form-control" name="resposta-'.$i.'" id="resposta-'.$i.'">
                                            <option selected value="">Selecionar</option>
                                            ';
                                        
                                    for($j = 0; $j<= 3; $j++){
                                        if(!empty($alternativas[$j])){
                                            echo '<option value="'.$alternativas[$j].'">'.$alternativas[$j].'</option>';
                                        }
                                    }        

                                    echo'   </select>
                                            </div>
                                        </div>
                                        <hr><br><br>
                                    ';
                                }
                            }

                            //fechamento formulário
                            echo '
                                        <button class="btn btn-primary my-3 ubuntu-bold w-100" name="submit" type="submit">Enviar respostas</button>
                                    </form>
                                </section> 
                            ';
                        } else {
                            header("Location: ../../pages/erros/erro-conexao.php");
                            exit();
                        }
                    break;
                    
                    case "":
                        header("Location: ../../pages/erros/atividade-inexistente.php");
                        exit();
                    break;
                }
            } else {
                header("Location: ../../pages/erros/erro-conexao.php");
                exit();
            }
        }
    }

    //FUNÇÃO QUE CORRIGE A ATIVIDADE E RETORNA O TOTAL DE ACERTOS
    function corrigirRespostas($mysqli, $id, $respostas){
        //pegar corretas das questoes id = e ver se são iguais as respostas, cada uma que for ++acerto;
        $id = $mysqli->real_escape_string($id);
        $consulta = "SELECT correta FROM questoes WHERE id_atividade = '$id'";

        if($resultado = $mysqli->query($consulta)){
            $acertos = 0;
            $i = 0;
            while($dados = $resultado->fetch_assoc()){
                $i++;
                if($dados['correta'] == $respostas[$i - 1]){
                    $acertos++;
                }
            }
        }
        return $acertos;
    }

    //FUNÇÃO QUE VERIFICA IP+USER_AGENT E ADICIONA VISUALIZAÇÃO P/ ATIVIDADE
    function adicionarVisualizacao($mysqli){
        $id = $mysqli->real_escape_string($_GET['id']);
        $ip = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $consulta = "SELECT COUNT(*) AS total FROM visualizacoes WHERE id_atividade = '$id' AND ip_visualizou = '$ip' AND user_agent_visualizou = '$userAgent'";

        if($resultado = $mysqli->query($consulta)){
            $dados = $resultado->fetch_assoc();

            if($dados['total'] == 0){
                $consulta = "INSERT INTO visualizacoes (id_atividade, ip_visualizou, user_agent_visualizou) VALUES ('$id', '$ip', '$userAgent')";
                $mysqli->query($consulta);
            }
        }
    }

    //FUNÇÃO QUE RETORNA O TOTAL DE RESULTADOS/VISUALIZACOES DE UMA ATIVIDADE
    function dadosAtividadepainel($mysqli, $id){
        $dadosAtividade = ["", ""];

        $consulta = "SELECT COUNT(*) AS total FROM resultados WHERE id_atividade = '$id'";

        $resultado = $mysqli->query($consulta);
        $dados = $resultado->fetch_assoc();
        $dadosAtividade[0] = $dados['total'];

        $consulta = "SELECT COUNT(*) AS total FROM visualizacoes WHERE id_atividade = '$id'";

        $resultado = $mysqli->query($consulta);
        $dados = $resultado->fetch_assoc();
        $dadosAtividade[1] = $dados['total'];
        
        return $dadosAtividade;
    }
?>