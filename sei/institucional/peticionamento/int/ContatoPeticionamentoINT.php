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
		/*
		$objContextoContatoDTO->retStrTelefone();
		$objContextoContatoDTO->retStrFax();
		$objContextoContatoDTO->retStrEmail();
		$objContextoContatoDTO->retStrSitioInternet();
		$objContextoContatoDTO->retStrEndereco();
		$objContextoContatoDTO->retStrBairro();
		$objContextoContatoDTO->retStrSiglaEstado();
		$objContextoContatoDTO->retStrNomeCidade();
		$objContextoContatoDTO->retStrNomePais();
		$objContextoContatoDTO->retStrCep();
		*/
		
		$objContextoContatoDTO->retStrNome();
		$objContextoContatoDTO->retNumIdContato();
		$objContextoContatoDTO->retStrSigla();
		$objContextoContatoDTO->retStrSinAtivo();
		
		$objContextoContatoDTO->setStrSigla( $cpfcnpj );
		$objContextoContatoDTO->setStrSinAtivo('S');
		
		$objContatoRN = new ContatoRN();
		$objContextoContatoDTO = $objContatoRN->consultarRN0324($objContextoContatoDTO);
	
		return $objContextoContatoDTO;
	}
	
}
?>