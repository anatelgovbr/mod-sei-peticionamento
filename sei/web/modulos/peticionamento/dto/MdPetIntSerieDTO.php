<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 07/12/2016 - criado por Marcelo Bezerra - CAST
 *
 * Versão do Gerador de Código: 1.39.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntSerieDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_pet_int_serie';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdSerie',
            'id_serie');

        $this->configurarPK('IdSerie', InfraDTO::$TIPO_PK_INFORMADO);





        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeSerie', 's.nome', 'serie s');

        $this->configurarFK('IdTipoProcessoPeticionamento', 'md_pet_tipo_processo tp', 'tp.id_md_pet_tipo_processo');
        $this->configurarFK('IdSerie', 'serie s', 's.id_serie');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeSerie', 's.nome', 'serie s');


    }
}

?>