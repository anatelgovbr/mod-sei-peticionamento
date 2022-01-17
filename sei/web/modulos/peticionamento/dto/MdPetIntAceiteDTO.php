<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 10/03/2017 - criado por jaqueline.mendes
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntAceiteDTO extends InfraDTO {

    public function getStrNomeTabela() {
        return 'md_pet_int_aceite';
    }

    public function montar() {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntAceite', 'id_md_pet_int_aceite');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Ip', 'ip');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Data', 'data');
        
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DataConsultaDireta', 'data_consulta_direta');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntRelDestinatario', 'id_md_pet_int_rel_dest');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL, 'IdDocumentoCertidao', 'id_documento_certidao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'TipoAceite', 'tipo_aceite');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUsuario', 'id_usuario');


        $this->configurarPK('IdMdPetIntAceite',InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdPetIntRelDestinatario','md_pet_int_rel_dest','id_md_pet_int_rel_dest');
        $this->configurarFK('IdDocumentoCertidao','documento','id_documento', InfraDTO::$TIPO_FK_OPCIONAL);
        $this->configurarFK('IdContato','usuario u','u.id_contato');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,'IdUsuarioExterno','u.id_usuario','usuario u');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,'IdContato','id_contato','md_pet_int_rel_dest', InfraDTO::$TIPO_FK_OPCIONAL);
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,'IdMdPetIntimacao','id_md_pet_intimacao','md_pet_int_rel_dest');
    }
}
?>