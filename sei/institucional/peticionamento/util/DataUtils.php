<?php
class DataUtils extends InfraPDF {
	
	var $datalargura;
	
	function DataUtils () {
	}
	
	function setFormat($valor, $formato='dd/mm/yyyy hh:mm:ss') {
		$formatolargura = strlen($formato);
		$datalargura = strlen($valor);
		return substr($valor, 0, $formatolargura-$datalargura);
	}

}
?>
