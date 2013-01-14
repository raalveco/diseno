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
			
			echo utf8_encode(Mensaje::get("INICIO_UPLOADER"));
		}
		
		public function old(){
			$this -> set_response("view");
		}
    }
?>