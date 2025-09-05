<?
/**
 * 19/02/2018 - criado por jaqueline.cast
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntRelatorioINT extends InfraINT {

    public static function getSituacoes()
    {
       $strReturn         = '';
       $arrSituacaoFiltro = array();
       $strOption         = '<option value="[valor]"> [descricaoSituacao] </option>';
       $strOptionSel      = '<option selected="selected" value="[valor]"> [descricaoSituacao] </option>';
       $arrSituacoes      = self::retornaArraySituacaoRelatorio(true);

        foreach($arrSituacoes as $key => $situacao)
        {
            //Verifica se no post temos id de situação para aplicar no filtro
            $hdnSituacao  = array_key_exists('hdnIdsSituacao', $_POST) ? $_POST['hdnIdsSituacao'] : null;
            if(!is_null($hdnSituacao) && $hdnSituacao != ''){
                 $arrSituacaoFiltro = json_decode($hdnSituacao);
            }

            //Verifica se esse valor deve ser selecionado
            if(count($arrSituacaoFiltro) > 0){
                $strOptionCorreta = in_array($key, $arrSituacaoFiltro) ? $strOptionSel : $strOption;
            }else{
                $strOptionCorreta = $key == MdPetIntimacaoRN::$TODAS ? $strOptionSel : $strOption;
            }

            $add        = str_replace('[valor]', $key, $strOptionCorreta);
            $add        = str_replace('[descricaoSituacao]', $situacao, $add);
            $strReturn .= ''.$add;
        }

        return $strReturn;
    }

    public static function retornaArraySituacaoRelatorio($retornaTodos = false){
        $arrSituacao = array();

        if($retornaTodos) {
            $arrSituacao[MdPetIntimacaoRN::$TODAS] = MdPetIntimacaoRN::$STR_TODAS_ACEITE;
        }

        $arrSituacao[MdPetIntimacaoRN::$INTIMACAO_PENDENTE]            = MdPetIntimacaoRN::$STR_INTIMACAO_PENDENTE_ACEITE;
        $arrSituacao[MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO] = MdPetIntimacaoRN::$STR_INTIMACAO_CUMPRIDA_POR_ACESSO;
        $arrSituacao[MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_PRAZO]      = MdPetIntimacaoRN::$STR_INTIMACAO_CUMPRIDA_PRAZO;
        $arrSituacao[MdPetIntimacaoRN::$INTIMACAO_RESPONDIDA]          = MdPetIntimacaoRN::$STR_INTIMACAO_RESPONDIDA_ACEITE;
        $arrSituacao[MdPetIntimacaoRN::$INTIMACAO_PRAZO_VENCIDO]       = MdPetIntimacaoRN::$STR_INTIMACAO_PRAZO_VENCIDO_ACEITE;

        return $arrSituacao;
    }

    public static function retornaArrayCorGrafico()
    {
      $letters = '0123456789ABCDEF';
      $color = '#';
      for ($i = 0; $i < 6; $i++) {
        $index = rand(0, 15);
        $color .= $letters[$index];
      }

      return $color;
      }
    
    public static function getOptionsTipoGrafico($selected){
        $strReturn  = '';
        $arrGrafico =  self::retornaArrayTiposGrafico();
        foreach($arrGrafico as $key=> $grafico){

            if($selected != 0 && $key == $selected){
                $strReturn .= '<option selected="selected" value="'.$key.'">'.$grafico.'</option>';
            }else {
                $strReturn .= '<option value="' . $key . '">' . $grafico . '</option>';
            }
        }

        return $strReturn;
    }

    public static function retornaArrayTiposGrafico(){
        $arrGrafico = array();
        $arrGrafico[MdPetIntRelatorioRN::$GRAFICO_BARRA]    = MdPetIntRelatorioRN::$STR_GRAFICO_BARRA;
        $arrGrafico[MdPetIntRelatorioRN::$GRAFICO_PIZZA]    = MdPetIntRelatorioRN::$STR_GRAFICO_PIZZA;
        $arrGrafico[MdPetIntRelatorioRN::$GRAFICO_RADAR]    = MdPetIntRelatorioRN::$STR_GRAFICO_RADAR;
        $arrGrafico[MdPetIntRelatorioRN::$GRAFICO_AR_POLAR] = MdPetIntRelatorioRN::$STR_GRAFICO_AR_POLAR;

        return $arrGrafico;
    }

    public static function _gerarGraficoBarra(){
        $objMdPetRelatorioRN  = new MdPetIntRelatorioRN();
        $arrDados    = $objMdPetRelatorioRN->getQtdDadosPorSituacao();
        return MdPetGraficoINT::gerarBarra($arrDados, 'barra', MdPetGraficoINT::$_tipoBar, 'Grafico Barra', '500px');
    }
    
    public static function gerarGraficosTipoIntimacao($tipoGrafico){
        return (new MdPetIntRelatorioRN())->getArrGraficosIntimacao($tipoGrafico);
    }

    public static function gerarGraficoGeral($tipoGrafico, $idGrafico = '0', $tamanho = 0){

        $tamanho = ($tamanho == 0) ? MdPetIntRelatorioRN::$GRAFICO_TAMANHO_PADRAO : $tamanho;
        $arrDadosGerais = (new MdPetIntRelatorioRN())->getQtdDadosPorSituacao();
        return static::_retornaHtmlGrafico($tipoGrafico, $arrDadosGerais, $idGrafico, $tamanho);

    }

    public static function _verificaNulo($arrDados){
        $retorno    = false;
        $valorTotal = 0;
        foreach($arrDados as $arr){
            $valorTotal += $arr['valor'];
        }

        $retorno = $valorTotal == 0;

        return $retorno;
    }

    public static function _retornaHtmlGrafico($tipoGrafico, $arrDados, $idGrafico, $tamanhoGrafico){
        $isNull          =  static::_verificaNulo($arrDados);

        if($isNull){
            return null;
        }

        switch ($tipoGrafico){
            case MdPetIntRelatorioRN::$GRAFICO_BARRA:
                $idCompleto = 'barra_'.$idGrafico;
                $htmlGrafico        = MdPetGraficoINT::gerarBarra($arrDados, $idCompleto, MdPetGraficoINT::$_tipoBar, $tamanhoGrafico);
                break;

            case MdPetIntRelatorioRN::$GRAFICO_PIZZA:
                $idCompleto = 'pizza_'.$idGrafico;
                $htmlGrafico = MdPetGraficoINT::gerarPizza($arrDados, $idCompleto, $tamanhoGrafico);
                break;

            case MdPetIntRelatorioRN::$GRAFICO_RADAR:
                $idCompleto = 'radar_'.$idGrafico;
                $htmlGrafico = MdPetGraficoINT::gerarRadar($arrDados, $idCompleto, $tamanhoGrafico);
                break;

            case MdPetIntRelatorioRN::$GRAFICO_AR_POLAR:
                $idCompleto = 'areaPolar_'.$idGrafico;
                $htmlGrafico = MdPetGraficoINT::gerarAreaPolar($arrDados, $idCompleto, $tamanhoGrafico);
                break;

            default:
                $htmlGrafico = '';
                break;
        }

        return $htmlGrafico;
    }


    public static function converterParaArrInfraDTO($arrObjDTO, $inicioLinha = 1){
        $arrAlfabeto = array('A','B','C','D','E','F','G', 'H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $arrRetorno = array();
        //obs : a key não foi reutilizada, pois a pesquisa  não segue uma ordem correta .

        $arrCabecalho = array('Processo', 'Documento Principal', 'Anexos', 'Destinatário','Tipo de Destinatário', 'Tipo de Intimação', 'Unidade Geradora', 'Data de Expedição', 'Situação da Intimação', 'Número SEI Certidão de Cumprimento','Data de Cumprimento');

        //Cabecalho
        $contador = 0;
       foreach($arrCabecalho as $label){
            $arrRetorno[1][$arrAlfabeto[$contador]] = $label;
            $contador++;
        }

        //Dados
        $contador = 0;
        foreach($arrObjDTO as $objDTO){
            $linhaExcel = $contador + $inicioLinha + 1;
            $arrRetorno[$linhaExcel][$arrAlfabeto[0]] = $objDTO->getStrProtocoloFormatadoProcedimento();
            $arrRetorno[$linhaExcel][$arrAlfabeto[1]] = $objDTO->getStrDocumentoPrincipal();
            $arrRetorno[$linhaExcel][$arrAlfabeto[2]] = $objDTO->getStrAnexos();
            $arrRetorno[$linhaExcel][$arrAlfabeto[3]] = $objDTO->getStrNomeContato();
            if($objDTO->getStrSinPessoaJuridica() == "S"){
                $arrRetorno[$linhaExcel][$arrAlfabeto[4]] = "Pessoa Jurídica";
            }else{
                $arrRetorno[$linhaExcel][$arrAlfabeto[4]] = "Pessoa Física";
            }
            $arrRetorno[$linhaExcel][$arrAlfabeto[5]] = $objDTO->getStrNomeTipoIntimacao();
            $arrRetorno[$linhaExcel][$arrAlfabeto[6]] = $objDTO->getStrSiglaUnidadeIntimacao();
            $arrRetorno[$linhaExcel][$arrAlfabeto[7]] = $objDTO->getDthDataCadastro();
            $arrRetorno[$linhaExcel][$arrAlfabeto[8]] = $objDTO->getStrSituacaoIntimacao();
            $arrRetorno[$linhaExcel][$arrAlfabeto[9]] = $objDTO->getStrDocumentoCertidaoAceite();
            $arrRetorno[$linhaExcel][$arrAlfabeto[10]] = $objDTO->getDthDataAceite();
            $contador++;
        }


        return $arrRetorno;
    }



}