<?
/**
 * ANATEL
 *
 * @dataProvider 19/05/2021
 * @author Lino - Felipe Lino <felipe.silva@gt1tecnologia.com.br>
 *
 */

class MdPetTipoPoderLegalAPIWS
{

    private $IdTipoPoderLegal;
    private $Nome;
    private $SinAtivo;

    /**
     * @return mixed
     */
    public function getIdTipoPoderLegal()
    {
        return $this->IdTipoPoderLegal;
    }

    /**
     * @param mixed IdTipoPoderLegal
     */
    public function setIdTipoPodeLegal($IdTipoPoderLegal)
    {
        $this->IdTipoPoderLegal = $IdTipoPoderLegal;
    }

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
    public function getSinAtivo()
    {
        return $this->SinAtivo;
    }

    /**
     * @param mixed $SinAtivo
     */
    public function setSinAtivo($SinAtivo)
    {
        $this->SinAtivo = $SinAtivo;
    }
}
