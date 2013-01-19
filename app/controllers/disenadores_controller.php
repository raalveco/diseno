<?php
	class DisenadoresController extends ApplicationController{
		public function reporte($mensaje) {
    		$this -> disenadores = Disenador::reporte();
			
			switch($mensaje){
				case "eliminado": $this -> mensaje = "El Diseñador ha sido eliminado correctamente."; break;
				case "registrado": $this -> mensaje = "El Diseñador ha sido registrado correctamente."; break;
			}
    	}
		
		public function registro() {
    
    	}
		
		public function registrar() {
    		$this -> render(null,null);
			
			$disenador = new Disenador();
			
			$disenador -> nombre = $this -> post("nombre");
			$disenador -> correo = $this -> post("correo");
			$disenador -> usuario = $this -> post("usuario");
			$disenador -> password = sha1($this -> post("password"));
			
			$disenador -> guardar();
			
			$this -> redirect("disenadores/reporte/registrado");
    	}
		
		public function consulta($id, $mensaje) {
    		$this -> disenador = Disenador::consultar($id);
			
			switch($mensaje){
				case "modificado": $this -> mensaje = "El Diseñador ha sido modificado correctamente."; 
			}
    	}
		
		public function modificar() {
			$this -> render(null,null);
			
    		$disenador = Disenador::consultar($this -> post("id"));
			
			$disenador -> nombre = $this -> post("nombre");
			$disenador -> correo = $this -> post("correo");
			$disenador -> usuario = $this -> post("usuario");
			$disenador -> password = $this -> post("password") != "**********" ? sha1($this -> post("password")) : $disenador -> password;
			$disenador -> activo = $this -> post("activo");
			
			$disenador -> guardar();
			
			$this -> redirect("disenadores/consulta/".$disenador -> id."/modificado");
    	}
		
		public function eliminar($id) {
    		$this -> render(null,null);
			
			$disenador = Disenador::consultar($id);
			
			$disenador -> eliminar();
			
			$this -> redirect("disenadores/reporte/eliminado");
    	}
		
		public function trabajos(){
			if(!Session::get("ACCESO")){
				$this -> redirect("disenadores/index/invalido");
			}
			
			if(Pedido::existe("estado = 'ASIGNADO' AND disenador_id = ".Session::get("id_usuario"))){
				$this -> redirect("disenadores/pendientes");
			}
			
			$this -> pedidos = Pedido::reporte("estado != 'INACTIVO' AND estado != 'TERMINADO' AND estado != 'NUEVO'");
		}
		
		public function pendientes(){
			if(!Session::get("ACCESO")){
				$this -> redirect("disenadores/index/invalido");
			}
			
			if(!Pedido::existe("estado = 'ASIGNADO' AND disenador_id = ".Session::get("id_usuario"))){
				$this -> redirect("disenadores/trabajos");
			}
			
			$this -> pedidos = Pedido::reporte("estado = 'ASIGNADO' AND disenador_id = ".Session::get("id_usuario"));
		}
		
		public function index($mensaje){
			Session::set("id_usuario", "");
			Session::set("usuario", "");
			Session::set("tipo","");
			Session::set("ACCESO", false);
			
			switch($mensaje){
				case "error": $this -> mensaje = "El Nombre de Usuario y/o Contraseña son incorrectos, Intente de nuevo."; break;
				case "invalido": $this -> mensaje = "Acceso Inválido, Por favor Inicie Sesión para poder ingresar a la sección para diseñadores."; break;
			}
		}
		
		public function ingresar() {
			$this -> render(null, null);
			
			if(Disenador::existe("usuario = '".$this -> post("usuario")."' AND password = '".sha1($this -> post("password"))."'")){
				$disenador = Disenador::consultar("usuario = '".$this -> post("usuario")."' AND password = '".sha1($this -> post("password"))."'");
				
				Session::set("usuario", $this -> post("usuario"));
				Session::set("tipo","DISEÑADOR");
				Session::set("ACCESO",true);
				Session::set("id_usuario", $disenador -> id);
				
				if(Pedido::existe("estado = 'ASIGNADO' AND disenador_id = ".Session::get("id_usuario"))){
					$this -> redirect("disenadores/pendientes");
				}
				else{
					$this -> redirect("disenadores/trabajos");
				}
			}
			else{
				Session::set("usuario", "");
				Session::set("tipo","");
				Session::set("ACCESO", false);
				
				$this -> redirect("disenadores/index/error");
			}   
    	}
    	
    	public function solicitar($id) {
			$this -> render(null, null);
			
			$pedido = Pedido::consultar($id);
			
			$pedido -> disenador_id = Session::get("id_usuario");
			$pedido -> estado = "ASIGNADO";
			
			$pedido -> guardar();
			
			$this -> redirect("disenadores/pendientes");
		}
		
		public function descargar($ov_cifrada){
			$this -> render(null, null);
			
			$pedido = Pedido::consultar("crm_cifrado = '".$ov_cifrada."'");
			
			$pedido -> descargado = $pedido -> descargado + 1;
			$pedido -> guardar(); 
			
			$download = APLICACION_URL ."files/repositorios/originales/".$pedido -> originales;
			
			header("location: ".$download);
			
			return;
		}
		
		public function trabajo($ov_cifrada, $tipo){
			$this -> render(null, null);
			
			$pedido = Pedido::consultar("crm_cifrado = '".$ov_cifrada."'");
			
			if($tipo == "OK"){
				$this -> redirect("disenadores/disenar/".$ov_cifrada."");
			}
			else{
				$this -> redirect("disenadores/rechazar/".$ov_cifrada."");
			}
		}
		
		public function cerrar() {
			$this -> render(null, null);
			
			Session::set("usuario", "");
			Session::set("tipo","");
			Session::set("ACCESO", false);
			
			$this -> redirect("disenadores/index");
		}
	}
?>