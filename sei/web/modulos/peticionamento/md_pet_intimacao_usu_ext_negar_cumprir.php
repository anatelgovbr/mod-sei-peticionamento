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
$id_contato = $_GET['id_contato'];
$estado = '';
switch ($_GET['acao']) {

    case 'md_pet_intimacao_usu_ext_negar_cumprir':
        try {
            $strTitulo = 'Cumprir Intimação Eletrônica não Permitida';
            
            $arrComandos[] = '<button type="button" accesskey="C" name="sbmFechar" id="sbmFechar"  onclick="window.close();" value="Fechar" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
            

        $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
        $objMdPetVincRepresentantDTO->setNumIdContato($_GET['id_destinatario']);
        $objMdPetVincRepresentantDTO->setStrSinAtivo('S');
        $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO, InfraDTO::$OPER_DIFERENTE);
        $objMdPetVincRepresentantDTO->setNumIdContatoVinc($_GET['id_contato']);
        $objMdPetVincRepresentantDTO->retNumIdContatoVinc();
        $objMdPetVincRepresentantDTO->retNumIdContato();
        $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
        $objMdPetVincRepresentantDTO->retStrStaEstado();
        $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
        $objMdPetVincRepresentantDTO->retStrCNPJ();

        $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
        $arrObjMdPetVincRepresentantDTO= $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);


        if($arrObjMdPetVincRepresentantDTO[0]->getStrStaEstado() == MdPetVincRepresentantRN::$RP_REVOGADA){
            $estado = "Revogada";
        }else if($arrObjMdPetVincRepresentantDTO[0]->getStrStaEstado() == MdPetVincRepresentantRN::$RP_SUSPENSO){
            $estado = "Suspenso";
        }else if($arrObjMdPetVincRepresentantDTO[0]->getStrStaEstado() == MdPetVincRepresentantRN::$RP_RENUNCIADA){
            $estado = "Renunciada";
        }else if($arrObjMdPetVincRepresentantDTO[0]->getStrStaEstado() == MdPetVincRepresentantRN::$RP_VENCIDA){
            $estado = "Vencida";
        }

        $texto = 'Você não possui mais permissão para cumprir a Intimação Eletrônica destinada à '.PaginaSEI::tratarHTML($arrObjMdPetVincRepresentantDTO[0]->getStrRazaoSocialNomeVinc()).' ('.infraUtil::formatarCnpj($arrObjMdPetVincRepresentantDTO[0]->getStrCNPJ()).'), pois sua Procuração Eletrônica está ' .$estado.'.';
            
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

<?php
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload=""');

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