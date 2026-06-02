<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 02/04/2018 - criado por jose vieira
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 * Versão do Gerador de Código: 1.41.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetProRepresWhitDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_pet_adm_pro_repres_whit';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetProRepresWhit', 'id_md_pet_adm_pro_repres_whit');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL, 'IdProtocolo', 'id_procedimento');

        //Procedimento
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'ProtocoloFormatado', 'protocolo_formatado', 'protocolo prot');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'ProtocoloFormatadoPesquisa', 'protocolo_formatado_pesquisa', 'protocolo prot');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'IdTipoProcedimento', 'proced.id_tipo_procedimento', 'procedimento proced');        
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeTipoProcedimento', 'tpro.nome', 'tipo_procedimento tpro');

        $this->configurarPK('IdMdPetProRepresWhit', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdProtocolo', 'protocolo prot', 'prot.id_protocolo');
        $this->configurarFK('IdProtocolo', 'procedimento proced', 'proced.id_procedimento');
        $this->configurarFK('IdTipoProcedimento', 'tipo_procedimento tpro', 'tpro.id_tipo_procedimento');

    }
}
