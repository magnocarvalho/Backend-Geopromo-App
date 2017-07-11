<?php  defined('INITIALIZED') OR exit('You cannot access this file directly');

class Cliente extends Model {
    private $id;
    private $nome;
    private $nascimento;
    private $foto;
    private $email;
    private $senha;
    private $oauth_provider;
    private $oauth_uid;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getNascimento()
    {
        return $this->nascimento;
    }

    public function setNascimento($nascimento)
    {
        $this->nascimento = $nascimento;
    }

    public function getFoto()
    {
        return $this->foto;
    }

    public function setFoto($foto)
    {
        $this->foto = $foto;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function setSenha($senha)
    {
        $this->senha = $senha;
    }

    public function getOauthProvider()
    {
        return $this->oauth_provider;
    }

    public function setOauthProvider($oauth_provider)
    {
        $this->oauth_provider = $oauth_provider;
    }

    public function getOauthUid()
    {
        return $this->oauth_uid;
    }

    public function setOauthUid($oauth_uid)
    {
        $this->oauth_uid = $oauth_uid;
    }


}