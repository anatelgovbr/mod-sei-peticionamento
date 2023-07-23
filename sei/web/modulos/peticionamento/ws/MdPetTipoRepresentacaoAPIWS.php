<?
/**
 * ANATEL
 *
 * @dataProvider 24/05/2021
 * @author Lino - Felipe Lino <felipe.silva@gt1tecnologia.com.br>
 *
 */

class MdPetTipoRepresentacaoAPIWS
{

    private $Nome;
    private $StrTipoRepresentacao;

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->Nome;
    }

    /**
     * @param mixed $Nome
     */
    public function setNome($Nome)
    {
        $this->Nome = $Nome;
    }

    /**
     * @return mixed
     */
    public function getStrTipoRepresentacao()
    {
        return $this->StrTipoRepresentacao;
    }

    /**
     * @param mixed $StrTipoRepresentacao
     */
    public function setStrTipoRepresentacao($StrTipoRepresentacao)
    {
        $this->StrTipoRepresentacao = $StrTipoRepresentacao;
    }


}
