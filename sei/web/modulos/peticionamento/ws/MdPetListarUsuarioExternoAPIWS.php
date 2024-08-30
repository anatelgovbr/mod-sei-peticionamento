<?
/**
 * ANATEL
 *
 * @dataProvider 19/05/2021
 * @author Lino - Felipe Lino <felipe.silva@gt1tecnologia.com.br>
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU 
 *
 */

class MdPetListarUsuarioExternoAPIWS
{

    private $IdUsuario;
    private $Nome;
    private $Email;
    private $SituacaoAtivo;
    private $LiberacaoCadastro;
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
