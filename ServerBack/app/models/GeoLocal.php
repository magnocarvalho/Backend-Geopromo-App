<?php  defined('INITIALIZED') OR exit('You cannot access this file directly');

class GeoLocal extends Model {


    public function pegaProximos($lat, $long, $raio) {
        $empresas = DB::all('vendedor');

        $dados = array();
        $i = 0;
        foreach ($empresas as $empresa) {
            $dist = $this->calcDistancia($lat, $long, $empresa->getLatitude(), $empresa->getLongitude());

            if($dist <= (float) $raio){
                $promocoes = (new Promocao())->where(
                    'id_vendedor = ? AND inicio <= ? AND fim >= ?',
                    [$empresa->getId(), date('Y-m-d'), date('Y-m-d')]
                )->find();

                foreach($promocoes as $promocao){
                    $dados['promo'][$i]['id'] = $promocao->getId();
                    $dados['promo'][$i]['empresa'] = $empresa->getEstabelecimento();
                    $dados['promo'][$i]['titulo'] = $promocao->getTitulo();
                    $dados['promo'][$i]['descricao'] = $promocao->getDescricao();
                    $dados['promo'][$i]['dist'] = number_format(
                        $dist*1000,
                        0,
                        ',',
                        '.'
                    ); // Distância em metros
                    $i++;
                }
            }
        }

        if(!empty($dados))
            $dados['promo'] = $this->ordenaVetorPorDistancia($dados['promo'], $raio);

        return $dados;
    }


    public function contaProximos($lat, $long, $raio) {
        $dados = $this->pegaProximos($lat, $long, $raio);

        // Verifica se foi encontrada alguma promoção e retorna o contador em caso positivo
        if(isset($dados['promo']))
            return count($dados['promo']);
        else
            return 0;
    }


    public function calcDistancia($lat1, $long1, $lat2, $long2) {
        $d2r = 0.017453292519943295769236;

        $dlong = ($long2 - $long1) * $d2r;
        $dlat = ($lat2 - $lat1) * $d2r;

        $temp_sin = sin($dlat/2.0);
        $temp_cos = cos($lat1 * $d2r);
        $temp_sin2 = sin($dlong/2.0);

        $a = ($temp_sin * $temp_sin) + ($temp_cos * $temp_cos) * ($temp_sin2 * $temp_sin2);
        $c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));

        return 6368.1 * $c;
    }


    public function valCoord($coord) {
        return preg_replace('/[^-0-9.]/', '', $coord);
    }


    public function ordenaVetorPorDistancia($vetor, $raio){
        $indiceMin = 0;
        $ordenado = array();
        $distsOrdenadas = array();
        $idsOrdenados = array();

        for($i = 0; $i < count($vetor); $i++){
            $disMin = $raio*1000;
            foreach($vetor as $key => $result){
                if($result['dist'] <= $disMin && !in_array($result['id'], $idsOrdenados)){
                    $disMin = $result['dist'];
                    $indiceMin = $key;
                }
            }

            $ordenado[] = $vetor[$indiceMin];
            $distsOrdenadas[] = $vetor[$indiceMin]['dist'];
            $idsOrdenados[] = $vetor[$indiceMin]['id'];
        }

        return $ordenado;
    }
}