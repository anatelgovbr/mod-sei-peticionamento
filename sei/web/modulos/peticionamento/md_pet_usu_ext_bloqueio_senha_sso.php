<?php

require_once dirname(__FILE__) . '/../../SEI.php';

$strTitulo = 'Senha não cadastrada';

SessaoSEIExterna::getInstance()->validarLink();
SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
PaginaSEIExterna::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);
PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: ' . PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo);
SessaoSEIExterna::getInstance()->configurarAcessoExterno($_GET['id_acesso_externo']);
?>
  <div>
    <p class="alert alert-danger" style="font-size: 14px">Identificamos que você realizou o login com GOV.br, porém ainda não possui uma senha cadastrada no sistema. O cadastro de senha é obrigatório para a assinatura de documentos e para a realização de peticionamentos.</p>
    <p>
        <button type="button" accesskey="G" name="sbmFechar" id="sbmFechar" onclick="infraFecharJanelaSelecao();" value="Fechar" class="infraButton" style="cursor:pointer"><span class="infraTeclaAtalho">G</span>erar Senha</button>
    </p>
  </div>  
<?
SessaoSEIExterna::getInstance()->configurarAcessoExterno($_GET['id_acesso_externo']);
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
SessaoSEIExterna::getInstance()->configurarAcessoExterno(0);
?>