<?php
	function imprimirError($filename, $error = "ERROR_DESCONOCIDO", $n = 0){
		
		switch($error){
			case "RESOLUCION": $error = "ERROR_RESOLUCION"; break;
			
			case "XLS": case "XLSX": case "XLSM": $error = "ERROR_XLS"; break;
			case "DOC": case "DOCX": $error = "ERROR_DOC"; break;
			case "PPT": case "PPTX": $error = "ERROR_PPT"; break;
			case "ODT": $error = "ERROR_ODT"; break;
			case "TXT": case "XML": case "HTML": $error = "ERROR_TXT"; break;
			
			default: $error = "ERROR_INVALIDO"; break;
		}
		
		$json = array("status" => "fail","file" => str_replace(" ", "_", $filename),"numero" => $n,"error" => $error);
		return json_encode($json);
	}
	
	function imprimirMensaje($filename, $mensaje = "Mensaje NO Definido", $n = 0){
		
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