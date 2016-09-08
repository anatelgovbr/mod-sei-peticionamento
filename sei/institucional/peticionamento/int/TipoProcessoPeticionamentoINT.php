<?
/**
* ANATEL
*
* 30/03/2016 - criado por jaqueline.mendes@cast.com.br - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class TipoProcessoPeticionamentoINT extends InfraINT {

	public static function montarSelectIndicacaoInteressadoPeticionamento($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
		$objTipoProcessoPeticionamentoRN      = new TipoProcessoPeticionamentoRN();
	
		$arrObjIndicacaoInteressadaDTO = $objTipoProcessoPeticionamentoRN->listarValoresIndicacaoInteressado();
	
		return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjIndicacaoInteressadaDTO, 'SinIndicacao', 'Descricao');
	
	}
	
	
	public static function montarSelectTipoDocumento($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
		$objTipoProcessoPeticionamentoRN      = new TipoProcessoPeticionamentoRN();
	
		$arrObjTipoDocumentoPeticionamentDTO = $objTipoProcessoPeticionamentoRN->listarValoresTipoDocumento();

		return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjTipoDocumentoPeticionamentDTO, 'TipoDoc', 'Descricao');
	
	}
	
	public static function montarSelectTipoProcesso($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
		$objTipoPeticionamentoRN  = new TipoProcedimentoRN();
		
		$objTipoProcedimento      = new TipoProcedimentoDTO();
		$objTipoProcedimento->retTodos();
		//listarRN0244Conectado
		$arrObjTiposProcessoDTO = $objTipoPeticionamentoRN->listarRN0244($objTipoProcedimento);
		
		return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjTiposProcessoDTO, 'IdTipoProcedimento', 'Nome');
		
	}
		
	public static function montarSelectHipoteseLegal($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado ){

		$peticionamento = false;
		$objHipoteseLegalPeticionamentoDTO = new HipoteseLegalPeticionamentoDTO();
		$objHipoteseLegalPeticionamentoRN  = new HipoteseLegalPeticionamentoRN();
		$objHipoteseLegalPeticionamentoDTO->retTodos();
		$countHipotesesPeticionamento = $objHipoteseLegalPeticionamentoRN->contar($objHipoteseLegalPeticionamentoDTO);
		
		if($countHipotesesPeticionamento > 0)
		{
			$peticionamento = true;
			$objHipoteseLegalPeticionamentoDTO->retStrNome();
			$objHipoteseLegalPeticionamentoDTO->retStrBaseLegal();
			$arrHipoteses = $objHipoteseLegalPeticionamentoRN->listar($objHipoteseLegalPeticionamentoDTO);
		}
		else
		{
			$objHipoteseLegalRN = new HipoteseLegalRN();
			$objHipoteseLegalCoreDTO = new HipoteseLegalDTO();
		
			$objHipoteseLegalCoreDTO->retTodos();		
			$objHipoteseLegalCoreDTO->setStrStaNivelAcesso( ProtocoloRN::$NA_RESTRITO );
			$objHipoteseLegalCoreDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
			$arrHipoteses = $objHipoteseLegalRN->listar( $objHipoteseLegalCoreDTO );
		}
		
		$stringFim = '<option value=""> </option>';
		if(count($arrHipoteses) > 0 ){
			
			foreach($arrHipoteses as $objHipoteseLegalDTO){
				
				$idHipoteseLegal = $peticionamento ? $objHipoteseLegalDTO->getNumIdHipoteseLegalPeticionamento() : $objHipoteseLegalDTO->getNumIdHipoteseLegal();
				
				if(!is_null($strValorItemSelecionado) &&  $strValorItemSelecionado == $idHipoteseLegal){
				    $stringFim .= '<option value="' . $idHipoteseLegal . '" selected="selected">' . $objHipoteseLegalDTO->getStrNome() . ' (' . $objHipoteseLegalDTO->getStrBaseLegal() .')';
				} else {
					$stringFim .= '<option value="' . $idHipoteseLegal . '">' . $objHipoteseLegalDTO->getStrNome() . ' (' . $objHipoteseLegalDTO->getStrBaseLegal() .  ')';
				}
				$stringFim .= '</option>';
				
			}
		}
		
		return $stringFim;
	}
	
	public static function montarSelectNivelAcesso($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $idTipoProcedimento = null){
		$objNivelAcessoRN  = new NivelAcessoPermitidoRN();
	
		$objNivelAcessoDTO = new NivelAcessoPermitidoDTO();
		$objNivelAcessoDTO->retTodos();
		
		if(!(is_null($idTipoProcedimento))){
			$objNivelAcessoDTO->setNumIdTipoProcedimento($idTipoProcedimento);
		}
		
		//listarRN0244Conectado
		$arrObjNivelAcessoDTO = $objNivelAcessoRN->listar($objNivelAcessoDTO);
		//montarItemSelect
		
		$stringFim = '';
		$arrayDescricoes = array();
		$arrayDescricoes[ProtocoloRN::$NA_PUBLICO] = 'Público';
		$arrayDescricoes[ProtocoloRN::$NA_RESTRITO] = 'Restrito';
		$arrayDescricoes[ProtocoloRN::$NA_SIGILOSO] = 'Sigiloso';
		$arrayDescricoes[''] = '';
		
		$stringFim = '<option value=""> </option>';
		
		if(count($arrObjNivelAcessoDTO) > 0 ){
			foreach($arrObjNivelAcessoDTO as $objNivelAcessoDTO){
			  $stringFim .= '<option value="'.$objNivelAcessoDTO->getStrStaNivelAcesso().'"';
			  
			  if(!is_null($strValorItemSelecionado) &&  ($strValorItemSelecionado == $objNivelAcessoDTO->getStrStaNivelAcesso())){
			  	$stringFim .= 'selected = selected';
			  }
			  
			  $stringFim .= '>';
			  $stringFim .= $arrayDescricoes[$objNivelAcessoDTO->getStrStaNivelAcesso()];
			  
			  $stringFim .= '</option>';
			}
		}
	
		return $stringFim;
	}
	
	
}