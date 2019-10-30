<?
/**
* ANATEL
*
* 11/05/2016 - criado por jaqueline.mendes - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntOrientacoesDTO extends InfraDTO  {

	public function getStrNomeTabela() {
		return 'md_pet_int_tp_int_orient';
	}
		
	public function montar() {
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdIntOrientacoesTipoDestinatario',
				'id_md_pet_int_tp_int_orient');
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'OrientacoesTipoDestinatario',
				'orientacoes_tp_destinatario');
                
                $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdConjuntoEstilos',
				'id_conjunto_estilos');
				
		$this->configurarPK('IdIntOrientacoesTipoDestinatario', InfraDTO::$TIPO_PK_INFORMADO);
	
	}}
?>