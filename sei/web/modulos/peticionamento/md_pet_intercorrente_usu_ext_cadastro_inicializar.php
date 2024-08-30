<?php

    //INICIALIZAÇÃO DE VARIÁVEIS DA PÁGINA
    $txtOrientacoes = "Este peticionamento serve para protocolizar documentos em processos já existentes. Condicionado ao número do processo e parametrizações da administração sobre o Tipo de Processo correspondente, os documentos poderão ser incluídos diretamente no processo indicado ou em processo novo relacionado.";

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
    $strMsgTooltipComplementoTipoDocumento			= 'O Complemento do Tipo de Documento é o texto que completa a identificação do documento a ser carregado, adicionando ao nome do Tipo o texto que for digitado no referido campo (Tipo "Recurso" e Complemento "de 1ª Instância" identificará o documento como "Recurso de 1ª Instância").\n\n\n Exemplos: O Complemento do Tipo "Nota" pode ser "Fiscal Eletrônica" ou "Fiscal nº 75/2016". O Complemento do Tipo "Comprovante" pode ser "de Pagamento" ou "de Endereço".';
    $strMsgTooltipNivelAcesso						= 'O Nível de Acesso que for indicado é de sua exclusiva responsabilidade e estará condicionado à análise por servidor público, que poderá alterá-lo a qualquer momento sem necessidade de prévio aviso.\n\n\n Selecione "Público" se no teor do documento a ser carregado não existir informações restritas. Se no teor do documento existir informações restritas, selecione "Restrito".';
    $strMsgTooltipHipoteseLegal						= 'Para o Nível de Acesso "Restrito" é obrigatória a indicação da Hipótese Legal correspondente à informação restrita constante no teor do documento a ser carregado, sendo de sua exclusiva responsabilidade a referida indicação. Em caso de dúvidas, pesquise sobre a legislação indicada entre parênteses em cada Hipótese listada.';
    $strMsgTooltipNivelAcessoPadraoPreDefinido		= 'Para o Número de Processo indicado o Nível de Acesso é previamente definido.';
    $strMsgTooltipHipoteseLegalPadraoPreDefinido	= 'Para o Número de Processo indicado o Nível de Acesso é previamente definido como "Restrito" e, assim, a Hipótese Legal também é previamente definida.';
    $strMsgTooltipFormato							= 'Selecione a opção "Nato-digital" se o arquivo a ser carregado foi criado originalmente em meio eletrônico.\n\n\n Selecione a opção "Digitalizado" somente se o arquivo a ser carregado foi produzido da digitalização de um documento em papel.';
    //Fim Msgs

    $selHipoteseLegal = MdPetHipoteseLegalINT::montarSelectHipoteseLegal($booOptionsOnly = true);
    
	//RN Tamanho Máximo Arquivo
    $tamanhoMaximo = MdPetIntercorrenteINT::tamanhoMaximoArquivoPermitido();
    //Fim RN

    //RN Extensões Permitidas
    $extensoesPermitidas = MdPetExtensoesArquivoINT::recuperaExtensoes(null, null, null, "N");
    //Fim RN

    //RN para exibir Hipótese Legal

    $objInfraParametroDTO = new InfraParametroDTO();
    $objInfraParametroDTO->retTodos();
    $objInfraParametroDTO->setStrNome('SEI_HABILITAR_HIPOTESE_LEGAL');
    $objInfraParametroDTO = (new MdPetParametroRN())->consultar($objInfraParametroDTO);
    $valorParametroHipoteseLegal = $objInfraParametroDTO->getStrValor();

    $exibirHipoteseLegal = ($valorParametroHipoteseLegal=='0') ? false : true;
    //Fim RN

    // Forcar o Nível de Acesso parametrizado
    $nivelAcessoDoc = MdPetForcarNivelAcessoDocINT::getDadosForcarNivelAcessoDoc($tipoPeticionamento = 'I');
