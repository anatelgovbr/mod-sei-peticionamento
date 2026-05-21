<?
/**
* ANATEL
*
* 28/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetEmailNotificacaoIntercorrenteRN extends MdPetEmailNotificacaoRN {

    public static $EMAIL_PETICIONAMENTO_USUARIO_PETICIONANTE = 'MD_PET_CONFIRMACAO_PETICIONAMENTO_USUARIO_EXTERNO';
    public static $EMAIL_PETICIONAMENTO_UNIDADE_ABERTURA = 'MD_PET_ALERTA_PETICIONAMENTO_UNIDADES';

    public function __construct() {

        session_start();

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

    protected function notificaoPeticionamentoExternoConectado($arrParams ){

        $objInfraParametro = new InfraParametro( $this->getObjInfraIBanco() );
        $arrParametros             = $arrParams[0]; // parametros adicionais fornecidos no formulario de peticionamento
        $objUnidadeDTO             = $arrParams[1]; // UnidadeDTO da unidade geradora do processo
        $objProcedimentoDTO        = $arrParams[2]; // ProcedimentoDTO para vincular o recibo ao processo correto
        $arrParticipantesParametro = $arrParams[3]; // array de ParticipanteDTO
        $reciboDTOBasico           = $arrParams[4]; // Recibo
        $documentoDTORecibo        = $arrParams[5]; // Documento Recibo

        //consultar email da unidade (orgao)
        $orgaoRN = new OrgaoRN();
        $objOrgaoDTO = new OrgaoDTO();
        $objOrgaoDTO->retTodos();
        $objOrgaoDTO->retStrSitioInternetContato();
        $objOrgaoDTO->setNumIdOrgao( $objUnidadeDTO->getNumIdOrgao() );
        $objOrgaoDTO->setStrSinAtivo('S');
        $objOrgaoDTO = $orgaoRN->consultarRN1352( $objOrgaoDTO );

        $objEmailUnidadeDTO = new EmailUnidadeDTO();
        $emailUnidadeRN = new EmailUnidadeRN();
        $objEmailUnidadeDTO->setDistinct(true);
        $objEmailUnidadeDTO->retNumIdUnidade();
        $objEmailUnidadeDTO->retStrEmail();
        // Se Direto no Processo Indicado, não só unidade geradoras, mas todas abertas
        if ($arrParametros['diretoProcessoIndicado']){
			$objMdPetIntercorrenteProcessoRN = new MdPetIntercorrenteProcessoRN(); 
			$arrObjAtividadeDTO = $objMdPetIntercorrenteProcessoRN->retornaUnidadesProcessoAberto( $arrParametros['id_procedimento'] );
			$arrUnidade = InfraArray::converterArrInfraDTO($arrObjAtividadeDTO,'IdUnidade');				

			$objEmailUnidadeDTO->adicionarCriterio(
				array('IdUnidade'),
				array(InfraDTO::$OPER_IN),
				array( $arrUnidade )
			);
		//pegar a lista de email da unidade, a unidade pode não ter, email unidade
		}else{
			$objEmailUnidadeDTO->setNumIdUnidade($objUnidadeDTO->getNumIdUnidade());
		}
		$arrEmailUnidade = $emailUnidadeRN->listar($objEmailUnidadeDTO);

    //    die(var_dump($arrEmailUnidade));

        //obtendo o tipo de procedimento
        $idTipoProc = $arrParametros['id_tipo_procedimento'];
        $objTipoProcDTO = new MdPetTipoProcessoDTO();
        $objTipoProcDTO->retTodos(true);
        $objTipoProcDTO->retStrNomeSerie();
        $objTipoProcDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
        $objTipoProcRN = new MdPetTipoProcessoRN();
        $objTipoProcDTO = $objTipoProcRN->consultar( $objTipoProcDTO );

        //variaveis basicas em uso no email
        $linkLoginUsuarioExterno = SessaoSEIExterna::getInstance()->getStrPaginaLogin() . '&id_orgao_acesso_externo=' . $objUnidadeDTO->getNumIdOrgao();

        $strNomeTipoProcedimento = $objProcedimentoDTO->getStrNomeTipoProcedimento();
        $strProtocoloFormatado = $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado();
        $strSiglaUnidade = $objUnidadeDTO->getStrSigla();

        $strSiglaSistema = SessaoSEIExterna::getInstance()->getStrSiglaSistema();
        $strEmailSistema = $objInfraParametro->getValor('SEI_EMAIL_SISTEMA');
        $strEmailAdministrador = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');

        // Acumula falhas de envio para notificar o admin em um unico e-mail ao final
        $arrFalhasEmail = array();

        $strSiglaOrgao = $objOrgaoDTO->getStrSigla();
        $strSiglaOrgaoMinusculas = InfraString::transformarCaixaBaixa($objOrgaoDTO->getStrSigla());
        $strSufixoEmail = $objInfraParametro->getValor('SEI_SUFIXO_EMAIL');

        //Tentando simular sessao de usuario interno do SEI
        SessaoSEI::getInstance()->setNumIdUnidadeAtual( $objUnidadeDTO->getNumIdUnidade() );
        SessaoSEI::getInstance()->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->retTodos();
        $objUsuarioDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );

        $objUsuarioRN = new UsuarioRN();
        $objUsuarioDTO = $objUsuarioRN->consultarRN0489( $objUsuarioDTO );

        $strNomeContato = $objUsuarioDTO->getStrNome();
        $strEmailContato = $objUsuarioDTO->getStrSigla();

        //RECIBO
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $idSerieParam = $objInfraParametro->getValor(MdPetIntSerieRN::$MD_PET_ID_SERIE_RECIBO);

        $documentoRN = new DocumentoRN();
        $documentoDTO = new DocumentoDTO();
        $documentoDTO->retStrProtocoloDocumentoFormatado();
        $documentoDTO->retTodos();
        $documentoDTO->setDblIdProcedimento( $reciboDTOBasico->getNumIdProtocolo() );
        $documentoDTO->setNumIdSerie( $idSerieParam );
        $documentoDTO->setDblIdDocumento( $documentoDTORecibo->getDblIdDocumento() );

        $documentoDTO = $documentoRN->consultarRN0005( $documentoDTO );

        //enviando email de sistema após cadastramento do processo de peticionamento pelo usuário externo
        //================================================================================================
        //EMAIL PARA O USUARIO PETICIONANTE
        //================================================================================================

        $objEmailSistemaDTO = new EmailSistemaDTO();
        $objEmailSistemaDTO->retNumIdEmailSistema();
        $objEmailSistemaDTO->retStrDe();
        $objEmailSistemaDTO->retStrPara();
        $objEmailSistemaDTO->retStrAssunto();
        $objEmailSistemaDTO->retStrConteudo();
        $objEmailSistemaDTO->setStrIdEmailSistemaModulo( MdPetEmailNotificacaoIntercorrenteRN::$EMAIL_PETICIONAMENTO_USUARIO_PETICIONANTE );

        $objEmailSistemaRN = new EmailSistemaRN();
        $objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

        if ($objEmailSistemaDTO!=null){

            $strDe = $objEmailSistemaDTO->getStrDe();
            $strDe = str_replace('@sigla_sistema@',SessaoSEIExterna::getInstance()->getStrSiglaSistema() ,$strDe);
            $strDe = str_replace('@email_sistema@',$objInfraParametro->getValor('SEI_EMAIL_SISTEMA'),$strDe);
            $strDe = str_replace('@processo@',$strProtocoloFormatado ,$strDe);
            $strDe = str_replace('@sigla_orgao@',$objOrgaoDTO->getStrSigla(),$strDe);
            $strDe = str_replace('@sigla_orgao_minusculas@',InfraString::transformarCaixaBaixa($objOrgaoDTO->getStrSigla()),$strDe);
            $strDe = str_replace('@sufixo_email@',$objInfraParametro->getValor('SEI_SUFIXO_EMAIL'),$strDe);

            $strPara = $objEmailSistemaDTO->getStrPara();
            $strPara = str_replace('@nome_contato@', $strNomeContato ,$strPara);
            $strPara = str_replace('@email_contato@', $strEmailContato ,$strPara);
            $strPara = str_replace('@email_usuario_externo@', $strEmailContato ,$strPara);

            $strAssunto = $objEmailSistemaDTO->getStrAssunto();
            $strAssunto = str_replace('@sigla_orgao@',$objOrgaoDTO->getStrSigla(),$strAssunto);
            $strAssunto = str_replace('@processo@',$strProtocoloFormatado ,$strAssunto);

            $strConteudo = $objEmailSistemaDTO->getStrConteudo();

            $strConteudo = str_replace('@processo@', $strProtocoloFormatado , $strConteudo);
            ///
            $strConteudo = str_replace('@tipo_processo@', $objProcedimentoDTO->getStrNomeTipoProcedimento() , $strConteudo);
            $strConteudo = str_replace('@nome_usuario_externo@', $strNomeContato ,$strConteudo);
            ///
            $strConteudo = str_replace('@email_usuario_externo@', $strEmailContato ,$strConteudo);
            $strConteudo = str_replace('@link_login_usuario_externo@', $linkLoginUsuarioExterno , $strConteudo);

            $strConteudo = str_replace('@tipo_peticionamento@',$reciboDTOBasico->getStrStaTipoPeticionamentoFormatado(),$strConteudo);

            $strConteudo = str_replace('@sigla_unidade_abertura_do_processo@', $strSiglaUnidade ,$strConteudo);
            $strConteudo = str_replace('@descricao_unidade_abertura_do_processo@',$objUnidadeDTO->getStrDescricao(),$strConteudo);
            $strProtocoloFormatado = '';

            $strConteudo = str_replace('@documento_recibo_eletronico_de_protocolo@',$documentoDTO->getStrProtocoloDocumentoFormatado(), $strConteudo);
            $strConteudo = str_replace('@sigla_orgao@',$objOrgaoDTO->getStrSigla(),$strConteudo);
            $strConteudo = str_replace('@descricao_orgao@',$objOrgaoDTO->getStrDescricao(),$strConteudo);
            $strConteudo = str_replace('@sitio_internet_orgao@',$objOrgaoDTO->getStrSitioInternetContato(),$strConteudo);

            try {
                InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strConteudo);
            } catch (\Throwable $eMail) {
                $arrFalhasEmail[] = array(
                    'contexto' => 'E-mail ao peticionante (' . $strPara . ')',
                    'erro'     => $eMail->getMessage()
                );
            }

        }

        //================================================================================================
        //EMAIL PARA A UNIDADE DE ABERTURA DO PETICIONAMENTO
        //================================================================================================

        $objEmailSistemaDTO = new EmailSistemaDTO();
        $objEmailSistemaDTO->retStrDe();
        $objEmailSistemaDTO->retStrPara();
        $objEmailSistemaDTO->retStrAssunto();
        $objEmailSistemaDTO->retStrConteudo();
        $objEmailSistemaDTO->setStrIdEmailSistemaModulo( MdPetEmailNotificacaoIntercorrenteRN::$EMAIL_PETICIONAMENTO_UNIDADE_ABERTURA );

        $objEmailSistemaRN = new EmailSistemaRN();
        $objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

        if ($objEmailSistemaDTO!=null){
            foreach($arrEmailUnidade as $mail){
	            $strDe = $objEmailSistemaDTO->getStrDe();
	            $strDe = str_replace('@sigla_sistema@',SessaoSEIExterna::getInstance()->getStrSiglaSistema() ,$strDe);
	            $strDe = str_replace('@processo@',$documentoDTO->getStrProtocoloDocumentoFormatado() ,$strDe);
	            $strDe = str_replace('@email_sistema@',$objInfraParametro->getValor('SEI_EMAIL_SISTEMA'),$strDe);
	            $strDe = str_replace('@sigla_orgao@',$objOrgaoDTO->getStrSigla(),$strDe);
	            $strDe = str_replace('@sigla_orgao_minusculas@',InfraString::transformarCaixaBaixa($objOrgaoDTO->getStrSigla()),$strDe);
	            $strDe = str_replace('@sufixo_email@',$objInfraParametro->getValor('SEI_SUFIXO_EMAIL'),$strDe);

	            $strAssunto = $objEmailSistemaDTO->getStrAssunto();
	            $strAssunto = str_replace('@sigla_orgao@',$objOrgaoDTO->getStrSigla(), $strAssunto);
	            $strAssunto = str_replace('@processo@', $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() , $strAssunto);

	            $strConteudo = $objEmailSistemaDTO->getStrConteudo();

	            $strConteudo = str_replace('@processo@',$objProcedimentoDTO->getStrProtocoloProcedimentoFormatado(),$strConteudo);
	            $strConteudo = str_replace('@tipo_processo@', $objProcedimentoDTO->getStrNomeTipoProcedimento() ,$strConteudo);
	            $strConteudo = str_replace('@nome_usuario_externo@', $strNomeContato ,$strConteudo);
	            $strConteudo = str_replace('@email_usuario_externo@', $strEmailContato ,$strConteudo);
	            $strConteudo = str_replace('@link_login_usuario_externo@', $linkLoginUsuarioExterno ,$strConteudo);

	            $strConteudo = str_replace('@tipo_peticionamento@',$reciboDTOBasico->getStrStaTipoPeticionamentoFormatado(),$strConteudo);

	            $enviaemail = false;
	            
	            // Se Direto no Processo Indicado, não só unidade geradoras, mas todas abertas
	            if ($arrParametros['diretoProcessoIndicado']){
	                $objUnidadeProcIndicRN = new UnidadeRN();
	                $objUnidadeProcIndicDTO = new UnidadeDTO();
	                $objUnidadeProcIndicDTO->retStrSigla();
	                $objUnidadeProcIndicDTO->retStrDescricao();
	                $objUnidadeProcIndicDTO->setNumIdUnidade($mail->getNumIdUnidade());
	                $objUnidadeProcIndicDTO->setBolExclusaoLogica(false);
	                $arrObjUnidadeProcIndicDTO = $objUnidadeProcIndicRN->consultarRN0125($objUnidadeProcIndicDTO);

	                if (is_object($arrObjUnidadeProcIndicDTO)){
	                	$enviaemail = true;
	                	$strConteudo = str_replace('@sigla_unidade_abertura_do_processo@' , $arrObjUnidadeProcIndicDTO->getStrSigla() , $strConteudo);
	                	$strConteudo = str_replace('@descricao_unidade_abertura_do_processo@' , $arrObjUnidadeProcIndicDTO->getStrDescricao() , $strConteudo);
	                }
	            }else{
	            	$enviaemail = true;
	                $strConteudo = str_replace('@sigla_unidade_abertura_do_processo@', $strSiglaUnidade ,$strConteudo);
	                $strConteudo = str_replace('@descricao_unidade_abertura_do_processo@',$objUnidadeDTO->getStrDescricao(),$strConteudo);
	            }

	            $strConteudo = str_replace('@documento_recibo_eletronico_de_protocolo@',$documentoDTO->getStrProtocoloDocumentoFormatado(),$strConteudo);
	            $strConteudo = str_replace('@sigla_orgao@',$objOrgaoDTO->getStrSigla(),$strConteudo);
	            $strConteudo = str_replace('@descricao_orgao@',$objOrgaoDTO->getStrDescricao(),$strConteudo);
	            $strConteudo = str_replace('@sitio_internet_orgao@',$objOrgaoDTO->getStrSitioInternetContato(),$strConteudo);

	            $strPara = $objEmailSistemaDTO->getStrPara();
	            $strPara = str_replace('@processo@', $documentoDTO->getStrProtocoloDocumentoFormatado() , $strPara);
	            $strPara = str_replace('@emails_unidade@', $mail->getStrEmail() , $strPara);
	            if ($enviaemail){
                    try {
                        InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strConteudo);
                    } catch (\Throwable $eMail) {
                        $arrFalhasEmail[] = array(
                            'contexto' => 'E-mail a unidade (' . $mail->getStrEmail() . ')',
                            'erro'     => $eMail->getMessage()
                        );
                    }
	            }
            }
        }

        // Notifica o admin com um unico e-mail listando todas as falhas acumuladas
        if (!empty($arrFalhasEmail)) {
            $this->notificarAdminFalhasEmail(
                $arrFalhasEmail,
                $arrParametros['id_procedimento'],
                $strEmailSistema,
                $strEmailAdministrador
            );
        }
    }
    /*
     * Registra no InfraLog e envia um unico e-mail ao administrador listando
     * todas as falhas de envio ocorridas no peticionamento intercorrente.
     * Nunca lanca excecao: a falha de notificacao nao deve interromper o fluxo.
     */
    private function notificarAdminFalhasEmail($arrFalhas, $idProcedimento, $strEmailSistema, $strEmailAdministrador)
    {
        // Registra cada falha individualmente no InfraLog
        foreach ($arrFalhas as $falha) {
            LogSEI::getInstance()->gravar(
                'Falha no envio de e-mail - Peticionamento intercorrente'
                . ' (processo ' . $idProcedimento . ')'
                . ' - ' . $falha['contexto']
                . ': ' . $falha['erro'],
                InfraLog::$INFORMACAO
            );
        }

        if (!InfraString::isBolVazia($strEmailSistema) && !InfraString::isBolVazia($strEmailAdministrador)) {
            try {
                $strDetalhes = '';
                foreach ($arrFalhas as $i => $falha) {
                    $strDetalhes .= ($i + 1) . '. ' . $falha['contexto'] . "\n"
                        . '   Erro: ' . $falha['erro'] . "\n\n";
                }

                MailSEI::getInstance()->limpar();
                $objEmailDTO = new EmailDTO();
                $objEmailDTO->setStrDe($strEmailSistema);
                $objEmailDTO->setStrPara($strEmailAdministrador);
                $objEmailDTO->setStrAssunto('SEI - Falha no envio de e-mail de peticionamento intercorrente');
                $objEmailDTO->setStrMensagem(
                    'Prezado(a) Administrador(a),' . "\n\n"
                    . 'O sistema registrou ' . count($arrFalhas) . ' falha(s) no envio de e-mail'
                    . ' de notificacao do peticionamento intercorrente (processo ' . $idProcedimento . ').' . "\n\n"
                    . 'Detalhes das falhas:' . "\n\n"
                    . $strDetalhes
                    . 'O peticionamento foi concluido com sucesso. Apenas o envio de e-mail falhou.' . "\n\n"
                    . 'Atenciosamente,' . "\n"
                    . 'SEI - Sistema Eletronico de Informacoes'
                );
                MailSEI::getInstance()->adicionar($objEmailDTO);
                MailSEI::getInstance()->enviar();
            } catch (\Throwable $eAdmin) {
                LogSEI::getInstance()->gravar(
                    'Falha ao notificar administrador sobre erros de e-mail do peticionamento intercorrente'
                    . ' (processo ' . $idProcedimento . ')'
                    . ': ' . $eAdmin->getMessage(),
                    InfraLog::$INFORMACAO
                );
            }
        }
    }

}
?>