<?

/**
 * ANATEL
 *
 * 13/04/2016 - criado por jaqueline.mendes - CAST
 *
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetProrrogacaoAutomaticaPrazosDTO extends InfraDTO {

  public function getStrNomeTabela() {
    return null;
  }

  public function montar() {
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR,'SinProrrogacao');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR,'Descricao');
  }
}
?>