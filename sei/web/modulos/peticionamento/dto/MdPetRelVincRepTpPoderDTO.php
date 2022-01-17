<?
/**
* ANATEL
*
* 14/04/2016 - criado por Renato Chaves - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetRelVincRepTpPoderDTO extends InfraDTO  {

	public function getStrNomeTabela() {
		return 'md_pet_rel_vincrep_tipo_poder';
	}
	
	public function montar() {
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdTipoPoderLegal',
				'id_md_pet_tipo_poder');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdVinculoRepresent',
				'id_md_pet_vinculo_represent');

		$this->configurarFK('IdTipoPoderLegal', 'id_md_pet_tipo_poder', 'id_md_pet_tipo_poder');
		$this->configurarFK('IdVinculoRepresent', 'id_md_pet_vinculo_represent', 'id_md_pet_vinculo_represent');
		
		$this->configurarPK('IdTipoPoderLegal',InfraDTO::$TIPO_PK_INFORMADO);

		$this->configurarPK('IdTipoPoderLegal',InfraDTO::$TIPO_PK_INFORMADO);
		
		//$this->configurarExclusaoLogica('SinAtivo', 'N');
                
	}}
?>