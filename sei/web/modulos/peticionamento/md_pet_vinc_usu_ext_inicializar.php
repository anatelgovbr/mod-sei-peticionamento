<?php
//URL's
$strUrlAcaoForm = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']);
    
$strUrlFechar = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinculacao_listar');
    
$strUrlAjaxMontarSelectTipoDocumento = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=montar_select_tipo_documento');
    
$strUrlAjaxMontarHipoteseLegal = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=montar_select_hipotese_legal');
    
$strUrlUploadArquivo = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_resposta_upload_anexo');

//Msgs dos Tooltips de Ajuda
$strMsgTooltipFormato = 'Selecione a opção "Nato-digital" se o arquivo a ser carregado foi criado originalmente em meio eletrônico.\n\n\n Selecione a opção "Digitalizado" somente se o arquivo a ser carregado foi produzido da digitalização de um documento em papel.';

$strMsgTooltipTipoDocumento = 'Selecione o Tipo de Documento que melhor identifique o documento a ser carregado e complemente o Tipo no campo ao lado.';

$strMsgTooltipComplementoTipoDocumento = 'O Complemento do Tipo de Documento é o texto que completa a identificação do documento a ser carregado, adicionando ao nome do Tipo o texto que for digitado no referido campo (Tipo "Recurso" e Complemento "de 1ª Instância" identificará o documento como "Recurso de 1ª Instância").\n\n\n Exemplos: O Complemento do Tipo "Nota" pode ser "Fiscal Eletrônica" ou "Fiscal nº 75/2016". O Complemento do Tipo "Comprovante" pode ser "de Pagamento" ou "de Endereço".';

$strMsgTooltipNivelAcesso = 'O Nível de Acesso que for indicado é de sua exclusiva responsabilidade e estará condicionado à análise por servidor público, que poderá, motivadamente, alterá-lo a qualquer momento sem necessidade de prévio aviso.\n\n\n Selecione “Público” se no teor do documento a ser carregado não existir informações restritas. Se no teor do documento existir informações restritas, selecione “Restrito”.';
$strMsgTooltipHipoteseLegal = 'Para o Nível de Acesso “Restrito” é obrigatória a indicação da Hipótese Legal correspondente à informação restrita constante no teor do documento a ser carregado, sendo de sua exclusiva responsabilidade a referida indicação. Em caso de dúvidas, pesquise sobre a legislação indicada entre parênteses em cada Hipótese listada.';

$strMsgTooltipTipoResposta = 'Este campo lista as Respostas possíveis para a presente Intimação Eletrônica e somente se ainda estiverem dentro do prazo.\n\n\nSe não listar Tipo de Resposta e ainda pretender protocolizar documentos neste processo, pode acessar o menu Peticionamento > Intercorrente.';
//Fim Msgs

//RN Tamanho Maximo Arquivo
$tamanhoMaximo = MdPetIntercorrenteINT::tamanhoMaximoArquivoPermitido();

//RN Extensoes Permitidas
$extensoesPermitidas = MdPetExtensoesArquivoINT::recuperaExtensoes(null, null, null, "N");
//Fim RN

//Geração do Captcha
$strCodigoParaGeracaoCaptcha = InfraCaptcha::obterCodigo();
PaginaSEIExterna::getInstance()->salvarCampo('captchaPeticionamentoRL', hash('SHA512',InfraCaptcha::gerar($strCodigoParaGeracaoCaptcha)));

$objMdPetVincRelSerieRN = new MdPetVincRelSerieRN();
$strDocsObrigatorios = $objMdPetVincRelSerieRN->buscarDocumentosObrigatorios();

$strLinkAjaxCidade = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=cidade_montar_select_id_cidade_nome');

$strPrimeiroItemValor     = '0';
$strPrimeiroItemDescricao = '&nbsp;';
$strValorItemSelecionado  = null;
$strEndereco              = '';
$strNumero                = '';
$cpfUsuarioExterno        = '';
$idUsuarioExterno         = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
$objMdPetIntAceiteRN      = new MdPetIntAceiteRN();
$objContatoUsuarioEx      = $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array($idUsuarioExterno, true));
$cpfUsuarioExterno        = !is_null($objContatoUsuarioEx) ? InfraUtil::formatarCpf($objContatoUsuarioEx->getDblCpf()) : '';

$hdnNumeroCnpj            = $_POST['hdnNumeroCnpj'];

//consultar orgão externo
$siglaOrgao = SessaoSEIExterna::getInstance()->getStrSiglaOrgaoUsuarioExterno();
$descricaoOrgao = SessaoSEIExterna::getInstance()->getStrDescricaoOrgaoUsuarioExterno();

$textoFormatadoDeclaracao = '<span style="font-weight: bold">Declaro </span>ser o Responsável Legal pela Pessoa Jurídica cujo CNPJ informei e que 
                concordo com os termos acima dispostos. Declaro ainda estar ciente de que o ato de inserir ou fazer 
                inserir declaração falsa ou diversa da que devia ser escrita é crime, conforme disposto no art. 299 do Código Penal Brasileiro.';

$textoDestaqueDeclaracao = '<span style="font-weight: bold">Declaro ser o Responsável Legal pela Pessoa Jurídica cujo CNPJ informei e que 
                concordo com os termos acima dispostos. Declaro ainda estar ciente de que o ato de inserir ou fazer 
                inserir declaração falsa ou diversa da que devia ser escrita é crime, conforme disposto no art. 299 do Código Penal Brasileiro.</span>';

if(!is_null($objContatoUsuarioEx)){

    $objVinculosExistentes = '';

    $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
    $objMdPetVincRepresentantDTO->setNumIdContato($objContatoUsuarioEx->getNumIdContato());
    $objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
    $objMdPetVincRepresentantDTO->setStrStaEstado([MdPetVincRepresentantRN::$RP_ATIVO, MdPetVincRepresentantRN::$RP_SUSPENSO], InfraDTO::$OPER_IN);
    $objMdPetVincRepresentantDTO->retStrCNPJ();
    $objMdPetVincRepresentantDTO->retStrStaEstado();
    $arrObjMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN)->listar($objMdPetVincRepresentantDTO);

    if(is_countable($arrObjMdPetVincRepresentantDTO)){
        $objVinculosExistentes = json_encode(InfraArray::converterArrInfraDTO($arrObjMdPetVincRepresentantDTO, 'StaEstado', 'CNPJ'));
    }

}
