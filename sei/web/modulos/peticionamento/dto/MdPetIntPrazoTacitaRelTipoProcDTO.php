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
  	 return 'md_pet_prz_tac_rel_tp_proc';
  }

  public function montar() {
  	
//	  $this->configurarPK('IdMdPetIntPrazoTacitaRelTipoProc', InfraDTO::$TIPO_PK_INFORMADO);
	
	  $this->configurarFK('IdMdPetIntPrazoTacita','md_pet_int_prazo_tacita','id_md_pet_int_prazo_tacita');
	  $this->configurarFK('IdTipoProcedimento','tipo_procedimento','id_tipo_procedimento');
	
	  $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntPrazoTacita', 'id_md_pet_int_prazo_tacita');
	  $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdTipoProcedimento', 'id_tipo_procedimento');
	
	  $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdTipoProcedimento', 'id_tipo_procedimento', 'tipo_procedimento');
	  $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeTipoProcedimento', 'nome', 'tipo_procedimento');
	  
  }
  
}
