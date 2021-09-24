<?
/*
 * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
 * 
 * */

try {
	
	require_once dirname(__FILE__).'/../../SEI.php';

	session_start();	
	
	//////////////////////////////////////////////////////////////////////////////
	InfraDebug::getInstance()->setBolLigado(false);
	InfraDebug::getInstance()->setBolDebugInfra(false);
	InfraDebug::getInstance()->limpar();
	//////////////////////////////////////////////////////////////////////////////
	
	SessaoSei::getInstance()->validarLink();
	SessaoSei::getInstance()->validarPermissao($_GET['acao']);
		
	$objRN = new MdPetTpCtxContatoRN();

    if ((isset( $_POST['hdnPrincipal'] ) && $_POST['hdnPrincipal'] != "") || (isset( $_POST['hdnPrincipal2'] ) && $_POST['hdnPrincipal2'] != "")) {
        $arrContatosPrincipais = array();
        if (isset( $_POST['hdnPrincipal'] ) && $_POST['hdnPrincipal'] != "") {
            $arrPrincipal = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnPrincipal']);
            $arrPrincipal['cadastro'] = 'S';
            array_push($arrContatosPrincipais, $arrPrincipal);
        }

        // São permitidos Contatos de sistema para Seleção
        if (isset($_POST['hdnPrincipal2']) && $_POST['hdnPrincipal2'] != "") {
            $arrPrincipal2 = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnPrincipal2']);
            $arrPrincipal2['cadastro'] = 'N';
            array_push($arrContatosPrincipais, $arrPrincipal2);
        }

        $objRN->cadastrarMultiplo($arrContatosPrincipais);
        PaginaSEI::getInstance()->adicionarMensagem("Os dados foram salvos com sucesso.", PaginaSEI::$TIPO_MSG_AVISO);
    }
   
   }catch(Exception $e){
   	PaginaSEI::getInstance()->processarExcecao($e);
   }

$objDTO = new MdPetRelTpCtxContatoDTO();
$objDTO->retTodos();
$objDTO->setStrSinCadastroInteressado('S');
$objDTO->setStrSinSelecaoInteressado('N');
$arrItens = $objRN->listar($objDTO);
$numero = count( $arrItens );
$strSelPrin = "";

$objDTO2 = new MdPetRelTpCtxContatoDTO();
$objDTO2->retTodos();
$objDTO2->setStrSinCadastroInteressado('N');
$objDTO2->setStrSinSelecaoInteressado('S');
$arrItens2 = $objRN->listar($objDTO2);
$numero2 = count( $arrItens2 );
$strSelPrin2 = "";

if( $numero > 0){
	
	//SEIv3
	$tipoContextoRN = new TipoContatoRN();
	
	foreach( $arrItens as $item ){

		//SEIv3
		$tipoContextoDTO = new TipoContatoDTO();
		$tipoContextoDTO->retNumIdTipoContato();
		$tipoContextoDTO->retStrNome();
		$tipoContextoDTO->setNumIdTipoContato( $item->getNumIdTipoContextoContato() );

		$tipoContextoDTO = $tipoContextoRN->consultarRN0336( $tipoContextoDTO );

		//SEIv3
		$strSelPrin .= "<option value='" . $item->getNumIdTipoContextoContato() . "'>" . $tipoContextoDTO->getStrNome() . " </option>";
	
	}
	
}

if( $numero2 > 0){

	//SEIv3
	$tipoContextoRN = new TipoContatoRN();

	foreach( $arrItens2 as $item ){

		//SEIv3
		$tipoContextoDTO = new TipoContatoDTO();
		$tipoContextoDTO->retNumIdTipoContato();
		$tipoContextoDTO->retStrNome();
		$tipoContextoDTO->setNumIdTipoContato( $item->getNumIdTipoContextoContato() );
		$tipoContextoDTO = $tipoContextoRN->consultarRN0336( $tipoContextoDTO );
		$strSelPrin2 .= "<option value='" . $item->getNumIdTipoContextoContato() . "'>" . $tipoContextoDTO->getStrNome() . " </option>";
		
	}

}

$strTitulo = "Peticionamento - Tipos de Contatos Permitidos";

$arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarGrupoUnidade" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSei::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&acao_origem='.$_GET['acao'])).'\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';


PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
require_once('md_pet_tp_ctx_contato_cadastro_css.php');
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');

//=====================================================
//INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
//=====================================================

require_once('md_pet_tp_ctx_contato_cadastro_inicializacao.php');

//=====================================================
//FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
//=====================================================
?>
  <form id="frmGrupoCadastro" method="post" onsubmit="return OnSubmitForm();" 
        action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSei::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
    <?
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados('45em');
    ?>
      <div id="divGeral" class="infraAreaDados">
          <div class="row">
              <div id="divLblPrincipal" class="col-sm-6 col-md-7 col-lg-6 col-xl-4">

                  <!-- //////////////////////////////////// CAMPO 1 //////////////////////////////////// -->
                  <label id="lblPrincipal" for="txtPrincipal" class="infraLabelObrigatorio">Cadastro de Interessado:
                      <img align="top"
                           src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                           onmouseover="return infraTooltipMostrar('Nos casos em que o Usuário Externo tiver que cadastrar Contato, o campo de Tipo apresentado para ele será restringido aos Tipos de Contatos indicados aqui.', 'Ajuda');"
                           onmouseout="return infraTooltipOcultar();" class="infraImgModulo">
                  </label>

                  <input type="text" id="txtPrincipal" name="txtPrincipal" class="infraText form-control"
                         onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50"
                         tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
              </div>
          </div>
          <div class="row">
              <div id="divSelPrincipal" class="col-sm-10 col-md-10 col-lg-8 col-xl-6">
                  <div class="input-group mb-3">
                      <select id="selPrincipal" name="selPrincipal" size="8" multiple="multiple" class="infraSelect"
                              tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                          <?= $strSelPrin; ?>
                      </select>
                      <div id="divIconesPrincipal">
                          <img id="imgLupaPrincipal" onclick="objLupaPrincipal.selecionar(700,500);"
                               onkeypress="objLupaPrincipal.selecionar(700,500);" src="<?=PaginaSEI::getInstance()->getDiretorioSvgGlobal()?>/pesquisar.svg"
                               alt="Selecionar Tipos de Contatos" title="Selecionar Tipos de Contatos" class="infraImg"
                               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

                          <img id="imgExcluirPrincipal" onclick="objLupaPrincipal.remover();"
                               onkeypress="objLupaPrincipal.remover();" src="<?=PaginaSEI::getInstance()->getDiretorioSvgGlobal()?>/remover.svg"
                               alt="Remover Tipos de Contatos Selecionadas" title="Remover Tipos de Contatos Selecionados"
                               class="infraImg" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

                      </div>
                  </div>
              </div>
          </div>

          <div class="row">
              <div id="divLblPrincipal2" class="col-sm-6 col-md-7 col-lg-6 col-xl-4">
                  <!--  //////////////////////////////////// CAMPO 2 //////////////////////////////////// -->
                  <label id="lblPrincipal2" for="txtPrincipal2" class="infraLabelObrigatorio">Seleção de Interessado:
                      <img align="top"
                           src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                           onmouseover="return infraTooltipMostrar('Nos casos em que o Usuário Externo tiver que selecionar Contato, os Contatos disponíveis para ele selecionar estarão restringidos aos Tipos de Contatos indicados aqui.', 'Ajuda');"
                           onmouseout="return infraTooltipOcultar();" class="infraImgModulo">
                  </label>

                  <input type="text" id="txtPrincipal2" name="txtPrincipal2" class="infraText form-control"
                         onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50"
                         tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
              </div>
          </div>
          <div class="row">
              <div id="divSelPrincipal2" class="col-sm-10 col-md-10 col-lg-8 col-xl-6">
                  <div class="input-group mb-3">
                      <select id="selPrincipal2" name="selPrincipal2" size="8" multiple="multiple" class="infraSelect"
                              tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                          <?= $strSelPrin2; ?>
                      </select>

                      <div id="divIconesPrincipal2">
                          <img id="imgLupaPrincipal2" onclick="objLupaPrincipal2.selecionar(700,500);"
                               onkeypress="objLupaPrincipal2.selecionar(700,500);" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                               alt="Selecionar Tipos de Contatos" title="Selecionar Tipos de Contatos" class="infraImg"
                               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

                          <img id="imgExcluirPrincipal2" onclick="objLupaPrincipal2.remover();"
                               onkeypress="objLupaPrincipal2.remover();" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                               alt="Remover Tipos de Contatos Selecionadas" title="Remover Tipos de Contatos Selecionados"
                               class="infraImg" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                      </div>
                  </div>
              </div>
          </div>

          <!--  //////////////////////////////////// CAMPOS HIDDEN ////////////////////////////////////  -->
          <input type="hidden" id="hdnIdPrincipal" name="hdnIdPrincipal" class="infraText" value="" />
          <input type="hidden" id="hdnPrincipal" name="hdnPrincipal" value="" />

          <input type="hidden" id="hdnIdPrincipal2" name="hdnIdPrincipal2" class="infraText" value="" />
          <input type="hidden" id="hdnPrincipal2" name="hdnPrincipal2" value="" />

      </div>

    <?
    PaginaSEI::getInstance()->fecharAreaDados();
    ?>
  </form>
<?
PaginaSEI::getInstance()->montarAreaDebug();
PaginaSEI::getInstance()->fecharBody();

//inclusao de conteudos JavaScript adicionais
require_once('md_pet_tp_ctx_contato_cadastro_js.php');

PaginaSEI::getInstance()->fecharHtml();

?>