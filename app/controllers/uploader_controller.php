<?php
    class UploaderController extends ApplicationController{
        public function index($ov_cifrada) {
        	$pedido = Pedido::consultar("crm_cifrado = '".$ov_cifrada."'");
			
			$this -> titulo = "ACCESO RESTRINGIDO";
			$this -> mensaje = "LA URL INGRESADA NO CORRESPONDE A UNA ORDEN DE VENTA VÁLIDA.";
			
			if($pedido){
				Session::set("pedido",$pedido -> id);
				
				if($pedido -> diseno_estado){
					$this -> titulo = "YA TENEMOS ARCHIVOS";
					$this -> mensaje = "YA TENEMOS ARCHIVOS PARA ESTA ORDEN DE VENTA, CONTACTE A SU ASESOR.";
				}
				else{
					$this -> render(null,null);
					$this -> redirect("uploader/inicio/pp");
					return;	
				}
			}
        }
		
		public function inicio($tipo){
			$pedido = Pedido::consultar(Session::get("pedido"));
			
			$directorio = APP_PATH."public/img/uploadify/tmp/".$pedido -> crm_numero;
			
			if(file_exists($directorio)){
				$dir = opendir($directorio); 
				
				while ($archivo = readdir($dir)){
					@unlink($directorio."/".$archivo);
				}
				
				@rmdir($directorio);
				
				@closedir($dir);
			}
			
			$this -> pedido = $pedido;
			$this -> tipo_folleto = $tipo;
		}
		
		public function revisar($ov){
			$this -> render(null,null);
			
			$directorio = APP_PATH."public/img/uploadify/tmp/".$ov;
			
			if(file_exists($directorio)){
				$dir = opendir($directorio); 
				
				while ($archivo = readdir($dir)){
					@unlink($directorio."/".$archivo);
				}
				
				@rmdir($directorio);
				
				@closedir($dir);
			}
			
			Load::lib("mensajes");
			
			echo utf8_encode(Mensajes::consultar("INICIO_UPLOADER",array("PRUEBA" => "Ramiro Vera")));
		}
		
		public function cargar(){
			$this -> render(null,null);
			
			$pedido = Pedido::consultar($this -> post("pedido"));
			
			echo $this -> post("pedido")."<br>";
			echo $this -> post("caras")."<br>";
			
			echo $this -> post("comentarios")."<br>";
			echo $this -> post("ordenventa")."<br>";
			echo $this -> post("archivo")."<br>";
			
			Load::lib("pclzip");
			
			$nombre = $pedido -> crm_numero.".zip";
			
			$directorio = substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/"))."/files/uploads/originales/";
			$url = $directorio.$nombre;
			
			//$directorio = APP_PATH."public/files/uploads/originales/";
			//$url = $directorio.$nombre;
			
			echo $directorio."<br>";
			echo $url."<br>";
			
			if(file_exists($url)){
				unlink($url);
			}
			
			if(file_exists(substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/"))."/img/uploadify/tmp/".$pedido -> crm_numero."/")){
				$zip = new PclZip($nombre);
			
	  			if ($zip->create(substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/"))."/img/uploadify/tmp/".$pedido -> crm_numero."/") == 0) {
	    			die('Error : '.$zip->errorInfo(true));
	  			}
				
				print_r($zip);
				
				$dir = opendir(substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/"))."/img/uploadify/tmp/".$pedido -> crm_numero."/"); 
				
				echo $dir."<br>";
				
				while ($archivo = readdir($dir)){
					if($archivo == "." || $archivo == "..") continue;
					
					$zip -> add(substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/"))."/img/uploadify/tmp/".$pedido -> crm_numero."/".$archivo,PCLZIP_OPT_REMOVE_ALL_PATH);
					echo substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/"))."/img/uploadify/tmp/".$pedido -> crm_numero."/".$archivo."<br>";
				}
				
				print_r($zip);
			}
			
			header ("Content-Disposition: attachment; filename=".$nombre."\n\n"); 
			header ("Content-Type: application/octet-stream");
			header ("Content-Length: ".filesize($url));
			readfile($url);
		}
		
		public function error($archivo, $mensaje){
			$this -> render(null,null);
			
			Load::lib("mensajes");
			
			echo utf8_encode(Mensajes::consultar($mensaje,array("ARCHIVO" => $archivo)));
		}
    }
?>