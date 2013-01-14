<?php
	/**
	 * 
	 */
	class PedidosController extends ApplicationController {
		
		 public function reporte($mensaje) {
    		$this -> pedidos = Pedido::reporte();
			
			switch($mensaje){
				case "eliminado": $this -> mensaje = "El pedido ha sido eliminado correctamente."; break;
				case "registrado": $this -> mensaje = "El pedido ha sido registrado correctamente."; break;
				case "anticipo_registrado": $this -> mensaje = "La seña ha sido registrada correctamente."; break;
			}
    	 }
		 public function consulta($id, $mensaje) {
    		$this -> pedido = Pedido::consultar($id);
			
			switch($mensaje){
				case "modificado": $this -> mensaje = "El pedido ha sido modificado correctamente."; 
			}
    	}
		 public function modificar() {
			$this -> render(null,null);
			
    		$pedido = Pedido::consultar($this -> post("id"));
			
			$pedido -> crm_numero = $this -> post("crm_numero");
			$pedido -> nombre = $this -> post("nombre");
			$pedido -> total = $this -> post("total");
			$pedido -> anticipo = $this -> post("anticipo");
			$pedido -> saldo = $this -> post("saldo");
			
			$pedido -> guardar();
			
			$this -> redirect("pedidos/consulta/".$pedido -> id."/modificado");
    	}
		public function eliminar($id) {
    		$this -> render(null,null);
			
			$pedido = Pedido::consultar($id);
			
			$pedido -> eliminar();
			
			$this -> redirect("pedidos/reporte/eliminado");
    	}
	}	

?>


