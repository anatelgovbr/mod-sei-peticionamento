<?php

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 0);

$objMdPetIntRelatorioRN  = new MdPetIntRelatorioRN();
$objConsultaDTO    = $objMdPetIntRelatorioRN->retornaSelectsRelatorio();
$arrObjDTO         = $objMdPetIntRelatorioRN->listarDados($objConsultaDTO);
$objPHPExcel       = new PHPExcel();

$arrDadosPlanilha  = MdPetIntRelatorioINT::converterParaArrInfraDTO($arrObjDTO);

$primeiraPosition    = '';
$ultimaPosition      = '';
$getPrimeiraPosition = false;
$primeiraLinha       = 0;
$colunaAtual         = '';

foreach($arrDadosPlanilha as $linha => $dadosLinha){
    foreach($dadosLinha as $coluna => $dado){
        $colunaAtual  = $coluna;
        $positionDado = $coluna.$linha;
        $qtdLinhas    = count($arrDadosPlanilha);

        if(!$getPrimeiraPosition){
            $primeiraPosition    = $positionDado;
            $primeiraLinha       = $linha;
            $getPrimeiraPosition = true;
        }

        $ultimaLinha    = $linha;
        $ultimaPosition = $positionDado;

        $value = utf8_encode($dado);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($positionDado, $value);
        $objPHPExcel->getActiveSheet()->getColumnDimension($coluna)->setAutoSize(true);

        //Borda Colunas
        $bordaColuna = $colunaAtual.$primeiraLinha.':'.$colunaAtual.$qtdLinhas;

        $objPHPExcel->getActiveSheet()->getStyle($bordaColuna)->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    }
}


$dtHrAtual   = InfraData::getStrDataHoraAtual();
$nomeArquivo = 'SEI - Intimações Eletrônicas - '.$dtHrAtual;

$bordaCompleta = $primeiraPosition.':'.$ultimaPosition;
$objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()
    ->getStyle('A1:I1')
    ->getFill()
    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
    ->getStartColor()
    ->setRGB('E4E4E4');

$objPHPExcel->getActiveSheet()->getStyle($bordaCompleta)->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

if(empty($arrObjDTO)){
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:I2');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', 'Nenhum registro encontrado.');
    $objPHPExcel->getActiveSheet()->getStyle('A2:I2')->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
}

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$nomeArquivo.'.xlsx"');
header('Cache-Control: max-age=0');
ob_end_clean();

$objWriter->save('php://output');

exit();

?>

