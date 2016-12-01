<?php
class UrlUtils extends InfraPDF {
	
	function UrlUtils () {
	}
	
	/**
	 * Validate field "Url - Dispositivo Normativo".
	 *
	 * @access public
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @return void
	 */
	public static function validarStrURL($url, InfraException $objInfraException, $msgTamanho='', $msgInvalida='') {
	
		// Se não mandou mensagem, utiliza a padrão
		if ($msgTamanho==''){
			$msgTamanho=='Url possui tamanho superior a 2083 caracteres.';
		}

		// Se não mandou mensagem, utiliza a padrão
		if ($msgInvalida=='') {
			$msgTamanho=='URL da Norma inválido.';
		}		
		
		
		// VERIFICA SE O CAMPO FOI PREENCHIDO
		if (!(InfraString::isBolVazia ($url))) {
			// Valida Quantidade de Caracteres
			if (strlen($url)>2083) {
				//$objInfraException->adicionarValidacao($msgTamanho);
				return $msgTamanho;
			}
			
			// Validando
			if(!filter_var($url, FILTER_VALIDATE_URL)) {
				//$objInfraException->adicionarValidacao($msgInvalida);
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
