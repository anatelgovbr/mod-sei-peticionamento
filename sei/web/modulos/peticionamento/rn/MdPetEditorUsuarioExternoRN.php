<?
/**
 * ANATEL
 *
 * 26/07/2016 - criado por marcelo.bezerra@cast.com.br
 *
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetEditorUsuarioExternoRN extends InfraRN
{

  public static $TE_NENHUM = 'N';
  public static $TE_INTERNO = 'I';
  public static $TE_EDOC = 'E';
  public static $VERSAO_CK = '07102014';

  public static $REGEXP_LINK_ASSINADO="@<a[^>]*href=\"[^>]*controlador\\.php\\?acao=([^&]*)&(?:amp;)?.*id_pro(?:tocolo|cedimento)=(\\d+)&.*infra_sistema=(\\d+)&(?:amp;)?infra_unidade_atual=\\d+&(?:amp;)?infra_hash=[^\"]*\"[^>]*>([^<]+)<\\/a>@i";
  public static $REGEXP_LINK_ASSINADO_SIMPLES='@(<a[^>]*href=")([^"]*_sistema=\d+&(?:amp;)?infra_unidade_atual=\d+&(?:amp;)?infra_hash=[^"]*)"([^>]*>)([^<]*)</a>@i';
  public static $REGEXP_SPAN_LINKSEI='@(?><span[^>]*>)?<a[^>]*id="lnkSei(\d+)[^>]*>([^<]+)<\/a>(?><\/span>)?@i';
  public static $REGEXP_ATRIB_VALOR="%'[^']*':'[^']*'%";

  private $arrProtocolos;

  public function __construct()
  {
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco()
  {
    return BancoSEI::getInstance();
  }

  protected function montarSimplesConectado(EditorDTO $objEditorDTO)
  {

    $ret=new EditorDTO();
    $objOrgaoDTO = new OrgaoDTO();
    $objOrgaoDTO->retStrServidorCorretorOrtografico();
    $objOrgaoDTO->retStrStaCorretorOrtografico();
    $objOrgaoDTO->setNumIdOrgao(SessaoSEIExterna::getInstance()->getNumIdOrgaoUsuarioExterno());

    if ($objEditorDTO->isSetStrSinEstilos()){
      $strEstilos=$objEditorDTO->getStrSinEstilos();
    } else {
      $strEstilos='S';
    }
    if ($objEditorDTO->isSetStrSinImagens()){
      $bolImagens=($objEditorDTO->getStrSinImagens()=='S');
    } else {
      $bolImagens=true;
    }
    if ($objEditorDTO->isSetStrSinCodigoFonte()){
      $strCodigoFonte=$objEditorDTO->getStrSinCodigoFonte();
    } else {
      $strCodigoFonte='';
    }
    if ($objEditorDTO->isSetStrSinAutoTexto()){
      $bolAutotexto=($objEditorDTO->getStrSinAutoTexto()=='S');
    } else {
      $bolAutotexto=false;
    }
    
    $objOrgaoRN = new OrgaoRN();
    $objOrgaoDTO = $objOrgaoRN->consultarRN1352($objOrgaoDTO);

    $objImagemFormatoDTO = new ImagemFormatoDTO();
    $objImagemFormatoDTO->retStrFormato();

    $objImagemFormatoRN = new ImagemFormatoRN();
    $arrImagemPermitida = InfraArray::converterArrInfraDTO($objImagemFormatoRN->listar($objImagemFormatoDTO), 'Formato');
    if (in_array('jpg', $arrImagemPermitida) && !in_array('jpeg', $arrImagemPermitida)) $arrImagemPermitida[] = 'jpeg';

    $includePlugins = array('autogrow', 'sharedspace','simpleLink','extenso', 'maiuscula', 'stylesheetparser', 'tableresize', 'tableclean', 'symbol');
    if ($bolAutotexto) $includePlugins[]='autotexto';
    $removePlugins = array('resize', 'maximize', 'link','wsc');

    $scayt="";
    if ($objOrgaoDTO != null && $objOrgaoDTO->getStrStaCorretorOrtografico()==OrgaoRN::$TCO_LICENCIADO) {
      try {
        $scayt = InfraUtil::isBolUrlValida($objOrgaoDTO->getStrServidorCorretorOrtografico()."/spellcheck/lf/scayt3/ckscayt/ckscayt.js")?"scayt3":"scayt";
      }
      catch(Exception $e){
        $scayt="";
        LogSEI::getInstance()->gravar("'Erro acessando servidor SCAYT para validar versão do plugin:\n".InfraException::inspecionar($e));
      }
    }
    if ($scayt!="") {
        $includePlugins[] = $scayt;
    }
    $ie = PaginaSEIExterna::getInstance()->isBolNavegadorIE();
    if ($ie) $ie = PaginaSEIExterna::getInstance()->getNumVersaoInternetExplorer();
    if ($bolImagens && count($arrImagemPermitida)>0 && !PaginaSEIExterna::getInstance()->isBolNavegadorSafariIpad() && (!$ie || $ie>7)) {
      $includePlugins[] = 'base64image';
    } else {
      $removePlugins[] = 'base64image';
    }
    $strInicializacao = '<script type="text/javascript" charset="utf-8" src="editor/ck/ckeditor.js?t=' . self::$VERSAO_CK . '"></script>';
    $strLinkAnexos = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_editor_imagem_upload');
    $strLinkAjax = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=upload_buscar');

    $strUsuario = strtolower(SessaoSEIExterna::getInstance()->getStrSiglaUsuario() . '.' . SessaoSEIExterna::getInstance()->getStrSiglaOrgaoUsuario());

    $arrConfig = ConfiguracaoSEI::getInstance()->getArrConfiguracoes();
    $strRegexSistema = str_replace('.','\.',$arrConfig['SEI']['URL']).'.*infra_hash=.*';
    $strRegexSistema = preg_replace("@http[s]?://@",'',$strRegexSistema);

    $strInicializacao .= " <!-- inicializar -->";
    $strInicializacao .= '<style type="text/css" >';
    $strInicializacao .= "\n.cke_combo__styles .cke_combo_text {width:230px !important;}\n";
    $strInicializacao .= ".cke_button__save_label {display:inline !important;}\n";
    $strInicializacao .= ".cke_button__autotexto_label {display:inline !important;}\n";
    $strInicializacao .= ".cke_combopanel__styles {width:400px !important;}\n";
    $strInicializacao .= '</style>';
    $strInicializacao .= '<script type="text/javascript">';
    $strInicializacao .= "CKEDITOR.config.url_sei_re='".$strRegexSistema."';\n";
    $strInicializacao .= "CKEDITOR.config.skin='moonocolor';\n";
    $strInicializacao .= "CKEDITOR.config.removePlugins='" . implode(',', $removePlugins) . "';\n";
    $strInicializacao .= "CKEDITOR.config.extraPlugins='" . implode(',', $includePlugins) . "';\n";
    $strInicializacao .= "CKEDITOR.config.dialog_noConfirmCancel = true;\n";
    $strInicializacao .= "CKEDITOR.config.base64image_filetypes='" . implode('|', $arrImagemPermitida) . "';\n";
    $strInicializacao .= "CKEDITOR.config.base64imageUploadUrl='" . $strLinkAnexos . "';\n";
    $strInicializacao .= "CKEDITOR.config.base64imageAjaxUrl='" . $strLinkAjax . "';\n";
    $strInicializacao .= "CKEDITOR.config.scayt_sLang='pt_BR';\n";
    $strInicializacao .= "CKEDITOR.config.scayt_userDictionaryName = '" . $strUsuario . "';\n";
    $strInicializacao .= "CKEDITOR.config.wsc_userDictionaryName = '" . $strUsuario . "';\n";
    $strInicializacao .= "CKEDITOR.config.scayt_autoStartup=true;\n";
    
    //altura automatica ajustado em relaçao ao conteudo dentro do editor
    $strInicializacao .= "CKEDITOR.config.autoGrow_minHeight = 1;\n";
    $strInicializacao .= "CKEDITOR.config.autoGrow_bottomSpace = 5;\n";

    if ($objOrgaoDTO != null && $objOrgaoDTO->getStrStaCorretorOrtografico()==OrgaoRN::$TCO_LICENCIADO) {
      $strInicializacao .= "CKEDITOR.config.wsc_customLoaderScript = '" . $objOrgaoDTO->getStrServidorCorretorOrtografico() . "/spellcheck/lf/22/js/wsc_fck2plugin.js';\n";
      if ($scayt=="scayt"){
        $strInicializacao .= "CKEDITOR.config.scayt_srcUrl = '" . $objOrgaoDTO->getStrServidorCorretorOrtografico() . "/spellcheck/lf/scayt/scayt.js?".self::$VERSAO_CK."';\n";
      } elseif ($scayt=="scayt3") {
        $strInicializacao .="CKEDITOR.config.scayt_srcUrl = '".$objOrgaoDTO->getStrServidorCorretorOrtografico()."/spellcheck/lf/scayt3/ckscayt/ckscayt.js?".self::$VERSAO_CK."';\n";
      }
      $strInicializacao .= "CKEDITOR.config.scayt_uiTabs='0,0,0';\n";
    }

    $strInicializacao .= "CKEDITOR.config.sharedSpaces= {'top':'divComandos'};\n";
    if ($objEditorDTO->getStrSinSomenteLeitura()=='S') {
      $strInicializacao .= "CKEDITOR.config.readOnly=true;\n";
    }
    $strInicializacao .= "CKEDITOR.on('dialogDefinition',function(ev)\n";
    $strInicializacao .= "{if(ev.data.name=='image'){\n";
    $strInicializacao .= "var dd=ev.data.definition;dd.removeContents('Link');dd.removeContents('advanced');dd.minHeight=200;dd.minWidth=250;\n";
    $strInicializacao .= "var tab=dd.getContents('info');tab.get('ratioLock').style='margin-top:20px;width:40px;height:40px;';tab.get('txtUrl').hidden=true;tab.get('txtAlt').hidden=true;tab.get('htmlPreview').hidden=true;tab.remove('txtHSpace');tab.remove('txtVSpace');tab.remove('cmbAlign');}});\n";
    $ret->setStrCss($this->jsEncode($this->montarCssEditor(0)));
    if ($strEstilos=='S') {
      $strInicializacao .= "CKEDITOR.config.contentsCss=" . $ret->getStrCss() . ";\n";
    }
    $strInicializacao .= "</script>\n";
    
    $ret->setStrInicializacao($strInicializacao);
    $ret->setNumVersao(0);
    $ret->setStrToolbar($this->jsEncode($this->montarBarraFerramentas(true, false, ($objOrgaoDTO->getStrStaCorretorOrtografico()!=OrgaoRN::$TCO_NENHUM))));

    $bolValidacao = false;
    $strValidacoes = '';
        
    $strEditor = "CKEDITOR.replace('" . $objEditorDTO->getStrNomeCampo() . "',{ 'toolbar':";
    $strEditor .= $this->jsEncode($this->montarBarraFerramentas($bolAutotexto, false, ($objOrgaoDTO != null && $objOrgaoDTO->getStrStaCorretorOrtografico()!=OrgaoRN::$TCO_NENHUM),$strCodigoFonte,$strEstilos)) . "});";
    $ret->setStrEditores($strEditor);
    $ret->setStrValidacao($bolValidacao);
    $ret->setStrMensagens($strValidacoes);
    
    return $ret;
  }

  private function montarBarraFerramentas($bolAdicionarTextoPadrao, $bolBtnWSC, $bolBtnScayt, $strBtnSource='', $strEstilos='')
  {

    $arrGrupoEstilos = array();

    if ($bolAdicionarTextoPadrao) {
      $arrGrupoEstilos[] = 'autotexto';
    }
    if ($strEstilos=='' || $strEstilos=='S') {
      $arrGrupoEstilos[] = 'Styles';
    }

    $arrRetorno = array();
    $arrRetorno[] = array('Save');
    $arrRetorno[] = array('Find', 'Replace', '-', 'RemoveFormat', 'Bold', 'Italic', 'Underline', 'Strike','Subscript','Superscript', 'Maiuscula', 'Minuscula', 'TextColor', 'BGColor' /*,'PageBreak'*/);

    $temp = array('Cut', 'Copy', 'PasteFromWord', 'PasteText', '-', 'Undo', 'Redo', 'ShowBlocks','Symbol');
    if ($bolBtnWSC) $temp[] = 'SpellChecker';
    if ($bolBtnScayt) $temp[] = 'Scayt';
    $arrRetorno[] = $temp;

    $arrRetorno[] = array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'base64image');
    if (!PaginaSEIExterna::getInstance()->isBolNavegadorFirefox() || PaginaSEIExterna::getInstance()->getNumVersaoFirefox()>=16) { // não disponibiliza zoom no firefox<16 devido a bug
      $arrRetorno[] = array('Table', 'SpecialChar', 'SimpleLink', 'linksei', 'Extenso', 'Zoom');
    } else {
      $arrRetorno[] = array('Table', 'SpecialChar', 'SimpleLink', 'linksei', 'Extenso');
    }
    $arrRetorno[] = $arrGrupoEstilos;

    return $arrRetorno;
  }

  protected function montarControlado(EditorDTO $parObjEditorDTO)
  {
    try {

      //gerar nova versao igual a anterior
      $objVersaoSecaoDocumentoDTO = new VersaoSecaoDocumentoDTO();
      $objVersaoSecaoDocumentoDTO->retNumIdSecaoModeloSecaoDocumento();
      $objVersaoSecaoDocumentoDTO->retStrSinAssinaturaSecaoDocumento();
      $objVersaoSecaoDocumentoDTO->retStrSinSomenteLeituraSecaoDocumento();
      $objVersaoSecaoDocumentoDTO->retStrSinPrincipalSecaoDocumento();
      $objVersaoSecaoDocumentoDTO->retStrSinDinamicaSecaoDocumento();
      $objVersaoSecaoDocumentoDTO->retStrSinCabecalhoSecaoDocumento();
      $objVersaoSecaoDocumentoDTO->retStrSinRodapeSecaoDocumento();
      $objVersaoSecaoDocumentoDTO->retStrConteudo();
      $objVersaoSecaoDocumentoDTO->setDblIdDocumentoSecaoDocumento($parObjEditorDTO->getDblIdDocumento());
      $objVersaoSecaoDocumentoDTO->setNumIdBaseConhecimentoSecaoDocumento($parObjEditorDTO->getNumIdBaseConhecimento());
      $objVersaoSecaoDocumentoDTO->setStrSinUltima('S');
      $objVersaoSecaoDocumentoDTO->setOrdNumOrdemSecaoDocumento(InfraDTO::$TIPO_ORDENACAO_ASC);

      $objVersaoSecaoDocumentoRN = new VersaoSecaoDocumentoRN();
      $arrObjVersaoSecaoDocumentoDTO = $objVersaoSecaoDocumentoRN->listar($objVersaoSecaoDocumentoDTO);

      $arrObjSecaoDocumentoDTO = array();
      $bolDinamica = false;
      foreach ($arrObjVersaoSecaoDocumentoDTO as $objVersaoSecaoDocumentoDTO2) {

        if ($objVersaoSecaoDocumentoDTO2->getStrSinAssinaturaSecaoDocumento()=='N') {
          $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
          $objSecaoDocumentoDTO->setNumIdSecaoModelo($objVersaoSecaoDocumentoDTO2->getNumIdSecaoModeloSecaoDocumento());
          $objSecaoDocumentoDTO->setStrConteudo($objVersaoSecaoDocumentoDTO2->getStrConteudo());
          $arrObjSecaoDocumentoDTO[] = $objSecaoDocumentoDTO;
        }

        if ($objVersaoSecaoDocumentoDTO2->getStrSinDinamicaSecaoDocumento()=='N') {
          $bolDinamica = true;
        }
      }

      $bolValidacao = false;
      $strValidacoes = '';
      try {

        $parObjEditorDTO->setArrObjSecaoDocumentoDTO($arrObjSecaoDocumentoDTO);
        $numVersao = $this->adicionarVersao($parObjEditorDTO);

      } catch (InfraException $e) {
        if ($e->contemValidacoes()) {
          $bolValidacao = true;
          $strValidacoes = $e->__toString();
        } else {
          throw $e;
        }
      }

      //se possui secoes dinamicas entao lista novamente para exibir o conteudo atualizado na ultima versao
      if ($bolDinamica) {
        $objVersaoSecaoDocumentoRN = new VersaoSecaoDocumentoRN();
        $arrObjVersaoSecaoDocumentoDTO = $objVersaoSecaoDocumentoRN->listar($objVersaoSecaoDocumentoDTO);
      }

      if (!$parObjEditorDTO->isSetNumIdConjuntoEstilos()) {
        $objConjuntoEstilosDTO = new ConjuntoEstilosDTO();
        $objConjuntoEstilosRN = new ConjuntoEstilosRN();
        $objConjuntoEstilosDTO->setStrSinUltimo('S');
        $objConjuntoEstilosDTO->retNumIdConjuntoEstilos();
        $objConjuntoEstilosDTO = $objConjuntoEstilosRN->consultar($objConjuntoEstilosDTO);
        $parObjEditorDTO->setNumIdConjuntoEstilos($objConjuntoEstilosDTO->getNumIdConjuntoEstilos());
      }
      $strConteudoCss = $this->montarCssEditor($parObjEditorDTO->getNumIdConjuntoEstilos());
      $strEditores = '';
      $strTextareas = '';

      //busca os estilos permitidos por seção-modelo
      $objRelSecaoModCjEstilosItemDTO = new RelSecaoModCjEstilosItemDTO();
      $objRelSecaoModCjEstilosItemDTO->retNumIdSecaoModelo();
      $objRelSecaoModCjEstilosItemDTO->retStrNomeEstilo();
      $objRelSecaoModCjEstilosItemDTO->retStrFormatacao();
      $objRelSecaoModCjEstilosItemDTO->setNumIdSecaoModelo(InfraArray::converterArrInfraDTO($arrObjVersaoSecaoDocumentoDTO, 'IdSecaoModeloSecaoDocumento'), InfraDTO::$OPER_IN);
      $objRelSecaoModCjEstilosItemDTO->setOrdStrNomeEstilo(InfraDTO::$TIPO_ORDENACAO_ASC);
      $objRelSecaoModCjEstilosItemDTO->setNumIdConjuntoEstilos($parObjEditorDTO->getNumIdConjuntoEstilos());
      $objRelSecaoModCjEstilosItemRN = new RelSecaoModCjEstilosItemRN();
      $arrObjRelSecaoModCjEstilosItemDTO = InfraArray::indexarArrInfraDTO($objRelSecaoModCjEstilosItemRN->listar($objRelSecaoModCjEstilosItemDTO), 'IdSecaoModelo', true);

      $strLinkAnexos = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=documento_upload_anexo');

      foreach ($arrObjVersaoSecaoDocumentoDTO as $objVersaoSecaoDocumentoDTO) {
        if ($objVersaoSecaoDocumentoDTO->getStrSinAssinaturaSecaoDocumento()=='N') {

          $strFormatos = "";
          if (isset($arrObjRelSecaoModCjEstilosItemDTO[$objVersaoSecaoDocumentoDTO->getNumIdSecaoModeloSecaoDocumento()])) {
            foreach ($arrObjRelSecaoModCjEstilosItemDTO[$objVersaoSecaoDocumentoDTO->getNumIdSecaoModeloSecaoDocumento()] as $objRelSecaoModCjEstilosItemDTO) {
              $strFormatos .= $objRelSecaoModCjEstilosItemDTO->getStrNomeEstilo() . "|";
            }
          }
          $strFormatos = rtrim($strFormatos, '|');

          $strTextareas .= '<textarea name="txaEditor_' . $objVersaoSecaoDocumentoDTO->getNumIdSecaoModeloSecaoDocumento() . '" style="display:none;">';
          $strTextareas .= InfraString::formatarXML($this->filtrarTags(PaginaSEI::tratarHTML($objVersaoSecaoDocumentoDTO->getStrConteudo())));
          $strTextareas .= '</textarea>';


          $strEditores .= "CKEDITOR.replace('txaEditor_" . $objVersaoSecaoDocumentoDTO->getNumIdSecaoModeloSecaoDocumento() . "',";
          $strEditores .= '{filebrowserUploadUrl:"' . $strLinkAnexos . '","toolbar":toolbar,"stylesheetParser_validSelectors":/^(p)\.(';
          $strEditores .= $strFormatos . ')$/i,';

          if ($objVersaoSecaoDocumentoDTO->getStrSinDinamicaSecaoDocumento()=='S') {
            $strEditores .= '"dinamico":true,';
          }

          $strEditores .= '"readOnly":';

          if ($objVersaoSecaoDocumentoDTO->getStrSinSomenteLeituraSecaoDocumento()=='S' || $bolValidacao) {
            $strEditores .= 'true});' . "\n";
          } else {
            $strEditores .= 'false,autoGrow_bottomSpace:20});' . "\n";
          }
        }
      }

      $objOrgaoDTO = new OrgaoDTO();
      $objOrgaoDTO->retStrServidorCorretorOrtografico();
      $objOrgaoDTO->retStrStaCorretorOrtografico();
      $objOrgaoDTO->setNumIdOrgao(SessaoSEIExterna::getInstance()->getNumIdOrgaoUnidadeAtual());

      $objOrgaoRN = new OrgaoRN();
      $objOrgaoDTO = $objOrgaoRN->consultarRN1352($objOrgaoDTO);

      $objImagemFormatoDTO = new ImagemFormatoDTO();
      $objImagemFormatoDTO->retStrFormato();

      $objImagemFormatoRN = new ImagemFormatoRN();
      $arrImagemPermitida = InfraArray::converterArrInfraDTO($objImagemFormatoRN->listar($objImagemFormatoDTO), 'Formato');
      if (in_array('jpg', $arrImagemPermitida) && !in_array('jpeg', $arrImagemPermitida)) $arrImagemPermitida[] = 'jpeg';


      $includePlugins = array('autogrow', 'linksei', 'sharedspace', 'autotexto', 'simpleLink', 'extenso', 'maiuscula', 'stylesheetparser', 'stylesdefault', 'tableresize', 'symbol', 'tableclean');
      $removePlugins = array('resize', 'maximize', 'link', 'wsc');


      $ie = PaginaSEIExterna::getInstance()->isBolNavegadorIE();
      if ($ie) $ie = PaginaSEIExterna::getInstance()->getNumVersaoInternetExplorer();
      if (count($arrImagemPermitida)>0 && !PaginaSEIExterna::getInstance()->isBolNavegadorSafariIpad() && (!$ie || $ie>7)) {
        $includePlugins[] = 'base64image';
      } else {
        $removePlugins[] = 'base64image';
      }

      $scayt="";
      if ($objOrgaoDTO->getStrStaCorretorOrtografico()==OrgaoRN::$TCO_LICENCIADO) {
        try {
          $scayt = InfraUtil::isBolUrlValida($objOrgaoDTO->getStrServidorCorretorOrtografico()."/spellcheck/lf/scayt3/ckscayt/ckscayt.js")?"scayt3":"scayt";
        }
        catch(Exception $e){
          $scayt="";
          LogSEI::getInstance()->gravar("'Erro acessando servidor SCAYT para validar versão do plugin:\n".InfraException::inspecionar($e));
        }
      }
      if ($scayt!="") {
          $includePlugins[] = $scayt;
      }
      //desabilita zoom devido a bug do firefox <16
      if (!PaginaSEIExterna::getInstance()->isBolNavegadorFirefox() || PaginaSEIExterna::getInstance()->getNumVersaoFirefox()>=16) {
        $includePlugins[] = "zoom";
      }

      $strUsuarioDicionario = strtolower(SessaoSEIExterna::getInstance()->getStrSiglaUsuario() . '.' . SessaoSEIExterna::getInstance()->getStrSiglaOrgaoUsuario());
      $strLinkAnexos = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_editor_imagem_upload');
      $strLinkAjax = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=upload_buscar');
      $arrConfig = ConfiguracaoSEI::getInstance()->getArrConfiguracoes();
      $strRegexSistema = str_replace('.','\.',$arrConfig['SEI']['URL']).'.*infra_hash=.*';
      $strRegexSistema = preg_replace("@http[s]?://@",'',$strRegexSistema);

      $strInicializacao = "<!-- INICIALIZAR 2 -->";
      $strInicializacao .= '<script type="text/javascript" charset="utf-8" src="editor/ck/ckeditor.js?t=' . self::$VERSAO_CK . '"></script>';
      $strInicializacao .= '<script type="text/javascript">';
      $strInicializacao .= "CKEDITOR.config.skin='moonocolor';\n";
      $strInicializacao .= "CKEDITOR.config.url_sei_re='".$strRegexSistema."';\n";
      $strInicializacao .= "CKEDITOR.config.removePlugins='" . implode(',', $removePlugins) . "';\n";
      $strInicializacao .= "CKEDITOR.config.extraPlugins='" . implode(',', $includePlugins) . "';\n";
      $strInicializacao .= "CKEDITOR.config.base64image_filetypes='" . implode('|', $arrImagemPermitida) . "';\n";
      $strInicializacao .= "CKEDITOR.config.base64imageUploadUrl='" . $strLinkAnexos . "';\n";
      $strInicializacao .= "CKEDITOR.config.base64imageAjaxUrl='" . $strLinkAjax . "';\n";
      $strInicializacao .= "CKEDITOR.config.autoGrow_minHeight= 30;\n";
      $strInicializacao .= "CKEDITOR.config.autoGrow_onStartup= true;\n";
      $strInicializacao .= "CKEDITOR.config.dialog_noConfirmCancel = true;\n";
      $strInicializacao .= "CKEDITOR.config.height=100;\n";
      $strInicializacao .= "CKEDITOR.config.language='pt-br';\n";
      $strInicializacao .= "CKEDITOR.config.scayt_sLang='pt_BR';\n";
      $strInicializacao .= "CKEDITOR.config.scayt_userDictionaryName = '" . $strUsuarioDicionario . "';\n";
      $strInicializacao .= "CKEDITOR.config.wsc_userDictionaryName = '" . $strUsuarioDicionario . "';\n";
      $strInicializacao .= "CKEDITOR.config.defaultLanguage='pt-br';\n";
      $strInicializacao .= "CKEDITOR.config.sharedSpaces= {'top':'divComandos'};\n";
      $strInicializacao .= "CKEDITOR.config.readOnly=true;\n";
      $strInicializacao .= "CKEDITOR.config.scayt_autoStartup=true;\n";

      if ($objOrgaoDTO->getStrStaCorretorOrtografico()==OrgaoRN::$TCO_LICENCIADO) {
        $strInicializacao .= "CKEDITOR.config.wsc_customLoaderScript = '" . $objOrgaoDTO->getStrServidorCorretorOrtografico() . "/spellcheck/lf/22/js/wsc_fck2plugin.js';\n";
        if ($scayt=="scayt"){
          $strInicializacao .= "CKEDITOR.config.scayt_srcUrl = '" . $objOrgaoDTO->getStrServidorCorretorOrtografico() . "/spellcheck/lf/scayt/scayt.js?".self::$VERSAO_CK."';\n";
        } elseif ($scayt=="scayt3") {
          $strInicializacao .="CKEDITOR.config.scayt_srcUrl = '".$objOrgaoDTO->getStrServidorCorretorOrtografico()."/spellcheck/lf/scayt3/ckscayt/ckscayt.js?".self::$VERSAO_CK."';\n";
        }
        $strInicializacao .= "CKEDITOR.config.scayt_uiTabs='0,0,0';\n";
      }

      $strInicializacao .= "CKEDITOR.on('dialogDefinition',function(ev)\n";
      $strInicializacao .= "{if(ev.data.name=='image'){\n";
      $strInicializacao .= "var dd=ev.data.definition;dd.removeContents('Link');dd.removeContents('advanced');dd.minHeight=200;dd.minWidth=250;\n";
      $strInicializacao .= "var tab=dd.getContents('info');tab.get('ratioLock').style='margin-top:20px;width:40px;height:40px;';tab.get('txtUrl').hidden=true;tab.get('txtAlt').hidden=true;tab.get('htmlPreview').hidden=true;tab.remove('txtHSpace');tab.remove('txtVSpace');tab.remove('cmbAlign');}});\n";

      $strInicializacao .= "</script>\n";

      $ret=new EditorDTO();

      $ret->setNumVersao($numVersao);
      $ret->setStrToolbar($this->jsEncode($this->montarBarraFerramentas(true, false, ($objOrgaoDTO->getStrStaCorretorOrtografico()!=OrgaoRN::$TCO_NENHUM))));
      $ret->setStrTextareas($strTextareas);
      $ret->setStrCss($strConteudoCss);
      $ret->setStrInicializacao($strInicializacao);
      $ret->setStrEditores($strEditores);
      $ret->setStrValidacao($bolValidacao);
      $ret->setStrMensagens($strValidacoes);

      return $ret;

    } catch (Exception $e) {
      throw new InfraException('Erro montando editor.', $e);
    }
  }

  public function jsEncode($val)
  {
    if (is_null($val)) {
      return 'null';
    }
    if (is_bool($val)) {
      return $val ? 'true' : 'false';
    }
    if (is_int($val)) {
      return $val;
    }
    if (is_float($val)) {
      return str_replace(',', '.', $val);
    }
    if (is_array($val) || is_object($val)) {
      if (is_array($val) && (array_keys($val)===range(0, count($val) - 1))) {
        return '[' . implode(',', array_map(array($this, 'jsEncode'), $val)) . ']';
      }
      $temp = array();
      foreach ($val as $k => $v) {
        $temp[] = $this->jsEncode("{$k}") . ':' . $this->jsEncode($v);
      }
      return '{' . implode(',', $temp) . '}';
    }
    // String otherwise
    if (strpos($val, '@@')===0)
      return substr($val, 2);
    if (strtoupper(substr($val, 0, 9))=='CKEDITOR.')
      return $val;

    return '"' . str_replace(array("\\", "/", "\n", "\t", "\r", "\x08", "\x0c", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'), $val) . '"';
  }

  protected function montarCssEditorConectado($numIdConjuntoEstilos)
  {
    $objConjuntoEstilosDTO = new ConjuntoEstilosDTO();
    $objConjuntoEstilosRN = new ConjuntoEstilosRN();
    $objConjuntoEstilosItemDTO = new ConjuntoEstilosItemDTO();
    $objConjuntoEstilosItemRN = new ConjuntoEstilosItemRN();
    if ($numIdConjuntoEstilos==0 || $numIdConjuntoEstilos==null) {
      $objConjuntoEstilosDTO->setStrSinUltimo('S');
      $objConjuntoEstilosDTO->retNumIdConjuntoEstilos();
      $objConjuntoEstilosDTO = $objConjuntoEstilosRN->consultar($objConjuntoEstilosDTO);
      $objConjuntoEstilosItemDTO->setNumIdConjuntoEstilos($objConjuntoEstilosDTO->getNumIdConjuntoEstilos());
    } else {
      $objConjuntoEstilosItemDTO->setNumIdConjuntoEstilos($numIdConjuntoEstilos);
    }
    $objConjuntoEstilosItemDTO->retTodos();
    $objConjuntoEstilosItemDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
    $arrObjConjuntoEstilosItemDTO = $objConjuntoEstilosItemRN->listar($objConjuntoEstilosItemDTO);

    $strCssEditor = "";
    //converte estilos do formato antigo para css
    foreach ($arrObjConjuntoEstilosItemDTO as $objConjuntoEstilosItemDTO) {
      $strCssEditor .= "p." . $objConjuntoEstilosItemDTO->getStrNome() . " {";
      $strFormatacao = $objConjuntoEstilosItemDTO->getStrFormatacao();
      preg_match_all(self::$REGEXP_ATRIB_VALOR, $strFormatacao, $arrStrConteudo);
      foreach ($arrStrConteudo[0] as $value) {
        $value = str_replace("'", "", $value);
        $strCssEditor .= $value . ";";
      }
      $strCssEditor .= "} ";
    }

    return $strCssEditor;
  }

  public function filtrarTags($strConteudo)
  {
    $strConteudo = preg_replace("%<font[^>]*>%si", "", $strConteudo);
    $strConteudo = preg_replace("%</font>%si", "", $strConteudo);
    return str_replace(array('<o:p>', '</o:p>'), '', $strConteudo);
  }

  protected function gerarVersaoInicialControlado(EditorDTO $parObjEditorDTO)
  {
    try {
		
      //preciso obter a unidade do documento
      $docDTO = new DocumentoDTO();
      $docRN = new DocumentoRN();
      $docDTO->retDblIdDocumento();
      $docDTO->retNumIdUnidadeResponsavel();
      $docDTO->setDblIdDocumento( $parObjEditorDTO->getDblIdDocumento() );
      
      $docDTO = $docRN->consultarRN0005( $docDTO );
      $idUnidadeResponsavel = $docDTO->getNumIdUnidadeResponsavel();
      
      $objParametrosEditorDTO = $this->obterParametros($parObjEditorDTO);

      $arrTags = $objParametrosEditorDTO->getArrTags();
      $arrTags[] = array('@versao@', '1');

      $objSecaoModeloRN = new SecaoModeloRN();
      $objSecaoDocumentoRN = new SecaoDocumentoRN();
      $objVersaoSecaoDocumentoRN = new VersaoSecaoDocumentoRN();

      $objDocumentoRN = new DocumentoRN();
      $dthAtual = InfraData::getStrDataHoraAtual();

      $parObjEditorDTO->setNumIdConjuntoEstilos(null);

      if ($parObjEditorDTO->isSetDblIdDocumentoBase() || $parObjEditorDTO->isSetDblIdDocumentoTextoBase()) {
        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->retNumIdConjuntoEstilos();
        if ($parObjEditorDTO->isSetDblIdDocumentoBase()) {
          $objDocumentoDTO->setDblIdDocumento($parObjEditorDTO->getDblIdDocumentoBase());
        } else {
          $objDocumentoDTO->setDblIdDocumento($parObjEditorDTO->getDblIdDocumentoTextoBase());
        }
        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);
        $parObjEditorDTO->setNumIdConjuntoEstilos($objDocumentoDTO->getNumIdConjuntoEstilos());

      } else if ($parObjEditorDTO->isSetNumIdBaseConhecimentoBase()) {
        $objBaseConhecimentoDTO = new BaseConhecimentoDTO();
        $objBaseConhecimentoDTO->retNumIdConjuntoEstilos();
        $objBaseConhecimentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimentoBase());
        $objBaseConhecimentoRN = new BaseConhecimentoRN();
        $objBaseConhecimentoDTO = $objBaseConhecimentoRN->consultar($objBaseConhecimentoDTO);
        $parObjEditorDTO->setNumIdConjuntoEstilos($objBaseConhecimentoDTO->getNumIdConjuntoEstilos());
      } else if ($parObjEditorDTO->isSetNumIdTextoPadraoInterno()) {
        $objTextoPadraoInternoDTO = new TextoPadraoInternoDTO();
        $objTextoPadraoInternoRN = new TextoPadraoInternoRN();
        $objTextoPadraoInternoDTO->retNumIdConjuntoEstilos();
        $objTextoPadraoInternoDTO->setNumIdTextoPadraoInterno($parObjEditorDTO->getNumIdTextoPadraoInterno());
        $objTextoPadraoInternoDTO = $objTextoPadraoInternoRN->consultar($objTextoPadraoInternoDTO);
        $parObjEditorDTO->setNumIdConjuntoEstilos($objTextoPadraoInternoDTO->getNumIdConjuntoEstilos());
      }

      if ($parObjEditorDTO->getNumIdConjuntoEstilos()==null) {
        $objConjuntoEstilosDTO = new ConjuntoEstilosDTO();
        $objConjuntoEstilosDTO->setStrSinUltimo('S');
        $objConjuntoEstilosDTO->retNumIdConjuntoEstilos();
        $objConjuntoEstilosRN = new ConjuntoEstilosRN();
        $objConjuntoEstilosDTO = $objConjuntoEstilosRN->consultar($objConjuntoEstilosDTO);
        $parObjEditorDTO->setNumIdConjuntoEstilos($objConjuntoEstilosDTO->getNumIdConjuntoEstilos());
      }

      if ($parObjEditorDTO->getNumIdBaseConhecimento()!=null) {
        $objBaseConhecimentoDTO = new BaseConhecimentoDTO();
        $objBaseConhecimentoRN = new BaseConhecimentoRN();
        $objBaseConhecimentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimento());
        $objBaseConhecimentoDTO->setNumIdConjuntoEstilos($parObjEditorDTO->getNumIdConjuntoEstilos());
        $objBaseConhecimentoRN->configurarEstilos($objBaseConhecimentoDTO);
      } else {
        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->setDblIdDocumento($parObjEditorDTO->getDblIdDocumento());
        $objDocumentoDTO->setNumIdConjuntoEstilos($parObjEditorDTO->getNumIdConjuntoEstilos());
        $objDocumentoRN->configurarEstilos($objDocumentoDTO);
      }

      $arrConteudoInicalSecoes = null;
      if ($parObjEditorDTO->isSetArrConteudoInicialSecoes()) {
        $arrConteudoInicalSecoes = $parObjEditorDTO->getArrConteudoInicialSecoes();
      }

      if (!$parObjEditorDTO->isSetDblIdDocumentoBase() && !$parObjEditorDTO->isSetNumIdBaseConhecimentoBase()) {

        //recupera seções do modelo
        $objSecaoModeloDTO = new SecaoModeloDTO();
        $objSecaoModeloDTO->retNumIdSecaoModelo();
        $objSecaoModeloDTO->retStrNome();
        $objSecaoModeloDTO->retStrSinSomenteLeitura();
        $objSecaoModeloDTO->retStrSinAssinatura();
        $objSecaoModeloDTO->retStrSinPrincipal();
        $objSecaoModeloDTO->retStrSinDinamica();
        $objSecaoModeloDTO->retStrSinCabecalho();
        $objSecaoModeloDTO->retStrSinRodape();
        $objSecaoModeloDTO->retStrSinHtml();
        $objSecaoModeloDTO->retStrConteudo();
        $objSecaoModeloDTO->retNumOrdem();
        $objSecaoModeloDTO->setNumIdModelo($parObjEditorDTO->getNumIdModelo());
        $objSecaoModeloDTO->setOrdNumOrdem(InfraDTO::$TIPO_ORDENACAO_ASC);

        $arrObjSecaoModeloDTO = $objSecaoModeloRN->listar($objSecaoModeloDTO);

        if (count($arrObjSecaoModeloDTO)==0) {
          throw new InfraException('Modelo do documento não contém seções.');
        }


        //recupera estilos padrão das seções do modelo
        $objRelSecaoModCjEstilosItemDTO = new RelSecaoModCjEstilosItemDTO();
        $objRelSecaoModCjEstilosItemDTO->retNumIdSecaoModelo();
        $objRelSecaoModCjEstilosItemDTO->retStrNomeEstilo();
        $objRelSecaoModCjEstilosItemDTO->setNumIdSecaoModelo(InfraArray::converterArrInfraDTO($arrObjSecaoModeloDTO, 'IdSecaoModelo'), InfraDTO::$OPER_IN);
        $objRelSecaoModCjEstilosItemDTO->setStrSinPadrao('S');
        $objRelSecaoModCjEstilosItemDTO->setNumIdConjuntoEstilos($parObjEditorDTO->getNumIdConjuntoEstilos());
        $objRelSecaoModCjEstilosItemRN = new RelSecaoModCjEstilosItemRN();
        $arrObjRelSecaoModCjEstilosItemDTO = InfraArray::indexarArrInfraDTO($objRelSecaoModCjEstilosItemRN->listar($objRelSecaoModCjEstilosItemDTO), 'IdSecaoModelo');

        $objImagemFormatoDTO = new ImagemFormatoDTO();
        $objImagemFormatoDTO->retStrFormato();
        $objImagemFormatoDTO->setBolExclusaoLogica(false);

        $objImagemFormatoRN = new ImagemFormatoRN();
        $arrImagemPermitida = InfraArray::converterArrInfraDTO($objImagemFormatoRN->listar($objImagemFormatoDTO), 'Formato');
        if (in_array('jpg', $arrImagemPermitida) && !in_array('jpeg', $arrImagemPermitida)) $arrImagemPermitida[] = 'jpeg';

        //gera copia das secoes do modelo, ja formatando o conteudo com a formatacao padrao
        foreach ($arrObjSecaoModeloDTO as $objSecaoModeloDTO) {

          $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
          $objSecaoDocumentoDTO->setDblIdDocumento($parObjEditorDTO->getDblIdDocumento());
          $objSecaoDocumentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimento());
          $objSecaoDocumentoDTO->setNumIdSecaoModelo($objSecaoModeloDTO->getNumIdSecaoModelo());
          $objSecaoDocumentoDTO->setNumOrdem($objSecaoModeloDTO->getNumOrdem());
          $objSecaoDocumentoDTO->setStrSinSomenteLeitura($objSecaoModeloDTO->getStrSinSomenteLeitura());
          $objSecaoDocumentoDTO->setStrSinAssinatura($objSecaoModeloDTO->getStrSinAssinatura());
          $objSecaoDocumentoDTO->setStrSinPrincipal($objSecaoModeloDTO->getStrSinPrincipal());
          $objSecaoDocumentoDTO->setStrSinDinamica($objSecaoModeloDTO->getStrSinDinamica());
          $objSecaoDocumentoDTO->setStrSinHtml($objSecaoModeloDTO->getStrSinHtml());
          $objSecaoDocumentoDTO->setStrSinCabecalho($objSecaoModeloDTO->getStrSinCabecalho());
          $objSecaoDocumentoDTO->setStrSinRodape($objSecaoModeloDTO->getStrSinRodape());
          $objSecaoDocumentoDTO->setStrConteudo($objSecaoModeloDTO->getStrConteudo());

          $objSecaoDocumentoDTO = $objSecaoDocumentoRN->cadastrar($objSecaoDocumentoDTO);

          if ($objSecaoModeloDTO->getStrSinAssinatura()=='N') {

            //cadastra primeiro registro de versão da seção
            $objVersaoSecaoDocumentoDTO = new VersaoSecaoDocumentoDTO();
            $objVersaoSecaoDocumentoDTO->setDblIdVersaoSecaoDocumento(null);
            $objVersaoSecaoDocumentoDTO->setNumIdSecaoDocumento($objSecaoDocumentoDTO->getNumIdSecaoDocumento());

            $strEstiloPadrao = '';
            if (isset($arrObjRelSecaoModCjEstilosItemDTO[$objSecaoModeloDTO->getNumIdSecaoModelo()])) {
              $strEstiloPadrao = 'class="' . $arrObjRelSecaoModCjEstilosItemDTO[$objSecaoModeloDTO->getNumIdSecaoModelo()]->getStrNomeEstilo() . '"';
            }

            $strConteudo = '';
            $bolConteudoEdoc = false;
            $bolConteudoSecaoPrincipal = false;
            $bolConteudoTextoPadrao = false;
            $bolConteudoTextoBase = false;

            //conteúdo informado especificamente para esta seção
            if ($arrConteudoInicalSecoes!=null && isset($arrConteudoInicalSecoes[$objSecaoModeloDTO->getStrNome()])) {
              $strConteudo = $arrConteudoInicalSecoes[$objSecaoModeloDTO->getStrNome()];

              //se deve copiar o conteúdo de um documento do eDoc então aplica na seção principal do documento
            } else if ($objSecaoModeloDTO->getStrSinPrincipal()=='S' && $parObjEditorDTO->isSetDblIdDocumentoEdocBase()) {

              $objDocumentoDTO = new DocumentoDTO();
              $objDocumentoDTO->setDblIdDocumentoEdoc($parObjEditorDTO->getDblIdDocumentoEdocBase());

              $objEDocRN = new EDocRN();
              $strConteudo = EDocINT::converterParaEditorInterno($objEDocRN->consultarHTMLPublicacaoDocumento($objDocumentoDTO));
              $bolConteudoEdoc = true;

              //configurar conteudo da seção editável com o conteúdo da mesma seção no documento usado para texto base
            } else if ($objSecaoModeloDTO->getStrSinSomenteLeitura()=='N' && $parObjEditorDTO->isSetDblIdDocumentoTextoBase()) {

              $objVersaoSecaoDocumentoDTOTextoBase = new VersaoSecaoDocumentoDTO();
              $objVersaoSecaoDocumentoDTOTextoBase->retStrConteudo();
              $objVersaoSecaoDocumentoDTOTextoBase->setStrSinUltima('S');
              $objVersaoSecaoDocumentoDTOTextoBase->setDblIdDocumentoSecaoDocumento($parObjEditorDTO->getDblIdDocumentoTextoBase());
              $objVersaoSecaoDocumentoDTOTextoBase->setStrNomeSecaoModelo($objSecaoModeloDTO->getStrNome());

              $objVersaoSecaoDocumentoDTOTextoBase = $objVersaoSecaoDocumentoRN->consultar($objVersaoSecaoDocumentoDTOTextoBase);

              if ($objVersaoSecaoDocumentoDTOTextoBase!=null) {
                $strConteudo = $objVersaoSecaoDocumentoDTOTextoBase->getStrConteudo();
                $bolConteudoTextoBase = true;
              }

              //conteudo informado para seção principal
            } else if ($objSecaoModeloDTO->getStrSinPrincipal()=='S' && $parObjEditorDTO->isSetStrConteudoSecaoPrincipal()) {
              $strConteudo = $parObjEditorDTO->getStrConteudoSecaoPrincipal();
              $bolConteudoSecaoPrincipal = true;

              //texto padrão deve ser aplicado na seção principal
            } else if ($objSecaoModeloDTO->getStrSinPrincipal()=='S' && $parObjEditorDTO->isSetNumIdTextoPadraoInterno()) {

              $objTextoPadraoInternoDTO = new TextoPadraoInternoDTO();
              $objTextoPadraoInternoDTO->retStrConteudo();
              $objTextoPadraoInternoDTO->retNumIdConjuntoEstilos();
              $objTextoPadraoInternoDTO->setNumIdTextoPadraoInterno($parObjEditorDTO->getNumIdTextoPadraoInterno());

              $objTextoPadraoInternoRN = new TextoPadraoInternoRN();
              $objTextoPadraoInternoDTO = $objTextoPadraoInternoRN->consultar($objTextoPadraoInternoDTO);

              $strConteudo = $objTextoPadraoInternoDTO->getStrConteudo();
              $bolConteudoTextoPadrao = true;

              //coloca conteúdo inicial definido no modelo
            } else {
              $strConteudo = $objSecaoModeloDTO->getStrConteudo();
            }

            if (trim($strConteudo)=='') {
              if ($objSecaoModeloDTO->getStrSinSomenteLeitura()=='S') {
                $objVersaoSecaoDocumentoDTO->setStrConteudo(null);
              } else {
                $objVersaoSecaoDocumentoDTO->setStrConteudo('<p ' . $strEstiloPadrao . '>' . "\r\n\t" . '&nbsp;</p>' . "\r\n");
              }
            } else {

              //efetua limpeza de tags para documentos gerados com conteudo inicial
              //$strConteudo = $this->limparTagsCriticas($strConteudo);
              $this->validarTagsCriticas($arrImagemPermitida, $strConteudo);
              $strConteudo=$this->processarLinksSei($strConteudo);

              if ($bolConteudoTextoBase) {

                $objVersaoSecaoDocumentoDTO->setStrConteudo($strConteudo);

              } else {

                foreach ($arrTags as $arrTag) {
                  $strConteudo = str_replace($arrTag[0], $arrTag[1], $strConteudo);
                }

                if ($bolConteudoEdoc || $bolConteudoTextoPadrao || $bolConteudoSecaoPrincipal) {
                  $objVersaoSecaoDocumentoDTO->setStrConteudo($strConteudo);
                } else { //conteúdo inicial de seção (ex.: nome da base de conhecimento passada para a seção de título) ou conteúdo definido nas seções do modelo

                  if ($objSecaoModeloDTO->getStrSinHtml() == 'N') {

                    $strConteudoFormatado = '';
                    $arrConteudo = explode("\n", $strConteudo);
                    foreach ($arrConteudo as $strItemConteudo) {
                      $strConteudoFormatado .= '<p ' . $strEstiloPadrao . '>' . "\r\n\t" . $strItemConteudo . '</p>' . "\r\n";
                    }

                    $objVersaoSecaoDocumentoDTO->setStrConteudo($strConteudoFormatado);

                  } else {
                    $objVersaoSecaoDocumentoDTO->setStrConteudo($strConteudo);
                  }
                }
              }
            }

            $objVersaoSecaoDocumentoDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
            $objVersaoSecaoDocumentoDTO->setNumIdUnidade( $idUnidadeResponsavel );
            $objVersaoSecaoDocumentoDTO->setDthAtualizacao($dthAtual);
            $objVersaoSecaoDocumentoDTO->setNumVersao(1);

            $objVersaoSecaoDocumentoRN->cadastrar($objVersaoSecaoDocumentoDTO);
          }
        }
        
      } else {

        $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
        $objSecaoDocumentoDTO->retStrNomeSecaoModelo();
        $objSecaoDocumentoDTO->retNumIdSecaoDocumento();
        $objSecaoDocumentoDTO->retNumIdSecaoModelo();
        $objSecaoDocumentoDTO->retNumOrdem();
        $objSecaoDocumentoDTO->retStrSinSomenteLeitura();
        $objSecaoDocumentoDTO->retStrSinAssinatura();
        $objSecaoDocumentoDTO->retStrSinPrincipal();
        $objSecaoDocumentoDTO->retStrSinDinamica();
        $objSecaoDocumentoDTO->retStrSinHtml();
        $objSecaoDocumentoDTO->retStrSinCabecalho();
        $objSecaoDocumentoDTO->retStrSinRodape();
        $objSecaoDocumentoDTO->retStrConteudo();

        if ($parObjEditorDTO->isSetDblIdDocumentoBase()) {
          $objSecaoDocumentoDTO->setDblIdDocumento($parObjEditorDTO->getDblIdDocumentoBase());
        }

        if ($parObjEditorDTO->isSetNumIdBaseConhecimentoBase()) {
          $objSecaoDocumentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimentoBase());
        }

        $arrObjSecaoDocumentoDTOBase = $objSecaoDocumentoRN->listar($objSecaoDocumentoDTO);

        //bloquear registros de versão
        $objVersaoSecaoDocumentoDTO = new VersaoSecaoDocumentoDTO();
        $objVersaoSecaoDocumentoDTO->retDblIdVersaoSecaoDocumento();
        $objVersaoSecaoDocumentoDTO->retNumIdSecaoDocumento();
        $objVersaoSecaoDocumentoDTO->retStrConteudo();
        $objVersaoSecaoDocumentoDTO->setNumIdSecaoDocumento(InfraArray::converterArrInfraDTO($arrObjSecaoDocumentoDTOBase, 'IdSecaoDocumento'), InfraDTO::$OPER_IN);
        $objVersaoSecaoDocumentoDTO->setStrSinUltima('S');

        $arrObjVersaoSecaoDocumentoDTOBase = InfraArray::indexarArrInfraDTO($objVersaoSecaoDocumentoRN->listar($objVersaoSecaoDocumentoDTO), 'IdSecaoDocumento');

        //busca estilo padrao da secao
        $objRelSecaoModCjEstilosItemDTO = new RelSecaoModCjEstilosItemDTO();
        $objRelSecaoModCjEstilosItemDTO->retNumIdSecaoModelo();
        $objRelSecaoModCjEstilosItemDTO->retStrNomeEstilo();
        $objRelSecaoModCjEstilosItemDTO->setNumIdSecaoModelo(InfraArray::converterArrInfraDTO($arrObjSecaoDocumentoDTOBase, 'IdSecaoModelo'), InfraDTO::$OPER_IN);
        $objRelSecaoModCjEstilosItemDTO->setStrSinPadrao('S');
        $objRelSecaoModCjEstilosItemDTO->setNumIdConjuntoEstilos($parObjEditorDTO->getNumIdConjuntoEstilos());
        $objRelSecaoModCjEstilosItemRN = new RelSecaoModCjEstilosItemRN();
        $arrObjRelSecaoModCjEstilosItemDTO = InfraArray::indexarArrInfraDTO($objRelSecaoModCjEstilosItemRN->listar($objRelSecaoModCjEstilosItemDTO), 'IdSecaoModelo');

        foreach ($arrObjSecaoDocumentoDTOBase as $objSecaoDocumentoDTOBase) {

          $objSecaoDocumentoDTO = clone($objSecaoDocumentoDTOBase);
          $objSecaoDocumentoDTO->setDblIdDocumento($parObjEditorDTO->getDblIdDocumento());
          $objSecaoDocumentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimento());
          $objSecaoDocumentoDTO = $objSecaoDocumentoRN->cadastrar($objSecaoDocumentoDTO);

          if ($objSecaoDocumentoDTOBase->getStrSinAssinatura()=='N') {

            $objVersaoSecaoDocumentoDTO = new VersaoSecaoDocumentoDTO();
            $objVersaoSecaoDocumentoDTO->setDblIdVersaoSecaoDocumento(null);
            $objVersaoSecaoDocumentoDTO->setNumIdSecaoDocumento($objSecaoDocumentoDTO->getNumIdSecaoDocumento());


            $strEstiloPadrao = '';
            if (isset($arrObjRelSecaoModCjEstilosItemDTO[$objSecaoDocumentoDTOBase->getNumIdSecaoModelo()])) {
              $strEstiloPadrao = 'class="' . $arrObjRelSecaoModCjEstilosItemDTO[$objSecaoDocumentoDTOBase->getNumIdSecaoModelo()]->getStrNomeEstilo() . '"';
            }

            //conteúdo informado especificamente para esta seção
            if ($arrConteudoInicalSecoes!=null && isset($arrConteudoInicalSecoes[$objSecaoDocumentoDTOBase->getStrNomeSecaoModelo()])) {

              $strConteudo = $arrConteudoInicalSecoes[$objSecaoDocumentoDTOBase->getStrNomeSecaoModelo()];

              if ($objSecaoDocumentoDTOBase->getStrSinHtml()=='N') {
                $strConteudoFormatado = '';
                $arrConteudo = explode("\n", $strConteudo);
                foreach ($arrConteudo as $strItemConteudo) {
                  $strConteudoFormatado .= '<p ' . $strEstiloPadrao . '>' . "\r\n\t" . $strItemConteudo . '</p>' . "\r\n";
                }
                $strConteudo = $strConteudoFormatado;
              }

            } else if ($objSecaoDocumentoDTOBase->getStrSinSomenteLeitura()=='S' || $objSecaoDocumentoDTOBase->getStrSinDinamica()=='S') {

              $strConteudo = $objSecaoDocumentoDTOBase->getStrConteudo();

              foreach ($arrTags as $arrTag) {
                $strConteudo = str_replace($arrTag[0], $arrTag[1], $strConteudo);
              }

              if ($objSecaoDocumentoDTOBase->getStrSinHtml()=='N') {
                $strConteudoFormatado = '';
                $arrConteudo = explode("\n", $strConteudo);
                foreach ($arrConteudo as $strItemConteudo) {
                  $strConteudoFormatado .= '<p ' . $strEstiloPadrao . '>' . "\r\n\t" . $strItemConteudo . '</p>' . "\r\n";
                }
                $strConteudo = $strConteudoFormatado;
              }

            } else {
              $strConteudo = $arrObjVersaoSecaoDocumentoDTOBase[$objSecaoDocumentoDTOBase->getNumIdSecaoDocumento()]->getStrConteudo();
            }

            $objVersaoSecaoDocumentoDTO->setStrConteudo($strConteudo);
            $objVersaoSecaoDocumentoDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
            $objVersaoSecaoDocumentoDTO->setNumIdUnidade( $idUnidadeResponsavel );
            $objVersaoSecaoDocumentoDTO->setDthAtualizacao($dthAtual);
            $objVersaoSecaoDocumentoDTO->setNumVersao(1);

            $objVersaoSecaoDocumentoRN->cadastrar($objVersaoSecaoDocumentoDTO);
          }
        }
      }

      //cadastrar conjunto de estilos
      $this->atualizarConteudo($parObjEditorDTO);

    } catch (Exception $e) {
      throw new InfraException('Erro gerando versão inicial documento.', $e);
    }
  }

  protected function adicionarVersaoControlado(EditorDTO $parObjEditorDTO)
  {
    try {
    	
      //preciso obter a unidade do documento
      $docDTO = new DocumentoDTO();
      $docRN = new DocumentoRN();
      $docDTO->retDblIdDocumento();
      $docDTO->retNumIdUnidadeResponsavel();
      $docDTO->setDblIdDocumento( $parObjEditorDTO->getDblIdDocumento() );
    	
      $docDTO = $docRN->consultarRN0005( $docDTO );
      $idUnidadeResponsavel = $docDTO->getNumIdUnidadeResponsavel();
    	
      $objInfraException = new InfraException();

      if ($parObjEditorDTO->getDblIdDocumento()!=null) {

        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->retNumIdConjuntoEstilos();
        $objDocumentoDTO->setDblIdDocumento($parObjEditorDTO->getDblIdDocumento());

        $objDocumentoRN = new DocumentoRN();
        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

        if ($objDocumentoDTO==null) {
          $objInfraException->lancarValidacao('Documento não encontrado.');
        } else {

          $numIdConjuntoEstilos = $objDocumentoDTO->getNumIdConjuntoEstilos();

          $objPesquisaProtocoloDTO = new PesquisaProtocoloDTO();
          $objPesquisaProtocoloDTO->setStrStaTipo(ProtocoloRN::$TPP_DOCUMENTOS_GERADOS);
          $objPesquisaProtocoloDTO->setStrStaAcesso(ProtocoloRN::$TAP_TODOS);
          $objPesquisaProtocoloDTO->setDblIdProtocolo($parObjEditorDTO->getDblIdDocumento());

          $objProtocoloRN = new ProtocoloRN();
          $arrObjProtocoloDTO = $objProtocoloRN->pesquisarRN0967($objPesquisaProtocoloDTO);

          if (count($arrObjProtocoloDTO) == 0){
            $objInfraException->lancarValidacao('Protocolo não encontrado.');
          }

          $objProtocoloDTO = $arrObjProtocoloDTO[0];

          if ($objProtocoloDTO->getNumCodigoAcesso() < 0) {
            if ($objProtocoloDTO->getStrStaNivelAcessoGlobal()==ProtocoloRN::$NA_SIGILOSO) {
              $objInfraException->lancarValidacao('Usuário sem acesso para alteração do documento.');
            }else{
              $objInfraException->lancarValidacao('Unidade sem acesso para alteração do documento.');
            }
          }

          $objProcedimentoDTO = new ProcedimentoDTO();
          $objProcedimentoDTO->setDblIdProcedimento($objProtocoloDTO->getDblIdProcedimentoDocumento());
          $objProcedimentoDTO->setStrSinDocTodos('S');
          $objProcedimentoDTO->setArrDblIdProtocoloAssociado(array($parObjEditorDTO->getDblIdDocumento()));

          $objProcedimentoRN = new ProcedimentoRN();
          $arrObjProcedimentoDTO = $objProcedimentoRN->listarCompleto($objProcedimentoDTO);

          if (count($arrObjProcedimentoDTO) == 0){
            $objInfraException->lancarValidacao('Processo não encontrado.');
          }

          $arrObjRelProtocoloProtocoloDTO = $arrObjProcedimentoDTO[0]->getArrObjRelProtocoloProtocoloDTO();

          if (count($arrObjRelProtocoloProtocoloDTO) == 0){
            $objInfraException->lancarValidacao('Documento não encontrado.');
          }

          $objDocumentoDTO = $arrObjRelProtocoloProtocoloDTO[0]->getObjProtocoloDTO2();

          if ($objDocumentoDTO->getStrSinPublicado() == 'S'){
            $objInfraException->lancarValidacao('Documento foi publicado.');
          }

          if ($objDocumentoDTO->getStrSinBloqueado() == 'S'){
            $objInfraException->lancarValidacao('Documento foi assinado e não pode mais ser alterado.');
          }

          if (SessaoSEIExterna::getInstance()->getNumIdUnidadeAtual() != $objDocumentoDTO->getNumIdUnidadeGeradoraProtocolo()){

            if ($objDocumentoDTO->getStrSinAssinadoPorOutraUnidade() == 'S') {
              $objInfraException->lancarValidacao('Documento foi assinado em outra unidade.');
            }

          }else {

            if ((!$parObjEditorDTO->isSetStrSinMontandoEditor() || $parObjEditorDTO->getStrSinMontandoEditor()=='N') && $objProtocoloDTO->getStrSinAssinado() == 'S'){
              $objInfraException->lancarValidacao('Documento foi assinado.');
            }

            if ($objProtocoloDTO->getStrSinDisponibilizadoParaOutraUnidade() == 'S'){
              $objInfraException->lancarValidacao('Documento disponibilizado em bloco de assinatura.');
            }

          }

          if ($objDocumentoDTO->getStrSinAssinado() == 'S') {
            $parObjEditorDTO->setStrSinForcarNovaVersao('S');
          }

          if ($numIdConjuntoEstilos==null || ($parObjEditorDTO->isSetStrSinForcarNovaVersao() && $parObjEditorDTO->getStrSinForcarNovaVersao()=='S')){
            $this->converterDocumento($parObjEditorDTO);
          } else {
            $parObjEditorDTO->setNumIdConjuntoEstilos($numIdConjuntoEstilos);
          }
        }

      } else {
        $objBaseConhecimentoDTO = new BaseConhecimentoDTO();
        $objBaseConhecimentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimento());
        $objBaseConhecimentoDTO->retNumIdConjuntoEstilos();

        $objBaseConhecimentoRN = new BaseConhecimentoRN();
        $objBaseConhecimentoDTO = $objBaseConhecimentoRN->consultar($objBaseConhecimentoDTO);
        if ($objBaseConhecimentoDTO==null) {
          $objInfraException->lancarValidacao('Base de conhecimento não encontrada.');
        } else {
          if ($objBaseConhecimentoDTO->getNumIdConjuntoEstilos()==null ||
              ($parObjEditorDTO->isSetStrSinForcarNovaVersao() && $parObjEditorDTO->getStrSinForcarNovaVersao()=='S')
          ) {
            $this->converterDocumento($parObjEditorDTO);
          } else {
            $parObjEditorDTO->setNumIdConjuntoEstilos($objBaseConhecimentoDTO->getNumIdConjuntoEstilos());
          }
        }
      }

      $dthAtual = InfraData::getStrDataHoraAtual();

      $arrObjSecaoDocumentoDTO = $parObjEditorDTO->getArrObjSecaoDocumentoDTO();

      if (count($arrObjSecaoDocumentoDTO)==0) {
        throw new InfraException('Documento sem seções.');
      }

      $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
      $objSecaoDocumentoDTO->retNumIdSecaoDocumento();
      $objSecaoDocumentoDTO->retNumIdSecaoModelo();
      $objSecaoDocumentoDTO->retStrSinDinamica();
      $objSecaoDocumentoDTO->retStrSinSomenteLeitura();
      $objSecaoDocumentoDTO->retStrSinHtml();
      $objSecaoDocumentoDTO->retStrSinCabecalho();
      $objSecaoDocumentoDTO->retStrSinRodape();
      $objSecaoDocumentoDTO->retStrConteudo();
      $objSecaoDocumentoDTO->setDblIdDocumento($parObjEditorDTO->getDblIdDocumento());
      $objSecaoDocumentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimento());
      $objSecaoDocumentoDTO->setStrSinAssinatura('N');

      $objSecaoDocumentoRN = new SecaoDocumentoRN();
      $arrObjSecaoDocumentoDTOBanco = $objSecaoDocumentoRN->listar($objSecaoDocumentoDTO);

      $numSecoesDocumento = count($arrObjSecaoDocumentoDTO);
      $numSecoesDocumentoBanco = count($arrObjSecaoDocumentoDTOBanco);

      if ($numSecoesDocumentoBanco!=$numSecoesDocumento) {
        throw new InfraException('Número de seções do documento inconsistente.');
      }

      for ($i = 0; $i<$numSecoesDocumentoBanco; $i++) {
        for ($j = 0; $j<$numSecoesDocumento; $j++) {
          if ($arrObjSecaoDocumentoDTOBanco[$i]->getNumIdSecaoModelo()==$arrObjSecaoDocumentoDTO[$j]->getNumIdSecaoModelo()) {
            $arrObjSecaoDocumentoDTO[$j]->setNumIdSecaoDocumento($arrObjSecaoDocumentoDTOBanco[$i]->getNumIdSecaoDocumento());
            $arrObjSecaoDocumentoDTO[$j]->setStrSinDinamica($arrObjSecaoDocumentoDTOBanco[$i]->getStrSinDinamica());
            $arrObjSecaoDocumentoDTO[$j]->setStrSinSomenteLeitura($arrObjSecaoDocumentoDTOBanco[$i]->getStrSinSomenteLeitura());
            $arrObjSecaoDocumentoDTO[$j]->setStrSinHtml($arrObjSecaoDocumentoDTOBanco[$i]->getStrSinHtml());
            $arrObjSecaoDocumentoDTO[$j]->setStrSinCabecalho($arrObjSecaoDocumentoDTOBanco[$i]->getStrSinCabecalho());
            $arrObjSecaoDocumentoDTO[$j]->setStrSinRodape($arrObjSecaoDocumentoDTOBanco[$i]->getStrSinRodape());
            $arrObjSecaoDocumentoDTO[$j]->setStrConteudoOriginal($arrObjSecaoDocumentoDTOBanco[$i]->getStrConteudo());
            break;
          }
        }
        if ($j==$numSecoesDocumento) {
          throw new InfraException('Seção [' . $arrObjSecaoDocumentoDTOBanco[$i]->getNumIdSecaoModelo() . '] do documento não encontrada.');
        }
      }

      $arrTags = null;
      foreach ($arrObjSecaoDocumentoDTO as $objSecaoDocumentoDTO) {
        if ($objSecaoDocumentoDTO->getStrSinDinamica()=='S') {
          $objParametrosEditorDTO = $this->obterParametros($parObjEditorDTO);
          $arrTags = $objParametrosEditorDTO->getArrTags();
          break;
        }
      }

      //bloquear registros de versão
      $objVersaoSecaoDocumentoDTO = new VersaoSecaoDocumentoDTO();
      $objVersaoSecaoDocumentoDTO->retDblIdVersaoSecaoDocumento();
      $objVersaoSecaoDocumentoDTO->retNumIdSecaoDocumento();
      $objVersaoSecaoDocumentoDTO->retStrSiglaUsuario();
      $objVersaoSecaoDocumentoDTO->retStrNomeUsuario();
      $objVersaoSecaoDocumentoDTO->retDthAtualizacao();
      $objVersaoSecaoDocumentoDTO->retStrConteudo();
      $objVersaoSecaoDocumentoDTO->retNumVersao();
      $objVersaoSecaoDocumentoDTO->setNumIdSecaoDocumento(InfraArray::converterArrInfraDTO($arrObjSecaoDocumentoDTOBanco, 'IdSecaoDocumento'), InfraDTO::$OPER_IN);
      $objVersaoSecaoDocumentoDTO->setStrSinUltima('S');

      $objVersaoSecaoDocumentoRN = new VersaoSecaoDocumentoRN();

      $arrObjVersaoSecaoDocumentoDTO = $objVersaoSecaoDocumentoRN->listar($objVersaoSecaoDocumentoDTO);

      $numVersao = 0;
      $objVersaoSecaoDocumentoDTOUltima = null;
      foreach ($arrObjVersaoSecaoDocumentoDTO as $dto) {
        if ($dto->getNumVersao()>$numVersao) {
          $numVersao = $dto->getNumVersao();
          $objVersaoSecaoDocumentoDTOUltima = $dto;
        }
      }

      if (count($arrObjVersaoSecaoDocumentoDTO)!=$numSecoesDocumento) {
        throw new InfraException('Número de seções da última versão não corresponde ao número de seções do documento.');
      }

      if ($parObjEditorDTO->isSetNumVersao() && $parObjEditorDTO->getNumVersao()!=$numVersao) {
        if (!$parObjEditorDTO->isSetStrSinIgnorarNovaVersao() || $parObjEditorDTO->getStrSinIgnorarNovaVersao()=='N') {
          //IMPORTANTE: o texto da validacao é verificado na interface, se houver mudança deve ser refletida no ponto correspondente da interface
          $objInfraException->lancarValidacao('Existe uma nova versão (nº ' . $numVersao . ') para este documento atualizada por ' . $objVersaoSecaoDocumentoDTOUltima->getStrSiglaUsuario() . ' (' . $objVersaoSecaoDocumentoDTOUltima->getStrNomeUsuario() . ') em ' . $objVersaoSecaoDocumentoDTOUltima->getDthAtualizacao() . '.');
        }
      }


      //aplica estilo padrao da secao
      $objRelSecaoModCjEstilosItemDTO = new RelSecaoModCjEstilosItemDTO();
      $objRelSecaoModCjEstilosItemDTO->retNumIdSecaoModelo();
      $objRelSecaoModCjEstilosItemDTO->retStrNomeEstilo();
      $objRelSecaoModCjEstilosItemDTO->setNumIdSecaoModelo(InfraArray::converterArrInfraDTO($arrObjSecaoDocumentoDTO, 'IdSecaoModelo'), InfraDTO::$OPER_IN);
      $objRelSecaoModCjEstilosItemDTO->setStrSinPadrao('S');
      $objRelSecaoModCjEstilosItemDTO->setNumIdConjuntoEstilos($parObjEditorDTO->getNumIdConjuntoEstilos());
      $objRelSecaoModCjEstilosItemRN = new RelSecaoModCjEstilosItemRN();
      $arrObjRelSecaoModCjEstilosItemDTO = InfraArray::indexarArrInfraDTO($objRelSecaoModCjEstilosItemRN->listar($objRelSecaoModCjEstilosItemDTO), 'IdSecaoModelo');

      $arrEstilosFormatados = array();
      foreach ($arrObjRelSecaoModCjEstilosItemDTO as $objRelSecaoModCjEstilosItemDTO) {
        $strEstiloFormatado = 'class="' . $objRelSecaoModCjEstilosItemDTO->getStrNomeEstilo() . '"';
        $arrEstilosFormatados[$objRelSecaoModCjEstilosItemDTO->getNumIdSecaoModelo()] = $strEstiloFormatado;
      }

      $bolSecaoAlterada = false;

      $objImagemFormatoDTO = new ImagemFormatoDTO();
      $objImagemFormatoDTO->retStrFormato();
      $objImagemFormatoDTO->setBolExclusaoLogica(false);
      $objImagemFormatoRN = new ImagemFormatoRN();
      $arrImagemPermitida = InfraArray::converterArrInfraDTO($objImagemFormatoRN->listar($objImagemFormatoDTO), 'Formato');
      if (in_array('jpg', $arrImagemPermitida) && !in_array('jpeg', $arrImagemPermitida)) $arrImagemPermitida[] = 'jpeg';

      foreach ($arrObjSecaoDocumentoDTO as $objSecaoDocumentoDTO) {
        foreach ($arrObjVersaoSecaoDocumentoDTO as $objVersaoSecaoDocumentoDTO) {
          if ($objSecaoDocumentoDTO->getNumIdSecaoDocumento()==$objVersaoSecaoDocumentoDTO->getNumIdSecaoDocumento()) {
            $strConteudo = $this->montarConteudoSecao($objSecaoDocumentoDTO, $arrEstilosFormatados, $arrTags, $numVersao);
            $strConteudo=$this->processarLinksSei($strConteudo);
            if ($objSecaoDocumentoDTO->getStrSinCabecalho()=='N' && $objSecaoDocumentoDTO->getStrSinRodape()=='N') {
              $this->validarTagsCriticas($arrImagemPermitida, $strConteudo);
            }
            if ($strConteudo!=$objVersaoSecaoDocumentoDTO->getStrConteudo()) {
              $bolSecaoAlterada = true;
            }
            break;
          }
        }
      }
      if ($bolSecaoAlterada || ($parObjEditorDTO->isSetStrSinForcarNovaVersao() && $parObjEditorDTO->getStrSinForcarNovaVersao()=='S')) {
        $numVersao++;
        foreach ($arrObjSecaoDocumentoDTO as $objSecaoDocumentoDTO) {
          foreach ($arrObjVersaoSecaoDocumentoDTO as $objVersaoSecaoDocumentoDTO) {
            if ($objSecaoDocumentoDTO->getNumIdSecaoDocumento()==$objVersaoSecaoDocumentoDTO->getNumIdSecaoDocumento()) {
              $strConteudo = $this->montarConteudoSecao($objSecaoDocumentoDTO, $arrEstilosFormatados, $arrTags, $numVersao);
              $strConteudo=$this->processarLinksSei($strConteudo);
              if ($strConteudo!=$objVersaoSecaoDocumentoDTO->getStrConteudo()) {
                $objVersaoSecaoDocumentoRN->anular($objVersaoSecaoDocumentoDTO);
                $dto = new VersaoSecaoDocumentoDTO();
                $dto->setNumIdSecaoDocumento($objSecaoDocumentoDTO->getNumIdSecaoDocumento());
                $dto->setStrConteudo($strConteudo);
                $dto->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
                $dto->setNumIdUnidade( $idUnidadeResponsavel );
                $dto->setDthAtualizacao($dthAtual);
                $dto->setNumVersao($numVersao);
                $objVersaoSecaoDocumentoRN->cadastrar($dto);
              }
              break;
            }
          }
        }
        $this->atualizarConteudo($parObjEditorDTO);
      }

      return $numVersao;

    } catch (Exception $e) {
      throw new InfraException('Erro adicionando versão do documento.', $e);
    }
  }

  protected function getArrayCssConectado($numIdConjuntoEstilos)
  {
    /*
     * transforma o conjunto de estilos em um array
     * p.Texto_Centralizado {font-size:1;text-align:center;}
     *
     * -> array { [Texto_Centralizado] => array {
     *                    [font-size] => "1"
     *                    [text-align] => "center"
     *                    }
     *          }
     */
    $strCss = $this->montarCssEditor($numIdConjuntoEstilos);
    //seleciona classes p.[nome_estilo]
    preg_match_all("%p\\.([^\\s]*) {([^}]*)}%", $strCss, $arrClassesCss);

    $arrResult = array();
    //para cada classe css
    for ($i = 0; $i<count($arrClassesCss[1]); $i++) {
      //cria item no array de resultado com nome da classe css
      $arrResult[$arrClassesCss[1][$i]] = array();
      //explode os atributos da classe (estilos)
      $arrEstilos = explode(';', $arrClassesCss[2][$i]);
      foreach ($arrEstilos as $value) {
        //se não for vazio
        if (strlen($value)>0) {
          $arrValor = explode(':', $value);
          //inclui no arrResult[nome_do_estilo][atributo]=valor_atributo;
          if ($arrValor[1]=='0 3pt 0 3pt') $arrValor[1] = '0px 3pt';
          $arrResult[$arrClassesCss[1][$i]][trim($arrValor[0])] = InfraString::transformarCaixaBaixa(trim($arrValor[1]));
        }
      }
    }
    return $arrResult;
  }

  private function comparaEstilo($arrEstilos, $strEstilo)
  {
    // verificar se strestilo está definida em arrestilos
    $strEstilo = str_replace(' 0px', ' 0', $strEstilo);
    $strEstilo = str_replace(' 0pt', ' 0', $strEstilo);
    $arrEstilos2 = array();
    $temp = explode(';', $strEstilo);
    foreach ($temp as $value) {
      //se não for vazio
      if (strlen($value)>0) {
        $arrValor = explode(':', $value);
        //inclui no arrEstilos2[atributo]=valor_atributo;
        if ($arrValor[1]=='0 3pt 0 3pt') $arrValor[1] = '0 3pt';
        $arrEstilos2[InfraString::transformarCaixaBaixa(trim($arrValor[0]))] = InfraString::transformarCaixaBaixa(trim($arrValor[1]));
      }
    }
    $numEstilos2 = count($arrEstilos2);
    //verifica se tem atributos definidos

    if ($numEstilos2>0) {
      //compara com todos os estilos do arrEstilos
      foreach ($arrEstilos as $key => $value) {
        if (!is_array($value[0])) {
          //se tiver mesma quantidade de atributos
          if (count($value)==$numEstilos2) {
            //compara as diferenças, que devem ser 0
            if (count(array_diff_assoc($value, $arrEstilos2))==0 &&
                count(array_diff_assoc($arrEstilos2, $value))==0
            )
              return $key;
          }
        } else {
          foreach ($value as $value2) {

            //se tiver mesma quantidade de atributos
            if (count($value2)==$numEstilos2) {
              //compara as diferenças, que devem ser 0
              if (count(array_diff_assoc($value2, $arrEstilos2))==0 &&
                  count(array_diff_assoc($arrEstilos2, $value2))==0
              )
                return $key;
            }
          }
        }
      }
    }
    return null;
  }

  private function converterDocumento(EditorDTO $parObjEditorDTO)
  {
    try {
      if ($parObjEditorDTO->isSetNumIdConjuntoEstilos() && $parObjEditorDTO->getNumIdConjuntoEstilos()!=null) {
        $arrEstilos = $this->getArrayCss($parObjEditorDTO->getNumIdConjuntoEstilos());
      } else {
        $objConjuntoEstilosRN = new ConjuntoEstilosRN();
        $objConjuntoEstilosDTO = new ConjuntoEstilosDTO();
        $objConjuntoEstilosDTO->setStrSinUltimo('S');
        $objConjuntoEstilosDTO->retNumIdConjuntoEstilos();
        $objConjuntoEstilosDTO = $objConjuntoEstilosRN->consultar($objConjuntoEstilosDTO);
        if ($objConjuntoEstilosDTO==null) throw new InfraException('Erro consultando conjunto de estilos.');
        $arrEstilos = $this->getArrayCss($objConjuntoEstilosDTO->getNumIdConjuntoEstilos());
        $parObjEditorDTO->setNumIdConjuntoEstilos($objConjuntoEstilosDTO->getNumIdConjuntoEstilos());
      }
      $arrObjSecaoDocumentoDTO = $parObjEditorDTO->getArrObjSecaoDocumentoDTO();
      foreach ($arrObjSecaoDocumentoDTO as $objSecaoDocumentoDTO) {
        //converter seção_documento
        $strConteudo = $objSecaoDocumentoDTO->getStrConteudo();
        $objSecaoDocumentoDTO->setStrConteudo($this->converteTextoEstiloCss($arrEstilos, $strConteudo));
        ///////
      }
    } catch (Exception $e) {
      throw new InfraException('Erro convertendo documento.', $e);
    }

    if ($parObjEditorDTO->getDblIdDocumento()!=null) {
      $objDocumentoRN = new DocumentoRN();
      $objDocumentoDTO = new DocumentoDTO();
      $objDocumentoDTO->setDblIdDocumento($parObjEditorDTO->getDblIdDocumento());
      $objDocumentoDTO->setNumIdConjuntoEstilos($parObjEditorDTO->getNumIdConjuntoEstilos());
      $objDocumentoRN->configurarEstilos($objDocumentoDTO);
    } else if ($parObjEditorDTO->getNumIdBaseConhecimento()!=null) {
      $objBaseConhecimentoRN = new BaseConhecimentoRN();
      $objBaseConhecimentoDTO = new BaseConhecimentoDTO();
      $objBaseConhecimentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimento());
      $objBaseConhecimentoDTO->setNumIdConjuntoEstilos($parObjEditorDTO->getNumIdConjuntoEstilos());
      $objBaseConhecimentoRN->configurarEstilos($objBaseConhecimentoDTO);
    }
  }

  public function converteTextoEstiloCss($arrEstilosCss, $strConteudo)
  {

    $strConteudoNovo = "";
    $posAnterior = 0;
    $cntNaoEncontrados = 0;
    $cntEncontrados = 0;
    while (($posAtual = strpos($strConteudo, 'style="', $posAnterior))!==false) {
      //copia conteudo até encontrar style
      $strConteudoNovo .= substr($strConteudo, $posAnterior, $posAtual - $posAnterior);
      $posFimEstilo = strpos($strConteudo, '"', $posAtual + 7);
      if ($posFimEstilo===false) {
        throw new InfraException('Erro localizando fim do estilo.');
      } else if ($posFimEstilo==$posAtual + 7) {
        $posAnterior = $posAtual + 8;
      } else {
        $strEstilo = substr($strConteudo, $posAtual + 7, $posFimEstilo - $posAtual - 7);
        $nomeClasse = $this->comparaEstilo($arrEstilosCss, $strEstilo);
        if ($nomeClasse==null) {
          $cntNaoEncontrados++;
          $posAnterior = $posAtual + 1;
          $strConteudoNovo .= 's';
        } else {
          $posAnterior = $posFimEstilo + 1;
          $cntEncontrados++;
          $strConteudoNovo .= 'class="' . $nomeClasse . '"';
        }
      }
    }
    $strConteudoNovo .= substr($strConteudo, $posAnterior);
    //InfraDebug::getInstance()->gravar("Conversão: encontrados ".strval($cntEncontrados)." não encontrados ".strval($cntNaoEncontrados));
    return $strConteudoNovo;

  }

  private function montarConteudoSecao($objSecaoDocumentoDTO, $arrEstilosFormatados, $arrTags, $numVersao)
  {

    $strConteudo = '';
    $strEstiloPadrao = '';
    if (isset($arrEstilosFormatados[$objSecaoDocumentoDTO->getNumIdSecaoModelo()])) {
      $strEstiloPadrao = $arrEstilosFormatados[$objSecaoDocumentoDTO->getNumIdSecaoModelo()];
    }

    if ($objSecaoDocumentoDTO->getStrSinDinamica()=='S') {

      $strConteudo = $objSecaoDocumentoDTO->getStrConteudoOriginal();

      $strConteudo = str_replace('@versao@', $numVersao, $strConteudo);

      foreach ($arrTags as $arrTag) {
        $strConteudo = str_replace($arrTag[0], $arrTag[1], $strConteudo);
      }

      if ($objSecaoDocumentoDTO->getStrSinSomenteLeitura()=='S') {
        if (trim($strConteudo)!='' && $objSecaoDocumentoDTO->getStrSinHtml()=='N') {
          $strConteudo = '<p ' . $strEstiloPadrao . '>' . "\r\n\t" . $strConteudo . '</p>' . "\r\n";
        }
      }

    } else {
      $strConteudo = $objSecaoDocumentoDTO->getStrConteudo();
      if (trim($strConteudo)=='' && $objSecaoDocumentoDTO->getStrSinSomenteLeitura()=='N') {
        $strConteudo = '<p ' . $strEstiloPadrao . '>' . "\r\n\t" . '&nbsp;</p>' . "\r\n";
      }
    }
    return $strConteudo;
  }

  private function atualizarConteudo(EditorDTO $parObjEditorDTO)
  {
    try {

      $objEditorDTO = new EditorDTO();
      $objEditorDTO->setDblIdDocumento($parObjEditorDTO->getDblIdDocumento());
      $objEditorDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimento());
      $objEditorDTO->setStrSinCabecalho('N');
      $objEditorDTO->setStrSinRodape('N');
      $objEditorDTO->setStrSinIdentificacaoVersao('N');
      $objEditorDTO->setNumIdConjuntoEstilos($parObjEditorDTO->getNumIdConjuntoEstilos());

      $strHtml = $this->consultarHtmlVersao($objEditorDTO);

      if ($parObjEditorDTO->getDblIdDocumento()!=null) {

        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->setStrConteudo($strHtml);
        $objDocumentoDTO->setDblIdDocumento($parObjEditorDTO->getDblIdDocumento());
        
        //alteracoes seiv3
        //checando se ja existe registro em documento_conteudo, se nao existir ainda, cadastra um
        $objDocumentoConteudoDTO = new DocumentoConteudoDTO();
        $objDocumentoConteudoDTO->retTodos();
        $objDocumentoConteudoDTO->setDblIdDocumento( $parObjEditorDTO->getDblIdDocumento() );
        
        $objDocumentoConteudoBD = new DocumentoConteudoBD( $this->getObjInfraIBanco() );
        $objDocumentoConteudoDTO = $objDocumentoConteudoBD->consultar( $objDocumentoConteudoDTO );
        
        //se nao existir ainda, cadastra
        if( $objDocumentoConteudoDTO == null ){
        	
        	$objDocumentoConteudoDTO = new DocumentoConteudoDTO();
        	$objDocumentoConteudoDTO->setDblIdDocumento( $parObjEditorDTO->getDblIdDocumento() );
        	$objDocumentoConteudoDTO->setStrConteudo( $strHtml );
        	$objDocumentoConteudoBD->cadastrar( $objDocumentoConteudoDTO );
        	
        }
        
        $objDocumentoRN = new DocumentoRN();
        $objDocumentoRN->atualizarConteudoRN1205($objDocumentoDTO);

      } else {

        $objBaseConhecimentoDTO = new BaseConhecimentoDTO();
        $objBaseConhecimentoDTO->setStrConteudo($strHtml);
        $objBaseConhecimentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimento());

        $objBaseConhecimentoRN = new BaseConhecimentoRN();
        $objBaseConhecimentoRN->alterar($objBaseConhecimentoDTO);
      }

    } catch (Exception $e) {
      throw new InfraException('Erro atualizando conteúdo.', $e);
    }
  }

  private function consultarHtmlIdentificacaoVersao(EditorDTO $parObjEditorDTO)
  {

    $objVersaoSecaoDocumentoDTO = new VersaoSecaoDocumentoDTO();
    $objVersaoSecaoDocumentoDTO->setDistinct(true);
    $objVersaoSecaoDocumentoDTO->retNumVersao();
    $objVersaoSecaoDocumentoDTO->retStrSiglaUsuario();
    $objVersaoSecaoDocumentoDTO->retStrNomeUsuario();
    $objVersaoSecaoDocumentoDTO->retDthAtualizacao();

    $objVersaoSecaoDocumentoDTO->setDblIdDocumentoSecaoDocumento($parObjEditorDTO->getDblIdDocumento());
    $objVersaoSecaoDocumentoDTO->setNumIdBaseConhecimentoSecaoDocumento($parObjEditorDTO->getNumIdBaseConhecimento());

    if ($parObjEditorDTO->isSetNumVersao()) {
      $objVersaoSecaoDocumentoDTO->setNumVersao($parObjEditorDTO->getNumVersao(), InfraDTO::$OPER_MENOR_IGUAL);
    }

    $objVersaoSecaoDocumentoDTO->setOrdNumVersao(InfraDTO::$TIPO_ORDENACAO_ASC);


    $objVersaoSecaoDocumentoRN = new VersaoSecaoDocumentoRN();
    $arrObjVersaoSecaoDocumentoDTO = $objVersaoSecaoDocumentoRN->listar($objVersaoSecaoDocumentoDTO);

    $qtdVersoes = count($arrObjVersaoSecaoDocumentoDTO);
    $numVersao = 0;
    if ($qtdVersoes) {
      $strSiglaUsuarioGerador = $arrObjVersaoSecaoDocumentoDTO[0]->getStrSiglaUsuario();
      $strNomeUsuarioGerador = $arrObjVersaoSecaoDocumentoDTO[0]->getStrNomeUsuario();

      $strSiglaUsuarioVersao = $arrObjVersaoSecaoDocumentoDTO[$qtdVersoes - 1]->getStrSiglaUsuario();
      $strNomeUsuarioVersao = $arrObjVersaoSecaoDocumentoDTO[$qtdVersoes - 1]->getStrNomeUsuario();
      $numVersao = $arrObjVersaoSecaoDocumentoDTO[$qtdVersoes - 1]->getNumVersao();
      $dthVersao = $arrObjVersaoSecaoDocumentoDTO[$qtdVersoes - 1]->getDthAtualizacao();
    }

    $html = '<hr style="border:1px solid #c0c0c0;" />';
    $html .= 'Criado por ';
    $html .= '<a onclick="alert(\'' . PaginaSEIExterna::getInstance()->formatarParametrosJavascript(PaginaSEIExterna::tratarHTML($strNomeUsuarioGerador)) . '\')" alt="' . $strNomeUsuarioGerador . '" title="' . $strNomeUsuarioGerador . '" style="color:#0066cc;text-decoration:none;cursor:pointer;">' . $strSiglaUsuarioGerador . '</a>';
    $html .= ', versão ' . $numVersao . ' por ';
    $html .= '<a onclick="alert(\'' . PaginaSEIExterna::getInstance()->formatarParametrosJavascript(PaginaSEIExterna::tratarHTML($strNomeUsuarioVersao)) . '\')" alt="' . $strNomeUsuarioVersao . '" title="' . $strNomeUsuarioVersao . '" style="color:#0066cc;text-decoration:none;cursor:pointer;">' . $strSiglaUsuarioVersao . '</a>';
    $html .= ' em ' . $dthVersao . '.' . "\n";

    return $html;
  }

  protected function consultarHtmlVersaoConectado(EditorDTO $parObjEditorDTO)
  {


    if ($parObjEditorDTO->getDblIdDocumento()!=null) {
      $objDocumentoDTO = new DocumentoDTO();
      $objDocumentoDTO->retDblIdDocumento();
      $objDocumentoDTO->retStrNomeSerie();
      $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
      $objDocumentoDTO->retStrProtocoloProcedimentoFormatado();
      $objDocumentoDTO->retStrCrcAssinatura();
      $objDocumentoDTO->retStrQrCodeAssinatura();
      $objDocumentoDTO->retObjPublicacaoDTO();
      $objDocumentoDTO->retNumIdConjuntoEstilos();
      $objDocumentoDTO->retStrSinBloqueado();

      $objDocumentoDTO->retStrStaProtocoloProtocolo();
      $objDocumentoDTO->retNumIdUnidadeGeradoraProtocolo();


      $objDocumentoDTO->setDblIdDocumento($parObjEditorDTO->getDblIdDocumento());

      $objDocumentoRN = new DocumentoRN();
      $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

      if ($objDocumentoDTO==null) {
        throw new InfraException('Documento não encontrado.');
      }

      if ($objDocumentoDTO->getNumIdConjuntoEstilos()!=null) {
        $strConteudoCss = $this->montarCssEditor($objDocumentoDTO->getNumIdConjuntoEstilos());
      } else {
        $strConteudoCss = "";
      }
      $strTitulo = DocumentoINT::montarTitulo($objDocumentoDTO);

      $objDocumentoRN->bloquearConsultado($objDocumentoDTO);

    } else {
      $objBaseConhecimentoDTO = new BaseConhecimentoDTO();
      $objBaseConhecimentoDTO->retNumIdBaseConhecimento();
      $objBaseConhecimentoDTO->retStrDescricao();
      $objBaseConhecimentoDTO->retStrSiglaUnidade();
      $objBaseConhecimentoDTO->retNumIdConjuntoEstilos();
      $objBaseConhecimentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimento());

      $objBaseConhecimentoRN = new BaseConhecimentoRN();
      $objBaseConhecimentoDTO = $objBaseConhecimentoRN->consultar($objBaseConhecimentoDTO);

      if ($objBaseConhecimentoDTO==null) {
        throw new InfraException('Base de conhecimento não encontrada.');
      }

      if ($objBaseConhecimentoDTO->getNumIdConjuntoEstilos()!=null) {
        $strConteudoCss = $this->montarCssEditor($objBaseConhecimentoDTO->getNumIdConjuntoEstilos());
      } else {
        $strConteudoCss = "";
      }
      $strTitulo = BaseConhecimentoINT::montarTitulo($objBaseConhecimentoDTO);
    }

    //regex reset de contadores
    $qtd=preg_match_all('/p\.(\S*) \{[^}]*counter-increment:([^;]*);/',$strConteudoCss,$arrCssContadores);
    if ($qtd>0){
      $arrCssContadores=array_combine($arrCssContadores[1],$arrCssContadores[2]);
    } else {
      $arrCssContadores=null;
    }

    $strHtml = '';
    $strHtml .= '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">' . "\n";
    $strHtml .= '<html lang="pt-br" >' . "\n";
    $strHtml .= '<head>' . "\n";
    $strHtml .= '<meta http-equiv="Pragma" content="no-cache" />' . "\n";
    $strHtml .= '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">' . "\n";
    if ($strConteudoCss!="") {
      $strHtml .= '<style type="text/css"><!--/*--><![CDATA[/*><!--*/' . "\n";
      $strHtml .= $strConteudoCss;
      $strHtml .= "\n/*]]>*/-->\n</style>";
    }
    $strHtml .= '<title>:: ' . $strTitulo . ' ::</title>' . "\n";
    $strHtml .= '</head>' . "\n";
    $strHtml .= '<body>' . "\n";

    if ($objDocumentoDTO!=null) {
      $strTextoPublicacao = PublicacaoINT::obterTextoInformativoPublicacao($objDocumentoDTO);
      if ($strTextoPublicacao!=null) {
        $strHtml .= '<div style="font-weight: 500; text-align: left; font-size: 9pt; border: 2px solid #777; position: absolute; left: 67%; padding: 4px;">' . nl2br($strTextoPublicacao) . '</div>' . "\n";
      }
    }

    $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
    $objSecaoDocumentoDTO->retNumIdSecaoDocumento();
    $objSecaoDocumentoDTO->retStrSinAssinatura();
    $objSecaoDocumentoDTO->setDblIdDocumento($parObjEditorDTO->getDblIdDocumento());
    $objSecaoDocumentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimento());

    if ($parObjEditorDTO->getStrSinCabecalho()=='N') {
      $objSecaoDocumentoDTO->setStrSinCabecalho('N');
    }

    if ($parObjEditorDTO->getStrSinRodape()=='N') {
      $objSecaoDocumentoDTO->setStrSinRodape('N');
    }

    $objSecaoDocumentoDTO->setOrdNumOrdem(InfraDTO::$TIPO_ORDENACAO_ASC);

    $objSecaoDocumentoRN = new SecaoDocumentoRN();
    $objVersaoSecaoDocumentoRN = new VersaoSecaoDocumentoRN();

    $arrObjSecaoDocumentoDTO = $objSecaoDocumentoRN->listar($objSecaoDocumentoDTO);

    $numVersao = null;

    if (!$parObjEditorDTO->isSetNumVersao()) {
      $objVersaoSecaoDocumentoDTO = new VersaoSecaoDocumentoDTO();
      $objVersaoSecaoDocumentoDTO->retNumIdSecaoDocumento();
      $objVersaoSecaoDocumentoDTO->retNumVersao();
      $objVersaoSecaoDocumentoDTO->retStrConteudo();
      $objVersaoSecaoDocumentoDTO->setNumIdSecaoDocumento(InfraArray::converterArrInfraDTO($arrObjSecaoDocumentoDTO, 'IdSecaoDocumento'), InfraDTO::$OPER_IN);
      $objVersaoSecaoDocumentoDTO->setStrSinUltima('S');
      $objVersaoSecaoDocumentoDTO->setOrdNumVersao(InfraDTO::$TIPO_ORDENACAO_DESC);

      $arrObjVersaoSecaoDocumentoDTO = $objVersaoSecaoDocumentoRN->listar($objVersaoSecaoDocumentoDTO);

      if (count($arrObjVersaoSecaoDocumentoDTO)) {
        $numVersao = $arrObjVersaoSecaoDocumentoDTO[0]->getNumVersao();
        $arrObjVersaoSecaoDocumentoDTO = InfraArray::indexarArrInfraDTO($arrObjVersaoSecaoDocumentoDTO, 'IdSecaoDocumento');
      }
    }

    foreach ($arrObjSecaoDocumentoDTO as $objSecaoDocumentoDTO) {
      if ($objSecaoDocumentoDTO->getStrSinAssinatura()=='N') {

        if (!$parObjEditorDTO->isSetNumVersao()) {

          $strHtml .= $this->resetContadoresCss($arrObjVersaoSecaoDocumentoDTO[$objSecaoDocumentoDTO->getNumIdSecaoDocumento()]->getStrConteudo(),$arrCssContadores);

        } else {

          $objVersaoSecaoDocumentoDTO = new VersaoSecaoDocumentoDTO();
          $objVersaoSecaoDocumentoDTO->retStrConteudo();
          $objVersaoSecaoDocumentoDTO->setNumIdSecaoDocumento($objSecaoDocumentoDTO->getNumIdSecaoDocumento());
          $objVersaoSecaoDocumentoDTO->setNumVersao($parObjEditorDTO->getNumVersao(), InfraDTO::$OPER_MENOR_IGUAL);
          $objVersaoSecaoDocumentoDTO->setOrdNumVersao(InfraDTO::$TIPO_ORDENACAO_DESC);
          $objVersaoSecaoDocumentoDTO->setNumMaxRegistrosRetorno(1);

          $arrObjVersaoSecaoDocumentoDTO = $objVersaoSecaoDocumentoRN->listar($objVersaoSecaoDocumentoDTO);
          $strHtml .= $this->resetContadoresCss($arrObjVersaoSecaoDocumentoDTO[0]->getStrConteudo(),$arrCssContadores);
        }

      } else {

        if ($parObjEditorDTO->isSetStrSinAssinaturas() && $parObjEditorDTO->getStrSinAssinaturas()=='N') {
          continue;
        }

        //só mostrar a tarja se consultando a última versão
        if ($parObjEditorDTO->isSetNumVersao()) {

          $objVersaoSecaoDocumentoDTO = new VersaoSecaoDocumentoDTO();
          $objVersaoSecaoDocumentoDTO->retNumVersao();
          $objVersaoSecaoDocumentoDTO->setDblIdDocumentoSecaoDocumento($parObjEditorDTO->getDblIdDocumento());
          $objVersaoSecaoDocumentoDTO->setStrSinUltima('S');
          $objVersaoSecaoDocumentoDTO->setNumMaxRegistrosRetorno(1);
          $objVersaoSecaoDocumentoDTO->setOrdNumVersao(InfraDTO::$TIPO_ORDENACAO_DESC);

          $arrObjVersaoSecaoDocumentoDTO = $objVersaoSecaoDocumentoRN->listar($objVersaoSecaoDocumentoDTO);

          if ($arrObjVersaoSecaoDocumentoDTO[0]->getNumVersao()!=$parObjEditorDTO->getNumVersao()) {
            continue;
          }
        }

        $objAssinaturaRN = new AssinaturaRN();
        $strHtml .= $objAssinaturaRN->montarTarjas($objDocumentoDTO);
      }
    }


    if ($parObjEditorDTO->getStrSinIdentificacaoVersao()=='S') {
      $strHtml .= $this->consultarHtmlIdentificacaoVersao($parObjEditorDTO);
    }

    $strHtml .= '</body>' . "\n";
    $strHtml .= '</html>' . "\n";

    if (!$parObjEditorDTO->isSetNumVersao()) {
      $parObjEditorDTO->setNumVersao($numVersao);
    }

    if ($parObjEditorDTO->isSetStrSinProcessarLinks() && $parObjEditorDTO->getStrSinProcessarLinks()=='S') {

      $strHtml=$this->processarLinksSei($strHtml);

      $posLinkSeiIni = 0;
      $strChaveBusca = 'id="lnkSei';
      while (($posLinkSeiIni = strpos($strHtml, $strChaveBusca, $posLinkSeiIni))!==false) {

        $posLinkSeiIni = $posLinkSeiIni + strlen($strChaveBusca);
        $posLinkSeiFim = strpos($strHtml, '"', $posLinkSeiIni);

        if ($posLinkSeiIni!==false && $posLinkSeiFim!==false) {

          $dblIdProtocolo = substr($strHtml, $posLinkSeiIni, $posLinkSeiFim - $posLinkSeiIni);

          $strLink = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=protocolo_visualizar&id_protocolo=' . $dblIdProtocolo);

          $strHtml = substr($strHtml, 0, $posLinkSeiIni - strlen($strChaveBusca)) . ' href="' . $strLink . '" target="_blank" ' . substr($strHtml, $posLinkSeiFim + 1);
        }
      }
    } else {
      $strHtml=preg_replace(self::$REGEXP_LINK_ASSINADO,'$4',$strHtml);
    }

    return $strHtml;
  }

  private function resetContadoresCss($strConteudoHtml,$arrClasses)
  {
    if(count($arrClasses)>1){
      $arrContadoresUsados=array();
      $qtd=preg_match_all('/<p\w*\s*class="([^"]*)/',$strConteudoHtml,$arrMatches);
      if ($qtd>0){
        $arrClassesUsadas=array_unique($arrMatches[1]);
        foreach ($arrClassesUsadas as $strClasse) {
          if(isset($arrClasses[$strClasse])){
            $arrContadoresUsados[]=$arrClasses[$strClasse];
          }
        }
        if (count($arrContadoresUsados)>0){
          $arrContadoresUsados=array_unique($arrContadoresUsados);
          $strDiv="\n<div style=\"counter-reset:";
          foreach ($arrContadoresUsados as $strContador) {
            $strDiv.=" ".$strContador;
          }
          $strDiv.=';"></div>'."\n";
          return $strConteudoHtml.$strDiv;
        }
      }
    }
    return $strConteudoHtml;
  }
  protected function obterParametrosConectado(EditorDTO $parObjEditorDTO){

    try {

      $objInfraException = new InfraException();

      $objParametrosEditorDTO = new ParametrosEditorDTO();

      if ($parObjEditorDTO->getDblIdDocumento()!=null) {

        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->retDblIdDocumento();
        $objDocumentoDTO->retDblIdProcedimento();
        $objDocumentoDTO->retDblIdDocumentoEdoc();
        $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $objDocumentoDTO->retStrProtocoloProcedimentoFormatado();
        $objDocumentoDTO->retStrNomeSerie();
        $objDocumentoDTO->retNumIdModeloSerie();
        $objDocumentoDTO->retNumIdModeloEdocSerie();
        $objDocumentoDTO->retNumIdUnidadeResponsavel();
        $objDocumentoDTO->retNumIdUsuarioGeradorProtocolo();
        $objDocumentoDTO->retDtaGeracaoProtocolo();
        $objDocumentoDTO->retStrNumero();
        $objDocumentoDTO->retStrCodigoBarrasProcedimento();
        $objDocumentoDTO->retStrCodigoBarrasDocumento();

        $objDocumentoDTO->setDblIdDocumento($parObjEditorDTO->getDblIdDocumento());

        $objDocumentoRN = new DocumentoRN();
        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

        if ($objDocumentoDTO==null) {
          $objInfraException->lancarValidacao('Documento não encontrado.');
        }

        $objParametrosEditorDTO->setObjDocumentoDTO($objDocumentoDTO);

        $numIdUnidadeResponsavel = $objDocumentoDTO->getNumIdUnidadeResponsavel();
        $numIdUsuarioGerador = $objDocumentoDTO->getNumIdUsuarioGeradorProtocolo();
        $dtaGeracao = $objDocumentoDTO->getDtaGeracaoProtocolo();

      } else if ($parObjEditorDTO->getNumIdBaseConhecimento()!=null) {

        $objBaseConhecimentoDTO = new BaseConhecimentoDTO();
        $objBaseConhecimentoDTO->retNumIdUnidade();
        $objBaseConhecimentoDTO->retNumIdUsuarioGerador();
        $objBaseConhecimentoDTO->retDthGeracao();
        $objBaseConhecimentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimento());

        $objBaseConhecimentoRN = new BaseConhecimentoRN();
        $objBaseConhecimentoDTO = $objBaseConhecimentoRN->consultar($objBaseConhecimentoDTO);

        $numIdUnidadeResponsavel = $objBaseConhecimentoDTO->getNumIdUnidade();
        $numIdUsuarioGerador = $objBaseConhecimentoDTO->getNumIdUsuarioGerador();
        $dtaGeracao = substr($objBaseConhecimentoDTO->getDthGeracao(), 0, 10);
      }

      $arrConteudoTags = array();

      /* Unidade Responsável ************************************************************************************/
      $objUnidadeDTO = new UnidadeDTO();
      $objUnidadeDTO->retNumIdContato();
      $objUnidadeDTO->retNumIdOrgao();
      $objUnidadeDTO->retNumIdUnidade();
      $objUnidadeDTO->retStrSigla();
      $objUnidadeDTO->retStrDescricao();
      $objUnidadeDTO->retStrSiglaOrgao();
      $objUnidadeDTO->retStrDescricaoOrgao();
      $objUnidadeDTO->retStrTimbreOrgao();

      $objUnidadeDTO->setNumIdUnidade($numIdUnidadeResponsavel);

      $objMdPetUnidadeRN = new MdPetUnidadeRN();
      $objUnidadeDTO = $objMdPetUnidadeRN->consultarRN0125($objUnidadeDTO);
      
      //seiv3 - Obtendo informaçoes de endereco da unidade
      $idContatoAssociado = $objUnidadeDTO->getNumIdContato();
      $contatoAssociadoDTO = new ContatoDTO();
      $contatoRN = new ContatoRN();
      $contatoAssociadoDTO->retTodos();
      $contatoAssociadoDTO->retStrNomeCidade();
      $contatoAssociadoDTO->retStrSiglaUf();
      $contatoAssociadoDTO->retStrSitioInternetContatoAssociado();
      $contatoAssociadoDTO->setNumIdContato( $idContatoAssociado );
      $objContatoAssociadoDTO = $contatoRN->consultarRN0324( $contatoAssociadoDTO );

      //seiv3
      if ( InfraString::isBolVazia( $objContatoAssociadoDTO->getStrEndereco()) ) {
        throw new InfraException('Unidade ' . $objUnidadeDTO->getStrSigla() . ' não possui endereço cadastrado.');
      }
      

      $objParametrosEditorDTO->setObjUnidadeDTO($objUnidadeDTO);

      /* Usuário Gerador ****************************************************************************************/
      $objUsuarioDTO = new UsuarioDTO();
      $objUsuarioDTO->setBolExclusaoLogica(false);
      $objUsuarioDTO->retStrNome();
      $objUsuarioDTO->setNumIdUsuario($numIdUsuarioGerador);

      $objUsuarioRN = new UsuarioRN();
      $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

      $arrConteudoTags[] = array('@sigla_orgao_origem@', $objUnidadeDTO->getStrSiglaOrgao());
      $arrConteudoTags[] = array('@descricao_orgao_origem@', $objUnidadeDTO->getStrDescricaoOrgao());

      if ($objUsuarioDTO->getStrNome()!='') {
        $arrConteudoTags[] = array('@nome_autor@', $objUsuarioDTO->getStrNome());
      }

      $strTag = InfraString::transformarCaixaAlta($objUnidadeDTO->getStrDescricaoOrgao());
      $arrConteudoTags[] = array('@descricao_orgao_maiusculas@', $strTag);

      $arrConteudoTags[] = array('@timbre_orgao@', '<img alt="Timbre" src="data:image/png;base64,' . $objUnidadeDTO->getStrTimbreOrgao() . '" />');

      //alteracoes seiv3
      $arrConteudoTags[] = array('@endereco_unidade@', $objContatoAssociadoDTO->getStrEndereco());
      
      //seiv3 @todo telefone fixo ou celular? e se tiver os dois? 
      $arrConteudoTags[] = array('@telefone_unidade@', $objContatoAssociadoDTO->getStrTelefoneFixo());
      //$arrConteudoTags[] = array('@fax_unidade@', $objContatoAssociadoDTO->getStrFax());

      $strTag = '';

      //alteracoes seiv3
      if (!InfraString::isBolVazia($objContatoAssociadoDTO->getStrBairro())) {
      	$strTag = ' - Bairro ' . $objContatoAssociadoDTO->getStrBairro();
      }
      
      $arrConteudoTags[] = array('@hifen_bairro_unidade@', $strTag);

      //alteracoes seiv3
      $arrConteudoTags[] = array('@cep_unidade@', 'CEP ' . $objContatoAssociadoDTO->getStrCep());

      //alteracoes seiv3
      if ( $objContatoAssociadoDTO->getStrNomeCidade()!='' ) {
      	$arrConteudoTags[] = array('@cidade_unidade@', $objContatoAssociadoDTO->getStrNomeCidade());
      }

      //alteracoes seiv3
      $arrConteudoTags[] = array('@sigla_uf_unidade@', $objContatoAssociadoDTO->getStrSiglaUf());

      $strTag = '';

      //alteracoes seiv3
      if ( !InfraString::isBolVazia( $objContatoAssociadoDTO->getStrSitioInternetContatoAssociado() ) ) {
      	$strTag = ' - ' . $objContatoAssociadoDTO->getStrSitioInternetContatoAssociado();
      }
      
      $arrConteudoTags[] = array('@hifen_sitio_internet_orgao@', $strTag);

      $strTag = '';

      //alteracoes seiv3
      if (!InfraString::isBolVazia( $objContatoAssociadoDTO->getStrComplemento() )) {
      	$strTag .= $objContatoAssociadoDTO->getStrComplemento();
      }
      
      $arrConteudoTags[] = array('@complemento_endereco_unidade@', $strTag);

      //usa data de geracao do protocolo, nas republicacoes, retificações, apostilamentos de atos, portarias... deve manter a data do original
      //para os outros casos o uso da data de geracao do protocolo ou do dia atual não faz diferença já que são iguais
      $arrConteudoTags[] = array('@dia@', substr($dtaGeracao, 0, 2));
      $arrConteudoTags[] = array('@mes@', substr($dtaGeracao, 3, 2));
      $arrConteudoTags[] = array('@ano@', substr($dtaGeracao, 6, 4));
      $arrConteudoTags[] = array('@mes_extenso@', strtolower(InfraData::descreverMes(substr($dtaGeracao, 3, 2))));

      $strHierarquiaUnidade = $objUnidadeRN->obterHierarquiaUnidade($objUnidadeDTO);
      $arrConteudoTags[] = array('@hierarquia_unidade@', $strHierarquiaUnidade);

      $arrHierarquiaUnidade = explode('/', $strHierarquiaUnidade);
      $strHierarquiaUnidade = '';
      for ($i = count($arrHierarquiaUnidade) - 1; $i>=0; $i--) {
        if ($strHierarquiaUnidade!='') {
          $strHierarquiaUnidade .= '/';
        }
        $strHierarquiaUnidade .= $arrHierarquiaUnidade[$i];
      }
      $arrConteudoTags[] = array('@hierarquia_unidade_invertida@', $strHierarquiaUnidade);

      $arrConteudoTags[] = array('@sigla_unidade@', InfraString::transformarCaixaAlta($objUnidadeDTO->getStrSigla()));
      $arrConteudoTags[] = array('@descricao_unidade@', $objUnidadeDTO->getStrDescricao());
      $arrConteudoTags[] = array('@descricao_unidade_maiusculas@', InfraString::transformarCaixaAlta($objUnidadeDTO->getStrDescricao()));

      if ($parObjEditorDTO->getDblIdDocumento()!=null) {

        $arrConteudoTags[] = array('@processo@', $objDocumentoDTO->getStrProtocoloProcedimentoFormatado());
        $arrConteudoTags[] = array('@codigo_barras_processo@', '<img alt="Código de Barras do Processo" src="data:image/png;base64,' . $objDocumentoDTO->getStrCodigoBarrasProcedimento() . '" />');

        $arrConteudoTags[] = array('@documento@', $objDocumentoDTO->getStrProtocoloDocumentoFormatado());
        $arrConteudoTags[] = array('@codigo_barras_documento@', '<img alt="Código de Barras do Documento" src="data:image/png;base64,' . $objDocumentoDTO->getStrCodigoBarrasDocumento() . '" />');

        if ($objDocumentoDTO->getStrNomeSerie()!='') {
          $arrConteudoTags[] = array('@serie@', $objDocumentoDTO->getStrNomeSerie());
        }

        if (!InfraString::isBolVazia($objDocumentoDTO->getStrNumero())) {
          $arrConteudoTags[] = array('@numeracao_serie@', $objDocumentoDTO->getStrNumero());
        } else {
          $arrConteudoTags[] = array('@numeracao_serie@', $objDocumentoDTO->getStrProtocoloDocumentoFormatado());
        }

        /* Participantes ******************************************************************************************/
        $objParticipanteDTO = new ParticipanteDTO();
        $objParticipanteDTO->retNumIdContato();
        $objParticipanteDTO->retStrStaParticipacao();
        $objParticipanteDTO->retNumSequencia();

        $objParticipanteDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdDocumento());

        $objParticipanteDTO->setOrdStrStaParticipacao(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objParticipanteDTO->setOrdNumSequencia(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objParticipanteRN = new ParticipanteRN();
        $arrObjParticipanteDTO = $objParticipanteRN->listarRN0189($objParticipanteDTO);

        /* Interessados *******************************************************************************************/

        $arrObjContatoDTOInteressados = null;

        $arr = InfraArray::converterArrInfraDTO(InfraArray::filtrarArrInfraDTO($arrObjParticipanteDTO, 'StaParticipacao', ParticipanteRN::$TP_INTERESSADO), 'IdContato');

        if (count($arr)>0) {

          $objContatoDTO = new ContatoDTO();
          $objContatoDTO->setBolExclusaoLogica(false);
          $objContatoDTO->retNumIdContato();
          $objContatoDTO->retStrNome();
          $objContatoDTO->retDblCnpj();

          //alteracoes seiv3
          $objContatoDTO->retStrMatricula();
          
          $objContatoDTO->setNumIdContato($arr, InfraDTO::$OPER_IN);

          $objContatoRN = new ContatoRN();
          $arr2 = InfraArray::indexarArrInfraDTO($objContatoRN->listarRN0325($objContatoDTO),'IdContato');

          //manter ordem realizada no cadastro
          $arrObjContatoDTOInteressados = array();
          foreach($arr as $numIdContatoInteressado){
            if (isset($arr2[$numIdContatoInteressado])){
              $arrObjContatoDTOInteressados[] = $arr2[$numIdContatoInteressado];
            }
          }
        }

        /* Destinatários ******************************************************************************************/
        $arr = InfraArray::converterArrInfraDTO(InfraArray::filtrarArrInfraDTO($arrObjParticipanteDTO, 'StaParticipacao', ParticipanteRN::$TP_DESTINATARIO), 'IdContato');
        $arrObjContatoDTODestinatarios = null;

        if (count($arr)>0) {

          $objContatoDTO = new ContatoDTO();
          $objContatoDTO->setBolExclusaoLogica(false);
          $objContatoDTO->retNumIdContato();
          $objContatoDTO->retStrNome();
          $objContatoDTO->retStrNomeContextoContato();
          $objContatoDTO->retStrSigla();
          $objContatoDTO->retStrSiglaContextoContato();
          $objContatoDTO->retStrExpressaoCargo();
          $objContatoDTO->retStrExpressaoTratamento();
          $objContatoDTO->retStrExpressaoVocativo();
          $objContatoDTO->retStrExpressaoTitulo();
          $objContatoDTO->retStrEndereco();
          $objContatoDTO->retStrEnderecoContextoContato();
          $objContatoDTO->retStrBairro();
          $objContatoDTO->retStrBairroContextoContato();
          $objContatoDTO->retStrNomeCidade();
          $objContatoDTO->retStrNomeCidadeContextoContato();
          $objContatoDTO->retStrCep();
          $objContatoDTO->retStrCepContextoContato();
          $objContatoDTO->retStrSiglaEstado();
          $objContatoDTO->retStrSiglaEstadoContextoContato();
          $objContatoDTO->retStrSinEnderecoContexto();

          $objContatoDTO->setNumIdContato($arr, InfraDTO::$OPER_IN);

          $objContatoRN = new ContatoRN();
          $arr2 = InfraArray::indexarArrInfraDTO($objContatoRN->listarRN0325($objContatoDTO),'IdContato');

          //manter ordem realizada no cadastro
          $arrObjContatoDTODestinatarios = array();
          foreach($arr as $numIdContatoDestinatario){
            if (isset($arr2[$numIdContatoDestinatario])){
              $arrObjContatoDTODestinatarios[] = $arr2[$numIdContatoDestinatario];
            }
          }
        }

        $objParametrosEditorDTO->setArrObjContatoDTODestinatarios($arrObjContatoDTODestinatarios);

        $numDestinatarios = count($arrObjContatoDTODestinatarios);

        if ($numDestinatarios) {

          $strTag = '';
          for ($i = 0; $i<$numDestinatarios; $i++) {
            if ($strTag!='') {
              $strTag .= ', ';
            }
            $strTag .= $arrObjContatoDTODestinatarios[$i]->getStrNome();
          }

          if ($strTag!='') {
            $arrConteudoTags[] = array('@destinatarios@', $strTag); //deprecated
            $arrConteudoTags[] = array('@destinatarios_virgula_espaco@', $strTag);
          }

          $strTag = '';
          for ($i = 0; $i<$numDestinatarios; $i++) {
            if ($strTag!='') {
              $strTag .= ', ';
            }
            $strTag .= InfraString::transformarCaixaAlta($arrObjContatoDTODestinatarios[$i]->getStrNome());
          }

          if ($strTag!='') {
            $arrConteudoTags[] = array('@destinatarios_virgula_espaco_maiusculas@', $strTag);
          }

          $strTag = '';
          for ($i = 0; $i<$numDestinatarios; $i++) {
            if ($strTag!='') {
              $strTag .= '<br />';
            }
            $strTag .= $arrObjContatoDTODestinatarios[$i]->getStrNome();
          }

          if ($strTag!='') {
            $arrConteudoTags[] = array('@destinatarios_quebra_linha@', $strTag);
          }

          $strTag = '';
          for ($i = 0; $i<$numDestinatarios; $i++) {
            if ($strTag!='') {
              $strTag .= '<br />';
            }
            $strTag .= InfraString::transformarCaixaAlta($arrObjContatoDTODestinatarios[$i]->getStrNome());
          }

          if ($strTag!='') {
            $arrConteudoTags[] = array('@destinatarios_quebra_linha_maiusculas@', $strTag);
          }

          $objContatoDTODestinatario = $arrObjContatoDTODestinatarios[0];

          if (($strTag = $objContatoDTODestinatario->getStrNome())!=''){
            $arrConteudoTags[] = array('@nome_destinatario@', $strTag);
          }

          if (($strTag = InfraString::transformarCaixaAlta($objContatoDTODestinatario->getStrNome()))!=''){
            $arrConteudoTags[] = array('@nome_destinatario_maiusculas@', $strTag);
          }

          if (($strTag = $objContatoDTODestinatario->getStrExpressaoTratamento())!=''){
            $arrConteudoTags[] = array('@tratamento_destinatario@', $strTag);
          }

          if (($strTag = $objContatoDTODestinatario->getStrExpressaoTitulo())!=''){
            $arrConteudoTags[] = array('@titulo_destinatario@', $strTag);
          }

          if (($strTag = $objContatoDTODestinatario->getStrExpressaoVocativo())!=''){
            $arrConteudoTags[] = array('@vocativo_destinatario@', $strTag);
          }

          if (($strTag = $objContatoDTODestinatario->getStrExpressaoCargo())!=''){
            $arrConteudoTags[] = array('@cargo_destinatario@', $strTag);
          }

          if (($strTag = $objContatoDTODestinatario->getStrNomeContextoContato())!=''){
            $arrConteudoTags[] = array('@origem_destinatario@', $strTag);
            $arrConteudoTags[] = array('@nome_contexto_destinatario@', $strTag);
          }

          if ($objContatoDTODestinatario->getStrSinEnderecoContexto() == 'N') {
            $strTag = $objContatoDTODestinatario->getStrEndereco();
          } else {
            $strTag = $objContatoDTODestinatario->getStrEnderecoContextoContato();
          }

          if ($strTag != '') {
            $arrConteudoTags[] = array('@endereco_destinatario@', $strTag);
          }

          if (($strTag = $objContatoDTODestinatario->getStrEnderecoContextoContato())!=''){
            $arrConteudoTags[] = array('@endereco_contexto_destinatario@', $strTag);
          }

          if ($objContatoDTODestinatario->getStrSinEnderecoContexto()=='N') {
            $strTag = $objContatoDTODestinatario->getStrBairro();
          } else {
            $strTag = $objContatoDTODestinatario->getStrBairroContextoContato();
          }

          if ($strTag!='') {
            $arrConteudoTags[] = array('@bairro_destinatario@', $strTag);
          }

          if (($strTag = $objContatoDTODestinatario->getStrBairroContextoContato())!=''){
            $arrConteudoTags[] = array('@bairro_contexto_destinatario@', $strTag);
          }

          if ($objContatoDTODestinatario->getStrSinEnderecoContexto()=='N') {
            $strTag = $objContatoDTODestinatario->getStrNomeCidade();
          } else {
            $strTag = $objContatoDTODestinatario->getStrNomeCidadeContextoContato();
          }

          if ($strTag!='') {
            $arrConteudoTags[] = array('@cidade_destinatario@', $strTag);
          }

          if (($strTag = $objContatoDTODestinatario->getStrNomeCidadeContextoContato())!=''){
            $arrConteudoTags[] = array('@cidade_contexto_destinatario@', $strTag);
          }

          if ($objContatoDTODestinatario->getStrSinEnderecoContexto()=='N') {
            $strTag = $objContatoDTODestinatario->getStrCep();
          } else {
            $strTag = $objContatoDTODestinatario->getStrCepContextoContato();
          }

          if ($strTag!='') {
            $arrConteudoTags[] = array('@cep_destinatario@', $strTag);
          }

          if (($strTag = $objContatoDTODestinatario->getStrCepContextoContato())!=''){
            $arrConteudoTags[] = array('@cep_contexto_destinatario@', $strTag);
          }

          $strTag = '';
          if ($objContatoDTODestinatario->getStrSinEnderecoContexto()=='N') {
            if ($objContatoDTODestinatario->getStrSiglaEstado()!='') {
              $strTag = ' - ' . $objContatoDTODestinatario->getStrSiglaEstado();
            }
          } else {
            if ($objContatoDTODestinatario->getStrSiglaEstadoContextoContato()!='') {
              $strTag = ' - ' . $objContatoDTODestinatario->getStrSiglaEstadoContextoContato();
            }
          }

          if ($strTag!='') {
            $arrConteudoTags[] = array('@hifen_uf_destinatario@', $strTag);
          }

          $strTag = '';
          if ($objContatoDTODestinatario->getStrSiglaEstadoContextoContato()!='') {
            $strTag = ' - ' . $objContatoDTODestinatario->getStrSiglaEstadoContextoContato();
          }

          if ($strTag!=''){
            $arrConteudoTags[] = array('@hifen_uf_contexto_destinatario@', $strTag);
          }

          if ($objContatoDTODestinatario->getStrSinEnderecoContexto()=='N') {
            $strTag = $objContatoDTODestinatario->getStrSiglaEstado();
          } else {
            $strTag = $objContatoDTODestinatario->getStrSiglaEstadoContextoContato();
          }

          if ($strTag!='') {
            $arrConteudoTags[] = array('@sigla_uf_destinatario@', $strTag);
          }

          if (($strTag = $objContatoDTODestinatario->getStrSiglaEstadoContextoContato())!=''){
            $arrConteudoTags[] = array('@sigla_uf_contexto_destinatario@', $strTag);
          }
        }

        $numInteressados = count($arrObjContatoDTOInteressados);

        if ($numInteressados) {

          $strTag = '';
          for ($i = 0; $i<$numInteressados; $i++) {
            if ($strTag!='') {
              $strTag .= ', ';
            }
            $strTag .= $arrObjContatoDTOInteressados[$i]->getStrNome();
          }

          if ($strTag!='') {
            $arrConteudoTags[] = array('@interessados@', $strTag); //deprecated
            $arrConteudoTags[] = array('@interessados_virgula_espaco@', $strTag);
          }


          $strTag = '';
          for ($i = 0; $i<$numInteressados; $i++) {
            if ($strTag!='') {
              $strTag .= ', ';
            }
            $strTag .= InfraString::transformarCaixaAlta($arrObjContatoDTOInteressados[$i]->getStrNome());
          }

          if ($strTag!='') {
            $arrConteudoTags[] = array('@interessados_virgula_espaco_maiusculas@', $strTag);
          }

          $strTag = '';
          for ($i = 0; $i<$numInteressados; $i++) {
            if ($strTag!='') {
              $strTag .= '<br />';
            }
            $strTag .= $arrObjContatoDTOInteressados[$i]->getStrNome();
          }

          if ($strTag!='') {
            $arrConteudoTags[] = array('@interessados_quebra_linha@', $strTag);
          }

          $strTag = '';
          for ($i = 0; $i<$numInteressados; $i++) {
            if ($strTag!='') {
              $strTag .= '<br />';
            }
            $strTag .= InfraString::transformarCaixaAlta($arrObjContatoDTOInteressados[$i]->getStrNome());
          }

          if ($strTag!='') {
            $arrConteudoTags[] = array('@interessados_quebra_linha_maiusculas@', $strTag);
          }

          if (($strTag = $arrObjContatoDTOInteressados[0]->getStrNome())!=''){
            $arrConteudoTags[] = array('@nome_interessado@', $strTag);
          }

          if (($strTag = InfraString::transformarCaixaAlta($arrObjContatoDTOInteressados[0]->getStrNome()))!=''){
            $arrConteudoTags[] = array('@nome_interessado_maiusculas@', $strTag);
          }
        }
      }
      
      $objParametrosEditorDTO->setArrTags($arrConteudoTags);

      return $objParametrosEditorDTO;


    } catch (Exception $e) {
      throw new InfraException('Erro obtendo parâmetros do editor.', $e, $objParametrosEditorDTO->__toString());
    }
  }

  public function buscarImagemUpload($nomeArquivo)
  {
    $objImagemFormatoDTO = new ImagemFormatoDTO();
    $objImagemFormatoDTO->retStrFormato();
    $objImagemFormatoRN = new ImagemFormatoRN();
    $arrImagemPermitida = InfraArray::converterArrInfraDTO($objImagemFormatoRN->listar($objImagemFormatoDTO), 'Formato');
    if (in_array('jpg', $arrImagemPermitida) && !in_array('jpeg', $arrImagemPermitida)) $arrImagemPermitida[] = 'jpeg';

    $ext = pathinfo(DIR_SEI_TEMP . '/' . $nomeArquivo);

    $ret = print_r($ext, true);
    if (!in_array($ext['extension'], $arrImagemPermitida)) return 'Tipo de Arquivo não permitido.';

    return 'data:image/' . $ext['extension'] . ';base64,' . base64_encode(file_get_contents(DIR_SEI_TEMP . '/' . $nomeArquivo));
  }

  protected function recuperarVersaoControlado(EditorDTO $parObjEditorDTO)
  {
    try {
      if ($parObjEditorDTO->getDblIdDocumento()!=null) {
        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->retDblIdDocumento();
        $objDocumentoDTO->retStrNomeSerie();
        $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $objDocumentoDTO->retStrProtocoloProcedimentoFormatado();
        $objDocumentoDTO->setDblIdDocumento($parObjEditorDTO->getDblIdDocumento());

        $objDocumentoRN = new DocumentoRN();
        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

        if ($objDocumentoDTO==null) {
          throw new InfraException('Documento não encontrado.');
        }
      }

      $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
      $objSecaoDocumentoDTO->retNumIdSecaoDocumento();
      $objSecaoDocumentoDTO->retNumIdSecaoModelo();
      $objSecaoDocumentoDTO->retStrSinAssinatura();
      $objSecaoDocumentoDTO->setDblIdDocumento($parObjEditorDTO->getDblIdDocumento());
      $objSecaoDocumentoDTO->setOrdNumOrdem(InfraDTO::$TIPO_ORDENACAO_ASC);
      $objSecaoDocumentoDTO->setStrSinAssinatura('N');

      $objSecaoDocumentoRN = new SecaoDocumentoRN();
      $arrObjSecaoDocumentoDTO = $objSecaoDocumentoRN->listar($objSecaoDocumentoDTO);
      $arrNovoObjSecaoDocumentoDTO = array();

      foreach ($arrObjSecaoDocumentoDTO as $objSecaoDocumentoDTO) {

        $objVersaoSecaoDocumentoDTO = new VersaoSecaoDocumentoDTO();
        $objVersaoSecaoDocumentoDTO->retStrConteudo();
        $objVersaoSecaoDocumentoDTO->setNumIdSecaoDocumento($objSecaoDocumentoDTO->getNumIdSecaoDocumento());
        $objVersaoSecaoDocumentoDTO->setNumVersao($parObjEditorDTO->getNumVersao(), InfraDTO::$OPER_MENOR_IGUAL);
        $objVersaoSecaoDocumentoDTO->setOrdNumVersao(InfraDTO::$TIPO_ORDENACAO_DESC);
        $objVersaoSecaoDocumentoDTO->setNumMaxRegistrosRetorno(1);

        $objVersaoSecaoDocumentoRN = new VersaoSecaoDocumentoRN();
        $arrObjVersaoSecaoDocumentoDTO = $objVersaoSecaoDocumentoRN->listar($objVersaoSecaoDocumentoDTO);

        if (count($arrObjVersaoSecaoDocumentoDTO)>0) {
          $objNovoSecaoDocumentoDTO = new SecaoDocumentoDTO();
          $objNovoSecaoDocumentoDTO->setNumIdSecaoModelo($objSecaoDocumentoDTO->getNumIdSecaoDocumento());
          $objNovoSecaoDocumentoDTO->setNumIdSecaoModelo($objSecaoDocumentoDTO->getNumIdSecaoModelo());
          $objNovoSecaoDocumentoDTO->setStrConteudo($arrObjVersaoSecaoDocumentoDTO[0]->getStrConteudo());
          $arrNovoObjSecaoDocumentoDTO[] = $objNovoSecaoDocumentoDTO;
        }
      }
      $objEditorDTO = new EditorDTO();
      $objEditorDTO->setDblIdDocumento($parObjEditorDTO->getDblIdDocumento());
      $objEditorDTO->setNumIdBaseConhecimento(null);
      $objEditorDTO->setArrObjSecaoDocumentoDTO($arrNovoObjSecaoDocumentoDTO);
      $objEditorDTO->setStrSinForcarNovaVersao('S');
      $this->adicionarVersao($objEditorDTO);

    } catch (Exception $e) {
      throw new InfraException('Erro recuperando versão.', $e);
    }
  }

  private function validarTagsCriticas($arrImagemPermitida, $str)
  {

    $objInfraException = new InfraException();

    $arrRemoverTags = array('img', 'script', 'iframe', 'frame', 'embed', 'object', 'param', 'video', 'audio', 'button', 'input', 'select');

    foreach ($arrRemoverTags as $tag) {
      if ($str!=preg_replace("%<" . $tag . "[^>]*>(.*?)<\\/" . $tag . ">%si", "", $str) || $str!=preg_replace("%<" . $tag . "[^>]*\\/>%si", "", $str)) {
        switch ($tag) {
          case 'script':
            $objInfraException->lancarValidacao('Documento possui código de script oculto no conteúdo.');
            break;

          case 'img':

            if (count($arrImagemPermitida)==0) {
              $objInfraException->lancarValidacao('Documento possui imagem no conteúdo.');
            }

            $arrImagensConteudo = array();
            preg_match_all('/src="([^"]*)"/i', $str, $arrImagensConteudo);

            foreach ($arrImagensConteudo[1] as $strImagem) {
              $posIni = strpos($strImagem, '/');
              $posFim = strpos($strImagem, ';', $posIni);
              if ($posIni!==false && $posFim!==false) {
                $posIni = $posIni + 1;
                if (!in_array(InfraString::transformarCaixaBaixa(substr($strImagem, $posIni, ($posFim - $posIni))), $arrImagemPermitida)) {
                  $objInfraException->lancarValidacao('Documento possui imagem no formato "' . substr($strImagem, $posIni, ($posFim - $posIni)) . '" não permitido no conteúdo.');
                }
              } else {
                $objInfraException->lancarValidacao('Documento possui imagem não permitida no conteúdo.');
              }
            }
            break;

          case 'button':
          case 'input':
          case 'select':
            $objInfraException->lancarValidacao('Documento possui componente HTML não permitido no conteúdo.');
            break;
          case 'iframe':
            $objInfraException->lancarValidacao('Documento possui formulário oculto no conteúdo.');
            break;

          case 'frame':
            $objInfraException->lancarValidacao('Documento possui formulário no conteúdo.');
            break;
          case 'embed':
          case 'object':
          case 'param':
            $objInfraException->lancarValidacao('Documento possui um objeto não autorizado no conteúdo.');
            break;
          case 'video':
            $objInfraException->lancarValidacao('Documento possui vídeo no conteúdo.');
            break;
          case 'audio':
            $objInfraException->lancarValidacao('Documento possui áudio no conteúdo.');
        }
      }
    }
  }

  public function processarLinksSei($str)
  {
    $str = preg_replace_callback(self::$REGEXP_LINK_ASSINADO, "self::validarLink", $str);

    if (preg_match_all(self::$REGEXP_SPAN_LINKSEI, $str, $matches)>0){
      $arrIdProtocolo=array_unique($matches[1]);
      if (count($arrIdProtocolo)>0) {
        $objProtocoloDTO = new ProtocoloDTO();
        $objProtocoloRN = new ProtocoloRN();
        $objProtocoloDTO->setDblIdProtocolo($arrIdProtocolo, InfraDTO::$OPER_IN);
        $objProtocoloDTO->retDblIdProtocolo();
        $objProtocoloDTO->retStrProtocoloFormatado();
        $arrObjProtocoloDTO = $objProtocoloRN->listarRN0668($objProtocoloDTO);
        if (count($arrObjProtocoloDTO) > 0) {
          $this->arrProtocolos = InfraArray::converterArrInfraDTO($arrObjProtocoloDTO, 'ProtocoloFormatado', 'IdProtocolo');
        }
      }

    }
    $str= preg_replace_callback(self::$REGEXP_SPAN_LINKSEI,'self::processarLinkProtocolo',$str);

    return $str;
  }

  /**
   * @param $matches  origem da REGEXP_LINK_ASSINADO ([0]=match [1]=acao [2]=id_protocolo [3]=id_sistema [4]=texto do link)
   * @return string
   */
  private function validarLink($matches)
  {
    if($matches[3]!=SessaoSEIExterna::getInstance()->getNumIdSistema()){
      return $matches[0];
    }
    if($matches[1]=='protocolo_visualizar'){
      return '<a class="ancoraSei" id="lnkSei'.$matches[2].'" style="text-indent:0;">'.$matches[4].'</a>';
    }
    return $matches[4];
  }
  /**
   * @param $matches  origem da REGEXP_SPAN_LINKSEI ([0]=match [1]=id_protocolo [2]==texto do link)
   * @return string
   */
  private function processarLinkProtocolo($matches)
  {
    if(!isset($this->arrProtocolos[$matches[1]]) || $this->arrProtocolos[$matches[1]]!=$matches[2] ) {
      //não foi encontrado protocolo correspondente, retorna somente o texto
      return $matches[2];
    } else {
      return '<span contenteditable="false" style="text-indent:0;"><a class="ancoraSei" id="lnkSei'.$matches[1].'" style="text-indent:0;">'.$matches[2].'</a></span>';
    }
  }

  private function limparTagsCriticas($str)
  {
    //remove tags mas deixa conteúdo
    $arrRemoverTags = array('html', 'body');
    foreach ($arrRemoverTags as $tag) {
      $str = preg_replace("%<" . $tag . "[^>]*>%si", "", $str);
      $str = preg_replace("%</" . $tag . "[^>]*>%si", "", $str);
    }
    //remove tags e todo o seu conteúdo
    $arrRemoverTags = array('img', 'script', 'iframe', 'frame', 'embed', 'object', 'param', 'video', 'audio', 'button', 'input', 'select', 'link', 'head', 'title');
    foreach ($arrRemoverTags as $tag) {
      $str = preg_replace("%<" . $tag . "[^>]*>(.*?)<\\/" . $tag . ">%si", "", $str);
      $str = preg_replace("%<" . $tag . "[^>]*\\/>%si", "", $str);
    }
    return $str;
  }

  protected function obterNumeroUltimaVersaoConectado(DocumentoDTO $objDocumentoDTO)
  {
    try {

      $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
      $objSecaoDocumentoDTO->retNumIdSecaoDocumento();
      $objSecaoDocumentoDTO->setDblIdDocumento($objDocumentoDTO->getDblIdDocumento());

      $objSecaoDocumentoRN = new SecaoDocumentoRN();
      $arrObjSecaoDocumentoDTO = $objSecaoDocumentoRN->listar($objSecaoDocumentoDTO);

      $objVersaoSecaoDocumentoDTO = new VersaoSecaoDocumentoDTO();
      $objVersaoSecaoDocumentoDTO->retNumVersao();
      $objVersaoSecaoDocumentoDTO->setNumIdSecaoDocumento(InfraArray::converterArrInfraDTO($arrObjSecaoDocumentoDTO, 'IdSecaoDocumento'), InfraDTO::$OPER_IN);
      $objVersaoSecaoDocumentoDTO->setStrSinUltima('S');
      $objVersaoSecaoDocumentoDTO->setNumMaxRegistrosRetorno(1);
      $objVersaoSecaoDocumentoDTO->setOrdNumVersao(InfraDTO::$TIPO_ORDENACAO_DESC);

      $objVersaoSecaoDocumentoRN = new VersaoSecaoDocumentoRN();
      $objVersaoSecaoDocumentoDTO = $objVersaoSecaoDocumentoRN->consultar($objVersaoSecaoDocumentoDTO);

      if ($objVersaoSecaoDocumentoDTO!=null) {
        return $objVersaoSecaoDocumentoDTO->getNumVersao();
      }

      return null;

    } catch (Exception $e) {
      throw new InfraException('Erro obtendo número da última versão.', $e);
    }
  }


}
