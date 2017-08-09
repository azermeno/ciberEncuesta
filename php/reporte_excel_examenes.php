<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Mexico_City');

require_once 'configMySQL.php';

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

$conn = new mysqli($mysql_config['host'], $mysql_config['user'], $mysql_config['pass'], $mysql_config['db']);
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} 
$conn->set_charset("utf8");

/** Include PHPExcel */
require_once dirname(__FILE__) . '/../PHPExcel-1.8/Classes/PHPExcel.php';

		$nombre = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
		$examen = isset($_POST['filtro']) ? $conn->real_escape_string($_POST['filtro']) : '';
		$condicion = '';
		if($nombre != ''){
						
			$condicion = $examen == 0 ? ' && a.Nombre LIKE "%'.$nombre.'%" ' : ' && p.puesto LIKE "%'.$nombre.'%" ';
		} 
			
		$returnJs = array();
		$aspirantes = array();
		$temporal = array();

		$sql = "SELECT a.pk_aspirante,a.Nombre,a.email,a.tiempo_inicio,p.puesto,p.pk_puesto ".
		"FROM aspirante as a,puesto as p ".
		"WHERE a.fk_puesto=p.pk_puesto ".$condicion." && (Nombre <> '' || email <> '') ORDER BY a.pk_aspirante DESC";

		$result = $conn->query($sql);

    if($resultado->num_rows > 0 ){



		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
									 ->setLastModifiedBy("Maarten Balliauw")
									 ->setTitle("Office 2007 XLSX Test Document")
									 ->setSubject("Office 2007 XLSX Test Document")
									 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test result file");


	
		// output data of each row
			while($row = $result->fetch_assoc()) {
				$aspirantes[]=$row;
			}
				
			foreach($aspirantes as $aspirante){
				
				$temporal = array();
				$sql = "SELECT a.area,a.pk_area ,COUNT(p.pk_pregunta) as total FROM area as a,pregunta as p WHERE  a.fk_puesto={$aspirante['pk_puesto']} && p.fk_area=a.pk_area GROUP by a.area; ";
				$result = $conn->query($sql);
				
				
				if ($result->num_rows > 0) {
					// output data of each row
							while($row = $result->fetch_assoc()) {
								
								$sql = "SELECT count(r.pk_respuesta)  as correctas FROM contestado as c, respuesta as r, pregunta as p WHERE p.fk_area={$row['pk_area']} && r.fk_pregunta=p.pk_pregunta && r.correcta=1 && c.fk_respuesta=r.pk_respuesta && c.fk_aspirante={$aspirante['pk_aspirante']}";
								
								$result1 = $conn->query($sql);
								if ($result1->num_rows > 0) {
								// output data of each row
									while($row1 = $result1->fetch_assoc()) {
										
										$temporal[]= array_merge($row,$row1);
									}
								} else {
									
									$temporal[]= array_merge($row,array($row['area'] =>'0'));
								}
								
							}
							$returnJs[]= array_merge(array('aspirante'=>$aspirante),array('areas'=>$temporal));
				}
				
			}
	
		// Add some data
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'Hello')
					->setCellValue('B2', 'world!')
					->setCellValue('C1', 'Hello')
					->setCellValue('D2', 'world!');

		// Miscellaneous glyphs, UTF-8
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A4', 'Miscellaneous glyphs')
					->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');



		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		while ($fila = $resultado->fetch_array()) {
			$objPHPExcel->setActiveSheetIndex(0)
        		    ->setCellValue('A'.$i,  "abraham")
		            ->setCellValue('B'.$i,  "15/11/1984")
        		    ->setCellValue('C'.$i,  "M")
            		->setCellValue('D'.$i, utf8_encode("Ing computación"));
					
		}
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Simple');

	} else {
	  
	  
	  
	} 
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="01simple.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
} else {
		print_r('No hay resultados para mostrar');
	}
