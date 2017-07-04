<?php  defined('INITIALIZED') OR exit('You cannot access this file directly');

class Promocao extends Model {
	private $id;
	private $id_vendedor;
	private $titulo;
	private $descricao;
	private $icone;
	private $foto;
	private $desconto;
	private $valorDe;
	private $valorPor;
	private $segmentacao;
	private $alcance;
	private $custo;
	private $inicio;
	private $fim;
	private $codigo;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getIdVendedor()
    {
        return $this->id_vendedor;
    }

    public function setIdVendedor($id_vendedor)
    {
        $this->id_vendedor = $id_vendedor;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getIcone()
    {
        return $this->icone;
    }

    public function setIcone($icone)
    {
        $this->icone = $icone;
    }

    public function getFoto()
    {
        return $this->foto;
    }

    public function setFoto($foto)
    {
        $this->foto = $foto;
    }

    public function getDesconto()
    {
        return $this->desconto;
    }

    public function setDesconto($desconto)
    {
        $this->desconto = $desconto;
    }

    public function getValorDe()
    {
        return $this->valorDe;
    }

    public function setValorDe($valorDe)
    {
        $this->valorDe = $valorDe;
    }

    public function getValorPor()
    {
        return $this->valorPor;
    }

    public function setValorPor($valorPor)
    {
        $this->valorPor = $valorPor;
    }

    public function getSegmentacao()
    {
        return $this->segmentacao;
    }

    public function setSegmentacao($segmentacao)
    {
        $this->segmentacao = $segmentacao;
    }

    public function getAlcance()
    {
        return $this->alcance;
    }

    public function setAlcance($alcance)
    {
        $this->alcance = $alcance;
    }

    public function getCusto()
    {
        return $this->custo;
    }

    public function setCusto($custo)
    {
        $this->custo = $custo;
    }

    public function getInicio()
    {
        return $this->inicio;
    }

    public function setInicio($inicio)
    {
        $this->inicio = $inicio;
    }

    public function getFim()
    {
        return $this->fim;
    }

    public function setFim($fim)
    {
        $this->fim = $fim;
    }

    public function getCodigo()
    {
        return $this->codigo;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }
}