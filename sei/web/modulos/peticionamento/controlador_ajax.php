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

function mdPetGetParametrosInfraestruturaPermitidos() {
    return array(
        'acao_ajax',
        'infra_sistema',
        'infra_unidade_atual',
        'acao_origem',
        'acao',
        'infra_hash',
		'id_documento'
    );
}

try{

    require_once dirname(__FILE__).'/../../SEI.php';

    session_start();
    
    InfraAjax::decodificarPost();

	$acaoAjax = isset($_GET['acao_ajax']) ? $_GET['acao_ajax'] : null;

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

	function mdPetErroJson($mensagem){
        echo json_encode(array('sucesso' => false, 'mensagem' => $mensagem), JSON_UNESCAPED_UNICODE);
    }

    function mdPetValidarContratoAjax($acao, $contratos){
        if (!isset($contratos[$acao])) {
            throw new InfraException("Acao '{$acao}' nao reconhecida pelo controlador AJAX do Peticionamento.");
        }

        $contrato = $contratos[$acao];
        $extrasGet = array_diff(array_keys($_GET), $contrato['get']);
        if (!empty($extrasGet)) {
            throw new InfraException('Parametro(s) GET fora do contrato: '.implode(', ', $extrasGet).'.');
        }

		$postPermitido = array_merge($contrato['post_required'], $contrato['post_optional']);
        $extrasPost = array_diff(array_keys($_POST), $postPermitido);
        if (!empty($extrasPost)) {
            throw new InfraException('Parametro(s) POST fora do contrato: '.implode(', ', $extrasPost).'.');
        }

        foreach ($contrato['post_required'] as $chaveObrigatoria) {
            if (!array_key_exists($chaveObrigatoria, $_POST)) {
                throw new InfraException("Parametro POST obrigatorio nao informado: {$chaveObrigatoria}.");
            }
        }
    }

	$acaoAjax = isset($acaoAjax) ? $acaoAjax : null;

	$post_params_relatorio_grafico = array(
		'hdnInfraTipoPagina',
		'txtProtocoloPesquisa',
		'txtTpIntimacao',
		'hdnTpIntimacao',
		'hdnIdTpIntimacao',
		'txtUnidade',
		'hdnUnidade',
		'hdnIdUnidade',
		'txtDataInicio',
		'txtDataFim',
		'selTipoDest',
		'txtDestinatarioPF',
		'hdnDestinatario',
		'hdnIdDestinatario',
		'selSituacao',
		'hdnIdsSituacao',
		'hdnTipoGrafico1',
		'hdnTipoGrafico2',
		'hdnTipoGrafico3',
		'hdnTipoGrafico4',
		'hdnTipoGrafico5',
		'hdnIsGrafico',
		'hdnExcel',
		'hdnIdSitTodas',
		'selInfraPaginacaoSuperior',
		'hdnInfraNroItens',
		'hdnInfraItemId',
		'hdnInfraItens',
		'hdnInfraItensHash',
		'hdnInfraItensSelecionados',
		'hdnInfraSelecoes',
		'hdnInfraCampoOrd',
		'hdnInfraTipoOrd',
		'selInfraPaginacaoInferior',
		'hdnInfraPaginaAtual',
		'hdnInfraHashCriterios',
		'selGrafico',
		'ocultarTiposIntVazios',
		'hdnIsPesquisa',
		'hdnAcaoOrigem',
		'tipoGrafico',
		'idTipoIntimacao',
	);
	
    $contratosAcoes = array(
        'md_pet_int_relatorio_grafico' => array(
			'get' => array('acao_ajax', 'infra_sistema', 'infra_unidade_atual', 'infra_hash'), 
			'post_required' => array('tipoGrafico'), 
			'post_optional' => $post_params_relatorio_grafico
		),
        'md_pet_verifica_usuarios_intimacao' => array(
			'get' => array('acao_ajax', 'id_documento', 'infra_sistema', 'infra_unidade_atual', 'infra_hash'), 
			'post_required' => array('cpfList'), 
			'post_optional' => array('id_documento')
		),
        'md_pet_verifica_destinatarios_intimacao' => array(
			'get' => array('acao_ajax', 'id_documento', 'infra_sistema', 'infra_unidade_atual', 'infra_hash'), 
			'post_required' => array('cnpjList'), 
			'post_optional' => array('id_documento')
		),
        'contato_auto_completar' => array(
			'get' => array('acao_ajax', 'origem', 'infra_sistema', 'infra_unidade_atual', 'infra_hash'), 
			'post_required' => array('palavras_pesquisa', 'id_grupo_contato', 'tipo_contato'), 
			'post_optional' => array()
		)
	);

    mdPetValidarContratoAjax($acaoAjax, $contratosAcoes);

	function validarPermissaoAjaxPeticionamento($acaoAjax, SessaoSEI $objSessaoSEI){

		$arrPermissoesPorAcao = [
			'md_pet_verifica_usuarios_intimacao' => ['md_pet_pessoa_fisica'],
			'md_pet_verifica_destinatarios_intimacao' => ['md_pet_pessoa_juridica'],
			'contato_auto_completar' => ['md_pet_int_relatorio_listar', 'md_pet_int_relatorio_fisica', 'md_pet_int_relatorio_juridica'],
			'md_pet_int_relatorio_grafico' => ['md_pet_int_relatorio_listar', 'md_pet_int_relatorio_fisica', 'md_pet_int_relatorio_juridica']
		];

		if (!isset($arrPermissoesPorAcao[$acaoAjax])) {
			return;
		}

		foreach ($arrPermissoesPorAcao[$acaoAjax] as $strPermissao) {
			if ($objSessaoSEI->verificarPermissao($strPermissao)) {
				return;
			}
		}

		throw new InfraException('Acesso negado para executar a ação AJAX de Peticionamento: ' . $acaoAjax . '.');
	}

	$objSessaoSEI = SessaoSEI::getInstance();
    $objSessaoSEI->validarLink();
  
 	switch($acaoAjax){

		case 'md_pet_int_relatorio_grafico':

			validarPermissaoAjaxPeticionamento($acaoAjax, $objSessaoSEI);

			$arrRetorno    = [];
			$tamanho       = MdPetIntRelatorioRN::$GRAFICO_TAMANHO_PADRAO;
			$arrSituacao   = MdPetIntRelatorioINT::retornaArraySituacaoRelatorio();

			foreach($arrSituacao as $key => $value){

				$objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
				$objMdPetIntRelDestDTO->setProcedimentoDocTIPOFK(InfraDTO::$TIPO_FK_OBRIGATORIA);
				$objMdPetIntRelDestDTO->setAceiteTIPOFK(InfraDTO::$TIPO_FK_OPCIONAL);
				$objMdPetIntRelDestDTO->setStrStaSituacaoIntimacao($key);
				$objMdPetIntRelDestDTO->setStrSinPrincipalDoc('S');
				$objMdPetIntRelDestDTO = (new MdPetIntRelatorioRN())->_addFiltroListagem($objMdPetIntRelDestDTO);
				$valor = (new MdPetIntRelDestinatarioRN())->contar($objMdPetIntRelDestDTO);
				if($valor > 0){
					array_push($arrRetorno, [
						'valor'    => $valor,
						'cor'      => MdPetIntRelatorioINT::retornaArrayCorGrafico(),
						'label'    => $value
					]);
				}

			}

			echo empty($arrRetorno) ? 'Nenhum registro encontrado.' : MdPetIntRelatorioINT::_retornaHtmlGrafico($_POST['tipoGrafico'], $arrRetorno, $_POST['idTipoIntimacao'], $tamanho);
        
		break;
         
		case 'md_pet_verifica_usuarios_intimacao':

			validarPermissaoAjaxPeticionamento($acaoAjax, $objSessaoSEI);
			
			// Busca os contatos ja intimados para o documento
			$arrContatosIntimados = [];
			$idDocumento = '';
			
			$cpfList = $_POST['cpfList'];

			if (!is_array($cpfList)) {
				mdPetErroJson('Payload invalido para cpfList.');
				break;
			}

			$foundCpfs = $notFoundCpfs = $notAbleCpfs = [];
			
			if(isset($_POST['id_documento']) && $_POST['id_documento'] !== ''){
				if (!ctype_digit((string) $_POST['id_documento']) || (int) $_POST['id_documento'] <= 0){
					mdPetErroJson('Payload invalido para id_documento.');
					break;
				}
				$idDocumento = (int) $_POST['id_documento'];
				$arrContatosIntimados = array_column((new MdPetIntimacaoRN())->buscaIntimacoesCadastradas($idDocumento), 'Id');
			}
			
			$cpfRegex = '/^(\d{3}\.?\d{3}\.?\d{3}-?\d{2}|\d{11})$/';
			
			foreach($cpfList as $cpf){
				
				$cpfOriginal = $cpf;
				
				// Pega sempre o primeiro dado
				if (strpos($cpf, ' ') !== false) {
					$cpf = explode(' ', $cpf)[0];
				}
				
				// Completa com zeros a esquerda
				if(is_numeric($cpf) && strlen($cpf) < 11){
					$cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
				}
				
				if (preg_match('/[^0-9.-]/', $cpf)) {
					$notFoundCpfs[] = utf8_encode($cpf . ' - Caracteres inválidos');
					continue;
				}
				
				if (!preg_match($cpfRegex, $cpf)) {
					$notFoundCpfs[] = utf8_encode($cpf . ' - Formato inválido');
					continue;
				}
				
				$cpf = trim(preg_replace('/\D/', '', $cpf));
				
				if (preg_match('/^(\d)\1*$/', substr(preg_replace('/\D/', '', $cpf), 0, 9))) {
					$notFoundCpfs[] = utf8_encode($cpf . ' - Sequência inválida');
					continue;
				}
				
				if(InfraUtil::validarCpf($cpf)){
					
					$objUsuarioDTO = new UsuarioDTO();
					$objUsuarioDTO->retNumIdUsuario();
					$objUsuarioDTO->retNumIdContato();
					$objUsuarioDTO->retStrSigla();
					$objUsuarioDTO->retStrNome();
					$objUsuarioDTO->retDblCpfContato();
					$objUsuarioDTO->retStrStaTipo();
					$objUsuarioDTO->setDblCpfContato(InfraUtil::retirarFormatacao($cpf));
					$objUsuarioDTO->adicionarCriterio(['StaTipo', 'StaTipo'], [InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL], [UsuarioRN::$TU_EXTERNO, UsuarioRN::$TU_EXTERNO], [InfraDTO::$OPER_LOGICO_OR]);
					$arrObjUsuarioDTO = (new UsuarioRN())->pesquisar($objUsuarioDTO);
					
					if(!empty($arrObjUsuarioDTO)){
						for($i=0;$i<count($arrObjUsuarioDTO);$i++){
							if (in_array($arrObjUsuarioDTO[$i]->getNumIdContato(), $arrContatosIntimados)) {
								$notFoundCpfs[] = utf8_encode(InfraUtil::formatarCpfCnpj($cpf) . ' - Já intimado');
								$notAbleCpfs[] = utf8_encode(InfraUtil::formatarCpfCnpj($cpf) . ' - ' . $arrObjUsuarioDTO[$i]->getStrNome());
							} else {
								$foundCpfs[] = utf8_encode($arrObjUsuarioDTO[$i]->getNumIdContato().'|'.$arrObjUsuarioDTO[$i]->getStrNome().'|'.$arrObjUsuarioDTO[$i]->getStrSigla().'|'.InfraUtil::formatarCpfCnpj($cpf));
							}
						}
					}else{
						$notFoundCpfs[] = utf8_encode($cpfOriginal . ' - Não localizado');
					}
					
				}else{
					$notFoundCpfs[] = utf8_encode(InfraUtil::formatarCpfCnpj($cpf) . ' - CPF inválido');
				}
				
			}
			
			$response = [
				'foundCpfs'     => $foundCpfs,
				'notFoundCpfs'  => $notFoundCpfs,
				'notAbleCpfs'   => $notAbleCpfs
			];
			
			echo json_encode($response);
		
		break;
		
		case 'md_pet_verifica_destinatarios_intimacao':

			validarPermissaoAjaxPeticionamento($acaoAjax, $objSessaoSEI);
			
			// Busca os contatos ja intimados para o documento
			$arrContatosIntimados = [];
			$idDocumento = '';
			
			$cnpjList = $_POST['cnpjList'];
			if (!is_array($cnpjList)) {
				mdPetErroJson('Payload invalido para cnpjList.');
				break;
			}
			$foundCnpjs = $notFoundCnpjs = $notAbleCnpjs = [];
			
			if(isset($_POST['id_documento']) && $_POST['id_documento'] !== ''){
				if (!ctype_digit((string) $_POST['id_documento']) || (int) $_POST['id_documento'] <= 0){
					mdPetErroJson('Payload invalido para id_documento.');
					break;
				}
				$idDocumento = (int) $_POST['id_documento'];
				$arrContatosIntimados = array_column((new MdPetIntimacaoRN())->buscaIntimacoesCadastradasJuridico($idDocumento), 'Id');
			}
			
			$cnpjRegex = '/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$|^\d{14}$/';
			
			foreach($cnpjList as $cnpj){
							
				$cnpjOriginal = $cnpj;			
				// Pega sempre o primeiro dado
				if (strpos($cnpj, ' ') !== false) {
					$cnpj = explode(' ', $cnpj)[0];
				}
				
				// Completa com zeros a esquerda
				if(is_numeric($cnpj) && strlen($cnpj) < 14){
					$cnpj = str_pad($cnpj, 14, '0', STR_PAD_LEFT);
				}
				
				if (!preg_match('/^[0-9.\-\/]+$/', $cnpj)) {
					$notFoundCnpjs[] = utf8_encode($cnpjOriginal . ' - Caracteres inválidos');
					continue;
				}
				
				if (!preg_match($cnpjRegex, $cnpj)) {
					$notFoundCnpjs[] = utf8_encode($cnpjOriginal . ' - Formato inválido');
					continue;
				}
				
				$cnpj = trim(preg_replace('/\D/', '', $cnpj));			
				if (preg_match('/^(\d)\1*$/', substr(preg_replace('/\D/', '', $cnpj), 0, 12))) {
					$notFoundCnpjs[] = utf8_encode($cnpjOriginal . ' - Sequência inválida');
					continue;
				}
				
				if(InfraUtil::validarCnpj($cnpj)){
					
					$dtoMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
					$dtoMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
					$dtoMdPetVincRepresentantDTO->retNumIdContatoVinc();
					$dtoMdPetVincRepresentantDTO->retNumIdContatoProcurador();
					$dtoMdPetVincRepresentantDTO->setDistinct(true);
					$dtoMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
					$dtoMdPetVincRepresentantDTO->setStrTpVinc(MdPetVincRepresentantRN::$NT_JURIDICA);
					$dtoMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
					$dtoMdPetVincRepresentantDTO->setStrIdxContato('%' . InfraUtil::retirarFormatacao($cnpj) . '%', InfraDTO::$OPER_LIKE);
					$arrObjMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN())->listar($dtoMdPetVincRepresentantDTO);				

					$arrRepres = array_unique(InfraArray::converterArrInfraDTO($arrObjMdPetVincRepresentantDTO, 'IdContatoVinc'));				

					if(is_iterable($arrRepres) && count($arrRepres) > 0){					

						$objContatoDTO = new ContatoDTO();
						$objContatoDTO->retStrNome();
						$objContatoDTO->retDblCnpj();
						$objContatoDTO->retNumIdContato();
						$objContatoDTO->setNumIdContato($arrRepres, infraDTO::$OPER_IN);
						$arrObjContatoDTO = (new ContatoRN())->listarRN0325($objContatoDTO);					

						if(!empty($arrObjContatoDTO) && count($arrObjContatoDTO) > 0){					 	

							$arrTemp = [];

							foreach($arrObjContatoDTO as $contatoDTO){

								if ($contatoDTO->get('Nome') != null && $contatoDTO->get('Cnpj') != null) {

									$strChave = strtolower($contatoDTO->get('Nome').'-'.$contatoDTO->get('Cnpj'));

									if (!isset($arrTemp[$strChave])) {

										$arrTemp[$strChave] = array($contatoDTO);

									} else {

										$arrTemp[$strChave][] = $contatoDTO;

									}

								}

							}						

							foreach($arrTemp as $arr){

								if (count($arr) == 1){

									$arr[0]->setStrNome($arr[0]->get('Nome').' - '.InfraUtil::formatarCpfCnpj($arr[0]->get('Cnpj')));

								}else{

									foreach($arr as $dto){

										$dto->setStrNome($dto->get('Nome').' - '.InfraUtil::formatarCpfCnpj($dto->get('Cnpj')));

									}

								}

							}						

						}					

					}

					
					if(!empty($arrObjContatoDTO)){
						
						for($i=0;$i<count($arrObjContatoDTO);$i++){
							if (in_array($arrObjContatoDTO[$i]->getNumIdContato(), $arrContatosIntimados)) {
								$notFoundCnpjs[] = utf8_encode(InfraUtil::formatarCpfCnpj($cnpj) . ' - Já intimado');
								$notAbleCnpjs[] = utf8_encode(InfraUtil::formatarCpfCnpj($cnpj) . ' - ' . $arrObjContatoDTO[$i]->getStrNome());
							} else {
								$foundCnpjs[] = utf8_encode($arrObjContatoDTO[$i]->getNumIdContato().'|'.$arrObjContatoDTO[$i]->getStrNome().'|'.InfraUtil::formatarCpfCnpj($cnpj));
							}
						}
						
					}else{
						$notFoundCnpjs[] = utf8_encode($cnpjOriginal . ' - Não localizado');
					}
					
				}else{
					$notFoundCnpjs[] = utf8_encode(InfraUtil::formatarCpfCnpj($cnpj) . ' - CNPJ inválido');
				}
			
			}
		
			$response = [
				'foundCnpjs'     => $foundCnpjs,
				'notFoundCnpjs'  => $notFoundCnpjs,
				'notAbleCnpjs'   => $notAbleCnpjs
			];
		
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
	
	 	break;
	
		case 'contato_auto_completar':

			validarPermissaoAjaxPeticionamento($acaoAjax, $objSessaoSEI);
			
			$strPalavrasPesquisa = utf8_decode(urldecode($_POST['palavras_pesquisa']));
			$numIdGrupoContato = $_POST['id_grupo_contato'];
			$natureza = $_POST['tipo_contato'];
			
			$arrObjContatoDTO = array();
			
			$objPesquisaTipoContatoDTO = new PesquisaTipoContatoDTO();
			$objPesquisaTipoContatoDTO->setStrStaAcesso(TipoContatoRN::$TA_CONSULTA_RESUMIDA);
			
			$objTipoContatoRN = new TipoContatoRN();
			$arrIdTipoContatoAcesso = $objTipoContatoRN->pesquisarAcessoUnidade($objPesquisaTipoContatoDTO);
			
			if (count($arrIdTipoContatoAcesso)) {
				
				$objContatoDTO = new ContatoDTO();
				$objContatoDTO->retNumIdContato();
				$objContatoDTO->retStrSigla();
				$objContatoDTO->retStrNome();
				$objContatoDTO->retStrSiglaContatoAssociado();
				
				$objContatoDTO->setStrPalavrasPesquisa($strPalavrasPesquisa);
				
				if ($numIdGrupoContato != '') {
					$objContatoDTO->setNumIdGrupoContato($numIdGrupoContato);
				}
				
				$objContatoDTO->adicionarCriterio(array('StaAcessoTipoContato', 'IdTipoContato'),
					array(InfraDTO::$OPER_DIFERENTE, InfraDTO::$OPER_IN),
					array(TipoContatoRN::$TA_NENHUM, $arrIdTipoContatoAcesso),
					InfraDTO::$OPER_LOGICO_OR);
				
				$objContatoDTO->setStrSinAtivoTipoContato('S');
				$objContatoDTO->setNumMaxRegistrosRetorno(50);
				$objContatoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
				
				$objContatoDTO->setStrStaNatureza($natureza);
				
				$objContatoRN = new ContatoRN();
				$arrObjContatoDTO = $objContatoRN->pesquisarRN0471($objContatoDTO);
				
				$arrTemp = array();
				foreach($arrObjContatoDTO as $objContatoDTO){
					if ($objContatoDTO->getStrSigla()!=null && $objContatoDTO->getStrSiglaContatoAssociado()!= null) {
						$strChave = strtolower($objContatoDTO->getStrNome().'-'.$objContatoDTO->getStrSigla());
						if (!isset($arrTemp[$strChave])) {
							$arrTemp[$strChave] = array($objContatoDTO);
						} else {
							$arrTemp[$strChave][] = $objContatoDTO;
						}
					}
				}
				
				foreach($arrTemp as $arr){
					if (count($arr) == 1){
						$arr[0]->setStrNome($arr[0]->getStrNome().' ('.$arr[0]->getStrSigla().')');
					}else{
						foreach($arr as $dto){
							$dto->setStrNome($dto->getStrNome().' ('.$dto->getStrSigla().' / '.$dto->getStrSiglaContatoAssociado().')');
						}
					}
				}
			}
			
			$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjContatoDTO,'IdContato', 'Nome');
			InfraAjax::enviarXML($xml);

		break;

	default:
      	throw new InfraException("Ação '".$acaoAjax."' não reconhecida pelo controlador AJAX do Peticionamento.");
  	}
  
}catch(Exception $e){
  InfraAjax::processarExcecao($e);
}
