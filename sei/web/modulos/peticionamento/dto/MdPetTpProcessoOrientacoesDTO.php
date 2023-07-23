<?
/**
* ANATEL
*
* 11/05/2016 - criado por jaqueline.mendes - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetTpProcessoOrientacoesDTO extends InfraDTO  {

	public function getStrNomeTabela() {
		return 'md_pet_tp_processo_orientacoes';
	}
	
	
	public function montar() {


		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdTipoProcessoOrientacoesPet',
				'id_md_pet_tp_proc_orientacoes');
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'OrientacoesGerais',
				'orientacoes_gerais');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdConjuntoEstilos',
				'id_conjunto_estilos');
		
		$this->configurarPK('IdTipoProcessoOrientacoesPet', InfraDTO::$TIPO_PK_INFORMADO);
	
	}}
?>