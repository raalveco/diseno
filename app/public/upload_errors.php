<?php
	function imprimirError($filename, $error = false, $n = 0){
		switch($error){
			case "RESOLUCION": $mensaje = "El dise�o tiene una resoluci�n muy baja y no se ver� bien en la impresi�n. Por favor env�anos un archivo con al menos 150 DPI de resoluci�n."; break;
			
			case "XLS": case "XLSX": case "XLSM": $mensaje = "No se puede imprimir folletos desde una hoja de Excel. Por favor p�selo a un formato de dise�o (Photoshop, Corel Draw, Illustrator, JPG...). Si no sabe c�mo hacerlo no se preocupe, tenemos nuestro propio estudio de dise�o gr�fico que se encargar� de re-dise�arlo en uno de los programas de dise�o permitidos. P�ngase en contacto con su Asesora de impresi�n para solicitarlo."; break;
			case "DOC": case "DOCX": $mensaje = "No se puede imprimir folletos desde un documento Word. Por favor p�selo a un formato de dise�o (Photoshop, Corel Draw, Illustrator, JPG...). Si no sabe c�mo hacerlo no se preocupe, tenemos nuestro propio estudio de dise�o gr�fico que se encargar� de re-dise�arlo en uno de los programas de dise�o permitidos. P�ngase en contacto con su Asesora de impresi�n para solicitarlo."; break;
			case "PPT": case "PPTX": $mensaje = "Archivo NO Soportado (Power Point)"; break;
			case "ODT": $mensaje = "Archivo NO Soportado (Open Office)"; break;
			case "TXT": case "XML": case "HTML": case $mensaje = "Archivo NO Soportado (Archivo de Texto Plano)"; break;
			
			default: $mensaje = "El Formato del archivo es inv�lido."; break;
		}
		
		$json = array("status" => "fail","file" => $filename,"numero" => $n,"msg" => utf8_encode("<b>".$filename."</b><br>".$mensaje));
		return json_encode($json);
	}
	
	function imprimirMensaje($filename, $mensaje = false, $n = 0){
		switch($mensaje){
			case "PSD": $mensaje = "<ul><li>�Las medidas del dise�o son iguales a las del folleto que vas a imprimir?</li><li>�Las capas est�n acopladas?</li><li>�Las fotos tienen una resoluci�n de 150 DPI como m�nimo?</li></ul>"; break;
			case "PDF": case "CDR": case "AI": $mensaje = "<ul><li>�Las medidas del dise�o son iguales a las del folleto que vas a imprimir?</li><li>�Los textos est�n convertidos a curvas?</li><li>�Las fotos tienen una resoluci�n de 150 DPI como m�nimo?</li></ul>"; break;
			case "JPG": case "JPEG": case "TIFF": case "PNG": $mensaje = "<ul><li>�Las medidas del dise�o son iguales a las del folleto que vas a imprimir?</li><li>�Las fotos tienen una resoluci�n de 150 DPI como m�nimo?</li></ul>"; break;
			
			default: $mensaje = "<ul><li>�Las medidas del dise�o son iguales a las del folleto que vas a imprimir?</li><li>�Los textos est�n convertidos a curvas?</li><li>�Las fotos tienen una resoluci�n de 150 DPI como m�nimo?</li></ul>"; break;
		}	
		
		$json = array("status" => "ok","numero" => $n,"file" => $filename,"msg" => utf8_encode('<div class="container" style="padding: 12px; max-width: 520px;"><b style="font-size: 18px;">Archivo: '.$filename.'</b><br>'.$mensaje.'</div>'));
		return json_encode($json);
	}
?>