<?
/**
* ANATEL
*
* 29/06/2016 - criado por marcelo.bezerra@castgroup.com.br - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class GerirTipoContextoPeticionamentoINT extends InfraINT {

	public static function montarSelectNome($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
		$objTipoContextoContatoDTO = new TipoContextoContatoDTO();
		$objTipoContextoContatoDTO->retNumIdTipoContextoContato();
		$objTipoContextoContatoDTO->retStrNome();
		$objTipoContextoContatoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
	
		if ($strValorItemSelecionado!=null){
	
			$objTipoContextoContatoDTO->setBolExclusaoLogica(false);
			$objTipoContextoContatoDTO->adicionarCriterio(array('SinAtivo','Nome'),
					array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
					array('S',$strValorItemSelecionado),
					InfraDTO::$OPER_LOGICO_OR);
		}
		
		$objTipoContextoContatoRN = new TipoContextoContatoRN();
		$arrObjTipoContextoContatoDTO = $objTipoContextoContatoRN->listarRN0337($objTipoContextoContatoDTO);
	    return $arrObjTipoContextoContatoDTO;
		//return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjTipoContextoContatoDTO, 'IdTipoContextoContato', 'Nome');
	}
	
  
}
