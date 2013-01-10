<?php
    class UploaderController extends ApplicationController{
        public function index() {
        	$ov = '150865000007874027';
            
            //$pedido = Pedido::cargarCRM($ov);
            $pedido = Pedido::cargarArchivo($ov,APP_PATH."/public/files/saleorder.xml");
			
			echo $pedido -> crm_numero;
			
			$pedido -> guardarCRM();
			 
			$this -> pedido = $pedido;
        }
    }
?>