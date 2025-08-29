<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 21/06/2023 - criado por michaelr.colab
*
* Versão do Gerador de Código: 1.43.2
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetRelContSitRfDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_rel_cont_sit_rf';
  }

  public function montar() {

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdContato', 'id_contato');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'CpfCnpj', 'cpf_cnpj');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'CodReceita', 'cod_receita');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DtConsulta', 'dt_consulta');

  }
}
