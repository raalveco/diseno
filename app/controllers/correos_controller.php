<?php
	class CorreosController extends ApplicationController{
		public function reporte($mensaje){
			$this -> correos = Correo::reporte();
			
			switch($mensaje){
				case "eliminado": $this -> mensaje = "El correo ha sido eliminado correctamente."; break;
				case "registrado": $this -> mensaje = "El correo ha sido guardado correctamente."; break;
				case "modificado": $this -> mensaje = "El correo ha sido modificado correctamente."; break;
			}
		}
		
		public function consulta($id, $mensaje) {
    		$this -> correo = Correo::consultar($id);
			
			switch($mensaje){
				case "modificado": $this -> mensaje = "El correo ha sido modificado correctamente."; 
			}
    	}
		
		public function modificar() {
			$this -> render(null,null);
			
    		$correo = Correo::consultar($this -> post("id"));
			
			$correo -> remitente = $this -> post("remitente");
			$correo -> asunto = $this -> post("asunto");
			$correo -> mensaje = $this -> post("mensaje");			
			
			$correo -> guardar();
			
			$this -> redirect("correos/reporte/modificado");
    	}
	}
?>
