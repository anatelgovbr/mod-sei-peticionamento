<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 08/12/2016 - criado por Marcelo Bezerra - CAST
 *
 * Versão do Gerador de Código: 1.39.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntTipoRespRN extends InfraRN
{

    public static $EXIGE_RESPOSTA = 'E';
    public static $FACULTATIVA = 'F';

    public static $TIPO_PRAZO_EXTERNO_DIA = 'D';
    public static $TIPO_PRAZO_EXTERNO_MES = 'M';
    public static $TIPO_PRAZO_EXTERNO_ANO = 'A';

    public static $TIPO_DIA_CORRIDO = 'C';
    public static $TIPO_DIA_UTIL = 'U';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarStrTipoPrazoExterno(MdPetIntTipoRespDTO $objMdPetIntTipoRespDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntTipoRespDTO->getStrTipoPrazoExterno())) {
            $objInfraException->adicionarValidacao('Prazo Externo não informado.');
            $objInfraException->lancarValidacoes();
        } else {
            if ($objMdPetIntTipoRespDTO->getStrTipoPrazoExterno()=='D' && $_POST['rdTipoDia']=='') {
                $objInfraException->adicionarValidacao('Tipo de Prazo Externo (Corridos ou Úteis) não informado');
                $objInfraException->lancarValidacoes();
            }

            $objMdPetIntTipoRespDTO->setStrTipoPrazoExterno(trim($objMdPetIntTipoRespDTO->getStrTipoPrazoExterno()));

            if (strlen($objMdPetIntTipoRespDTO->getStrTipoPrazoExterno()) > 1) {
                $objInfraException->adicionarValidacao('Prazo Externo possui tamanho superior a 1 caracteres.');
                $objInfraException->lancarValidacoes();
            }
        }
    }

    private function validarNumValorPrazoExterno(MdPetIntTipoRespDTO $objMdPetIntTipoRespDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntTipoRespDTO->getNumValorPrazoExterno())) {
            $objInfraException->adicionarValidacao('Valor do Prazo Externo não informado.');
            $objInfraException->lancarValidacoes();
        }
    }

    private function validarStrNome(MdPetIntTipoRespDTO $objMdPetIntTipoRespDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntTipoRespDTO->getStrNome())) {
            $objInfraException->adicionarValidacao('Nome não informado.');
            $objInfraException->lancarValidacoes();
        } else {
            $objMdPetIntTipoRespDTO->setStrNome(trim($objMdPetIntTipoRespDTO->getStrNome()));

            if (strlen($objMdPetIntTipoRespDTO->getStrNome()) > 70) {
                $objInfraException->adicionarValidacao('Nome possui tamanho superior a 70 caracteres.');
                $objInfraException->lancarValidacoes();
            }

            //validação de duplicidade alterada para englobar nome, prazo e tipo de resposta
        }
    }
    
    private function validarDuplicidade(MdPetIntTipoRespDTO $objMdPetIntTipoRespDTO, InfraException $objInfraException)
    {
       
        //Cadastro ou alteração de Tipos de Respostas para aceitar mais de uma Resposta com:
        // 1) o mesmo Nome, 
        // 2) mesmo Prazo e 
        // 3)mesmo Tipo (Resposta Facultativa ou Exige Resposta).
        $objComparacaoDTO = new MdPetIntTipoRespDTO();
        $objComparacaoDTO->retTodos();
        $objComparacaoDTO->setStrNome( $objMdPetIntTipoRespDTO->getStrNome() );
        $objComparacaoDTO->setStrTipoPrazoExterno( $objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() );
        $objComparacaoDTO->setNumValorPrazoExterno( $objMdPetIntTipoRespDTO->getNumValorPrazoExterno() );
        $objComparacaoDTO->setStrTipoRespostaAceita( $objMdPetIntTipoRespDTO->getStrTipoRespostaAceita() );

        if ($objMdPetIntTipoRespDTO->getStrTipoPrazoExterno()==MdPetIntTipoRespRN::$TIPO_PRAZO_EXTERNO_DIA){
            if ($objMdPetIntTipoRespDTO->getStrTipoDia()==MdPetIntTipoRespRN::$TIPO_DIA_CORRIDO){
                $objComparacaoDTO->adicionarCriterio(array('TipoDia','TipoDia'), array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL), array('C',NULL), InfraDTO::$OPER_LOGICO_OR);
            }else if ($objMdPetIntTipoRespDTO->getStrTipoDia()) {
                $objComparacaoDTO->setStrTipoDia( $objMdPetIntTipoRespDTO->getStrTipoDia() );
            }else{
	            $objInfraException->adicionarValidacao('Para Tipo de Prazo em Dias é obrigatório informar Tipo de Prazo (Corridos ou Úteis).');
                $objInfraException->lancarValidacoes();
            }
        }

        $objComparacaoDTO->setNumIdMdPetIntTipoResp( $objMdPetIntTipoRespDTO->getNumIdMdPetIntTipoResp(), InfraDTO::$OPER_DIFERENTE );
        // $objComparacaoDTO->setStrSinAtivo('S');
        
        $total = $this->contar( $objComparacaoDTO );
        
        if( $total > 0){
            $objInfraException->adicionarValidacao('Tipo de Resposta já existente.');
            $objInfraException->lancarValidacoes();
        }
        
    }

    private function validarStrTipoRespostaAceita(MdPetIntTipoRespDTO $objMdPetIntTipoRespDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntTipoRespDTO->getStrTipoRespostaAceita())) {
            $objInfraException->adicionarValidacao('Resposta do Usuário Externo não informado.');
            $objInfraException->lancarValidacoes();
        } else {
            $objMdPetIntTipoRespDTO->setStrTipoRespostaAceita(trim($objMdPetIntTipoRespDTO->getStrTipoRespostaAceita()));

            if (strlen($objMdPetIntTipoRespDTO->getStrTipoRespostaAceita()) > 1) {
                $objInfraException->adicionarValidacao('Resposta do Usuário Externo possui tamanho superior a 1 caracteres.');
                $objInfraException->lancarValidacoes();
            }
        }
    }

    private function validarStrSinAtivo(MdPetIntTipoRespDTO $objMdPetIntTipoRespDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntTipoRespDTO->getStrSinAtivo())) {
            $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica não informado.');
        } else {
            if (!InfraUtil::isBolSinalizadorValido($objMdPetIntTipoRespDTO->getStrSinAtivo())) {
                $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica inválido.');
            }
        }
    }

    protected function cadastrarControlado(MdPetIntTipoRespDTO $objMdPetIntTipoRespDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_tipo_resp_cadastrar',__METHOD__,$objMdPetIntTipoRespDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarStrTipoPrazoExterno($objMdPetIntTipoRespDTO, $objInfraException, $_POST['rdTipoDia']);
            $this->validarNumValorPrazoExterno($objMdPetIntTipoRespDTO, $objInfraException);
            $this->validarStrNome($objMdPetIntTipoRespDTO, $objInfraException);
            $this->validarStrTipoRespostaAceita($objMdPetIntTipoRespDTO, $objInfraException);
            $this->validarStrSinAtivo($objMdPetIntTipoRespDTO, $objInfraException);
            
            //aplicando nova regra de duplicidade de registro englobando nome, prazo e tipo de resposta
            $this->validarDuplicidade($objMdPetIntTipoRespDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdPetIntTipoRespBD = new MdPetIntTipoRespBD($this->getObjInfraIBanco());

            $ret = $objMdPetIntTipoRespBD->cadastrar($objMdPetIntTipoRespDTO);

            //Auditoria

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando .', $e);
        }
    }

    protected function alterarControlado(MdPetIntTipoRespDTO $objMdPetIntTipoRespDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_tipo_resp_alterar',__METHOD__,$objMdPetIntTipoRespDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdPetIntTipoRespDTO->isSetStrTipoPrazoExterno()) {
                $this->validarStrTipoPrazoExterno($objMdPetIntTipoRespDTO, $objInfraException);
            }
            if ($objMdPetIntTipoRespDTO->isSetNumValorPrazoExterno()) {
                $this->validarNumValorPrazoExterno($objMdPetIntTipoRespDTO, $objInfraException);
            }
            if ($objMdPetIntTipoRespDTO->isSetStrNome()) {
                $this->validarStrNome($objMdPetIntTipoRespDTO, $objInfraException);
            }
            if ($objMdPetIntTipoRespDTO->isSetStrTipoRespostaAceita()) {
                $this->validarStrTipoRespostaAceita($objMdPetIntTipoRespDTO, $objInfraException);
            }
            if ($objMdPetIntTipoRespDTO->isSetStrSinAtivo()) {
                $this->validarStrSinAtivo($objMdPetIntTipoRespDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();
            
            //aplicando nova regra de duplicidade de registro englobando nome, prazo e tipo de resposta
            $this->validarDuplicidade($objMdPetIntTipoRespDTO, $objInfraException);

            $objMdPetIntTipoRespBD = new MdPetIntTipoRespBD($this->getObjInfraIBanco());
            $objMdPetIntTipoRespBD->alterar($objMdPetIntTipoRespDTO);

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro alterando .', $e);
        }
    }

    protected function excluirControlado($arrObjMdPetIntTipoRespDTO)
    {
        try {
            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_tipo_resp_excluir',__METHOD__,$arrObjMdPetIntTipoRespDTO);

            //Regras de Negocio
            $objMdPetIntRelIntimRespDTO = new MdPetIntRelIntimRespDTO();
            $objMdPetIntTipoRespBD = new MdPetIntTipoRespBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdPetIntTipoRespDTO); $i++) {
                $objMdPetIntRelIntimRespDTO->setNumIdMdPetIntTipoResp($arrObjMdPetIntTipoRespDTO[$i]->getNumIdMdPetIntTipoResp());
                $objMdPetIntRelIntimRespDTO->retTodos(true);

                $objMdPetIntRelIntimRespRN = new MdPetIntRelIntimRespRN();
                $arrObjMdPetIntRelIntimRespDTO = $objMdPetIntRelIntimRespRN->listar($objMdPetIntRelIntimRespDTO);

                if (count($arrObjMdPetIntRelIntimRespDTO) == 0) {
                    $objMdPetIntTipoRespBD->excluir($arrObjMdPetIntTipoRespDTO[$i]);
                } else {
                    $nomeTpResp = $this->_getNomeTipoResposta($arrObjMdPetIntTipoRespDTO[$i]->getNumIdMdPetIntTipoResp());
                    $objInfraException = new InfraException();
                    $objInfraException->adicionarValidacao('O Tipo de Resposta "'.$nomeTpResp.'" não pode ser excluído pois está vinculado à uma Intimação.');
                    $objInfraException->lancarValidacoes();
                }
            }

            //Auditoria

        } catch (Exception $e) {

            throw new InfraException('Erro excluindo .', $e);
        }
    }

    private function _getNomeTipoResposta($idTipoResp){
        $nome = '';
        $objMdPetIntTipoRespDTO = new MdPetIntTipoRespDTO();
        $objMdPetIntTipoRespDTO->setNumIdMdPetIntTipoResp($idTipoResp);
        $objMdPetIntTipoRespDTO->retStrNome();

        $objMdPetIntTipoRespDTO = $this->consultarConectado($objMdPetIntTipoRespDTO);

        $nome = !is_null($objMdPetIntTipoRespDTO) ? $objMdPetIntTipoRespDTO->getStrNome() : '';

        return $nome;
    }

    protected function consultarConectado(MdPetIntTipoRespDTO $objMdPetIntTipoRespDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_tipo_resp_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntTipoRespBD = new MdPetIntTipoRespBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntTipoRespBD->consultar($objMdPetIntTipoRespDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando .', $e);
        }
    }

    protected function listarConectado(MdPetIntTipoRespDTO $objMdPetIntTipoRespDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_tipo_resp_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntTipoRespBD = new MdPetIntTipoRespBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntTipoRespBD->listar($objMdPetIntTipoRespDTO);

            //Auditoria

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando .', $e);
        }
    }

    protected function contarConectado(MdPetIntTipoRespDTO $objMdPetIntTipoRespDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_tipo_resp_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntTipoRespBD = new MdPetIntTipoRespBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntTipoRespBD->contar($objMdPetIntTipoRespDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando .', $e);
        }
    }

    protected function desativarControlado($arrObjMdPetIntTipoRespDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_tipo_resp_desativar',__METHOD__,$arrObjMdPetIntTipoRespDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntTipoRespBD = new MdPetIntTipoRespBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdPetIntTipoRespDTO); $i++) {
                $objMdPetIntTipoRespBD->desativar($arrObjMdPetIntTipoRespDTO[$i]);
            }

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro desativando .', $e);
        }
    }

    protected function reativarControlado($arrObjMdPetIntTipoRespDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_tipo_resp_reativar',__METHOD__,$arrObjMdPetIntTipoRespDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntTipoRespBD = new MdPetIntTipoRespBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdPetIntTipoRespDTO); $i++) {
                $objMdPetIntTipoRespBD->reativar($arrObjMdPetIntTipoRespDTO[$i]);
            }

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro reativando .', $e);
        }
    }

    protected function bloquearControlado(MdPetIntTipoRespDTO $objMdPetIntTipoRespDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_tipo_resp_consultar',__METHOD__,$objMdPetIntTipoRespDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntTipoRespBD = new MdPetIntTipoRespBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntTipoRespBD->bloquear($objMdPetIntTipoRespDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro bloqueando .', $e);
        }
    }

    public function setRadio($varValorRadio, $varValor, $varValorPrazo = null)
    {
        if ($varValor === $varValorRadio) {
            $retorno[0] = ' checked="checked"';
            $retorno[1] = $varValorPrazo;
        } else {
            $retorno[0] = ' value="' . $varValorRadio . '" ';
            $retorno[1] = '';
        }
        return $retorno;
    }

}

?>