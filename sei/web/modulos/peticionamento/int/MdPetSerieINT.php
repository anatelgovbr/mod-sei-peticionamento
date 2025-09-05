<?
/**
*
* 19/04/2016 - criado por jaqueline.mendes - CAST
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetSerieINT extends SerieINT {

  public static function autoCompletarSeries( $strPalavrasPesquisa, $tipoDoc ){
      	
  	$objSerieDTO = new SerieDTO();
  	$objSerieDTO->retNumIdSerie();
  	$objSerieDTO->retStrNome();
  
  	if (!InfraString::isBolVazia($strPalavrasPesquisa)){
  			
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
  			$d = array_fill(0,count($arrPalavrasPesquisa)-1,InfraDTO::$OPER_LOGICO_AND);
  			$objSerieDTO->adicionarCriterio($a,$c,$arrPalavrasPesquisa,$d);
  		}
  	}
    
  	
  	if( $tipoDoc == "1" || $tipoDoc == MdPetTipoProcessoRN::$DOC_GERADO ){
  		$aplicalidade = SerieRN::$TA_INTERNO;
  	} else if($tipoDoc == "2" || $tipoDoc == MdPetTipoProcessoRN::$DOC_EXTERNO){
  		$aplicalidade = SerieRN::$TA_EXTERNO;
  	} else if($tipoDoc == "3" || $tipoDoc == MdPetTipoProcessoRN::$DOC_FORMULARIO){
	    $aplicalidade = SerieRN::$TA_FORMULARIO;
    }

	//Alterado o Ajax para ter o mesmo comportamento da Modal - Instrução do Gestor.
	$objSerieDTO->adicionarCriterio(array('StaAplicabilidade'),
			array(InfraDTO::$OPER_IN),
			array(array(SerieRN::$TA_INTERNO_EXTERNO, $aplicalidade)));

  	$objSerieDTO->setNumMaxRegistrosRetorno(50);  
  	$objSerieDTO->setStrSinAtivo('S');
  	$objSerieDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
  
  	$objSerieRN = new SerieRN();
  	$arrObjSerieDTO = $objSerieRN->listarRN0646($objSerieDTO);
  
  	return $arrObjSerieDTO;
  }

    public static function autoCompletarSeriesIntimacao($strPalavrasPesquisa)
    {

        $objSerieDTO = new SerieDTO();
        $objSerieDTO->retNumIdSerie();
        $objSerieDTO->retStrNome();

        if (!InfraString::isBolVazia($strPalavrasPesquisa)) {

            $arrPalavrasPesquisa = explode(' ', $strPalavrasPesquisa);
            $numPalavras = count($arrPalavrasPesquisa);
            for ($i = 0; $i < $numPalavras; $i++) {
                $arrPalavrasPesquisa[$i] = '%' . $arrPalavrasPesquisa[$i] . '%';
            }

            if ($numPalavras == 1) {
                $objSerieDTO->setStrNome($arrPalavrasPesquisa[0], InfraDTO::$OPER_LIKE);
            } else {
                $a = array_fill(0, count($arrPalavrasPesquisa), 'Nome');
                $c = array_fill(0, count($arrPalavrasPesquisa), InfraDTO::$OPER_LIKE);
                $d = array_fill(0, count($arrPalavrasPesquisa) - 1, InfraDTO::$OPER_LOGICO_AND);
                $objSerieDTO->adicionarCriterio($a, $c, $arrPalavrasPesquisa, $d);
            }
        }


        $objSerieDTO->adicionarCriterio(array('StaAplicabilidade'),
            array(InfraDTO::$OPER_IN),
            array(array(SerieRN::$TA_INTERNO, SerieRN::$TA_INTERNO_EXTERNO)));

        $objSerieDTO->setNumMaxRegistrosRetorno(50);
        $objSerieDTO->setStrSinAtivo('S');
        $objSerieDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objSerieRN = new SerieRN();
        $arrObjSerieDTO = $objSerieRN->listarRN0646($objSerieDTO);

        return $arrObjSerieDTO;
    }

  public static function autoCompletarFormulario( $strPalavrasPesquisa ){

    $objTipoFormularioDTO = new TipoFormularioDTO();
    $objTipoFormularioDTO->retNumIdTipoFormulario();
    $objTipoFormularioDTO->retStrNome();


    if (!InfraString::isBolVazia($strPalavrasPesquisa)){

      $arrPalavrasPesquisa = explode(' ',$strPalavrasPesquisa);
      $numPalavras = count($arrPalavrasPesquisa);
      for($i=0;$i<$numPalavras;$i++){
        $arrPalavrasPesquisa[$i] = '%'.$arrPalavrasPesquisa[$i].'%';
      }

      if ($numPalavras==1){
        $objTipoFormularioDTO->setStrNome($arrPalavrasPesquisa[0],InfraDTO::$OPER_LIKE);
      }else{
        $a = array_fill(0,count($arrPalavrasPesquisa),'Nome');
        $c = array_fill(0,count($arrPalavrasPesquisa),InfraDTO::$OPER_LIKE);
        $d = array_fill(0,count($arrPalavrasPesquisa)-1,InfraDTO::$OPER_LOGICO_AND);
        $objTipoFormularioDTO->adicionarCriterio($a,$c,$arrPalavrasPesquisa,$d);
      }
    }

    $objTipoFormularioDTO->setNumMaxRegistrosRetorno(50);
    $objTipoFormularioDTO->setStrSinAtivo('S');
    $objTipoFormularioDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

    $objTipoFormularioRN = new TipoFormularioRN();
    $arrObjTipoFormularioDTO = $objTipoFormularioRN->listar($objTipoFormularioDTO);

    return $arrObjTipoFormularioDTO;
  }
  
}
?>