<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 21/06/2023 - criado por michaelr.colab
*
* Versão do Gerador de Código: 1.43.2
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetFilaConsultaRfDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_fila_consulta_rf';
  }

  public function montar() {

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetFilaConsultaRf', 'id_md_pet_fila_consulta_rf');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'StaNatureza', 'sta_natureza');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL, 'CpfCnpj', 'cpf_cnpj');

    $this->configurarPK('IdMdPetFilaConsultaRf',InfraDTO::$TIPO_PK_NATIVA);
  }
}
