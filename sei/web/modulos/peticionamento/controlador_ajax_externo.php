<?
/**
 * ANATEL
 *
 * 
 * 22/07/2016 - criado por marcelo.bezerra@cast.com.br - CAST
 * Arquivo para realizar controle requisição ajax de usuario externo no modulo peticionamento.
 */

try{
    require_once dirname(__FILE__).'/../../SEI.php';
    session_start();
    
    InfraAjax::decodificarPost();
  
 switch($_GET['acao_ajax_externo']){

	case 'md_pet_contato_pj_vinculada':

		// buscando primeira unidade para simular login para conseguir fazer a "pesquisarRN0471"
		SessaoSEIExterna::getInstance();
		$seiRN = new SeiRN();
		$objEntradaConsultarDocumentoAPI = new EntradaListarUnidadesAPI();
		$objSaidaConsultarDocumentoAPI = $seiRN->listarUnidades($objEntradaConsultarDocumentoAPI);
		SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(), $objSaidaConsultarDocumentoAPI[0]->getIdUnidade());

		if( ($_POST['id_tipo_contexto_contato'] != null && $_POST['id_tipo_contexto_contato'] != "" )
			&&
			($_POST['palavras_pesquisa'] != null && $_POST['palavras_pesquisa'] != "")
		) {

			$objContatoRN = new ContatoRN();
			$objContextoContatoDTO = new ContatoDTO();

			$objContextoContatoDTO->retNumIdContato();
			$objContextoContatoDTO->retStrNome();

			//trazer todos que sejam empresas (CNPJ diferente de null), estejam ativos,
			//e atenda ao filtro por nome e tipo de contexto informado na tela

			$objContextoContatoDTO->adicionarCriterio(
					//alteracoes seiv3
					array('Cnpj','Nome', 'SinAtivo', 'IdTipoContato'),
					array(InfraDTO::$OPER_DIFERENTE,InfraDTO::$OPER_LIKE, InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL ),
					array(null, "%".$_POST['palavras_pesquisa']."%", 'S', $_POST['id_tipo_contexto_contato'] ),
					array( InfraDTO::$OPER_LOGICO_AND , InfraDTO::$OPER_LOGICO_AND , InfraDTO::$OPER_LOGICO_AND )
			);

			$objContextoContatoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

			$arrObjContatoDTO = $objContatoRN->pesquisarRN0471( $objContextoContatoDTO );
			$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjContatoDTO,'IdContato', 'Nome');
			InfraAjax::enviarXML($xml);

		}
		break;

	case 'md_pet_contato_auto_completar_contexto_pesquisa':

		//alterado para atender anatel exibir apenas nome contato
		$objContatoDTO = new ContatoDTO();
  		$objContatoDTO->retNumIdContato();
  		$objContatoDTO->retStrSigla();
  		$objContatoDTO->retStrNome();  		
  		$objContatoDTO->setStrPalavrasPesquisa($_POST['extensao']);
  		
  		$objContatoDTO->adicionarCriterio(
  				array('SinAtivo','Nome'),
  				array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_LIKE ),
  				array('S', '%'.$_POST["extensao"]. '%' ),
  				array( InfraDTO::$OPER_LOGICO_AND ) 
  		);
  		
  		$objContatoDTO->setNumMaxRegistrosRetorno(50);
  		$objContatoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objMdPetRelTpCtxContatoDTO = new MdPetRelTpCtxContatoDTO();
        $objMdPetTpCtxContatoRN = new MdPetTpCtxContatoRN();
        $objMdPetRelTpCtxContatoDTO->retTodos();
        $arrobjMdPetRelTpCtxContatoDTO = $objMdPetTpCtxContatoRN->listar( $objMdPetRelTpCtxContatoDTO );
        
        if(!empty($arrobjMdPetRelTpCtxContatoDTO)){
            
        	$arrId = array();
            
            foreach($arrobjMdPetRelTpCtxContatoDTO as $item){
                array_push($arrId, $item->getNumIdTipoContextoContato());
            }

            //alteracoes seiv3
            $objContatoDTO->adicionarCriterio(array('IdTipoContato', 'IdTipoContato'),
                array(InfraDTO::$OPER_IN, InfraDTO::$OPER_IGUAL),
                array($arrId, null), 
            	array( InfraDTO::$OPER_LOGICO_OR));
        }

        $objMdPetContatoRN = new MdPetContatoRN();
        $arrObjContatoDTO = $objMdPetContatoRN->pesquisar($objContatoDTO);        
        $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjContatoDTO,'IdContato', 'Nome');
        InfraAjax::enviarXML($xml);
        break;

	case 'md_pet_cargo_montar_select_genero':
		// para uso com usuário externo - clone de controlador.ajax->cargo_montar_select_genero
		SessaoSEIExterna::getInstance();

		$strOptions = MdPetTpCtxContatoINT::montarSelectGeneroComTratamentoEVocativo($_POST['primeiroItemValor'],$_POST['primeiroItemDescricao'],$_POST['valorItemSelecionado'],$_POST['staGenero']);

		$xml = InfraAjax::gerarXMLSelect($strOptions);

		InfraAjax::enviarXML($xml);
		break;

	case 'md_pet_cargo_dados':
		// para uso com usuário externo - clone de controlador.ajax->cargo_dados 
		SessaoSEIExterna::getInstance();

		$objCargoDTO = new CargoDTO();
		$objCargoDTO->setBolExclusaoLogica(false);
		$objCargoDTO->retStrExpressaoTratamento();
		$objCargoDTO->retStrExpressaoVocativo();
		$objCargoDTO->setNumIdCargo($_POST['id_cargo']);

		$objCargoRN = new CargoRN();
		$objCargoDTO = $objCargoRN->consultarRN0301($objCargoDTO);

		if ($objCargoDTO!=null){
			$xml = InfraAjax::gerarXMLComplementosArrInfraDTO($objCargoDTO,array('ExpressaoTratamento','ExpressaoVocativo'));
		}

		InfraAjax::enviarXML($xml);
		break;


	 case 'get_acoes_intimacao_lista':

		 SessaoSEIExterna::getInstance();

		 $idMdPetDest		= $_POST['dataAttributes']['idmdpetdest'];
		 $docPrinc 			= $_POST['dataAttributes']['docprinc'];
		 $docTipo			= $_POST['dataAttributes']['doctipo'];
		 $idSituacao 		= $_POST['dataAttributes']['idsituacao'];
		 $descricao 		= $_POST['dataAttributes']['descricao'];
		 $idAcExt 			= $_POST['dataAttributes']['idacext'];
		 $tpProcesso 		= $_POST['dataAttributes']['tpprocesso'];
		 $idProcesso 		= $_POST['dataAttributes']['idprocesso'];
		 $idIntimacao 		= $_POST['dataAttributes']['idintimacao'];
		 $idUsuarioExterno 	= $_POST['dataAttributes']['idusuariouxterno'];
		 $idDocCert 		= (new MdPetIntAceiteRN())->getIdCertidaoPorIntimacao(array($idIntimacao, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));

		 $strResultado 		= '';

		 //Ação Consulta
		 if (!is_null($idProcesso)) {

			 $strResultado .= (new MdPetIntRelDestinatarioRN())->addConsultarProcesso($idProcesso, $tpProcesso, $idAcExt, $descricao);

			 if (!is_null($idSituacao) && $idSituacao != MdPetIntimacaoRN::$INTIMACAO_PENDENTE) {

				 $docNum = '';
				 $idDocCert = (new MdPetIntAceiteRN())->getIdCertidaoPorIntimacao(array($idIntimacao, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));

				 $strResultado .= (new MdPetIntCertidaoRN())->addIconeAcessoCertidao(array($docPrinc, $idIntimacao, $idAcExt, $idDocCert));

				 //RECIBO
				 //Próprio Processo
				 $isRelacionado = false;
				 $objMdPetReciboDTO = new MdPetReciboDTO();
				 $objMdPetReciboDTO->retTodos();
				 $objMdPetReciboDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO);
				 $objMdPetReciboDTO->setNumIdProtocolo($idProcesso);
				 $objMdPetReciboDTO->unSetDblIdProtocoloRelacionado();
				 $arrObjMdPetReciboDTO = (new MdPetReciboRN())->listar($objMdPetReciboDTO);

				 if (empty($arrObjMdPetReciboDTO)) {
					 //Relacionado
					 $isRelacionado = true;
					 $objMdPetReciboDTO = new MdPetReciboDTO();
					 $objMdPetReciboDTO->retTodos();
					 $objMdPetReciboDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO);
					 $objMdPetReciboDTO->unSetNumIdProtocolo();
					 $objMdPetReciboDTO->setDblIdProtocoloRelacionado($idProcesso);
					 $arrObjMdPetReciboDTO = (new MdPetReciboRN())->listar($objMdPetReciboDTO);
				 }

				 if(!empty($arrObjMdPetReciboDTO)){

					 foreach ($arrObjMdPetReciboDTO as $objMdPetReciboDTO) {

						 $usuarioDTO = new UsuarioDTO();
						 $usuarioDTO->retNumIdUsuario();
						 $usuarioDTO->retNumIdContato();
						 $usuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
						 $usuarioDTO = (new UsuarioRN())->consultarRN0489($usuarioDTO);

						 $emailDestinatario = SessaoSEIExterna::getInstance()->getStrSiglaUsuarioExterno();
						 $acessoExtDTO = new AcessoExternoDTO();
						 $acessoExtDTO->retTodos();
						 $acessoExtDTO->setOrd("IdAcessoExterno", InfraDTO::$TIPO_ORDENACAO_DESC);
						 $acessoExtDTO->retDblIdProtocoloAtividade();
						 $acessoExtDTO->retNumIdContatoParticipante();

						 //trazer acesso externo  mais recente, deste processo, para este usuario externo, que estejam dentro da data de validade
						 $acessoExtDTO->setDblIdProtocoloAtividade($objMdPetReciboDTO->getNumIdProtocolo());

						 $acessoExtDTO->setNumIdContatoParticipante($usuarioDTO->getNumIdContato());
						 $acessoExtDTO->setStrEmailDestinatario($emailDestinatario);
						 $acessoExtDTO->setStrStaTipo(AcessoExternoRN::$TA_USUARIO_EXTERNO);
						 $acessoExtDTO->setStrSinAtivo('S');

						 //Verificar se traz somente o do acesso atual ou do relacionado desta intimação (linha 1215)
						 //$acessoExtDTO->setNumIdAcessoExterno($idAcessoExterno);
						 //@todo adicionar verificaçao de data de validade do acesso externo

						 $arrAcessosExternos = (new AcessoExternoRN())->listar($acessoExtDTO);

						 if (is_array($arrAcessosExternos) && count($arrAcessosExternos) > 0) {

							 $id_acesso_ext_link = $arrAcessosExternos[0]->getNumIdAcessoExterno();

							 $docLink = "documento_consulta_externa.php?id_acesso_externo=" . $id_acesso_ext_link;
							 $docLink .= "&id_documento=" . $objMdPetReciboDTO->getDblIdDocumento();
							 $docLink .= "&id_orgao_acesso_externo=0";

							 //se nao configurar acesso externo ANTES, a assinatura do link falha:
							 SessaoSEIExterna::getInstance()->configurarAcessoExterno($id_acesso_ext_link);
							 $linkAssinado = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink($docLink));

							 $strResultado .= (new MdPetIntReciboRN())->addIconeRecibo(array($objMdPetReciboDTO->getDthDataHoraRecebimentoFinal(), $docPrinc, $docTipo, $docNum, $linkAssinado, $objMdPetReciboDTO->getDblIdDocumento(), $idMdPetDest));

						 }

					 }

				 }

			 }

		 }

		 if (!is_numeric($strResultado)) {
			 $strResultado = str_replace('&', '&amp;', $strResultado);
			 $strResultado = str_replace('<', '&amp;lt;', $strResultado);
			 $strResultado = str_replace('>', '&amp;gt;', $strResultado);
			 $strResultado = str_replace('\"', '&amp;quot;', $strResultado);
			 $strResultado = str_replace('"', '&amp;quot;', $strResultado);
		 }

		 if(!empty($strResultado)){
			 InfraAjax::enviarXML('<actions>'.$strResultado.'</actions>');
		 }

	 	break;


	default:
      throw new InfraException("Ação '".$_GET['acao_ajax_externo']."' não reconhecida pelo controlador AJAX externo.");
  }
  
}catch(Exception $e){
  InfraAjax::processarExcecao($e);
}
