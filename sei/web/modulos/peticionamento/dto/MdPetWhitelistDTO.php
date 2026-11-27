<?php
/**
 * Generic whitelist data transfer object.
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetWhitelistDTO extends InfraDTO
{
    public function getStrNomeTabela(): ?string
    {
        return 'md_pet_adm_whitelist';
    }

    public function montar(): void
    {
        $this->adicionarAtributoTabela(
            InfraDTO::$PREFIXO_NUM,
            'IdMdPetWhitelist',
            'id_md_pet_adm_whitelist'
        );
        $this->adicionarAtributoTabela(
            InfraDTO::$PREFIXO_STR,
            'StaFinalidade',
            'sta_finalidade'
        );
        $this->adicionarAtributoTabela(
            InfraDTO::$PREFIXO_STR,
            'StaTipoObjeto',
            'sta_tipo_objeto'
        );
        $this->adicionarAtributoTabela(
            InfraDTO::$PREFIXO_DBL,
            'IdObjeto',
            'id_objeto'
        );

        $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'IdObjetos');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'IdentificadorObjeto');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'DescricaoObjeto');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'NaturezaContato');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'DocumentoContato');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'SituacaoContato');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'TipoContato');

        $this->configurarPK('IdMdPetWhitelist', InfraDTO::$TIPO_PK_NATIVA);
    }
}
