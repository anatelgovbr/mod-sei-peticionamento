  <?
/**
* ANATEL
*
* 08/12/2017 - criado por jaqueline.mendes - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIndisponibilidadeDocRN extends InfraRN {
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}

	protected function cadastrarControlado(MdPetIndisponibilidadeDocDTO $objMdPetIndisponibilidadeDocDTO) {
		
		try{
	
			//Valida Permissao
		//	SessaoSEI::getInstance()->validarAuditarPermissao('anexo_cadastrar',__METHOD__,$objIndisponibilidadeProtDTO);
	
			//Regras de Negocio
			$objInfraException = new InfraException();

			$objInfraException->lancarValidacoes();
			
			$objMdPetIndisponibilidadeDocBD = new MdPetIndisponibilidadeDocBD($this->getObjInfraIBanco());
			$ret = $objMdPetIndisponibilidadeDocBD->cadastrar($objMdPetIndisponibilidadeDocDTO);

			return $ret;
			//Auditoria
	
		}catch(Exception $e){
			throw new InfraException('Erro cadastrando Documento.',$e);
		}
	}
	

	protected function listarConectado(MdPetIndisponibilidadeDocDTO $objMdPetIndisponibilidadeDocDTO) {
		try {
	
			//Valida Permissao
			//SessaoSEI::getInstance()->validarAuditarPermissao('indisponibilidade_anexo_listar',__METHOD__,$objAnexoDTO);
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
	
			$objMdPetIndisponibilidadeDocBD = new MdPetIndisponibilidadeDocBD($this->getObjInfraIBanco());
			$ret = $objMdPetIndisponibilidadeDocBD->listar($objMdPetIndisponibilidadeDocDTO);
	
			//Auditoria
	
			return $ret;
	
		}catch(Exception $e){
			throw new InfraException('Erro listando Documentos.',$e);
		}
	}
	
	protected function contarConectado(MdPetIndisponibilidadeDocDTO $objMdPetIndisponibilidadeDocDTO) {

		try {

			$objMdPetIndisponibilidadeDocBD = new MdPetIndisponibilidadeDocBD($this->getObjInfraIBanco());
			$ret = $objMdPetIndisponibilidadeDocBD->contar($objMdPetIndisponibilidadeDocDTO);
			return $ret;

		}catch(Exception $e){
			throw new InfraException('Erro listando Documentos.',$e);
		}
	}
	
	protected function consultarConectado(MdPetIndisponibilidadeDocDTO $objMdPetIndisponibilidadeDocDTO) {
		
		try {
			$objMdPetIndisponibilidadeDocBD = new MdPetIndisponibilidadeDocBD($this->getObjInfraIBanco());
			$ret = $objMdPetIndisponibilidadeDocBD->consultar($objMdPetIndisponibilidadeDocDTO);
			return $ret;
	
		}catch(Exception $e){
			throw new InfraException('Erro listando Documentos.',$e);
		}
	}
	
	protected function excluirControlado($arrObjMdPetIndisponibilidadeDocDTO){
		try {
			//Valida Permissao
			//SessaoSEI::getInstance()->validarAuditarPermissao('anexo_excluir',__METHOD__,$arrObjAnexoDTO);
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
	
			$objMdPetIndisponibilidadeDocBD = new MdPetIndisponibilidadeDocBD($this->getObjInfraIBanco());
			for($i=0;$i<count($arrObjMdPetIndisponibilidadeDocDTO);$i++){
				$objMdPetIndisponibilidadeDocBD->excluir($arrObjMdPetIndisponibilidadeDocDTO[$i]);
			}
	
			//Auditoria
	
		}catch(Exception $e){
			throw new InfraException('Erro excluindo Documento.',$e);
		}
	}
	
	protected function consultarIndisponibilidadeDocPorIdConectado($arrParams)
	{

		$idIndisponibilidade = $arrParams[0];
		$sessaoInterna       = array_key_exists('1', $arrParams) ? $arrParams[1] : true;

		$objMdPetIndisponibilidadeDocDTO = new  MdPetIndisponibilidadeDocDTO();
		$objMdPetIndisponibilidadeDocDTO->setNumIdIndisponibilidade($idIndisponibilidade);
		$objMdPetIndisponibilidadeDocDTO->setNumMaxRegistrosRetorno(1);
		$objMdPetIndisponibilidadeDocDTO->retNumIdProtPeticionamento();
		$objMdPetIndisponibilidadeDocDTO->retDblIdDocumento();
		$objMdPetIndisponibilidadeDocDTO->retDblIdProtocoloDocumento();
		$objMdPetIndisponibilidadeDocDTO->retStrNumero();
		$objMdPetIndisponibilidadeDocDTO->retStrProtocoloFormatadoDocumento();
		$objMdPetIndisponibilidadeDocDTO->retDthInclusao();
		$objMdPetIndisponibilidadeDocDTO->retStrSiglaUnidade();
		$objMdPetIndisponibilidadeDocDTO->retStrDescricaoUnidade();
		$objMdPetIndisponibilidadeDocDTO->retNumIdSerie();
		$objMdPetIndisponibilidadeDocDTO->retStrNomeSerie();
		$objMdPetIndisponibilidadeDocDTO->retDblIdProtocoloProcedimento();
		$objMdPetIndisponibilidadeDocDTO->retNumIdAcessoExterno();

		$objDTO = $this->consultar($objMdPetIndisponibilidadeDocDTO);

		//Formatar Valores
		if (!is_null($objDTO)) {
			$nomeUnidade = $objDTO->getStrSiglaUnidade() . ' - ' . $objDTO->getStrDescricaoUnidade();
			$nomeDocumento = $objDTO->getStrNumero() != '' ? $objDTO->getStrNomeSerie() . ' ' . $objDTO->getStrNumero() : $objDTO->getStrNomeSerie();
			$nomeDocumento .= ' (' . $objDTO->getStrProtocoloFormatadoDocumento() . ')';
			$dataInclusao = explode(' ', $objDTO->getDthInclusao());
			$idProcedimento = $objDTO->getDblIdProtocoloProcedimento();

			if($sessaoInterna)
			{
				$urlDocumento = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=documento_visualizar&id_procedimento=' . $idProcedimento . '&id_documento=' . 	$objDTO->getDblIdDocumento() . '&arvore=1');
				$objDTO->setStrUrlDocumento($urlDocumento);
			}else{
				$urlDocumento  = $this->_retornaUrlExterna($objDTO);
				$objDTO->setStrUrlDocumento($urlDocumento);
			}

			$objDTO->setStrNomeUnidadeFormatada($nomeUnidade);
			$objDTO->setStrNomeDocFormatado($nomeDocumento);
			$objDTO->setDtaInclusaoDta($dataInclusao[0]);
		}


		return $objDTO;
	}

	private function _retornaUrlExterna($objDTO){
		SessaoSEIExterna::getInstance()->validarSessao();
		SessaoSEIExterna::getInstance()->configurarAcessoExterno($objDTO->getNumIdAcessoExterno());
		$strLink        = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/documento_consulta_externa.php?id_acesso_externo=' . $objDTO->getNumIdAcessoExterno() . '&id_documento=' . $objDTO->getDblIdDocumento();
		$urlDocumento = SessaoSEIExterna::getInstance($objDTO->getNumIdAcessoExterno())->assinarLink($strLink);
		SessaoSEIExterna::getInstance()->configurarAcessoExterno(null);

		return $urlDocumento;
	}

	protected function removerAcessosExternosControlado($arrIndispDocDTO){
		$objAcessoExternoRN = new AcessoExternoRN();

		foreach($arrIndispDocDTO as $objDTO){
			if(!is_null($objDTO->getNumIdAcessoExterno())){
				$idsAcessoExterno[] = $objDTO->getNumIdAcessoExterno();
			}
		}

		if (count($idsAcessoExterno) > 0) 
		{
			$objAcessoExtDTO = new AcessoExternoDTO();
			$objAcessoExtDTO->retTodos();
			$objAcessoExtDTO->setNumIdAcessoExterno($idsAcessoExterno, InfraDTO::$OPER_IN);
			$arrObjAcessoExternoDTO = $objAcessoExternoRN->listar($objAcessoExtDTO);
			$objAcessoExternoRN->excluir($arrObjAcessoExternoDTO);
		}
	}





}
?>
  
 