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
	
		$objContextoContatoDTO = new ContatoDTO();
	
		$objContextoContatoDTO->retStrNome();
		$objContextoContatoDTO->retNumIdContato();
		$objContextoContatoDTO->retStrSigla();
		$objContextoContatoDTO->retStrSinAtivo();
		
		$objContextoContatoDTO->adicionarCriterio(array('Cpf', 'Cnpj', 'SinAtivo'),
				
				array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
				
				array( $cpfcnpj, $cpfcnpj,'S'),
				
				array(InfraDTO::$OPER_LOGICO_OR, 
					  InfraDTO::$OPER_LOGICO_AND)
		);
		
		//$objContextoContatoDTO->setStrSigla( $cpfcnpj );
		//$objContextoContatoDTO->setStrSinAtivo('S');
	
		$objContatoRN = new ContatoRN();
		$arrObjContextoContatoDTO = $objContatoRN->listarRN0325($objContextoContatoDTO);
		
		$total = 0;
		
		if( $arrObjContextoContatoDTO != null && count( $arrObjContextoContatoDTO ) > 0  ){
			$total = count( $arrObjContextoContatoDTO );
		}
		
		return $total;
	}
	
}
?>