<?php

namespace Business\Util;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Excel
{
	public function exportar($cabeceras, $detalle, $documento = 'Archivo')
	{
		$spreadsheet = new Spreadsheet();
		$styleArray = [
            // estilo de la fuente
			'font' => [
				'bold' => true,
				'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE],
			],
            // alinicion del texto
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
			],
            // background de fondo
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'argb' => '223368',
				],
			],
		];
		$ABC = 'A';
		foreach ($cabeceras as $cabecera) {
			$spreadsheet->setActiveSheetIndex(0)->setCellValue($ABC.'1', $cabecera);
			$spreadsheet->getActiveSheet()->getStyle($ABC.'1')->applyFromArray($styleArray);
			++$ABC;
		}

        // recorre el array de los detalle y insertar los valores
		$styleArrayTwo = [
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
			]
		];
		$num = 2;
		$letra = 'A';
		foreach ($detalle as $key => $data) {
			$letra = 'A';
			foreach ($cabeceras as $llave => $valueCabecera) {
				// agregando informacion a cada celda
				$spreadsheet->setActiveSheetIndex(0)
					->setCellValue($letra.$num, $data[$llave]);
				// agregando estilos a cada celda
				$spreadsheet->getActiveSheet()->getStyle($letra.$num)->applyFromArray($styleArrayTwo);
				$spreadsheet->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
				++$letra;
			}
			$num++;
		}

		//$spreadsheet->getActiveSheet()->setTitle('Simple');
		$spreadsheet->setActiveSheetIndex(0);

		  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		  header('Content-Disposition: attachment;filename="' . $documento.'.xls"');
		  header('Cache-Control: max-age=0');
		  header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');
    }
}
