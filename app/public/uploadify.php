<?php

	session_start();
	
	require_once("upload_errors.php");
	
	$targetFolder = '/diseno/app/public/img/uploadify/tmp'; // Relative to the root
	
	$verifyToken = md5('unique_salt' . $_POST['timestamp']);

	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
	
	if(!file_exists($targetPath."/".$_POST["pedido"])){
		mkdir($targetPath."/".$_POST["pedido"]);
	}
	
	$targetPath = $targetPath."/".$_POST["pedido"];
	
	$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];
	$filename = $_FILES['Filedata']['name']; 
	
	// Validate the file type
	$fileTypes = array('jpg','jpeg','gif','png','tiff','pdf','psd','ai','cdr'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	
	$directorio = rtrim($targetPath,'/') . '/';
		
	$dir = opendir($directorio); 
			
	$n = -2;
			
	while ($archivo = readdir($dir)){
		$n++;
	}
	
	if (in_array($fileParts['extension'],$fileTypes)) {
		
		if($_FILES['Filedata']["size"]<150000){
			echo imprimirError($filename, "ERROR_RESOLUCION");
			exit;
		}
		
		//MAS VALIDACIONES DEL DISEÑO
		
		move_uploaded_file($tempFile,$targetFile);
		
		$n++;
		
		echo imprimirMensaje($filename,strtoupper($fileParts['extension']),$n);
		exit;
		
	} else {
		echo imprimirError($filename, strtoupper($fileParts['extension']),$n);
		exit;
	}
?>