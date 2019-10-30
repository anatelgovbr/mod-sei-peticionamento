<?
/**
 * ANATEL
 *
 * 25/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
 *
 */

try {

    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    InfraDebug::getInstance()->setBolLigado(false);
    InfraDebug::getInstance()->setBolDebugInfra(false);
    InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////
    PaginaSEIExterna::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);
    SessaoSEIExterna::getInstance()->validarLink();
    SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);

    //=====================================================
    //INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
    //=====================================================


    //=====================================================
    //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
    //=====================================================
    switch ($_GET['acao']) {

        case 'md_pet_vinc_usu_ext_negado':

            $strTitulo = 'Impedimento de Substituição de Responsável Legal';
            $idUsuarioExternoLogado = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
            $srtCnpj = "";
            $strRazaoSocial = "";

            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->setNumIdUsuario($idUsuarioExternoLogado);
            $objUsuarioDTO->retNumIdContato();
            $objUsuarioDTO = (new UsuarioRN())->consultarRN0489($objUsuarioDTO);

            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->setNumIdContato($objUsuarioDTO->getNumIdContato());
            $objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL);
            $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            $objMdPetVincRepresentantDTO->setStrTipoDocumento(MdPetVincDocumentoRN::$TP_PROTOCOLO_PROCURACAO_ESPECIAL);
            $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($_GET['idVinculo']);
            $objMdPetVincRepresentantDTO->retDblIdDocumento();
            $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
            $objMdPetVincRepresentantDTO->retStrCNPJ();
            $objMdPetVincRepresentantDTO->retStrNomeProcurador();
            $objMdPetVincRepresentantDTO->retStrCpfProcurador();
            $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
            $arrObjMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN)->listar($objMdPetVincRepresentantDTO);
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
                $strResultado .= '<th class="infraTh" style="width:30%">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Processo', 'CNPJ', $arrObjMdPetVincRepresentantDTO) . '</th>';
                $strResultado .= '<th class="infraTh" style="width:35%">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Procuracao', 'DblIdDocumento', $arrObjMdPetVincRepresentantDTO) . '</th>';
                $strResultado .= '<th class="infraTh" >' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Tipo Procuracao', 'StrTipoRepresentante', $arrObjMdPetVincRepresentantDTO) . '</th>';
                $strResultado .= '</tr>';

                $arrSelectTipoVinculo = array();
                //Populando obj para tabela
                for ($i = 0; $i < $numRegistros; $i++) {
                    $srtCnpj = InfraUtil::formatarCnpj($arrObjMdPetVincRepresentantDTO[$i]->getStrCNPJ());
                    $strRazaoSocial = $arrObjMdPetVincRepresentantDTO[$i]->getStrRazaoSocialNomeVinc();
                    $objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
                    $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($arrObjMdPetVincRepresentantDTO[$i]->getDblIdDocumento());
                    $objRelProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo1();
                    $objRelProtocoloProtocoloDTO = (new RelProtocoloProtocoloRN())->consultarRN0841($objRelProtocoloProtocoloDTO);
                    $strResultado .= '<tr class="infraTrClara" id="tr-' . $i . '">';
                    // $strResultado .= '<td valign="top">' . PaginaSEIExterna::getInstance()->getTrCheck($i, $arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent(), $idDocumento) . '</td>';
                    //$strResultado .= '<td>' . $idDocumentoFormatado . '</td>';
                    $strResultado .= '<td>' . $objRelProtocoloProtocoloDTO->getStrProtocoloFormatadoProtocolo1() . '</td>';
                    $strResultado .= '<td>' . PaginaSEI::tratarHTML($arrObjMdPetVincRepresentantDTO[$i]->getDblIdDocumento()) . '</td>';
                    $strResultado .= '<td>' . PaginaSEI::tratarHTML((new MdPetVincRepresentantDTO())->getStrNomeTipoRepresentante($arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante())) . '</td>';
                    $strResultado .= '</tr>';
                }
                $strResultado .= '</table>';
            }

            break;


        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

} catch (Exception $e) {

    //removendo atributos da sessao
    //if( SessaoSEIExterna::getInstance()->isSetAtributo('docPrincipalConteudoHTML') ){
    //SessaoSEIExterna::getInstance()->removerAtributo('docPrincipalConteudoHTML');
    //}

    if (SessaoSEIExterna::getInstance()->isSetAtributo('arrIdAnexoPrincipal')) {
        SessaoSEIExterna::getInstance()->removerAtributo('arrIdAnexoPrincipal');
    }

    if (SessaoSEIExterna::getInstance()->isSetAtributo('arrIdAnexoEssencial')) {
        SessaoSEIExterna::getInstance()->removerAtributo('arrIdAnexoEssencial');
    }

    if (SessaoSEIExterna::getInstance()->isSetAtributo('arrIdAnexoComplementar')) {
        SessaoSEIExterna::getInstance()->removerAtributo('arrIdAnexoComplementar');
    }

    if (SessaoSEIExterna::getInstance()->isSetAtributo('idDocPrincipalGerado')) {
        SessaoSEIExterna::getInstance()->removerAtributo('idDocPrincipalGerado');
    }

    PaginaSEIExterna::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: ' . PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');

$arrComandos = array();
$arrComandos[] = '<button tabindex="-1" type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="fecharJanela()" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
?>
<p>
    <label>
        Não foi possível finalizar a sua Vinculação como Responsável Legal em substituição ao Responsável Legal já existente, tendo em vista ainda possuir Procurações Eletrônicas vigentes em que o Outorgante é <span style="font-weight: bold"><?php echo $strRazaoSocial; ?></span> - <span style="font-weight: bold">(<?php echo $srtCnpj; ?>)</span>.<br /><br />
        Para prosseguir, antes você deve renunciar as Procurações abaixo ou o Outorgante deve revogá-las no menu Procurações Eletrônicas.
    </label>
</p>


<?
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->montarAreaTabela($strResultado, $numRegistros);
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>
<script type="text/javascript">


    function inicializar() {
        infraEfeitoTabelas();
        if (document.getElementById('selCargo') != null) {
            document.getElementById('selCargo').focus();
        }
    }

    function fecharJanela() {

        if (window.opener != null && !window.opener.closed) {
            window.opener.focus();
        }

        window.close();
    }

</script>