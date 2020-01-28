<?
/**
* ANATEL
*
* 28/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetParticipanteRN extends InfraRN { 
	
	public function __construct() {
		
		session_start();
		
		//////////////////////////////////////////////////////////////////////////////
		InfraDebug::getInstance()->setBolLigado(false);
		InfraDebug::getInstance()->setBolDebugInfra(false);
		InfraDebug::getInstance()->limpar();
		//////////////////////////////////////////////////////////////////////////////
		
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	private function getContatoDTOUsuarioLogado(){
		
		$usuarioRN = new UsuarioRN();
		$usuarioDTO = new UsuarioDTO();
		$usuarioDTO->retNumIdUsuario();
		$usuarioDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
		$usuarioDTO->retNumIdContato();
		$usuarioDTO->retStrNomeContato();
		$usuarioDTO = $usuarioRN->consultarRN0489( $usuarioDTO );
		
		$contatoRN = new ContatoRN();
		$contatoDTO = new ContatoDTO();
		$contatoDTO->retTodos();
		$contatoDTO->setNumIdContato( $usuarioDTO->getNumIdContato() );
		$contatoDTO = $contatoRN->consultarRN0324( $contatoDTO );
		
		return $contatoDTO;
	}

	public function setInteressadosRemetentesProcedimentoDocumentoControlado($arrParam){

		if (isset($arrParam[0])) {
			$protocolo = $arrParam[0];
		}else{
			$protocolo = null;
		}
		if (isset($arrParam[1])) {
			$participantes = $arrParam[1];
		}else{
			$participantes = null;	
		}


		$arrObjParticipantesDTO = $participantes;

        // PROTOCOLO - participantes já cadastrados
        $objParticipanteDTO = new ParticipanteDTO();
        $objParticipanteDTO->retNumIdParticipante();
        $objParticipanteDTO->retNumIdContato();
        $objParticipanteDTO->retNumIdUnidade();
        $objParticipanteDTO->retStrStaParticipacao();
        $objParticipanteDTO->retStrNomeContato();
        $objParticipanteDTO->retNumSequencia();
        $objParticipanteDTO->setDblIdProtocolo($protocolo);

        $objParticipanteRN = new ParticipanteRN();
        $arrParticipantesAntigos = $objParticipanteRN->listarRN0189($objParticipanteDTO);

        $arrParticipantesNovos = $arrObjParticipantesDTO;        

        foreach($arrParticipantesNovos as $participanteNovo){
          $flagCadastrar = true;
          $objParticipanteDTOAntigo = null;

          foreach($arrParticipantesAntigos as $participanteAntigo){
            if ($participanteNovo->getNumIdContato()=== $participanteAntigo->getNumIdContato() &&
              $participanteNovo->getStrStaParticipacao()=== $participanteAntigo->getStrStaParticipacao()){
              $objParticipanteDTOAntigo = $participanteAntigo;
              $flagCadastrar = false;
              break;
            }
          }

          if ($flagCadastrar){
              $participanteNovo->retDblIdProtocolo();
              $arrObjParticipanteNovo = $objParticipanteRN->listarRN0189($participanteNovo);
              if(!$arrObjParticipanteNovo) {
                  $objParticipanteRN->cadastrarRN0170($participanteNovo);
              }
          }else if ($participanteNovo->getNumSequencia()!=$objParticipanteDTOAntigo->getNumSequencia()){
            //altera sequencia
            $participanteNovo->setNumIdParticipante($participanteAntigo->getNumIdParticipante());
            //garante que não vai alterar a unidade
            $participanteNovo->unSetNumIdUnidade();
       	    $objParticipanteRN->alterarRN0889($participanteNovo);
          }
        }

	}
}
?>