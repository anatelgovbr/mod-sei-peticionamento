<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 25/01/2018 - criado por Usuário
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU
*
* Versão do Gerador de Código: 1.41.0
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntegParametroDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_adm_integ_param';
  }

  public function montar() {

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntegParametro', 'id_md_pet_adm_integ_param');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntegracao', 'id_md_pet_adm_integracao');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Nome', 'nome');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'TpParametro', 'tp_parametro');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'NomeCampo', 'nome_campo');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'ValorPadrao', 'valor_padrao');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeMdPetIntegracao', 'nome', 'md_pet_adm_integracao');

    $this->configurarPK('IdMdPetIntegParametro',InfraDTO::$TIPO_PK_NATIVA);

    $this->configurarFK('IdMdPetIntegracao', 'md_pet_adm_integracao', 'id_md_pet_adm_integracao');
  }
}
