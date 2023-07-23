<?php
/**
 * ANATEL
 *
 * 20/02/2017 - criado por marcelo.bezerra - CAST
 *
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntercorrenteReaberturaRN extends InfraRN {
	
	public function __construct(){
		parent::__construct();
	}
	
	protected function inicializarObjInfraIBanco(){
		return BancoSEI::getInstance();
	}
	
	/*
	 * Fun��o respons�vel por verificar se o processo n�o est� aberto em nenhuma unidade e se � necess�rio reabri-lo para poder inserir documentos nele
	 * */
	public function isNecessarioReabrirProcedimentoConectado ( ProcedimentoDTO $objProcedimentoDTO ){
		
		$objSEIRN = new SeiRN();
		//Reabre o Processo quando necess�rio de Crit�rio Intercorrente
		$objEntradaConsultaProcApi = new EntradaConsultarProcedimentoAPI();
		$objEntradaConsultaProcApi->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
		$objEntradaConsultaProcApi->setSinRetornarUnidadesProcedimentoAberto('S');
		
		$ret = $objSEIRN->consultarProcedimento($objEntradaConsultaProcApi);
		$arrUnidadesAberto = $ret->getUnidadesProcedimentoAberto();
				
		if( count( $arrUnidadesAberto ) == 0 ){
			
			return true;
		}
		
		return false;
		
	}
	
	/*
	 * Fun��o respons�vel pela reabertura de processo
	 * */
	protected function reabrirProcessoApiConectado(ProcedimentoDTO $objProcedimentoDTO) {
		
		$objSEIRN = new SeiRN();
		//Reabre o Processo quando necess�rio de Crit�rio Intercorrente
		$objEntradaConsultaProcApi = new EntradaConsultarProcedimentoAPI();
		$objEntradaConsultaProcApi->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
		$objEntradaConsultaProcApi->setSinRetornarUnidadesProcedimentoAberto('S');
	
		$ret = $objSEIRN->consultarProcedimento($objEntradaConsultaProcApi);
		$arrUnidadesAberto = $ret->getUnidadesProcedimentoAberto();
		$unidadesAberto = count($arrUnidadesAberto);
	
		if ($unidadesAberto < 0) {
			return false;
		}
		
		$objAtividadeDTO = new AtividadeDTO();
		$objAtividadeDTO->setDblIdProcedimentoProtocolo($objProcedimentoDTO->getDblIdProcedimento());
		$idUnidadeReabrirProcesso = $this->retornaUltimaUnidadeProcessoConcluido($objAtividadeDTO);
	
		$unidadeDTO = new UnidadeDTO();
		$unidadeDTO->retTodos();
		$unidadeDTO->setBolExclusaoLogica(false);
		$unidadeDTO->setNumIdUnidade($idUnidadeReabrirProcesso);
		$unidadeRN = new UnidadeRN();
		$objUnidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);

		if($objUnidadeDTO->getStrSinAtivo() == 'N'){
			$idUnidadeReabrirProcesso = null;

			$objMdPetAtividadeRN = new MdPetAtividadeRN();

			$arrObjMdPetAtividadeDTO = $objMdPetAtividadeRN->listarUnidadesTramitacao($objProcedimentoDTO);

			foreach ($arrObjMdPetAtividadeDTO as $itemObjMdPetAtividadeDTO) {
				$unidadeDTO = new UnidadeDTO();
				$unidadeDTO->retNumIdUnidade();
				$unidadeDTO->retStrSinAtivo();
				$unidadeDTO->setBolExclusaoLogica(false);
				$unidadeDTO->setNumIdUnidade($itemObjMdPetAtividadeDTO->getNumIdUnidade());
				$unidadeRN = new UnidadeRN();
				$objUnidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);
				if (count($objUnidadeDTO)==1 && $objUnidadeDTO->getStrSinAtivo() == 'S') {
					$idUnidadeReabrirProcesso = $objUnidadeDTO->getNumIdUnidade();
				}
			}
		}

		if (!$idUnidadeReabrirProcesso) {
			return false;
		}
	
		$this->simularLogin($idUnidadeReabrirProcesso);
	
		$objEntradaReabrirProcessoAPI = new EntradaReabrirProcessoAPI();
		$objEntradaReabrirProcessoAPI->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
		$objEntradaReabrirProcessoAPI->setProtocoloProcedimento($objProcedimentoDTO->getStrProtocoloProcedimentoFormatado());
	
		$objSEIRN->reabrirProcesso($objEntradaReabrirProcessoAPI);
		return true;
	}

    private function simularLogin($idUnidade)
    {
        SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(), $idUnidade);
    }
    
    /**
     * Retorna a ultima unidade que o processo foi
     * Pesquisa o processo exatamente como foi digitado SEM considerar a formata��o
     * @access protected
     * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
     * @param  ProtocoloDTO $parObjProtocoloDTO
     * @return mixed
     */
    protected function retornaUltimaUnidadeProcessoConcluidoConectado(AtividadeDTO $objAtividadeDTO){
    
    	$idUnidadeReabrirProcesso = null;
    	$objAtividadeRN  = new AtividadeRN();
    	$objAtividadeDTO->retDthConclusao();
    	$objAtividadeDTO->retNumIdUnidade();
    	$objAtividadeDTO->setOrdDthConclusao(InfraDTO::$TIPO_ORDENACAO_DESC);
    	
    	//s� considerar para reaburtura se a tarefa for 28, 41 ou 63 a saber:
    	//- 28: Conclus�o do processo na unidade
    	//- 41: Conclus�o autom�tica de processo na unidade
    	//- 63: Processo conclu�do
    	
    	$objAtividadeDTO->setNumIdTarefa( array( TarefaRN::$TI_CONCLUSAO_PROCESSO_UNIDADE , TarefaRN::$TI_CONCLUSAO_AUTOMATICA_UNIDADE, TarefaRN::$TI_CONCLUSAO_PROCESSO_USUARIO), InfraDTO::$OPER_IN );
    	
    	$arrObjAtividadeDTO = $objAtividadeRN->listarRN0036($objAtividadeDTO);
    	$objUltimaAtvProcesso = count($arrObjAtividadeDTO) > 0 ? current($arrObjAtividadeDTO) : null;
    	if(!is_null($objUltimaAtvProcesso)) {
    		$idUnidadeReabrirProcesso = $objUltimaAtvProcesso->getNumIdUnidade();
    	}
    
    	return $idUnidadeReabrirProcesso;
    }
}