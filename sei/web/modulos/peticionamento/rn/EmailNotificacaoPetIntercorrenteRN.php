<?
/**
* ANATEL
*
* 28/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class EmailNotificacaoPetIntercorrenteRN extends EmailNotificacaoPeticionamentoRN {

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
        $objOrgaoDTO->retTodos(true);
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

        //obtendo o tipo de procedimento
        $idTipoProc = $arrParametros['id_tipo_procedimento'];
        $objTipoProcDTO = new TipoProcessoPeticionamentoDTO();
        $objTipoProcDTO->retTodos(true);
        $objTipoProcDTO->retStrNomeSerie();
        $objTipoProcDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
        $objTipoProcRN = new TipoProcessoPeticionamentoRN();
        $objTipoProcDTO = $objTipoProcRN->consultar( $objTipoProcDTO );

        //variaveis basicas em uso no email
        //$linkLoginUsuarioExterno = $objOrgaoDTO->getStrSitioInternet();
        //$linkLoginUsuarioExterno = $this->getObjInfraSessao()->getStrPaginaLogin()
        //$linkLoginUsuarioExterno = SessaoSEIExterna::getInstance()->getStrPaginaLogin();
        $linkLoginUsuarioExterno = SessaoSEIExterna::getInstance()->getStrPaginaLogin() . '&id_orgao_acesso_externo=0';


        $strNomeTipoProcedimento = $objProcedimentoDTO->getStrNomeTipoProcedimento();
        $strProtocoloFormatado = $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado();
        $strSiglaUnidade = $objUnidadeDTO->getStrSigla();

        $strSiglaSistema = SessaoSEIExterna::getInstance()->getStrSiglaSistema();
        $strEmailSistema = $objInfraParametro->getValor('SEI_EMAIL_SISTEMA');

        $strSiglaOrgao = $objOrgaoDTO->getStrSigla();
        $strSiglaOrgaoMinusculas = InfraString::transformarCaixaBaixa($objOrgaoDTO->getStrSigla());
        $strSufixoEmail = $objInfraParametro->getValor('SEI_SUFIXO_EMAIL');

        //$strNomeContato = $objProcedimentoDTO->getStrNome();
        //$strEmailContato = $objProcedimentoDTO->getStrEmail();

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
        $idSerieParam = $objInfraParametro->getValor('ID_SERIE_RECIBO_MODULO_PETICIONAMENTO');

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
        $objEmailSistemaDTO->setStrIdEmailSistemaModulo( EmailNotificacaoPeticionamentoRN::$EMAIL_PETICIONAMENTO_USUARIO_PETICIONANTE );

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

            if ($reciboDTOBasico->getStrStaTipoPeticionamento()=="N"){
                $strConteudo = str_replace('@tipo_peticionamento@',"Processo Novo",$strConteudo);
            }else if ($reciboDTOBasico->getStrStaTipoPeticionamento()=="I"){
                $strConteudo = str_replace('@tipo_peticionamento@',"Intercorrente",$strConteudo);
            }

            $strConteudo = str_replace('@sigla_unidade_abertura_do_processo@', $strSiglaUnidade ,$strConteudo);
            $strConteudo = str_replace('@descricao_unidade_abertura_do_processo@',$objUnidadeDTO->getStrDescricao(),$strConteudo);
            $strProtocoloFormatado = '';

            $strConteudo = str_replace('@documento_recibo_eletronico_de_protocolo@',$documentoDTO->getStrProtocoloDocumentoFormatado(), $strConteudo);
            $strConteudo = str_replace('@sigla_orgao@',$objOrgaoDTO->getStrSigla(),$strConteudo);
            $strConteudo = str_replace('@descricao_orgao@',$objOrgaoDTO->getStrDescricao(),$strConteudo);
            $strConteudo = str_replace('@sitio_internet_orgao@',$objOrgaoDTO->getStrSitioInternetContato(),$strConteudo);

            InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strConteudo);

        }

        //================================================================================================
        //EMAIL PARA A UNIDADE DE ABERTURA DO PETICIONAMENTO
        //================================================================================================

        $objEmailSistemaDTO = new EmailSistemaDTO();
        $objEmailSistemaDTO->retStrDe();
        $objEmailSistemaDTO->retStrPara();
        $objEmailSistemaDTO->retStrAssunto();
        $objEmailSistemaDTO->retStrConteudo();
        $objEmailSistemaDTO->setStrIdEmailSistemaModulo( EmailNotificacaoPeticionamentoRN::$EMAIL_PETICIONAMENTO_UNIDADE_ABERTURA );

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

	            if ($reciboDTOBasico->getStrStaTipoPeticionamento()=="N"){
	                $strConteudo = str_replace('@tipo_peticionamento@',"Processo Novo",$strConteudo);
	            }else if ($reciboDTOBasico->getStrStaTipoPeticionamento()=="I"){
	                $strConteudo = str_replace('@tipo_peticionamento@',"Intercorrente",$strConteudo);
	            }

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
		
	                if (count($arrObjUnidadeProcIndicDTO)>0){
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
                	InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strConteudo);
	            }
            }
        }
    }
}
?>