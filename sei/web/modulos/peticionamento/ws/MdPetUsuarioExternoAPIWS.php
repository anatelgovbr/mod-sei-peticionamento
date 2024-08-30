<?
/**
 * ANATEL
 *
 * @dataProvider 19/05/2021
 * @author Lino - Felipe Lino <felipe.silva@gt1tecnologia.com.br>
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU 
 *
 */

class MdPetUsuarioExternoAPIWS
{

    private $IdUsuario;
    private $Email;
    private $Nome;
    private $Cpf;
    private $SituacaoAtivo;
    private $LiberacaoCadastro;
    private $Rg;
    private $OrgaoExpedidor;
    private $Telefone;
    private $Endereco;
    private $Bairro;
    private $SiglaUf;
    private $NomeCidade;
    private $Cep;
    private $DataCadastro;

    /**
     * @return mixed
     */
    public function getIdUsuario()
    {
        return $this->IdUsuario;
    }

    /**
     * @param mixed IdUsuario
     */
    public function setIdUsuario($IdUsuario)
    {
        $this->IdUsuario = $IdUsuario;
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
    public function getSituacaoAtivo()
    {
        return $this->SituacaoAtivo;
    }

    /**
     * @param mixed $SituacaoAtivo
     */
    public function setSituacaoAtivo($SituacaoAtivo)
    {
        $this->SituacaoAtivo = $SituacaoAtivo;
    }

    /**
     * @return mixed
     */
    public function getLiberacaoCadastro()
    {
        return $this->LiberacaoCadastro;
    }

    /**
     * @param mixed $LiberacaoCadastro
     */
    public function setLiberacaoCadastro($LiberacaoCadastro)
    {
        $this->LiberacaoCadastro = $LiberacaoCadastro;
    }

    /**
     * @return mixed
     */
    public function getRg()
    {
        return $this->Rg;
    }

    /**
     * @param mixed $Rg
     */
    public function setRg($Rg)
    {
        $this->Rg = $Rg;
    }

    /**
     * @return mixed
     */
    public function getOrgaoExpedidor()
    {
        return $this->OrgaoExpedidor;
    }

    /**
     * @param mixed $OrgaoExpedidor
     */
    public function setOrgaoExpedidor($OrgaoExpedidor)
    {
        $this->OrgaoExpedidor = $OrgaoExpedidor;
    }

    /**
     * @return mixed
     */
    public function getTelefone()
    {
        return $this->Telefone;
    }

    /**
     * @param mixed $Telefone
     */
    public function setTelefone($Telefone)
    {
        $this->Telefone = $Telefone;
    }

    /**
     * @return mixed
     */
    public function getEndereco()
    {
        return $this->Endereco;
    }

    /**
     * @param mixed $Endereco
     */
    public function setEndereco($Endereco)
    {
        $this->Endereco = $Endereco;
    }

    /**
     * @return mixed
     */
    public function getBairro()
    {
        return $this->Bairro;
    }

    /**
     * @param mixed $Bairro
     */
    public function setBairro($Bairro)
    {
        $this->Bairro = $Bairro;
    }

    /**
     * @return mixed
     */
    public function getSiglaUf()
    {
        return $this->SiglaUf;
    }

    /**
     * @param mixed $SiglaUf
     */
    public function setSiglaUf($SiglaUf)
    {
        $this->SiglaUf = $SiglaUf;
    }

    /**
     * @return mixed
     */
    public function getNomeCidade()
    {
        return $this->NomeCidade;
    }

    /**
     * @param mixed $NomeCidade
     */
    public function setNomeCidade($NomeCidade)
    {
        $this->NomeCidade = $NomeCidade;
    }

    /**
     * @return mixed
     */
    public function getCep()
    {
        return $this->Cep;
    }

    /**
     * @param mixed $Cep
     */
    public function setCep($Cep)
    {
        $this->Cep = $Cep;
    }

    /**
     * @return mixed
     */
    public function getDataCadastro()
    {
        return $this->DataCadastro;
    }

    /**
     * @param mixed $DataCadastro
     */
    public function setDataCadastro($DataCadastro)
    {
        $this->DataCadastro = $DataCadastro;
    }



}
