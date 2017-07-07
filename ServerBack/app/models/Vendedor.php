<?php  defined('INITIALIZED') OR exit('You cannot access this file directly');

class Vendedor extends Model {
    private $id;
    private $email;
    private $senha;
    private $estabelecimento;
    private $latitude;
    private $longitude;
    private $telefone;
    private $logotipo;
    private $foto_fachada;
    private $ativo;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
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

    public function getEstabelecimento()
    {
        return $this->estabelecimento;
    }

    public function setEstabelecimento($estabelecimento)
    {
        $this->estabelecimento = $estabelecimento;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    public function getTelefone()
    {
        return $this->telefone;
    }

    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
    }

    public function getLogotipo()
    {
        return $this->logotipo;
    }

    public function setLogotipo($logotipo)
    {
        $this->logotipo = $logotipo;
    }

    public function getFotoFachada()
    {
        return $this->foto_fachada;
    }

    public function setFotoFachada($foto_fachada)
    {
        $this->foto_fachada = $foto_fachada;
    }

    public function getAtivo()
    {
        return $this->ativo;
    }

    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }


}