<?php
    /**
     * @author André Luiz <andre.luiz@castgroup.com.br>
     * @since  04/04/2017
     */

    require_once dirname(__FILE__) . '/../../../SEI.php';

    class MdPetIntPrazoRN extends InfraRN
    {

        public function __construct()
        {
            parent::__construct();
        }

        protected function inicializarObjInfraIBanco()
        {
            return BancoSEI::getInstance();
        }

        protected function retornarTipoRespostaValidoConectado($arrParams)
        {
           
            $numIdMdPetIntimacao      = $arrParams[0];
            $idMdPetDest              = $arrParams[1];
            $retornaPrazoExpirado     = isset($arrParams[2]) && is_bool($arrParams[2]) ? $arrParams[2] : false;
            
            $retornaNomeComposto      = isset($arrParams[3]) && is_bool($arrParams[3]) ? $arrParams[3] : true;
            $objMdPetIntAceiteRN      = new MdPetIntAceiteRN();
            $dataCumprimentoIntimacao = $objMdPetIntAceiteRN->retornaDataCumprimentoIntimacao($idMdPetDest);
            $objMdPetIntRelTipoRespRN     = new MdPetIntRelTipoRespRN();
            
            $arrObjMdPetIntRelTipoRespDTO = $objMdPetIntRelTipoRespRN->listarTipoResposta(array($numIdMdPetIntimacao,$idMdPetDest));

            //A contagem do Prazo Externo deve ser iniciada somente no dia util seguinte ao da "Data de Cumprimento da Intimacao"
            InfraArray::ordenarArrInfraDTO($arrObjMdPetIntRelTipoRespDTO, 'Nome', InfraArray::$TIPO_ORDENACAO_ASC);

            $arrObjMdPetIntRelTipoRespValido = $this->_retornaArrayTpRespValidoFormatados($arrObjMdPetIntRelTipoRespDTO, $retornaPrazoExpirado, $retornaNomeComposto);

            InfraArray::ordenarArrInfraDTO($arrObjMdPetIntRelTipoRespValido, 'Nome', InfraArray::$TIPO_ORDENACAO_ASC);
            
            return $arrObjMdPetIntRelTipoRespValido;
        }


        protected function retornarTipoRespostaValidoTelaRespostaConectado($arrParams)
        {
           
            $numIdMdPetIntimacao      = $arrParams[0];
            $idMdPetDest              = $arrParams[1];
            $retornaPrazoExpirado     = isset($arrParams[2]) && is_bool($arrParams[2]) ? $arrParams[2] : false;
            
            $retornaNomeComposto      = isset($arrParams[3]) && is_bool($arrParams[3]) ? $arrParams[3] : true;
            $objMdPetIntAceiteRN      = new MdPetIntAceiteRN();
            $dataCumprimentoIntimacao = $objMdPetIntAceiteRN->retornaDataCumprimentoIntimacao($idMdPetDest);
            $objMdPetIntRelTipoRespRN     = new MdPetIntRelTipoRespRN();
            
            $arrObjMdPetIntRelTipoRespDTO = $objMdPetIntRelTipoRespRN->listarTipoRespostaExterno(array($numIdMdPetIntimacao,$idMdPetDest));
           //A contagem do Prazo Externo deve ser iniciada somente no dia útil seguinte ao da "Data de Cumprimento da Intimação",
          
            InfraArray::ordenarArrInfraDTO($arrObjMdPetIntRelTipoRespDTO, 'Nome', InfraArray::$TIPO_ORDENACAO_ASC);

            $arrObjMdPetIntRelTipoRespValido = $this->_retornaArrayTpRespValidoFormatados($arrObjMdPetIntRelTipoRespDTO, $retornaPrazoExpirado, $retornaNomeComposto);

            InfraArray::ordenarArrInfraDTO($arrObjMdPetIntRelTipoRespValido, 'Nome', InfraArray::$TIPO_ORDENACAO_ASC);
            return $arrObjMdPetIntRelTipoRespValido;
        }

        private function _retornaArrayTpRespValidoFormatados($arrObjMdPetIntRelTipoRespDTO, $retornaPrazoExpirado, $retornaNomeComposto){
            $dataAtual = InfraData::getStrDataAtual();
            $arrObjMdPetIntRelTipoRespValido = array();

            foreach ($arrObjMdPetIntRelTipoRespDTO as $objMdPetIntRelTipoRespDTO) {

                $dataFinal = $objMdPetIntRelTipoRespDTO->getDthDataProrrogada();
                if (empty($dataFinal)){
                    $dataFinal = $objMdPetIntRelTipoRespDTO->getDthDataLimite();
                }
                if (is_array(explode(" ", $dataFinal))){
                    $dataFinal=explode(" ", $dataFinal);
                    $dataFinal=$dataFinal[0];
                }

                $nome = $objMdPetIntRelTipoRespDTO->getStrNome();

                if ($objMdPetIntRelTipoRespDTO->getStrTipoPrazoExterno() == 'D') {
                    if ($retornaPrazoExpirado || InfraData::compararDatas($dataFinal, $dataAtual) <= 0) {
                        if ($retornaNomeComposto){
                            $nome .=  ' (';
                            $nome .= $objMdPetIntRelTipoRespDTO->getNumValorPrazoExterno();

                            $tipoDia = '';
                            if ($objMdPetIntRelTipoRespDTO->getStrTipoDia() == 'U') {
                                $tipoDia = ' Útil';
                                if ($objMdPetIntRelTipoRespDTO->getNumValorPrazoExterno() > 1) {
                                    $tipoDia = ' Úteis';
                                }
                            }
                            $nome .= $objMdPetIntRelTipoRespDTO->getNumValorPrazoExterno() > 1 ?  ' Dias'.$tipoDia : ' Dia'.$tipoDia;

                            $nome .= ') - Data Limite: ' . $dataFinal;
                        }
                    }
                    $objMdPetIntRelTipoRespDTO->setStrNome($nome);
                    $objMdPetIntRelTipoRespDTO->setNumPrazoFinal(strtotime(str_replace('/', '-',$dataFinal)));
                    $arrObjMdPetIntRelTipoRespValido[] = $objMdPetIntRelTipoRespDTO;
                } else if ($objMdPetIntRelTipoRespDTO->getStrTipoPrazoExterno() == 'M') {
                    if ($retornaPrazoExpirado || InfraData::compararDatas($dataFinal, $dataAtual) <= 0) {
                        if ($retornaNomeComposto){
                            $nome .=  ' (';
                            $nome .= $objMdPetIntRelTipoRespDTO->getNumValorPrazoExterno();
                            $nome .= $objMdPetIntRelTipoRespDTO->getNumValorPrazoExterno() > 1 ?  ' meses' : ' mês';
                            $nome .= ') - Data Limite: ' . $dataFinal;
                        }
                    }
                    $objMdPetIntRelTipoRespDTO->setStrNome($nome);
                    $objMdPetIntRelTipoRespDTO->setNumPrazoFinal(strtotime(str_replace('/', '-',$dataFinal)));
                    $arrObjMdPetIntRelTipoRespValido[] = $objMdPetIntRelTipoRespDTO;
                } else if ($objMdPetIntRelTipoRespDTO->getStrTipoPrazoExterno() == 'A') {
                    if ($retornaPrazoExpirado || InfraData::compararDatas($dataFinal, $dataAtual) <= 0) {
                        if ($retornaNomeComposto){
                            $nome .=  ' (';
                            $nome .= $objMdPetIntRelTipoRespDTO->getNumValorPrazoExterno();
                            $nome .= $objMdPetIntRelTipoRespDTO->getNumValorPrazoExterno() > 1 ?  ' anos' : ' ano';
                            $nome .= ') - Data Limite: ' . $dataFinal;
                        }
                    }
                    $objMdPetIntRelTipoRespDTO->setStrNome($nome);
                    $objMdPetIntRelTipoRespDTO->setNumPrazoFinal(strtotime(str_replace('/', '-',$dataFinal)));
                    $arrObjMdPetIntRelTipoRespValido[] = $objMdPetIntRelTipoRespDTO;
                } else {
                    $objMdPetIntRelTipoRespDTO->setStrNome($objMdPetIntRelTipoRespDTO->getStrNome());
                    $objMdPetIntRelTipoRespDTO->setNumPrazoFinal(0);
                    $objMdPetIntRelTipoRespDTO->unSetDthDataLimite();
                    $arrObjMdPetIntRelTipoRespValido[] = $objMdPetIntRelTipoRespDTO;
                }

            }
            return $arrObjMdPetIntRelTipoRespValido;
        }

        protected function retornarTipoRespostaDataLimiteConectado($arrParams)
        {

            $numIdMdPetIntimacao      = $arrParams[0];
            $idMdPetDest              = $arrParams[1];
            $numIdMdPetIntTipoResp    = isset($arrParams[2]) ? $arrParams[2] : $arrParams[2];

            $objMdPetIntAceiteRN      = new MdPetIntAceiteRN();
            $dataCumprimentoIntimacao = $objMdPetIntAceiteRN->retornaDataCumprimentoIntimacao($idMdPetDest);

            $objMdPetIntRelTipoRespRN     = new MdPetIntRelTipoRespRN();


            $arrObjMdPetIntRelTipoRespDTO = $objMdPetIntRelTipoRespRN->listarTipoResposta( array($numIdMdPetIntimacao, $idMdPetDest) );

            $dataAtual = InfraData::getStrDataAtual();
            $arrObjMdPetIntRelTipoRespDestDTO = array();

//            InfraArray::ordenarArrInfraDTO($arrObjMdPetIntRelTipoRespValido, 'Nome', InfraArray::$TIPO_ORDENACAO_ASC);
            // Pode mais de uma tipo de resposta?
            foreach ($arrObjMdPetIntRelTipoRespDTO as $objMdPetIntRelTipoRespDTO) {
                //FACULTATIVA ou outra com Prazo = 0 não são permitidas?
                if ($objMdPetIntRelTipoRespDTO->getNumValorPrazoExterno()>0) {
                    $objMdPetIntRelTipoRespDestDTO = new MdPetIntRelTipoRespDestDTO();
                    $objMdPetIntRelTipoRespDestDTO->setNumIdMdPetIntRelTipoResp($objMdPetIntRelTipoRespDTO->getNumIdMdPetIntRelTipoResp());
                    $objMdPetIntRelTipoRespDestDTO->setNumIdMdPetIntRelDest($idMdPetDest);
                    $objMdPetIntRelTipoRespDestDTO->retNumIdMdPetIntTipoRespDest();
                    $objMdPetIntRelTipoRespDestDTO->retNumIdMdPetIntRelTipoResp();
                    $objMdPetIntRelTipoRespDestDTO->retNumIdMdPetIntRelDest();
                    $objMdPetIntRelTipoRespDestDTO->retDthDataLimite();
                    $objMdPetIntRelTipoRespDestDTO->retDthDataProrrogada();
                    $objMdPetIntRelTipoRespDestRN = new MdPetIntRelTipoRespDestRN();
                    $arrObjMdPetIntRelTipoRespDestExistenteDTO = $objMdPetIntRelTipoRespDestRN->listar($objMdPetIntRelTipoRespDestDTO);
                    if (count($arrObjMdPetIntRelTipoRespDestExistenteDTO)==0){
                        $dataCalculada = $this->calcularDataPrazoPorTipo($objMdPetIntRelTipoRespDTO->getStrTipoPrazoExterno(), $objMdPetIntRelTipoRespDTO->getNumValorPrazoExterno(), $dataCumprimentoIntimacao, $objMdPetIntRelTipoRespDTO->getStrTipoDia());
                        $objMdPetIntRelTipoRespDTO->setDthDataLimite($dataCalculada);
                        $objMdPetIntRelTipoRespDestDTO->setDthDataLimite($objMdPetIntRelTipoRespDTO->getDthDataLimite());
                        $arrObjMdPetIntRelTipoRespDestDTO[] = $objMdPetIntRelTipoRespDestDTO;
                    }
                }

            }

            return $arrObjMdPetIntRelTipoRespDestDTO;
        }

        public function somarDiaUtil($numQtde, $strData, $numIdOrgao = null)
        {

            $strDataFinal = InfraData::calcularData(($numQtde + 365), InfraData::$UNIDADE_DIAS, InfraData::$SENTIDO_ADIANTE, $strData);

            $this->_removerTimeDate($strData);
            $arrFeriados  = $this->_recuperarFeriados($strData, $strDataFinal, $numIdOrgao);

            $count = 0;
            while ($count < $numQtde) {
                $strData = InfraData::calcularData(1, InfraData::$UNIDADE_DIAS, InfraData::$SENTIDO_ADIANTE, $strData);
                if (!in_array(InfraData::obterDescricaoDiaSemana($strData), ['sábado', 'domingo']) && !in_array($strData, $arrFeriados)) {
                    $count++;
                }
            }

            return $strData;

        }


        private function _recuperarFeriados($strDataInicial, $strDataFinal, $numIdOrgao = null)
        {

        	if(is_null($numIdOrgao)){
		        $numIdOrgao = SessaoSEI::getInstance()->getNumIdOrgaoUnidadeAtual();
		        $numIdOrgao = is_null($numIdOrgao) ? SessaoSEIExterna::getInstance()->getNumIdOrgaoUnidadeAtual() : $numIdOrgao;

		        if (is_null($numIdOrgao)){
			        $objOrgaoDTO = new OrgaoDTO();
			        $objOrgaoDTO->retNumIdOrgao();
			        $objOrgaoDTO->setBolExclusaoLogica(false);
			        $objOrgaoDTO->adicionarCriterio(array('SinAtivo','Sigla'),array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),array('S',ConfiguracaoSEI::getInstance()->getValor('SessaoSEI','SiglaOrgaoSistema')),InfraDTO::$OPER_LOGICO_AND);

			        $objOrgaoRN = new OrgaoRN();
			        $arrObjOrgaoDTO = $objOrgaoRN->listarRN1353($objOrgaoDTO);
			        $numIdOrgao = !is_null($arrObjOrgaoDTO) && count($arrObjOrgaoDTO) > 0 ? current($arrObjOrgaoDTO)->getNumIdOrgao() : null;
		        }
	        }

            $arrFeriados    = array();

            $objFeriadoRN   = new FeriadoRN();
            $objFeriadoDTO  = new FeriadoDTO();

            $objFeriadoDTO->retDtaFeriado();
            $objFeriadoDTO->retStrDescricao();

            if(is_numeric($numIdOrgao)){
                $objFeriadoDTO->adicionarCriterio(array('IdOrgao','IdOrgao'),
                    array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
                    array(null,$numIdOrgao),
                    array(InfraDTO::$OPER_LOGICO_OR));
            }else{
                $objFeriadoDTO->setNumIdOrgao(null);
            }

            $objFeriadoDTO->adicionarCriterio(
            	array('Feriado', 'Feriado'),
                array(InfraDTO::$OPER_MAIOR_IGUAL, InfraDTO::$OPER_MENOR_IGUAL),
                array($strDataInicial, $strDataFinal),
                array(InfraDTO::$OPER_LOGICO_AND)
            );

            $objFeriadoDTO->setOrdDtaFeriado(InfraDTO::$TIPO_ORDENACAO_ASC);

            $count = $objFeriadoRN->contar($objFeriadoDTO);
            $arrObjFeriadoDTO = $objFeriadoRN->listar($objFeriadoDTO);

            if($count > 0){
                $arrFeriados = InfraArray::converterArrInfraDTO($arrObjFeriadoDTO, 'Feriado');
            }

            return $arrFeriados;

        }


        private function _recuperarIndisponibilidade($data=null)
        {
            $objMdPetIndisponibilidadeDTO = new MdPetIndisponibilidadeDTO();
            $objMdPetIndisponibilidadeDTO->retTodos();
            $objMdPetIndisponibilidadeDTO->setStrSinProrrogacao('S');
            if (!is_null($data)){
                $objMdPetIndisponibilidadeDTO->setDthDataInicio($data , InfraDTO::$OPER_MENOR_IGUAL);
                $objMdPetIndisponibilidadeDTO->setDthDataFim($data , InfraDTO::$OPER_MAIOR_IGUAL);
            }

            $objMdPetIndisponibilidadeDTO->setOrd('DataInicio', InfraDTO::$TIPO_ORDENACAO_ASC);

            $objMdPetIndisponibilidadeRN = new MdPetIndisponibilidadeRN();
            $arrObjMdPetIndisponibilidadeDTO = $objMdPetIndisponibilidadeRN->listar($objMdPetIndisponibilidadeDTO);        	

            return $arrObjMdPetIndisponibilidadeDTO;
        }

        private function _somarMes($numMes, $strData)
        {
            $strDataFinal = InfraData::calcularData(($numMes + 12), InfraData::$UNIDADE_MESES, InfraData::$SENTIDO_ADIANTE, $strData);
            $arrFeriados  = $this->_recuperarFeriados($strData, $strDataFinal);

            $this->_removerTimeDate($strData);
            $strDataEUA    = implode('-', array_reverse(explode('/', $strData)));
            $objData       = new DateTime($strDataEUA);
            $numMes        = '+' . $numMes . 'month';
            $novaData      = $objData->modify($numMes);
            $dataCalculada = $novaData->format('d/m/Y');

            while (InfraData::obterDescricaoDiaSemana($dataCalculada) == 'sábado' ||
                InfraData::obterDescricaoDiaSemana($dataCalculada) == 'domingo' ||
                in_array($dataCalculada, $arrFeriados)) {
                $dataCalculada = InfraData::calcularData(1, InfraData::$UNIDADE_DIAS, InfraData::$SENTIDO_ADIANTE, $dataCalculada);
            }

            return $dataCalculada;

        }

        private function _somarAno($numAno, $strData)
        {
            $strDataFinal = InfraData::calcularData($numAno, InfraData::$UNIDADE_ANOS, InfraData::$SENTIDO_ADIANTE, $strData);
            $arrFeriados  = $this->_recuperarFeriados($strData, $strDataFinal);

            $this->_removerTimeDate($strData);
            $strDataEUA    = implode('-', array_reverse(explode('/', $strData)));
            $objData       = new DateTime($strDataEUA);
            $numAno        = '+' . $numAno . 'year';
            $novaData      = $objData->modify($numAno);
            $dataCalculada = $novaData->format('d/m/Y');

            while (InfraData::obterDescricaoDiaSemana($dataCalculada) == 'sábado' ||
                InfraData::obterDescricaoDiaSemana($dataCalculada) == 'domingo' ||
                in_array($dataCalculada, $arrFeriados)) {
                $dataCalculada = InfraData::calcularData(1, InfraData::$UNIDADE_DIAS, InfraData::$SENTIDO_ADIANTE, $dataCalculada);


            return $dataCalculada;
            }
        }

        private function _removerTimeDate(&$strData){
            $countDate  = strlen($strData);
            $isDateTime = $countDate > 10 ? true : false;
            if($isDateTime){
                $arrData = explode(" ",$strData);
                $strData = $arrData[0];
            }
        }

        public function calcularDataPrazo($prazoTacita, $dataCumprimentoIntimacao = null, $numIdOrgao = null)
        {
            // DATA INÍCIO
            if (is_null($dataCumprimentoIntimacao)) {
                $dataCumprimentoIntimacao = InfraData::getStrDataAtual();
            }

            if ($prazoTacita>1){
                $dataCumprimentoIntimacao = $this->somarDiaUtil(1, $dataCumprimentoIntimacao, $numIdOrgao);
                if ($prazoTacita>2){
                    $data = DateTime::createFromFormat('d/m/Y', $dataCumprimentoIntimacao);
                    $dtsSomar = 'P'.($prazoTacita-2).'D';
                    $data->add(new DateInterval($dtsSomar));
                    $dataCumprimentoIntimacao =  $data->format('d/m/Y');
                }
            }

            return $this->somarDiaUtil(1, $dataCumprimentoIntimacao, $numIdOrgao);
        }

        public function calcularDataPrazoPorTipo($tipo, $prazo, $dataCumprimentoIntimacao = null, $tipoDia = null, $numIdOrgao = null) {

            // DATA INÍCIO
            if (is_null($dataCumprimentoIntimacao)) {
                $dataCumprimentoIntimacao = InfraData::getStrDataAtual();
            }

            if ($tipo=='D'){
                if ($prazo>1){
                    $qtdDia = 1;
                    if($tipoDia == 'U'){
                      $qtdDia = $prazo;
                    }
                    $dataCumprimentoIntimacao = $this->somarDiaUtil($qtdDia, $dataCumprimentoIntimacao, $numIdOrgao);

                  if ($tipoDia != 'U') {
                    if ($prazo > 2) {
                      $data = DateTime::createFromFormat('d/m/Y', $dataCumprimentoIntimacao);
                      $dtsSomar = 'P' . ($prazo - 2) . 'D';
                      $data->add(new DateInterval($dtsSomar));
                      $dataCumprimentoIntimacao = $data->format('d/m/Y');
                    }
                  }
                }
            } elseif ($tipo=='M' || $tipo=='A'){
                if ($prazo>0){
                    $dataCumprimentoIntimacao = $this->somarDiaUtil(1, $dataCumprimentoIntimacao, $numIdOrgao);
                    $data = DateTime::createFromFormat('d/m/Y', $dataCumprimentoIntimacao);
                    $dtsSomar = $tipo=='M' ? 'P'.$prazo.'M' : 'P'.$prazo.'Y';
                    $data->add(new DateInterval($dtsSomar));
                    $dtsSomar = 'P1D';
                    $data->sub(new DateInterval($dtsSomar));
                    $dataCumprimentoIntimacao =  $data->format('d/m/Y');
                }
            }


            if (is_null($tipoDia) || $tipoDia!='U'){
              $dataCumprimentoIntimacao =  $this->somarDiaUtil(1, $dataCumprimentoIntimacao, $numIdOrgao);
            }

            return $dataCumprimentoIntimacao;
        }

        public function calcularDataProrrogacao($dataLimite, $dataInicio, $dataFim) {

            if (is_null($dataLimite)) {
                $dataLimite = InfraData::getStrDataAtual();
            }

            if (InfraData::compararDatas($dataInicio, $dataLimite)>=0 && InfraData::compararDatas($dataLimite, $dataFim)>=0){
                $dataLimite = $this->somarDiaUtil(1, $dataLimite);
                return $this->calcularDataProrrogacao($dataLimite, $dataInicio, $dataFim);
            }
            return $dataLimite;

        }
    }
