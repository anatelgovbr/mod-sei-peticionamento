<?
/*
 * ANATEL
 * 
 * 22/09/2016 - CAST
 *
 */
 
 require_once dirname(__FILE__).'/../../SEI.php';
 
 class PaginaPeticionamentoExterna extends PaginaSEIExterna
 {
   private static $instance = null;

   public static function getInstance()
   {
     if (self::$instance == null) {
     	self::$instance = new PaginaPeticionamentoExterna();
     }
     return self::$instance;
   }

   public function __construct()
   {
     SeiINT::validarHttps();
     parent::__construct();
   }

   public function getStrLogoSistema()
   {
     return '<img src="../../imagens/sei_logo_' . $this->getStrEsquemaCores() . '.jpg" title="Sistema Eletrônico de Informações - Versão ' . SEI_VERSAO . '"/><span class="infraTituloLogoSistema">' . ConfiguracaoSEI::getInstance()->getValor('PaginaSEI', 'NomeSistemaComplemento') . '</span>';
   }

 }
?>