<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 02/04/2018 - criado por jose vieira
 *
 * Versão do Gerador de Código: 1.41.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetVinculoDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_pet_vinculo';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetVinculo', 'id_md_pet_vinculo');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdContato', 'id_contato');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL, 'IdProtocolo', 'id_procedimento');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinValidado', 'sin_validado');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinWebService', 'sin_web_service');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'TpVinculo', 'tp_vinculo');

        //Procedimento
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'ProtocoloFormatado', 'protocolo_formatado', 'protocolo prot');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'ProtocoloFormatadoPesquisa', 'protocolo_formatado_pesquisa', 'protocolo prot');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'IdTipoProcedimento', 'proced.id_tipo_procedimento', 'procedimento proced');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeTipoProcedimento', 'tpro.nome', 'tipo_procedimento tpro');

        //Representante
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdContatoRepresentante', 'repr.id_contato', 'md_pet_vinculo_represent repr');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdPetVinculoRepresent', 'repr.id_md_pet_vinculo_represent', 'md_pet_vinculo_represent repr');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'TipoRepresentante', 'repr.tipo_representante', 'md_pet_vinculo_represent repr');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'StaResponsavelLegal', 'repr.sin_ativo', 'md_pet_vinculo_represent repr');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SinAtivoRepresentante', 'repr.sin_ativo', 'md_pet_vinculo_represent repr');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'StaEstado', 'repr.sta_estado', 'md_pet_vinculo_represent repr');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DTH, 'DataVinculo', 'repr.data_cadastro', 'md_pet_vinculo_represent repr');
        
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DTH, 'DataEncerramento', 'repr.data_encerramento', 'md_pet_vinculo_represent repr');

        //representante - contato
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeContatoRepresentante', 'contrep.nome', 'contato contrep');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'CpfContatoRepresentante', 'contrep.cpf', 'contato contrep');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'EmailContatoRepresentante', 'contrep.email', 'contato contrep');

        // Contato do Vínculo
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'RazaoSocialNomeVinc', 'contvinc.nome', 'contato contvinc');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL, 'CNPJ', 'contvinc.cnpj', 'contato contvinc');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'CNPJPesquisa', 'contvinc.cnpj', 'contato contvinc');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdCidadeContatoVinc', 'contvinc.id_cidade', 'contato contvinc');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdUfContatoVinc', 'contvinc.id_uf', 'contato contvinc');

        // Cidade e Uf do Vínculo
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeCidadeContatoVinc', 'cidvinc.nome', 'cidade cidvinc');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SiglaUfContatoVinc', 'ufvinc.sigla', 'uf ufvinc');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdUsuario', 'usuario.id_usuario', 'usuario usuario');

        $this->configurarPK('IdMdPetVinculo', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdProtocolo', 'protocolo prot', 'prot.id_protocolo');
        $this->configurarFK('IdProtocolo', 'procedimento proced', 'proced.id_procedimento');
        $this->configurarFK('IdTipoProcedimento', 'tipo_procedimento tpro', 'tpro.id_tipo_procedimento');

        $this->configurarFK('IdMdPetVinculo', 'md_pet_vinculo_represent repr', 'repr.id_md_pet_vinculo');

        $this->configurarFK('IdContatoRepresentante', 'contato contrep', 'contrep.id_contato');

        $this->configurarFK('IdContato', 'contato contvinc', 'contvinc.id_contato');
        $this->configurarFK('IdContatoRepresentante', 'usuario usuario', 'usuario.id_contato');
        $this->configurarFK('IdCidadeContatoVinc', 'cidade cidvinc', 'cidvinc.id_cidade');
        $this->configurarFK('IdUfContatoVinc', 'uf ufvinc', 'ufvinc.id_uf');

    }
}
