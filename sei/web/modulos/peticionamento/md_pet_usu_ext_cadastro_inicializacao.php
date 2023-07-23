<?
/**
* ANATEL
*
* 01/08/2016 - criado por marcelo.bezerra@cast.com.br - CAST
*
* Defini��o de objetos e variaveis necess�rias para a inicializa��o da p�gina
*
*/
//=====================================================
//INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
//=====================================================

//extensoes permitidas para upload
if( $_GET['acao'] != "md_pet_usu_ext_download"){

	$strDesabilitar = '';

	$arrComandos = array();

	//pegar lista de extensoes parametrizadas do m�dulo
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
$strMsgTooltipInteressadoProprioUsuarioExterno	= 'Para o Tipo de Processo escolhido o Interessado do processo a ser aberto somente pode ser o pr�prio Usu�rio Externo logado no sistema.';
$strMsgTooltipInteressadoInformandoCPFeCNPJ		= 'Para o Tipo de Processo escolhido � poss�vel adicionar os Interessados do processo a ser aberto por meio da indica��o de CPF ou CNPJ v�lidos, devendo complementar seus cadastros caso necess�rio.';
$strMsgTooltipInteressadoDigitadoNomeExistente	= 'Para o Tipo de Processo escolhido � poss�vel adicionar os Interessados do processo a ser aberto a partir da base de Interessados j� existentes. Caso necess�rio, clique na Lupa "Localizar Interessados" para uma pesquisa mais detalhada ou, na janela aberta, acessar o bot�o "Cadastrar Novo Interessado".';
$strMsgTooltipTipoDocumentoPrincipal			= 'Como somente pode ter um Documento Principal, o Tipo de Documento correspondente j� � previamente definido. Deve, ainda, ser complementado no campo ao lado.';
$strMsgTooltipTipoDocumentoPrincipalFormulario	= 'O documento principal deste tipo de peticionamento possui modelo previamente definido, o qual deve ser acessado no Editor do SEI no link ao lado.';
$strMsgTooltipTipoDocumento						= 'Selecione o Tipo de Documento que melhor identifique o documento a ser carregado e complemente o Tipo no campo ao lado.';
$strMsgTooltipComplementoTipoDocumento			= 'O Complemento do Tipo de Documento � o texto que completa a identifica��o do documento a ser carregado, adicionando ao nome do Tipo o texto que for digitado no referido campo (Tipo �Recurso� e Complemento �de 1� Inst�ncia� identificar� o documento como �Recurso de 1� Inst�ncia�).\n\n\n Exemplos: O Complemento do Tipo �Nota� pode ser �Fiscal Eletr�nica� ou �Fiscal n� 75/2016�. O Complemento do Tipo �Comprovante� pode ser �de Pagamento� ou �de Endere�o�.';
$strMsgTooltipNivelAcesso						= 'O N�vel de Acesso que for indicado � de sua exclusiva responsabilidade e estar� condicionado � an�lise por servidor p�blico, que poder� alter�-lo a qualquer momento sem necessidade de pr�vio aviso.\n\n\n Selecione "P�blico" se no teor do documento a ser carregado n�o existir informa��es restritas. Se no teor do documento existir informa��es restritas, selecione "Restrito".';
$strMsgTooltipHipoteseLegal						= 'Para o N�vel de Acesso "Restrito" � obrigat�ria a indica��o da Hip�tese Legal correspondente � informa��o restrita constante no teor do documento a ser carregado, sendo de sua exclusiva responsabilidade a referida indica��o. Em caso de d�vidas, pesquise sobre a legisla��o indicada entre par�nteses em cada Hip�tese listada.';
$strMsgTooltipNivelAcessoPadraoPreDefinido		= 'Para o Tipo de Processo escolhido o N�vel de Acesso � previamente definido.';
$strMsgTooltipHipoteseLegalPadraoPreDefinido	= 'Para o Tipo de Processo escolhido o N�vel de Acesso � previamente definido como "Restrito" e, assim, a Hip�tese Legal tamb�m � previamente definida.';
$strMsgTooltipFormato							= 'Selecione a op��o �Nato-digital� se o arquivo a ser carregado foi criado originalmente em meio eletr�nico.\n\n\n Selecione a op��o �Digitalizado� somente se o arquivo a ser carregado foi produzido da digitaliza��o de um documento em papel.';
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

//ler configura�oes necessarias para aplicar a RN 8
/*
 [RN8]	O sistema deve verificar na funcionalidade �Gerir Tipos de Processo para Peticionamento� se o documento principal
selecionado foi �Externo (Anexa��o de Arquivo)�. Caso tenha sido selecionado ao preencher os dados do
	novo peticionamento, o sistema permitir� anexar o arquivo conforme o tipo informado.
*/
$isDocPrincipalGerado = $objTipoProcDTO->getStrSinDocGerado();
$isDocPrincipalExterno = $objTipoProcDTO->getStrSinDocExterno();

$serieRN = new SerieRN();
$serieDTO = new SerieDTO();
$serieDTO->retTodos();
$serieDTO->setNumIdSerie( $objTipoProcDTO->getNumIdSerie() );
$serieDTO = $serieRN->consultarRN0644( $serieDTO );
$strTipoDocumentoPrincipal = $serieDTO->getStrNome();

//ler configura�oes necessarias para aplicar RN18
/*
 [RN18]	Os campos �N�veis de Acesso� e �Hip�tese Legal� deve conter as op��es de acordo com o cadastro realizado:

- CENARIO 1 ::: Na funcionalidade �Gerir Tipo de Processo� tenha sido selecionado como n�vel de acesso a op��o �Padr�o�, o sistema
deve apresentar o n�vel de acesso para o tipo cadastrado na pr�pria funcionalidade de Gerir Tipo de Processo,
apresentando somente o registro cadastrado.

- CENARIO 2 ::: Caso tenha sido selecionada a op��o �Usu�rio Externo pode Indicar dentre os permitidos para o Tipo de Processo�
dever� ser apresentada as op��es: P�blico, Restrito e Sigiloso.
*/

$isNivelAcessoPadrao = $objTipoProcDTO->getStrSinNaPadrao();
$nivelAcessoPadrao = $objTipoProcDTO->getStrStaNivelAcesso();

if( $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == "1"){
    $objTipoProcDTOHp = clone($objTipoProcDTO);
	$objTipoProcDTOHp->retNumIdHipoteseLegal();
	$objTipoProcDTOHp->retStrNomeHipoteseLegal();
    $objTipoProcDTOHp->retStrBaseLegalHipoteseLegal();
    $objTipoProcDTOHp = $objTipoProcRN->consultar( $objTipoProcDTOHp );
	$idHipoteseLegalPadrao = $objTipoProcDTOHp->getNumIdHipoteseLegal();
    $strHipoteseLegalPadrao = $objTipoProcDTOHp->getStrNomeHipoteseLegal();
    $strHipoteseLegalPadrao .= " (".$objTipoProcDTOHp->getStrBaseLegalHipoteseLegal().")";
}

$isUsuarioExternoPodeIndicarNivelAcesso = $objTipoProcDTO->getStrSinNaUsuarioExterno();
$strNomeNivelAcessoPadrao = "";

if( $isNivelAcessoPadrao == 'S'){
	 
	if( $nivelAcessoPadrao == "0"){ $strNomeNivelAcessoPadrao = "P�blico"; }
	else if( $nivelAcessoPadrao == "1"){ $strNomeNivelAcessoPadrao = "Restrito"; }
	else if( $nivelAcessoPadrao == "2"){ $strNomeNivelAcessoPadrao = "Sigiloso"; }
	 
}

//checando se Documento Principal est� parametrizado para "Externo (Anexa��o de Arquivo) ou Gerador (editor do SEI)
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
//aplicar RA 5: Os campos �Hip�tese Legal� somente ser�o apresentados se na funcionalidade Infra > Par�metros
//a op��o SEI_HABILITAR_HIPOTESE_LEGAL estiver configurado como 1 ou 2 sendo assim obrigat�rio.
$objInfraParametro = new InfraParametro(BancoSEI::getInstance());
$valorConfigHipoteseLegal = $objInfraParametro->getValor('SEI_HABILITAR_HIPOTESE_LEGAL', false);
$isConfigHipoteseLegal = false;

if( $valorConfigHipoteseLegal == 1 || $valorConfigHipoteseLegal == 2){

	$isConfigHipoteseLegal = true;
	 
	//verificar se irei trazer hipoteses legais da parametriza�ao do peticionamento ou se irei consultar as hipoteses cadastradas no sistema
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

//preenche a combo "Documento Objeto da Digitaliza��o era"
$arrDocumentoObjetoDigitalizacao = array();

//preenche tabela de documentos (final da tela)
$arrTabelaDocumentos = array();

//DTO basico de Processo Peticionamento Novo
$objMdPetIndisponibilidadeDTO = new MdPetIndisponibilidadeDTO();

//listagem da combo "Documento objeto da Digitaliza��o era:�
$tipoConferenciaRN = new TipoConferenciaRN();
$tipoConferenciaDTO = new TipoConferenciaDTO();
$tipoConferenciaDTO->retTodos();
$tipoConferenciaDTO->setStrSinAtivo('S');
$tipoConferenciaDTO->setOrdStrDescricao(InfraDTO::$TIPO_ORDENACAO_ASC);
$arrTipoConferencia = $tipoConferenciaRN->listar( $tipoConferenciaDTO );

//tamanho maximo de arquivo, tem o interno do SEI e tem o da parametriza�ao 
$numSeiTamMbDocExterno = $objInfraParametro->getValor('SEI_TAM_MB_DOC_EXTERNO');
$numSeiTamMbDocExterno = ($numSeiTamMbDocExterno < 1024 ? $numSeiTamMbDocExterno." MB" : (round($numSeiTamMbDocExterno/1024,2))." GB");

//limpando variavel de sessao que controla detalhes de exibicao internos 
//da janela de cadastro de interessado (quando � indicacao por nome)
SessaoSEIExterna::getInstance()->removerAtributo('janelaSelecaoPorNome');

$urlBaseLink = "";
$arrComandos = array();
$arrComandos[] = '<button tabindex="-1" type="button" accesskey="p" name="Peticionar" id="Peticionar" value="Peticionar" onclick="abrirPeticionar()" class="infraButton"><span class="infraTeclaAtalho">P</span>eticionar</button>';
$arrComandos[] = '<button tabindex="-1" type="button" accesskey="v" name="btnVoltar" id="btnVoltar" value="Voltar" onclick="location.href=\''.PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_iniciar&id_orgao_acesso_externo=0&id_tipo_procedimento='.$_GET['id_tipo_procedimento'].'')).'\';" class="infraButton"><span class="infraTeclaAtalho">V</span>oltar</button>';


$objInfraParametroDTO = new InfraParametroDTO();
$objMdPetParametroRN = new MdPetParametroRN();
$objInfraParametroDTO->retTodos();
$objInfraParametroDTO->setStrNome('SEI_HABILITAR_HIPOTESE_LEGAL');
$objInfraParametroDTO = $objMdPetParametroRN->consultar($objInfraParametroDTO);
$valorParametroHipoteseLegal = $objInfraParametroDTO->getStrValor();

// Forcar o Nivel de Acesso parametrizado
$nivelAcessoDoc = MdPetForcarNivelAcessoDocINT::getDadosForcarNivelAcessoDoc($tipoPeticionamento = 'N');