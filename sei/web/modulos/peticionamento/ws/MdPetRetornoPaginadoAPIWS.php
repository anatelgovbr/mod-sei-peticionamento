<?
/**
 * ANATEL
 *
 * @dataProvider 19/05/2021
 * @author Lino - Felipe Lino <felipe.silva@gt1tecnologia.com.br>
 *
 */

class MdPetRetornoPaginadoAPIWS
{

    private $Pagina;
    private $TotalPaginas;
    private $ListaItens;

    /**
     * @return mixed
     */
    public function getPagina()
    {
        return $this->Pagina;
    }

    /**
     * @param mixed IdUsuario
     */
    public function setPagina($Pagina)
    {
        $this->Pagina = $Pagina;
    }

    /**
     * @return mixed
     */
    public function getTotalPaginas()
    {
        return $this->TotalPaginas;
    }

    /**
     * @param mixed TotalPaginas
     */
    public function setTotalPaginas($TotalPaginas)
    {
        $this->TotalPaginas = $TotalPaginas;
    }

    /**
     * @return mixed
     */
    public function getListaItens()
    {
        return $this->ListaItens;
    }

    /**
     * @param mixed $ListaItens
     */
    public function setListaItens($ListaItens)
    {
        $this->ListaItens = $ListaItens;
    }
}
