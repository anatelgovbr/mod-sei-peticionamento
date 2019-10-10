<?php
/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 08/02/2018
 * Time: 11:16
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();
    SessaoSEI::getInstance()->validarLink();
    //SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    switch ($_GET['acao']) {
        case 'md_pet_vinc_usu_ext_pe_listar':
            $strTitulo = 'Procurações Eletrônicas';
            break;
    }

    PaginaSEIExterna::getInstance()->salvarCamposPost(array('txtCnpj', 'txtRazaoSocial', 'txtCpf', 'txtNomeProcurador', 'slTipoViculo', 'slSituacao'));
    $strLinkMotivoRevogar = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=processo_eletronico_responder_motivo_revogar');
    $strLinkMotivoRenunciar = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=processo_eletronico_responder_motivo_renunciar');
    $strLinkConsultaDocumento = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_doc_procuracao_consultar&id_documento=');

    $strCnpj = trim(PaginaSEIExterna::getInstance()->recuperarCampo('txtCnpj'));
    $strCnpj = InfraUtil::retirarFormatacao($strCnpj);

    if ($strCnpj){
        $intCnpj = intval($strCnpj);
    }
    
    $strCpf = trim(PaginaSEIExterna::getInstance()->recuperarCampo('txtCpf'));
    $strCpf = InfraUtil::retirarFormatacao($strCpf);

    if ($strCpf){
        $intCpf = intval($strCpf);
    }

    $strRazaoSocial = trim(PaginaSEIExterna::getInstance()->recuperarCampo('txtRazaoSocial'));
    $strNome = trim(PaginaSEIExterna::getInstance()->recuperarCampo('txtNomeProcurador'));
    $strTipoViculo = trim(PaginaSEIExterna::getInstance()->recuperarCampo('slTipoViculo'));
    $strSituacao = trim(PaginaSEIExterna::getInstance()->recuperarCampo('slSituacao'));

    $idUsuarioExterno = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
    $usuarioDTO = new UsuarioDTO();
    $usuarioRN = new UsuarioRN();
    $usuarioDTO->retNumIdContato();
    $usuarioDTO->setNumIdUsuario($idUsuarioExterno);
    $contatoExterno = $usuarioRN->consultarRN0489($usuarioDTO);
    $idContatoExterno = $contatoExterno->getNumIdContato();

    $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();

    //Suspenso
    $objMdPetVincRepresentantSuspensoDTO = new MdPetVincRepresentantDTO();

    if ($intCpf > 0) {
        $objMdPetVincRepresentantSuspensoDTO->setStrCpfProcurador('%'.$intCpf.'%',InfraDTO::$OPER_LIKE);
    }

    if ($strNome != '') {
        $objMdPetVincRepresentantSuspensoDTO->setStrNomeProcurador('%'.$strNome.'%',InfraDTO::$OPER_LIKE);
    }

    if ($strRazaoSocial != '') {
        $objMdPetVincRepresentantSuspensoDTO->setStrRazaoSocialNomeVinc('%'.$strRazaoSocial.'%',InfraDTO::$OPER_LIKE);
    }

    if ($intCnpj > 0) {
        $objMdPetVincRepresentantSuspensoDTO->setStrCNPJ('%'.$intCnpj.'%',InfraDTO::$OPER_LIKE);
    }

    if($strTipoViculo != '' && $strTipoViculo != 'null'){
        $objMdPetVincRepresentantSuspensoDTO->setStrTipoRepresentante($strTipoViculo);
    }

    if ($strSituacao != ''){
        $objMdPetVincRepresentantSuspensoDTO->setStrStaEstado($strSituacao);
    }

    $objMdPetVincRepresentantSuspensoDTO->retNumIdMdPetVinculoRepresent();
    $objMdPetVincRepresentantSuspensoDTO->retStrStaEstado();
    
    $objMdPetVincRepresentantSuspensoDTO->adicionarCriterio(array('IdContato','IdContatoOutorg'),
            array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
            array($idContatoExterno,$idContatoExterno),
            array(InfraDTO::$OPER_LOGICO_OR));

    $objMdPetVincRepresentantSuspensoDTO->adicionarCriterio(array('TipoRepresentante', 'StaEstado'),
            array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
            array(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL, MdPetVincRepresentantRN::$RP_SUSPENSO),
            array(InfraDTO::$OPER_LOGICO_AND));

    $arrObjMdPetVincRepresentantSuspensoDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantSuspensoDTO);

    //Recuperando os documentos da suspensão
    $staEstadoRepresentantSuspenso = '';
    if($arrObjMdPetVincRepresentantSuspensoDTO) {
        $staEstadoRepresentantSuspenso = current($arrObjMdPetVincRepresentantSuspensoDTO)->getStrStaEstado();
    }
    $arrIdVincRepresentantSuspenso = InfraArray::converterArrInfraDTO($arrObjMdPetVincRepresentantSuspensoDTO, 'IdMdPetVinculoRepresent');
    // Suspenso - fim

    $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
    $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();

    if ($intCpf > 0) {
        $objMdPetVincRepresentantDTO->setStrCpfProcurador('%'.$intCpf.'%',InfraDTO::$OPER_LIKE);
    }

    if ($strNome != '') {
        $objMdPetVincRepresentantDTO->setStrNomeProcurador('%'.$strNome.'%',InfraDTO::$OPER_LIKE);
    }

    if ($strRazaoSocial != '') {
        $objMdPetVincRepresentantDTO->setStrRazaoSocialNomeVinc('%'.$strRazaoSocial.'%',InfraDTO::$OPER_LIKE);
    }

    if ($intCnpj > 0) {
        $objMdPetVincRepresentantDTO->setStrCNPJ('%'.$intCnpj.'%',InfraDTO::$OPER_LIKE);
    }

    if($strTipoViculo != ''){
        $objMdPetVincRepresentantDTO->setStrTipoRepresentante($strTipoViculo);
    }else{
        $strTipoViculo = '';
    }

    if($strSituacao != ''){
        $objMdPetVincRepresentantDTO->setStrStaEstado($strSituacao);
    }

    $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
    $objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
    $objMdPetVincRepresentantDTO->retStrTipoRepresentante();

    $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
    $objMdPetVincRepresentantDTO->retStrCNPJ();
    $objMdPetVincRepresentantDTO->retStrStaEstado();

    $objMdPetVincRepresentantDTO->retNumIdContato();
    $objMdPetVincRepresentantDTO->retNumIdContatoOutorg();

    $objMdPetVincRepresentantDTO->retStrCpfProcurador();
    $objMdPetVincRepresentantDTO->retStrNomeProcurador();
    $objMdPetVincRepresentantDTO->retDblIdProcedimentoVinculo();

    $objMdPetVincRepresentantDTO->adicionarCriterio(array('IdContato','IdContatoOutorg'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array($idContatoExterno,$idContatoExterno),
        array(InfraDTO::$OPER_LOGICO_OR));

    $objMdPetVincRepresentantDTO->adicionarCriterio(array('TipoRepresentante'),
        array(InfraDTO::$OPER_NOT_IN),
        array(array(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL)));

    PaginaSEIExterna::getInstance()->prepararOrdenacao($objMdPetVincRepresentantDTO, 'CpfProcurador', InfraDTO::$TIPO_ORDENACAO_ASC);
    PaginaSEIExterna::getInstance()->prepararPaginacao($objMdPetVincRepresentantDTO);

    $arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);

    //Recuperando os documentos da procuração
    $arrIdVincRepresentant = InfraArray::converterArrInfraDTO($arrObjMdPetVincRepresentantDTO, 'IdMdPetVinculoRepresent');

    PaginaSEIExterna::getInstance()->processarPaginacao($objMdPetVincRepresentantDTO);

    $strCnpj = trim(PaginaSEIExterna::getInstance()->recuperarCampo('txtCnpj'));
    $strCpf = trim(PaginaSEIExterna::getInstance()->recuperarCampo('txtCpf'));

} catch (Exception $e) {
    PaginaSEIExterna::getInstance()->processarExcecao($e);
}

if(count($arrIdVincRepresentant)>0) {

    $arrIdVincRepresentant = array_merge($arrIdVincRepresentant, $arrIdVincRepresentantSuspenso);

    $objMdPetVincDocumentoRN = new MdPetVincDocumentoRN();
    $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
    $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent($arrIdVincRepresentant, InfraDTO::$OPER_IN);

    $arrIdsSeries = (new MdPetVinculoUsuExtRN)->retornaSeriesInfraParamentro($idSerieFormulario);

    $objMdPetVincDocumentoDTO->setStrTipoDocumento(array(MdPetVincDocumentoRN::$TP_PROTOCOLO_PROCURACAO_ESPECIAL), InfraDTO::$OPER_IN);

    $objMdPetVincDocumentoDTO->retDblIdDocumento();
    $objMdPetVincDocumentoDTO->retStrProtocoloFormatadoProtocolo();
    $objMdPetVincDocumentoDTO->retNumIdSerie();
    $objMdPetVincDocumentoDTO->retNumIdMdPetVinculo();
    $objMdPetVincDocumentoDTO->retStrNomeSerieProtocolo();

    $objMdPetVincDocumentoDTO->retNumIdMdPetVinculoRepresent();

    $arrObjMdPetVincDocumentoDTO = $objMdPetVincDocumentoRN->listar($objMdPetVincDocumentoDTO);

    $arrDocumento = "";
    foreach ($arrObjMdPetVincDocumentoDTO as $objMdPetVincDocumentoDTO) {
        $arrDocumento[] = $objMdPetVincDocumentoDTO;
    }
}

$numRegistros = count($arrObjMdPetVincRepresentantDTO);
if ($numRegistros > 0) {

    $strResultado = '';
    $strSumarioTabela = 'Procurações Eletrônicas';
    $strCaptionTabela = 'Procurações Eletrônicas';
    $strResultado .= '<table width="99%" class="infraTable" summary="' . $strSumarioTabela . '">';
    $strResultado .= '<caption class="infraCaption">' . PaginaSEIExterna::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';

    $strResultado .= '<tr>';
    //$strResultado .= '<th class="infraTh" width="1%">' . PaginaSEIExterna::getInstance()->getThCheck() . '</th>' . "\n";
    //$strResultado .= '<th class="infraTh" width="13%">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'N° do Documento', 'ProtocoloFormatado', $arrObjMdPetVincRepresentantDTO) . '</th>';
    $strResultado .= '<th class="infraTh" style="width:155px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'CPF / CNPJ Outorgante', 'CNPJ', $arrObjMdPetVincRepresentantDTO) . '</th>';
    $strResultado .= '<th class="infraTh">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Nome / Razão Social do Outorgante', 'RazaoSocialNomeVinc', $arrObjMdPetVincRepresentantDTO) . '</th>';
    $strResultado .= '<th class="infraTh" style="width:120px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'CPF Outorgado', 'CpfProcurador', $arrObjMdPetVincRepresentantDTO) . '</th>';
    $strResultado .= '<th class="infraTh" style="width:150px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Nome do Outorgado', 'NomeProcurador', $arrObjMdPetVincRepresentantDTO) . '</th>';
    $strResultado .= '<th class="infraTh" style="width:120px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Tipo de Procuração', 'TipoRepresentante', $arrObjMdPetVincRepresentantDTO) . '</th>';
    $strResultado .= '<th class="infraTh" style="width:80px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Situação', 'StaEstado', $arrObjMdPetVincRepresentantDTO) . '</th>';    
    $strResultado .= '<th class="infraTh" style="width:50px">Ações</th>';
    $strResultado .= '</tr>';

    $arrSelectTipoVinculo = array();
    //Populando obj para tabela
    for ($i = 0; $i < $numRegistros; $i++)
    {
        $arrSerieSituacao = MdPetVincRepresentantDTO::getArrSerieSituacao(
            $arrObjMdPetVincRepresentantDTO[$i]->getStrStaEstado()
        );
        
        $strLabelSituacao = $arrSerieSituacao['strSituacao'];
        $idSerieFormulario = $arrSerieSituacao['numSerie'];

        if($arrIdsSeries) {
            $idSerieFormulario = $arrIdsSeries;
        }
        //Buscar documento da procuração
        foreach($arrDocumento as $chave => $objMdPetVincDocumentoDTO){

            if($objMdPetVincDocumentoDTO->getNumIdMdPetVinculoRepresent() == $arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent() ){
                $idVinculacao = $objMdPetVincDocumentoDTO->getNumIdMdPetVinculo();
                $idDocumentoFormatado = $objMdPetVincDocumentoDTO->getStrProtocoloFormatadoProtocolo();
                $idDocumento = $objMdPetVincDocumentoDTO->getDblIdDocumento();
            }
        }
        
        if (!in_array($arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante(), $arrSelectTipoVinculo)){
            $arrSelectTipoVinculo[$arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante()] = $arrObjMdPetVincRepresentantDTO[$i]->getStrNomeTipoRepresentante();
        }
        $strResultado .= '<tr class="infraTrClara" id="tr-'.$i.'">';
        // $strResultado .= '<td valign="top">' . PaginaSEIExterna::getInstance()->getTrCheck($i, $arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent(), $idDocumento) . '</td>';
        //$strResultado .= '<td>' . $idDocumentoFormatado . '</td>';
        $strResultado .= '<td>' . InfraUtil::formatarCnpj($arrObjMdPetVincRepresentantDTO[$i]->getStrCNPJ()) . '</td>';
        $strResultado .= '<td>' . PaginaSEI::tratarHTML($arrObjMdPetVincRepresentantDTO[$i]->getStrRazaoSocialNomeVinc()) . '</td>';
        $strResultado .= '<td>' . InfraUtil::formatarCpf($arrObjMdPetVincRepresentantDTO[$i]->getStrCpfProcurador()) . '</td>';
        $strResultado .= '<td>' . $arrObjMdPetVincRepresentantDTO[$i]->getStrNomeProcurador() . '</td>';
        $strResultado .= '<td>' . $arrObjMdPetVincRepresentantDTO[$i]->getStrNomeTipoRepresentante() /*$strTipoRepresentante*/ . '</td>';
        $strResultado .= '<td>' . $strLabelSituacao . '</td>';

        //Acesso Externo
        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->setNumIdUsuario(
            SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()
        );
        $objUsuarioDTO->retNumIdContato();

        $objUsuarioRN = new UsuarioRN();
        $arrObjUsuarioDTO = $objUsuarioRN->listarRN0490($objUsuarioDTO);
        $idContato = $arrObjMdPetVincRepresentantDTO[$i]->getNumIdContato();

        if (count($arrObjUsuarioDTO)>0){
            $idContato = $arrObjUsuarioDTO[0]->getNumIdContato();
        }

        $idProcedimento = $arrObjMdPetVincRepresentantDTO[$i]->getDblIdProcedimentoVinculo();
        $objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();
        $idAcessoExterno = $objMdPetAcessoExternoRN->_getUltimaConcessaoAcessoExternoModulo($idProcedimento, $idContato, true);
        //Acesso Externo - fim

        if ($idAcessoExterno!='' and $idDocumento!=''){
            SessaoSEIExterna::getInstance()->configurarAcessoExterno($idAcessoExterno);
            $strLinkConsultaDocumento = SessaoSEIExterna::getInstance()->assinarLink('documento_consulta_externa.php?id_acesso_externo=' . $idAcessoExterno . '&id_documento=' . $idDocumento);

            $iconeConsulta = 'Consultar Procuração';

            $iconeConsulta = '<img style="width:16px;"  src="modulos/peticionamento/imagens/visualizar_procuracao_especial.png" title="' . $iconeConsulta . '" alt="' . $iconeConsulta . '" class="infraImg" />';

            $acaoConsulta = '<a target="_blank" href="'.$strLinkConsultaDocumento.'">'.$iconeConsulta.'</a>';
            SessaoSEIExterna::getInstance()->configurarAcessoExterno(null);
        }

        $iconeAcao='';
        
        
        if($arrObjMdPetVincRepresentantDTO[$i]->getStrStaEstado() == MdPetVincRepresentantRN::$RP_ATIVO) {
            if ($arrObjMdPetVincRepresentantDTO[$i]->getNumIdContato() == $idContatoExterno) {
                $iconeAcao = '<a href="javascript:;" onclick="desvincularProcuracao(\'' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=processo_eletronico_responder_motivo_renunciar&tpDocumento=renunciar&id_procedimento=' . $arrObjMdPetVincRepresentantDTO[$i]->getDblIdProcedimentoVinculo() . '&id_documento=' . $idDocumento . '&cpf=' . $arrObjMdPetVincRepresentantDTO[$i]->getStrCpfProcurador() . '&id_vinculacao=' . $arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent()) . '\',\'' .$i.'\')"><img style="width:16px;"  src="modulos/peticionamento/imagens/renunciar_procuracao.png" title="Renunciar Procuração" alt="Renunciar Procuração" class="infraImg" /></a>';
            } else if ($staEstadoRepresentantSuspenso != MdPetVincRepresentantRN::$RP_SUSPENSO) {
                $iconeAcao = '<a href="javascript:;" onclick="desvincularProcuracao(\'' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=processo_eletronico_responder_motivo_revogar&tpDocumento=revogar&id_procedimento=' . $arrObjMdPetVincRepresentantDTO[$i]->getDblIdProcedimentoVinculo() . '&id_documento=' . $idDocumento . '&cpf=' . $arrObjMdPetVincRepresentantDTO[$i]->getStrCpfProcurador() . '&id_vinculacao=' . $arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent()) . '\',\'' .$i.'\')"><img style="width:16px;"  src="modulos/peticionamento/imagens/revogar_renunciar_procuracao.png" title="Revogar Procuração" alt="Revogar Procuração" class="infraImg" /></a>';
            }
        }

        $strResultado .= '<td align="center">' . $acaoConsulta . $iconeAcao.'</td>';
        $strResultado .= '</tr>';
    }
    $strResultado .= '</table>';
}

//Responsável Legal
$objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
$objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();

$objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
$objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
$objMdPetVincRepresentantDTO->setNumIdContato($idContatoExterno);
$objMdPetVincRepresentantDTO->setStrSinAtivo('S');
$objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);

$arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
$bolAcaoCadastrar = false;

if (count($arrObjMdPetVincRepresentantDTO)>0){
    $bolAcaoCadastrar = true;
}

$arrComandos = array();
$arrComandos[] = '<button type="submit" accesskey="p" id="btnPesquisar" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
if ($bolAcaoCadastrar){
    $arrComandos[] = '<button type="button" accesskey="N" id="btnNova" value="Nova Procuração Eletrônica" onclick="location.href=\'' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_pe_cadastrar&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">N</span>ova Procuração Eletrônica</button>';
}
$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" onclick="location.href=\'' . PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=usuario_externo_controle_acessos&id_orgao_acesso_externo=0')) . '\';" class="infraButton" >Fe<span class="infraTeclaAtalho">c</span>har</button>';

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(
    PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo
);
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
if (0){ ?>
<script>
<?php } ?>
    function desvincularProcuracao(link, idTd)
    {
        $('#tr-'+idTd).css('backgroundColor','#efff00');
        infraAbrirJanela(link, 'janelaDesvinculo', 700, 220, '',true);
        return;
    }
    
    function infraMonitorarModal(){
      if (infraJanelaModal.closed){
        infraFecharJanelaModal(); 
        $('.infraTrClara').css('backgroundColor','#FFFFFF');
      }
    }
    
    function inicializar()
    {
        infraEfeitoTabelas();
    }

    function controlarCpfCnpj(objeto)
    {
        var valor = $.trim(objeto.value.replace(/\D/g,""));
        if(valor.length <= 11){
            var novoValor = maskCPF($.trim(objeto.value));
            objeto.value = novoValor;            
        }else{
            var novoValor = maskCNPJ(valor);
            objeto.value = novoValor;            
        }
    }
    
    function validaCpfCnpjOutorgante(objeto)
    {
        var erro = false;
        var valor = $.trim(objeto.value.replace(/\D/g,""));

        if(valor.length == 11 || valor.length == 14){
            if(valor.length == 11 && !infraValidarCpf(valor)) {
                erro = true;
            }

            if(valor.length == 14 && !infraValidarCnpj(valor)) {
                erro = true;
            }
        }else{
            erro = true;
        }

        if(erro){
            alert('Informe um CPF/CNPJ completo ou válido para realizar a pesquisa.');
            document.getElementById('txtCnpj').value = '';
        }
    }

    function infraMascaraCPFProcurador(objeto)
    {
        var novoValor = maskCPF($.trim(objeto.value));
        objeto.value = novoValor;
    }

    function validaCpfProcurador(objeto)
    {
        var erro = false;
        var valor = $.trim(objeto.value.replace(/\D/g,""));

        if(valor.length == 11){
            if(!infraValidarCpf(valor)) {
                erro = true;
            }            
        }else{
            erro = true;
        }   
        
        if(erro){
            alert('Informe o CPF do outorgado completo ou válido para realizar a pesquisa.');
            document.getElementById('txtCpf').value = '';            
        }
    }

    function maskCPF(cpf)
    {
        cpf=cpf.replace(/\D/g,"");
        cpf=cpf.replace(/(\d{3})(\d)/,"$1.$2");
        cpf=cpf.replace(/(\d{3})(\d)/,"$1.$2");
        cpf=cpf.replace(/(\d{3})(\d{1,2})$/,"$1-$2");

        return cpf;
    }

    function maskCNPJ(cnpj)
    {
        cnpj = cnpj.replace( /\D/g , ""); //Remove tudo o que não é dígito
        cnpj = cnpj.replace( /^(\d{2})(\d)/ , "$1.$2"); //Coloca ponto entre o segundo e o terceiro dígitos
        cnpj = cnpj.replace( /^(\d{2})\.(\d{3})(\d)/ , "$1.$2.$3"); //Coloca ponto entre o quinto e o sexto dígitos
        cnpj = cnpj.replace( /\.(\d{3})(\d)/ , ".$1/$2"); //Coloca uma barra entre o oitavo e o nono dígitos
        cnpj = cnpj.replace( /(\d{4})(\d)/ , "$1-$2"); //Coloca um hífen depois do bloco de quatro dígitos

        return cnpj;
    }
<?php if(0){ ?>
</script>
<?php } 
    PaginaSEIExterna::getInstance()->fecharJavaScript();
    PaginaSEIExterna::getInstance()->fecharHead();
    PaginaSEIExterna::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
<style type="text/css">

#container{
  width: 100%;
}
.clear {
  clear: both;
}

.bloco {
  float: left;
  margin-top: 1%;
  margin-right: 1%;
}

label[for^=txt] {
  display: block;
  white-space: nowrap;
}
label[for^=s] {
  display: block;
  white-space: nowrap;
}

#txtCnpj{
  width:98%;
}
#txtRazaoSocial{
  width:98%;
}
#txtCpf{
  width:98%;
}
#txtNomeProcurador{
  width:96%;
}
#slTipoViculo{
  width:100%;
}
#slSituacao{
  width:98%;
}
</style>

<form id="frmPesquisa" method="post"
      action="<?= SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
<?
    PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
?>
<!-- div class="container" -->
    <div class="bloco" style="min-width:150px; width:15%">
        <label id="lblTxtCnpj"
               for="txtCnpj" style="minwidth:10%; width:10%"
               class="infraLabelOpcional">CPF/CNPJ do Outorgante:</label>
        <input type="text"
               id="txtCnpj"
               name="txtCnpj"
               class="infraText"
               value="<?=PaginaSEIExterna::tratarHTML($strCnpj)?>"
               maxlength="18"
               tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
               onkeypress="return controlarCpfCnpj(this);"
               onkeyup ="return controlarCpfCnpj(this);"
               onkeydown ="return controlarCpfCnpj(this);"
               onchange="validaCpfCnpjOutorgante(this)" />
    </div>

    <div class="bloco" style="min-width:200px; width:20%;">
        <label id="lblRazaoSocial"
               for="txtRazaoSocial"
               class="infraLabelOpcional">Nome/Razão Social do Outorgante:</label>
        <input type="text"
               id="txtRazaoSocial"
               name="txtRazaoSocial"
               class="infraText"
               value="<?=PaginaSEIExterna::tratarHTML($strRazaoSocial)?>"
               maxlength="100"
               tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"/>
    </div>

    <div class="bloco" style="min-width:150px; width:15%">
        <label id="lblCpf" 
               for="txtCpf" 
               class="infraLabelOpcional">CPF do Outorgado:</label>
        <input type="text"
               id="txtCpf"
               name="txtCpf"
               class="infraText"
               value="<?=PaginaSEIExterna::tratarHTML($strCpf)?>"
               maxlength="14"
               tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
               onkeypress="return infraMascaraCPFProcurador(this);" 
               onchange="validaCpfProcurador(this)"/>
    </div>

    <div class="bloco" style="min-width:200px; width:18%;">
        <label id="lblNomeProcurador"
               for="txtNomeProcurador"
               class="infraLabelOpcional">Nome do Outorgado:</label>
        <input type="text"
               id="txtNomeProcurador"
               name="txtNomeProcurador"
               class="infraText"
               value="<?=PaginaSEIExterna::tratarHTML($strNome)?>"
               maxlength="100"
               tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"/>
   </div>
   <div class="bloco" style="min-width:100px; width:110px">
        <label id="lblTipoVinculo"
               for="slTipoVinculo"
               class="infraLabelOpcional">Tipo de Procuração:</label>
        <select name="slTipoViculo" id="slTipoViculo" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>">
            <option value=""></option>
        <?php if ($arrSelectTipoVinculo) : ?>
        <?php   foreach ($arrSelectTipoVinculo as $chaveTipoVinculo => $itemTipoVinculo) : ?>
            <option value="<?php echo $chaveTipoVinculo; ?>"
                    <?php if($chaveTipoVinculo == $strTipoVinculo){?>
                    selected="selected"
                    <?php }?>>
                <?php echo $itemTipoVinculo; ?>
            </option>
        <?php   endforeach; ?>
        <?php endif; ?>
        </select>
   </div>

   <div class="bloco" style="min-width:80px; width:80px">
        <label id="lblSituacao"
               for="slSituacao"
               class="infraLabelOpcional">Situação:</label>
        <select name="slSituacao" id="slSituacao" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>">
            <option value=""></option>
            <?php if (count($strResultado) > 0) : ?>
            <option value="<?php echo MdPetVincRepresentantRN::$RP_ATIVO?>"
                    <?php if(MdPetVincRepresentantRN::$RP_ATIVO == $strSituacao){?>
                    selected="selected"
                    <?php }?>>
                Ativa
            </option>
            <option value="<?php echo MdPetVincRepresentantRN::$RP_SUSPENSO?>"
                    <?php if(MdPetVincRepresentantRN::$RP_SUSPENSO == $strSituacao){?>
                    selected="selected"
                    <?php }?>>
                Suspensa
            </option>
            <option value="<?php echo MdPetVincRepresentantRN::$RP_REVOGADA?>"
                    <?php if(MdPetVincRepresentantRN::$RP_REVOGADA == $strSituacao){?>
                    selected="selected"
                    <?php }?>>
                Revogada
            </option>
            <option value="<?php echo MdPetVincRepresentantRN::$RP_RENUNCIADA?>"
                    <?php if(MdPetVincRepresentantRN::$RP_RENUNCIADA == $strSituacao){?>
                    selected="selected"
                    <?php }?>>
                Renunciada
            </option>
            <option value="<?php echo MdPetVincRepresentantRN::$RP_VENCIDA?>"
                    <?php if(MdPetVincRepresentantRN::$RP_VENCIDA == $strSituacao){?>
                    selected="selected"
                    <?php }?>>
                Vencida
            </option>
            <?php endif; ?>
        </select>
    </div>
<!-- /div -->
<?
    PaginaSEIExterna::getInstance()->fecharAreaDados();
    PaginaSEIExterna::getInstance()->montarAreaTabela($strResultado, $numRegistros);
?>
</form>
<?
    PaginaSEIExterna::getInstance()->fecharBody();
    PaginaSEIExterna::getInstance()->fecharHtml();
?>
