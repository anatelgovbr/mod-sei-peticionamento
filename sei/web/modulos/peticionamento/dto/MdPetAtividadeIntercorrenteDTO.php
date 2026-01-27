<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 08/02/2012 - criado por bcu
*
* Versão do Gerador de Código: 1.32.1
*
* Versão no CVS: $Id$
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetAtividadeIntercorrenteDTO extends AtividadeDTO {

    public function __construct(){
        parent::__construct();
    }

    public function montar(): void
    {
        parent::montar();


        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
            'SinAtivoUnidade',
            'u.sin_ativo',
            'unidade u');
    }
  }	


?>