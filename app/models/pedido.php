<?php
    class Pedido extends ActiveRecord{
        var $xml;
        
        public static function cargar($ov, $xml){
            if(Pedido::existe("crm_id = '".$ov."'")){
                $pedido = Pedido::consultar("crm_id = '".$ov."'");
            }
            else{
                $pedido = new Pedido();
            }
            
            $pedido -> xml = $xml;
            
            $xml = simplexml_load_string($xml,'SimpleXMLElement', LIBXML_NOCDATA);
            
            $tmp = $xml -> result -> SalesOrders -> row;
            
            foreach($tmp -> FL as $fl){
                if($fl[0] == "null") $fl[0] = "";
                
                if($fl["val"]==utf8_encode("SALESORDERID")){
                    $pedido -> crm_id = $fl[0];
                }
                
                if($fl["val"]==utf8_encode("SO Number")){
                    $pedido -> crm_numero = $fl[0];
                }
                
                if($fl["val"]==utf8_encode("Due Date")){
                    $pedido -> fecha_vencimiento = $fl[0];
                }
                
                if($fl["val"]==utf8_encode("Contact Name")){
                    $pedido -> nombre = $fl[0];
                }
                
                if($fl["val"]==utf8_encode("Anticipo necesario")){
                    $pedido -> anticipo_minimo = $fl[0];
                }
                
                if($fl["val"]==utf8_encode("Sub Total")){
                    $pedido -> total = $fl[0];
                }
                
                if($fl["val"]==utf8_encode("Adjustment")){
                    $pedido -> anticipo = $fl[0];
                }
                
                if($fl["val"]==utf8_encode("Grand Total")){
                    $pedido -> saldo = $fl[0];
                }
                
                if($fl["val"]==utf8_encode("Diseño gráfico")){
                    $pedido -> diseno_grafico = $fl[0];
                }
                
                if($fl["val"]==utf8_encode("Diseño gráfico (estado)")){
                    $pedido -> diseno_estado = $fl[0];
                }
                
                if($fl["val"]==utf8_encode("Diseño gráfico (detalle)")){
                    $pedido -> diseno_detalle = $fl[0];
                }
                
                if($fl["val"]==utf8_encode("Diseño gráfico (tipo)")){
                    $pedido -> diseno_tipo = $fl[0];
                }
				
				$pedido -> correo = "lizaolaa@gmail.com";
				$pedido -> crm_cifrado = sha1($pedido -> crm_numero);
				
				$pedido -> guardar();
            }
            
            return $pedido;
        }

        public static function cargarArchivo($ov, $file){
            $xml = file_get_contents($file);
            
            return Pedido::cargar($ov, $xml);
        }
        
        public static function cargarCRM($ov){
            $xml_response = simplexml_load_file("https://crm.zoho.com/crm/private/xml/SalesOrders/getRecordById?authtoken=".CRM_TOKEN."&newFormat=2&scope=crmapi&id=".$ov,'SimpleXMLElement', LIBXML_NOCDATA);
            $xml = $xml_response -> asXML();
            
            return Pedido::cargar($ov, $xml);
        }
        
        public function guardarCRM(){
            $this -> guardar();
            
            $xml = "<SalesOrders>
                <row no='1'>
                    <FL val='SALESORDERID'>".$this -> crm_id."</FL>
                    <FL val='SO Number'>".$this -> crm_numero."</FL>
                    <FL val='Due Date'>".$this -> fecha_vencimiento."</FL>
                    <FL val='Contact Name'>".$this -> nombre."</FL>
                    <FL val='Anticipo necesario'>".$this -> anticipo_minimo."</FL>
                    <FL val='Sub Total'>".$this -> total."</FL>
                    <FL val='Adjustment'>".$this -> anticipo."</FL>
                    <FL val='Grand Total'>".$this -> saldo."</FL>
                    <FL val='Diseño gráfico'>".$this -> diseno_grafico."</FL>
                    <FL val='Diseño gráfico (estado)'>".$this -> diseno_estado."</FL>
                    <FL val='Diseño gráfico (detalle)'>".$this -> diseno_detalle."</FL>
                    <FL val='Diseño gráfico (tipo)'>".$this -> diseno_tipo."</FL>
                </row>
            </SalesOrders>";
            
            $xml = simplexml_load_file('https://crm.zoho.com/crm/private/xml/SalesOrders/updateRecords?authtoken='.CRM_TOKEN.'&scope=crmapi&newFormat=1&id='.$this -> crm_id.'&xmlData='.$xml,'SimpleXMLElement', LIBXML_NOCDATA);
        }
    }
?>