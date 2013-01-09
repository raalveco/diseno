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
	}
?>