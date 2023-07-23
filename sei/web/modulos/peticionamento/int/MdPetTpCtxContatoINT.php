<?
/**
* ANATEL
*
* 29/06/2016 - criado por marcelo.bezerra@castgroup.com.br - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetTpCtxContatoINT extends InfraINT {

	public static function montarSelectNome($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){

		//ajustes SEIv3
		$objTipoContextoContatoDTO = new TipoContatoDTO();
		$objTipoContextoContatoDTO->retNumIdTipoContato();
		$objTipoContextoContatoDTO->retStrNome();
		$objTipoContextoContatoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
	    $objTipoContextoContatoDTO->setNumMaxRegistrosRetorno(50);
		if ($strValorItemSelecionado!=null){
	
			$objTipoContextoContatoDTO->setBolExclusaoLogica(false);
			$objTipoContextoContatoDTO->adicionarCriterio(array('SinSistema', 'SinAtivo','Nome'),
					array( InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_LIKE),
					array('N', 'S', '%' . $strValorItemSelecionado . '%'),
					array( InfraDTO::$OPER_LOGICO_AND , InfraDTO::$OPER_LOGICO_AND ) );
		} else {
			
			$objTipoContextoContatoDTO->setBolExclusaoLogica(false);
			
			$objTipoContextoContatoDTO->adicionarCriterio(array('SinSistema', 'SinAtivo' ),
					array( InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL ),
					array('N', 'S' ),
					InfraDTO::$OPER_LOGICO_AND );			
		}
		
		//ajustes SEIv3
		$objTipoContextoContatoRN = new TipoContatoRN();
		$arrObjTipoContextoContatoDTO = $objTipoContextoContatoRN->listarRN0337($objTipoContextoContatoDTO);
	    return $arrObjTipoContextoContatoDTO;
		
	}
	
	public static function montarSelectTipoInteressado($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $strTipo){
		
		$objMdPetRelTpCtxContatoDTO = new MdPetRelTpCtxContatoDTO();
		$objMdPetRelTpCtxContatoDTO->retNumIdTipoContextoContato();
		$objMdPetRelTpCtxContatoDTO->retStrNomeTipoContexto();
		$objMdPetRelTpCtxContatoDTO->setOrdStrNomeTipoContexto(InfraDTO::$TIPO_ORDENACAO_ASC);
		
		if( $strTipo == 'Cadastro' ){
			$objMdPetRelTpCtxContatoDTO->setStrSinCadastroInteressado('S');
		} else {
			$objMdPetRelTpCtxContatoDTO->setStrSinSelecaoInteressado('S');			
		}
		
		$objMdPetRelTpCtxContatoDTO->setStrSinSistema('N');
		
		$objMdPetTpCtxContatoRN = new MdPetTpCtxContatoRN();
		$arrObjTipoContextoContatoDTO = $objMdPetTpCtxContatoRN->listar($objMdPetRelTpCtxContatoDTO);

	   	return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjTipoContextoContatoDTO , 'IdTipoContextoContato', 'NomeTipoContexto');
	}

	// semelhante a CargoINT::montarSelectGenero, mas traz somente aqueles com Tratamento e Vocativo preenchido	
	public static function montarSelectGeneroComTratamentoEVocativo($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $strStaGenero){
		$objCargoDTO = new CargoDTO();
		$objCargoDTO->retNumIdCargo();
		$objCargoDTO->retStrExpressao();
		if ($strStaGenero!=''){
			$objCargoDTO->adicionarCriterio(array('StaGenero','IdTratamento','IdVocativo'),
											array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_DIFERENTE, InfraDTO::$OPER_DIFERENTE),
											array($strStaGenero, null, null),
											array(InfraDTO::$OPER_LOGICO_AND, InfraDTO::$OPER_LOGICO_AND)
											);
		}
		$objCargoDTO->setOrdStrExpressao(InfraDTO::$TIPO_ORDENACAO_ASC);

		$objCargoRN = new CargoRN();
		$arrObjCargoDTO = $objCargoRN->listarRN0302($objCargoDTO);

		return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjCargoDTO, 'IdCargo', 'Expressao');
	}

}