<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 05/06/2008 - criado por fbv
*
* Versão do Gerador de Código: 1.17.0
*
* Versão no CVS: $Id$ 
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntercorrenteAtividadeRN extends AtividadeRN {



	protected function listarUnidadesTramitacaoControlado(ProcedimentoDTO $objProcedimentoDTO){
		try{

			$objAtividadeDTO = new AtividadeDTO();
			$objAtividadeDTO->setDistinct(true);
			$objAtividadeDTO->retNumIdUnidade();
			$objAtividadeDTO->retStrSiglaUnidade();
			$objAtividadeDTO->retStrDescricaoUnidade();

			$objAtividadeDTO->setNumIdTarefa(array(TarefaRN::$TI_GERACAO_PROCEDIMENTO,
                                                   TarefaRN::$TI_REABERTURA_PROCESSO_UNIDADE,
                                                   TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE,
                                                   TarefaRN::$TI_PROCESSO_CONCESSAO_CREDENCIAL,
                                                   TarefaRN::$TI_PROCESSO_CONCESSAO_CREDENCIAL_ANULADA),InfraDTO::$OPER_IN);

            if($objProcedimentoDTO->getStrStaEstadoProtocolo() == 3){
                $objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
                $objRelProtocoloProtocoloDTO->retDblIdProtocolo1();
                $objRelProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo1();
                $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($objProcedimentoDTO->getDblIdProcedimento());
                $objRelProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO);

                $objRelProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
                $objRelProtocoloProtocoloDTO = $objRelProtocoloProtocoloRN->consultarRN0841($objRelProtocoloProtocoloDTO);

                $idProcedimento = $objRelProtocoloProtocoloDTO->getDblIdProtocolo1();
            }else{
                $idProcedimento = $objProcedimentoDTO->getDblIdProcedimento();
            }

            $objAtividadeDTO->setDblIdProtocolo($idProcedimento);
			$objAtividadeDTO->setOrdStrSiglaUnidade(InfraDTO::$TIPO_ORDENACAO_ASC);

			$arrObjAtividadeDTO = $this->listarRN0036($objAtividadeDTO);

			if ( count($arrObjAtividadeDTO) > 0){
				$arrIdUnidade = InfraArray::converterArrInfraDTO($arrObjAtividadeDTO,'IdUnidade');
			}

            $objUnidadeDTO = new UnidadeDTO();
            $objUnidadeDTO->retTodos();
            $objUnidadeDTO->setBolExclusaoLogica(false);
            $objUnidadeDTO->setNumIdUnidade((array)$arrIdUnidade, InfraDTO::$OPER_IN);

            $objUnidadeRN = new UnidadeRN();
            $arrObjUnidadeDTO = $objUnidadeRN->listarRN0127($objUnidadeDTO);

			return $arrObjUnidadeDTO;

		}catch(Exception $e){
			throw new InfraException('Erro listando unidades de tramitação.',$e);
		}
	}


}
?>