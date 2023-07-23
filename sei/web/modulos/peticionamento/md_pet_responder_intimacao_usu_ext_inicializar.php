<?php
//URL's
$strUrlAcaoForm = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']);
    
$strUrlFechar = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=usuario_externo_controle_acessos');
    
$strUrlAjaxMontarSelectTipoDocumento = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=montar_select_tipo_documento');
    
$strUrlAjaxMontarHipoteseLegal = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=montar_select_hipotese_legal');
    
$strUrlUploadArquivo = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_resposta_upload_anexo');

//Msgs dos Tooltips de Ajuda
$strMsgTooltipFormato = 'Selecione a op��o "Nato-digital" se o arquivo a ser carregado foi criado originalmente em meio eletr�nico.\n\n\n Selecione a op��o "Digitalizado" somente se o arquivo a ser carregado foi produzido da digitaliza��o de um documento em papel.';

$strMsgTooltipTipoDocumento = 'Selecione o Tipo de Documento que melhor identifique o documento a ser carregado e complemente o Tipo no campo ao lado.';

$strMsgTooltipComplementoTipoDocumento = 'O Complemento do Tipo de Documento � o texto que completa a identifica��o do documento a ser carregado, adicionando ao nome do Tipo o texto que for digitado no referido campo (Tipo "Recurso" e Complemento "de 1� Inst�ncia" identificar� o documento como "Recurso de 1� Inst�ncia").\n\n\n Exemplos: O Complemento do Tipo "Nota" pode ser "Fiscal Eletr�nica" ou "Fiscal n� 75/2016". O Complemento do Tipo "Comprovante" pode ser "de Pagamento" ou "de Endere�o".';

$strMsgTooltipNivelAcesso = 'O N�vel de Acesso que for indicado � de sua exclusiva responsabilidade e estar� condicionado � an�lise por servidor p�blico, que poder� alter�-lo a qualquer momento sem necessidade de pr�vio aviso.\n\n\n Selecione �P�blico� se no teor do documento a ser carregado n�o existir informa��es restritas. Se no teor do documento existir informa��es restritas, selecione �Restrito�.';
$strMsgTooltipHipoteseLegal = 'Para o N�vel de Acesso �Restrito� � obrigat�ria a indica��o da Hip�tese Legal correspondente � informa��o restrita constante no teor do documento a ser carregado, sendo de sua exclusiva responsabilidade a referida indica��o. Em caso de d�vidas, pesquise sobre a legisla��o indicada entre par�nteses em cada Hip�tese listada.';

$strMsgTooltipTipoResposta = 'Este campo lista as Respostas poss�veis para a presente Intima��o Eletr�nica e somente se ainda estiverem dentro do prazo.\n\n\nSe n�o listar Tipo de Resposta e ainda pretender protocolizar documentos neste processo, pode acessar o menu Peticionamento > Intercorrente.';
//Fim Msgs

//RN Tamanho Maximo Arquivo
$tamanhoMaximo = MdPetIntercorrenteINT::tamanhoMaximoArquivoPermitido();

//RN Extensoes Permitidas
$extensoesPermitidas = MdPetExtensoesArquivoINT::recuperaExtensoes(null, null, null, "N");
//Fim RN

// Forcar o Nivel de Acesso parametrizado
$nivelAcessoDoc = MdPetForcarNivelAcessoDocINT::getDadosForcarNivelAcessoDoc($tipoPeticionamento = 'I');
