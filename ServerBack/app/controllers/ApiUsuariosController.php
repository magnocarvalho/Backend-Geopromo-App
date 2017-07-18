<?php defined('INITIALIZED') OR exit('You cannot access this file directly');

class ApiUsuariosController extends Controller {

    // Verifica se o usuário está logado
    public function getAuth () {
        echo jsonSerialize(Auth::isLogged());
    }


    // Verifica se o email digitado/recebido já está cadastrado no banco de dados
    public function checkEmail () {
        // Busca o cliente pelo email recebido
        $cliente = (new Cliente())->where('email = ?', [$_POST['email']])->find();

        // Retorna com base na existência ou não de registros no BD
        if(sizeof($cliente) == 1) {
            echo jsonSerialize(true);
            session('usuarioLogin', serialize($cliente[0]->getID()));
        } else
            echo jsonSerialize(false);
    }

    // Checa a senha inserida
    public function checkSenha () {
        // Verifica se o tempo desde a última tentativa válida é maior ou menor a [2 horas]
        if(Auth::countTries() && (time() - session('loginTime')) < 60 * 60 * 2){
            echo jsonSerialize('-1');
        } else {
            $idCliente = unserialize(session('usuarioLogin'));

            // Obtém os dados do cliente no banco, contendo o ID gerado
            $cliente = (new Cliente())->get($idCliente);

            // Compara a senha digitada com a gravada no banco
            if (Auth::bindAuth(['email' => $cliente->getEmail(), 'senha' => $_POST['senha']], 'cliente')) {
                // Cria a sessão do usuário
                Auth::createAuthSession($cliente);
                echo jsonSerialize(true);
            } else {
                if (Auth::countTries()) {
                    // Se o máximo de tentativas (padrão: 5) for alcançado, cria uma var de sessão com o time atual
                    session('loginTime', time());

                    echo jsonSerialize('-1');
                } else {
                    echo jsonSerialize(false);
                }
            }
        }
    }


    // Armazena o email para ser registrado
    public function registroEmail () {
        $cliente = new Cliente();
        $cliente->setEmail($_POST['email']);

        session('usuarioRegistro', serialize($cliente));
        echo jsonSerialize(true);
    }

    // Armazena a senha para ser registrada
    public function registroSenha () {
        $cliente = unserialize(session('usuarioRegistro'));
        $cliente->setSenha(Auth::hashPassword($_POST['senha']));

        $cliente->save(); // Salva no banco

        session('usuarioRegistro', serialize($cliente->getID()));
        echo jsonSerialize(true);
    }

    // Armazena os demais dados do usuário
    public function registroDados () {
        // Obtém a data de nascimento informada e a formata para o padrão do BD
        $arrData = explode('-', $_POST['nascimento']);
        $arrData = [$arrData[2], $arrData[1], $arrData[0]];
        $dataNasc = implode('-', $arrData);


        $idCliente = unserialize(session('usuarioRegistro'));

        // Obtém os dados do cliente no banco, contendo o ID gerado
        $cliente = (new Cliente())->get($idCliente);

        $cliente->setNome($_POST['nome']);
        $cliente->setNascimento($dataNasc);

        $cliente->save(); // Salva no banco

        // Define a variável de sessão usada para o registro como nula
        session('usuarioRegistro', null);

        // Cria a sessão do usuário
        Auth::createAuthSession($cliente);

        echo jsonSerialize(true);
    }

    // Função que realiza o logout do usuário
    public function logout () {
        Auth::doLogout();
        echo jsonSerialize(true);
    }



    // Busca o histórico de promoções do usuário logado
    public function historicoPromos () {
        // Armazena em uma posição do vetor os cheques do dia atual e
        $checkins['hoje'] = (new Checkin())->where(
            'datahora BETWEEN ? AND ?',
            [date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59']
        )->orderBy('datahora', 'desc')->find();

        $checkins['anteriores'] = (new Checkin())->where(
            'datahora < ?',
            date('Y-m-d').' 00:00:00'
        )->orderBy('datahora', 'desc')->find();

        echo jsonSerialize($checkins);
    }

    // Retorna os dados do usuário logado, usado na página "minha conta"
    public function getInfoLoggedUser () {
        $cliente = Auth::getLoggedUser();
        $cliente->setSenha("");

        echo jsonSerialize($cliente);
    }

    // Atualiza os dados do usuário logado
    public function updateLoggedUser () {
        // Obtém a data de nascimento informada e a formata para o padrão do BD
        $arrData = explode('/', $_POST['nascimento']);
        $arrData = [$arrData[2], $arrData[1], $arrData[0]];
        $dataNasc = implode('-', $arrData);


        $cliente = Auth::getLoggedUser();
        $cliente->setNome($_POST['nome']);
        $cliente->setNascimento(date('Y-m-d', strtotime($dataNasc)));
        $cliente->save();

        Auth::createAuthSession($cliente);

        echo jsonSerialize(true);
    }

    // Atualiza a senha do usuário logado
    public function updateLoggedPassword () {
        $cliente = Auth::getLoggedUser();

        if($_POST['novasenha'] == $_POST['confirmacao'] && $_POST['novasenha'] != $_POST['senhaatual']) {
            if (Auth::bindAuth(['email' => $cliente->getEmail(), 'senha' => $_POST['senhaatual']], 'cliente')) {
                $cliente->setSenha(Auth::hashPassword($_POST['novasenha']));
                $cliente->save();

                Auth::createAuthSession($cliente);
                echo jsonSerialize(true);

            } else {
                echo jsonSerialize(-1);
            }
        } else {
            echo jsonSerialize(false);
        }
    }



    // LOGIN COM FACEBOOK
    public function fbLogin () {
        // Buscar no DB antes pelo provider e pelo uid (oauth_[...]) para definir o que fazer
        // Obter os dados, adicionar a um objeto Cliente ($cliente) e salvar no banco caso não exista
        // Por fim, iniciar sessão (Auth::createAuthSession($cliente);)

        $userData = json_decode($_POST['userData']);

        if(!empty($userData)){
            if(isset($userData->id) && $userData->id != '') {
                $provider = $_POST['provider'];

                $cliente = (new Cliente())->where(
                    'oauth_provider = ? AND oauth_uid = ?',
                    [$provider, $userData->id]
                )->find();


                if (count($cliente) == 0) {
                    // Insere o usuário no banco com os dados vindos do FB
                    $cliente = new Cliente();
                    @$cliente->setEmail($userData->email);
                    $cliente->setNome($userData->first_name . ' ' . $userData->last_name);
                    @$cliente->setNascimento(date('Y-m-d', strtotime($userData->birthday)));
                    $cliente->setOauthProvider('Facebook');
                    $cliente->setOauthUid($userData->id);
                    $cliente->save();
                } else {
                    // Atualiza alguns dados no banco com os dados vindos do FB

                    // "@" indica opcional. Pode ser que estes dados não venham, e aí ele não gera erros e não
                    // executa tais linhas
                    $cliente = $cliente[0];
                    @$cliente->setEmail($userData->email);
                    @$cliente->setNascimento(date('Y-m-d', strtotime($userData->birthday)));
                    $cliente->setNome($userData->first_name . ' ' . $userData->last_name);
                    $cliente->save();
                }

                Auth::createAuthSession($cliente);

                echo jsonSerialize(true);

            } else {
                echo jsonSerialize(false);
            }
        }
    }
}