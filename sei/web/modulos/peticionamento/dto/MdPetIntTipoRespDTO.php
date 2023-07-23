<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
*
* 08/12/2016 - criado por Marcelo Bezerra - CAST
*
* Vers�o do Gerador de C�digo: 1.39.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntTipoRespDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_int_tipo_resp';
  }

  public function montar() {

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
                                   'IdMdPetIntTipoResp',
                                   'id_md_pet_int_tipo_resp');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
                                   'TipoPrazoExterno',
                                   'tipo_prazo_externo');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
                                   'ValorPrazoExterno',
                                   'valor_prazo_externo');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
                                   'Nome',
                                   'nome');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
                                   'TipoRespostaAceita',
                                   'tipo_resposta_aceita');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
                                   'SinAtivo',
                                   'sin_ativo');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
                                   'TipoDia',
                                   'tipo_dia');

    $this->configurarPK('IdMdPetIntTipoResp',InfraDTO::$TIPO_PK_NATIVA);

    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'PalavrasPesquisa');

  }
}
?>