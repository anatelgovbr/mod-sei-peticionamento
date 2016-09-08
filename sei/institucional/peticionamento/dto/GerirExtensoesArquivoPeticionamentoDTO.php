<?
/**
* ANATEL
*
* 04/05/2016 - criado por alan.campos - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class GerirExtensoesArquivoPeticionamentoDTO extends InfraDTO  {

	public function getStrNomeTabela() {
		return 'md_pet_extensao_arquivo_perm';
	}
	
	
	public function montar() {
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdExtensaoArquivoPerm',
				'id_md_pet_extensao_arquivo_perm');
	
	
	    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdArquivoExtensao',
				'id_arquivo_extensao');

	    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinPrincipal',
				'sin_principal');
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinAtivo',
				'sin_ativo');
		
		$this->configurarPK('IdExtensaoArquivoPerm',InfraDTO::$TIPO_PK_NATIVA);
		
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
				'Extensao',
				'extensao',
				'arquivo_extensao');
	
		$this->configurarFK('IdArquivoExtensao', 'arquivo_extensao', 'id_arquivo_extensao');
	
	}}
?>