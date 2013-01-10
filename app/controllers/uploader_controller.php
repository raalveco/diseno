<?php
    class UploaderController extends ApplicationController{
        public function index() {
            $this -> render(null,null);
            
            $ov = '150865000007874027';
            
            $pedido = Pedido::cargarCRM($ov);
            //$pedido = Pedido::cargarArchivo($ov,APP_PATH."/public/files/saleorder.xml");
            
            echo $pedido -> crm_id;
            echo "<br>";
            echo $pedido -> crm_numero;
            echo "<br>";
            echo $pedido -> nombre;
            echo "<br>";
            echo $pedido -> anticipo_minimo;
            echo "<br>";
            echo $pedido -> total;
            echo "<br>";
            echo $pedido -> anticipo;
            echo "<br>";
            echo $pedido -> saldo;
            echo "<br>";
            echo $pedido -> diseno_grafico;
            echo "<br>";
            echo $pedido -> diseno_estado;
            echo "<br>";
            echo $pedido -> fecha_vencimiento;
            echo "<br>";
            
            $pedido -> guardarCRM();
        }
    }
?>