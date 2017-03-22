<?
/**
*
* 22/08/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class ContatoPeticionamentoINT extends ContatoINT {

	public static function getContatoByCPFCNPJ( $cpfcnpj ){
	
		$objContextoContatoDTO = new ContatoDTO();
		
		$objContextoContatoDTO->retStrNome();
		$objContextoContatoDTO->retNumIdContato();
		$objContextoContatoDTO->retStrSigla();
		$objContextoContatoDTO->retStrSinAtivo();
		$objContextoContatoDTO->retNumIdUsuarioCadastro();

		$objContextoContatoDTO->adicionarCriterio(array('Cpf', 'Cnpj', 'SinAtivo'),
				
				array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
				
				array( $cpfcnpj, $cpfcnpj,'S'),
				
				array(InfraDTO::$OPER_LOGICO_OR, 
					  InfraDTO::$OPER_LOGICO_AND)
		);
		
		//$objContextoContatoDTO->setStrSigla( $cpfcnpj );
		//$objContextoContatoDTO->setStrSinAtivo('S');
		
		$objContatoRN = new ContatoRN();
		$objContextoContatoDTO = $objContatoRN->consultarRN0324($objContextoContatoDTO);
	
		return $objContextoContatoDTO;
	}
	
	public static function getTotalContatoByCPFCNPJ( $cpfcnpj ){
		//Contato
		$objContextoContatoDTO = new ContatoDTO();
		$objContextoContatoDTO->retStrNome();
		$objContextoContatoDTO->retNumIdContato();
		$objContextoContatoDTO->retNumIdUsuarioCadastro();
		$objContextoContatoDTO->retStrSigla();
		$objContextoContatoDTO->retStrSinAtivo();
		$objContextoContatoDTO->setDistinct(true);
		$objContextoContatoDTO->adicionarCriterio(array('Cpf', 'Cnpj'),
				array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
				array( $cpfcnpj, $cpfcnpj),
				array(InfraDTO::$OPER_LOGICO_OR)
		);

		$objContatoRN = new ContatoRN();
		$arrObjContextoContatoDTO = $objContatoRN->listarRN0325($objContextoContatoDTO);

		if (count($arrObjContextoContatoDTO)==0) {
			return null;
		} else if (count($arrObjContextoContatoDTO)>1) {
			$arrObjContextoContato = InfraArray::converterArrInfraDTO($arrObjContextoContatoDTO,'IdUsuarioCadastro');
			$arrObjContextoContato = array_filter($arrObjContextoContato);
			if (count($arrObjContextoContato)>0){
				//Usuário Externo
				$objUsuarioDTO = new UsuarioDTO();
				$objUsuarioDTO->retNumIdUsuario();
				$objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_EXTERNO);
				$objUsuarioDTO->setDistinct(true);
				$objUsuarioDTO->adicionarCriterio(
						array('IdUsuario'),
						array(InfraDTO::$OPER_IN),
						array($arrObjContextoContato)
				);

				$objUsuarioRN = new UsuarioRN();
				$arrObjUsuarioDTO = $objUsuarioRN->listarRN0490($objUsuarioDTO);

				if (count($arrObjUsuarioDTO)>0) {
					$arrObjUsuario = InfraArray::converterArrInfraDTO($arrObjUsuarioDTO,'IdUsuario');

					//Contato Filtrado
					$objContextoContatoDTO = new ContatoDTO();
					$objContextoContatoDTO->retStrNome();
					$objContextoContatoDTO->retNumIdContato();
					$objContextoContatoDTO->retNumIdUsuarioCadastro();
					$objContextoContatoDTO->retStrSigla();
					$objContextoContatoDTO->retStrSinAtivo();
					$objContextoContatoDTO->setDistinct(true);
					$objContextoContatoDTO->setOrd('IdContato', InfraDTO::$TIPO_ORDENACAO_DESC);
					$objContextoContatoDTO->adicionarCriterio(array('Cpf', 'Cnpj'),
							array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
							array( $cpfcnpj, $cpfcnpj),
							array(InfraDTO::$OPER_LOGICO_OR)
					);
					$objContextoContatoDTO->adicionarCriterio(array('IdUsuarioCadastro'),
							array(InfraDTO::$OPER_DIFERENTE),
							array(NULL)
					);
					$objContextoContatoDTO->adicionarCriterio(
							array('IdUsuarioCadastro'),
							array(InfraDTO::$OPER_IN),
							array($arrObjUsuario)
					);
					$objContatoRN = new ContatoRN();
					$arrObjContextoContatoDTO = $objContatoRN->listarRN0325($objContextoContatoDTO);

				}else{
					return null;
				}
			}else{
				return null;
			}
		}
		return $arrObjContextoContatoDTO;
	}

}
?>