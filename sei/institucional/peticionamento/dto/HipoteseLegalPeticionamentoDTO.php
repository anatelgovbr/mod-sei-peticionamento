<?
/**
 * ANATEL
 *
 * 30/08/2016 - criado por jaqueline.mendes@castgroup.com.br - CAST
 *
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class HipoteseLegalPeticionamentoDTO extends InfraDTO {
	
	public function getStrNomeTabela() {
		return 'md_pet_hipotese_legal';
	}
	
	public function montar() {
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdHipoteseLegalPeticionamento',
				'id_md_pet_hipotese_legal');
		
		$this->configurarPK('IdHipoteseLegalPeticionamento',InfraDTO::$TIPO_PK_INFORMADO);
		$this->configurarFK('IdHipoteseLegalPeticionamento', 'hipotese_legal h', 'h.id_hipotese_legal');
		
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'Nome', 'h.nome', 'hipotese_legal h');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'BaseLegal', 'h.base_legal', 'hipotese_legal h');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SinAtivo', 'h.sin_ativo', 'hipotese_legal h');
	}
	
	
}