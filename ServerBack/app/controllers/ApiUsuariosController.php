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

        session('usuarioRegistro', serialize($cliente));
        echo jsonSerialize(true);
    }
}