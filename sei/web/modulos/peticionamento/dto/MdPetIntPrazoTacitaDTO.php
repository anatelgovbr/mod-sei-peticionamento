<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 06/12/2016 - criado por Wilton J�nior
 *
 * Vers�o do Gerador de C�digo: 1.39.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntPrazoTacitaDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_pet_int_prazo_tacita';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdMdPetIntPrazoTacita',
            'id_md_pet_int_prazo_tacita');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'NumPrazo',
            'num_prazo');

        $this->configurarPK('IdMdPetIntPrazoTacita', InfraDTO::$TIPO_PK_NATIVA);

    }
}

?>