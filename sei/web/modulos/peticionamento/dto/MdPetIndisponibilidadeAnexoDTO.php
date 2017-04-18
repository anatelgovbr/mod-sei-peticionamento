<?
/**
 * ANATEL
 *
 * 29/04/2016 - criado por jaqueline.mendes - CAST
 *
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIndisponibilidadeAnexoDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_indisp_anexo';
  }

  public function montar() {

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
                                   'IdAnexoPeticionamento',
                                   'id_md_pet_anexo');
    
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
    		'IdIndisponibilidade',
    		'id_md_pet_indisponibilidade');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
                                   'IdUnidade',
                                   'id_unidade');
    
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
    		'IdUsuario',
    		'id_usuario');
                                   
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
                                   'Nome',
                                   'nome');
                                   
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
                                   'Tamanho',
                                   'tamanho');
    
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
    								'Hash',
    								'hash');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH,
                                   'Inclusao',
                                   'dth_inclusao');
    
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
                                   'SinAtivo',
                                   'sin_ativo');
    
    $this->configurarPK('IdAnexoPeticionamento', InfraDTO::$TIPO_PK_NATIVA);
    
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
     'SiglaUnidade',
     'sigla',
     'unidade');
     
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
    'SiglaUsuario',
    'sigla',
    'usuario');
    
    
    /*$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
                                   'Hash',
                                   'hash');*/


    
//    $this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'IdAnexoOrigem');
    
    //$this->adicionarAtributo(InfraDTO::$PREFIXO_STR,'SinDuplicando');
    //$this->adicionarAtributo(InfraDTO::$PREFIXO_STR,'SinExclusaoAutomatica');
    
    
    $this->configurarFK('IdUnidade', 'unidade', 'id_unidade');
    $this->configurarFK('IdUsuario', 'usuario', 'id_usuario');
  
  }
}
?>