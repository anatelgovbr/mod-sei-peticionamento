<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntRelTipoRespDTO extends InfraDTO {

    public function getStrNomeTabela() {
        return 'md_pet_int_rel_tipo_resp';
    }

    public function montar() {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntRelTipoResp', 'id_md_pet_int_rel_tipo_resp');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinAtivo', 'sin_ativo');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntimacao', 'id_md_pet_intimacao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntTipoResp', 'id_md_pet_int_tipo_resp');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntimacaoMdPetIntimacao', 'id_md_pet_intimacao', 'md_pet_intimacao');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'TipoPrazoExterno', 'tipo_prazo_externo', 'md_pet_int_tipo_resp');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'TipoDia', 'tipo_dia', 'md_pet_int_tipo_resp');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'Nome', 'nome', 'md_pet_int_tipo_resp');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'ValorPrazoExterno', 'valor_prazo_externo', 'md_pet_int_tipo_resp');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntTipoRespDest', 'id_md_pet_int_rel_tipo_res_des', 'md_pet_int_rel_tpo_res_des');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntRelDest', 'id_md_pet_int_rel_dest', 'md_pet_int_rel_tpo_res_des');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DTH, 'DataLimite', 'data_limite', 'md_pet_int_rel_tpo_res_des');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DTH, 'DataProrrogada', 'data_prorrogada', 'md_pet_int_rel_tpo_res_des');

        $this->configurarPK('IdMdPetIntRelTipoResp',InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdPetIntimacao', 'md_pet_intimacao', 'id_md_pet_intimacao');
        $this->configurarFK('IdMdPetIntTipoResp', 'md_pet_int_tipo_resp', 'id_md_pet_int_tipo_resp');
        $this->configurarFK('IdMdPetIntRelTipoResp', 'md_pet_int_rel_tpo_res_des', 'id_md_pet_int_rel_tipo_resp', InfraDTO::$TIPO_FK_OPCIONAL);

        $this->configurarExclusaoLogica('SinAtivo', 'N');

        $this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'PrazoFinal');

    }
}
?>