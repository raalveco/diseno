<?php
    class BootstrapController extends ApplicationController{
        public function index() {
            $this -> set_response("view");
        }
        
        public function login() {
            $this -> set_response("view");
        }
    }
?>   