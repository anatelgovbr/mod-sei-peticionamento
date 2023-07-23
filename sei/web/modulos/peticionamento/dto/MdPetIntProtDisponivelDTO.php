<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Vers�o do Gerador de C�digo: 1.40.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntProtDisponivelDTO extends InfraDTO {

    public function getStrNomeTabela() {
        return 'md_pet_int_prot_disponivel';
    }

    public function montar() {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntimacao', 'id_md_pet_intimacao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL, 'IdProtocolo', 'id_protocolo');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntimacaoMdPetIntimacao', 'id_md_pet_intimacao', 'md_pet_intimacao');

        $this->configurarPK('IdProtocolo',InfraDTO::$TIPO_PK_INFORMADO);
        $this->configurarPK('IdMdPetIntimacao',InfraDTO::$TIPO_PK_INFORMADO);

        $this->configurarFK('IdMdPetIntimacao', 'md_pet_intimacao', 'id_md_pet_intimacao');
        $this->configurarFK('IdProtocolo', 'protocolo', 'id_protocolo');
    }
}
?>