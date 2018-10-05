<?php
class MdPetDataUtils extends InfraPDF {
	
	var $datalargura;
	
	public function MdPetDataUtils () {
	}
	
	public static function setFormat($valor, $formato='dd/mm/yyyy hh:mm:ss') {
		$formatolargura = strlen($formato);
		$datalargura = strlen($valor);
		return substr($valor, 0, $formatolargura-$datalargura);
	}

}
?>