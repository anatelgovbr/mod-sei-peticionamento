<?php
class MdPetUrlUtils extends InfraPDF {
	
	function MdPetUrlUtils () {
	}
	
	/**
	 * Validate field "Url - Dispositivo Normativo".
	 *
	 * @access public
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @return void
	 */
	public static function validarStrURL($url, InfraException $objInfraException, $msgTamanho='', $msgInvalida='') {
	
		// Se n�o mandou mensagem, utiliza a padr�o
		if ($msgTamanho==''){
			$msgTamanho=='Url possui tamanho superior a 2083 caracteres.';
		}

		// Se n�o mandou mensagem, utiliza a padr�o
		if ($msgInvalida=='') {
			$msgTamanho=='URL da Norma inv�lido.';
		}		
		
		
		// VERIFICA SE O CAMPO FOI PREENCHIDO
		if (!(InfraString::isBolVazia ($url))) {
			// Valida Quantidade de Caracteres
			if (strlen($url)>2083) {
				return $msgTamanho;
			}
			
			// Validando
			if(!filter_var($url, FILTER_VALIDATE_URL)) {
				return $msgInvalida;
			}
			
			// Sucesso
			return true;
		} else {
			return false;
		}
			
	}

}
?>
