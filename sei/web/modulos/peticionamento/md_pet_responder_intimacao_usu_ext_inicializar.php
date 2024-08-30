<?php
/**
 * ANATEL
 *
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 */
 
//URL's
$strUrlAcaoForm = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']);
    
$strUrlFechar = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=usuario_externo_controle_acessos');
    
$strUrlAjaxMontarSelectTipoDocumento = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=montar_select_tipo_documento');
    
$strUrlAjaxMontarHipoteseLegal = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=montar_select_hipotese_legal');
    
$strUrlUploadArquivo = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_resposta_upload_anexo');

//Msgs dos Tooltips de Ajuda
$strMsgTooltipFormato = 'Selecione a opção "Nato-digital" se o arquivo a ser carregado foi criado originalmente em meio eletrônico.\n\n\n Selecione a opção "Digitalizado" somente se o arquivo a ser carregado foi produzido da digitalização de um documento em papel.';

$strMsgTooltipTipoDocumento = 'Selecione o Tipo de Documento que melhor identifique o documento a ser carregado e complemente o Tipo no campo ao lado.';

$strMsgTooltipComplementoTipoDocumento = 'O Complemento do Tipo de Documento é o texto que completa a identificação do documento a ser carregado, adicionando ao nome do Tipo o texto que for digitado no referido campo (Tipo "Recurso" e Complemento "de 1ª Instância" identificará o documento como "Recurso de 1ª Instância").\n\n\n Exemplos: O Complemento do Tipo "Nota" pode ser "Fiscal Eletrônica" ou "Fiscal nº 75/2016". O Complemento do Tipo "Comprovante" pode ser "de Pagamento" ou "de Endereço".';

$strMsgTooltipNivelAcesso = 'O Nível de Acesso que for indicado é de sua exclusiva responsabilidade e estará condicionado à análise por servidor público, que poderá alterá-lo a qualquer momento sem necessidade de prévio aviso.\n\n\n Selecione "Público" se no teor do documento a ser carregado não existir informações restritas. Se no teor do documento existir informações restritas, selecione "Restrito".';
$strMsgTooltipHipoteseLegal = 'Para o Nível de Acesso "Restrito" é obrigatória a indicação da Hipótese Legal correspondente à informação restrita constante no teor do documento a ser carregado, sendo de sua exclusiva responsabilidade a referida indicação. Em caso de dúvidas, pesquise sobre a legislação indicada entre parênteses em cada Hipótese listada.';

$strMsgTooltipTipoResposta = 'Este campo lista as Respostas possíveis para a presente Intimação Eletrônica e somente se ainda estiverem dentro do prazo.\n\n\nSe não listar Tipo de Resposta e ainda pretender protocolizar documentos neste processo, pode acessar o menu Peticionamento > Intercorrente.';
//Fim Msgs

//RN Tamanho Máximo Arquivo
$tamanhoMaximo = MdPetIntercorrenteINT::tamanhoMaximoArquivoPermitido();

//RN Extensões Permitidas
$extensoesPermitidas = MdPetExtensoesArquivoINT::recuperaExtensoes(null, null, null, "N");
//Fim RN

// Forçar o Nível de Acesso parametrizado
$nivelAcessoDoc = MdPetForcarNivelAcessoDocINT::getDadosForcarNivelAcessoDoc($tipoPeticionamento = 'I');
