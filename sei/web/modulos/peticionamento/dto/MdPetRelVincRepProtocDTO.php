<?
/**
* ANATEL
*
* 14/04/2016 - criado por Renato Chaves - CAST
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetRelVincRepProtocDTO extends InfraDTO  {

	public function getStrNomeTabela() {
		return 'md_pet_rel_vincrep_protoc';
	}
	
	public function montar() {
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdVincRepresent',
				'id_md_pet_vinculo_represent');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdProtocolo',
				'id_protocolo');

		$this->configurarFK('IdVincRepresent', 'id_md_pet_vinculo_represent', 'id_md_pet_vinculo_represent');
		$this->configurarFK('IdProtocolo', 'id_protocolo', 'id_protocolo');
		
		$this->configurarPK('IdVincRepresent',InfraDTO::$TIPO_PK_INFORMADO);

		$this->configurarPK('IdProtocolo',InfraDTO::$TIPO_PK_INFORMADO);
		
		//$this->configurarExclusaoLogica('SinAtivo', 'N');
                
	}}
?>