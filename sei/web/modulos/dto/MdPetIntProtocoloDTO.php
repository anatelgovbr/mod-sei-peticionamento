<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntProtocoloDTO extends InfraDTO {

    public function getStrNomeTabela() {
        return 'md_pet_int_protocolo';
    }

    public function montar() {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntProtocolo', 'id_md_pet_int_protocolo');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinPrincipal', 'sin_principal');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntimacao', 'id_md_pet_intimacao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL, 'IdProtocolo', 'id_protocolo');

        $this->configurarPK('IdMdPetIntProtocolo',InfraDTO::$TIPO_PK_NATIVA);

        //FKs
        $this->configurarFK('IdMdPetIntimacao', 'md_pet_intimacao m', 'm.id_md_pet_intimacao');
        
        $this->configurarFK('IdMdPetTipoIntimacao', 'md_pet_int_tipo_intimacao', 'id_md_pet_int_tipo_intimacao');

        $this->configurarFK('IdProtocolo', 'protocolo p', 'p.id_protocolo');
        $this->configurarFK('IdDocumento', 'documento d', 'd.id_documento');
        $this->configurarFK('IdSerie', 'serie', 'id_serie');
        $this->configurarFK('IdProcedimento', 'procedimento', 'id_procedimento');
        $this->configurarFK('IdProtocoloProcedimento', 'protocolo p2', 'p2.id_protocolo');

        //atributos relacionados
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntimacao', 'm.id_md_pet_intimacao', 'md_pet_intimacao m');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NumeroDocumento', 'd.numero', 'documento d');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL, 'IdProcedimento','d.id_procedimento','documento d');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdSerie','d.id_serie','documento d');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeSerie','nome','serie');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL, 'IdProcedimento','d.id_procedimento','documento d');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdTipoProcedimento','id_tipo_procedimento','procedimento');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL, 'IdDocumento','p.id_protocolo','protocolo p');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'ProtocoloFormatadoDocumento','p.protocolo_formatado','protocolo p');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL, 'IdProtocoloProcedimento','d.id_procedimento','documento d');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'ProtocoloFormatadoProcedimento','p2.protocolo_formatado','protocolo p2');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdPetTipoIntimacao','m.id_md_pet_int_tipo_intimacao','md_pet_intimacao m');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeTipoIntimacao','nome','md_pet_int_tipo_intimacao');

    }
}
?>