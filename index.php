<?php
// Incluir autoload de Composer
require 'vendor/autoload.php';
require 'src/config.php';

require 'src/auth.php';
require 'src/functions.php';
// Cargar rutas después de la configuración
require 'src/routes.php';
require 'src/routes-get-mods.php';
require 'src/routes-update-mods.php';

Flight::start();


