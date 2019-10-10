<?
/**
* ANATEL
*
* 01/08/2016 - criado por marcelo.bezerra@cast.com.br - CAST
*
* Definição de objetos e variaveis necessárias para a inicialização da página
*
*/
//=====================================================
//INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
//=====================================================

//extensoes permitidas para upload
if( $_GET['acao'] != "md_pet_usu_ext_download"){

	$strDesabilitar = '';

	$arrComandos = array();

	//pegar lista de extensoes parametrizadas do módulo
	$dtoTamanhoArquivoPrincipal = new MdPetExtensoesArquivoDTO();
	$dtoTamanhoArquivoPrincipal->retTodos();
	$dtoTamanhoArquivoPrincipal->setStrSinAtivo('S');
	$dtoTamanhoArquivoPrincipal->setStrSinPrincipal('S');
	
	$dtoTamanhoArquivoEssencialComplementar = new MdPetExtensoesArquivoDTO();
	$dtoTamanhoArquivoEssencialComplementar->retTodos();
	$dtoTamanhoArquivoEssencialComplementar->setStrSinAtivo('S');
	$dtoTamanhoArquivoEssencialComplementar->setStrSinPrincipal('N');
	
	$objMdPetExtensoesArquivoRN = new MdPetExtensoesArquivoRN();
	
	$arrDTOTamanhoArquivoPrincipal = $objMdPetExtensoesArquivoRN->listar( $dtoTamanhoArquivoPrincipal );
	$arrDTOTamanhoArquivoEssencialComplementar = $objMdPetExtensoesArquivoRN->listar( $dtoTamanhoArquivoEssencialComplementar );
	$arrExtPermitidas = array();
	$arrExtPermitidasEssencialComplementar = array();
	
	$objArquivoExtensaoRN = new ArquivoExtensaoRN();
	
	//extensoes para doc principal	
	if(count($arrDTOTamanhoArquivoPrincipal) > 0)
	{
		
		$key = 0;
		
		foreach($arrDTOTamanhoArquivoPrincipal as $objTamanhoArquivoDTO)
		{
			
			$objArquivoExtensaoDTO = new ArquivoExtensaoDTO();
			$objArquivoExtensaoDTO->retTodos();
			$objArquivoExtensaoDTO->setNumIdArquivoExtensao( $objTamanhoArquivoDTO->getNumIdArquivoExtensao() );
			$objArquivoExtensaoDTO = $objArquivoExtensaoRN->consultar( $objArquivoExtensaoDTO );
			
			$key = $key + 1;
			$chave = (string) $key;
			$chave = 'ext_'.$chave;
			$arrExtPermitidas[$chave] = $objArquivoExtensaoDTO->getStrExtensao();
		}
	} 
	
	//extensoes para docs essencial e complementar
	if(count($arrDTOTamanhoArquivoEssencialComplementar) > 0)
	{
		$keyEssencial = 0;
		
		foreach($arrDTOTamanhoArquivoEssencialComplementar as $objTamanhoArquivoDTOEssencialComplementar)
		{
			$objArquivoExtensaoDTO = new ArquivoExtensaoDTO();
			$objArquivoExtensaoDTO->retTodos();
			$objArquivoExtensaoDTO->setNumIdArquivoExtensao( $objTamanhoArquivoDTOEssencialComplementar->getNumIdArquivoExtensao() );
			$objArquivoExtensaoDTO = $objArquivoExtensaoRN->consultar( $objArquivoExtensaoDTO );
			
			$keyEssencial = $keyEssencial + 1;
			$chave = (string) $keyEssencial;
			$chave = 'ext_'.$chave;
			$arrExtPermitidasEssencialComplementar[$chave] = $objArquivoExtensaoDTO->getStrExtensao();
		}
	}

	$jsonExtPermitidas =  count($arrExtPermitidas) > 0 ? json_encode($arrExtPermitidas) : null;
	$jsonExtEssencialComplementarPermitidas =  count($arrExtPermitidasEssencialComplementar) > 0 ? json_encode($arrExtPermitidasEssencialComplementar) : null;
}

//tipo de processo escolhido

$idTipoProc = $_GET['id_tipo_procedimento'];
$objTipoProcDTO = new MdPetTipoProcessoDTO();
$objTipoProcDTO->retTodos();
$objTipoProcDTO->retStrNomeProcesso();
$objTipoProcDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
$objTipoProcRN = new MdPetTipoProcessoRN();
$objTipoProcDTO = $objTipoProcRN->consultar( $objTipoProcDTO );

//texto de orientacoes
$txtOrientacoes = $objTipoProcDTO->getStrOrientacoes();

//Msgs dos Tooltips de Ajuda
$strMsgTooltipInteressadoProprioUsuarioExterno	= 'Para o Tipo de Processo escolhido o Interessado do processo a ser aberto somente pode ser o próprio Usuário Externo logado no sistema.';
$strMsgTooltipInteressadoInformandoCPFeCNPJ		= 'Para o Tipo de Processo escolhido é possível adicionar os Interessados do processo a ser aberto por meio da indicação de CPF ou CNPJ válidos, devendo complementar seus cadastros caso necessário.';
$strMsgTooltipInteressadoDigitadoNomeExistente	= 'Para o Tipo de Processo escolhido é possível adicionar os Interessados do processo a ser aberto a partir da base de Interessados já existentes. Caso necessário, clique na Lupa "Localizar Interessados" para uma pesquisa mais detalhada ou, na janela aberta, acessar o botão "Cadastrar Novo Interessado".';
$strMsgTooltipTipoDocumentoPrincipal			= 'Como somente pode ter um Documento Principal, o Tipo de Documento correspondente já é previamente definido. Deve, ainda, ser complementado no campo ao lado.';
$strMsgTooltipTipoDocumentoPrincipalFormulario	= 'O documento principal deste tipo de peticionamento possui modelo previamente definido, o qual deve ser acessado no Editor do SEI no link ao lado.';
$strMsgTooltipTipoDocumento						= 'Selecione o Tipo de Documento que melhor identifique o documento a ser carregado e complemente o Tipo no campo ao lado.';
$strMsgTooltipComplementoTipoDocumento			= 'O Complemento do Tipo de Documento é o texto que completa a identificação do documento a ser carregado, adicionando ao nome do Tipo o texto que for digitado no referido campo (Tipo “Recurso” e Complemento “de 1ª Instância” identificará o documento como “Recurso de 1ª Instância”).\n\n\n Exemplos: O Complemento do Tipo “Nota” pode ser “Fiscal Eletrônica” ou “Fiscal nº 75/2016”. O Complemento do Tipo “Comprovante” pode ser “de Pagamento” ou “de Endereço”.';
$strMsgTooltipNivelAcesso						= 'O Nível de Acesso que for indicado é de sua exclusiva responsabilidade e estará condicionado à análise por servidor público, que poderá alterá-lo a qualquer momento sem necessidade de prévio aviso.\n\n\n Selecione "Público" se no teor do documento a ser carregado não existir informações restritas. Se no teor do documento existir informações restritas, selecione "Restrito".';
$strMsgTooltipHipoteseLegal						= 'Para o Nível de Acesso "Restrito" é obrigatória a indicação da Hipótese Legal correspondente à informação restrita constante no teor do documento a ser carregado, sendo de sua exclusiva responsabilidade a referida indicação. Em caso de dúvidas, pesquise sobre a legislação indicada entre parênteses em cada Hipótese listada.';
$strMsgTooltipNivelAcessoPadraoPreDefinido		= 'Para o Tipo de Processo escolhido o Nível de Acesso é previamente definido.';
$strMsgTooltipHipoteseLegalPadraoPreDefinido	= 'Para o Tipo de Processo escolhido o Nível de Acesso é previamente definido como "Restrito" e, assim, a Hipótese Legal também é previamente definida.';
$strMsgTooltipFormato							= 'Selecione a opção “Nato-digital” se o arquivo a ser carregado foi criado originalmente em meio eletrônico.\n\n\n Selecione a opção “Digitalizado” somente se o arquivo a ser carregado foi produzido da digitalização de um documento em papel.';
//Fim Msgs

//obtendo a unidade do tipo de processo selecionado - pode ser uma ou MULTIPLAS unidades selecionadas
$objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
$objMdPetRelTpProcessoUnidDTO->retTodos();
$objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
$objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
$arrMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar( $objMdPetRelTpProcessoUnidDTO );

$arrUnidadeUFDTO = null;
$idUnidadeTipoProcesso = null;

//APENAS UMA UNIDADE
if( $arrMdPetRelTpProcessoUnidDTO != null && count( $arrMdPetRelTpProcessoUnidDTO ) == 1 ) {
  
	$idUnidadeTipoProcesso = $arrMdPetRelTpProcessoUnidDTO[0]->getNumIdUnidade();
  
}

//MULTIPLAS UNIDADES
else if( $arrMdPetRelTpProcessoUnidDTO != null && count( $arrMdPetRelTpProcessoUnidDTO ) > 1 ){
		
	$arrIdUnidade = array();
	
	//consultar UFs das unidades informadas
	foreach( $arrMdPetRelTpProcessoUnidDTO as $itemRelTipoProcDTO ){
		$arrIdUnidade[] = $itemRelTipoProcDTO->getNumIdUnidade();
	}
	
	$objUnidadeDTO = new UnidadeDTO();
	$objUnidadeDTO->retNumIdUnidade();
	$objUnidadeDTO->retNumIdContato();

	$objUnidadeDTO->adicionarCriterio(array('IdUnidade', 'SinAtivo'),
			array(InfraDTO::$OPER_IN, InfraDTO::$OPER_IGUAL),
			array( $arrIdUnidade,'S'),
			InfraDTO::$OPER_LOGICO_AND);
	
	$objUnidadeRN = new UnidadeRN();
	$arrUnidadeUFDTO = $objUnidadeRN->listarRN0127( $objUnidadeDTO );
	
}

$ObjMdPetRelTpProcSerieRN = new MdPetRelTpProcSerieRN();
$ObjMdPetRelTpProcSerieDTO = new MdPetRelTpProcSerieDTO();
$ObjMdPetRelTpProcSerieDTO->retTodos();
$ObjMdPetRelTpProcSerieDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
$arrTiposDocumentosComplementares = $ObjMdPetRelTpProcSerieRN->listar( $ObjMdPetRelTpProcSerieDTO );

//ler configuraçoes necessarias para aplicar a RN 8
/*
 [RN8]	O sistema deve verificar na funcionalidade “Gerir Tipos de Processo para Peticionamento” se o documento principal
selecionado foi “Externo (Anexação de Arquivo)”. Caso tenha sido selecionado ao preencher os dados do
	novo peticionamento, o sistema permitirá anexar o arquivo conforme o tipo informado.
*/
$isDocPrincipalGerado = $objTipoProcDTO->getStrSinDocGerado();
$isDocPrincipalExterno = $objTipoProcDTO->getStrSinDocExterno();

$serieRN = new SerieRN();
$serieDTO = new SerieDTO();
$serieDTO->retTodos();
$serieDTO->setNumIdSerie( $objTipoProcDTO->getNumIdSerie() );
$serieDTO = $serieRN->consultarRN0644( $serieDTO );
$strTipoDocumentoPrincipal = $serieDTO->getStrNome();

//ler configuraçoes necessarias para aplicar RN18
/*
 [RN18]	Os campos “Níveis de Acesso” e “Hipótese Legal” deve conter as opções de acordo com o cadastro realizado:

- CENARIO 1 ::: Na funcionalidade “Gerir Tipo de Processo” tenha sido selecionado como nível de acesso a opção “Padrão”, o sistema
deve apresentar o nível de acesso para o tipo cadastrado na própria funcionalidade de Gerir Tipo de Processo,
apresentando somente o registro cadastrado.

- CENARIO 2 ::: Caso tenha sido selecionada a opção “Usuário Externo pode Indicar dentre os permitidos para o Tipo de Processo”
deverá ser apresentada as opções: Público, Restrito e Sigiloso.
*/

$isNivelAcessoPadrao = $objTipoProcDTO->getStrSinNaPadrao();
$nivelAcessoPadrao = $objTipoProcDTO->getStrStaNivelAcesso();

if( $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == "1"){
	
	$objTipoProcDTO->retTodos(true);
	$objTipoProcDTO = $objTipoProcRN->consultar( $objTipoProcDTO );
	$idHipoteseLegalPadrao = $objTipoProcDTO->getNumIdHipoteseLegal();
    $strHipoteseLegalPadrao = $objTipoProcDTO->getStrNomeHipoteseLegal();
    $strHipoteseLegalPadrao .= " (".$objTipoProcDTO->getStrBaseLegalHipoteseLegal().")";
}

$isUsuarioExternoPodeIndicarNivelAcesso = $objTipoProcDTO->getStrSinNaUsuarioExterno();
$strNomeNivelAcessoPadrao = "";

if( $isNivelAcessoPadrao == 'S'){
	 
	if( $nivelAcessoPadrao == "0"){ $strNomeNivelAcessoPadrao = "Público"; }
	else if( $nivelAcessoPadrao == "1"){ $strNomeNivelAcessoPadrao = "Restrito"; }
	else if( $nivelAcessoPadrao == "2"){ $strNomeNivelAcessoPadrao = "Sigiloso"; }
	 
}

//checando se Documento Principal está parametrizado para "Externo (Anexação de Arquivo) ou Gerador (editor do SEI)
$objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
$objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
$objMdPetTipoProcessoDTO->setStrSinAtivo('S', InfraDTO::$OPER_IGUAL);
$objMdPetTipoProcessoDTO->retTodos();
$objMdPetTipoProcessoDTO->setNumIdProcedimento( $objTipoProcDTO->getNumIdProcedimento() , InfraDTO::$OPER_IGUAL );
$ObjMdPetTipoProcessoDTO = $objMdPetTipoProcessoRN->consultar( $objMdPetTipoProcessoDTO );

$txtTipoProcessoEscolhido = $objTipoProcDTO->getStrNomeProcesso();

//preeche a lista de interessados PF/PJ CASO 2
$arrPFPJInteressados = array();

//preenche a combo de interessados - CASO 3
$arrContatosInteressados = array();

//preenche a combo "Tipo"
$arrTipo = array();

//preenche a combo "Nivel de acesso"
$arrNivelAcesso = array();

//monta combo "Nivel de acesso"
$strItensSelNivelAcesso  = MdPetTipoProcessoINT::montarSelectNivelAcesso(null, null, null, $objTipoProcDTO->getNumIdProcedimento());

//ler valor do parametro SEI_HABILITAR_HIPOTESE_LEGAL
//aplicar RA 5: Os campos “Hipótese Legal” somente serão apresentados se na funcionalidade Infra > Parâmetros
//a opção SEI_HABILITAR_HIPOTESE_LEGAL estiver configurado como 1 ou 2 sendo assim obrigatório.
$objInfraParametro = new InfraParametro(BancoSEI::getInstance());
$valorConfigHipoteseLegal = $objInfraParametro->getValor('SEI_HABILITAR_HIPOTESE_LEGAL', false);
$isConfigHipoteseLegal = false;

if( $valorConfigHipoteseLegal == 1 || $valorConfigHipoteseLegal == 2){

	$isConfigHipoteseLegal = true;
	 
	//verificar se irei trazer hipoteses legais da parametrizaçao do peticionamento ou se irei consultar as hipoteses cadastradas no sistema
	$objMdPetHipoteseLegalRN = new MdPetHipoteseLegalRN();
	$objMdPetHipoteseLegalDTO = new MdPetHipoteseLegalDTO();
	$objMdPetHipoteseLegalDTO->retTodos(true);
	$arrObjMdPetHipoteseLegalDTO = $objMdPetHipoteseLegalRN->listar( $objMdPetHipoteseLegalDTO );
	
	$arrIdsHipotesesParametrizadas = array();
	if( $arrObjMdPetHipoteseLegalDTO != null && count( $arrObjMdPetHipoteseLegalDTO ) > 0){
		
		foreach( $arrObjMdPetHipoteseLegalDTO as $itemDTO ){
			$arrIdsHipotesesParametrizadas[] = $itemDTO->getNumIdHipoteseLegalPeticionamento();
		}
		
		//trazer uma lista contendo apenas as hipoteses legais parametrizadas
		
		$arrHipoteseLegal = $objMdPetHipoteseLegalRN->listarHipotesesParametrizadas( $arrIdsHipotesesParametrizadas );
		
	} else {
	
		//preenche a combo "Hipotese legal"
		$hipoteseRN = new HipoteseLegalRN();
		$hipoteseDTO = new HipoteseLegalDTO();
		$hipoteseDTO->retTodos();
		$hipoteseDTO->setStrSinAtivo('S');
		$hipoteseDTO->setOrd('Nome', InfraDTO::$TIPO_ORDENACAO_ASC);
		$arrHipoteseLegal = $hipoteseRN->listar( $hipoteseDTO );
	
	}
}

//preenche a combo "Documento Objeto da Digitalização era"
$arrDocumentoObjetoDigitalizacao = array();

//preenche tabela de documentos (final da tela)
$arrTabelaDocumentos = array();

//DTO basico de Processo Peticionamento Novo
$objMdPetIndisponibilidadeDTO = new MdPetIndisponibilidadeDTO();

//listagem da combo "Documento objeto da Digitalização era:”
$tipoConferenciaRN = new TipoConferenciaRN();
$tipoConferenciaDTO = new TipoConferenciaDTO();
$tipoConferenciaDTO->retTodos();
$tipoConferenciaDTO->setStrSinAtivo('S');
$tipoConferenciaDTO->setOrdStrDescricao(InfraDTO::$TIPO_ORDENACAO_ASC);
$arrTipoConferencia = $tipoConferenciaRN->listar( $tipoConferenciaDTO );

//tamanho maximo de arquivo, tem o interno do SEI e tem o da parametrizaçao 
$numSeiTamMbDocExterno = $objInfraParametro->getValor('SEI_TAM_MB_DOC_EXTERNO');
$numSeiTamMbDocExterno = ($numSeiTamMbDocExterno < 1024 ? $numSeiTamMbDocExterno." MB" : (round($numSeiTamMbDocExterno/1024,2))." GB");

//limpando variavel de sessao que controla detalhes de exibicao internos 
//da janela de cadastro de interessado (quando é indicacao por nome)
SessaoSEIExterna::getInstance()->removerAtributo('janelaSelecaoPorNome');

$urlBaseLink = "";
$arrComandos = array();
$arrComandos[] = '<button tabindex="-1" type="button" accesskey="p" name="Peticionar" id="Peticionar" value="Peticionar" onclick="abrirPeticionar()" class="infraButton"><span class="infraTeclaAtalho">P</span>eticionar</button>';
$arrComandos[] = '<button tabindex="-1" type="button" accesskey="v" name="btnVoltar" id="btnVoltar" value="Voltar" onclick="location.href=\''.PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_iniciar&id_orgao_acesso_externo=0&id_tipo_procedimento='.$_GET['id_tipo_procedimento'].'')).'\';" class="infraButton"><span class="infraTeclaAtalho">V</span>oltar</button>';
?>