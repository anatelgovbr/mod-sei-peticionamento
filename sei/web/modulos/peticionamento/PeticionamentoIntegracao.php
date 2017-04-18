<?
/**
 * ANATEL
 *
 * 23/11/2016 - criado por marcelo.bezerra - CAST
 *
 */

require_once dirname(__FILE__).'/util/MdPetDataUtils.php';

class PeticionamentoIntegracao extends SeiIntegracao {

  public function __construct(){
  }

  public function getNome(){
    return 'Peticionamento e Intimação Eletrônicos';
  }

  public function getVersao() {
    return '1.1.0';
  }

  public function getInstituicao(){
    return 'ANATEL (Projeto Colaborativo no Portal do SPB)';
  }

  public function inicializar($strVersaoSEI){

  }

  public function processarControlador($strAcao){

    switch($strAcao) {

	       case 'md_pet_extensoes_arquivo_cadastrar' :
			require_once dirname ( __FILE__ ) . '/md_pet_extensoes_arquivo_cadastro.php';
			return true;

	       case 'md_pet_tamanho_arquivo_cadastrar' :
			require_once dirname ( __FILE__ ) . '/md_pet_tamanho_arquivo_cadastro.php';
			return true;

	       // SEM UTILIZAÇÃO EM TELA
	       //case 'md_pet_tamanho_arquivo_listar' :
	       //	return true;

	       // SEM UTILIZAÇÃO EM TELA
	       //case 'md_pet_tamanho_arquivo_consultar' :
	       //	return true;


	       case 'md_pet_indisponibilidade_listar' :
	       case 'md_pet_indisponibilidade_desativar' :
	       case 'md_pet_indisponibilidade_reativar' :
	       case 'md_pet_indisponibilidade_excluir' :
			require_once dirname ( __FILE__ ) . '/md_pet_indisponibilidade_lista.php';
			return true;

	       case 'md_pet_indisponibilidade_cadastrar':
	       case 'md_pet_indisponibilidade_consultar':
	       case 'md_pet_indisponibilidade_alterar':
	       case 'md_pet_indisponibilidade_upload_anexo':
	       case 'md_pet_indisponibilidade_download':
			require_once dirname ( __FILE__ ) . '/md_pet_indisponibilidade_cadastro.php';
			return true;

	       case 'md_pet_tipo_processo_listar' :
	       case 'md_pet_tipo_processo_desativar' :
	       case 'md_pet_tipo_processo_reativar':
	       case 'md_pet_tipo_processo_excluir':
			require_once dirname ( __FILE__ ) . '/md_pet_tipo_processo_lista.php';
			return true;

	       case 'md_pet_tipo_processo_cadastrar':
	       case 'md_pet_tipo_processo_alterar':
	       case 'md_pet_tipo_processo_consultar':
	       case 'md_pet_tipo_processo_salvar':
			require_once dirname ( __FILE__ ) . '/md_pet_tipo_processo_cadastro.php';
			return true;

	       case 'md_pet_tipo_processo_cadastrar_orientacoes':
			require_once dirname ( __FILE__ ) . '/md_pet_tipo_processo_cadastro_orientacoes.php';
			return true;

//	       case 'tipo_procedimento_selecionar':
//			require_once dirname ( __FILE__ ) . '/tipo_procedimento_lista.php';
//			return true;

	       case 'md_pet_serie_selecionar':
			require_once dirname ( __FILE__ ) . '/md_pet_serie_lista.php';
			return true;

	       case 'md_pet_menu_usu_ext_listar' :
	       case 'md_pet_menu_usu_ext_desativar' :
	       case 'md_pet_menu_usu_ext_reativar':
	       case 'md_pet_menu_usu_ext_excluir':
			require_once dirname ( __FILE__ ) . '/md_pet_menu_usu_ext_lista.php';
			return true;

	       case 'md_pet_menu_usu_ext_cadastrar':
	       case 'md_pet_menu_usu_ext_alterar':
	       case 'md_pet_menu_usu_ext_consultar':
			require_once dirname ( __FILE__ ) . '/md_pet_menu_usu_ext_cadastro.php';
			return true;

	       case 'md_pet_pagina_conteudo_externo':
			require_once dirname ( __FILE__ ) . '/md_pet_pagina_conteudo_externo.php';
			return true;

	       case 'md_pet_tp_ctx_contato_cadastrar':
			require_once dirname ( __FILE__ ) . '/md_pet_tp_ctx_contato_cadastro.php';
			return true;

	       case 'md_pet_hipotese_legal_nl_acesso_cadastrar':
			require_once dirname ( __FILE__ ).'/md_pet_hipotese_legal_nl_acesso_cadastro.php';
			return true;

	       case 'md_pet_hipotese_legal_selecionar':
			require_once dirname ( __FILE__ ).'/md_pet_hipotese_legal_lista.php';
			return true;

		   //ADMINISTRAÇÃO DE CRITÉRIOS PARA INTERCORRENTE
	       case 'md_pet_intercorrente_criterio_listar' :
	       case 'md_pet_intercorrente_criterio_desativar' :
	       case 'md_pet_intercorrente_criterio_reativar':
	       case 'md_pet_intercorrente_criterio_excluir':
			require_once dirname ( __FILE__ ) . '/md_pet_intercorrente_criterio_lista.php';
			return true;

	       case 'md_pet_intercorrente_criterio_cadastrar':
	       case 'md_pet_intercorrente_criterio_alterar':
	       case 'md_pet_intercorrente_criterio_consultar':
			require_once dirname ( __FILE__ ) . '/md_pet_intercorrente_criterio_cadastro.php';
			return true;

	       case 'md_pet_intercorrente_criterio_padrao':
			require_once dirname ( __FILE__ ) . '/md_pet_intercorrente_criterio_padrao.php';
			return true;

	       case 'md_pet_arquivo_extensao_selecionar':
			require_once dirname ( __FILE__ ).'/md_pet_arquivo_extensao_lista.php';
			return true;

//	       case 'md_pet_int_prazo_tacita_cadastrar':
//	       case 'md_pet_int_prazo_tacita_alterar':
//			require_once dirname ( __FILE__ ).'/md_pet_int_prazo_tacita_cadastro.php';
//			return true;

//		case 'md_pet_int_serie_cadastrar':
//			require_once dirname ( __FILE__ ).'/md_pet_int_serie_cadastro.php';
//			return true;
                
    }

    return false;
  }

  public function processarControladorAjax($strAcao){

    $xml = null;

		switch($_GET['acao_ajax']){

			case 'md_pet_serie_auto_completar':
				$arrObjSerieDTO = MdPetSerieINT::autoCompletarSeries( $_POST['palavras_pesquisa'] , $_POST['tipoDoc']);
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjSerieDTO,'IdSerie', 'Nome');
				break;

			// NÃO ENCONTRADO USO
			//case 'serie_auto_completar':
			//	$arrObjSerieDTO = MdPetSerieINT::autoCompletarSeries( $_POST['palavras_pesquisa'] , false);
			//	$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjSerieDTO,'IdSerie', 'Nome');
			//	break;

			case 'md_pet_tipo_processo_auto_completar':
				$arrObjTipoProcessoDTO = TipoProcedimentoINT::autoCompletarTipoProcedimento($_POST['palavras_pesquisa']);
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjTipoProcessoDTO,'IdTipoProcedimento', 'Nome');
				break;

			case 'md_pet_intercorrente_tipo_processo_auto_completar':
				$arrObjTipoProcessoDTO = MdPetTipoProcessoINT::autoCompletarTipoProcedimento($_POST['palavras_pesquisa'], $_POST['itens_selecionados'] );
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjTipoProcessoDTO,'IdTipoProcedimento', 'Nome');
				break;

			// NÃO ENCONTRADO USO
			//case 'tipo_processo_auto_completar_com_assunto':
			//	$arrObjTipoProcessoDTO = TipoProcedimentoINT::autoCompletarTipoProcessoComAssunto($_POST['palavras_pesquisa'] );
			//	$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjTipoProcessoDTO,'IdTipoProcedimento', 'Nome');
			//	break;

			case 'md_pet_unidade_auto_completar':
				$arrObjUnidadeDTO = UnidadeINT::autoCompletarUnidades($_POST['palavras_pesquisa'], true, '');
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjUnidadeDTO,'IdUnidade', 'Sigla');
				break;

			case 'md_pet_tipo_processo_nivel_acesso_auto_completar':
				$arrObjNivelAcessoDTO = MdPetTipoProcessoINT::montarSelectNivelAcesso(null, null,  null, $_POST['idTipoProcesso']);
				$xml = InfraAjax::gerarXMLSelect($arrObjNivelAcessoDTO);
				break;

			case 'md_pet_tipo_processo_nivel_acesso_validar':
				$xml = MdPetTipoProcessoINT::validarNivelAcesso($_POST);
				break;

			// NÃO ENCONTRADO USO
			//case 'md_pet_tipo_processo_assunto_validar':
			//	$xml = MdPetTipoProcessoINT::validarTipoProcessoComAssunto($_POST);
			//	break;

			case 'md_pet_tp_ctx_contato_listar':
				$arrObjTipoContextoDTO = MdPetTpCtxContatoINT::montarSelectNome(null, null, $_POST['extensao']);
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjTipoContextoDTO, 'IdTipoContato', 'Nome');
				break;

			case 'md_pet_hipotese_rest_auto_completar':
				$arrObjHipoteseLegalDTO = MdPetHipoteseLegalINT::autoCompletarHipoteseLegal($_POST['palavras_pesquisa'], ProtocoloRN::$NA_RESTRITO);
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjHipoteseLegalDTO, 'IdHipoteseLegal', 'Nome');
				break;

			case 'md_pet_arquivo_extensao_listar_todos':
				$arrObjMdPetArquivoExtensaoDTO = MdPetArquivoExtensaoINT::autoCompletarExtensao($_POST['extensao']);
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjMdPetArquivoExtensaoDTO,'IdArquivoExtensao', 'Extensao');
				break;

			// NÃO ENCONTRADO USO
			//case 'buscar_unidade_id':
			//    $xml = MdPetTipoProcessoINT::retornarUnidadeSelecionada($_POST['id_unidade']);
			//    break;

		}

    	return $xml;
  }

  public function processarControladorPublicacoes($strAcao){

    switch($strAcao) {

      case 'abc_publicacao_exemplo':
        require_once dirname(__FILE__) . '/publicacao_exemplo.php';
        return true;
    }

    return false;
  }

  public function processarControladorExterno($strAcao){

    switch($strAcao) {

		    case 'md_pet_pagina_conteudo_externo':
		  		require_once dirname ( __FILE__ ) . '/md_pet_pagina_conteudo_externo.php';
		  		return true;

		  	case 'md_pet_usu_ext_indisponibilidade_listar' :
		  		require_once dirname ( __FILE__ ) . '/md_pet_usu_ext_indisponibilidade_lista.php';
		  		return true;

  		    case 'md_pet_usu_ext_indisponibilidade_consultar':
		    case 'md_pet_usu_ext_indisponibilidade_download':
		  		require_once dirname ( __FILE__ ) . '/md_pet_usu_ext_indisponibilidade_cadastro.php';
		  		return true;

		  	//peticionamento intercorrente
		  	case 'md_pet_intercorrente_usu_ext_cadastrar':
		  		require_once dirname ( __FILE__ ) . '/md_pet_intercorrente_usu_ext_cadastro.php';
		  		return true;

		    case 'md_pet_intercorrente_usu_ext_concluir':
			case 'md_pet_intercorrente_usu_ext_assinar':
				require_once dirname ( __FILE__ ) . '/md_pet_intercorrente_usu_ext_concluir.php';
				return true;
		
			//novo peticionamento - 5152
	  		case 'md_pet_usu_ext_iniciar':
	  			require_once dirname ( __FILE__ ) . '/md_pet_usu_ext_inicio.php';
	  			return true;


			case 'md_pet_usu_ext_cadastrar':
		  		require_once dirname ( __FILE__ ) . '/md_pet_usu_ext_cadastro.php';
		  		return true;

			// NÃO ENCONTRADO USO
			//case 'peticionamento_interessado_usuario_externo_cadastrar':
			//		require_once dirname ( __FILE__ ) . '/peticionamento_interessado_usuario_externo_cadastro.php';
			//		return true;

		  	case 'peticionamento_usuario_externo_concluir':
		  		require_once dirname ( __FILE__ ) . '/md_pet_usu_ext_concluir.php';
		  		return true;

		  	//peticionamento intercorrente - Janela de concluir peticionamento
		  	case 'md_pet_intercorrente_usu_ext_concluir':
		  		require_once dirname ( __FILE__ ) . '/md_pet_intercorrente_usu_ext_conclusao.php';
		  		return true;

		  	//consulta de recibo - 5153
		  	case 'md_pet_usu_ext_recibo_listar':
		  		require_once dirname ( __FILE__ ) . '/md_pet_usu_ext_recibo_lista.php';
		  		return true;

		  	case 'md_pet_usu_ext_recibo_consultar':
		  			require_once dirname ( __FILE__ ) . '/md_pet_usu_ext_recibo_consulta.php';
		  			return true;

			//Consulta de Recibo - EU7050
		    case 'md_pet_intercorrente_usu_ext_recibo_consultar':
				   require_once dirname ( __FILE__ ) . '/md_pet_intercorrente_usu_ext_recibo_consulta.php';
				   return true;

			// NÃO ENCONTRADO USO
		  	//peticionamento intercorrente - tela de detalhe de recibo de peticionamento intercorrente
		  	//case 'md_pet_intercorrente_recibo_usu_ext_consultar':
		  	//		require_once dirname ( __FILE__ ) . '/md_pet_intercorrente_recibo_usu_ext_consulta.php';
		  	//		return true;

  			case 'md_pet_interessado_cadastro':
  				    require_once dirname ( __FILE__ ) . '/md_pet_interessado_cadastro.php';
  				    return true;

  		    case 'md_pet_contato_selecionar':
  		    		require_once dirname ( __FILE__ ) . '/md_pet_contato_selecionar.php';
  				    return true;

  			case 'md_pet_usu_ext_upload_anexo':
		  		if (isset($_FILES['fileArquivoEssencial'])){
		  			PaginaSEIExterna::getInstance()->processarUpload('fileArquivoEssencial', DIR_SEI_TEMP, true);
		  		}
		  		die;

		  	case 'md_pet_usu_ext_upload_doc_principal':

		  		if (isset($_FILES['fileArquivoPrincipal'])){

		  			PaginaSEIExterna::getInstance()->processarUpload('fileArquivoPrincipal', DIR_SEI_TEMP, false);
		  		}
		  		die;

		  	case 'md_pet_usu_ext_upload_doc_essencial':

		  		if (isset($_FILES['fileArquivoEssencial'])){

		  			PaginaSEIExterna::getInstance()->processarUpload('fileArquivoEssencial', DIR_SEI_TEMP, false);
		  		}
		  		die;

			case 'md_pet_usu_ext_upload_doc_complementar':

		  		if (isset($_FILES['fileArquivoComplementar'])){

		  			PaginaSEIExterna::getInstance()->processarUpload('fileArquivoComplementar', DIR_SEI_TEMP, false);
		  		}
		  		die;

			// NÃO ENCONTRADO USO - ver 30 linhas acima
  			//case 'md_pet_usu_ext_upload_anexo':
  			case 'md_pet_usu_ext_download':
  					require_once dirname ( __FILE__ ) . '/md_pet_usu_ext_cadastro.php';
  					return true;

  			case 'md_pet_editor_montar':
  			case 'md_pet_editor_imagem_upload':
  			    	require_once dirname ( __FILE__ ) . '/md_pet_editor_usuario_externo_processar.php';
  			    	return true;

  			case 'md_pet_validar_documento_principal':

  				  $conteudo = "";

  				  if( SessaoSEIExterna::getInstance()->isSetAtributo('docPrincipalConteudoHTML') ){
  				  	$conteudo = SessaoSEIExterna::getInstance()->getAtributo('docPrincipalConteudoHTML');
  				  }

  				  echo $conteudo;
  				  return true;

  			case 'md_pet_contato_cpf_cnpj':

  				$cpfcnpj =  $_POST['cpfcnpj'];
  				$cpfcnpj = str_replace(".","", $cpfcnpj );
				$cpfcnpj = str_replace("-","", $cpfcnpj );
				$cpfcnpj = str_replace("/","", $cpfcnpj );

				$objContextoContatoDTO = MdPetContatoINT::getTotalContatoByCPFCNPJ( $cpfcnpj );

				if(count($objContextoContatoDTO)>0) {
					$objContato = new stdClass();
					$objContato->usuario = $objContextoContatoDTO[0]->getNumIdUsuarioCadastro();
					$objContato->nome =  utf8_encode( $objContextoContatoDTO[0]->getStrNome() );
					$objContato->id = utf8_encode( $objContextoContatoDTO[0]->getNumIdContato() );
					$objContato->nomeTratado = utf8_encode( PaginaSEI::tratarHTML($objContextoContatoDTO[0]->getStrNome()) );
					$json = json_encode( $objContato , JSON_FORCE_OBJECT);
				}else{
					$json = null;
				}

				echo $json;
				return true;
				break;

  			 //EU7050
			case 'md_pet_processo_validar_numero':
				$xml = MdPetIntercorrenteINT::gerarXMLvalidacaoNumeroProcesso($_POST['txtNumeroProcesso']);
				echo $xml;

				return true;

        case 'md_pet_usu_ext_upload_arquivo':
            if (isset($_FILES['fileArquivo'])) {
                PaginaSEIExterna::getInstance()->processarUpload('fileArquivo', DIR_SEI_TEMP, false);
            }
            die;

        case 'md_pet_montar_select_tipo_documento':
            $strOptions = MdPetIntercorrenteINT::montarSelectTipoDocumento('null', ' ', 'null');
            $xml        = InfraAjax::gerarXMLSelect($strOptions);
            InfraAjax::enviarXML($xml);

            return true;

        case 'md_pet_montar_select_nivel_acesso':
            $strOptions = MdPetTipoProcessoINT::montarSelectNivelAcesso('null', '', 'null', $_POST['id_tipo_procedimento']);
            $xml        = InfraAjax::gerarXMLSelect($strOptions);
            InfraAjax::enviarXML($xml);

            return true;

        case 'md_pet_verificar_criterio_intercorrente':
            $arrNivelAcessoHipoteseLegal = MdPetIntercorrenteINT::verificarCriterioIntercorrente($_POST['idTipoProcedimento']);
            echo json_encode($arrNivelAcessoHipoteseLegal);
            return true;
    }

         return false;
  }

  public function montarIconeProcesso(ProcedimentoAPI $objProcedimentoAPI){

  	$arrObjArvoreAcaoItemAPI = array();
  	$dblIdProcedimento = $objProcedimentoAPI->getIdProcedimento();
  	$reciboRN = new MdPetReciboRN();

  	//verificar se este processo é de peticionamento intercorrente
  	$reciboIntercorrenteDTO = new MdPetReciboDTO();
  	$reciboIntercorrenteDTO->retNumIdProtocolo();
  	$reciboIntercorrenteDTO->retStrStaTipoPeticionamento();
  	$reciboIntercorrenteDTO->retDthDataHoraRecebimentoFinal();
  	$reciboIntercorrenteDTO->setNumIdProtocolo( $dblIdProcedimento );
  	$reciboIntercorrenteDTO->setStrStaTipoPeticionamento( MdPetReciboRN::$TP_RECIBO_INTERCORRENTE );
	$reciboIntercorrenteDTO->setOrd('DataHoraRecebimentoFinal', InfraDTO::$TIPO_ORDENACAO_DESC);

  	$arrRecibosIntercorrentes = $reciboRN->listar( $reciboIntercorrenteDTO );

  	if( $arrRecibosIntercorrentes != null && count( $arrRecibosIntercorrentes ) > 0){

  		$recibo = $arrRecibosIntercorrentes[0];
  		$data = MdPetDataUtils::setFormat($recibo->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');
  		$title = 'Peticionamento Eletrônico\nIntercorrente: ' . $data;

  		$objArvoreAcaoItemAPI = new ArvoreAcaoItemAPI();
  		$objArvoreAcaoItemAPI->setTipo('PETICIONAMENTO');
  		$objArvoreAcaoItemAPI->setId('PET' . $dblIdProcedimento);
  		$objArvoreAcaoItemAPI->setIdPai($dblIdProcedimento);
  		$objArvoreAcaoItemAPI->setTitle( $title );
  		$objArvoreAcaoItemAPI->setIcone('modulos/peticionamento/imagens/peticionamento_intercorrente.png');

  		$objArvoreAcaoItemAPI->setTarget(null);
  		$objArvoreAcaoItemAPI->setHref('javascript:;');

  		$objArvoreAcaoItemAPI->setSinHabilitado('S');

  		$arrObjArvoreAcaoItemAPI[] = $objArvoreAcaoItemAPI;

  	} else {

  		//verificar se este processo é de peticionamento
  		$reciboDTO = new MdPetReciboDTO();
  		$reciboDTO->retNumIdProtocolo();
  		$reciboDTO->retDthDataHoraRecebimentoFinal();
  		$reciboDTO->setNumIdProtocolo( $dblIdProcedimento );
  		$arrRecibos = $reciboRN->listar( $reciboDTO );

  		if( $arrRecibos != null && count( $arrRecibos ) > 0){

  			$recibo = $arrRecibos[0];
  			$data = MdPetDataUtils::setFormat($recibo->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');
  			$title = 'Peticionamento Eletrônico\nProcesso Novo: ' . $data;

  			$objArvoreAcaoItemAPI = new ArvoreAcaoItemAPI();
  			$objArvoreAcaoItemAPI->setTipo('PETICIONAMENTO');
  			$objArvoreAcaoItemAPI->setId('PET' . $dblIdProcedimento);
  			$objArvoreAcaoItemAPI->setIdPai($dblIdProcedimento);
  			$objArvoreAcaoItemAPI->setTitle( $title );
  			$objArvoreAcaoItemAPI->setIcone('modulos/peticionamento/imagens/peticionamento_processo_novo.png');

  			$objArvoreAcaoItemAPI->setTarget(null);
  			$objArvoreAcaoItemAPI->setHref('javascript:;');

  			$objArvoreAcaoItemAPI->setSinHabilitado('S');

  			$arrObjArvoreAcaoItemAPI[] = $objArvoreAcaoItemAPI;

  		}

  	}

  	return $arrObjArvoreAcaoItemAPI;

  }

  //Icone exibido na tela "Controle de Processos"
  public function montarIconeControleProcessos($arrObjProcedimentoDTO){

  	$reciboRN = new MdPetReciboRN();
  	$arrParam = array();

  	if( $arrObjProcedimentoDTO != null && count( $arrObjProcedimentoDTO ) > 0 ){

  		foreach( $arrObjProcedimentoDTO as $objProcedimentoAPI  ){

  			//verificando se há algum recibo intercorrente para esse processo
  			$reciboIntercorrenteDTO = new MdPetReciboDTO();
  			$reciboIntercorrenteDTO->retNumIdProtocolo();
  			$reciboIntercorrenteDTO->retStrStaTipoPeticionamento();
  			$reciboIntercorrenteDTO->retDthDataHoraRecebimentoFinal();
  			$reciboIntercorrenteDTO->setNumIdProtocolo($objProcedimentoAPI->getIdProcedimento());
  			$reciboIntercorrenteDTO->setStrStaTipoPeticionamento( MdPetReciboRN::$TP_RECIBO_INTERCORRENTE );
			$reciboIntercorrenteDTO->setOrd('DataHoraRecebimentoFinal', InfraDTO::$TIPO_ORDENACAO_DESC);
  			$arrRecibosIntercorrentes = $reciboRN->listar( $reciboIntercorrenteDTO );

  			if( $arrRecibosIntercorrentes != null && count( $arrRecibosIntercorrentes ) > 0){

  				$recibo = $arrRecibosIntercorrentes[0];
  				$data = MdPetDataUtils::setFormat($recibo->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');
  				$linhaDeCima = '"Peticionamento Eletrônico"';
  				$linhaDeBaixo = '"Intercorrente: ' . $data . '"';
  				$arrParam[$objProcedimentoAPI->getIdProcedimento()] = array("<img src='modulos/peticionamento/imagens/peticionamento_intercorrente.png' onmouseout='return infraTooltipOcultar();' onmouseover='return infraTooltipMostrar(" . $linhaDeBaixo .  "," . $linhaDeCima .  ");' />");

  			} else {

  				//verificando se há algum recibo de processo novo para esse processo
  				$reciboDTO = new MdPetReciboDTO();
  				$reciboDTO->retNumIdProtocolo();
  				$reciboDTO->retDthDataHoraRecebimentoFinal();
  				$reciboDTO->retStrStaTipoPeticionamento();
  				$reciboDTO->setNumIdProtocolo($objProcedimentoAPI->getIdProcedimento());
  				$reciboDTO->setStrStaTipoPeticionamento( MdPetReciboRN::$TP_RECIBO_NOVO );
  				$arrRecibos = $reciboRN->listar( $reciboDTO );

  				if( $arrRecibos != null && count( $arrRecibos ) > 0){

  					$recibo = $arrRecibos[0];
  					$data = MdPetDataUtils::setFormat($recibo->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');
  					$linhaDeCima = '"Peticionamento Eletrônico"';
  					$linhaDeBaixo = '"Processo Novo: ' . $data . '"';
  					$arrParam[$objProcedimentoAPI->getIdProcedimento()] = array("<img src='modulos/peticionamento/imagens/peticionamento_processo_novo.png' onmouseout='return infraTooltipOcultar();' onmouseover='return infraTooltipMostrar(" . $linhaDeBaixo .  "," . $linhaDeCima .  ");' />");
  				}

  			}


  		}

  	}

  	return $arrParam;
  }

  //Icone exibido na tela "Acompanhamento Especial"
  public function montarIconeAcompanhamentoEspecial($arrObjProcedimentoDTO){

  	$reciboRN = new MdPetReciboRN();
  	$arrParam = array();

  	if( $arrObjProcedimentoDTO != null && count( $arrObjProcedimentoDTO ) > 0 ){

  		foreach( $arrObjProcedimentoDTO as $procDTO ){

  			//verificar se este processo é de peticionamento intercorrente
  			$reciboIntercorrenteDTO = new MdPetReciboDTO();
  			$reciboIntercorrenteDTO->retNumIdProtocolo();
  			$reciboIntercorrenteDTO->retStrStaTipoPeticionamento();
  			$reciboIntercorrenteDTO->retDthDataHoraRecebimentoFinal();
  			$reciboIntercorrenteDTO->setNumIdProtocolo($procDTO->getIdProcedimento());
  			$reciboIntercorrenteDTO->setStrStaTipoPeticionamento( MdPetReciboRN::$TP_RECIBO_INTERCORRENTE );
  			$arrRecibosIntercorrentes = $reciboRN->listar( $reciboIntercorrenteDTO );

  			if( $arrRecibosIntercorrentes != null && count( $arrRecibosIntercorrentes ) > 0){

  				$recibo = $arrRecibosIntercorrentes[0];
  				$data = MdPetDataUtils::setFormat($recibo->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');

  				$linhaDeCima = '"Peticionamento Eletrônico"';
  				$linhaDeBaixo = '"Intercorrente: ' . $data . '"';
  				$arrParam[$procDTO->getIdProcedimento()] = array("<img src='modulos/peticionamento/imagens/peticionamento_intercorrente.png' onmouseout='return infraTooltipOcultar();' onmouseover='return infraTooltipMostrar(" . $linhaDeBaixo .  "," . $linhaDeCima .  ");' />");

  			} else {

  				//verificar se este processo é de peticionamento de processo novo
  				$reciboDTO = new MdPetReciboDTO();
  				$reciboDTO->retNumIdProtocolo();
  				$reciboDTO->retDthDataHoraRecebimentoFinal();
  				$reciboDTO->setNumIdProtocolo($procDTO->getIdProcedimento());
  				$arrRecibos = $reciboRN->listar( $reciboDTO );

  				if( $arrRecibos != null && count( $arrRecibos ) > 0){

  					$recibo = $arrRecibos[0];
  					$data = MdPetDataUtils::setFormat($recibo->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');

  					$linhaDeCima = '"Peticionamento Eletrônico"';
  					$linhaDeBaixo = '"Processo Novo: ' . $data . '"';
  					$arrParam[$procDTO->getIdProcedimento()] = array("<img src='modulos/peticionamento/imagens/peticionamento_processo_novo.png' onmouseout='return infraTooltipOcultar();' onmouseover='return infraTooltipMostrar(" . $linhaDeBaixo .  "," . $linhaDeCima .  ");' />");

  				}

  			}

  		}

  	}

  	return $arrParam;
  }

  public function montarMenuUsuarioExterno(){

  	$menuExternoRN = new MdPetMenuUsuarioExternoRN();
  	$menuExternoDTO = new MdPetMenuUsuarioExternoDTO();
  	$menuExternoDTO->retTodos();
  	$menuExternoDTO->setStrSinAtivo('S');

  	$menuExternoDTO->setOrd("Nome", InfraDTO::$TIPO_ORDENACAO_ASC);

  	$objLista = $menuExternoRN->listar( $menuExternoDTO );
  	$numRegistros = count($objLista);

  	//utilizado para ordenação
  	$urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI','URL');
  	$arrMenusNomes = array();

  	$arrMenusNomes["Peticionamento"] = $urlBase .'/controlador_externo.php?acao=md_pet_usu_ext_iniciar';

  	$arrMenusNomes["Recibos Eletrônicos de Protocolo"] = $urlBase .'/controlador_externo.php?acao=md_pet_usu_ext_recibo_listar';

  	if( is_array( $objLista ) && $numRegistros > 0 ){

  		for($i = 0;$i < $numRegistros; $i++){

  			$item = $objLista[$i];

  			if( $item->getStrTipo() == MdPetMenuUsuarioExternoRN::$TP_EXTERNO ) {
  				$link = "javascript:";
  				$link .= "var a = document.createElement('a'); ";
  				$link .= "a.href='" . $item->getStrUrl() ."'; ";
  				$link .= "a.target = '_blank'; ";
  				$link .= "document.body.appendChild(a); ";
  				$link .= "a.click(); ";
  				$arrMenusNomes[$item->getStrNome()] = $link;
  			}

  			else if( $item->getStrTipo() == MdPetMenuUsuarioExternoRN::$TP_CONTEUDO_HTML ) {

  				$idItem = $item->getNumIdMenuPeticionamentoUsuarioExterno();
  				$strLinkMontado = SessaoSEIExterna::getInstance()->assinarLink($urlBase . '/controlador_externo.php?acao=md_pet_pagina_conteudo_externo&id_md_pet_usu_externo_menu='. $idItem);
  				$arrMenusNomes[$item->getStrNome()] = $strLinkMontado;

  			}

  		}
  	}

  	$arrLink = array();
  	$numRegistrosMenu = count($arrMenusNomes);

  	$objMdPetCriterioRN  = new MdPetCriterioRN();
  	$objMdPetCriterioDTO = new MdPetCriterioDTO();
  	$objMdPetCriterioDTO->setStrSinCriterioPadrao('S');
  	$objMdPetCriterioDTO->retTodos();
  	$arrObjMdPetCriterioDTO = $objMdPetCriterioRN->listar($objMdPetCriterioDTO);
  	$objMdPetCriterioDTO = count($arrObjMdPetCriterioDTO) > 0 ? current($arrObjMdPetCriterioDTO) : null;

  	if( is_array( $arrMenusNomes ) && $numRegistrosMenu > 0 ){

  		foreach ( $arrMenusNomes as $key => $value) {

  			$urlLink = $arrMenusNomes[ $key ];
  			$nomeMenu = $key;

  			if($nomeMenu=='Peticionamento'){

  				$urlLinkIntercorrente = $urlBase .'/controlador_externo.php?acao=md_pet_intercorrente_usu_ext_cadastrar';

  				$arrLink[] = '-^#^^' . $nomeMenu .'^';
  				$arrLink[] = '--^' . $urlLink .'^^' . 'Processo Novo' .'^';
				if(!is_null($objMdPetCriterioDTO)){
  					$arrLink[] = '--^' . $urlLinkIntercorrente .'^^' . 'Intercorrente' .'^';
				}

  			}else{

  				$arrLink[] = '-^' . $urlLink .'^^' . $nomeMenu .'^';
  			}

  		}
  	}

  	return $arrLink;

  }

  public function montarBotaoAcessoExternoAutorizado(ProcedimentoAPI $objProcedimentoAPI){

      $strParam = 'acao=md_pet_intercorrente_usu_ext_cadastrar&id_orgao_acesso_externo=0';
      $hash = md5($strParam.'#'.SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno().'@'.SessaoSEIExterna::getInstance()->getAtributo('RAND_USUARIO_EXTERNO'));
      
      $urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI','URL');
      
      $link = $urlBase . '/controlador_externo.php?acao=md_pet_intercorrente_usu_ext_cadastrar&id_orgao_acesso_externo=0&infra_hash=' . $hash;
      $id_procedimento = $_GET['id_procedimento'];

    $array[] = "<script> function criarForm(){ 
            var f = document.createElement(\"form\");
        f.setAttribute('method',\"post\");
        f.setAttribute('action',\"$link\");
        
        var i = document.createElement(\"input\"); 
        i.setAttribute('type',\"hidden\");
        i.setAttribute('name',\"id_procedimento\");
        i.setAttribute('value',\"$id_procedimento\");
    
        f.appendChild(i);
        document.getElementsByTagName('body')[0].appendChild(f);
        f.submit();
    }</script>";
    $array[] = '<button type="button" accesskey="i" name="btnPetIntercorrente" value="Peticionamento Intercorrente" onclick="criarForm();" class="infraButton">Peticionamento <span class="infraTeclaAtalho">I</span>ntercorrente</button>';

    return $array;

    }

    public function montarTipoTarjaAssinaturaCustomizada()
    {
        $objArrTipoDTO = array();

        $objTipoDTO = new TipoDTO();
        $objTipoDTO->setStrStaTipo(MdPetAssinaturaRN::$TT_ASSINATURA_SENHA_PETICIONAMENTO);
        $objTipoDTO->setStrDescricao('Assinatura Eletrônica por Usuários Externos');
        $objArrTipoDTO[] = $objTipoDTO;

        return $objArrTipoDTO;
    }

	/**
	 * Valida se o Documento que está sendo cancelado foi peticionado
	 *
	 * @access public
	 * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
	 * @param  DocumentoAPI $objDocumentoAPI
	 * @return mixed
	 */
	public function cancelarDocumento(DocumentoAPI $objDocumentoAPI)
	{
		$numRecibo = '';
		$idDoc = $_GET['id_documento'];

		$objReciboDocAnexPetDTO = new MdPetRelReciboDocumentoAnexoDTO();
		$objReciboDocAnexPetDTO->setNumIdDocumento($idDoc);

		$objReciboDocAnexPetRN  = new MdPetRelReciboDocumentoAnexoRN();
		$cont = $objReciboDocAnexPetRN->contar($objReciboDocAnexPetDTO);

		if ($cont > 0) {
			$objReciboDocAnexPetDTO->retNumIdReciboPeticionamento();
			$objReciboDocAnexPetDTO = 	$objReciboDocAnexPetRN->consultar($objReciboDocAnexPetDTO);

			$objReciboPetDTO = new MdPetReciboDTO();
			$objReciboPetDTO->setNumIdReciboPeticionamento($objReciboDocAnexPetDTO->getNumIdReciboPeticionamento());
			$objReciboPetDTO->retStrNumeroProcessoFormatadoDoc();

			$objReciboPetRN  = new MdPetReciboRN();
			$objReciboPetDTO = $objReciboPetRN->consultar($objReciboPetDTO);

			if($objReciboPetDTO){
				$numRecibo = 	$objReciboPetDTO->getStrNumeroProcessoFormatadoDoc();
			}

			$msg = 'Não é permitido cancelar este documento, pois ele é oriundo de Peticionamento Eletrônico, conforme Recibo Eletrônico de Protocolo SEI nº '.$numRecibo.'.';
			$objInfraException = new InfraException();
			$objInfraException->adicionarValidacao($msg);
			$objInfraException->lancarValidacoes();
		}

		return parent::cancelarDocumento($objDocumentoAPI);
	}


}
?>