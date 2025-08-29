<?
/**
 * ANATEL
 *
 * @dataProvider 19/05/2021
 * @author Lino - Felipe Lino <felipe.silva@gt1tecnologia.com.br>
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU 
 *
 */

class MdPetListarRepresentantesAPIWS
{
	
	private $IdVinculoRepresentante;
    private $TipoVinculo;
    private $NomeRepresentado;
    private $NomeRepresentante;
    private $CpfRepresentado;
    private $CpfRepresentante;
    private $EmailRepresentante;
    private $StaSituacao;
    private $StaTipoRepresentacao;
    private $TipoPoderesLegais;
    private $ProcessosAbrangencia;
    private $DataLimite;
    private $RazaoSocialRepresentado;
    private $CnpjRepresentado;
	
	/**
	 * @return mixed
	 */
	public function getIdVinculoRepresentante()
	{
		return $this->IdVinculoRepresentante;
	}
	
	/**
	 * @param mixed $Nome
	 */
	public function setIdVinculoRepresentante($IdVinculoRepresentante)
	{
		$this->IdVinculoRepresentante = $IdVinculoRepresentante;
	}
	
	/**
     * @return mixed
     */
    public function getTipoVinculo()
    {
        return $this->TipoVinculo;
    }

    /**
     * @param mixed TipoRepresentante
     */
    public function setTipoVinculo($TipoVinculo)
    {
        $this->TipoVinculo = $TipoVinculo;
    }

    /**
     * @return mixed
     */
    public function getNomeRepresentado()
    {
        return $this->NomeRepresentado;
    }

    /**
     * @param mixed $NomeRepresentado
     */
    public function setNomeRepresentado($NomeRepresentado)
    {
        $this->NomeRepresentado = $NomeRepresentado;
    }

    /**
     * @return mixed
     */
    public function getNomeRepresentante()
    {
        return $this->NomeRepresentante;
    }

    /**
     * @param mixed $NomeRepresentante
     */
    public function setNomeRepresentante($NomeRepresentante)
    {
        $this->NomeRepresentante = $NomeRepresentante;
    }

    /**
     * @return mixed
     */
    public function getCpfRepresentado()
    {
        return $this->CpfRepresentado;
    }

    /**
     * @param mixed $CpfRepresentado
     */
    public function setCpfRepresentado($CpfRepresentado)
    {
        $this->CpfRepresentado = $CpfRepresentado;
    }

    /**
     * @return mixed
     */
    public function getCpfRepresentante()
    {
        return $this->CpfRepresentante;
    }

    /**
     * @param mixed $CpfRepresentante
     */
    public function setCpfRepresentante($CpfRepresentante)
    {
        $this->CpfRepresentante = $CpfRepresentante;
    }

    /**
     * @return mixed
     */
    public function getEmailRepresentante()
    {
        return $this->EmailRepresentante;
    }

    /**
     * @param mixed $Email
     */
    public function setEmailRepresentante($Email)
    {
        $this->EmailRepresentante = $Email;
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

    /**
     * @return mixed
     */
    public function getRazaoSocialRepresentado()
    {
        return $this->RazaoSocialRepresentado;
    }

    /**
     * @param mixed $RazaoSocialRepresentado
     */
    public function setRazaoSocialRepresentado($RazaoSocialRepresentado)
    {
        $this->RazaoSocialRepresentado = $RazaoSocialRepresentado;
    }

    /**
     * @return mixed
     */
    public function getCnpjRepresentado()
    {
        return $this->CnpjRepresentado;
    }

    /**
     * @param mixed $CnpjRepresentado
     */
    public function setCnpjRepresentado($CnpjRepresentado)
    {
        $this->CnpjRepresentado = $CnpjRepresentado;
    }

}
