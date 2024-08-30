<?
/**
* ANATEL
*
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU
*
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetRelIntDestExternoDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_pet_rel_int_dest_extern';
    }

    public function montar()
    {
      
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntRelDestinatario', 'id_md_pet_int_rel_dest');

	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdAcessoExterno', 'id_acesso_externo');

        $this->configurarFK('IdMdPetIntRelDestinatario', 'id_md_pet_int_rel_dest', 'id_md_pet_int_rel_dest');

        $this->configurarPK('IdMdPetIntRelDestinatario',InfraDTO::$TIPO_PK_INFORMADO);
        $this->configurarPK('IdAcessoExterno',InfraDTO::$TIPO_PK_INFORMADO);

    }
}

?>