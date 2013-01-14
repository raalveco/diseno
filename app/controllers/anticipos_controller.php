<?php
	class AnticiposController extends ApplicationController{
		public function registro($id_pedido, $crm_numero) {
    		if(!$id_pedido || !$crm_numero){
    			$this -> redirect("pedidos/reporte");
    		}
    		$this -> crm_numero = $crm_numero;
			$this -> id_pedido = $id_pedido;
    	}
		public function registrar($tipo) {
			Load::lib("formato");
    		$this -> render(null, null);
			
			//GUARDANDO INFORMACION EN TABLA SOLO DE INFORMACION
			$anticipo = new Anticipo();
			
			$anticipo -> pedido_id= $this -> post("id_pedido");
			$anticipo -> sucursal = $this -> post("sucursal");
			$anticipo -> importe = Formato::noDinero($this -> post("importe"));
			$anticipo -> numero_transferencia = $this -> post("numero");		
			$anticipo -> tipo = strtoupper($tipo);							
			if($tipo == "deposito"){
				$anticipo -> fecha = Formato::FechaDB($this -> post("fechaDepo"));
			}
			if($tipo == "transferencia"){
				$anticipo -> fecha = Formato::FechaDB($this -> post("fechaTrans"));
			}
			if($tipo == "efectivo"){
				$anticipo -> fecha = Formato::FechaDB($this -> post("fechaEfe"));
			}
					
			$anticipo -> guardar();
			
			//ACTUALIZANDO TABLA DE PEDIDOS
			$pedido = Pedido::consultar($anticipo -> pedido_id);
			$pedido -> anticipo = ($pedido -> anticipo) + ($anticipo -> importe);
			$pedido -> saldo = ($pedido -> total) - ($pedido -> anticipo);
			
			if(!$pedido -> saldo){
				$pedido -> saldo = "0";
			}
			
			$pedido -> guardar();
						
			//$this -> redirect("anticipos/reporte");
		    $this -> redirect("pedidos/reporte/anticipo_registrado" );
    	}
		public function reporte() {
    		$this -> anticipos = Anticipo::reporte();
			
    	}		

	}

?>
