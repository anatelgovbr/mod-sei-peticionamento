<?
/**
* ANATEL
*
* 04/08/2016 - criado por jaqueline.mendes@castgroup.com.br - CAST  (EU6155 - SM)
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetRelTpProcessoUnidDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_rel_tp_processo_unid';
  }

  public function montar() {

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
                                   'IdTipoProcessoPeticionamento',
                                   'id_md_pet_tipo_processo');
    
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
						    		'IdUnidade',
						    		'id_unidade');
    
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
    								'StaTipoUnidade',
    								'sta_tp_unidade');    
        
    $this->configurarPK('IdTipoProcessoPeticionamento',InfraDTO::$TIPO_PK_INFORMADO);
    $this->configurarPK('IdUnidade',InfraDTO::$TIPO_PK_INFORMADO);
    
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'siglaUnidade', 'u.sigla', 'unidade u');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'descricaoUnidade', 'u.descricao', 'unidade u');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdOrgaoUnidade', 'u.id_orgao', 'unidade u');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'DescricaoOrgao', 'o.descricao', 'orgao o');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SiglaOrgao', 'o.sigla', 'orgao o');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'DescricaoOrgao', 'o.descricao', 'orgao o');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdCidadeContato', 'c.id_cidade', 'contato c');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdContato', 'u.id_contato', 'unidade u');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdUf', 'c.id_uf', 'contato c');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SiglaUf', 'u1.sigla', 'uf u1');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeCidade', 'c1.nome', 'cidade c1');
    
    $this->configurarFK('IdTipoProcessoPeticionamento', 'md_pet_tipo_processo tp', 'tp.id_md_pet_tipo_processo');
    $this->configurarFK('IdUnidade', 'unidade u', 'u.id_unidade');
    $this->configurarFK('IdOrgaoUnidade', 'orgao o', 'o.id_orgao');
    $this->configurarFK('IdContato', 'contato c', 'c.id_contato');
    $this->configurarFK('IdCidadeContato', 'cidade c1', 'c1.id_cidade');
    $this->configurarFK('IdUf', 'uf u1', 'u1.id_uf', InfraDTO::$TIPO_FK_OPCIONAL);
    

  }
}
?>