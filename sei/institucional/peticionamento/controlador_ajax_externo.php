<?
/**
 * ANATEL
 *
 * 22/07/2016 - criado por marcelo.bezerra@cast.com.br - CAST
 * Arquivo para realizar controle requisiчуo ajax de usuario externo no modulo peticionamento.
 */

try{
    require_once dirname(__FILE__).'/../../SEI.php';
    session_start();
  	SessaoSEIExterna::getInstance()->validarLink();
    InfraAjax::decodificarPost();
  
 switch($_GET['acao_ajax_externo']){
    
 	case 'contato_pj_vinculada':
 		
 		if( ($_POST['id_tipo_contexto_contato'] != null && $_POST['id_tipo_contexto_contato'] != "" ) 
 		    && 
 		    ($_POST['palavras_pesquisa'] != null && $_POST['palavras_pesquisa'] != "")
 		) {
 		
	 		$objContatoRN = new ContatoRN();
	 		$objContextoContatoDTO = new ContatoDTO();
	 		
	 		$objContextoContatoDTO->retNumIdContato();
	 		$objContextoContatoDTO->retStrNome();
			
	 		//trazer todos que sejam empresas (CNPJ diferente de null), estejam ativos, 
	 		//e atenda ao filtro por nome e tipo de contexto informado na tela
	 			 		
	 		$objContextoContatoDTO->adicionarCriterio(
	 				array('Cnpj','Nome', 'SinAtivo', 'IdTipoContextoContato'),
	 				array(InfraDTO::$OPER_DIFERENTE,InfraDTO::$OPER_LIKE, InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL ),
	 				array(null, "%".$_POST['palavras_pesquisa']."%", 'S', $_POST['id_tipo_contexto_contato'] ),
	 				array( InfraDTO::$OPER_LOGICO_AND , InfraDTO::$OPER_LOGICO_AND , InfraDTO::$OPER_LOGICO_AND ) 
	 		);
	 		
	 		$objContextoContatoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
	 			 		
	 		$arrObjContatoDTO = $objContatoRN->pesquisarRN0471( $objContextoContatoDTO );
	 		$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjContatoDTO,'IdContato', 'Nome');
	 		InfraAjax::enviarXML($xml);
	 		
 		}
 		
 		break;
 	
  	case 'contato_auto_completar_contexto_pesquisa':
  		//alterado para atender anatel exibir apenas nome contato
  		$objContatoDTO = new ContatoDTO();
  		$objContatoDTO->retNumIdContato();
  		$objContatoDTO->retStrSigla();
  		$objContatoDTO->retStrNome();
  		
  		$objContatoDTO->setStrPalavrasPesquisa($_POST['extensao']);
  		$objContatoDTO->setStrNome("%".$_POST['extensao']."%", InfraDTO::$OPER_LIKE);
  		
  		$objContatoDTO->adicionarCriterio(array('SinAtivo','Nome'),
  				array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_LIKE),
  				array('S', "%".$_POST['extensao']."%" ),
  				InfraDTO::$OPER_LOGICO_OR);
  		
  		$objContatoDTO->setStrSinContexto('S');
  		$objContatoDTO->setNumMaxRegistrosRetorno(50);
  		$objContatoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objRelTipoContextoPeticionamentoDTO = new RelTipoContextoPeticionamentoDTO();
        $objRelTipoContextoPeticionamentoRN = new GerirTipoContextoPeticionamentoRN();
        $objRelTipoContextoPeticionamentoDTO->retTodos();
        $objRelTipoContextoPeticionamentoDTO->setStrSinSelecaoInteressado('S');
        $arrobjRelTipoContextoPeticionamentoDTO = $objRelTipoContextoPeticionamentoRN->listar( $objRelTipoContextoPeticionamentoDTO );
        if(!empty($arrobjRelTipoContextoPeticionamentoDTO)){
            $arrId = array();
            foreach($arrobjRelTipoContextoPeticionamentoDTO as $item){
                array_push($arrId, $item->getNumIdTipoContextoContato());
            }
            $objContatoDTO->adicionarCriterio(array('IdTipoContextoContato'),
                array(InfraDTO::$OPER_IN),
                array($arrId));
        }

        $objContatoRN = new ContatoRN();
        $arrObjContatoDTO = $objContatoRN->pesquisarRN0471($objContatoDTO);

  		//$objContatoRN = new ContatoRN();  		
  		//$arrObjContatoDTO = $objContatoRN->listarRN0325($objContatoDTO);
  		//$arrObjContatoDTO = $objContatoRN->pesquisarRN0471($objContatoDTO);
  		
  		$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjContatoDTO,'IdContato', 'Nome');
  		InfraAjax::enviarXML($xml);
  		break;
      
   default:
      throw new InfraException("Aчуo '".$_GET['acao_ajax_externo']."' nуo reconhecida pelo controlador AJAX externo.");
  }
  
}catch(Exception $e){
	//LogSEI::getInstance()->gravar('ERRO AJAX: '.$e->__toString());
  InfraAjax::processarExcecao($e);
}
?>