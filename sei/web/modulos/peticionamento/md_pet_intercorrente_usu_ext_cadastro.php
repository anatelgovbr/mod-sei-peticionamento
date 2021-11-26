<?php
/**
     * ANATEL
     *
     * 20/10/2016 - criado por marcelo.bezerra@cast.com.br - CAST
     *
     * ========================================================================================================
     * P�gina principal do cadastro de peticionamento intercorrente, ela invoca p�ginas auxiliares (via require)
     * contendo:
     *
     *  - variaveis e consultas de inicializacao da pagina
     *  - switch case controlador de a��es principais da p�gina
     *  - estilos CSS
     *  - fun��es JavaScript
     *  - �rea / bloco de processos
     *  - �rea / bloco de documentos
     * ===========================================================================================================
*/
try {

        require_once dirname(__FILE__) . '/../../SEI.php';

        session_start();

        //////////////////////////////////////////////////////////////////////////////
        InfraDebug::getInstance()->setBolLigado(false);
        InfraDebug::getInstance()->setBolDebugInfra(false);
        InfraDebug::getInstance()->limpar();
        //////////////////////////////////////////////////////////////////////////////

        SessaoSEIExterna::getInstance()->validarLink();
        SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);

        //=====================================================
        //INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
        //=====================================================
        require_once('md_pet_intercorrente_usu_ext_cadastro_inicializar.php');
        //=====================================================
        //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
        //=====================================================

        //inclusao de script com o controle das a��es principais da tela
        require_once('md_pet_intercorrente_usu_ext_cadastro_acoes.php');

    } catch (Exception $e) {
        PaginaSEIExterna::getInstance()->processarExcecao($e);
    }

    $strTitulo = "Peticionamento Intercorrente";
    PaginaSEIExterna::getInstance()->montarDocType();
    PaginaSEIExterna::getInstance()->abrirHtml();
    PaginaSEIExterna::getInstance()->abrirHead();
    PaginaSEIExterna::getInstance()->montarMeta();
    PaginaSEIExterna::getInstance()->montarTitle(':: ' . PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
    PaginaSEIExterna::getInstance()->montarStyle();

    //=====================================================
    //INICIO FOLHAS DE ESTILOS CSS
    //=====================================================
    require_once('md_pet_intercorrente_usu_ext_cadastro_css.php');
    //=====================================================
    //FIM FOLHAS DE ESTILOS CSS
    //=====================================================

    PaginaSEIExterna::getInstance()->montarJavaScript();
    //=====================================================
    //INICIO JAVASCRIPT
    //=====================================================
    require_once('md_pet_intercorrente_usu_ext_cadastro_js.php');
    //=====================================================
    //FIM JAVASCRIPT
    //=====================================================
    PaginaSEIExterna::getInstance()->fecharHead();
    PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmPeticionamentoIntercorrente">
        <!--  esta tela n�o ter� multiplos forms de uploads como ocorre no Peticionamento Novo, logo , termos apenas um form geral -->
        <?php
            PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
            PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
        ?>
        <fieldset id="field1" class="infraFieldset sizeFieldset">
            <legend class="infraLegend">&nbsp; Orienta��es &nbsp;</legend>
		<div class="bloco">
			<label>
				<?= $txtOrientacoes ?>
			</label>
		</div>
        </fieldset>
        <br/>

        <!-- =========================== -->
        <!--  INICIO FIELDSET PROCESSOS -->
        <!-- =========================== -->
        <?php require_once('md_pet_intercorrente_usu_ext_cadastro_bloco_processos.php'); ?>
        <!-- =========================== -->
        <!--  FIM FIELDSET PROCESSOS -->
        <!-- =========================== -->
        <br/>

        <!-- =========================== -->
        <!--  INICIO FIELDSET DOCUMENTOS -->
        <!-- =========================== -->
        <?php require_once('md_pet_intercorrente_usu_ext_cadastro_bloco_documentos.php'); ?>
        <!-- =========================== -->
        <!--  FIM FIELDSET DOCUMENTOS -->
        <!-- =========================== -->

    </form>
<?php
    PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandos);
    PaginaSEIExterna::getInstance()->fecharAreaDados();
    PaginaSEIExterna::getInstance()->fecharBody();
    PaginaSEIExterna::getInstance()->fecharHtml();