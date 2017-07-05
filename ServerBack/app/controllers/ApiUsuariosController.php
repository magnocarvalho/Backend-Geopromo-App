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
        if(sizeof($cliente) > 0)
            echo jsonSerialize(true);
        else
            echo jsonSerialize(false);
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

        // Obtém os dados do cliente no banco, contendo o ID gerado
        $cliente = $cliente->where('email = ?', [$cliente->getEmail()])->find()[0];

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

        // Cria a sessão do usuário
        Auth::createAuthSession($cliente);

        echo jsonSerialize(true);
    }

    // Função que realiza o logout do usuário
    public function logout () {
        Auth::doLogout();
        echo jsonSerialize(true);
    }
}