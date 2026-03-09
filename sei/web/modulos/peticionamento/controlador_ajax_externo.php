<?
/**
 * ANATEL
 *
 * 
 * 22/07/2016 - criado por marcelo.bezerra@cast.com.br - CAST
 * Arquivo para realizar controle requisição ajax de usuario externo no modulo peticionamento.
 */

function mdPetNegarAcessoAjaxExterno($strMensagem = null){
    throw new InfraException($strMensagem ?: 'Acesso negado para a ação solicitada.');
}

function mdPetRecuperarTiposContextoPermitidos(){
    $objMdPetRelTpCtxContatoDTO = new MdPetRelTpCtxContatoDTO();
    $objMdPetRelTpCtxContatoDTO->retNumIdTipoContextoContato();
    $arrObjMdPetRelTpCtxContatoDTO = (new MdPetTpCtxContatoRN())->listar($objMdPetRelTpCtxContatoDTO);

    $arrIdTipoContextoPermitido = array();

    if (!empty($arrObjMdPetRelTpCtxContatoDTO)) {
        foreach ($arrObjMdPetRelTpCtxContatoDTO as $objMdPetRelTpCtxContatoDTO) {
            $arrIdTipoContextoPermitido[] = $objMdPetRelTpCtxContatoDTO->getNumIdTipoContextoContato();
        }
    }

    return $arrIdTipoContextoPermitido;
}

function mdPetGetParametrosInfraestruturaPermitidos() {
    return array(
        'acao_ajax_externo',        // Roteamento deste controlador
        'id_orgao_acesso_externo',  // Controle de órgão na sessão externa (md_pet_interessado_cadastro_js.php:2)
        'id_acesso_externo',        // Identificador de acesso externo assinado
        'acao_origem',              // Retorno de fluxo (md_pet_intimacao_usu_ext_lista.php:24)
        'acao',                     // Ação genérica do SEI (algumas rotas usam)
        'id_contato',               // Contexto de cadastro (quando presente em GET legado)
		'infra_hash',				// Hash do link
    );
}

// Adicionar função de validação específica:
function mdPetValidarDataAttributes($dataAttributes) {
    $camposPermitidos = array(
        'idmdpetdest', 'docprinc', 'doctipo', 'idsituacao', 
        'descricao', 'idacext', 'tpprocesso', 'idprocesso', 
        'idintimacao', 'idusuarioexterno'
	);
    
    $camposExtras = array_diff(array_keys($dataAttributes), $camposPermitidos);
    if (!empty($camposExtras)) {
        mdPetNegarAcessoAjaxExterno('Campos dataAttributes não permitidos: ' . implode(', ', $camposExtras));
    }
    
    // Validações de tipo específicas
    if (!empty($dataAttributes['idprocesso']) && !is_numeric($dataAttributes['idprocesso'])) {
        mdPetNegarAcessoAjaxExterno('idprocesso deve ser numérico.');
    }
    // ... outras validações
}

try{

    require_once dirname(__FILE__).'/../../SEI.php';
    session_start();

	InfraDebug::getInstance()->setBolLigado(true);
	InfraDebug::getInstance()->setBolDebugInfra(false);
	InfraDebug::getInstance()->setBolEcho(false);
	InfraDebug::getInstance()->limpar();
    
    InfraAjax::decodificarPost();

	$acaoAjaxExterno = isset($_GET['acao_ajax_externo']) ? $_GET['acao_ajax_externo'] : null;

	// ============================================================
    // VALIDAÇÃO DE PARÂMETROS GET - HARDENING FLEXÍVEL
    // ============================================================
    
    $arrParametrosInfraPermitidos = mdPetGetParametrosInfraestruturaPermitidos();

	// Verifica se há parâmetros GET fora da lista de infraestrutura permitida
    $arrGetKeys = array_keys($_GET);
    $arrGetNaoPermitidos = array_diff($arrGetKeys, $arrParametrosInfraPermitidos);
    
    if (!empty($arrGetNaoPermitidos)) {
        // Log para auditoria (opcional, mas recomendado)

		InfraDebug::getInstance()->gravar(sprintf(
            '[HARDENING] Parâmetros GET não permitidos detectados: %s | Ação: %s | IP: %s',
            implode(', ', $arrGetNaoPermitidos),
            $acaoAjaxExterno,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ));

		LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);
        
        throw new InfraException(
            "Parâmetros GET não autorizados: '" . implode("', '", $arrGetNaoPermitidos) . "'. " .
            "Acesso negado."
        );
    }

    function mdPetValidarContratoAjax($acao, $contratos){
        if (!isset($contratos[$acao])) {
            mdPetNegarAcessoAjaxExterno("Acao '{$acao}' nao reconhecida pelo controlador AJAX do Peticionamento.");
        }

        $contrato = $contratos[$acao];
        
        // Validação de parâmetros GET conforme contrato
        $extrasGet = array_diff(array_keys($_GET), $contrato['get']);

        if (!empty($extrasGet)) {
            mdPetNegarAcessoAjaxExterno('Parametro(s) GET fora do contrato: '.implode(', ', $extrasGet).'.');
        }

        // Validação de parâmetros POST
		$postPermitido = array_merge($contrato['post_required'], $contrato['post_optional']);
        $extrasPost = array_diff(array_keys($_POST), $postPermitido);
        if (!empty($extrasPost)) {
            mdPetNegarAcessoAjaxExterno('Parametro(s) POST fora do contrato: '.implode(', ', $extrasPost).'.');
        }

        foreach ($contrato['post_required'] as $chaveObrigatoria) {
            if (!array_key_exists($chaveObrigatoria, $_POST)) {
                mdPetNegarAcessoAjaxExterno("Parametro POST obrigatorio nao informado: {$chaveObrigatoria}.");
            }
        }
    }

    // Ações protegidas neste controlador.
    $arrAcoesProtegidas = array(
        'md_pet_contato_pj_vinculada',
        'md_pet_contato_auto_completar_contexto_pesquisa',
        'md_pet_cargo_montar_select_genero',
        'md_pet_cargo_dados',
        'get_acoes_intimacao_lista'
    );

	// Exceções públicas: no momento não há ações públicas neste endpoint.
    $arrAcoesPublicas = array();

	if (!in_array($acaoAjaxExterno, $arrAcoesProtegidas, true) && !in_array($acaoAjaxExterno, $arrAcoesPublicas, true)) {
        mdPetNegarAcessoAjaxExterno("Ação '".$acaoAjaxExterno."' não reconhecida pelo controlador AJAX externo.");
    }

	// Validação de sessão externa para ações protegidas
    $objSessaoExterna = SessaoSEIExterna::getInstance();
    if (!in_array($acaoAjaxExterno, $arrAcoesPublicas, true)) {
        $objSessaoExterna->validarSessao();
    }

	// Contratos de POST por ação (GET já foi validado acima de forma genérica)
    $contratosAcoes = array(
        'md_pet_contato_pj_vinculada' => array(
			'get' => array('acao_ajax_externo', 'acao_origem', 'id_orgao_acesso_externo', 'infra_hash'),
            'post_required' => array('id_tipo_contexto_contato', 'palavras_pesquisa'), 
            'post_optional' => array()
        ),
        'md_pet_contato_auto_completar_contexto_pesquisa' => array(
			'get' => array('acao_ajax_externo', 'acao_origem', 'id_orgao_acesso_externo', 'infra_hash'),
            'post_required' => array('extensao'), 
            'post_optional' => array()
        ),
        'md_pet_cargo_montar_select_genero' => array(
			'get' => array('acao_ajax_externo', 'acao_origem', 'id_orgao_acesso_externo', 'infra_hash'),
            'post_required' => array('primeiroItemValor', 'primeiroItemDescricao', 'valorItemSelecionado', 'staGenero'), 
            'post_optional' => array()
        ),
        'md_pet_cargo_dados' => array(
			'get' => array('acao_ajax_externo', 'acao_origem', 'id_orgao_acesso_externo', 'infra_hash'),
            'post_required' => array('id_cargo'), 
            'post_optional' => array()
        ),
        'get_acoes_intimacao_lista' => array(
			'get' => array('acao_ajax_externo', 'acao_origem', 'id_orgao_acesso_externo', 'infra_hash'),
            'post_required' => array('dataAttributes'), 
            'post_optional' => array()
        )
    );

	mdPetValidarContratoAjax($acaoAjaxExterno, $contratosAcoes);

	switch($acaoAjaxExterno){

		case 'md_pet_contato_pj_vinculada':

			// Buscando primeira unidade para simular login para conseguir fazer a "pesquisarRN0471"
			$seiRN = new SeiRN();
			$objEntradaConsultarDocumentoAPI = new EntradaListarUnidadesAPI();
			$objSaidaConsultarDocumentoAPI = $seiRN->listarUnidades($objEntradaConsultarDocumentoAPI);

			if (empty($objSaidaConsultarDocumentoAPI)) {
				throw new InfraException('Nenhuma unidade disponível para simulação de login.');
			}

			SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(), $objSaidaConsultarDocumentoAPI[0]->getIdUnidade());

			$arrIdTipoContextoPermitido = mdPetRecuperarTiposContextoPermitidos();
			if (empty($arrIdTipoContextoPermitido) || !in_array((int)$_POST['id_tipo_contexto_contato'], array_map('intval', $arrIdTipoContextoPermitido), true)) {
				mdPetNegarAcessoAjaxExterno('Usuário externo não autorizado para o tipo de contexto informado.');
			}

			$objContatoRN = new ContatoRN();
			$objContextoContatoDTO = new ContatoDTO();

			$objContextoContatoDTO->retNumIdContato();
			$objContextoContatoDTO->retStrNome();

			// Trazer todos que sejam empresas (CNPJ diferente de null), estejam ativos,
			// e atenda ao filtro por nome e tipo de contexto informado na tela

			$palavrasPesquisaRaw = filter_input(INPUT_POST, 'palavras_pesquisa', FILTER_UNSAFE_RAW);
			if ($palavrasPesquisaRaw === null || $palavrasPesquisaRaw === false) {
				mdPetNegarAcessoAjaxExterno('Parâmetro palavras_pesquisa inválido.');
			}
			$palavrasPesquisa = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $palavrasPesquisaRaw);

			$idTipoContexto = filter_input(INPUT_POST, 'id_tipo_contexto_contato', FILTER_VALIDATE_INT);
			if ($idTipoContexto === false || $idTipoContexto === null) {
				mdPetNegarAcessoAjaxExterno('Tipo de contexto inválido.');
			}

			$objContextoContatoDTO->adicionarCriterio(
					//alteracoes seiv3
					array('Cnpj','Nome', 'SinAtivo', 'IdTipoContato'),
					array(InfraDTO::$OPER_DIFERENTE,InfraDTO::$OPER_LIKE, InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL ),
					array(null, "%".$palavrasPesquisa."%", 'S', $idTipoContexto ),
					array( InfraDTO::$OPER_LOGICO_AND , InfraDTO::$OPER_LOGICO_AND , InfraDTO::$OPER_LOGICO_AND )
			);

			$objContextoContatoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

			$arrObjContatoDTO = $objContatoRN->pesquisarRN0471( $objContextoContatoDTO );
			$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjContatoDTO,'IdContato', 'Nome');
			InfraAjax::enviarXML($xml);

			break;

		case 'md_pet_contato_auto_completar_contexto_pesquisa':

			$arrIdTipoContextoPermitido = mdPetRecuperarTiposContextoPermitidos();
            if (empty($arrIdTipoContextoPermitido)) {
                mdPetNegarAcessoAjaxExterno('Usuário externo sem autorização de contexto para consulta de contatos.');
            }

			// alterado para atender anatel exibir apenas nome contato
			$objContatoDTO = new ContatoDTO();
			$objContatoDTO->retNumIdContato();
			$objContatoDTO->retStrSigla();
			$objContatoDTO->retStrNome();  		
			$objContatoDTO->setStrPalavrasPesquisa($_POST['extensao']);
			
			$objContatoDTO->adicionarCriterio(
					array('SinAtivo','Nome'),
					array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_LIKE ),
					array('S', '%'.$_POST['extensao']. '%' ),
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

				// alteracoes seiv3
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

			$strOptions = MdPetTpCtxContatoINT::montarSelectGeneroComTratamentoEVocativo($_POST['primeiroItemValor'],$_POST['primeiroItemDescricao'],$_POST['valorItemSelecionado'],$_POST['staGenero']);

			$xml = InfraAjax::gerarXMLSelect($strOptions);

			InfraAjax::enviarXML($xml);
			break;

		case 'md_pet_cargo_dados':
			// para uso com usuário externo - clone de controlador.ajax->cargo_dados 

			$objCargoDTO = new CargoDTO();
			$objCargoDTO->setBolExclusaoLogica(false);
			$objCargoDTO->retStrExpressaoTratamento();
			$objCargoDTO->retStrExpressaoVocativo();
			if (!ctype_digit((string) $_POST['id_cargo']) || (int) $_POST['id_cargo'] <= 0) {
				throw new InfraException('Selecione um cargo.');
			}
			$objCargoDTO->setNumIdCargo($_POST['id_cargo']);

			$objCargoRN = new CargoRN();
			$objCargoDTO = $objCargoRN->consultarRN0301($objCargoDTO);

			if ($objCargoDTO!=null){
				$xml = InfraAjax::gerarXMLComplementosArrInfraDTO($objCargoDTO,array('ExpressaoTratamento','ExpressaoVocativo'));
				InfraAjax::enviarXML($xml);
			} else {
				throw new InfraException('Cargo não encontrado.');
			}

			break;


		case 'get_acoes_intimacao_lista':

			$dataAttributes = $_POST['dataAttributes'];

			if (!is_array($dataAttributes)) {
				throw new InfraException('Payload invalido para dataAttributes.');
			}

			// Valida os indices dentro de $dataAttributes:
			mdPetValidarDataAttributes($dataAttributes);

			$idMdPetDest		= $_POST['dataAttributes']['idmdpetdest'];
			$docPrinc 			= $_POST['dataAttributes']['docprinc'];
			$docTipo			= $_POST['dataAttributes']['doctipo'];
			$idSituacao 		= $_POST['dataAttributes']['idsituacao'];
			$descricao 			= $_POST['dataAttributes']['descricao'];
			$idAcExt 			= $_POST['dataAttributes']['idacext'];
			$tpProcesso 		= $_POST['dataAttributes']['tpprocesso'];
			$idProcesso 		= $_POST['dataAttributes']['idprocesso'];
			$idIntimacao 		= $_POST['dataAttributes']['idintimacao'];
			$idUsuarioExterno 	= $_POST['dataAttributes']['idusuariouxterno'];
			$idDocCert 			= (new MdPetIntAceiteRN())->getIdCertidaoPorIntimacao(array($idIntimacao, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));

			$strResultado 		= '';

			if (!empty($idUsuarioExterno) && (int)$idUsuarioExterno !== (int)$objSessaoExterna->getNumIdUsuarioExterno()) {
                mdPetNegarAcessoAjaxExterno('Usuário externo não autorizado para consultar esta intimação.');
            }

			// Ação Consulta
			if (!is_null($idProcesso)) {

				$strResultado .= (new MdPetIntRelDestinatarioRN())->addConsultarProcesso($idProcesso, $tpProcesso, $idAcExt, $descricao);

				if (!is_null($idSituacao) && $idSituacao != MdPetIntimacaoRN::$INTIMACAO_PENDENTE) {

					$docNum = '';
					$idDocCert = (new MdPetIntAceiteRN())->getIdCertidaoPorIntimacao(array($idIntimacao, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));

					$strResultado .= (new MdPetIntCertidaoRN())->addIconeAcessoCertidao(array($docPrinc, $idIntimacao, $idAcExt, $idDocCert));

					// RECIBO
					// Próprio Processo
					$objMdPetReciboDTO = new MdPetReciboDTO();
					$objMdPetReciboDTO->retTodos();
					$objMdPetReciboDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO);
					$objMdPetReciboDTO->setNumIdProtocolo($idProcesso);
					$objMdPetReciboDTO->unSetDblIdProtocoloRelacionado();
					$arrObjMdPetReciboDTO = (new MdPetReciboRN())->listar($objMdPetReciboDTO);

					if (empty($arrObjMdPetReciboDTO)) {
						// Relacionado
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

							// Verificar se traz somente o do acesso atual ou do relacionado desta intimação (linha 1215)
							// @todo adicionar verificaçao de data de validade do acesso externo

							$arrAcessosExternos = (new AcessoExternoRN())->listar($acessoExtDTO);

							if (is_array($arrAcessosExternos) && count($arrAcessosExternos) > 0) {

								$id_acesso_ext_link = $arrAcessosExternos[0]->getNumIdAcessoExterno();

								$docLink = "documento_consulta_externa.php?id_acesso_externo=" . $id_acesso_ext_link;
								$docLink .= "&id_documento=" . $objMdPetReciboDTO->getDblIdDocumento();
								$docLink .= "&id_orgao_acesso_externo=0";

								// se nao configurar acesso externo ANTES, a assinatura do link falha:
								SessaoSEIExterna::getInstance()->configurarAcessoExterno($id_acesso_ext_link);
								$linkAssinado = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink($docLink));

								$strResultado .= (new MdPetIntReciboRN())->addIconeRecibo(array($objMdPetReciboDTO->getDthDataHoraRecebimentoFinal(), $docPrinc, $docTipo, $docNum, $linkAssinado, $objMdPetReciboDTO->getDblIdDocumento(), $idMdPetDest));

							}

						}

					}

				}

			}

			if (!is_numeric($strResultado)) {

				$getChars 	= ['<','>','"',"'",'&'];
				$replaceBy 	= ['&lt;','&gt;','&quot;','&apos;','&amp;'];

				$strResultado = str_replace($getChars, $replaceBy, $strResultado);

			}

			if(!empty($strResultado)){
				InfraAjax::enviarXML('<actions>'.$strResultado.'</actions>');
			}

			break;

		default:
		throw new InfraException("Ação '".$acaoAjaxExterno."' não reconhecida pelo controlador AJAX externo.");
	}
  
}catch(Exception $e){
  InfraAjax::processarExcecao($e);
}
