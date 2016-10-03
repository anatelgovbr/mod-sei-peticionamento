<?
/**
* ANATEL
*
* 28/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class AcessoExternoPeticionamentoRN extends InfraRN {
	
	public static $TA_INTERESSADO = 'I';
	public static $TA_USUARIO_EXTERNO = 'E';
	public static $TA_DESTINATARIO_ISOLADO = 'D';
	public static $TA_SISTEMA = 'S';
	public static $TA_ASSINATURA_EXTERNA = 'A';

	public function __construct()
	{
		parent::__construct();
	}

	protected function inicializarObjInfraIBanco()
	{
		return BancoSEI::getInstance();
	}

	private function validarNumIdAtividade(AcessoExternoDTO $objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getNumIdAtividade())) {
			$objInfraException->adicionarValidacao('Atividade não informado.');
		}
	}

	private function validarNumIdParticipante(AcessoExternoDTO $objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getNumIdParticipante())) {
			$objInfraException->adicionarValidacao('Interessado não informado.');
		}
	}

	private function validarNumIdUsuarioExterno(AcessoExternoDTO $objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getNumIdUsuarioExterno())) {
			$objInfraException->adicionarValidacao('Usuário Externo não informado.');
		}
	}

	private function validarDblIdProtocoloAtividade(AcessoExternoDTO $objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getDblIdProtocoloAtividade())) {
			$objInfraException->adicionarValidacao('Processo não informado.');
		}
	}

	private function validarNumIdContatoParticipante(AcessoExternoDTO $objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getNumIdContatoParticipante())) {
			$objInfraException->adicionarValidacao('Contato não informado.');
		}
	}


	private function validarDblIdDocumento(AcessoExternoDTO $objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getDblIdDocumento())) {
			$objInfraException->adicionarValidacao('Documento não informado.');
		}
	}

	private function validarNumDias(AcessoExternoDTO $objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getNumDias())) {
			$objInfraException->adicionarValidacao('Validade do acesso não informada..');
		} else {
			if ($objAcessoExternoDTO->getNumDias() <= 0) {
				$objInfraException->adicionarValidacao('Validade do acesso deve ser de pelo menos um dia.');
			}
			/*
			 if ($objAcessoExternoDTO->getNumDias()>60){
			$objInfraException->adicionarValidacao('Validade do acesso não pode ser superior a 60 dias.');
			}
			*/
		}
	}

	private function validarDtaValidade(AcessoExternoDTO $objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getDtaValidade())) {
			$objInfraException->adicionarValidacao('Data de Validade não informada.');
		} else {
			if (!InfraData::validarData($objAcessoExternoDTO->getDtaValidade())) {
				$objInfraException->adicionarValidacao('Data de Validade inválida.');
			}
		}
	}

	private function validarStrEmailUnidade(AcessoExternoDTO $objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getStrEmailUnidade())) {
			$objInfraException->adicionarValidacao('E-mail da Unidade não informado.');
		}
	}

	private function validarStrEmailDestinatario(AcessoExternoDTO $objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getStrEmailDestinatario())) {
			$objInfraException->adicionarValidacao('E-mail do Destinatário não informado.');
		} else {
			$objAcessoExternoDTO->setStrEmailDestinatario(trim($objAcessoExternoDTO->getStrEmailDestinatario()));

			if (strlen($objAcessoExternoDTO->getStrEmailDestinatario()) > 100) {
				$objInfraException->adicionarValidacao('E-mail do Destinatário possui tamanho superior a 100 caracteres.');
			}
		}
	}

	private function validarStrHashInterno(AcessoExternoDTO $objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getStrHashInterno())) {
			$objInfraException->adicionarValidacao('HASH Interno não informado.');
		} else {
			$objAcessoExternoDTO->setStrHashInterno(trim($objAcessoExternoDTO->getStrHashInterno()));

			if (strlen($objAcessoExternoDTO->getStrHashInterno()) > 32) {
				$objInfraException->adicionarValidacao('HASH Interno possui tamanho superior a 32 caracteres.');
			}
		}
	}

	private function validarStrStaTipo(AcessoExternoDTO $objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getStrStaTipo())) {
			$objInfraException->adicionarValidacao('Tipo não informado.');
		} else {
			if (!in_array($objAcessoExternoDTO->getStrStaTipo(), InfraArray::converterArrInfraDTO($this->listarValoresTipoAcessoExterno(), 'StaTipo'))) {
				$objInfraException->adicionarValidacao('Tipo inválido.');
			}
		}
	}

	private function validarStrSenha(AcessoExternoDTO $objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getStrSenha())) {
			$objInfraException->adicionarValidacao('Senha não informada.');
		}
	}

	private function validarStrMotivo(AcessoExternoDTO $objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getStrMotivo())) {
			$objInfraException->adicionarValidacao('Motivo não informado.');
		}
	}

	private function validarStrSinProcesso(AcessoExternoDTO $objAcessoExternoDTO, InfraException $objInfraException)
	{

		if (InfraString::isBolVazia($objAcessoExternoDTO->getStrSinProcesso())) {
			$objInfraException->adicionarValidacao('Sinalizador de acesso ao processo não informado.');
		} else {
			if (!InfraUtil::isBolSinalizadorValido($objAcessoExternoDTO->getStrSinProcesso())) {
				$objInfraException->adicionarValidacao('Sinalizador de acesso ao processo inválido.');
			}
		}
	}

	protected function cadastrarControlado(AcessoExternoDTO $objAcessoExternoDTO)
	{
		try {
			
			//Regras de Negocio
			$objInfraException = new InfraException();

			$this->validarStrStaTipo($objAcessoExternoDTO, $objInfraException);
			
			$objInfraException->lancarValidacoes();

			if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_INTERESSADO ||
			$objAcessoExternoDTO->getStrStaTipo() == self::$TA_USUARIO_EXTERNO ||
			$objAcessoExternoDTO->getStrStaTipo() == self::$TA_DESTINATARIO_ISOLADO
			) {

				$this->validarStrEmailUnidade($objAcessoExternoDTO, $objInfraException);

				if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_INTERESSADO) {
					$this->validarNumIdParticipante($objAcessoExternoDTO, $objInfraException);
				} else if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_USUARIO_EXTERNO) {
					$this->validarNumIdUsuarioExterno($objAcessoExternoDTO, $objInfraException);
					$this->validarDblIdProtocoloAtividade($objAcessoExternoDTO, $objInfraException);
				} else {

					if (InfraString::isBolVazia($objAcessoExternoDTO->getNumIdContatoParticipante())) {
						if (InfraString::isBolVazia($objAcessoExternoDTO->getStrNomeContato())) {
							$objInfraException->adicionarValidacao('Destinatário não informado.');
						} else {
							$objContatoDTO = new ContatoDTO();
							$objContatoDTO->setStrNome($objAcessoExternoDTO->getStrNomeContato());
							$objContatoRN = new ContatoRN();
							$objContatoDTO = $objContatoRN->cadastrarContextoTemporario($objContatoDTO);
							$objAcessoExternoDTO->setNumIdContatoParticipante($objContatoDTO->getNumIdContato());
						}
					}
				}

				$this->validarStrEmailDestinatario($objAcessoExternoDTO, $objInfraException);
				//$this->validarDtaValidade($objAcessoExternoDTO, $objInfraException);
				$this->validarStrSenha($objAcessoExternoDTO, $objInfraException);
				$this->validarStrMotivo($objAcessoExternoDTO, $objInfraException);
				$this->validarNumDias($objAcessoExternoDTO, $objInfraException);

				$objInfraException->lancarValidacoes();

				$objAcessoExternoDTO->setDblIdDocumento(null);
				$objAcessoExternoDTO->setStrSinProcesso('S');

				$objInfraParametro = new InfraParametro(BancoSEI::getInstance());

				$objInfraSip = new InfraSip(SessaoSEI::getInstance());
				$objInfraSip->autenticar(SessaoSEI::getInstance()->getNumIdOrgaoUsuario(),
						SessaoSEI::getInstance()->getNumIdContextoUsuario(),
						SessaoSEI::getInstance()->getStrSiglaUsuario(),
						$objAcessoExternoDTO->getStrSenha());

				$objAcessoExternoDTO->setDtaValidade(InfraData::calcularData($objAcessoExternoDTO->getNumDias(), InfraData::$UNIDADE_DIAS, InfraData::$SENTIDO_ADIANTE));

				$objParticipanteRN = new ParticipanteRN();

				if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_USUARIO_EXTERNO) {

					$objUsuarioDTO = new UsuarioDTO();
					$objUsuarioDTO->retNumIdUsuario();
					$objUsuarioDTO->retNumIdContato();
					$objUsuarioDTO->retStrSigla();
					$objUsuarioDTO->retStrNome();
					$objUsuarioDTO->setNumIdUsuario($objAcessoExternoDTO->getNumIdUsuarioExterno());
					$objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_EXTERNO);

					$objUsuarioRN = new UsuarioRN();
					$objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);


					$objParticipanteDTO = new ParticipanteDTO();
					$objParticipanteDTO->retNumIdParticipante();
					$objParticipanteDTO->setDblIdProtocolo($objAcessoExternoDTO->getDblIdProtocoloAtividade());
					$objParticipanteDTO->setNumIdContato($objUsuarioDTO->getNumIdContato());
					$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);

					$objParticipanteDTO = $objParticipanteRN->consultarRN1008($objParticipanteDTO);

					if ($objParticipanteDTO == null) {
						$objParticipanteDTO = new ParticipanteDTO();
						$objParticipanteDTO->setDblIdProtocolo($objAcessoExternoDTO->getDblIdProtocoloAtividade());
						$objParticipanteDTO->setNumIdContato($objUsuarioDTO->getNumIdContato());
						$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);
						$objParticipanteDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
						$objParticipanteDTO->setNumSequencia(0);
						$objParticipanteDTO = $objParticipanteRN->cadastrarRN0170($objParticipanteDTO);
					}

					$objAcessoExternoDTO->setNumIdParticipante($objParticipanteDTO->getNumIdParticipante());
					$objAcessoExternoDTO->setStrEmailDestinatario($objUsuarioDTO->getStrSigla());

				} else if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_DESTINATARIO_ISOLADO) {

					$objContatoDTO = new ContatoDTO();
					$objContatoDTO->retNumIdContato();
					$objContatoDTO->retStrNome();
					$objContatoDTO->setNumIdContato($objAcessoExternoDTO->getNumIdContatoParticipante());

					$objContatoRN = new ContatoRN();
					$objContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);

					$objParticipanteDTO = new ParticipanteDTO();
					$objParticipanteDTO->retNumIdParticipante();
					$objParticipanteDTO->setDblIdProtocolo($objAcessoExternoDTO->getDblIdProtocoloAtividade());
					$objParticipanteDTO->setNumIdContato($objContatoDTO->getNumIdContato());
					$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);

					$objParticipanteDTO = $objParticipanteRN->consultarRN1008($objParticipanteDTO);

					if ($objParticipanteDTO == null) {
						$objParticipanteDTO = new ParticipanteDTO();
						$objParticipanteDTO->setDblIdProtocolo($objAcessoExternoDTO->getDblIdProtocoloAtividade());
						$objParticipanteDTO->setNumIdContato($objContatoDTO->getNumIdContato());
						$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);
						$objParticipanteDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
						$objParticipanteDTO->setNumSequencia(0);
						$objParticipanteDTO = $objParticipanteRN->cadastrarRN0170($objParticipanteDTO);
					}

					$objAcessoExternoDTO->setNumIdParticipante($objParticipanteDTO->getNumIdParticipante());
				}

				$objParticipanteDTO = new ParticipanteDTO();
				$objParticipanteDTO->retNumIdParticipante();
				$objParticipanteDTO->retDblIdProtocolo();
				$objParticipanteDTO->retStrNomeContato();
				$objParticipanteDTO->setNumIdParticipante($objAcessoExternoDTO->getNumIdParticipante());
				$objParticipanteRN = new ParticipanteRN();
				$objParticipanteDTO = $objParticipanteRN->consultarRN1008($objParticipanteDTO);

				$arrObjAtributoAndamentoDTO = array();
				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('DESTINATARIO_NOME');
				$objAtributoAndamentoDTO->setStrValor($objParticipanteDTO->getStrNomeContato());
				$objAtributoAndamentoDTO->setStrIdOrigem($objParticipanteDTO->getNumIdParticipante());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('DESTINATARIO_EMAIL');
				$objAtributoAndamentoDTO->setStrValor($objAcessoExternoDTO->getStrEmailDestinatario());
				$objAtributoAndamentoDTO->setStrIdOrigem($objParticipanteDTO->getNumIdParticipante());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('MOTIVO');
				$objAtributoAndamentoDTO->setStrValor($objAcessoExternoDTO->getStrMotivo());
				$objAtributoAndamentoDTO->setStrIdOrigem($objAcessoExternoDTO->getNumIdParticipante());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('DATA_VALIDADE');
				$objAtributoAndamentoDTO->setStrValor($objAcessoExternoDTO->getDtaValidade());
				$objAtributoAndamentoDTO->setStrIdOrigem(null);
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('DIAS_VALIDADE');
				$objAtributoAndamentoDTO->setStrValor($objAcessoExternoDTO->getNumDias() . ' ' . ($objAcessoExternoDTO->getNumDias() == 1 ? 'dia' : 'dias'));
				$objAtributoAndamentoDTO->setStrIdOrigem(null);
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

				$objAtividadeDTO = new AtividadeDTO();
				$objAtividadeDTO->setDblIdProtocolo($objParticipanteDTO->getDblIdProtocolo());
				$objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

				$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_LIBERACAO_ACESSO_EXTERNO);

				$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);

				$objAtividadeRN = new AtividadeRN();
				$objAtividadeDTO = $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);

				$objAcessoExternoDTO->setNumIdAtividade($objAtividadeDTO->getNumIdAtividade());


				$objAcessoExternoDTO->setStrHashInterno(md5(time()));


			} else if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_ASSINATURA_EXTERNA) {

				$this->validarStrEmailUnidade($objAcessoExternoDTO, $objInfraException);
				$this->validarDblIdDocumento($objAcessoExternoDTO, $objInfraException);
				//$this->validarStrEmailDestinatario($objAcessoExternoDTO, $objInfraException);
				//$this->validarDtaValidade($objAcessoExternoDTO, $objInfraException);
				//$this->validarStrSenha($objAcessoExternoDTO, $objInfraException);
				//$this->validarStrMotivo($objAcessoExternoDTO, $objInfraException);
				//$this->validarNumDias($objAcessoExternoDTO, $objInfraException);
				$this->validarStrSinProcesso($objAcessoExternoDTO, $objInfraException);

				$objInfraException->lancarValidacoes();

				$objAcessoExternoDTO->setDtaValidade(null);
				$objAcessoExternoDTO->setStrMotivo(null);

				//busca processo
				$objDocumentoDTO = new DocumentoDTO();
				$objDocumentoDTO->retDblIdDocumento();
				$objDocumentoDTO->retDblIdProcedimento();
				$objDocumentoDTO->retStrProtocoloProcedimentoFormatado();
				$objDocumentoDTO->retStrProtocoloDocumentoFormatado();
				$objDocumentoDTO->retStrNomeSerie();
				$objDocumentoDTO->setDblIdDocumento($objAcessoExternoDTO->getDblIdDocumento());

				$objDocumentoRN = new DocumentoRN();
				$objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

				//busca contato
				$objUsuarioDTO = new UsuarioDTO();
				$objUsuarioDTO->retNumIdUsuario();
				$objUsuarioDTO->retStrSigla();
				$objUsuarioDTO->retStrNome();
				$objUsuarioDTO->retStrStaTipo();
				$objUsuarioDTO->retNumIdContato();
				$objUsuarioDTO->setNumIdUsuario($objAcessoExternoDTO->getNumIdUsuarioExterno());

				$objUsuarioRN = new UsuarioRN();
				$objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

				if ($objUsuarioDTO->getStrStaTipo() == UsuarioRN::$TU_EXTERNO_PENDENTE) {
					$objInfraException->lancarValidacao('Usuário externo "' . $objUsuarioDTO->getStrSigla() . '" ainda não foi liberado.');
				}

				if ($objUsuarioDTO->getStrStaTipo() != UsuarioRN::$TU_EXTERNO) {
					$objInfraException->lancarValidacao('Usuário "' . $objUsuarioDTO->getStrSigla() . '" não é um usuário externo.');
				}

				//verifica se o contato já é participante do processo
				$objParticipanteDTO = new ParticipanteDTO();
				
				$objParticipanteDTO->retNumIdParticipante();
				$objParticipanteDTO->retDblIdProtocolo();
				$objParticipanteDTO->retNumIdContato();
				$objParticipanteDTO->retStrStaParticipacao();
				
				$objParticipanteDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdProcedimento());
				$objParticipanteDTO->setNumIdContato($objUsuarioDTO->getNumIdContato());
				$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);

				$objParticipanteRN = new ParticipanteRN();
				$objParticipanteDTO = $objParticipanteRN->consultarRN1008($objParticipanteDTO);

				if ($objParticipanteDTO == null) {

					$objParticipanteDTO = new ParticipanteDTO();
					$objParticipanteDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdProcedimento());
					$objParticipanteDTO->setNumIdContato($objUsuarioDTO->getNumIdContato());
					$objParticipanteDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
					$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);
					$objParticipanteDTO->setNumSequencia(0);

					$objParticipanteDTO = $objParticipanteRN->cadastrarRN0170($objParticipanteDTO);
				} else {
					$dto = new AcessoExternoDTO();
					$dto->retStrSiglaContato();
					$dto->retDthAberturaAtividade();
					$dto->setDblIdDocumento($objDocumentoDTO->getDblIdDocumento());
					$dto->setNumIdParticipante($objParticipanteDTO->getNumIdParticipante());
					$dto->setStrStaTipo(AcessoExternoRN::$TA_ASSINATURA_EXTERNA);

					$dto = $this->consultar($dto);

					if ($dto != null) {
						$objInfraException->lancarValidacao('Usuário externo ' . $dto->getStrSiglaContato() . ' já recebeu liberação para assinatura externa no documento ' . $objDocumentoDTO->getStrProtocoloDocumentoFormatado() . ' em ' . substr($dto->getDthAberturaAtividade(), 0, 16) . '.');
					}
				}

				$objAcessoExternoDTO->setNumIdParticipante($objParticipanteDTO->getNumIdParticipante());

				$arrObjAtributoAndamentoDTO = array();
				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('USUARIO_EXTERNO_SIGLA');
				$objAtributoAndamentoDTO->setStrValor($objUsuarioDTO->getStrSigla());
				$objAtributoAndamentoDTO->setStrIdOrigem($objUsuarioDTO->getNumIdUsuario());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('USUARIO_EXTERNO_NOME');
				$objAtributoAndamentoDTO->setStrValor($objUsuarioDTO->getStrNome());
				$objAtributoAndamentoDTO->setStrIdOrigem($objUsuarioDTO->getNumIdUsuario());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('DOCUMENTO');
				$objAtributoAndamentoDTO->setStrValor($objDocumentoDTO->getStrProtocoloDocumentoFormatado());
				$objAtributoAndamentoDTO->setStrIdOrigem($objDocumentoDTO->getDblIdDocumento());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

				//a pedido do cliente removendo do historico / andamento a atividade de "liberar para assinatura"
				//$objAtividadeDTO = new AtividadeDTO();
				//$objAtividadeDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdProcedimento());
				//$objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
				//$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_LIBERACAO_ASSINATURA_EXTERNA);
				//$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
				
				$objAtividadeDTO = new AtividadeDTO();
				$objAtividadeDTO->setDblIdProtocolo($objParticipanteDTO->getDblIdProtocolo());
				$objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
				$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_ACESSO_EXTERNO_SISTEMA);
				$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
				
				$objAtividadeRN = new AtividadeRN();
				$objAtividadeDTO = $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
								
				$objAtividadeRN = new AtividadeRN();
				//a pedido do cliente removendo do historico / andamento a atividade de "liberar para assinatura"
				$objAtividadeDTO = $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
				$objAcessoExternoDTO->setNumIdAtividade($objAtividadeDTO->getNumIdAtividade());

				$objAcessoExternoDTO->setStrHashInterno(md5(time()));

			} else if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_SISTEMA) {

				$this->validarNumIdParticipante($objAcessoExternoDTO, $objInfraException);

				$objInfraException->lancarValidacoes();

				$objAcessoExternoDTO->setDblIdDocumento(null);
				$objAcessoExternoDTO->setStrSinProcesso('S');


				$objAcessoExternoDTO->setStrEmailUnidade(null);
				$objAcessoExternoDTO->setStrEmailDestinatario(null);
				$objAcessoExternoDTO->setDtaValidade(null);

				$objParticipanteDTO = new ParticipanteDTO();
				$objParticipanteDTO->retStrSiglaContato();
				$objParticipanteDTO->retStrNomeContato();
				$objParticipanteDTO->retDblIdProtocolo();
				$objParticipanteDTO->setNumIdParticipante($objAcessoExternoDTO->getNumIdParticipante());

				$objParticipanteRN = new ParticipanteRN();
				$objParticipanteDTO = $objParticipanteRN->consultarRN1008($objParticipanteDTO);

				$arrObjAtributoAndamentoDTO = array();
				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('INTERESSADO');
				$objAtributoAndamentoDTO->setStrValor($objParticipanteDTO->getStrSiglaContato() . '¥' . $objParticipanteDTO->getStrNomeContato());
				$objAtributoAndamentoDTO->setStrIdOrigem($objAcessoExternoDTO->getNumIdParticipante());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
				
				$objAtividadeDTO = new AtividadeDTO();
				$objAtividadeDTO->setDblIdProtocolo($objParticipanteDTO->getDblIdProtocolo());
				$objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
				$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_ACESSO_EXTERNO_SISTEMA);
				$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);

				$objAtividadeRN = new AtividadeRN();
				$objAtividadeDTO = $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
								
				$objAcessoExternoDTO->setNumIdAtividade($objAtividadeDTO->getNumIdAtividade());

			}

			//gera da mesma forma independente do tipo
			$objAcessoExternoDTO->setStrHashInterno(md5(time()));
			$objAcessoExternoDTO->setStrSinAtivo('S');

			$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
			$ret = $objAcessoExternoBD->cadastrar($objAcessoExternoDTO);

			//ENVIAR EMAIL
			if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_INTERESSADO || $objAcessoExternoDTO->getStrStaTipo() == self::$TA_DESTINATARIO_ISOLADO) {

				$objEmailSistemaDTO = new EmailSistemaDTO();
				$objEmailSistemaDTO->retStrDe();
				$objEmailSistemaDTO->retStrPara();
				$objEmailSistemaDTO->retStrAssunto();
				$objEmailSistemaDTO->retStrConteudo();
				$objEmailSistemaDTO->setNumIdEmailSistema(EmailSistemaRN::$ES_DISPONIBILIZACAO_ACESSO_EXTERNO);

				$objEmailSistemaRN = new EmailSistemaRN();
				$objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

				if ($objEmailSistemaDTO!=null){

					$objProtocoloDTO = new ProtocoloDTO();
					$objProtocoloDTO->retStrProtocoloFormatado();
					$objProtocoloDTO->setDblIdProtocolo($objParticipanteDTO->getDblIdProtocolo());

					$objProtocoloRN = new ProtocoloRN();
					$objProtocoloDTO = $objProtocoloRN->consultarRN0186($objProtocoloDTO);

					$objUnidadeDTO = new UnidadeDTO();
					$objUnidadeDTO->retStrSigla();
					$objUnidadeDTO->retStrDescricao();
					$objUnidadeDTO->retStrSiglaOrgao();
					$objUnidadeDTO->retStrDescricaoOrgao();
					$objUnidadeDTO->retStrSitioInternetOrgao();
					$objUnidadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

					$objUnidadeRN = new UnidadeRN();
					$objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);

					$strDe = $objEmailSistemaDTO->getStrDe();
					$strDe = str_replace('@email_unidade@', $objAcessoExternoDTO->getStrEmailUnidade(), $strDe);

					$strPara = $objEmailSistemaDTO->getStrPara();
					$strPara = str_replace('@email_destinatario@', $objAcessoExternoDTO->getStrEmailDestinatario(), $strPara);

					$strAssunto = $objEmailSistemaDTO->getStrAssunto();
					$strAssunto = str_replace('@processo@', $objProtocoloDTO->getStrProtocoloFormatado(), $strAssunto);

					$strConteudo = $objEmailSistemaDTO->getStrConteudo();
					$strConteudo = str_replace('@processo@', $objProtocoloDTO->getStrProtocoloFormatado(), $strConteudo);
					$strConteudo = str_replace('@nome_destinatario@', $objParticipanteDTO->getStrNomeContato(), $strConteudo);
					$strConteudo = str_replace('@data_validade@', $objAcessoExternoDTO->getDtaValidade(), $strConteudo);
					$strConteudo = str_replace('@link_acesso_externo@', SessaoSEIExterna::getInstance($ret->getNumIdAcessoExterno())->assinarLink(ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/processo_acesso_externo_consulta.php?id_acesso_externo=' . $ret->getNumIdAcessoExterno()), $strConteudo);
					$strConteudo = str_replace('@sigla_unidade@', $objUnidadeDTO->getStrSigla(), $strConteudo);
					$strConteudo = str_replace('@descricao_unidade@', $objUnidadeDTO->getStrDescricao(), $strConteudo);
					$strConteudo = str_replace('@sigla_orgao@', $objUnidadeDTO->getStrSiglaOrgao(), $strConteudo);
					$strConteudo = str_replace('@descricao_orgao@', $objUnidadeDTO->getStrDescricaoOrgao(), $strConteudo);
					$strConteudo = str_replace('@sitio_internet_orgao@', $objUnidadeDTO->getStrSitioInternetOrgao(), $strConteudo);

					//InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strConteudo);
				}
			} else if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_USUARIO_EXTERNO) {

				$objEmailSistemaDTO = new EmailSistemaDTO();
				$objEmailSistemaDTO->retStrDe();
				$objEmailSistemaDTO->retStrPara();
				$objEmailSistemaDTO->retStrAssunto();
				$objEmailSistemaDTO->retStrConteudo();
				$objEmailSistemaDTO->setNumIdEmailSistema(EmailSistemaRN::$ES_DISPONIBILIZACAO_ACESSO_EXTERNO_USUARIO_EXTERNO);

				$objEmailSistemaRN = new EmailSistemaRN();
				$objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

				if ($objEmailSistemaDTO!=null){
					$objProtocoloDTO = new ProtocoloDTO();
					$objProtocoloDTO->retStrProtocoloFormatado();
					$objProtocoloDTO->setDblIdProtocolo($objParticipanteDTO->getDblIdProtocolo());

					$objProtocoloRN = new ProtocoloRN();
					$objProtocoloDTO = $objProtocoloRN->consultarRN0186($objProtocoloDTO);

					$objUnidadeDTO = new UnidadeDTO();
					$objUnidadeDTO->retNumIdOrgao();
					$objUnidadeDTO->retStrSigla();
					$objUnidadeDTO->retStrDescricao();
					$objUnidadeDTO->retStrSiglaOrgao();
					$objUnidadeDTO->retStrDescricaoOrgao();
					$objUnidadeDTO->retStrSitioInternetOrgao();
					$objUnidadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

					$objUnidadeRN = new UnidadeRN();
					$objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);

					$strDe = $objEmailSistemaDTO->getStrDe();
					$strDe = str_replace('@email_unidade@', $objAcessoExternoDTO->getStrEmailUnidade(), $strDe);

					$strPara = $objEmailSistemaDTO->getStrPara();
					$strPara = str_replace('@email_usuario_externo@', $objUsuarioDTO->getStrSigla(), $strPara);

					$strAssunto = $objEmailSistemaDTO->getStrAssunto();
					$strAssunto = str_replace('@processo@', $objProtocoloDTO->getStrProtocoloFormatado(), $strAssunto);

					$strConteudo = $objEmailSistemaDTO->getStrConteudo();
					$strConteudo = str_replace('@processo@', $objProtocoloDTO->getStrProtocoloFormatado(), $strConteudo);
					$strConteudo = str_replace('@nome_usuario_externo@', $objUsuarioDTO->getStrNome(), $strConteudo);
					$strConteudo = str_replace('@email_usuario_externo@', $objUsuarioDTO->getStrSigla(), $strConteudo);
					$strConteudo = str_replace('@link_login_usuario_externo@', ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador_externo.php?acao=usuario_externo_logar&id_orgao_acesso_externo=' . $objUnidadeDTO->getNumIdOrgao(), $strConteudo);

					$strConteudo = str_replace('@sigla_unidade@', $objUnidadeDTO->getStrSigla(), $strConteudo);
					$strConteudo = str_replace('@descricao_unidade@', $objUnidadeDTO->getStrDescricao(), $strConteudo);
					$strConteudo = str_replace('@sigla_orgao@', $objUnidadeDTO->getStrSiglaOrgao(), $strConteudo);
					$strConteudo = str_replace('@descricao_orgao@', $objUnidadeDTO->getStrDescricaoOrgao(), $strConteudo);
					$strConteudo = str_replace('@sitio_internet_orgao@', $objUnidadeDTO->getStrSitioInternetOrgao(), $strConteudo);

					//InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strConteudo);
				}
			} else if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_ASSINATURA_EXTERNA) {

				$objEmailSistemaDTO = new EmailSistemaDTO();
				$objEmailSistemaDTO->retStrDe();
				$objEmailSistemaDTO->retStrPara();
				$objEmailSistemaDTO->retStrAssunto();
				$objEmailSistemaDTO->retStrConteudo();
				$objEmailSistemaDTO->setNumIdEmailSistema(EmailSistemaRN::$ES_DISPONIBILIZACAO_ASSINATURA_EXTERNA_USUARIO_EXTERNO);

				$objEmailSistemaRN = new EmailSistemaRN();
				$objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

				if ($objEmailSistemaDTO!=null){

					$objUnidadeDTO = new UnidadeDTO();
					$objUnidadeDTO->retNumIdOrgao();
					$objUnidadeDTO->retStrSigla();
					$objUnidadeDTO->retStrDescricao();
					$objUnidadeDTO->retStrSiglaOrgao();
					$objUnidadeDTO->retStrDescricaoOrgao();
					$objUnidadeDTO->retStrSitioInternetOrgao();
					$objUnidadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

					$objUnidadeRN = new UnidadeRN();
					$objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);

					$strDe = $objEmailSistemaDTO->getStrDe();
					$strDe = str_replace('@email_unidade@', $objAcessoExternoDTO->getStrEmailUnidade(), $strDe);

					$strPara = $objEmailSistemaDTO->getStrPara();
					$strPara = str_replace('@email_usuario_externo@', $objUsuarioDTO->getStrSigla(), $strPara);

					$strAssunto = $objEmailSistemaDTO->getStrAssunto();
					$strAssunto = str_replace('@processo@', $objDocumentoDTO->getStrProtocoloProcedimentoFormatado(), $strAssunto);

					$strConteudo = $objEmailSistemaDTO->getStrConteudo();
					$strConteudo = str_replace('@processo@', $objDocumentoDTO->getStrProtocoloProcedimentoFormatado(), $strConteudo);
					$strConteudo = str_replace('@documento@', $objDocumentoDTO->getStrProtocoloDocumentoFormatado(), $strConteudo);
					$strConteudo = str_replace('@tipo_documento@', $objDocumentoDTO->getStrNomeSerie(), $strConteudo);
					$strConteudo = str_replace('@nome_usuario_externo@', $objUsuarioDTO->getStrNome(), $strConteudo);
					$strConteudo = str_replace('@email_usuario_externo@', $objUsuarioDTO->getStrSigla(), $strConteudo);
					$strConteudo = str_replace('@link_login_usuario_externo@', ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador_externo.php?acao=usuario_externo_logar&id_orgao_acesso_externo=' . $objUnidadeDTO->getNumIdOrgao(), $strConteudo);
					$strConteudo = str_replace('@sigla_unidade@', $objUnidadeDTO->getStrSigla(), $strConteudo);
					$strConteudo = str_replace('@descricao_unidade@', $objUnidadeDTO->getStrDescricao(), $strConteudo);
					$strConteudo = str_replace('@sigla_orgao@', $objUnidadeDTO->getStrSiglaOrgao(), $strConteudo);
					$strConteudo = str_replace('@descricao_orgao@', $objUnidadeDTO->getStrDescricaoOrgao(), $strConteudo);
					$strConteudo = str_replace('@sitio_internet_orgao@', $objUnidadeDTO->getStrSitioInternetOrgao(), $strConteudo);

					//InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strConteudo);
				}
			}

			return $ret;

			//Auditoria

		} catch (Exception $e) {
			throw new InfraException('Erro cadastrando Acesso Externo.', $e);
		}
	}

	protected function listarDocumentosControleAcessoConectado(AcessoExternoDTO $parObjAcessoExternoDTO)
	{
		try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_listar', __METHOD__, $parObjAcessoExternoDTO);

			//Regras de Negocio
			$objInfraException = new InfraException();

			$objUsuarioDTO = new UsuarioDTO();
			$objUsuarioDTO->retNumIdUsuario();
			$objUsuarioDTO->retStrSigla();
			$objUsuarioDTO->retStrNome();
			$objUsuarioDTO->retStrStaTipo();
			$objUsuarioDTO->retNumIdContato();
			$objUsuarioDTO->setNumIdUsuario($parObjAcessoExternoDTO->getNumIdUsuarioExterno());

			$objUsuarioRN = new UsuarioRN();
			$objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

			if ($objUsuarioDTO->getStrStaTipo() == UsuarioRN::$TU_EXTERNO_PENDENTE) {
				$objInfraException->lancarValidacao('Usuário externo "' . $objUsuarioDTO->getStrSigla() . '" ainda não foi liberado.');
			}

			if ($objUsuarioDTO->getStrStaTipo() != UsuarioRN::$TU_EXTERNO) {
				$objInfraException->lancarValidacao('Usuário "' . $objUsuarioDTO->getStrSigla() . '" não é um usuário externo.');
			}

			$objAcessoExternoDTO = new AcessoExternoDTO();
			$objAcessoExternoDTO->retNumIdAcessoExterno();
			$objAcessoExternoDTO->retDblIdProtocoloAtividade();
			$objAcessoExternoDTO->retDblIdDocumento();
			$objAcessoExternoDTO->retStrSinProcesso();
			$objAcessoExternoDTO->retDthAberturaAtividade();
			$objAcessoExternoDTO->retDtaValidade();
			//$objAcessoExternoDTO->retStrSiglaUnidade();
			//$objAcessoExternoDTO->retStrDescricaoUnidade();
			$objAcessoExternoDTO->setStrStaTipo(array(AcessoExternoRN::$TA_ASSINATURA_EXTERNA, AcessoExternoRN::$TA_USUARIO_EXTERNO), InfraDTO::$OPER_IN);
			$objAcessoExternoDTO->setNumIdContatoParticipante($objUsuarioDTO->getNumIdContato());
			$objAcessoExternoDTO->setOrdDthAberturaAtividade(InfraDTO::$TIPO_ORDENACAO_DESC);

			//paginação
			$objAcessoExternoDTO->setNumMaxRegistrosRetorno($parObjAcessoExternoDTO->getNumMaxRegistrosRetorno());
			$objAcessoExternoDTO->setNumPaginaAtual($parObjAcessoExternoDTO->getNumPaginaAtual());

			$arrObjAcessoExternoDTO = $this->listar($objAcessoExternoDTO);

			//paginação
			$parObjAcessoExternoDTO->setNumTotalRegistros($objAcessoExternoDTO->getNumTotalRegistros());
			$parObjAcessoExternoDTO->setNumRegistrosPaginaAtual($objAcessoExternoDTO->getNumRegistrosPaginaAtual());

			if (count($arrObjAcessoExternoDTO)) {

				//Carregar dados do cabeçalho
				$objProcedimentoDTO = new ProcedimentoDTO();
				$objProcedimentoDTO->retStrNomeTipoProcedimento();
				$objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();

				$objProcedimentoDTO->setDblIdProcedimento(InfraArray::converterArrInfraDTO($arrObjAcessoExternoDTO, 'IdProtocoloAtividade'), InfraDTO::$OPER_IN);
				$objProcedimentoDTO->setStrSinDocTodos('S');
				$objProcedimentoDTO->setArrDblIdProtocoloAssociado(InfraArray::converterArrInfraDTO($arrObjAcessoExternoDTO, 'IdDocumento'));

				$objProcedimentoRN = new ProcedimentoRN();
				$arrObjProcedimentoDTO = $objProcedimentoRN->listarCompleto($objProcedimentoDTO);

				foreach ($arrObjAcessoExternoDTO as $objAcessoExternoDTO) {
					foreach ($arrObjProcedimentoDTO as $objProcedimentoDTO) {
						if ($objAcessoExternoDTO->getDblIdProtocoloAtividade() == $objProcedimentoDTO->getDblIdProcedimento()) {

							$objAcessoExternoDTO->setObjProcedimentoDTO($objProcedimentoDTO);

							$arrObjDocumentoDTO = $objProcedimentoDTO->getArrObjDocumentoDTO();
							foreach ($arrObjDocumentoDTO as $objDocumentoDTO) {
								if ($objAcessoExternoDTO->getDblIdDocumento() == $objDocumentoDTO->getDblIdDocumento()) {
									$objAcessoExternoDTO->setObjDocumentoDTO($objDocumentoDTO);
								}
							}
							break;
						}
					}
				}
			}

			//Auditoria

			return $arrObjAcessoExternoDTO;

		} catch (Exception $e) {
			throw new InfraException('Erro listando documentos para assinatura externa.', $e);
		}
	}

	protected function listarDocumentosAssinaturaExternaConectado(AcessoExternoDTO $parObjAcessoExternoDTO)
	{
		try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_listar', __METHOD__, $parObjAcessoExternoDTO);

			//Regras de Negocio
			$objInfraException = new InfraException();

			$objUsuarioDTO = new UsuarioDTO();
			$objUsuarioDTO->retNumIdUsuario();
			$objUsuarioDTO->retStrSigla();
			$objUsuarioDTO->retStrNome();
			$objUsuarioDTO->retStrStaTipo();
			$objUsuarioDTO->retNumIdContato();
			$objUsuarioDTO->setNumIdUsuario($parObjAcessoExternoDTO->getNumIdUsuarioExterno());

			$objUsuarioRN = new UsuarioRN();
			$objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

			if ($objUsuarioDTO->getStrStaTipo() == UsuarioRN::$TU_EXTERNO_PENDENTE) {
				$objInfraException->lancarValidacao('Usuário externo "' . $objUsuarioDTO->getStrSigla() . '" ainda não foi liberado.');
			}

			if ($objUsuarioDTO->getStrStaTipo() != UsuarioRN::$TU_EXTERNO) {
				$objInfraException->lancarValidacao('Usuário "' . $objUsuarioDTO->getStrSigla() . '" não é um usuário externo.');
			}

			$objAcessoExternoDTO = new AcessoExternoDTO();
			$objAcessoExternoDTO->retNumIdAcessoExterno();
			$objAcessoExternoDTO->retDblIdProtocoloAtividade();
			$objAcessoExternoDTO->retDblIdDocumento();
			$objAcessoExternoDTO->retStrSinProcesso();
			$objAcessoExternoDTO->retDthAberturaAtividade();
			$objAcessoExternoDTO->retDtaValidade();
			//$objAcessoExternoDTO->retStrSiglaUnidade();
			//$objAcessoExternoDTO->retStrDescricaoUnidade();
			$objAcessoExternoDTO->setStrStaTipo(array(AcessoExternoRN::$TA_ASSINATURA_EXTERNA, AcessoExternoRN::$TA_USUARIO_EXTERNO), InfraDTO::$OPER_IN);
			$objAcessoExternoDTO->setNumIdContatoParticipante($objUsuarioDTO->getNumIdContato());
			$objAcessoExternoDTO->setOrdDthAberturaAtividade(InfraDTO::$TIPO_ORDENACAO_DESC);

			//paginação
			$objAcessoExternoDTO->setNumMaxRegistrosRetorno($parObjAcessoExternoDTO->getNumMaxRegistrosRetorno());
			$objAcessoExternoDTO->setNumPaginaAtual($parObjAcessoExternoDTO->getNumPaginaAtual());

			$arrObjAcessoExternoDTO = $this->listar($objAcessoExternoDTO);

			//paginação
			$parObjAcessoExternoDTO->setNumTotalRegistros($objAcessoExternoDTO->getNumTotalRegistros());
			$parObjAcessoExternoDTO->setNumRegistrosPaginaAtual($objAcessoExternoDTO->getNumRegistrosPaginaAtual());

			if (count($arrObjAcessoExternoDTO)) {

				//Carregar dados do cabeçalho
				$objProcedimentoDTO = new ProcedimentoDTO();
				$objProcedimentoDTO->retStrNomeTipoProcedimento();
				$objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();

				$objProcedimentoDTO->setDblIdProcedimento(InfraArray::converterArrInfraDTO($arrObjAcessoExternoDTO, 'IdProtocoloAtividade'), InfraDTO::$OPER_IN);
				$objProcedimentoDTO->setStrSinDocTodos('S');
				$objProcedimentoDTO->setArrDblIdProtocoloAssociado(InfraArray::converterArrInfraDTO($arrObjAcessoExternoDTO, 'IdDocumento'));

				$objProcedimentoRN = new ProcedimentoRN();
				$arrObjProcedimentoDTO = $objProcedimentoRN->listarCompleto($objProcedimentoDTO);

				foreach ($arrObjAcessoExternoDTO as $objAcessoExternoDTO) {
					foreach ($arrObjProcedimentoDTO as $objProcedimentoDTO) {
						if ($objAcessoExternoDTO->getDblIdProtocoloAtividade() == $objProcedimentoDTO->getDblIdProcedimento()) {

							$objAcessoExternoDTO->setObjProcedimentoDTO($objProcedimentoDTO);

							$arrObjDocumentoDTO = $objProcedimentoDTO->getArrObjDocumentoDTO();
							foreach ($arrObjDocumentoDTO as $objDocumentoDTO) {
								if ($objAcessoExternoDTO->getDblIdDocumento() == $objDocumentoDTO->getDblIdDocumento()) {
									$objAcessoExternoDTO->setObjDocumentoDTO($objDocumentoDTO);
								}
							}
							break;
						}
					}
				}
			}

			//Auditoria

			return $arrObjAcessoExternoDTO;

		} catch (Exception $e) {
			throw new InfraException('Erro listando documentos para assinatura externa.', $e);
		}
	}

	protected function listarProcessosUsuarioExternoConectado(AcessoExternoDTO $parObjAcessoExternoDTO)
	{
		try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_listar', __METHOD__, $parObjAcessoExternoDTO);

			//Regras de Negocio
			$objInfraException = new InfraException();

			$objUsuarioDTO = new UsuarioDTO();
			$objUsuarioDTO->retNumIdUsuario();
			$objUsuarioDTO->retStrSigla();
			$objUsuarioDTO->retStrNome();
			$objUsuarioDTO->retStrStaTipo();
			$objUsuarioDTO->retNumIdContato();
			$objUsuarioDTO->setNumIdUsuario($parObjAcessoExternoDTO->getNumIdUsuarioExterno());

			$objUsuarioRN = new UsuarioRN();
			$objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

			if ($objUsuarioDTO->getStrStaTipo() == UsuarioRN::$TU_EXTERNO_PENDENTE) {
				$objInfraException->lancarValidacao('Usuário externo "' . $objUsuarioDTO->getStrSigla() . '" ainda não foi liberado.');
			}

			if ($objUsuarioDTO->getStrStaTipo() != UsuarioRN::$TU_EXTERNO) {
				$objInfraException->lancarValidacao('Usuário "' . $objUsuarioDTO->getStrSigla() . '" não é um usuário externo.');
			}

			$objAcessoExternoDTO = new AcessoExternoDTO();
			$objAcessoExternoDTO->retNumIdAcessoExterno();
			$objAcessoExternoDTO->retDblIdProtocoloAtividade();
			//$objAcessoExternoDTO->retDblIdDocumento();
			//$objAcessoExternoDTO->retStrSinProcesso();
			$objAcessoExternoDTO->retDtaValidade();
			//$objAcessoExternoDTO->setStrStaTipo(AcessoExternoRN::$TA_USUARIO_EXTERNO);
			$objAcessoExternoDTO->setStrSinProcesso('S');
			$objAcessoExternoDTO->setNumIdContatoParticipante($objUsuarioDTO->getNumIdContato());
			$objAcessoExternoDTO->setOrdDthAberturaAtividade(InfraDTO::$TIPO_ORDENACAO_DESC);

			//paginação
			//$objAcessoExternoDTO->setNumMaxRegistrosRetorno($parObjAcessoExternoDTO->getNumMaxRegistrosRetorno());
			//$objAcessoExternoDTO->setNumPaginaAtual($parObjAcessoExternoDTO->getNumPaginaAtual());

			$arrObjAcessoExternoDTO = $this->listar($objAcessoExternoDTO);

			//paginação
			//$parObjAcessoExternoDTO->setNumTotalRegistros($objAcessoExternoDTO->getNumTotalRegistros());
			//$parObjAcessoExternoDTO->setNumRegistrosPaginaAtual($objAcessoExternoDTO->getNumRegistrosPaginaAtual());

			if (count($arrObjAcessoExternoDTO)) {

				//Carregar dados do cabeçalho
				$objProcedimentoDTO = new ProcedimentoDTO();
				$objProcedimentoDTO->retStrNomeTipoProcedimento();
				$objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();

				$objProcedimentoDTO->setDblIdProcedimento(InfraArray::converterArrInfraDTO($arrObjAcessoExternoDTO, 'IdProtocoloAtividade'), InfraDTO::$OPER_IN);

				$objProcedimentoRN = new ProcedimentoRN();
				$arrObjProcedimentoDTO = $objProcedimentoRN->listarCompleto($objProcedimentoDTO);

				foreach ($arrObjAcessoExternoDTO as $objAcessoExternoDTO) {
					foreach ($arrObjProcedimentoDTO as $objProcedimentoDTO) {
						if ($objAcessoExternoDTO->getDblIdProtocoloAtividade() == $objProcedimentoDTO->getDblIdProcedimento()) {
							$objAcessoExternoDTO->setObjProcedimentoDTO($objProcedimentoDTO);
							break;
						}
					}
				}
			}

			//Auditoria

			return $arrObjAcessoExternoDTO;

		} catch (Exception $e) {
			throw new InfraException('Erro listando processos com acesso externo.', $e);
		}
	}

	protected function listarDisponibilizacoesConectado(AcessoExternoDTO $parObjAcessoExternoDTO)
	{
		try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_listar', __METHOD__, $parObjAcessoExternoDTO);

			$objAcessoExternoDTO = new AcessoExternoDTO();
			$objAcessoExternoDTO->setBolExclusaoLogica(false);
			$objAcessoExternoDTO->retNumIdAcessoExterno();
			$objAcessoExternoDTO->retStrSiglaContato();
			$objAcessoExternoDTO->retStrNomeContato();
			$objAcessoExternoDTO->retStrSiglaUnidade();
			$objAcessoExternoDTO->retStrDescricaoUnidade();
			$objAcessoExternoDTO->retNumIdAtividade();
			$objAcessoExternoDTO->retDthAberturaAtividade();
			$objAcessoExternoDTO->retNumIdTarefaAtividade();
			$objAcessoExternoDTO->retStrEmailDestinatario();
			$objAcessoExternoDTO->retDtaValidade();
			$objAcessoExternoDTO->retStrSinAtivo();

			$objAcessoExternoDTO->setStrStaTipo(array(AcessoExternoRN::$TA_INTERESSADO,
					AcessoExternoRN::$TA_DESTINATARIO_ISOLADO,
					AcessoExternoRN::$TA_USUARIO_EXTERNO), InfraDTO::$OPER_IN);

			$objAcessoExternoDTO->setDblIdProtocoloAtividade($parObjAcessoExternoDTO->getDblIdProtocoloAtividade());

			$objAcessoExternoDTO->setOrdDthAberturaAtividade(InfraDTO::$TIPO_ORDENACAO_DESC);

			$objAcessoExternoRN = new AcessoExternoRN();
			$arrObjAcessoExternoDTO = $objAcessoExternoRN->listar($objAcessoExternoDTO);

			$objAtributoAndamentoRN = new AtributoAndamentoRN();

			foreach ($arrObjAcessoExternoDTO as $objAcessoExternoDTO) {

				if ($objAcessoExternoDTO->getNumIdTarefaAtividade() == TarefaRN::$TI_LIBERACAO_ACESSO_EXTERNO) {
					$objAcessoExternoDTO->setDthCancelamento(null);
				} else {
					$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
					$objAtributoAndamentoDTO->retStrValor();
					$objAtributoAndamentoDTO->setStrNome('DATA_HORA');
					$objAtributoAndamentoDTO->setNumIdAtividade($objAcessoExternoDTO->getNumIdAtividade());

					$objAtributoAndamentoDTO = $objAtributoAndamentoRN->consultarRN1366($objAtributoAndamentoDTO);
					$objAcessoExternoDTO->setDthCancelamento($objAtributoAndamentoDTO->getStrValor());
				}
			}

			return $arrObjAcessoExternoDTO;

		} catch (Exception $e) {
			throw new InfraException('Erro listando disponibilizações de acesso externo.', $e);
		}
	}

	protected function cancelarDisponibilizacaoControlado($parArrObjAcessoExternoDTO)
	{
		try {

			SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_cancelar', __METHOD__, $parArrObjAcessoExternoDTO);


			$objInfraException = new InfraException();

			$objAcessoExternoDTO = new AcessoExternoDTO();
			$objAcessoExternoDTO->setBolExclusaoLogica(false);
			$objAcessoExternoDTO->retNumIdAcessoExterno();
			$objAcessoExternoDTO->retNumIdAtividade();
			$objAcessoExternoDTO->retDblIdProtocoloAtividade();
			$objAcessoExternoDTO->retNumIdTarefaAtividade();
			$objAcessoExternoDTO->retNumIdUnidadeAtividade();
			$objAcessoExternoDTO->retNumIdContatoParticipante();
			$objAcessoExternoDTO->retStrNomeContato();
			$objAcessoExternoDTO->retStrStaTipo();
			$objAcessoExternoDTO->retDblIdDocumento();
			$objAcessoExternoDTO->retStrProtocoloDocumentoFormatado();

			$objAcessoExternoDTO->setNumIdAcessoExterno(InfraArray::converterArrInfraDTO($parArrObjAcessoExternoDTO, 'IdAcessoExterno'), InfraDTO::$OPER_IN);

			$arrObjAcessoExternoDTO = InfraArray::indexarArrInfraDTO($this->listar($objAcessoExternoDTO), 'IdAcessoExterno');


			foreach ($parArrObjAcessoExternoDTO as $parObjAcessoExternoDTO) {

				$objAcessoExternoDTO = $arrObjAcessoExternoDTO[$parObjAcessoExternoDTO->getNumIdAcessoExterno()];

				if ($objAcessoExternoDTO == null) {
					throw new InfraException('Registro de acesso externo [' . $parObjAcessoExternoDTO->getNumIdAcessoExterno() . '] não encontrado.');
				}

				$objAcessoExternoDTO->setStrMotivo($parObjAcessoExternoDTO->getStrMotivo());

				if ($objAcessoExternoDTO->getStrStaTipo() != AcessoExternoRN::$TA_INTERESSADO &&
				$objAcessoExternoDTO->getStrStaTipo() != AcessoExternoRN::$TA_DESTINATARIO_ISOLADO &&
				$objAcessoExternoDTO->getStrStaTipo() != AcessoExternoRN::$TA_USUARIO_EXTERNO
				) {
					$objInfraException->adicionarValidacao('Registro [' . $objAcessoExternoDTO->getNumIdAcessoExterno() . '] não é uma Disponibilização de Acesso Externo.');
				}

				if ($objAcessoExternoDTO->getNumIdTarefaAtividade() == TarefaRN::$TI_LIBERACAO_ACESSO_EXTERNO_CANCELADA) {
					$objInfraException->adicionarValidacao('Disponibilização de acesso externo para "' . $objAcessoExternoDTO->getStrNomeContato() . '" já consta como cancelada.');
				} else if ($objAcessoExternoDTO->getNumIdTarefaAtividade() != TarefaRN::$TI_LIBERACAO_ACESSO_EXTERNO) {
					$objInfraException->adicionarValidacao('Andamento do processo [' . $objAcessoExternoDTO->getNumIdTarefaAtividade() . '] não é uma Disponibilização de Acesso Externo.');
				}

				if ($objAcessoExternoDTO->getNumIdUnidadeAtividade() != SessaoSEI::getInstance()->getNumIdUnidadeAtual()) {
					$objInfraException->adicionarValidacao('Disponibilização de acesso externo para o interessado "' . $objAcessoExternoDTO->getStrNomeContato() . '" não foi concedida pela unidade atual.');
				}
			}
			$objInfraException->lancarValidacoes();


			$strDataHoraAtual = InfraData::getStrDataHoraAtual();

			$objAtividadeRN = new AtividadeRN();
			$objAtributoAndamentoRN = new AtributoAndamentoRN();
			$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
			foreach ($arrObjAcessoExternoDTO as $objAcessoExternoDTO) {

				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->retStrNome();
				$objAtributoAndamentoDTO->retStrValor();
				$objAtributoAndamentoDTO->retStrIdOrigem();
				$objAtributoAndamentoDTO->setNumIdAtividade($objAcessoExternoDTO->getNumIdAtividade());

				$arrObjAtributoAndamentoDTO = $objAtributoAndamentoRN->listarRN1367($objAtributoAndamentoDTO);

				foreach ($arrObjAtributoAndamentoDTO as $objAtributoAndamentoDTO) {
					if ($objAtributoAndamentoDTO->getStrNome() == 'MOTIVO') {
						$objAtributoAndamentoDTO->setStrValor($objAcessoExternoDTO->getStrMotivo());
						break;
					}
				}

				//lança andamento para o usuário atual registrando o cancelamento da liberação
				$objAtividadeDTO = new AtividadeDTO();
				$objAtividadeDTO->setDblIdProtocolo($objAcessoExternoDTO->getDblIdProtocoloAtividade());
				$objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
				$objAtividadeDTO->setNumIdUnidadeOrigem(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
				$objAtividadeDTO->setNumIdUsuario(null);
				$objAtividadeDTO->setNumIdUsuarioOrigem(SessaoSEI::getInstance()->getNumIdUsuario());
				$objAtividadeDTO->setDtaPrazo(null);

				$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);

				$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_CANCELAMENTO_LIBERACAO_ACESSO_EXTERNO);

				$ret = $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);

				//altera andamento original de concessão ou transferência
				$objAtividadeDTO = new AtividadeDTO();

				$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_LIBERACAO_ACESSO_EXTERNO_CANCELADA);

				$objAtividadeDTO->setNumIdAtividade($objAcessoExternoDTO->getNumIdAtividade());
				$objAtividadeRN->mudarTarefa($objAtividadeDTO);

				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('USUARIO');
				$objAtributoAndamentoDTO->setStrValor(SessaoSEI::getInstance()->getStrSiglaUsuario() . '¥' . SessaoSEI::getInstance()->getStrNomeUsuario());
				$objAtributoAndamentoDTO->setStrIdOrigem(SessaoSEI::getInstance()->getNumIdUsuario());
				$objAtributoAndamentoDTO->setNumIdAtividade($objAcessoExternoDTO->getNumIdAtividade());
				$objAtributoAndamentoRN->cadastrarRN1363($objAtributoAndamentoDTO);

				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('DATA_HORA');
				$objAtributoAndamentoDTO->setStrValor($strDataHoraAtual);
				$objAtributoAndamentoDTO->setStrIdOrigem($ret->getNumIdAtividade()); //relaciona com o andamento de cassação
				$objAtributoAndamentoDTO->setNumIdAtividade($objAcessoExternoDTO->getNumIdAtividade());
				$objAtributoAndamentoRN->cadastrarRN1363($objAtributoAndamentoDTO);

				$objAcessoExternoBD->desativar($objAcessoExternoDTO);
			}

		} catch (Exception $e) {
			throw new InfraException('Erro cancelando disponibilização de acesso externo.', $e);
		}
	}


	protected function listarLiberacoesAssinaturaExternaConectado(AcessoExternoDTO $parObjAcessoExternoDTO)
	{
		try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_listar', __METHOD__, $parObjAcessoExternoDTO);

			$objAcessoExternoDTO = new AcessoExternoDTO();
			$objAcessoExternoDTO->setBolExclusaoLogica(false);
			$objAcessoExternoDTO->retNumIdAcessoExterno();
			$objAcessoExternoDTO->retStrSiglaContato();
			$objAcessoExternoDTO->retStrNomeContato();
			$objAcessoExternoDTO->retStrSiglaUnidade();
			$objAcessoExternoDTO->retStrDescricaoUnidade();
			$objAcessoExternoDTO->retNumIdAtividade();
			$objAcessoExternoDTO->retDthAberturaAtividade();
			$objAcessoExternoDTO->retNumIdTarefaAtividade();
			$objAcessoExternoDTO->retStrSinProcesso();
			$objAcessoExternoDTO->retNumIdContatoParticipante();
			$objAcessoExternoDTO->retStrSinAtivo();

			$objAcessoExternoDTO->setStrStaTipo(AcessoExternoRN::$TA_ASSINATURA_EXTERNA);
			$objAcessoExternoDTO->setDblIdDocumento($parObjAcessoExternoDTO->getDblIdDocumento());

			$objAcessoExternoRN = new AcessoExternoRN();
			$arrObjAcessoExternoDTO = $objAcessoExternoRN->listar($objAcessoExternoDTO);

			if (count($arrObjAcessoExternoDTO)) {

				$objAssinaturaRN = new AssinaturaRN();
				$objAtributoAndamentoRN = new AtributoAndamentoRN();

				foreach ($arrObjAcessoExternoDTO as $objAcessoExternoDTO) {

					$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
					$objAtributoAndamentoDTO->retStrIdOrigem();
					$objAtributoAndamentoDTO->setStrNome('DOCUMENTO');
					$objAtributoAndamentoDTO->setNumIdAtividade($objAcessoExternoDTO->getNumIdAtividade());
					$objAtributoAndamentoDTO = $objAtributoAndamentoRN->consultarRN1366($objAtributoAndamentoDTO);

					$objAssinaturaDTO = new AssinaturaDTO();
					$objAssinaturaDTO->retDthAberturaAtividade();
					$objAssinaturaDTO->setDblIdDocumento($objAtributoAndamentoDTO->getStrIdOrigem());
					$objAssinaturaDTO->setNumIdContatoUsuario($objAcessoExternoDTO->getNumIdContatoParticipante());

					$objAssinaturaDTO = $objAssinaturaRN->consultarRN1322($objAssinaturaDTO);

					if ($objAssinaturaDTO != null) {
						$objAcessoExternoDTO->setDthUtilizacao($objAssinaturaDTO->getDthAberturaAtividade());
					} else {
						$objAcessoExternoDTO->setDthUtilizacao(null);
					}

					if ($objAcessoExternoDTO->getNumIdTarefaAtividade() == TarefaRN::$TI_LIBERACAO_ASSINATURA_EXTERNA_CANCELADA) {
						$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
						$objAtributoAndamentoDTO->retStrValor();
						$objAtributoAndamentoDTO->setStrNome('DATA_HORA');
						$objAtributoAndamentoDTO->setNumIdAtividade($objAcessoExternoDTO->getNumIdAtividade());

						$objAtributoAndamentoDTO = $objAtributoAndamentoRN->consultarRN1366($objAtributoAndamentoDTO);
						$objAcessoExternoDTO->setDthCancelamento($objAtributoAndamentoDTO->getStrValor());
					} else {
						$objAcessoExternoDTO->setDthCancelamento(null);
					}
				}
			}

			return $arrObjAcessoExternoDTO;

		} catch (Exception $e) {
			throw new InfraException('Erro listando liberações de assinatura externa.', $e);
		}
	}


	protected function cancelarLiberacaoAssinaturaExternaControlado($parArrObjAcessoExternoDTO)
	{
		try {

			SessaoSEI::getInstance()->validarAuditarPermissao('assinatura_externa_cancelar', __METHOD__, $parArrObjAcessoExternoDTO);

			$objInfraException = new InfraException();

			$objAcessoExternoDTO = new AcessoExternoDTO();
			$objAcessoExternoDTO->setBolExclusaoLogica(false);
			$objAcessoExternoDTO->retNumIdAcessoExterno();
			$objAcessoExternoDTO->retNumIdAtividade();
			$objAcessoExternoDTO->retDblIdProtocoloAtividade();
			$objAcessoExternoDTO->retNumIdTarefaAtividade();
			$objAcessoExternoDTO->retNumIdUnidadeAtividade();
			$objAcessoExternoDTO->retNumIdContatoParticipante();
			$objAcessoExternoDTO->retStrStaTipo();
			$objAcessoExternoDTO->retDblIdDocumento();
			$objAcessoExternoDTO->retStrProtocoloDocumentoFormatado();
			$objAcessoExternoDTO->retStrSinProcesso();

			$objAcessoExternoDTO->setNumIdAcessoExterno(InfraArray::converterArrInfraDTO($parArrObjAcessoExternoDTO, 'IdAcessoExterno'), InfraDTO::$OPER_IN);

			$arrObjAcessoExternoDTO = InfraArray::indexarArrInfraDTO($this->listar($objAcessoExternoDTO), 'IdAcessoExterno');


			$objUsuarioDTO = new UsuarioDTO();
			$objUsuarioDTO->setBolExclusaoLogica(false);
			$objUsuarioDTO->retNumIdUsuario();
			$objUsuarioDTO->retNumIdContato();
			$objUsuarioDTO->retStrSigla();
			$objUsuarioDTO->retStrNome();
			$objUsuarioDTO->setNumIdContato(InfraArray::converterArrInfraDTO($arrObjAcessoExternoDTO, 'IdContatoParticipante'), InfraDTO::$OPER_IN);

			$objUsuarioRN = new UsuarioRN();
			$arrObjUsuarioDTO = InfraArray::indexarArrInfraDTO($objUsuarioRN->listarRN0490($objUsuarioDTO), 'IdContato');


			foreach ($parArrObjAcessoExternoDTO as $parObjAcessoExternoDTO) {

				$objAcessoExternoDTO = $arrObjAcessoExternoDTO[$parObjAcessoExternoDTO->getNumIdAcessoExterno()];
				$objUsuarioDTO = $arrObjUsuarioDTO[$objAcessoExternoDTO->getNumIdContatoParticipante()];

				if ($objAcessoExternoDTO == null) {
					throw new InfraException('Registro de acesso externo [' . $parObjAcessoExternoDTO->getNumIdAcessoExterno() . '] não encontrado.');
				}

				$objAcessoExternoDTO->setStrMotivo($parObjAcessoExternoDTO->getStrMotivo());

				if ($objAcessoExternoDTO->getStrStaTipo() != AcessoExternoRN::$TA_ASSINATURA_EXTERNA) {
					$objInfraException->adicionarValidacao('Registro [' . $objAcessoExternoDTO->getNumIdAcessoExterno() . '] não é uma Liberação de Assinatura Externa.');
				}

				if ($objAcessoExternoDTO->getNumIdTarefaAtividade() == TarefaRN::$TI_LIBERACAO_ASSINATURA_EXTERNA_CANCELADA) {
					$objInfraException->adicionarValidacao('Liberação de Assinatura Externa para o usuário "' . $objUsuarioDTO->getStrSigla() . '" no documento ' . $objAcessoExternoDTO->getStrProtocoloDocumentoFormatado() . ' já consta como cancelada.');
				} else if ($objAcessoExternoDTO->getNumIdTarefaAtividade() != TarefaRN::$TI_LIBERACAO_ASSINATURA_EXTERNA) {
					$objInfraException->adicionarValidacao('Andamento do processo [' . $objAcessoExternoDTO->getNumIdTarefaAtividade() . '] não é uma Liberação de Assinatura Externa.');
				}

				if ($objAcessoExternoDTO->getNumIdUnidadeAtividade() != SessaoSEI::getInstance()->getNumIdUnidadeAtual()) {
					$objInfraException->adicionarValidacao('Liberação de Assinatura Externa para o usuário "' . $objUsuarioDTO->getStrSigla() . '" no documento ' . $objAcessoExternoDTO->getStrProtocoloDocumentoFormatado() . ' não foi concedida pela unidade atual.');
				}

				if ($objAcessoExternoDTO->getStrSinProcesso() == 'N') {
					$objAssinaturaDTO = new AssinaturaDTO();
					$objAssinaturaDTO->retStrSiglaUsuario();
					$objAssinaturaDTO->setNumIdUsuario($objUsuarioDTO->getNumIdUsuario());
					$objAssinaturaDTO->setDblIdDocumento($objAcessoExternoDTO->getDblIdDocumento());

					$objAssinaturaRN = new AssinaturaRN();
					$objAssinaturaDTO = $objAssinaturaRN->consultarRN1322($objAssinaturaDTO);

					if ($objAssinaturaDTO != null) {
						$objInfraException->adicionarValidacao('Usuário "' . $objAssinaturaDTO->getStrSiglaUsuario() . '" já assinou o documento ' . $objAcessoExternoDTO->getStrProtocoloDocumentoFormatado() . '.');
					}
				}
			}
			$objInfraException->lancarValidacoes();

			$strDataHoraAtual = InfraData::getStrDataHoraAtual();

			$objAtividadeRN = new AtividadeRN();
			$objAtributoAndamentoRN = new AtributoAndamentoRN();
			$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());

			foreach ($arrObjAcessoExternoDTO as $objAcessoExternoDTO) {

				$objUsuarioDTO = $arrObjUsuarioDTO[$objAcessoExternoDTO->getNumIdContatoParticipante()];

				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->retStrNome();
				$objAtributoAndamentoDTO->retStrValor();
				$objAtributoAndamentoDTO->retStrIdOrigem();
				$objAtributoAndamentoDTO->setNumIdAtividade($objAcessoExternoDTO->getNumIdAtividade());

				$arrObjAtributoAndamentoDTO = $objAtributoAndamentoRN->listarRN1367($objAtributoAndamentoDTO);

				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('MOTIVO');
				$objAtributoAndamentoDTO->setStrValor($objAcessoExternoDTO->getStrMotivo());
				$objAtributoAndamentoDTO->setStrIdOrigem(null); //relaciona com o andamento de cassação
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

				//lança andamento para o usuário atual registrando o cancelamento da liberação
				$objAtividadeDTO = new AtividadeDTO();
				$objAtividadeDTO->setDblIdProtocolo($objAcessoExternoDTO->getDblIdProtocoloAtividade());
				$objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
				$objAtividadeDTO->setNumIdUnidadeOrigem(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
				$objAtividadeDTO->setNumIdUsuario(null);
				$objAtividadeDTO->setNumIdUsuarioOrigem(SessaoSEI::getInstance()->getNumIdUsuario());
				$objAtividadeDTO->setDtaPrazo(null);

				$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
				$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_CANCELAMENTO_LIBERACAO_ASSINATURA_EXTERNA);

				$ret = $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);

				//altera andamento original de concessão ou transferência
				$objAtividadeDTO = new AtividadeDTO();
				$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_LIBERACAO_ASSINATURA_EXTERNA_CANCELADA);
				$objAtividadeDTO->setNumIdAtividade($objAcessoExternoDTO->getNumIdAtividade());
				$objAtividadeRN->mudarTarefa($objAtividadeDTO);

				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('USUARIO');
				$objAtributoAndamentoDTO->setStrValor(SessaoSEI::getInstance()->getStrSiglaUsuario() . '¥' . SessaoSEI::getInstance()->getStrNomeUsuario());
				$objAtributoAndamentoDTO->setStrIdOrigem(SessaoSEI::getInstance()->getNumIdUsuario());
				$objAtributoAndamentoDTO->setNumIdAtividade($objAcessoExternoDTO->getNumIdAtividade());
				$objAtributoAndamentoRN->cadastrarRN1363($objAtributoAndamentoDTO);

				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('DATA_HORA');
				$objAtributoAndamentoDTO->setStrValor($strDataHoraAtual);
				$objAtributoAndamentoDTO->setStrIdOrigem($ret->getNumIdAtividade()); //relaciona com o andamento de cassação
				$objAtributoAndamentoDTO->setNumIdAtividade($objAcessoExternoDTO->getNumIdAtividade());
				$objAtributoAndamentoRN->cadastrarRN1363($objAtributoAndamentoDTO);


				$objAcessoExternoBD->desativar($objAcessoExternoDTO);
			}

		} catch (Exception $e) {
			throw new InfraException('Erro cancelando liberação de assinatura externa.', $e);
		}
	}


	/*
	 protected function alterarControlado(AcessoExternoDTO $objAcessoExternoDTO){
	try {

	//Valida Permissao
	SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_alterar',__METHOD__,$objAcessoExternoDTO);

	//Regras de Negocio
	$objInfraException = new InfraException();

	if ($objAcessoExternoDTO->isSetNumIdAtividade()){
	$this->validarNumIdAtividade($objAcessoExternoDTO, $objInfraException);
	}
	if ($objAcessoExternoDTO->isSetNumIdParticipante()){
	$this->validarNumIdParticipante($objAcessoExternoDTO, $objInfraException);
	}
	if ($objAcessoExternoDTO->isSetDtaValidade()){
	$this->validarDtaValidade($objAcessoExternoDTO, $objInfraException);
	}
	if ($objAcessoExternoDTO->isSetStrEmailUnidade()){
	$this->validarStrEmailUnidade($objAcessoExternoDTO, $objInfraException);
	}
	if ($objAcessoExternoDTO->isSetStrEmailDestinatario()){
	$this->validarStrEmailDestinatario($objAcessoExternoDTO, $objInfraException);
	}
	if ($objAcessoExternoDTO->isSetStrHashInterno()){
	$this->validarStrHashInterno($objAcessoExternoDTO, $objInfraException);
	}

	$objInfraException->lancarValidacoes();

	$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
	$objAcessoExternoBD->alterar($objAcessoExternoDTO);

	//Auditoria

	}catch(Exception $e){
	throw new InfraException('Erro alterando Acesso Externo.',$e);
	}
	}

	*/

	protected function excluirControlado($arrObjAcessoExternoDTO)
	{
		try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_excluir', __METHOD__, $arrObjAcessoExternoDTO);

			//Regras de Negocio
			$objInfraException = new InfraException();

			for ($i = 0; $i < count($arrObjAcessoExternoDTO); $i++) {

				$objAcessoExternoDTO = new AcessoExternoDTO();
				$objAcessoExternoDTO->setBolExclusaoLogica(false);
				$objAcessoExternoDTO->retStrStaTipo();
				$objAcessoExternoDTO->setNumIdAcessoExterno($arrObjAcessoExternoDTO[$i]->getNumIdAcessoExterno());

				$objAcessoExternoDTO = $this->consultar($objAcessoExternoDTO);

				if ($objAcessoExternoDTO->getStrStaTipo() != AcessoExternoRN::$TA_SISTEMA) {
					throw new InfraException('Acesso Externo não pode ser excluído.');
				}
			}

			$objInfraException->lancarValidacoes();

			$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
			for ($i = 0; $i < count($arrObjAcessoExternoDTO); $i++) {
				$objAcessoExternoBD->excluir($arrObjAcessoExternoDTO[$i]);
			}

			//Auditoria

		} catch (Exception $e) {
			throw new InfraException('Erro excluindo Acesso Externo.', $e);
		}
	}

	protected function consultarConectado(AcessoExternoDTO $objAcessoExternoDTO)
	{
		try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_consultar', __METHOD__, $objAcessoExternoDTO);

			//Regras de Negocio
			//$objInfraException = new InfraException();

			//$objInfraException->lancarValidacoes();

			$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
			$ret = $objAcessoExternoBD->consultar($objAcessoExternoDTO);

			//Auditoria

			return $ret;
		} catch (Exception $e) {
			throw new InfraException('Erro consultando Acesso Externo.', $e);
		}
	}

	protected function listarConectado(AcessoExternoDTO $objAcessoExternoDTO)
	{
		try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_listar', __METHOD__, $objAcessoExternoDTO);

			//Regras de Negocio
			//$objInfraException = new InfraException();

			//$objInfraException->lancarValidacoes();

			$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
			$ret = $objAcessoExternoBD->listar($objAcessoExternoDTO);

			//Auditoria

			return $ret;

		} catch (Exception $e) {
			throw new InfraException('Erro listando Acessos Externos.', $e);
		}
	}

	protected function contarConectado(AcessoExternoDTO $objAcessoExternoDTO)
	{
		try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_listar', __METHOD__, $objAcessoExternoDTO);

			//Regras de Negocio
			//$objInfraException = new InfraException();

			//$objInfraException->lancarValidacoes();

			$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
			$ret = $objAcessoExternoBD->contar($objAcessoExternoDTO);

			//Auditoria

			return $ret;
		} catch (Exception $e) {
			throw new InfraException('Erro contando Acessos Externos.', $e);
		}
	}

	public function listarValoresTipoAcessoExterno()
	{
		try {

			$arrObjTipoDTO = array();

			$objTipoDTO = new TipoDTO();
			$objTipoDTO->setStrStaTipo(self::$TA_INTERESSADO);
			$objTipoDTO->setStrDescricao('Interessado do Processo');
			$arrObjTipoDTO[] = $objTipoDTO;

			$objTipoDTO = new TipoDTO();
			$objTipoDTO->setStrStaTipo(self::$TA_USUARIO_EXTERNO);
			$objTipoDTO->setStrDescricao('Usuário Externo');
			$arrObjTipoDTO[] = $objTipoDTO;

			$objTipoDTO = new TipoDTO();
			$objTipoDTO->setStrStaTipo(self::$TA_DESTINATARIO_ISOLADO);
			$objTipoDTO->setStrDescricao('Destinatário Isolado');
			$arrObjTipoDTO[] = $objTipoDTO;

			$objTipoDTO = new TipoDTO();
			$objTipoDTO->setStrStaTipo(self::$TA_SISTEMA);
			$objTipoDTO->setStrDescricao('Sistema');
			$arrObjTipoDTO[] = $objTipoDTO;

			$objTipoDTO = new TipoDTO();
			$objTipoDTO->setStrStaTipo(self::$TA_ASSINATURA_EXTERNA);
			$objTipoDTO->setStrDescricao('Assinatura Externa de Documento');
			$arrObjTipoDTO[] = $objTipoDTO;

			return $arrObjTipoDTO;

		} catch (Exception $e) {
			throw new InfraException('Erro listando valores de Tipo de Acesso Externo.', $e);
		}
	}

	/*
	 protected function desativarControlado($arrObjAcessoExternoDTO){
	try {

	//Valida Permissao
	SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_desativar',__METHOD__,$arrObjAcessoExternoDTO);

	//Regras de Negocio
	//$objInfraException = new InfraException();

	//$objInfraException->lancarValidacoes();

	$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
	for($i=0;$i<count($arrObjAcessoExternoDTO);$i++){
	$objAcessoExternoBD->desativar($arrObjAcessoExternoDTO[$i]);
	}

	//Auditoria

	}catch(Exception $e){
	throw new InfraException('Erro desativando Acesso Externo.',$e);
	}
	}

	protected function reativarControlado($arrObjAcessoExternoDTO){
	try {

	//Valida Permissao
	SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_reativar',__METHOD__,$arrObjAcessoExternoDTO);

	//Regras de Negocio
	//$objInfraException = new InfraException();

	//$objInfraException->lancarValidacoes();

	$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
	for($i=0;$i<count($arrObjAcessoExternoDTO);$i++){
	$objAcessoExternoBD->reativar($arrObjAcessoExternoDTO[$i]);
	}

	//Auditoria

	}catch(Exception $e){
	throw new InfraException('Erro reativando Acesso Externo.',$e);
	}
	}

	protected function bloquearControlado(AcessoExternoDTO $objAcessoExternoDTO){
	try {

	//Valida Permissao
	SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_consultar',__METHOD__,$objAcessoExternoDTO);

	//Regras de Negocio
	//$objInfraException = new InfraException();

	//$objInfraException->lancarValidacoes();

	$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
	$ret = $objAcessoExternoBD->bloquear($objAcessoExternoDTO);

	//Auditoria

	return $ret;
	}catch(Exception $e){
	throw new InfraException('Erro bloqueando Acesso Externo.',$e);
	}
	}

	*/
}



?>