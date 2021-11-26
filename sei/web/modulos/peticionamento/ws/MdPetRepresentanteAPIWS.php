<?
/**
 * ANATEL
 *
 * @dataProvider 19/05/2021
 * @author Lino - Felipe Lino <felipe.silva@gt1tecnologia.com.br>
 *
 */

class MdPetRepresentanteAPIWS
{

    private $Nome;
    private $Cpf;
    private $Email;
    private $StaSituacao;
    private $StaTipoRepresentacao;
    private $TipoPoderesLegais;
    private $ProcessosAbrangencia;
    private $DataLimite;

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
    public function getCpf()
    {
        return $this->Cpf;
    }

    /**
     * @param mixed $Cpf
     */
    public function setCpf($Cpf)
    {
        $this->Cpf = $Cpf;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->Email;
    }

    /**
     * @param mixed $Email
     */
    public function setEmail($Email)
    {
        $this->Email = $Email;
    }

    /**
     * @return mixed
     */
    public function getStaSituacao()
    {
        return $this->StaSituacao;
    }

    /**
     * @param mixed $StaSituacao
     */
    public function setStaSituacao($StaSituacao)
    {
        $this->StaSituacao = $StaSituacao;
    }

    /**
     * @return mixed
     */
    public function getStaTipoRepresentacao()
    {
        return $this->StaTipoRepresentacao;
    }

    /**
     * @param mixed $StaTipoRepresentacao
     */
    public function setStaTipoRepresentacao($StaTipoRepresentacao)
    {
        $this->StaTipoRepresentacao = $StaTipoRepresentacao;
    }

    /**
     * @return mixed
     */
    public function getTipoPoderesLegais()
    {
        return $this->TipoPoderesLegais;
    }

    /**
     * @param mixed $TipoPoderesLegais
     */
    public function setTipoPoderesLegais($TipoPoderesLegais)
    {
        $this->TipoPoderesLegais = $TipoPoderesLegais;
    }

    /**
     * @return mixed
     */
    public function getProcessosAbrangencia()
    {
        return $this->ProcessosAbrangencia;
    }

    /**
     * @param mixed $ProcessosAbrangencia
     */
    public function setProcessosAbrangencia($ProcessosAbrangencia)
    {
        $this->ProcessosAbrangencia = $ProcessosAbrangencia;
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



}
