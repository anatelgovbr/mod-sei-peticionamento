<?
/**
 * ANATEL
 *
 * 14/04/2016 - criado por Renato Chaves - CAST
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetTipoPoderLegalDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_pet_adm_tipo_poder';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdTipoPoderLegal',
            'id_md_pet_tipo_poder');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'Nome',
            'nome');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTA,
            'DtaCadastro',
            'data_cadastro');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'StaSistema',
            'sta_sistema');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinAtivo', 'sin_ativo');

        $this->configurarPK('IdTipoPoderLegal', InfraDTO::$TIPO_PK_NATIVA);

        //$this->configurarExclusaoLogica('SinAtivo', 'N');

    }
}

?>