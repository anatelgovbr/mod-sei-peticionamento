<?php
/**
 * ANATEL
 *
 * 07/08/2019 - criado por kamyla.sakamoto@castgroup.com.br - CAST
 *
 */
require_once dirname(__FILE__) . '/../../SEI.php';
SessaoSEIExterna::getInstance()->validarLink();
SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
PaginaSEIExterna::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

$arrComandos = array();
$texto = '';
$idContato = $_GET['id_contato'];

switch ($_GET['acao']) {

    case 'md_pet_intimacao_usu_ext_negar_resposta_peticionar':
        try {
            $strTitulo = 'Responder Intimação Eletrônica não Permitida';

            $arrComandos[] = '<button type="button" accesskey="C" name="sbmFechar" id="sbmFechar"  onclick="fechar();" value="Fechar" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->retStrNome();
            $objContatoDTO->retDblCpf();
            $objContatoDTO->retStrCnpj();
            $objContatoDTO->setNumIdContato($idContato);
            $objContatoRN = new ContatoRN();
            $objContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);

            $texto  = '<p>Você não possui mais permissão para Responder a Intimação Eletrônica, conforme abaixo:</p>';
            $texto .= '<p>Destinatários não permitidos:</p>';
            $texto .= '<ul>';

            if (!is_null(InfraUtil::formatarCpfCnpj($objContatoDTO->retDblCpf()))) {
                $texto .= '<li>'.$objContatoDTO->getStrNome() . ' (' . InfraUtil::formatarCpfCnpj($objContatoDTO->getDblCpf()) . ')</li>';
            } else {
                $texto .= '<li>'.$objContatoDTO->getStrNome() . ' (' . InfraUtil::formatarCpfCnpj($objContatoDTO->getStrCnpj()) . ')</li>';
            }
            
            $texto .= '</ul>';

            $texto .= '<p>verifique seus Poderes de Representação.</p>';
            
            $url = "controlador_externo.php?acao=usuario_externo_controle_acessos&acao_origem=usuario_externo_logar&acao_origem=md_pet_usu_ext_recibo_listar&id_orgao_acesso_externo=0";
	    $urlAssinada = SessaoSEIExterna::getInstance()->assinarLink($url);
            
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
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody();
?>
<form action="<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_intimacao_usu_ext_confirmar_aceite&id_procedimento=' . $_GET['id_procedimento'] . '&id_acesso_externo=' . $_GET['id_acesso_externo'] . '&id_documento=' . $_GET['id_documento']); ?>" method="post" id="frmMdPetIntimacaoConfirmarAceite" name="frmMdPetIntimacaoConfirmarAceite">

    <div class="row">
        <div class="col-12">
            <div style="padding-right: 50%; padding-top: 2%">
                <?php PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos); ?>
            </div>
        </div>
        <div class="col-12">
            <h4><?= $strTitulo ?></h4>
        </div>
        <div class="col-12">
            <div class="textoIntimacaoEletronica">
                <?= $texto ?>
            </div>
        </div>
    </div>

</form>
<?php
SessaoSEIExterna::getInstance()->configurarAcessoExterno($_GET['id_acesso_externo']);
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
SessaoSEIExterna::getInstance()->configurarAcessoExterno(0);
?>


<script type="text/javascript">
    function fechar() {
        window.opener.location = '<?= $urlAssinada;?>';
        window.opener.focus();
        window.close();
    }
</script>
