<?php
	function imprimirError($filename, $error = "ERROR_DESCONOCIDO", $n = 0){
		
		switch($error){
			case "ERROR_RESOLUCION": $error = "ERROR_RESOLUCION"; break;
			
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
			case "PSD": $mensaje = "ARCHIVO_PSD"; break;
			case "PDF": case "CDR": case "AI": $mensaje = "ARCHIVO_CURVAS"; break;
			case "JPG": case "JPEG": case "TIFF": case "PNG": $mensaje = "ARCHIVO_PNG"; break;
			
			default: $mensaje = "ARCHIVO_IMAGEN"; break;
		}	
		
		$json = array("status" => "ok","numero" => $n,"file" => str_replace(" ", "_", $filename),"msg" => $mensaje);
		return json_encode($json);
	}
?>