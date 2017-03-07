<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4Є REGIГO
*
* 05/06/2008 - criado por fbv
*
* Versгo do Gerador de Cуdigo: 1.17.0
*
* Versгo no CVS: $Id$ 
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
																						 TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE,
																						 TarefaRN::$TI_PROCESSO_CONCESSAO_CREDENCIAL,
																						 TarefaRN::$TI_PROCESSO_CONCESSAO_CREDENCIAL_ANULADA),InfraDTO::$OPER_IN);

			$objAtividadeDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());

			//$objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual(),InfraDTO::$OPER_DIFERENTE);

			$objAtividadeDTO->setOrdStrSiglaUnidade(InfraDTO::$TIPO_ORDENACAO_ASC);

			$arrObjAtividadeDTO = $this->listarRN0036($objAtividadeDTO);

			foreach($arrObjAtividadeDTO as $objAtividadeDTO){
				$objAtividadeDTO->setDtaPrazo(null);
			}

			if (count($arrObjAtividadeDTO)>0){

				$arrObjAtividadeDTO = InfraArray::indexarArrInfraDTO($arrObjAtividadeDTO,'IdUnidade');

				$arrIdUnidade=InfraArray::converterArrInfraDTO($arrObjAtividadeDTO,'IdUnidade');

				//Acessar os retornos programados para a unidade atual
				$objRetornoProgramadoDTO = new RetornoProgramadoDTO();
				$objRetornoProgramadoDTO->setNumFiltroFkAtividadeRetorno(InfraDTO::$FILTRO_FK_WHERE);
				$objRetornoProgramadoDTO->retNumIdUnidade();
				$objRetornoProgramadoDTO->retDtaProgramada();
				$objRetornoProgramadoDTO->setNumIdUnidade($arrIdUnidade,InfraDTO::$OPER_IN);
				$objRetornoProgramadoDTO->setDblIdProtocoloAtividadeEnvio($objProcedimentoDTO->getDblIdProcedimento());
				$objRetornoProgramadoDTO->setNumIdUnidadeAtividadeEnvio(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
				$objRetornoProgramadoDTO->setNumIdUnidadeAtividadeRetorno(null);

				$objRetornoProgramadoRN = new RetornoProgramadoRN();
				$arrObjRetornoProgramadoDTO = $objRetornoProgramadoRN->listar($objRetornoProgramadoDTO);

				foreach ($arrObjRetornoProgramadoDTO as $objRetornoProgramadoDTO) {
					$arrObjAtividadeDTO[$objRetornoProgramadoDTO->getNumIdUnidade()]->setDtaPrazo($objRetornoProgramadoDTO->getDtaProgramada());
				}
			}

            $objUnidadeDTO = new UnidadeDTO();
            $objUnidadeDTO->retTodos();
            $objUnidadeDTO->setBolExclusaoLogica(false);
            $objUnidadeDTO->setNumIdUnidade(array_keys($arrObjAtividadeDTO), InfraDTO::$OPER_IN);

            $objUnidadeRN = new UnidadeRN();
            $arrObjUnidadeDTO = $objUnidadeRN->listarRN0127($objUnidadeDTO);

			return $arrObjUnidadeDTO;

		}catch(Exception $e){
			throw new InfraException('Erro listando unidades de tramitaзгo.',$e);
		}
	}


}
?>