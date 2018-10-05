<?php

/**
 * ANATEL
 *
 * 30/03/2017 - criado por jaqueline.mendes - CAST
 *
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntEmailNotificacaoRN extends InfraRN {

	public static $EMAIL_INTIMACAO_CADASTRO = 'MD_PET_CADASTRO_INTIMACAO';
	
	public function __construct() {
	  /**
     * Retirada do método nativo que estava entrando em conflito com o AgendamentoRN :: otimizarIndicesSolr
	  */
//		session_start();
		//////////////////////////////////////////////////////////////////////////////
		InfraDebug::getInstance()->setBolLigado(true);
		InfraDebug::getInstance()->setBolDebugInfra(true);
		InfraDebug::getInstance()->limpar();
		//////////////////////////////////////////////////////////////////////////////
		
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	public function notificaCadastroIntimacaoConectado($arrParams){
		$objEmailSistemaRN = new EmailSistemaRN();
		
		
		
	}
	
	protected function enviarEmailIntimacaoConectado($dadosIntimacao){
		$arrDadosEmail= array();
		
		$arrDadosEmail['dadosUsuario']['nome'] = $dadosIntimacao['nome'];
		$arrDadosEmail['dadosUsuario']['email'] = $dadosIntimacao['email'];
		$arrDadosEmail['dadosUsuario']['dataHora'] = $dadosIntimacao['dataHora'];
		$arrDadosEmail['dadosUsuario']['processo'] = $dadosIntimacao['processo'];
		
		//Busca dados Montar Email.
		//Dados Documento
		$objDocumentoDTO = new DocumentoDTO();
		$objDocumentoDTO->retDblIdDocumento();
		$objDocumentoDTO->retNumIdOrgaoUnidadeResponsavel();
		$objDocumentoDTO->retStrProtocoloDocumentoFormatado();
		$objDocumentoDTO->retStrNomeSerie();
		$objDocumentoDTO->retStrNumero();
		$objDocumentoDTO->retNumIdSerie();
		$objDocumentoDTO->setDblIdDocumento($dadosIntimacao['POST']['hdnIdDocumento']);
		$objDocumentoRN = new DocumentoRN();
		$objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);
		$strProtocoloDocumentoFormatado = $objDocumentoDTO->getStrProtocoloDocumentoFormatado();
		$arrDadosEmail['objDocumentoDTO'] = $objDocumentoDTO;
		
		//Dados Unidade
		$objUnidadeDTO = new UnidadeDTO();
		$objUnidadeDTO->retStrSigla();
		$objUnidadeDTO->retStrDescricao();
		$objUnidadeDTO->retStrSiglaOrgao();
		$objUnidadeDTO->retStrDescricaoOrgao();
		$objUnidadeDTO->retStrSitioInternetOrgaoContato();
		$objUnidadeDTO->setBolExclusaoLogica(false);
		$objUnidadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
		
		$objUnidadeRN = new UnidadeRN();
		$objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);
		
		$arrDadosEmail['objUnidadeDTO'] = $objUnidadeDTO;
		
		//Dados Tipo Intimacao
		$objMdPetIntTipoIntimacaoDTO = new MdPetIntTipoIntimacaoDTO();
		$objMdPetIntTipoIntimacaoDTO->retTodos();
		$objMdPetIntTipoIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($dadosIntimacao['POST']['selTipoIntimacao']);
		
		$objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
		$objMdPetIntTipoIntimacaoDTO = $objMdPetIntTipoIntimacaoRN->consultar($objMdPetIntTipoIntimacaoDTO);
		
		$arrDadosEmail['objMdPetIntTipoIntimacaoDTO'] = $objMdPetIntTipoIntimacaoDTO;
		
		$objMdPetIntPrazoTacitaDTO = new MdPetIntPrazoTacitaDTO();
		$objMdPetIntPrazoTacitaDTO->retTodos();
		$objMdPetIntPrazoTacitaRN = new MdPetIntPrazoTacitaRN();
		$objMdPetIntPrazoTacitaDTO = $objMdPetIntPrazoTacitaRN->consultar($objMdPetIntPrazoTacitaDTO);
		
		
		//PrazoTacita
		$arrDadosEmail['prazoTacita']          = $objMdPetIntPrazoTacitaDTO->getNumNumPrazo();
		$objMdPetIntPrazoRN                    = new MdPetIntPrazoRN();
		$arrDadosEmail['dataFinalPrazoTacita'] = $objMdPetIntPrazoRN->calcularDataPrazo($arrDadosEmail['prazoTacita']);

		if($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita() == MdPetIntTipoIntimacaoRN::$FACULTATIVA){
			$this->emailRespostasFacultativas($arrDadosEmail);
		}else if($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita() == MdPetIntTipoIntimacaoRN::$EXIGE_RESPOSTA){
			$objMdPetIntTipoRespDTO = new MdPetIntTipoRespDTO();
			$objMdPetIntTipoRespDTO->retTodos();
			$objMdPetIntTipoRespDTO->setNumIdMdPetIntTipoResp($dadosIntimacao['POST']['selTipoResposta'][0]);
			$objMdPetIntTipoRespRN = new MdPetIntTipoRespRN();
			$objMdPetIntTipoRespDTO = $objMdPetIntTipoRespRN->consultar($objMdPetIntTipoRespDTO);
			
			$arrDadosEmail['objMdPetIntTipoRespDTO'] = $objMdPetIntTipoRespDTO;
			
			$this->emailExigeResposta($arrDadosEmail);
		}else if($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita() == MdPetIntTipoIntimacaoRN::$SEM_RESPOSTA){
			$this->emailSemResposta($arrDadosEmail);
		}

		return true;
	}

	protected function enviarEmailReiteracaoIntimacaoConectado($params){

		$arrObjMdPetIntRelDestinatarioDTO = $params[0];	

		$qtdEnviadas = 0;
		$arrDadosEmail = array();

		//Usuário do Módulo de Peticionamento
		$objUsuarioPetRN  = new MdPetIntUsuarioRN();
		$idUsuarioPet = $objUsuarioPetRN->getObjUsuarioPeticionamento(true);

		////////// CAMPOS EM COMUM
		$arrDadosEmail['sigla_sistema'] = ConfiguracaoSEI::getInstance()->getValor('SessaoSEI','SiglaSistema');

		$objInfraParametro = new InfraParametro(BancoSEI::getInstance());
		$arrDadosEmail['email_sistema'] = $objInfraParametro->getValor('SEI_EMAIL_SISTEMA');

		$arrDadosEmail['link_login_usuario_externo'] = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador_externo.php?acao=usuario_externo_logar&id_orgao_acesso_externo=0';
		////////// CAMPOS EM COMUM - fim

		if (count($arrObjMdPetIntRelDestinatarioDTO)>0){
		foreach ($arrObjMdPetIntRelDestinatarioDTO as $destinatario) {
			$arrDadosEmail['tipo_resposta'] = '';
			$isReiteracao = false;

			$idIntimacao = $destinatario->getNumIdMdPetIntimacao();
			$idMdPetIntRelDestinatario = $destinatario->getNumIdMdPetIntRelDestinatario();
			$idContato = $destinatario->getNumIdContato();

			$dtIntimacaoAceite = !is_null($destinatario->getDthDataAceite()) ? explode(' ', $destinatario->getDthDataAceite()) : null;
			$arrDadosEmail['data_cumprimento_intimacao'] = count($dtIntimacaoAceite) > 0 ? $dtIntimacaoAceite[0] : null;	 

			$objMdPetIntPrazoRN = new MdPetIntPrazoRN();
			$arrObjMdPetIntPrazoDTO = $objMdPetIntPrazoRN->retornarTipoRespostaValido(array($idIntimacao, $idMdPetIntRelDestinatario));

			//Existe algum Tipo de Resposta que ainda possui prazo?
			if (count($arrObjMdPetIntPrazoDTO)>0) {

				$objMdPetIntPrazoRN = new MdPetIntPrazoRN();
				$arrObjMdPetIntPrazoDTO = $objMdPetIntPrazoRN->retornarTipoRespostaValido(array($idIntimacao, $idMdPetIntRelDestinatario, true, false));

				if (count($arrObjMdPetIntPrazoDTO)>0) {

					$tipoRespostaDTO = $arrObjMdPetIntPrazoDTO[0];

					//Data prazo de 1 ou 5 dias
					$dataAtual = InfraData::getStrDataAtual();

					$dataFinal = $tipoRespostaDTO->getDthDataProrrogada();
					if (empty($dataFinal)){
						$dataFinal = $tipoRespostaDTO->getDthDataLimite();
					}
					if (is_array(explode(" ", $dataFinal))){
						$dataFinal=explode(" ", $dataFinal);
						$dataFinal=$dataFinal[0];
					}

					if (InfraData::compararDatas($dataFinal, $dataAtual) == -1 || InfraData::compararDatas($dataFinal, $dataAtual) == -5) {
						$isReiteracao = true;
						$arrDadosEmail['tipo_resposta'] = $tipoRespostaDTO->getStrNome();
						$arrDadosEmail['prazo_externo_tipo_resposta'] = $tipoRespostaDTO->getNumValorPrazoExterno();
						$arrDadosEmail['tipo_prazo_externo_tipo_resposta'] = $tipoRespostaDTO->getStrTipoPrazoExterno();
					}
				}

				if ($isReiteracao){
					//Dados Unidade
					$objMdPetIntimacaoRN    = new MdPetIntimacaoRN();
					$objUnidadeIntimacaoDTO = $objMdPetIntimacaoRN->getUnidadeIntimacao(array($idIntimacao));		

					if ( count($objUnidadeIntimacaoDTO)>0 ){
						if (is_numeric($objUnidadeIntimacaoDTO->getNumIdUnidade())) {
							$objUnidadeDTO = new UnidadeDTO();
							$objUnidadeDTO->retStrSigla();
							$objUnidadeDTO->retStrDescricao();
							$objUnidadeDTO->retStrSiglaOrgao();
							$objUnidadeDTO->retStrDescricaoOrgao();
							$objUnidadeDTO->retStrSitioInternetOrgaoContato();
							$objUnidadeDTO->setBolExclusaoLogica(false);
							$objUnidadeDTO->setNumIdUnidade( $objUnidadeIntimacaoDTO->getNumIdUnidade() );

							$objUnidadeRN = new UnidadeRN();
							$objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);

							if ( count($objUnidadeIntimacaoDTO)>0 ){
								$arrDadosEmail['sigla_orgao'] = $objUnidadeDTO->getStrSiglaOrgao();
								$arrDadosEmail['descricao_orgao'] = $objUnidadeDTO->getStrDescricaoOrgao(); 
								$arrDadosEmail['sitio_internet_orgao'] = $objUnidadeDTO->getStrSitioInternetOrgaoContato();
							}

						}
					}

					//contato
					$objUsuarioDTO = new UsuarioDTO();
					$objUsuarioDTO->retNumIdUsuario();
					$objUsuarioDTO->retStrSigla();
					$objUsuarioDTO->retStrNome();
					$objUsuarioDTO->retStrStaTipo();
					$objUsuarioDTO->retNumIdContato();
					$objUsuarioDTO->setNumIdContato($idContato);

					$objUsuarioRN = new UsuarioRN();
					$objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

					if (count($objUsuarioDTO)==1){
						if ($objUsuarioDTO->getStrStaTipo() == UsuarioRN::$TU_EXTERNO_PENDENTE) {
							$objInfraException->lancarValidacao('Usuário externo "' . $objUsuarioDTO->getStrSigla() . '" ainda não foi liberado.');
						}

						if ($objUsuarioDTO->getStrStaTipo() != UsuarioRN::$TU_EXTERNO) {
							$objInfraException->lancarValidacao('Usuário "' . $objUsuarioDTO->getStrSigla() . '" não é um usuário externo.');
						}
						//contato - fim

						$arrDadosEmail['email_usuario_externo'] = $objUsuarioDTO->getStrSigla();
						$arrDadosEmail['nome_usuario_externo']  = $objUsuarioDTO->getStrNome();

						$arrDadosEmail['tipo_intimacao']  = $destinatario->getStrNomeTipoIntimacao();

						//Get Prazo Tácito
						$objPrazoTacitoDTO = new MdPetIntPrazoTacitaDTO();
						$objPrazoTacitoDTO->retNumNumPrazo();
						$objPrazoTacitoRN = new MdPetIntPrazoTacitaRN();
						$retLista = $objPrazoTacitoRN->listar($objPrazoTacitoDTO);
						$objPrazoTacitoDTO = !is_null($retLista) && count($retLista) > 0 ? current($retLista) : null;
						$arrDadosEmail['prazo_intimacao_tacita'] = !is_null($objPrazoTacitoDTO) ? $objPrazoTacitoDTO->getNumNumPrazo() : null;

						$dtIntimacao = !is_null($destinatario->getDthDataCadastro()) ? explode(' ', $destinatario->getDthDataCadastro()) : null;

						//Data Expedição Intimação
						$arrDadosEmail['data_expedicao_intimacao'] = count($dtIntimacao) > 0 ? $dtIntimacao[0] : null;

						//Calcular Data Final do Prazo Tácito
						$dataFimPrazoTacito = '';
						$objMdPetIntPrazoRN = new MdPetIntPrazoRN();
						$arrDadosEmail['data_final_prazo_intimacao_tacita'] = $objMdPetIntPrazoRN->calcularDataPrazo($arrDadosEmail['prazo_intimacao_tacita'], $arrDadosEmail['data_expedicao_intimacao']);                

						//Documento Principal
						$dados = $objMdPetIntimacaoRN->retornaDadosDocPrincipalIntimacao(array($idIntimacao));

						$objDocumentoDTO = new DocumentoDTO();
						$objDocumentoDTO->retDblIdDocumento();
						$objDocumentoDTO->retNumIdOrgaoUnidadeResponsavel();
						$objDocumentoDTO->retStrProtocoloDocumentoFormatado();
						$objDocumentoDTO->retStrNomeSerie();
						$objDocumentoDTO->retStrNumero();
						$objDocumentoDTO->retNumIdSerie();
						$objDocumentoDTO->retStrProtocoloProcedimentoFormatado();
						$objDocumentoDTO->retStrNomeTipoProcedimentoProcedimento();

						$objDocumentoDTO->setDblIdDocumento($dados[3]);
						$objDocumentoRN = new DocumentoRN();
						$objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

						$arrDadosEmail['documento_principal_intimacao'] = $dados[0];
						$arrDadosEmail['tipo_documento_principal_intimacao'] = $dados[1];
						if( !empty($dados[4]) ) {
							$arrDadosEmail['tipo_documento_principal_intimacao'] .= ' '. $dados[4];
						}

						$arrDadosEmail['processo'] = $objDocumentoDTO->getStrProtocoloProcedimentoFormatado();
						$arrDadosEmail['tipo_processo'] = $objDocumentoDTO->getStrNomeTipoProcedimentoProcedimento();

						$this->emailReiteracaoExigeResposta($arrDadosEmail);
						$qtdEnviadas++;
					}

				}

			}

		}
		}
		return $qtdEnviadas;
	}

	public function emailRespostasFacultativas($arrDadosEmail){
		//Enviar Email
		$objInfraParametro = new InfraParametro(BancoSEI::getInstance());
		$objEmailSistemaDTO = new EmailSistemaDTO();
		$objEmailSistemaDTO->retStrDe();
		$objEmailSistemaDTO->retStrPara();
		$objEmailSistemaDTO->retStrAssunto();
		$objEmailSistemaDTO->retStrConteudo();
		$objEmailSistemaDTO->setStrIdEmailSistemaModulo('MD_PET_INTIMACAO_APENAS_RESPOSTAS_FACULTATIVAS');

		$objEmailSistemaRN = new EmailSistemaRN();
		$objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

		//variaveis basicas em uso no email
		$linkLoginUsuarioExterno = SessaoSEIExterna::getInstance()->getStrPaginaLogin() . '&id_orgao_acesso_externo=0';

		//Monta Email
		$strDe = $objEmailSistemaDTO->getStrDe();
		$strDe = str_replace('@sigla_sistema@',SessaoSEI::getInstance()->getStrSiglaSistema(),$strDe);
		$strDe = str_replace('@email_sistema@',$objInfraParametro->getValor('SEI_EMAIL_SISTEMA'),$strDe);
		
		$strPara = $objEmailSistemaDTO->getStrPara();
		$strPara = str_replace('@email_usuario_externo@', $arrDadosEmail['dadosUsuario']['email'], $strPara);//email usuario
		
		$strAssunto = $objEmailSistemaDTO->getStrAssunto();
		$strAssunto = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strAssunto);//sistema
		
		$strConteudo = $objEmailSistemaDTO->getStrConteudo();
		$strConteudo = str_replace('@nome_usuario_externo@', $arrDadosEmail['dadosUsuario']['nome'], $strConteudo);

		$strConteudo = str_replace('@sigla_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSiglaOrgao(), $strConteudo);
		$strConteudo = str_replace('@tipo_intimacao@', $arrDadosEmail['objMdPetIntTipoIntimacaoDTO']->getStrNome(), $strConteudo);
		$strConteudo = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strConteudo);
		$strConteudo = str_replace('@documento_principal_intimacao@',  $arrDadosEmail['objDocumentoDTO']->getStrProtocoloDocumentoFormatado(), $strConteudo);
		$strConteudo = str_replace('@tipo_documento_principal_intimacao@', DocumentoINT::formatarIdentificacao($arrDadosEmail['objDocumentoDTO']), $strConteudo);
		$strConteudo = str_replace('@link_login_usuario_externo@', $linkLoginUsuarioExterno, $strConteudo);
		$strConteudo = str_replace('@prazo_intimacao_tacita@',$arrDadosEmail['prazoTacita'] , $strConteudo);
		$strConteudo = str_replace('@data_expedicao_intimacao@', $arrDadosEmail['dadosUsuario']['dataHora'], $strConteudo);
		$strConteudo = str_replace('@data_final_prazo_intimacao_tacita@', $arrDadosEmail['dataFinalPrazoTacita'], $strConteudo);
		$strConteudo = str_replace('@descricao_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrDescricaoOrgao(), $strConteudo);
		$strConteudo = str_replace('@sitio_internet_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSitioInternetOrgaoContato(), $strConteudo);
		
		$objEmailDTO = new EmailDTO();
		$objEmailDTO->setStrDe($strDe);
		$objEmailDTO->setStrPara($strPara);
		$objEmailDTO->setStrAssunto($strAssunto);
		$objEmailDTO->setStrMensagem($strConteudo);
		
		EmailRN::processar(array($objEmailDTO));
	}
	
	public function emailExigeResposta($arrDadosEmail){
		//Enviar Email
		$objInfraParametro = new InfraParametro(BancoSEI::getInstance());
		$objEmailSistemaDTO = new EmailSistemaDTO();
		$objEmailSistemaDTO->retStrDe();
		$objEmailSistemaDTO->retStrPara();
		$objEmailSistemaDTO->retStrAssunto();
		$objEmailSistemaDTO->retStrConteudo();
		$objEmailSistemaDTO->setStrIdEmailSistemaModulo('MD_PET_INTIMACAO_QUE_EXIGE_RESPOSTA');
		
		$objEmailSistemaRN = new EmailSistemaRN();
		$objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

		//variaveis basicas em uso no email
		$linkLoginUsuarioExterno = SessaoSEIExterna::getInstance()->getStrPaginaLogin() . '&id_orgao_acesso_externo=0';

		$strDe = $objEmailSistemaDTO->getStrDe();
		$strDe = str_replace('@sigla_sistema@',SessaoSEI::getInstance()->getStrSiglaSistema(),$strDe);
		$strDe = str_replace('@email_sistema@',$objInfraParametro->getValor('SEI_EMAIL_SISTEMA'),$strDe);
		
		$strPara = $objEmailSistemaDTO->getStrPara();
		$strPara = str_replace('@email_usuario_externo@', $arrDadosEmail['dadosUsuario']['email'], $strPara);//email usuario
		
		$strAssunto = $objEmailSistemaDTO->getStrAssunto();
		$strAssunto = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strAssunto);//sistema
		
		$strConteudo = $objEmailSistemaDTO->getStrConteudo();
		$strConteudo = str_replace('@nome_usuario_externo@', $arrDadosEmail['dadosUsuario']['nome'], $strConteudo);

		$strConteudo = str_replace('@sigla_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSiglaOrgao(), $strConteudo);
		$strConteudo = str_replace('@tipo_intimacao@', $arrDadosEmail['objMdPetIntTipoIntimacaoDTO']->getStrNome(), $strConteudo);
		$strConteudo = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strConteudo);
		$strConteudo = str_replace('@documento_principal_intimacao@',  $arrDadosEmail['objDocumentoDTO']->getStrProtocoloDocumentoFormatado(), $strConteudo);
		$strConteudo = str_replace('@tipo_documento_principal_intimacao@', DocumentoINT::formatarIdentificacao($arrDadosEmail['objDocumentoDTO']), $strConteudo);
		
		$strConteudo = str_replace('@tipo_resposta@', $arrDadosEmail['objMdPetIntTipoRespDTO']->getStrNome(), $strConteudo);
		$prazo = $arrDadosEmail['objMdPetIntTipoRespDTO']->getNumValorPrazoExterno();
		if ($arrDadosEmail['objMdPetIntTipoRespDTO']->getStrTipoPrazoExterno() == 'D') {
			$prazo .= $arrDadosEmail['objMdPetIntTipoRespDTO']->getNumValorPrazoExterno() > 1 ? ' Dias' : ' Dia';
		} else if ($arrDadosEmail['objMdPetIntTipoRespDTO']->getStrTipoPrazoExterno() == 'M') {
			$prazo .= $arrDadosEmail['objMdPetIntTipoRespDTO']->getNumValorPrazoExterno() > 1 ? ' Meses' : ' Ms';
		} else if ($arrDadosEmail['objMdPetIntTipoRespDTO']->getStrTipoPrazoExterno() == 'A') {
			$prazo .= $arrDadosEmail['objMdPetIntTipoRespDTO']->getNumValorPrazoExterno() > 1 ? ' Anos' : ' Ano';
		}
		$strConteudo = str_replace('@prazo_externo_tipo_resposta@', $prazo, $strConteudo);
		$strConteudo = str_replace('@link_login_usuario_externo@', $linkLoginUsuarioExterno, $strConteudo);
		$strConteudo = str_replace('@prazo_intimacao_tacita@',$arrDadosEmail['prazoTacita'] , $strConteudo);
		$strConteudo = str_replace('@data_expedicao_intimacao@', $arrDadosEmail['dadosUsuario']['dataHora'], $strConteudo);
		$strConteudo = str_replace('@data_final_prazo_intimacao_tacita@', $arrDadosEmail['dataFinalPrazoTacita'], $strConteudo);
		$strConteudo = str_replace('@descricao_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrDescricaoOrgao(), $strConteudo);
		$strConteudo = str_replace('@sitio_internet_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSitioInternetOrgaoContato(), $strConteudo);
		
		$objEmailDTO = new EmailDTO();
		$objEmailDTO->setStrDe($strDe);
		$objEmailDTO->setStrPara($strPara);
		$objEmailDTO->setStrAssunto($strAssunto);
		$objEmailDTO->setStrMensagem($strConteudo);
		
		EmailRN::processar(array($objEmailDTO));
	}

	public function emailSemResposta($arrDadosEmail){
		//Enviar Email
		$objInfraParametro = new InfraParametro(BancoSEI::getInstance());
		$objEmailSistemaDTO = new EmailSistemaDTO();
		$objEmailSistemaDTO->retStrDe();
		$objEmailSistemaDTO->retStrPara();
		$objEmailSistemaDTO->retStrAssunto();
		$objEmailSistemaDTO->retStrConteudo();
		$objEmailSistemaDTO->setStrIdEmailSistemaModulo('MD_PET_INTIMACAO_SEM_RESPOSTA');

		$objEmailSistemaRN = new EmailSistemaRN();
		$objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

		//variaveis basicas em uso no email
		$linkLoginUsuarioExterno = SessaoSEIExterna::getInstance()->getStrPaginaLogin() . '&id_orgao_acesso_externo=0';

		//Monta Email
		$strDe = $objEmailSistemaDTO->getStrDe();
		$strDe = str_replace('@sigla_sistema@',SessaoSEI::getInstance()->getStrSiglaSistema(),$strDe);
		$strDe = str_replace('@email_sistema@',$objInfraParametro->getValor('SEI_EMAIL_SISTEMA'),$strDe);
		
		$strPara = $objEmailSistemaDTO->getStrPara();
		$strPara = str_replace('@email_usuario_externo@', $arrDadosEmail['dadosUsuario']['email'], $strPara);//email usuario

		$strAssunto = $objEmailSistemaDTO->getStrAssunto();
		$strAssunto = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strAssunto);//sistema

		$strConteudo = $objEmailSistemaDTO->getStrConteudo();
		$strConteudo = str_replace('@nome_usuario_externo@', $arrDadosEmail['dadosUsuario']['nome'], $strConteudo);

		$strConteudo = str_replace('@sigla_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSiglaOrgao(), $strConteudo);
		$strConteudo = str_replace('@tipo_intimacao@', $arrDadosEmail['objMdPetIntTipoIntimacaoDTO']->getStrNome(), $strConteudo);
		$strConteudo = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strConteudo);
		$strConteudo = str_replace('@documento_principal_intimacao@',  $arrDadosEmail['objDocumentoDTO']->getStrProtocoloDocumentoFormatado(), $strConteudo);
		$strConteudo = str_replace('@tipo_documento_principal_intimacao@', DocumentoINT::formatarIdentificacao($arrDadosEmail['objDocumentoDTO']), $strConteudo);
		$strConteudo = str_replace('@link_login_usuario_externo@', $linkLoginUsuarioExterno, $strConteudo);
		$strConteudo = str_replace('@prazo_intimacao_tacita@',$arrDadosEmail['prazoTacita'] , $strConteudo);
		$strConteudo = str_replace('@data_expedicao_intimacao@', $arrDadosEmail['dadosUsuario']['dataHora'], $strConteudo);
		$strConteudo = str_replace('@data_final_prazo_intimacao_tacita@', $arrDadosEmail['dataFinalPrazoTacita'], $strConteudo);
		$strConteudo = str_replace('@descricao_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrDescricaoOrgao(), $strConteudo);
		$strConteudo = str_replace('@sitio_internet_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSitioInternetOrgaoContato(), $strConteudo);

		$objEmailDTO = new EmailDTO();
		$objEmailDTO->setStrDe($strDe);
		$objEmailDTO->setStrPara($strPara);
		$objEmailDTO->setStrAssunto($strAssunto);
		$objEmailDTO->setStrMensagem($strConteudo);
		
		EmailRN::processar(array($objEmailDTO));
	}

	public function emailReiteracaoExigeResposta($arrDadosEmail){

		//Enviar Email
		$objInfraParametro = new InfraParametro(BancoSEI::getInstance());
		$objEmailSistemaDTO = new EmailSistemaDTO();
		$objEmailSistemaDTO->retStrDe();
		$objEmailSistemaDTO->retStrPara();
		$objEmailSistemaDTO->retStrAssunto();
		$objEmailSistemaDTO->retStrConteudo();
		$objEmailSistemaDTO->setStrIdEmailSistemaModulo('MD_PET_REITERACAO_INTIMACAO_QUE_EXIGE_RESPOSTA');

		$objEmailSistemaRN = new EmailSistemaRN();
		$objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

		$strDe = $objEmailSistemaDTO->getStrDe();

		$strDe = str_replace('@sigla_sistema@',$arrDadosEmail['sigla_sistema'],$strDe);

		$strDe = str_replace('@email_sistema@',$arrDadosEmail['email_sistema'],$strDe);


		$strPara = $objEmailSistemaDTO->getStrPara();
		$strPara = str_replace('@email_usuario_externo@', $arrDadosEmail['email_usuario_externo'], $strPara);//email usuario


		$strAssunto = $objEmailSistemaDTO->getStrAssunto();
		$strAssunto = str_replace('@processo@', $arrDadosEmail['processo'], $strAssunto);//sistema


		$strConteudo = $objEmailSistemaDTO->getStrConteudo();
		$strConteudo = str_replace('@processo@', $arrDadosEmail['processo'], $strConteudo);
		$strConteudo = str_replace('@tipo_processo@', $arrDadosEmail['tipo_processo'], $strConteudo);
		$strConteudo = str_replace('@nome_usuario_externo@', $arrDadosEmail['nome_usuario_externo'], $strConteudo);
		$strConteudo = str_replace('@email_usuario_externo@', $arrDadosEmail['email_usuario_externo'], $strConteudo);

		//variaveis basicas em uso no email
		$strConteudo = str_replace('@link_login_usuario_externo@', $arrDadosEmail['link_login_usuario_externo'], $strConteudo);
		$strConteudo = str_replace('@tipo_intimacao@', $arrDadosEmail['tipo_intimacao'], $strConteudo);
		$strConteudo = str_replace('@data_expedicao_intimacao@', $arrDadosEmail['data_expedicao_intimacao'], $strConteudo);
		$strConteudo = str_replace('@prazo_intimacao_tacita@',$arrDadosEmail['prazo_intimacao_tacita'] , $strConteudo);
		$strConteudo = str_replace('@data_final_prazo_intimacao_tacita@', $arrDadosEmail['data_final_prazo_intimacao_tacita'], $strConteudo);
		$strConteudo = str_replace('@documento_principal_intimacao@',  $arrDadosEmail['documento_principal_intimacao'], $strConteudo);
		$strConteudo = str_replace('@tipo_documento_principal_intimacao@', /*DocumentoINT::formatarIdentificacao(*/$arrDadosEmail['tipo_documento_principal_intimacao']/*)*/, $strConteudo);

		$strConteudo = str_replace('@tipo_resposta@', $arrDadosEmail['tipo_resposta'], $strConteudo);		

		$prazo = $arrDadosEmail['prazo_externo_tipo_resposta'];
		if ($arrDadosEmail['tipo_prazo_externo_tipo_resposta'] == 'D') {
			$prazo .= $prazo > 1 ? ' dias' : ' dia';
		} else if ($arrDadosEmail['tipo_prazo_externo_tipo_resposta'] == 'M') {
			$prazo .= $prazo > 1 ? ' meses' : ' mês';
		} else if ($arrDadosEmail['tipo_prazo_externo_tipo_resposta'] == 'A') {
			$prazo .= $prazo > 1 ? ' anos' : ' ano';
		}
		$strConteudo = str_replace('@prazo_externo_tipo_resposta@', $prazo, $strConteudo);

		$strConteudo = str_replace('@data_cumprimento_intimacao@', $arrDadosEmail['data_cumprimento_intimacao'], $strConteudo);		

		$strConteudo = str_replace('@sigla_orgao@', $arrDadosEmail['sigla_orgao'], $strConteudo);
		$strConteudo = str_replace('@descricao_orgao@', $arrDadosEmail['descricao_orgao'], $strConteudo);
		$strConteudo = str_replace('@sitio_internet_orgao@', $arrDadosEmail['sitio_internet_orgao'], $strConteudo);

		$objEmailDTO = new EmailDTO();
		$objEmailDTO->setStrDe($strDe);
		$objEmailDTO->setStrPara($strPara);
		$objEmailDTO->setStrAssunto($strAssunto);
		$objEmailDTO->setStrMensagem($strConteudo);
		EmailRN::processar(array($objEmailDTO));
	}
}