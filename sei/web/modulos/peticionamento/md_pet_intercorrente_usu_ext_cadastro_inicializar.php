<?php
    //INICIALIZACAO DE VARIAVEIS DA PAGINA
    $txtOrientacoes = "Este peticionamento serve para protocolizar documentos em processos j� existentes. Condicionado ao n�mero do processo e parametriza��es da administra��o sobre o Tipo de Processo correspondente, os documentos poder�o ser inclu�dos diretamente no processo indicado ou em processo novo relacionado.";

    $arrComandos   = array();
    $arrComandos[] = '<button tabindex="-1" type="button" accesskey="p" name="Peticionar" id="Peticionar" value="Peticionar" onclick="abrirPeticionar()" class="infraButton"><span class="infraTeclaAtalho">P</span>eticionar</button>';
    $arrComandos[] = '<button tabindex="-1" type="button" accesskey="C" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\'' . PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=usuario_externo_controle_acessos&id_orgao_acesso_externo=0')) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

    //Links de acesso
    $strLinkUploadArquivo                = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_upload_arquivo');
    $strUrlAjaxMontarSelectTipoDocumento = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_montar_select_tipo_documento');
    $strUrlAjaxMontarSelectNivelAcesso   = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_montar_select_nivel_acesso');
    $strUrlAjaxCriterioIntercorrente     = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_verificar_criterio_intercorrente');

    //Fim Links

    //Msgs dos Tooltips de Ajuda
    $strMsgTooltipTipoDocumento						= 'Selecione o Tipo de Documento que melhor identifique o documento a ser carregado e complemente o Tipo no campo ao lado.';
    $strMsgTooltipComplementoTipoDocumento			= 'O Complemento do Tipo de Documento � o texto que completa a identifica��o do documento a ser carregado, adicionando ao nome do Tipo o texto que for digitado no referido campo (Tipo �Recurso� e Complemento �de 1� Inst�ncia� identificar� o documento como �Recurso de 1� Inst�ncia�).\n\n\n Exemplos: O Complemento do Tipo �Nota� pode ser �Fiscal Eletr�nica� ou �Fiscal n� 75/2016�. O Complemento do Tipo �Comprovante� pode ser �de Pagamento� ou �de Endere�o�.';
    $strMsgTooltipNivelAcesso						= 'O N�vel de Acesso que for indicado � de sua exclusiva responsabilidade e estar� condicionado � an�lise por servidor p�blico, que poder� alter�-lo a qualquer momento sem necessidade de pr�vio aviso.\n\n\n Selecione �P�blico� se no teor do documento a ser carregado n�o existir informa��es restritas. Se no teor do documento existir informa��es restritas, selecione �Restrito�.';
    $strMsgTooltipHipoteseLegal						= 'Para o N�vel de Acesso �Restrito� � obrigat�ria a indica��o da Hip�tese Legal correspondente � informa��o restrita constante no teor do documento a ser carregado, sendo de sua exclusiva responsabilidade a referida indica��o. Em caso de d�vidas, pesquise sobre a legisla��o indicada entre par�nteses em cada Hip�tese listada.';
    $strMsgTooltipNivelAcessoPadraoPreDefinido		= 'Para o N�mero de Processo indicado o N�vel de Acesso � previamente definido.';
    $strMsgTooltipHipoteseLegalPadraoPreDefinido	= 'Para o N�mero de Processo indicado o N�vel de Acesso � previamente definido como �Restrito� e, assim, a Hip�tese Legal tamb�m � previamente definida.';
    $strMsgTooltipFormato							= 'Selecione a op��o �Nato-digital� se o arquivo a ser carregado foi criado originalmente em meio eletr�nico.\n\n\n Selecione a op��o �Digitalizado� somente se o arquivo a ser carregado foi produzido da digitaliza��o de um documento em papel.';
    //Fim Msgs

    $selHipoteseLegal = MdPetHipoteseLegalINT::montarSelectHipoteseLegal($booOptionsOnly = true);
    
//RN Tamanho Maximo Arquivo
    $tamanhoMaximo = MdPetIntercorrenteINT::tamanhoMaximoArquivoPermitido();
    //Fim RN

    //RN Extensoes Permitidas
    $extensoesPermitidas = MdPetExtensoesArquivoINT::recuperaExtensoes(null, null, null, "N");
    //Fim RN

    //RN para exibir Hipotese Legal

    $objInfraParametroDTO = new InfraParametroDTO();
    $objMdPetParametroRN = new MdPetParametroRN();
    $objInfraParametroDTO->retTodos();
    $objInfraParametroDTO->setStrNome('SEI_HABILITAR_HIPOTESE_LEGAL');
    $objInfraParametroDTO = $objMdPetParametroRN->consultar($objInfraParametroDTO);
    $valorParametroHipoteseLegal = $objInfraParametroDTO->getStrValor();

    if ($valorParametroHipoteseLegal=='0') {
    	$exibirHipoteseLegal = false;
    }else{
    	$exibirHipoteseLegal = true;
    }
    //Fim RN

    // Forcar o Nivel de Acesso parametrizado
    $nivelAcessoDoc = MdPetForcarNivelAcessoDocINT::getDadosForcarNivelAcessoDoc($tipoPeticionamento = 'I');
