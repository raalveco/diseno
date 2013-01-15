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
			
			Load::lib("pclzip");
            $nombre = $pedido -> crm_numero.".zip";
            
			$directorio = substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/"));
			$url = $directorio."/".$nombre;
			
			if(file_exists($url)){
				unlink($url);
			}
			
			if(file_exists(substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/"))."/img/uploadify/tmp/".$pedido -> crm_numero."/")){
				$zip = new PclZip($nombre);
			
                $zip -> create(".");
            
	  			$dir = opendir(substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/"))."/img/uploadify/tmp/".$pedido -> crm_numero."/"); 
				
				while ($archivo = readdir($dir)){
					if($archivo == "." || $archivo == "..") continue;
					
					$zip -> add(substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/"))."/img/uploadify/tmp/".$pedido -> crm_numero."/".$archivo,PCLZIP_OPT_REMOVE_ALL_PATH);
                    
                    unlink(substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/"))."/img/uploadify/tmp/".$pedido -> crm_numero."/".$archivo);
                }
			}
            
            if(!file_exists($directorio."/files/repositorios/")){
                mkdir($directorio."/files/repositorios/");
                mkdir($directorio."/files/repositorios/originales/");
                mkdir($directorio."/files/repositorios/listos/");
            }
            
            if(!file_exists($directorio."/files/repositorios/originales/")){
                mkdir($directorio."/files/repositorios/originales/");
            }
            
            if(!file_exists($directorio."/files/repositorios/listos/")){
                mkdir($directorio."/files/repositorios/listos/");
            }
            
            if($this -> post("caras")==0){
                $nombre2 = $pedido -> crm_numero." [F].zip";
            }
            
            if($this -> post("caras")==1){
                $nombre2 = $pedido -> crm_numero." [FYF].zip";
            }
            
            if(strtoupper($this -> post("tipo_folleto"))=="DA"){
                rename($directorio."/".$nombre,$directorio."/files/repositorios/listos/".$nombre2);
            }
            else{
                rename($directorio."/".$nombre,$directorio."/files/repositorios/originales/".$nombre2);
            }
            
            if(file_exists(substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/"))."/img/uploadify/tmp/".$pedido -> crm_numero."/")){
                rmdir(substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/"))."/img/uploadify/tmp/".$pedido -> crm_numero."/");
            }
		}
		
		public function error($archivo, $mensaje){
			$this -> render(null,null);
			
			Load::lib("mensajes");
			
			echo utf8_encode(Mensajes::consultar($mensaje,array("ARCHIVO" => $archivo)));
		}
    }
?>