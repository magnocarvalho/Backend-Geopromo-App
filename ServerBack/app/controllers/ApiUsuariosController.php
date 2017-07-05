<?php defined('INITIALIZED') OR exit('You cannot access this file directly');

class ApiUsuariosController extends Controller {

    // Verifica se o usuário está logado
    public function getAuth () {
        echo jsonSerialize(Auth::isLogged());
    }


    // Verifica se o email digitado/recebido já está cadastrado no banco de dados
    public function checkEmail ($params) {
        // Busca o cliente pelo email recebido
        $cliente = (new Cliente())->where('email = ?', [$params['id']])->find();

        // Retorna com base na existência ou não de registros no BD
        if(sizeof($cliente) > 0)
            echo jsonSerialize(true);
        else
            echo jsonSerialize(false);
    }


    // Armazena o email para ser registrado
    public function registroEmail ($params) {
        $cliente = new Cliente();
        $cliente->setEmail($params['id']);

        session('usuarioRegistro', serialize($cliente));
        echo jsonSerialize(true);
    }

    // Armazena a senha para ser registrada
    public function registroSenha ($params) {
        $cliente = unserialize(session('usuarioRegistro'));
        $cliente->setSenha(Auth::hashPassword($params['id']));

        $cliente->save(); // Salva no banco

        // Obtém os dados do cliente no banco, contendo o ID gerado
        $cliente = $cliente->where('email = ?', [$cliente->getEmail()])->find()[0];

        session('usuarioRegistro', serialize($cliente->getID()));
        echo jsonSerialize(true);
    }

    // Armazena os demais dados do usuário
    public function registroDados ($params) {
        // Obtém a data de nascimento informada e a formata para o padrão do BD
        $arrData = explode('-', $params[0]);
        $arrData = [$arrData[2], $arrData[1], $arrData[0]];
        $dataNasc = implode('-', $arrData);


        $idCliente = unserialize(session('usuarioRegistro'));

        // Obtém os dados do cliente no banco, contendo o ID gerado
        $cliente = (new Cliente())->get($idCliente);

        $cliente->setNome($params['id']);
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