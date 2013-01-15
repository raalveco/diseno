<?php
    class ConfiguracionController extends ApplicationController{
        public function mensajes() {
            
        }
        
        public function mensaje($id){
            $this -> mensaje = Mensaje::consultar($id);
        }
        
        public function correos() {
            
        }
        
        public function correo($id){
            $this -> mensaje = Mensaje::consultar($id);
        }
		
		public function pedidos(){
			$this -> render(null,null);
			
			//$xml_response = simplexml_load_file("https://crm.zoho.com/crm/private/xml/Contacts/getRecords?authtoken=".CRM_TOKEN."&scope=crmapi&newFormat=2&fromIndex=1&toIndex=100&sortColumnString=Created Time&sortOrderString=desc&selectColumns=All");
			//$xml = $xml_response -> asXML();
			
			//$xml = file_get_contents(APP_PATH."/public/files/pedidos.xml");
			
			//Pedido::cargarPedidos($xml);
			
			//header('Content-Type: text/xml');
		    //$xml = simplexml_load_file("https://crm.zoho.com/crm/private/xml/Invoices/getRecordById?authtoken=".$authtoken."&scope=crmapi&newFormat=2&selectColumns=All&id=".$_GET['invid'],'SimpleXMLElement', LIBXML_NOCDATA);
		    //echo $xml -> asXML();
		    //exit();
		    
		    Pedido::cargarCRM("150865000007874027");
		}
    }
?>