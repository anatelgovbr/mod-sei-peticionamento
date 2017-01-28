<?php
    //INICIALIZACAO DE VARIAVEIS DA PAGINA
    $txtOrientacoes = "Este peticionamento serve para protocolizar documentos em processos já existentes. Condicionado ao número do processo a ser validado abaixo e parametrizações da administração sobre o Tipo de Processo correspondente, os documentos poderão ser incluídos diretamente no processo indicado ou protocolizado em processo novo apartado relacionado ao processo indicado.";

    $arrComandos   = array();
    $arrComandos[] = '<button type="button" accesskey="p" name="Peticionar" id="Peticionar" value="Peticionar" onclick="abrirPeticionar()" class="infraButton"><span class="infraTeclaAtalho">P</span>eticionar</button>';
    $arrComandos[] = '<button type="button" accesskey="C" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\'' . PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=usuario_externo_controle_acessos&id_orgao_acesso_externo=0')) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

    //Links de acesso
    $strLinkUploadArquivo                = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_upload_arquivo');
    $strUrlAjaxMontarSelectTipoDocumento = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=montar_select_tipo_documento');
    $strUrlAjaxMontarSelectNivelAcesso   = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=montar_select_nivel_acesso');
    $strUrlAjaxCriterioIntercorrente     = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=verificar_criterio_intercorrente');
    $strUrlAjaxMontarHipoteseLegal       = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=montar_select_hipotese_legal');
    //Fim Links

    //Msgs dos Tooltips de Ajuda
    $strMsgTooltipFormato                  = 'Selecione a opção “Nato-digital” se o arquivo a ser carregado foi criado originalmente em meio eletrônico.\n\n\n Selecione a opção “Digitalizado” somente se o arquivo a ser carregado foi produzido da digitalização de um documento em papel.';
    $strMsgTooltipTipoDocumento            = 'Selecione o Tipo de Documento que melhor identifique o documento a ser carregado e complemente o Tipo no campo ao lado.';
    $strMsgTooltipComplementoTipoDocumento = 'O Complemento do Tipo de Documento é o texto que completa a identificação do documento a ser carregado, adicionando ao nome do Tipo o texto que for digitado no referido campo (Tipo “Recurso” e Complemento “de 1ª Instância” identificará o documento como “Recurso de 1ª Instância”).\n\n\n Exemplos: O Complemento do Tipo “Nota” pode ser “Fiscal Eletrônica” ou “Fiscal nº 75/2016”. O Complemento do Tipo “Comprovante” pode ser “de Pagamento” ou “de Endereço”.';
    //Fim Msgs


    //RN Tamanho Maximo Arquivo
    $tamanhoMaximo = MdPetIntercorrenteINT::tamanhoMaximoArquivoPermitido();
    //Fim RN

    //RN Extensoes Permitidas
    $extensoesPermitidas = GerirExtensoesArquivoPeticionamentoINT::recuperaExtensoes(null, null, null, "N");
    //Fim RN

    //RN para exibir Hipotese Legal
    $exibirHipoteseLegal = true;
    //@todo Não estou conseguindo instanciar aqui
    //$exibirHipoteseLegal = MdPetIntercorrenteINT::verificarHipoteseLegal();
    //Fim RN