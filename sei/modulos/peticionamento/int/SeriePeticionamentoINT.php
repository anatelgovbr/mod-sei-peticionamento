<?
/**
*
* 19/04/2016 - criado por jaqueline.mendes - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class SeriePeticionamentoINT extends SerieINT {

  public static function autoCompletarSeries( $strPalavrasPesquisa, $tipoDoc ){
      	
  	$objSerieDTO = new SerieDTO();
  	$objSerieDTO->retNumIdSerie();
  	$objSerieDTO->retStrNome();
  
  	if (!InfraString::isBolVazia($strPalavrasPesquisa)){
  		
  		$strPalavrasPesquisa = InfraString::prepararIndexacao($strPalavrasPesquisa);
  			
  		$arrPalavrasPesquisa = explode(' ',$strPalavrasPesquisa);
  		$numPalavras = count($arrPalavrasPesquisa);
  		for($i=0;$i<$numPalavras;$i++){
  			$arrPalavrasPesquisa[$i] = '%'.$arrPalavrasPesquisa[$i].'%';
  		}
  
  		if ($numPalavras==1){
  			$objSerieDTO->setStrNome($arrPalavrasPesquisa[0],InfraDTO::$OPER_LIKE);
  		}else{
  			$a = array_fill(0,count($arrPalavrasPesquisa),'Nome');
  			$c = array_fill(0,count($arrPalavrasPesquisa),InfraDTO::$OPER_LIKE);
  			$d = array_fill(0,count($arrPalavrasPesquisa)-1,InfraDTO::$OPER_LOGICO_OR);
  			$objSerieDTO->adicionarCriterio($a,$c,$arrPalavrasPesquisa,$d);
  		}
  	}
    
  	
  	if( $tipoDoc == "1" || $tipoDoc == TipoProcessoPeticionamentoRN::$DOC_GERADO ){
  		$aplicalidade = SerieRN::$TA_INTERNO;
  	} else {
  		$aplicalidade = SerieRN::$TA_EXTERNO;
  	}
  	
  	//$aplicalidade = $tipoDoc == TipoProcessoPeticionamentoRN::$DOC_GERADO ? SerieRN::$TA_INTERNO : SerieRN::$TA_EXTERNO;
  	
 /* 	$objSerieDTO->adicionarCriterio(array('StaAplicabilidade', 'SinInterno'),
  			array(InfraDTO::$OPER_IN, InfraDTO::$OPER_IGUAL),
  			array(array(SerieRN::$TA_TODOS, $aplicalidade),'S'),
  			InfraDTO::$OPER_LOGICO_OR);*/
  	
  	//Alterado o Ajax para ter o mesmo comportamento da Modal - Instrução do Gestor.
  	$objSerieDTO->adicionarCriterio(array('StaAplicabilidade'),
  			array(InfraDTO::$OPER_IN),
  			array(array(SerieRN::$TA_TODOS, $aplicalidade)));
  	
  	$objSerieDTO->setNumMaxRegistrosRetorno(50);  
  	$objSerieDTO->setStrSinAtivo('s');
  	$objSerieDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
  
  	$objSerieRN = new SerieRN();
  	$arrObjSerieDTO = $objSerieRN->listarRN0646($objSerieDTO);
  
  	return $arrObjSerieDTO;
  }
  
}
?>