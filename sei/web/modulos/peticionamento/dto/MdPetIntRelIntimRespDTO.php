<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 08/12/2016 - criado por Marcelo Bezerra - CAST
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU
*
* Versão do Gerador de Código: 1.39.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntRelIntimRespDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_int_rel_intim_resp';
  }

  public function montar() {

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
                                   'IdMdPetIntTipoIntimacao',
                                   'id_md_pet_int_tipo_intimacao');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
                                   'IdMdPetIntTipoResp',
                                   'id_md_pet_int_tipo_resp');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
                                   'SinAtivo',
                                   'sin_ativo');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,
                                              'IdMdPetIntTipoIntimacaoMdPetIntTipoIntimacao',
                                              'id_md_pet_int_tipo_intimacao',
                                              'md_pet_int_tipo_intimacao');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,
                                              'IdMdPetIntTipoRespMdPetIntTipoResp',
                                              'id_md_pet_int_tipo_resp',
                                              'md_pet_int_tipo_resp');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
                                              'TipoPrazoExternoMdPetIntTipoResp',
                                              'tipo_prazo_externo',
                                              'md_pet_int_tipo_resp');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,
                                              'ValorPrazoExternoMdPetIntTipoResp',
                                              'valor_prazo_externo',
                                              'md_pet_int_tipo_resp');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
                                              'NomeMdPetIntTipoResp',
                                              'nome',
                                              'md_pet_int_tipo_resp');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
                                              'TipoDia',
                                              'tipo_dia',
                                              'md_pet_int_tipo_resp');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
                                              'TipoRespostaAceitaMdPetIntTipoResp',
                                              'tipo_resposta_aceita',
                                              'md_pet_int_tipo_resp');

    $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjRelIntRespostaDTO');

    $this->configurarPK('IdMdPetIntTipoIntimacao',InfraDTO::$TIPO_PK_INFORMADO);
    $this->configurarPK('IdMdPetIntTipoResp',InfraDTO::$TIPO_PK_INFORMADO);

    $this->configurarFK('IdMdPetIntTipoIntimacao', 'md_pet_int_tipo_intimacao', 'id_md_pet_int_tipo_intimacao');
    $this->configurarFK('IdMdPetIntTipoResp', 'md_pet_int_tipo_resp', 'id_md_pet_int_tipo_resp');
    $this->configurarExclusaoLogica('SinAtivo', 'N');

  }
}
?>