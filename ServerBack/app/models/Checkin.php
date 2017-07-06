<?php  defined('INITIALIZED') OR exit('You cannot access this file directly');

class Checkin extends Model {
    private $id;
    private $id_cliente;
    private $id_promocao;
    private $datahora;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getIdCliente()
    {
        return $this->id_cliente;
    }

    public function setIdCliente($id_cliente)
    {
        $this->id_cliente = $id_cliente;
    }

    public function getIdPromocao()
    {
        return $this->id_promocao;
    }

    public function setIdPromocao($id_promocao)
    {
        $this->id_promocao = $id_promocao;
    }

    public function getDatahora()
    {
        return $this->datahora;
    }

    public function setDatahora($datahora)
    {
        $this->datahora = $datahora;
    }



}