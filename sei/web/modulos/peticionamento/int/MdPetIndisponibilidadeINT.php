<?
/**
* ANATEL
*
* 30/03/2016 - criado por jaqueline.mendes@cast.com.br - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIndisponibilidadeINT extends InfraINT {

	public static function montarSelectProrrogacaoAutomaticaPrazos($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
		$objMdPetIndisponibilidadeRN = new MdPetIndisponibilidadeRN();
	
		$arrObjProrrogacaoAutomaticaPrazoDTO = $objMdPetIndisponibilidadeRN->listarValoresProrrogacao();
	
		return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjProrrogacaoAutomaticaPrazoDTO, 'SinProrrogacao', 'Descricao');
	
	}
	
	
	public static function formatarData($data, $fim){
		$dataArray = split(' ',$data);
		
		$data1 = split('/',$dataArray[0]); 
		$data2 = $data1[2] + '/' + $data1[1] + '/' + $data1[0];

		$date = new DateTime($data2);

		$hours = split(':', $dataArray[1]);
		
		$date->setTime($hours[0], $hours[1]);
		
		$value =  $date->format('d-m-Y - H:i');

		return $value;
	}

	public static function validarNumeroSEI($numeroSEI){


		$objDocumentoRN  = new DocumentoRN();
		$objDocumentoDTO = new DocumentoDTO();
		$numeroSEIFormt = '%'.trim($numeroSEI).'%';
		$objDocumentoDTO->setStrProtocoloDocumentoFormatado($numeroSEIFormt,  InfraDTO::$OPER_LIKE);
		$objDocumentoDTO->retDblIdProcedimento();
		$objDocumentoDTO->retDblIdDocumento();
		$objDocumentoDTO->retStrStaDocumento();
		$objDocumentoDTO->setNumMaxRegistrosRetorno('1');

		$objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);
		$idProcedimento  = !is_null($objDocumentoDTO) ? $objDocumentoDTO->getDblIdProcedimento() : null;

		$arr = ProtocoloINT::pesquisarLinkEditor($idProcedimento , false, $numeroSEI);
		$urlDocumento = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=documento_visualizar&id_procedimento=' . $idProcedimento . '&id_documento=' . 	$objDocumentoDTO->getDblIdDocumento() . '&arvore=1');
		$countAss=1;

		if ($objDocumentoDTO->getStrStaDocumento()==DocumentoRN::$TD_EDITOR_INTERNO){
			
			$objAssinaturaDTO = new AssinaturaDTO();
			$objAssinaturaDTO->setDblIdDocumento($objDocumentoDTO->getDblIdDocumento());
			$objAssinaturaDTO->retDthAberturaAtividade();
			$objAssinaturaDTO->setOrdDthAberturaAtividade(InfraDTO::$TIPO_ORDENACAO_ASC);
			$objAssinaturaRN = new AssinaturaRN();
			$countAss = $objAssinaturaRN->contarRN1324($objAssinaturaDTO);;
		}



		if(count($arr) > 0 && (!is_null($objDocumentoDTO))){
			$arr['DataAtual']    = InfraData::getStrDataAtual();
			$arr['UnidadeAtual'] = SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . ' - '.SessaoSEI::getInstance()->getStrDescricaoUnidadeAtual();
			$arr['UrlDocumento'] = $urlDocumento;
			if($countAss == 0){
				$arr['Assinatura'] = 'Documento sem assinatura.';
			}

		}


		$xml = InfraAjax::gerarXMLComplementosArray($arr);

		return $xml;
	}

	public static function validarPeriodoIndisp($dataInicio,$dataFim)
	{
		$dataInicio.=':00';
		$dataFim.=':00';

		$objMdPetIndisponibilidadeDTO = new MdPetIndisponibilidadeDTO();
		$objMdPetIndisponibilidadeDTO->adicionarCriterio(array('DataFim', 'DataInicio'),
			array(InfraDTO::$OPER_MAIOR_IGUAL, InfraDTO::$OPER_MENOR_IGUAL),
			array($dataInicio, $dataInicio),
			InfraDTO::$OPER_LOGICO_AND,'periodoInicial');

		$objMdPetIndisponibilidadeDTO->adicionarCriterio(array('DataFim','DataInicio'),
			array(InfraDTO::$OPER_MAIOR_IGUAL, InfraDTO::$OPER_MENOR_IGUAL),
			array($dataFim, $dataFim),
			InfraDTO::$OPER_LOGICO_AND, 'periodoFinal');

		$objMdPetIndisponibilidadeDTO->agruparCriterios(array('periodoInicial','periodoFinal'), InfraDTO::$OPER_LOGICO_OR);

		$objMdPetIndisponibilidadeDTO->setStrSinProrrogacao('S');
		$objMdPetIndisponibilidadeDTO->retNumIdIndisponibilidade();

		$objMdPetIndisponibilidadeRN = new MdPetIndisponibilidadeRN();
		$objMdPetIndisponibilidade = count($objMdPetIndisponibilidadeRN->listar($objMdPetIndisponibilidadeDTO)) > 0;

		if ($objMdPetIndisponibilidade) {
			$xml = "<Documento>";
			$xml .= "<validacao>S</validacao>";
			$xml .= "</Documento>";

			return $xml;
		}else{
			$xml = "<Documento>";
			$xml .= "<validacao>N</validacao>";
			$xml .= "</Documento>";

			return $xml;
		}

	}

	
}