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

            $strTitulo = 'Impedimento de Substitui��o de Respons�vel Legal';
            $idUsuarioExternoLogado = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
            $srtCnpj = "";
            $strRazaoSocial = "";

            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->setNumIdUsuario($idUsuarioExternoLogado);
            $objUsuarioDTO->retNumIdContato();
            $objUsuarioDTO = (new UsuarioRN())->consultarRN0489($objUsuarioDTO);

            $arrTipoRepresentante = array(MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL,MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES);
            $arrTipoDocumento = array(MdPetVincDocumentoRN::$TP_PROTOCOLO_PROCURACAO,MdPetVincDocumentoRN::$TP_PROTOCOLO_PROCURACAO_ESPECIAL);

            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->setNumIdContato($objUsuarioDTO->getNumIdContato());
            $objMdPetVincRepresentantDTO->setStrTipoRepresentante($arrTipoRepresentante, InfraDTO::$OPER_IN);
            $objMdPetVincRepresentantDTO->setStrTipoDocumento($arrTipoDocumento, InfraDTO::$OPER_IN);
            $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($_GET['idVinculo']);
            $objMdPetVincRepresentantDTO->retDblIdDocumento();
            $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
            $objMdPetVincRepresentantDTO->retStrCNPJ();
            $objMdPetVincRepresentantDTO->retStrNomeProcurador();
            $objMdPetVincRepresentantDTO->retStrCpfProcurador();
            $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
            $objMdPetVincRepresentantDTO->retDthDataLimite();
            $arrObjMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN)->listar($objMdPetVincRepresentantDTO);
            $numRegistros = count($arrObjMdPetVincRepresentantDTO);
            if ($numRegistros > 0) {

                $strResultado = '';
                $strSumarioTabela = 'Procura��es Eletr�nicas';
                $strCaptionTabela = 'Procura��es Eletr�nicas';
                $strResultado .= '<table width="99%" class="infraTable" summary="' . $strSumarioTabela . '">';
                $strResultado .= '<caption class="infraCaption">' . PaginaSEIExterna::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';

                $strResultado .= '<tr>';
                //$strResultado .= '<th class="infraTh" width="1%">' . PaginaSEIExterna::getInstance()->getThCheck() . '</th>' . "\n";
                //$strResultado .= '<th class="infraTh" width="13%">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'N� do Documento', 'ProtocoloFormatado', $arrObjMdPetVincRepresentantDTO) . '</th>';
                $strResultado .= '<th class="infraTh" style="width:30%">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Processo', 'CNPJ', $arrObjMdPetVincRepresentantDTO) . '</th>';
                $strResultado .= '<th class="infraTh" style="width:35%">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Procura��o', 'DblIdDocumento', $arrObjMdPetVincRepresentantDTO) . '</th>';
                $strResultado .= '<th class="infraTh" >' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Tipo Procura��o', 'StrTipoRepresentante', $arrObjMdPetVincRepresentantDTO) . '</th>';
                $strResultado .= '</tr>';

                $arrSelectTipoVinculo = array();
                //Populando obj para tabela

                $qntProcuracao = 0;
                foreach ($arrObjMdPetVincRepresentantDTO as $itemObjMdPetVinculoDTO) {
                    //verifica se a procura��o � do tipo simples, caso seja existe mais uma valida��o a ser feita
                    if ($itemObjMdPetVinculoDTO->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES) {
                        // verifica se existe data limite, caso tenha existe mais uma valida��o a ser feita
                        if (!is_null($itemObjMdPetVinculoDTO->getDthDataLimite())) {
                            $dataAtual = date("Y-m-d");
                            $anoLimite = substr($itemObjMdPetVinculoDTO->getDthDataLimite(), 6);
                            $mesLimite = substr($itemObjMdPetVinculoDTO->getDthDataLimite(), 3, -5);
                            $diaLimite = substr($itemObjMdPetVinculoDTO->getDthDataLimite(), 0, -8);
                            $dataLimite = $anoLimite . "-" . $mesLimite . "-" . $diaLimite;
                            //se a data estiver vigente a procura��o � informada ao usu�rio
                            if (strtotime($dataAtual) <= strtotime($dataLimite)) {
                                $informaProcuracao = true;
                            //se a data n�o estiver vigente a procura��o
                            } else {
                                $informaProcuracao = false;

                            }
                        // caso n�o tenha data limite a procura��o � informada ao usu�rio
                        } else {
                            $informaProcuracao = true;
                        }
                    //se for especial � informada a procura��o � informada ao usu�rio
                    } else {
                        $informaProcuracao = true;
                    }

                    if($informaProcuracao == true){
                        $srtCnpj = InfraUtil::formatarCnpj($itemObjMdPetVinculoDTO->getStrCNPJ());
                        $strRazaoSocial = $itemObjMdPetVinculoDTO->getStrRazaoSocialNomeVinc();
                        $objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
                        $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($itemObjMdPetVinculoDTO->getDblIdDocumento());
                        $objRelProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo1();
                        $objRelProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo2();
                        $objRelProtocoloProtocoloDTO = (new RelProtocoloProtocoloRN())->consultarRN0841($objRelProtocoloProtocoloDTO);
                        $strResultado .= '<tr class="infraTrClara" id="tr-' . $qntProcuracao . '">';

                        $strResultado .= '<td>' . $objRelProtocoloProtocoloDTO->getStrProtocoloFormatadoProtocolo1() . '</td>';
                        $strResultado .= '<td>' . $objRelProtocoloProtocoloDTO->getStrProtocoloFormatadoProtocolo2() . '</td>';
                        $strResultado .= '<td>' . PaginaSEI::tratarHTML((new MdPetVincRepresentantDTO())->getStrNomeTipoRepresentante($itemObjMdPetVinculoDTO->getStrTipoRepresentante())) . '</td>';
                        $strResultado .= '</tr>';
                        $qntProcuracao++;
                    }
                }

                $strResultado .= '</table>';
            }

            break;


        default:
            throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecida.");
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
$arrComandos[] = '<button tabindex="-1" type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="infraFecharJanelaModal()" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
?>
<p>
    <label>
        N�o foi poss�vel finalizar a sua Vincula��o como Respons�vel Legal em substitui��o ao Respons�vel Legal j� existente, tendo em vista ainda possuir Procura��es Eletr�nicas vigentes em que o Outorgante � <span style="font-weight: bold"><?php echo $strRazaoSocial; ?></span> - <span style="font-weight: bold">(<?php echo $srtCnpj; ?>)</span>.<br /><br />
        Para prosseguir, antes voc� deve renunciar as Procura��es abaixo ou o Outorgante deve revog�-las no menu Procura��es Eletr�nicas.
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
