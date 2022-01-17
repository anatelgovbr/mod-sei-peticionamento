<?
/**
 * ANATEL
 *
 * @dataProvider 21/05/2021
 * @author Lino - Felipe Lino <felipe.silva@gt1tecnologia.com.br>
 *
 */

class MdPetRepresentadoAPIWS
{

    private $CnpjCpf;
    private $RazaoSocial;
    private $DataLimite;
    private $Representante;

    /**
     * @return mixed
     */
    public function getCnpjCpf()
    {
        return $this->CnpjCpf;
    }

    /**
     * @param mixed $CnpjCpf
     */
    public function setCnpjCpf($CnpjCpf)
    {
        $this->CnpjCpf = $CnpjCpf;
    }

    /**
     * @return mixed
     */
    public function getRazaoSocial()
    {
        return $this->RazaoSocial;
    }

    /**
     * @param mixed $RazaoSocial
     */
    public function setRazaoSocial($RazaoSocial)
    {
        $this->RazaoSocial = $RazaoSocial;
    }

    /**
     * @return mixed
     */
    public function getDataLimite()
    {
        return $this->DataLimite;
    }

    /**
     * @param mixed $DataLimite
     */
    public function setDataLimite($DataLimite)
    {
        $this->DataLimite = $DataLimite;
    }


    /**
     * @return mixed
     */
    public function getRepresentante()
    {
        return $this->Representante;
    }

    /**
     * @param mixed $Representante
     */
    public function setRepresentante($Representante)
    {
        $this->Representante = $Representante;
    }



}
