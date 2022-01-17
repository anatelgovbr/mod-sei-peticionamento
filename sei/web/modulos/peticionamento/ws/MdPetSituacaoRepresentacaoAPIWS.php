<?
/**
 * ANATEL
 *
 * @dataProvider 19/05/2021
 * @author Lino - Felipe Lino <felipe.silva@gt1tecnologia.com.br>
 *
 */

class MdPetSituacaoRepresentacaoAPIWS
{

    private $StaEstado;
    private $Nome;

    /**
     * @return mixed
     */
    public function getStaEstado()
    {
        return $this->StaEstado;
    }

    /**
     * @param mixed StaEstado
     */
    public function setStaEstado($StaEstado)
    {
        $this->StaEstado = $StaEstado;
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
}
