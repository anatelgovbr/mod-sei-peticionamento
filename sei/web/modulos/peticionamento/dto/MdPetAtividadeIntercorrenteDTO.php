<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
*
* 08/02/2012 - criado por bcu
*
* Vers�o do Gerador de C�digo: 1.32.1
*
* Vers�o no CVS: $Id$
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetAtividadeIntercorrenteDTO extends AtividadeDTO {

    public function __construct(){
        parent::__construct();
    }

    public function montar()
    {
        parent::montar();


        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
            'SinAtivoUnidade',
            'u.sin_ativo',
            'unidade u');
    }
  }	


?>