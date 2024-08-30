<?
/**
* ANATEL
*
* 25/04/2016 - criado por jaqueline.mendes - CAST
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetTamanhoArquivoDTO extends InfraDTO  {

	public function getStrNomeTabela() {
		return 'md_pet_tamanho_arquivo';
	}
	
	
	public function montar() {
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdTamanhoArquivo',
				'id_md_pet_tamanho_arquivo');
	
	
	    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'ValorDocPrincipal',
				'valor_doc_principal');

	    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
	    		'ValorDocComplementar',
	    		'valor_doc_complementar');	
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinAtivo',
				'sin_ativo');
	
		
		$this->configurarPK('IdTamanhoArquivo',InfraDTO::$TIPO_PK_INFORMADO);
	
	}}
?>