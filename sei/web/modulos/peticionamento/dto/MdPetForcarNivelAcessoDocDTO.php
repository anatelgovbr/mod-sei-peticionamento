<?
/**
 * ANATEL
 *
 * 30/08/2016 - criado por jaqueline.mendes@castgroup.com.br - CAST
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetForcarNivelAcessoDocDTO extends InfraDTO {
	
	public function getStrNomeTabela() {
		return 'md_pet_adm_nivel_aces_doc';
	}
	
	public function montar() {
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdForcaNivelAcessoDoc', 'id_md_pet_adm_nivel_aces_doc');
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'NivelAcesso', 'sta_nivel_acesso');
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'TipoPeticionamento', 'sta_tipo_peticionamento');
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdHipoteseLegal', 'id_md_pet_hipodese_legal');
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'IdsTiposDocumento', 'ids_tipos_documento');

		$this->configurarPK('IdForcaNivelAcessoDoc', InfraDTO::$TIPO_PK_NATIVA);
		$this->configurarFK('IdHipoteseLegal', 'hipotese_legal h', 'h.id_hipotese_legal');
		
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'HipoteseLegal', 'h.nome', 'hipotese_legal h');

	}
	
	
}