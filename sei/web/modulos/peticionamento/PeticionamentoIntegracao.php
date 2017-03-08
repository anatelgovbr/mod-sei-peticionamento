<?
/**
 * ANATEL
 *
 * 23/11/2016 - criado por marcelo.bezerra - CAST
 *
 */

require_once dirname(__FILE__).'/util/DataUtils.php';

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

      case 'gerir_extensoes_arquivo_peticionamento_cadastrar' :
				require_once dirname ( __FILE__ ) . '/gerir_extensoes_arquivo_peticionamento_cadastro.php';
				return true;

		   case 'gerir_tamanho_arquivo_peticionamento_cadastrar' :
                require_once dirname ( __FILE__ ) . '/gerir_tamanho_arquivo_peticionamento_cadastro.php';
                return true;

			case 'indisponibilidade_peticionamento_listar' :
			case 'indisponibilidade_peticionamento_desativar' :
			case 'indisponibilidade_peticionamento_reativar' :
			case 'indisponibilidade_peticionamento_excluir' :
                require_once dirname ( __FILE__ ) . '/indisponibilidade_peticionamento_lista.php';
                return true;

		    case 'indisponibilidade_peticionamento_cadastrar':
		    case 'indisponibilidade_peticionamento_consultar':
		    case 'indisponibilidade_peticionamento_alterar':
		    case 'indisponibilidade_peticionamento_upload_anexo':
		    case 'indisponibilidade_peticionamento_download':
                require_once dirname ( __FILE__ ) . '/indisponibilidade_peticionamento_cadastro.php';
                return true;

		    case 'tipo_processo_peticionamento_listar' :
		    case 'tipo_processo_peticionamento_desativar' :
		    case 'tipo_processo_peticionamento_reativar':
		    case 'tipo_processo_peticionamento_excluir':
		    	require_once dirname ( __FILE__ ) . '/tipo_processo_peticionamento_lista.php';
		    	return true;

		    case 'tipo_processo_peticionamento_cadastrar':
		    case 'tipo_processo_peticionamento_alterar':
		    case 'tipo_processo_peticionamento_consultar':
		    case 'tipo_processo_peticionamento_salvar':
		    	require_once dirname ( __FILE__ ) . '/tipo_processo_peticionamento_cadastro.php';
		    	return true;

		   case 'tipo_processo_peticionamento_cadastrar_orientacoes':
		   		require_once dirname ( __FILE__ ) . '/tipo_processo_peticionamento_cadastro_orientacoes.php';
		   		return true;

		   case 'tipo_procedimento_selecionar':
                require_once dirname ( __FILE__ ) . '/tipo_procedimento_lista.php';
                return true;

		   case 'serie_peticionamento_selecionar':
		    	require_once dirname ( __FILE__ ) . '/serie_peticionamento_lista.php';
		    	return true;

		   case 'menu_peticionamento_usuario_externo_listar' :
		   case 'menu_peticionamento_usuario_externo_desativar' :
		   case 'menu_peticionamento_usuario_externo_reativar':
		   case 'menu_peticionamento_usuario_externo_excluir':
	    		require_once dirname ( __FILE__ ) . '/menu_peticionamento_usuario_externo_lista.php';
	    		return true;

	    	case 'menu_peticionamento_usuario_externo_cadastrar':
	    	case 'menu_peticionamento_usuario_externo_alterar':
	    	case 'menu_peticionamento_usuario_externo_consultar':
	    		require_once dirname ( __FILE__ ) . '/menu_peticionamento_usuario_externo_cadastro.php';
	    		return true;

	    	case 'pagina_conteudo_externo_peticionamento':
	    		require_once dirname ( __FILE__ ) . '/pagina_conteudo_externo_peticionamento.php';
	    		return true;

	    	case 'gerir_tipo_contexto_peticionamento_cadastrar':
	    		require_once dirname ( __FILE__ ) . '/gerir_tipo_contexto_peticionamento_cadastro.php';
	    		return true;

	    	case 'hipotese_legal_nl_acesso_peticionamento_cadastrar':
	    		require_once dirname ( __FILE__ ).'/hipotese_legal_nl_acesso_peticionamento_cadastro.php';
	    		return true;

	       case 'hipotese_legal_peticionamento_selecionar':
                require_once dirname ( __FILE__ ).'/hipotese_legal_peticionamento_lista.php';
	    	    return true;

		   //ADMINISTRAÇÃO DE CRITÉRIOS PARA INTERCORRENTE
	       case 'criterio_intercorrente_peticionamento_listar' :
	       case 'criterio_intercorrente_peticionamento_desativar' :
	       case 'criterio_intercorrente_peticionamento_reativar':
	       case 'criterio_intercorrente_peticionamento_excluir':
	    	  	require_once dirname ( __FILE__ ) . '/criterio_intercorrente_peticionamento_lista.php';
	    	  	return true;

	       case 'criterio_intercorrente_peticionamento_cadastrar':
	       case 'criterio_intercorrente_peticionamento_alterar':
	       case 'criterio_intercorrente_peticionamento_consultar':
	         	require_once dirname ( __FILE__ ) . '/criterio_intercorrente_peticionamento_cadastro.php';
	    	  	return true;

	       case 'criterio_intercorrente_peticionamento_padrao':
	    	  	require_once dirname ( __FILE__ ) . '/criterio_intercorrente_peticionamento_padrao.php';
	    	  	return true;

	       case 'arquivo_extensao_peticionamento_selecionar':
				require_once dirname ( __FILE__ ).'/arquivo_extensao_peticionamento_lista.php';
                return true;

	       case 'md_pet_int_prazo_tacita_cadastrar':
	       case 'md_pet_int_prazo_tacita_alterar':
				require_once dirname ( __FILE__ ).'/md_pet_int_prazo_tacita_cadastro.php';
                return true;

			case 'md_pet_int_serie_cadastrar':
				require_once dirname ( __FILE__ ).'/md_pet_int_serie_cadastro.php';
                return true;
                
    }

    return false;
  }

  public function processarControladorAjax($strAcao){

    $xml = null;

		switch($_GET['acao_ajax']){

			case 'serie_peticionamento_auto_completar':
				$arrObjSerieDTO = SeriePeticionamentoINT::autoCompletarSeries( $_POST['palavras_pesquisa'] , $_POST['tipoDoc']);
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjSerieDTO,'IdSerie', 'Nome');
				break;

			case 'serie_auto_completar':
				$arrObjSerieDTO = SeriePeticionamentoINT::autoCompletarSeries( $_POST['palavras_pesquisa'] , false);
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjSerieDTO,'IdSerie', 'Nome');
				break;

			case 'tipo_processo_auto_completar':
				$arrObjTipoProcessoDTO = TipoProcedimentoINT::autoCompletarTipoProcedimento($_POST['palavras_pesquisa']);
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjTipoProcessoDTO,'IdTipoProcedimento', 'Nome');
				break;

            case 'tipo_processo_auto_completar_intercorretne':
                $arrObjTipoProcessoDTO = TipoProcessoPeticionamentoINT::autoCompletarTipoProcedimento($_POST['palavras_pesquisa'], $_POST['itens_selecionados'] );
                $xml = TipoProcessoPeticionamentoINT::gerarXMLItensArrInfraApi($arrObjTipoProcessoDTO,'IdTipoProcedimento', 'Nome');
                break;

            case 'tipo_processo_auto_completar_com_assunto':
				$arrObjTipoProcessoDTO = TipoProcedimentoINT::autoCompletarTipoProcessoComAssunto($_POST['palavras_pesquisa'] );
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjTipoProcessoDTO,'IdTipoProcedimento', 'Nome');
				break;

			case 'unidade_auto_completar':
				$arrObjUnidadeDTO = UnidadeINT::autoCompletarUnidades($_POST['palavras_pesquisa'], true, '');
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjUnidadeDTO,'IdUnidade', 'Sigla');
				break;

		   case 'nivel_acesso_auto_completar':
				$arrObjNivelAcessoDTO = TipoProcessoPeticionamentoINT::montarSelectNivelAcesso(null, null,  null, $_POST['idTipoProcesso']);
				$xml = InfraAjax::gerarXMLSelect($arrObjNivelAcessoDTO);
				break;

		   case 'nivel_acesso_validar':
				$xml = TipoProcessoPeticionamentoINT::validarNivelAcesso($_POST);
				break;

		   case 'tipo_peticionamento_assunto_validar':
				$xml = TipoProcessoPeticionamentoINT::validarTipoProcessoComAssunto($_POST);
				break;

			case 'tipo_contexto_contato_listar':
					$arrObjTipoContextoDTO = GerirTipoContextoPeticionamentoINT::montarSelectNome(null, null, $_POST['extensao']);
					$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjTipoContextoDTO, 'IdTipoContato', 'Nome');
					break;

			case 'hipotese_legal_rest_peticionamento_auto_completar':
				$arrObjHipoteseLegalDTO = HipoteseLegalPeticionamentoINT::autoCompletarHipoteseLegal($_POST['palavras_pesquisa'], ProtocoloRN::$NA_RESTRITO);
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjHipoteseLegalDTO, 'IdHipoteseLegal', 'Nome');
				break;

			case 'arquivo_extensao_peticionamento_listar_todos':
				$arrObjArquivoExtensaoPeticionamentoDTO = ArquivoExtensaoPeticionamentoINT::autoCompletarExtensao($_POST['extensao']);
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjArquivoExtensaoPeticionamentoDTO,'IdArquivoExtensao', 'Extensao');
				break;

            case 'buscar_unidade_id':
                $xml = TipoProcessoPeticionamentoINT::retornarUnidadeSelecionada($_POST['id_unidade']);
                break;

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

		    case 'pagina_conteudo_externo_peticionamento':
		  		require_once dirname ( __FILE__ ) . '/pagina_conteudo_externo_peticionamento.php';
		  		return true;

		  	case 'indisponibilidade_peticionamento_usuario_externo_listar' :
		  		require_once dirname ( __FILE__ ) . '/indisponibilidade_peticionamento_usuario_externo_lista.php';
		  		return true;

  		    case 'indisponibilidade_peticionamento_usuario_externo_consultar':
		    case 'indisponibilidade_peticionamento_usuario_externo_download':
		  		require_once dirname ( __FILE__ ) . '/indisponibilidade_peticionamento_usuario_externo_cadastro.php';
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
	  		case 'peticionamento_usuario_externo_iniciar':
	  			require_once dirname ( __FILE__ ) . '/peticionamento_usuario_externo_inicio.php';
	  			return true;

		  	case 'peticionamento_usuario_externo_cadastrar':
		  		require_once dirname ( __FILE__ ) . '/peticionamento_usuario_externo_cadastro.php';
		  		return true;

		  	case 'peticionamento_interessado_usuario_externo_cadastrar':
		  			require_once dirname ( __FILE__ ) . '/peticionamento_interessado_usuario_externo_cadastro.php';
		  			return true;

		  	case 'peticionamento_usuario_externo_concluir':
		  		require_once dirname ( __FILE__ ) . '/peticionamento_usuario_externo_concluir.php';
		  		return true;

		  	//peticionamento intercorrente - Janela de concluir peticionamento
		  	case 'md_pet_intercorrente_usu_ext_concluir':
		  		require_once dirname ( __FILE__ ) . '/md_pet_intercorrente_usu_ext_conclusao.php';
		  		return true;

		  	//consulta de recibo - 5153
		  	case 'recibo_peticionamento_usuario_externo_listar':
		  		require_once dirname ( __FILE__ ) . '/recibo_peticionamento_usuario_externo_lista.php';
		  		return true;

		  	case 'recibo_peticionamento_usuario_externo_consultar':
		  			require_once dirname ( __FILE__ ) . '/recibo_peticionamento_usuario_externo_consulta.php';
		  			return true;

			//Consulta de Recibo - EU7050
		    case 'recibo_pet_intercorrente_usuario_externo_consultar':
				   require_once dirname ( __FILE__ ) . '/recibo_peticionamento_intercorrente_usuario_externo_consulta.php';
				   return true;

		  	//peticionamento intercorrente - tela de detalhe de recibo de peticionamento intercorrente
		  	case 'md_pet_intercorrente_recibo_usu_ext_consultar':
		  			require_once dirname ( __FILE__ ) . '/md_pet_intercorrente_recibo_usu_ext_consulta.php';
		  			return true;

  			case 'peticionamento_interessado_cadastro':
  				    require_once dirname ( __FILE__ ) . '/peticionamento_interessado_cadastro.php';
  				    return true;

  		    case 'peticionamento_contato_selecionar':
  		    		require_once dirname ( __FILE__ ) . '/peticionamento_contato_selecionar.php';
  				    return true;

  			case 'peticionamento_usuario_externo_upload_anexo':
		  		if (isset($_FILES['fileArquivoEssencial'])){
		  			PaginaSEIExterna::getInstance()->processarUpload('fileArquivoEssencial', DIR_SEI_TEMP, true);
		  		}
		  		die;

		  	case 'peticionamento_usuario_externo_upload_doc_principal':

		  		if (isset($_FILES['fileArquivoPrincipal'])){

		  			PaginaSEIExterna::getInstance()->processarUpload('fileArquivoPrincipal', DIR_SEI_TEMP, true);
		  		}
		  		die;

		  	case 'peticionamento_usuario_externo_upload_doc_essencial':

		  		if (isset($_FILES['fileArquivoEssencial'])){

		  			PaginaSEIExterna::getInstance()->processarUpload('fileArquivoEssencial', DIR_SEI_TEMP, true);
		  		}
		  		die;

			case 'peticionamento_usuario_externo_upload_doc_complementar':

		  		if (isset($_FILES['fileArquivoComplementar'])){

		  			PaginaSEIExterna::getInstance()->processarUpload('fileArquivoComplementar', DIR_SEI_TEMP, true);
		  		}
		  		die;

  			case 'peticionamento_usuario_externo_upload_anexo':
  			case 'peticionamento_usuario_externo_download':
  					require_once dirname ( __FILE__ ) . '/peticionamento_usuario_externo_cadastro.php';
  					return true;

  			case 'editor_peticionamento_montar':
  			case 'editor_peticionamento_imagem_upload':
  			    	require_once dirname ( __FILE__ ) . '/editor_peticionamento_processar.php';
  			    	return true;

  			case 'validar_documento_principal':

  				  $conteudo = "";

  				  if( SessaoSEIExterna::getInstance()->isSetAtributo('docPrincipalConteudoHTML') ){
  				  	$conteudo = SessaoSEIExterna::getInstance()->getAtributo('docPrincipalConteudoHTML');
  				  }

  				  echo $conteudo;
  				  return true;

  			case 'contato_cpf_cnpj':

  				$cpfcnpj =  $_POST['cpfcnpj'];
  				$cpfcnpj = str_replace(".","", $cpfcnpj );
  				$cpfcnpj = str_replace("-","", $cpfcnpj );
  				$cpfcnpj = str_replace("/","", $cpfcnpj );

  				$total = ContatoPeticionamentoINT::getTotalContatoByCPFCNPJ( $cpfcnpj );
  				$json = null;

  				if( $total == 1 ) {

	  				$objContatoDTO = ContatoPeticionamentoINT::getContatoByCPFCNPJ( $cpfcnpj );

	  				if( $objContatoDTO != null){
	  				  $objContato = new stdClass();
                      $objContato->usuario = $objContatoDTO->getNumIdUsuarioCadastro();
	  				  $objContato->nome =  utf8_encode( $objContatoDTO->getStrNome() );
	  				  $objContato->id = utf8_encode( $objContatoDTO->getNumIdContato() );
					  $objContato->nomeTratado = PaginaSEI::tratarHTML($objContatoDTO->getStrNome());
	  			      $json = json_encode( $objContato , JSON_FORCE_OBJECT);
	  				}

  				}

  				echo $json;

  			    return true;

  			 //EU7050
			case 'validar_numero_processo_peticionamento':
				$xml = MdPetIntercorrenteINT::gerarXMLvalidacaoNumeroProcesso($_POST['txtNumeroProcesso']);
				echo $xml;

				return true;

        case 'peticionamento_usuario_externo_upload_arquivo':
            if (isset($_FILES['fileArquivo'])) {
                PaginaSEIExterna::getInstance()->processarUpload('fileArquivo', DIR_SEI_TEMP, false);
            }
            die;

        case 'montar_select_tipo_documento':
            $strOptions = MdPetIntercorrenteINT::montarSelectTipoDocumento('null', ' ', 'null');
            $xml        = InfraAjax::gerarXMLSelect($strOptions);
            InfraAjax::enviarXML($xml);

            return true;

        case 'montar_select_nivel_acesso':
            $strOptions = TipoProcessoPeticionamentoINT::montarSelectNivelAcesso('null', '', 'null', $_POST['id_tipo_procedimento']);
            $xml        = InfraAjax::gerarXMLSelect($strOptions);
            InfraAjax::enviarXML($xml);

            return true;

        case 'verificar_criterio_intercorrente':
            $arrNivelAcessoHipoteseLegal = MdPetIntercorrenteINT::verificarCriterioIntercorrente($_POST['idTipoProcedimento']);
            echo json_encode($arrNivelAcessoHipoteseLegal);
            return true;
    }

         return false;
  }

  public function montarIconeProcesso(ProcedimentoAPI $objProcedimentoAPI){

  	$arrObjArvoreAcaoItemAPI = array();
  	$dblIdProcedimento = $objProcedimentoAPI->getIdProcedimento();
  	$reciboRN = new ReciboPeticionamentoRN();

  	//verificar se este processo é de peticionamento intercorrente
  	$reciboIntercorrenteDTO = new ReciboPeticionamentoDTO();
  	$reciboIntercorrenteDTO->retNumIdProtocolo();
  	$reciboIntercorrenteDTO->retStrStaTipoPeticionamento();
  	$reciboIntercorrenteDTO->retDthDataHoraRecebimentoFinal();
  	$reciboIntercorrenteDTO->setNumIdProtocolo( $dblIdProcedimento );
  	$reciboIntercorrenteDTO->setStrStaTipoPeticionamento( ReciboPeticionamentoRN::$TP_RECIBO_INTERCORRENTE );
  	$arrRecibosIntercorrentes = $reciboRN->listar( $reciboIntercorrenteDTO );

  	if( $arrRecibosIntercorrentes != null && count( $arrRecibosIntercorrentes ) > 0){

  		$recibo = $arrRecibosIntercorrentes[0];
  		$data = DataUtils::setFormat($recibo->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');
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
  		$reciboDTO = new ReciboPeticionamentoDTO();
  		$reciboDTO->retNumIdProtocolo();
  		$reciboDTO->retDthDataHoraRecebimentoFinal();
  		$reciboDTO->setNumIdProtocolo( $dblIdProcedimento );
  		$arrRecibos = $reciboRN->listar( $reciboDTO );

  		if( $arrRecibos != null && count( $arrRecibos ) > 0){

  			$recibo = $arrRecibos[0];
  			$data = DataUtils::setFormat($recibo->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');
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

  	$reciboRN = new ReciboPeticionamentoRN();
  	$arrParam = array();

  	if( $arrObjProcedimentoDTO != null && count( $arrObjProcedimentoDTO ) > 0 ){

  		foreach( $arrObjProcedimentoDTO as $objProcedimentoAPI  ){

  			//verificando se há algum recibo intercorrente para esse processo
  			$reciboIntercorrenteDTO = new ReciboPeticionamentoDTO();
  			$reciboIntercorrenteDTO->retNumIdProtocolo();
  			$reciboIntercorrenteDTO->retStrStaTipoPeticionamento();
  			$reciboIntercorrenteDTO->retDthDataHoraRecebimentoFinal();
  			$reciboIntercorrenteDTO->setNumIdProtocolo($objProcedimentoAPI->getIdProcedimento());
  			$reciboIntercorrenteDTO->setStrStaTipoPeticionamento( ReciboPeticionamentoRN::$TP_RECIBO_INTERCORRENTE );
  			$arrRecibosIntercorrentes = $reciboRN->listar( $reciboIntercorrenteDTO );

  			if( $arrRecibosIntercorrentes != null && count( $arrRecibosIntercorrentes ) > 0){

  				$recibo = $arrRecibosIntercorrentes[0];
  				$data = DataUtils::setFormat($recibo->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');
  				$linhaDeCima = '"Peticionamento Eletrônico"';
  				$linhaDeBaixo = '"Intercorrente: ' . $data . '"';
  				$arrParam[$objProcedimentoAPI->getIdProcedimento()] = array("<img src='modulos/peticionamento/imagens/peticionamento_intercorrente.png' onmouseout='return infraTooltipOcultar();' onmouseover='return infraTooltipMostrar(" . $linhaDeBaixo .  "," . $linhaDeCima .  ");' />");

  			} else {

  				//verificando se há algum recibo de processo novo para esse processo
  				$reciboDTO = new ReciboPeticionamentoDTO();
  				$reciboDTO->retNumIdProtocolo();
  				$reciboDTO->retDthDataHoraRecebimentoFinal();
  				$reciboDTO->retStrStaTipoPeticionamento();
  				$reciboDTO->setNumIdProtocolo($objProcedimentoAPI->getIdProcedimento());
  				$reciboDTO->setStrStaTipoPeticionamento( ReciboPeticionamentoRN::$TP_RECIBO_NOVO );
  				$arrRecibos = $reciboRN->listar( $reciboDTO );

  				if( $arrRecibos != null && count( $arrRecibos ) > 0){

  					$recibo = $arrRecibos[0];
  					$data = DataUtils::setFormat($recibo->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');
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

  	$reciboRN = new ReciboPeticionamentoRN();
  	$arrParam = array();

  	if( $arrObjProcedimentoDTO != null && count( $arrObjProcedimentoDTO ) > 0 ){

  		foreach( $arrObjProcedimentoDTO as $procDTO ){

  			//verificar se este processo é de peticionamento intercorrente
  			$reciboIntercorrenteDTO = new ReciboPeticionamentoDTO();
  			$reciboIntercorrenteDTO->retNumIdProtocolo();
  			$reciboIntercorrenteDTO->retStrStaTipoPeticionamento();
  			$reciboIntercorrenteDTO->retDthDataHoraRecebimentoFinal();
  			$reciboIntercorrenteDTO->setNumIdProtocolo($procDTO->getIdProcedimento());
  			$reciboIntercorrenteDTO->setStrStaTipoPeticionamento( ReciboPeticionamentoRN::$TP_RECIBO_INTERCORRENTE );
  			$arrRecibosIntercorrentes = $reciboRN->listar( $reciboIntercorrenteDTO );

  			if( $arrRecibosIntercorrentes != null && count( $arrRecibosIntercorrentes ) > 0){

  				$recibo = $arrRecibosIntercorrentes[0];
  				$data = DataUtils::setFormat($recibo->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');

  				$linhaDeCima = '"Peticionamento Eletrônico"';
  				$linhaDeBaixo = '"Intercorrente: ' . $data . '"';
  				$arrParam[$procDTO->getIdProcedimento()] = array("<img src='modulos/peticionamento/imagens/peticionamento_intercorrente.png' onmouseout='return infraTooltipOcultar();' onmouseover='return infraTooltipMostrar(" . $linhaDeBaixo .  "," . $linhaDeCima .  ");' />");

  			} else {

  				//verificar se este processo é de peticionamento de processo novo
  				$reciboDTO = new ReciboPeticionamentoDTO();
  				$reciboDTO->retNumIdProtocolo();
  				$reciboDTO->retDthDataHoraRecebimentoFinal();
  				$reciboDTO->setNumIdProtocolo($procDTO->getIdProcedimento());
  				$arrRecibos = $reciboRN->listar( $reciboDTO );

  				if( $arrRecibos != null && count( $arrRecibos ) > 0){

  					$recibo = $arrRecibos[0];
  					$data = DataUtils::setFormat($recibo->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');

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

  	$menuExternoRN = new MenuPeticionamentoUsuarioExternoRN();
  	$menuExternoDTO = new MenuPeticionamentoUsuarioExternoDTO();
  	$menuExternoDTO->retTodos();
  	$menuExternoDTO->setStrSinAtivo('S');

  	$menuExternoDTO->setOrd("Nome", InfraDTO::$TIPO_ORDENACAO_ASC);

  	$objLista = $menuExternoRN->listar( $menuExternoDTO );
  	$numRegistros = count($objLista);

  	//utilizado para ordenação
  	$urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI','URL');
  	$arrMenusNomes = array();

  	$arrMenusNomes["Peticionamento"] = $urlBase .'/controlador_externo.php?acao=peticionamento_usuario_externo_iniciar';

  	$arrMenusNomes["Recibos Eletrônicos de Protocolo"] = $urlBase .'/controlador_externo.php?acao=recibo_peticionamento_usuario_externo_listar';

  	if( is_array( $objLista ) && $numRegistros > 0 ){

  		for($i = 0;$i < $numRegistros; $i++){

  			$item = $objLista[$i];

  			if( $item->getStrTipo() == MenuPeticionamentoUsuarioExternoRN::$TP_EXTERNO ) {
  				$link = "javascript:";
  				$link .= "var a = document.createElement('a'); ";
  				$link .= "a.href='" . $item->getStrUrl() ."'; ";
  				$link .= "a.target = '_blank'; ";
  				$link .= "document.body.appendChild(a); ";
  				$link .= "a.click(); ";
  				$arrMenusNomes[$item->getStrNome()] = $link;
  			}

  			else if( $item->getStrTipo() == MenuPeticionamentoUsuarioExternoRN::$TP_CONTEUDO_HTML ) {

  				$idItem = $item->getNumIdMenuPeticionamentoUsuarioExterno();
  				$strLinkMontado = SessaoSEIExterna::getInstance()->assinarLink($urlBase . '/controlador_externo.php?acao=pagina_conteudo_externo_peticionamento&id_md_pet_usu_externo_menu='. $idItem);
  				$arrMenusNomes[$item->getStrNome()] = $strLinkMontado;

  			}

  		}
  	}

  	$arrLink = array();
  	$numRegistrosMenu = count($arrMenusNomes);

	  $objCriterioIntercorrenteRN  = new CriterioIntercorrentePeticionamentoRN();
	  $objCriterioIntercorrenteDTO = new CriterioIntercorrentePeticionamentoDTO();
	  $objCriterioIntercorrenteDTO->setStrSinCriterioPadrao('S');
	  $objCriterioIntercorrenteDTO->retTodos();
	  $arrObjCriterioIntercorrenteDTO = $objCriterioIntercorrenteRN->listar($objCriterioIntercorrenteDTO);
	  $objCriterioIntercorrenteDTO = count($arrObjCriterioIntercorrenteDTO) > 0 ? current($arrObjCriterioIntercorrenteDTO) : null;


  	if( is_array( $arrMenusNomes ) && $numRegistrosMenu > 0 ){

  		foreach ( $arrMenusNomes as $key => $value) {

  			$urlLink = $arrMenusNomes[ $key ];
  			$nomeMenu = $key;

  			if($nomeMenu=='Peticionamento'){

  				$urlLinkIntercorrente = $urlBase .'/controlador_externo.php?acao=md_pet_intercorrente_usu_ext_cadastrar';

  				$arrLink[] = '-^#^^' . $nomeMenu .'^';
  				$arrLink[] = '--^' . $urlLink .'^^' . 'Processo Novo' .'^';
				if(!is_null($objCriterioIntercorrenteDTO)){
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
        $objTipoDTO->setStrStaTipo(AssinaturaPeticionamentoRN::$TT_ASSINATURA_SENHA_PETICIONAMENTO);
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

		$objReciboDocAnexPetDTO = new ReciboDocumentoAnexoPeticionamentoDTO();
		$objReciboDocAnexPetDTO->setNumIdDocumento($idDoc);

		$objReciboDocAnexPetRN  = new ReciboDocumentoAnexoPeticionamentoRN();
		$cont = $objReciboDocAnexPetRN->contar($objReciboDocAnexPetDTO);

		if ($cont > 0) {
			$objReciboDocAnexPetDTO->retNumIdReciboPeticionamento();
			$objReciboDocAnexPetDTO = 	$objReciboDocAnexPetRN->consultar($objReciboDocAnexPetDTO);

			$objReciboPetDTO = new ReciboPeticionamentoDTO();
			$objReciboPetDTO->setNumIdReciboPeticionamento($objReciboDocAnexPetDTO->getNumIdReciboPeticionamento());
			$objReciboPetDTO->retStrNumeroProcessoFormatadoDoc();

			$objReciboPetRN  = new ReciboPeticionamentoRN();
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