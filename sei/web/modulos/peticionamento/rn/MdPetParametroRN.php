<?
/**
* ANATEL
*
* 25/11/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetParametroRN extends InfraRN {

	private $infraParametro;

	public function __construct() {
		parent::__construct ();
        $this->infraParametro = new InfraParametro($this->getObjInfraIBanco());
	}

	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}

	protected function getValorConectado($valor, $bolErroNaoEncontrado)
    {
        return $this->infraParametro->getValor($valor, $bolErroNaoEncontrado);
    }

    protected function consultarConectado(InfraParametroDTO $objInfraParametroDTO){
        $arrObjInfraParametro = $this->infraParametro->listarValores(array($objInfraParametroDTO->getStrNome()), false);
        list($nome, $valor) = each($arrObjInfraParametro);
        $objInfraParametroDTO = new InfraParametroDTO();
        $objInfraParametroDTO->setStrNome($nome);
        $objInfraParametroDTO->setStrValor($valor);
        return $objInfraParametroDTO;
    }

    protected function listarValoresConectado($arrNomes = null, $bolErroNaoEncontrado = true) {
        $arrKeyValueParametro = $this->infraParametro->listarValores($arrNomes, $bolErroNaoEncontrado);
        $retorno = array();
        foreach($arrKeyValueParametro as $key=>$value) {
            $objInfraParametroDTO = new InfraParametroDTO();
            $objInfraParametroDTO->setStrNome($key);
            $objInfraParametroDTO->setStrValor($value);
            $retorno[] = $objInfraParametroDTO;
        }
        return $retorno;
    }

}
?>