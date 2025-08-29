<?
/**
* ANATEL
*
* 08/03/2024 - criado por Gabriel Glauber - SPASSU
*
* Versão do Gerador de Código: 1.39.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntPrazoTacitaRelTipoProcDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_adm_nivel_aces_doc_rel_tp_doc';
  }

  public function montar() {
	
	  $this->configurarFK('IdMdPetAdmNivelAcesDoc','md_pet_adm_nivel_aces_doc','id_md_pet_adm_nivel_aces_doc');
	  $this->configurarFK('IdSerie','serie','id_serie');
	
	  $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntPrazoTacita', 'id_md_pet_int_prazo_tacita');
	  $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdTipoProcedimento', 'id_tipo_procedimento');
	
	  $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdTipoProcedimento', 'id_tipo_procedimento', 'tipo_procedimento');
	  $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeDocumento', 'nome', 'tipo_procedimento');
	  
  }
  
}
