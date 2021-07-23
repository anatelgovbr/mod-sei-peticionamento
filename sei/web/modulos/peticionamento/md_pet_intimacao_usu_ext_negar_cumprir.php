<?php
/**
 * ANATEL
 *
 * 30/04/2018 - criado por renato.monteiro@castgroup.com.br - CAST
 *
 */
require_once dirname(__FILE__) . '/../../SEI.php';
SessaoSEIExterna::getInstance()->validarLink();
SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
PaginaSEIExterna::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

$arrComandos = array();
$texto = '';
$idContato = $_GET['id_contato'];
$idDestinatario = $_GET['id_destinatario'];
$idDocumento = $_GET['id_documento'];

switch ($_GET['acao']) {

    case 'md_pet_intimacao_usu_ext_negar_cumprir':
        try {
            $strTitulo = 'Cumprir Intimação Eletrônica não Permitida';

            $arrComandos[] = '<button type="button" accesskey="C" name="sbmFechar" id="sbmFechar"  onclick="window.close();" value="Fechar" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            if (count($idContato) == 1) {
                $objMdPetVincRepresentantDTO->setNumIdContatoVinc(current($idContato));
            } else {
                $objMdPetVincRepresentantDTO->setNumIdContatoVinc($idContato, InfraDTO::$OPER_IN);
            }
            $objMdPetVincRepresentantDTO->setNumIdContato($idDestinatario);
            $objMdPetVincRepresentantDTO->setStrSinAtivo('S');
            $objMdPetVincRepresentantDTO->retNumIdContatoVinc();
            $objMdPetVincRepresentantDTO->retNumIdContato();
            $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
            $objMdPetVincRepresentantDTO->retStrStaEstado();
            $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
            $objMdPetVincRepresentantDTO->retStrCNPJ();
            $objMdPetVincRepresentantDTO->retStrCPF();
            $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
            $objMdPetVincRepresentantDTO->retDthDataLimite();
            $objMdPetVincRepresentantDTO->retStrStaAbrangencia();
            $objMdPetVincRepresentantDTO->retStrStaEstado();

            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);

            if ($arrObjMdPetVincRepresentantDTO) {
                $arrPessoaJuridica = array();
                $arrPessoaFisica = array();
                $texto = '<div style="padding-top: 10px; padding-bottom: 10px"><p>Você não possui mais permissão para cumprir a Intimação Eletrônica conforme abaixo:<br><br>Destinatários não permitidos:';
                foreach ($arrObjMdPetVincRepresentantDTO as $chaveVinculo => $itemObjMdPetVinculoDTO) {
                    $procuracaoValida = true;
                    if ($itemObjMdPetVinculoDTO->getStrStaEstado() != MdPetVincRepresentantRN::$RP_ATIVO) {
                        $procuracaoValida = false;
                    }elseif($itemObjMdPetVinculoDTO->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES) {
                        $rnMdPetIntimacaoRN = new MdPetIntimacaoRN();
                        $verificacaoCriteriosProcuracaoSimples = $rnMdPetIntimacaoRN->_verificarCriteriosProcuracaoSimples($itemObjMdPetVinculoDTO->getNumIdMdPetVinculoRepresent(), $itemObjMdPetVinculoDTO->getStrStaEstado(), $itemObjMdPetVinculoDTO->getDthDataLimite(), $idDocumento, $itemObjMdPetVinculoDTO->getStrStaAbrangencia());
                        if (!$verificacaoCriteriosProcuracaoSimples) {
                            $procuracaoValida = false;
                        }
                    }                    
                    if(!$procuracaoValida){
                        if(!is_null($itemObjMdPetVinculoDTO->getStrCPF())){
                            $arrPessoaFisica[] = '&nbsp;&nbsp;&nbsp;&nbsp;- '.$itemObjMdPetVinculoDTO->getStrRazaoSocialNomeVinc() . ' (' . InfraUtil::formatarCpfCnpj($itemObjMdPetVinculoDTO->getStrCPF()) . '), verifique seus Poderes de Representação.';
                        }else{
                            $arrPessoaJuridica[] = '&nbsp;&nbsp;&nbsp;&nbsp;- '.$itemObjMdPetVinculoDTO->getStrRazaoSocialNomeVinc() . ' (' . InfraUtil::formatarCpfCnpj($itemObjMdPetVinculoDTO->getStrCNPJ()) . '), verifique seus Poderes de Representação.';
                        }
                    }
                }
                $texto .= implode('<br>',$arrPessoaJuridica)."<br>".implode('<br>',$arrPessoaFisica);
            }
            $texto .= '</p></div>';
        } catch (Exception $e) {
            PaginaSEIExterna::getInstance()->processarExcecao($e);
        }

        break;
    default:
        throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
}

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();

PaginaSEIExterna::getInstance()->montarTitle(':: ' . PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');

PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
?>

.textoIntimacaoEletronica {}
.clear {clear: both;}
    p{
    font-size: 0.875rem;
    }
<?php
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo);
?>
<form action="<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_intimacao_usu_ext_confirmar_aceite&id_procedimento=' . $_GET['id_procedimento'] . '&id_acesso_externo=' . $_GET['id_acesso_externo'] . '&id_documento=' . $_GET['id_documento']); ?>" method="post" id="frmMdPetIntimacaoConfirmarAceite" name="frmMdPetIntimacaoConfirmarAceite">

    <div class="clear"></div>
    <div class="textoIntimacaoEletronica">
        <h2>
            <?php echo $texto; ?>
        </h2>

    </div>
    <div style="padding-right: 50%;padding-top: 2%">
        <?php PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos); ?>

</form>
<?php
SessaoSEIExterna::getInstance()->configurarAcessoExterno($_GET['id_acesso_externo']);
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
SessaoSEIExterna::getInstance()->configurarAcessoExterno(0);
?>