<?php
/**
 * ANATEL
 *
 * 25/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
 *
 */

$strTitulo = 'Impedimento de Substituição de Responsável Legal';
$strResultado = '';
$numRegistros = 0;
$srtCnpj = '';
$strRazaoSocial = '';

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

            $idVinculo = isset($_GET['idVinculo']) && ctype_digit((string) $_GET['idVinculo'])
                ? (int) $_GET['idVinculo']
                : 0;

            if ($idVinculo <= 0) {
                throw new InfraException('Vínculo não informado.');
            }

            $idUsuarioExternoLogado = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();

            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->setNumIdUsuario($idUsuarioExternoLogado);
            $objUsuarioDTO->retNumIdContato();
            $objUsuarioDTO = (new UsuarioRN())->consultarRN0489($objUsuarioDTO);

            if (is_null($objUsuarioDTO)) {
                throw new InfraException('Usuário externo não localizado.');
            }

            $arrTipoRepresentante = [MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL, MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES];
            $arrTipoDocumento = [MdPetVincDocumentoRN::$TP_PROTOCOLO_PROCURACAO, MdPetVincDocumentoRN::$TP_PROTOCOLO_PROCURACAO_ESPECIAL];

            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->setNumIdContato($objUsuarioDTO->getNumIdContato());
            $objMdPetVincRepresentantDTO->setStrTipoRepresentante($arrTipoRepresentante, InfraDTO::$OPER_IN);
            $objMdPetVincRepresentantDTO->setStrTipoDocumento($arrTipoDocumento, InfraDTO::$OPER_IN);
            $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
            $objMdPetVincRepresentantDTO->retDblIdDocumento();
            $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
            $objMdPetVincRepresentantDTO->retStrCNPJ();
            $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
            $objMdPetVincRepresentantDTO->retDthDataLimite();
            $arrObjMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN)->listar($objMdPetVincRepresentantDTO);

            $arrProcuracoesImpedimento = [];
            foreach ($arrObjMdPetVincRepresentantDTO as $objRepresentacaoDTO) {
                $bolVigente = true;
                if ($objRepresentacaoDTO->getStrTipoRepresentante() === MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES
                    && !is_null($objRepresentacaoDTO->getDthDataLimite())) {
                    $strDataLimite = explode(' ', $objRepresentacaoDTO->getDthDataLimite())[0];
                    $bolVigente = InfraData::compararDatas(InfraData::getStrDataAtual(), $strDataLimite) >= 0;
                }

                if ($bolVigente) {
                    $arrProcuracoesImpedimento[] = $objRepresentacaoDTO;
                }
            }

            $numRegistros = count($arrProcuracoesImpedimento);

            if ($numRegistros > 0) {

                $strResultado = '';
                $strSumarioTabela = 'Procurações Eletrônicas';
                $strCaptionTabela = 'Procurações Eletrônicas';
                $strResultado .= '<table width="100%" class="infraTable" summary="' . $strSumarioTabela . '">';
                $strResultado .= '<caption class="infraCaption">' . PaginaSEIExterna::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';

                $strResultado .= '<tr>';
                $strResultado .= '<th class="infraTh" style="width:27%">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Processo', 'CNPJ', $arrProcuracoesImpedimento) . '</th>';
                $strResultado .= '<th class="infraTh" style="width:32%">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Procuração', 'DblIdDocumento', $arrProcuracoesImpedimento) . '</th>';
                $strResultado .= '<th class="infraTh" >' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Tipo Procuração', 'StrTipoRepresentante', $arrProcuracoesImpedimento) . '</th>';
                $strResultado .= '</tr>';

                $qntProcuracao = 0;
                foreach ($arrProcuracoesImpedimento as $itemObjMdPetVinculoDTO) {
                    $srtCnpj = InfraUtil::formatarCnpj($itemObjMdPetVinculoDTO->getStrCNPJ());
                    $strRazaoSocial = $itemObjMdPetVinculoDTO->getStrRazaoSocialNomeVinc();

                    $objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
                    $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($itemObjMdPetVinculoDTO->getDblIdDocumento());
                    $objRelProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo1();
                    $objRelProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo2();
                    $objRelProtocoloProtocoloDTO = (new RelProtocoloProtocoloRN())->consultarRN0841($objRelProtocoloProtocoloDTO);

                    $strResultado .= '<tr class="infraTrClara" id="tr-' . $qntProcuracao . '">';
                    $strResultado .= '<td>' . PaginaSEI::tratarHTML($objRelProtocoloProtocoloDTO->getStrProtocoloFormatadoProtocolo1()) . '</td>';
                    $strResultado .= '<td class="text-center">' . PaginaSEI::tratarHTML($objRelProtocoloProtocoloDTO->getStrProtocoloFormatadoProtocolo2()) . '</td>';
                    $strResultado .= '<td class="text-center">' . PaginaSEI::tratarHTML((new MdPetVincRepresentantDTO())->getStrNomeTipoRepresentante($itemObjMdPetVinculoDTO->getStrTipoRepresentante())) . '</td>';
                    $strResultado .= '</tr>';
                    $qntProcuracao++;
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
?>
    #divInfraBarraComandosSuperior {
        margin-bottom: 1rem;
    }

    .mdPetImpedimentoMensagem {
        clear: both;
        width: 100%;
        margin: 0 0 1.25rem;
        padding: 1rem 1.25rem;
        border-left: 4px solid #1b6ca8;
        border-radius: 0.25rem;
        background-color: #f4f8fb;
        line-height: 1.5;
        box-sizing: border-box;
    }

    .mdPetImpedimentoMensagem p {
        margin: 0;
    }

    .mdPetImpedimentoMensagem p + p {
        margin-top: 0.75rem;
    }

    .mdPetImpedimentoTabela {
        clear: both;
        width: 100%;
        overflow-x: auto;
    }

    .mdPetImpedimentoTabela .infraAreaTabela,
    .mdPetImpedimentoTabela .infraTable {
        width: 100% !important;
    }

    .mdPetImpedimentoTabela .infraTable {
        min-width: 640px;
    }
<?php
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');

$arrComandos = [];
$arrComandos[] = '<button tabindex="-1" type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="fecharJanela()" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
?>
<div class="mdPetImpedimentoMensagem">
    <p>
        Não foi possível prosseguir a sua Vinculação como Responsável Legal em substituição ao Responsável Legal já existente, pois ainda existem Procurações Eletrônicas vigentes em que o Outorgante é <strong><?php echo PaginaSEI::tratarHTML($strRazaoSocial); ?></strong> (<strong><?php echo PaginaSEI::tratarHTML($srtCnpj); ?></strong>).
    </p>
    <p>
        Para prosseguir, renuncie às procurações no menu <strong>Procurações Eletrônicas</strong> ou solicite ao Outorgante que as revogue.
    </p>
</div>

<?
PaginaSEIExterna::getInstance()->fecharAreaDados();
?>
<div class="mdPetImpedimentoTabela">
<?php
PaginaSEIExterna::getInstance()->montarAreaTabela($strResultado, $numRegistros);
?>
</div>
<?php
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>
<script type="text/javascript">


    function inicializar() {
        infraEfeitoTabelas();
    }

    function fecharJanela() {
        if (window.opener != null && !window.opener.closed) {
            window.opener.focus();
        }

        window.close();
    }

</script>
