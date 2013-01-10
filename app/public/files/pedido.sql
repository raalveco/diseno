CREATE TABLE IF NOT EXISTS `pedido` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `crm_id` varchar(50) NOT NULL,
  `crm_numero` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `anticipo_minimo` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `anticipo` decimal(10,2) NOT NULL,
  `saldo` decimal(10,2) NOT NULL,
  `diseno_grafico` varchar(100) NOT NULL,
  `diseno_estado` varchar(100) NOT NULL,
  `diseno_detalle` varchar(100) NOT NULL,
  `diseno_tipo` varchar(100) NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
