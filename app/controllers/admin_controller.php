<?php
	class AdminController extends ApplicationController{
		public function index($mensaje) {
			switch($mensaje){
				case "error": $this -> mensaje = "El nombre de usuario o contraseña es incorrecto, intente de nuevo."; break;
			}
    
    	}
		public function iniciar() {
			$this -> render(null, null);
			
			if(($this -> post("usuario") == ADMIN_USER) && ($this -> post("contrasena") == ADMIN_PASSWORD)){
				$this -> redirect("pedidos/pendientes" );
				
			}
			else{
				$this -> redirect("admin/index/error" );
			}
			    
    	}

	}

?>
