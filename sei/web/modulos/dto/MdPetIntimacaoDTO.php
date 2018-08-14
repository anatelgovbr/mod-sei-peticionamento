<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntimacaoDTO extends InfraDTO {

    public function getStrNomeTabela() {
        return 'md_pet_intimacao';
    }

    public function montar() {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntimacao', 'id_md_pet_intimacao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntTipoIntimacao', 'id_md_pet_int_tipo_intimacao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinTipoAcessoProcesso', 'sin_tipo_acesso_processo');

        $this->configurarPK('IdMdPetIntimacao',InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdPetIntTipoIntimacao', 'md_pet_int_tipo_intimacao ti', 'ti.id_md_pet_int_tipo_intimacao');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeTipoIntimacao','ti.nome','md_pet_int_tipo_intimacao ti');
    }
}
?>