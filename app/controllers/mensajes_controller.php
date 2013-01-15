<?php
	class MensajesController extends ApplicationController{
		public function agregar(){
			
		}
		
		public function guardar(){
			$this -> render(null, null);
			$mensaje = new Mensaje();
			
			$mensaje -> codigo = $this -> post("codigo");
			$mensaje -> mensaje = $this -> post("mensaje");			
			$mensaje -> guardar();
			
			$this -> redirect("mensajes/agregar");
			
		}
		
		public function reporte($mensaje){
			$this -> mensajes = Mensaje::reporte();
			
			switch($mensaje){
				case "eliminado": $this -> mensaje = "El mensaje ha sido eliminado correctamente."; break;
				case "registrado": $this -> mensaje = "El mensaje ha sido guardado correctamente."; break;
				case "modificado": $this -> mensaje = "El mensaje ha sido modificado correctamente."; break;
			}
		}
		
		public function consulta($id, $mensaje) {
    		$this -> mensajes = Mensaje::consultar($id);
			
			switch($mensaje){
				case "modificado": $this -> mensaje = "El mensaje ha sido modificado correctamente."; 
			}
    	}
		
		public function eliminar($id) {
    		$this -> render(null,null);
			
			$mensajes = Mensaje::consultar($id);
			
			$mensajes -> eliminar();
			
			$this -> redirect("mensajes/reporte/eliminado");
    	}
		
		public function modificar() {
			$this -> render(null,null);
			
    		$mensaje = Mensaje::consultar($this -> post("id"));
			$mensaje -> mensaje = $this -> post("mensaje");			
			
			$mensaje -> guardar();
			
			$this -> redirect("mensajes/reporte/modificado");
    	}
		
	}
?>