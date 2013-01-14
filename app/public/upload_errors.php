<?php
	function imprimirError($filename, $error = false, $n = 0){
		switch($error){
			case "RESOLUCION": $mensaje = "El diseño tiene una resolución muy baja y no se verá bien en la impresión. Por favor envíanos un archivo con al menos 150 DPI de resolución."; break;
			
			case "XLS": case "XLSX": case "XLSM": $mensaje = "No se puede imprimir folletos desde una hoja de Excel. Por favor páselo a un formato de diseño (Photoshop, Corel Draw, Illustrator, JPG...). Si no sabe cómo hacerlo no se preocupe, tenemos nuestro propio estudio de diseño gráfico que se encargará de re-diseñarlo en uno de los programas de diseño permitidos. Póngase en contacto con su Asesora de impresión para solicitarlo."; break;
			case "DOC": case "DOCX": $mensaje = "No se puede imprimir folletos desde un documento Word. Por favor páselo a un formato de diseño (Photoshop, Corel Draw, Illustrator, JPG...). Si no sabe cómo hacerlo no se preocupe, tenemos nuestro propio estudio de diseño gráfico que se encargará de re-diseñarlo en uno de los programas de diseño permitidos. Póngase en contacto con su Asesora de impresión para solicitarlo."; break;
			case "PPT": case "PPTX": $mensaje = "Archivo NO Soportado (Power Point)"; break;
			case "ODT": $mensaje = "Archivo NO Soportado (Open Office)"; break;
			case "TXT": case "XML": case "HTML": case $mensaje = "Archivo NO Soportado (Archivo de Texto Plano)"; break;
			
			default: $mensaje = "El Formato del archivo es inválido."; break;
		}
		
		$json = array("status" => "fail","file" => $filename,"numero" => $n,"msg" => utf8_encode("<b>".$filename."</b><br>".$mensaje));
		return json_encode($json);
	}
	
	function imprimirMensaje($filename, $mensaje = false, $n = 0){
		switch($mensaje){
			case "PSD": $mensaje = "<ul><li>¿Las medidas del diseño son iguales a las del folleto que vas a imprimir?</li><li>¿Las capas están acopladas?</li><li>¿Las fotos tienen una resolución de 150 DPI como mínimo?</li></ul>"; break;
			case "PDF": case "CDR": case "AI": $mensaje = "<ul><li>¿Las medidas del diseño son iguales a las del folleto que vas a imprimir?</li><li>¿Los textos están convertidos a curvas?</li><li>¿Las fotos tienen una resolución de 150 DPI como mínimo?</li></ul>"; break;
			case "JPG": case "JPEG": case "TIFF": case "PNG": $mensaje = "<ul><li>¿Las medidas del diseño son iguales a las del folleto que vas a imprimir?</li><li>¿Las fotos tienen una resolución de 150 DPI como mínimo?</li></ul>"; break;
			
			default: $mensaje = "<ul><li>¿Las medidas del diseño son iguales a las del folleto que vas a imprimir?</li><li>¿Los textos están convertidos a curvas?</li><li>¿Las fotos tienen una resolución de 150 DPI como mínimo?</li></ul>"; break;
		}	
		
		$json = array("status" => "ok","numero" => $n,"file" => $filename,"msg" => utf8_encode('<div class="container" style="padding: 12px; max-width: 520px;"><b style="font-size: 18px;">Archivo: '.$filename.'</b><br>'.$mensaje.'</div>'));
		return json_encode($json);
	}
?>