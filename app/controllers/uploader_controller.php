<?php
    class UploaderController extends ApplicationController{
        public function index($ov_cifrada) {
        	$pedido = Pedido::consultar("crm_cifrado = '".$ov_cifrada."'");
			
			Load::lib("mensajes");
			
			if($pedido){
				Session::set("pedido",$pedido -> id);
				
				if($pedido -> tipo_diseno == "DA"){
					$this -> mensaje = Mensajes::consultar("PEDIDO_ES_DA");
				}
				else{
					$this -> render(null,null);
					$this -> redirect("uploader/inicio/pp");
					return;	
				}
			}
			else{
				$this -> mensaje = Mensajes::consultar("ORDEN_INVALIDA");
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
			
			$pedido -> originales = $nombre2;
			$pedido -> descargado = 0;
			$pedido -> guardar(); 
            
            if(strtoupper($this -> post("tipo_folleto"))=="DA"){
                rename($directorio."/".$nombre,$directorio."/files/repositorios/listos/".$nombre2);
				$repositorio = APLICACION_URL."files/repositorios/listos/".$nombre2;
            }
            else{
                rename($directorio."/".$nombre,$directorio."/files/repositorios/originales/".$nombre2);
                $repositorio = APLICACION_URL."files/repositorios/originales/".$nombre2;
            }
            
            if(file_exists(substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/"))."/img/uploadify/tmp/".$pedido -> crm_numero."/")){
                rmdir(substr($_SERVER["SCRIPT_FILENAME"],0,strrpos($_SERVER["SCRIPT_FILENAME"],"/"))."/img/uploadify/tmp/".$pedido -> crm_numero."/");
            }
			
			Load::lib("mensajes");
			
			switch($this -> post("caras")){
				case 2: $caras = "Dos caras distintas."; break;
				case 1: $caras = "1 Cara, imprimir lo mismo de los dos lados."; break;
				default: $caras = "1 Cara, dejar una Cara en blanco.";
			}
			
			$correo = Mensajes::correo("CORREO_CARGADO",array("CONTACTO" => $pedido -> nombre, "REPOSITORIO" => $repositorio, "PEDIDO" => $pedido -> crm_numero, "CARAS" => $caras, "COMENTARIOS" => $this -> post("comentarios")));
			$correo -> enviarCorreo("raalveco@gmail.com");
			
			$pedido -> diseno_grafico = "Cliente Envia";
			$pedido -> diseno_estado = "Archivo Recibido";
			
			if($pedido -> anticipo >= $pedido -> anticipo_minimo){
				if(strtoupper($this -> post("tipo_folleto"))!="DA"){
					$pedido -> estado = "DISPONIBLE";
				}
				
				$this -> mensaje = Mensajes::consultar("MENSAJE_CARGADO_SENADO",array("CONTACTO" => $pedido -> nombre, "REPOSITORIO" => $repositorio, "PEDIDO" => $pedido -> crm_numero, "CARAS" => $caras, "COMENTARIOS" => $this -> post("comentarios")));
			}
			else{
				
				$url = APLICACION_URL."pedidos/anticipo/".$pedido -> crm_cifrado;
				
				$boton = '<a href="'.$url.'" id="boton" class="btn btn-success">Registrar Señar</a>';
				
				$this -> mensaje = Mensajes::consultar("MENSAJE_CARGADO_NOSENADO",array("CONTACTO" => $pedido -> nombre, "REPOSITORIO" => $repositorio, "PEDIDO" => $pedido -> crm_numero, "CARAS" => $caras, "COMENTARIOS" => $this -> post("comentarios"),"BOTON_SENAR" => $boton));
			
				$correo = Mensajes::correo("CORREO_CARGADO_NOSENADO",array("CONTACTO" => $pedido -> nombre, "REPOSITORIO" => $repositorio, "PEDIDO" => $pedido -> crm_numero, "CARAS" => $caras, "COMENTARIOS" => $this -> post("comentarios"),"URL" => $url));
				$correo -> enviarCorreo("raalveco@gmail.com");
			}	
			
			$pedido -> guardarCRM();
		}
		
		public function error($archivo, $mensaje){
			$this -> render(null,null);
			
			Load::lib("mensajes");
			echo utf8_encode(Mensajes::consultar($mensaje,array("ARCHIVO" => $archivo)));
		}
		
		public function archivo($archivo, $mensaje){
			$this -> render(null,null);
			
			Load::lib("mensajes");
			echo utf8_encode(Mensajes::consultar($mensaje,array("ARCHIVO" => $archivo)));
		}
    }
?>