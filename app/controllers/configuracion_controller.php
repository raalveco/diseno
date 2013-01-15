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
    }
?>   