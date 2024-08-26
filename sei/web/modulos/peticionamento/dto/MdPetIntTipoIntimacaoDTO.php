<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 08/12/2016 - criado por Marcelo Bezerra
*
* Versão do Gerador de Código: 1.39.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntTipoIntimacaoDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_int_tipo_intimacao';
  }

  public function montar() {

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
                                   'IdMdPetIntTipoIntimacao',
                                   'id_md_pet_int_tipo_intimacao');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
                                   'Nome',
                                   'nome');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
                                   'TipoRespostaAceita',
                                   'tipo_resposta_aceita');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
                                   'SinAtivo',
                                   'sin_ativo');

    $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjRelIntRespostaDTO');

    $this->configurarPK('IdMdPetIntTipoIntimacao',InfraDTO::$TIPO_PK_NATIVA);

  }
}
?>