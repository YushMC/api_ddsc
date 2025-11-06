<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use WebPConvert\WebPConvert;

//funciones
function esFecha($valor) {
    // Definir formatos de fecha esperados
    $formatos = ['Y-m-d', 'd/m/Y', 'Y-m-d H:i:s', 'd-m-Y', 'm/d/Y'];

    foreach ($formatos as $formato) {
        $fecha = DateTime::createFromFormat($formato, $valor);
        if ($fecha && $fecha->format($formato) === $valor) {
            return true;
        }
    }
    return false;
}
//rutas

Flight::route('POST /add-mod', function() {
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    
    Auth::init();
    date_default_timezone_set('America/Mexico_City');
    
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    $userId = $authData->data->id; // Obtener el ID del usuario desde el token
    $userRol = $authData->data->rol;
    
    if($userRol == 1){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para realizar esta acción."]));
        return;
    }
    
    // Obtener los datos de POST
    $nameMod = $_POST['name'] ?? null;
    $descriptionMod = $_POST['descripcion'] ?? null;
    $duracionMod = $_POST['duracion'] ?? null;
    $estadoMod = $_POST['estado'] ?? null;
    $enfoqueMod = $_POST['personaje'] ?? null;
    $tipoMod = $_POST['tipo'] ?? null;
    $isNSFW = $_POST['nsfw'] ?? null;
    $enlace = $_POST['pc'] ?? null;
    $enlaceAndroid = $_POST['android'] ?? null;
    $portada = $_POST['portada'] ?? null;
    $fecha = $_POST['fecha'] ?? date("Y-m-d h:m:s");
    $slug = $_POST['slug'] ?? '';

    // Extraer arrays si existen
    
    
    if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ0-9_: ]+$/', $nameMod)) {
        Flight::halt(400, json_encode(["error" => "Nombre del mod inválido"]));
        return;
    }
    // Si es válido, se sanitiza
    $nameMod = filter_var($nameMod, FILTER_SANITIZE_SPECIAL_CHARS);


    // Validar que el usuario solo contenga letras y números
   

    
     // Validar que el usuario solo contenga letras y números
    if (!preg_match('/^[0-9]$/', $duracionMod)) {
        Flight::halt(400, json_encode(["error" => "La duración del mod no es valida"]));
        return;
    }
    
    if (!preg_match('/^[0-9]$/', $estadoMod)) {
        Flight::halt(400, json_encode(["error" => "El estado del mod no es valido"]));
        return;
    }
    
    if (!preg_match('/^[0-9]$/', $enfoqueMod)) {
        Flight::halt(400, json_encode(["error" => "El enfoque del mod no es valido"]));
        return;
    }
    
    if (!preg_match('/^[0-9]$/', $tipoMod)) {
        Flight::halt(400, json_encode(["error" => "El tipo de mod no es valido"]));
        return;
    }
    
    if(empty($nameMod) || empty($descriptionMod)){
        Flight::halt(400, json_encode(["error" => "Los campos de nombre y descripcion no pueden estar vacios"]));
        return;
    }
    
    if(empty($slug)){
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', str_replace(":", "-", str_replace(",", "",str_replace(".","",str_replace(" ", "-", strtolower(trim($nameMod)))))));
    }

    try {
        // Conectar a la base de datos
        $db = Flight::db();
        
        // Verificar si el usuario ya existe
        $stmt = $db->prepare("SELECT nombre FROM mods WHERE nombre = ?");
        $stmt->execute([$nameMod]);
        if ($stmt->fetch()) {
            Flight::halt(400, json_encode(["error" => "El mod ya existe."]));
            return;
        }
        $porteador = 0;
        
        if(!isset($_POST['id_porteador'])){
            $porteador = null;
        }else{
            $porteador = $_POST['id_porteador'];
        }

        // Insertar el usuario en la base de datos
        $stmt = $db->prepare("INSERT INTO mods 
    (nombre, descripcion, id_duracion, id_estado, id_enfoque, isSelection, isNSFW, id_tipo, slug, created, visitas, isPublic, id_porteador) 
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$nameMod, $descriptionMod, $duracionMod,$estadoMod, $enfoqueMod, 0, $isNSFW, $tipoMod, $slug, $fecha,0, 1,$porteador]);

        // Responder con éxito
        // mail($email, "Tu código de verificación", "Tu código es: $codigoVerificacion");
        
        $idModConsulta = $db->prepare("SELECT id FROM mods WHERE nombre = ?");
        $idModConsulta->execute([$nameMod]);
        $idModGuardado=$idModConsulta->fetchColumn();
        
        $creadores = isset($_POST['creador']) ? json_decode($_POST['creador'], true) : [];
        if (!is_array($creadores)) {
            $creadores = []; // Evitar errores si la decodificación falla
        }
        $stmtCreador = $db->prepare("INSERT INTO modders (id_user, id_mod, nombre) VALUES (?, ?, ?)");
        foreach ($creadores as $creador) {
            $id = isset($creador['id']) ? (int) $creador['id'] : null;
            $nombre = isset($creador['nombre']) ? (string) $creador['nombre'] : null;
            
            $detectarIdUser = $db->prepare("SELECT id FROM users WHERE id = ?");
            $detectarIdUser->execute([$id]);
            if ($detectarIdUser->fetch()) {
                $stmtCreador->execute([$id, $idModGuardado, null]);
            }else{
                $nombre = filter_var($nombre, FILTER_SANITIZE_SPECIAL_CHARS);
                $stmtCreador->execute([null, $idModGuardado, $nombre]);  
            }
        }
        
        $traductores = isset($_POST['traductor']) ? json_decode($_POST['traductor'], true) : [];
        if (!is_array($traductores)) {
            $traductores = []; // Evitar errores si la decodificación falla
        }
        $stmtTraductor = $db->prepare("INSERT INTO traductores (id_user, id_mod, nombre) VALUES (?, ?, ?)");
        foreach ($traductores as $traductor) {
            $id = isset($traductor['id']) ? (int) $traductor['id'] : null;
            $nombre = isset($traductor['nombre']) ? (string) $traductor['nombre'] : null;
            $detectarIdUser = $db->prepare("SELECT id FROM users WHERE id = ?");
            $detectarIdUser->execute([$id]);
            if ($detectarIdUser->fetch()) {
                $stmtTraductor->execute([$id, $idModGuardado, null]);
            }else{
                $nombre = filter_var($nombre, FILTER_SANITIZE_SPECIAL_CHARS);
                $stmtTraductor->execute([null, $idModGuardado, $nombre]);
            }
        }
        
        $generos = isset($_POST['generos']) ? json_decode($_POST['generos'], true) : [];
        if (!is_array($generos)) {
            $generos = [];
        }

        $stmtGeneros = $db->prepare("INSERT INTO generos_mod (id_mod, id_genero) VALUES (?, ?)");

        // ahora $genero es un número, no un array
        foreach ($generos as $genero) {
            $id = (int) $genero;
            $stmtGeneros->execute([$idModGuardado, $id]);
        }
        
        if(!empty($_POST['pc'])){
            $enlacePC = filter_var($_POST['pc'], FILTER_SANITIZE_SPECIAL_CHARS);
            $stmtGeneros = $db->prepare("INSERT INTO enlaces (id_tipo,id_mod, url) VALUES (?, ?, ?)");
            $stmtGeneros->execute([2,$idModGuardado, $enlacePC]);
        }
        
        if(!empty($_POST['android'])){
            $enlaceAndroid = filter_var($_POST['android'], FILTER_SANITIZE_SPECIAL_CHARS);
            $stmtGeneros = $db->prepare("INSERT INTO enlaces (id_tipo,id_mod, url) VALUES (?, ?, ?)");
            $stmtGeneros->execute([1,$idModGuardado, $enlaceAndroid]);
        }
        $newFilePathSqlLogo = "";
        if (isset($_FILES['logo'])) {
            $fileLogo = $_FILES['logo'];
            $uploadDir = 'images/mods/'.$idModGuardado;
            
            if(!file_exists($uploadDir)){
                mkdir($uploadDir, 0777, true);
            }
            
            $logo_tmp = $_FILES['logo']['tmp_name'];
            
            $extension = pathinfo($fileLogo['name'], PATHINFO_EXTENSION);
            $newFileName = 'logo_' . uniqid() . '.' . $extension;
            $webpFilePath = "images/mods/" . $idModGuardado."/". pathinfo($newFileName, PATHINFO_FILENAME) . ".webp"; // Ruta WebP
            $newFilePathSql = "images/mods/". $idModGuardado."/". $newFileName; 
            
            if(move_uploaded_file($logo_tmp, $newFilePathSql)){
                
                if (convertirAWebP($newFilePathSql, $webpFilePath)) {
                    $db = Flight::db();
                    $stmt2 = $db->prepare("INSERT INTO imagenes_mod (id_mod, id_tipo_imagen, url) VALUES (?, ?, ?)");
                    $stmt2->execute([$idModGuardado,1, $webpFilePath]);
                 }
                 
            }
        }
        
        $sql_file = "";
        
        if (isset($_FILES['portada'])) {
            $fileLogo = $_FILES['portada'];
            $uploadDir = 'images/mods/'.$idModGuardado;
            
            if(!file_exists($uploadDir)){
                mkdir($uploadDir, 0777, true);
            }
            
            $logo_tmp = $_FILES['portada']['tmp_name'];
            
            $extension = pathinfo($fileLogo['name'], PATHINFO_EXTENSION);
            $newFileName = 'portada_' . uniqid() . '.' . $extension;
            $webpFilePath = "images/mods/" . $idModGuardado."/". pathinfo($newFileName, PATHINFO_FILENAME) . ".webp"; // Ruta WebP
            $sql_file = $webpFilePath;
            $newFilePathSql = "images/mods/". $idModGuardado."/". $newFileName; // Ruta WebP en BD
            
            if(move_uploaded_file($logo_tmp, $newFilePathSql)){
                // Convertir a WebP
                
                if (convertirAWebP($newFilePathSql, $webpFilePath)) {
                $db = Flight::db();
                $stmt2 = $db->prepare("INSERT INTO imagenes_mod (id_mod, id_tipo_imagen, url) VALUES (?, ?, ?)");
                $stmt2->execute([$idModGuardado,3, $webpFilePath]);
                }
                
            }
        }   
        
        if(isset($_FILES['capturas'])){
            $uploadDir = 'images/mods/'.$idModGuardado;
            if(!file_exists($uploadDir)){
                mkdir($uploadDir, 0777, true);
            }
            foreach ($_FILES['capturas']['tmp_name'] as $key => $tmp_name) {
                $fileName = $_FILES['capturas']['name'][$key]; // Obtener el nombre del archivo correcto
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = 'captura_' . uniqid() . '.' . $extension;
                $webpFilePath = "images/mods/" . $idModGuardado."/". pathinfo($newFileName, PATHINFO_FILENAME) . ".webp"; // Ruta WebP
                $newFilePathSql = "images/mods/". $idModGuardado."/". $newFileName; 
            
                if(move_uploaded_file($tmp_name, $newFilePathSql)){
                    
                    if (convertirAWebP($newFilePathSql, $webpFilePath)) {
                    $db = Flight::db();
                    $stmt2 = $db->prepare("INSERT INTO imagenes_mod (id_mod, id_tipo_imagen, url) VALUES (?, ?, ?)");
                    $stmt2->execute([$idModGuardado,2, $webpFilePath]);
                    }
                    
                }
            }
        }
        
        if(isset($_POST['id_saga']) && isset($_POST['tipo_en_saga'])){
            $stmtSaga = $db->prepare("INSERT INTO mods_registrados_sagas (id_mod, id_saga, tipo) VALUES (?, ?, ?)");
            $stmtSaga->execute([$idModGuardado,$_POST['id_saga'], $_POST['tipo_en_saga']]);
        }
        
        $stmtSaga = $db->prepare("INSERT INTO updates (tipo, titulo, descripcion, img, enlace) VALUES (?, ?, ?, ?, ?)");
        $stmtSaga->execute(['mod',"¡Nuevo mod añadido!","El mod {$nameMod} fue agregado a DDSC",$sql_file,"/mods/{$slug}"]);
    
        $stmt4 = $db->query("SELECT * FROM subscriptions");
        $subscriptions = $stmt4->fetchAll(PDO::FETCH_ASSOC);
    
        $vapidKeys = [
            'subject' => 'mailto:soporte@dokidokispanish.club',
            'publicKey' => 'BIuTU82pi4qBD-sNaJNezG1kGs_cA5-Qb_ZaVQ1ZCj7PxvoqBSDe9q33cZO4BmVZRL-97GAYzu18rPxf7lSw1pk',
            'privateKey' => 'rlwhiDtAYDeELyYC5fEDRtk3jIIjPTcryTsLXzKa6oQ',
        ];
    
        $webPush = new WebPush([
            'VAPID' => $vapidKeys
        ]);
        $site_url = getenv('URL_SITE');
        // Enviar notificaciones a todas las suscripciones
        foreach ($subscriptions as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub['endpoint'],
                'keys' => [
                    'p256dh' => $sub['p256dh'],
                    'auth' => $sub['auth'],
                ],
            ]);
            $webPush->queueNotification($subscription, json_encode([
                'title' => "Un nuevo mod fue agregado!",
                'body' => "El mod: ". $nameMod. " ya esta disponible en nuestro sitio web!",
                'icon' => "https://api.dokidokispanish.club/". $newFilePathSqlLogo,
                'data' => ['url' => "{$site_url}/mods/". $slug] // 🔹 Agregamos la URL
            ]));
        }
    
        foreach ($webPush->flush() as $report) {
            
        }
        
        Flight::json(["message" => "Mod registrado correctamente!"]);
    } catch (Exception $e) {
        Flight::halt(500, json_encode(["error" => "Error en el servidor: " . $e->getMessage()]));
        return;
    }
});

Flight::route('POST /add-category', function() {
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    
    Auth::init();
    date_default_timezone_set('America/Mexico_City');
    $request = json_decode(file_get_contents("php://input"), true);
    
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    $userId = $authData->data->id; // Obtener el ID del usuario desde el token
    $userRol = $authData->data->rol;
    
    if($userRol !=4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para realizar esta acción."]));
        return;
    }
    
    // Obtener los datos de POST
    $nameMod = $request['category_name'];

    // Extraer arrays si existen
    // Si es válido, se sanitiza
    
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', str_replace(":", "-", str_replace(",", "",str_replace(".","",str_replace(" ", "-", strtolower(trim($nameMod)))))));
    

    try {
        // Conectar a la base de datos
        $db = Flight::db();
        
        // Verificar si el usuario ya existe
        $stmt = $db->prepare("SELECT categoria FROM categories WHERE categoria = ?");
        $stmt->execute([$nameMod]);
        if ($stmt->fetch()) {
            Flight::halt(400, json_encode(["error" => "La categoria ya existe."]));
            return;
        }

        // Insertar el usuario en la base de datos
        $stmt = $db->prepare("INSERT INTO categories (categoria, slug, active) VALUES (?,?,?)");
        $stmt->execute([$nameMod, $slug, 1]);
        
        Flight::json(["message" => "Categoria registrada correctamente!"]);
    } catch (Exception $e) {
        Flight::halt(500, json_encode(["error" => "Error en el servidor: " . $e->getMessage()]));
        return;
    }
});

Flight::route('POST /add-mod-category/', function() {
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    
    Auth::init();
    date_default_timezone_set('America/Mexico_City');
    
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    $userRol = $authData->data->rol;
    
    if($userRol != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para realizar esta acción."]));
        return;
    }
    $data = json_decode(file_get_contents("php://input"), true);
    // Obtener los datos de POST
    $id_mod = $data['id_mod'] ?? 1;

    
    
    if (!preg_match('/^[0-9]+$/', $id_mod)) {
        Flight::halt(400, json_encode(["error" => "Id del mod inválido"]));
        return;
    }
    

    try {
        // Conectar a la base de datos
        $db = Flight::db();
        
        $categorias =$data['categorias'] ?? [];
        if (!is_array($categorias)) {
            $categorias = [];
        }

        $stmt1 = $db->prepare("INSERT INTO mod_category (id_mod, id_category, active) VALUES (?,?,?)");

        // ahora $genero es un número, no un array
        foreach ($categorias as $categoria) {
            $id = (int) $categoria;
            $stmt = $db->prepare("SELECT * FROM mod_category WHERE id_mod = ? AND id_category = ?");
            $stmt->execute([$id_mod, $id]);
            if ($stmt->fetch()) {
                Flight::halt(400, json_encode(["error" => "El mod ya existe con esta categoria."]));
                return;
            }else{
                $stmt1->execute([$id_mod, $id, 1]);
            }
            // Insertar el usuario en la base de datos
        }
        // Verificar si el usuario ya existe
        
        Flight::json(["message" => "Categorias registradas correctamente!"]);
    } catch (Exception $e) {
        Flight::halt(500, json_encode(["error" => "Error en el servidor: " . $e->getMessage()]));
        return;
    }
});

Flight::route('POST /add-saga', function() {
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    
    Auth::init();
    date_default_timezone_set('America/Mexico_City');
    $request = json_decode(file_get_contents("php://input"), true);
    
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    $userId = $authData->data->id; // Obtener el ID del usuario desde el token
    $userRol = $authData->data->rol;
    
    if($userRol !=4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para realizar esta acción."]));
        return;
    }
    
    // Obtener los datos de POST
    $nameMod = $request['saga_name'];
    // Extraer arrays si existen
    // Si es válido, se sanitiza
    
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', str_replace(":", "-", str_replace(",", "",str_replace(".","",str_replace(" ", "-", strtolower(trim($nameMod)))))));
    

    try {
        // Conectar a la base de datos
        $db = Flight::db();
        
        // Verificar si el usuario ya existe
        $stmt = $db->prepare("SELECT * FROM todas_las_sagas WHERE titulo = ?");
        $stmt->execute([$nameMod]);
        if ($stmt->fetch()) {
            Flight::halt(400, json_encode(["error" => "La saga ya existe."]));
            return;
        }

        // Insertar el usuario en la base de datos
        $stmt = $db->prepare("INSERT INTO todas_las_sagas (titulo, slug) VALUES (?,?)");
        $stmt->execute([$nameMod, $slug]);
        
        Flight::json(["message" => "Saga registrada correctamente!"]);
    } catch (Exception $e) {
        Flight::halt(500, json_encode(["error" => "Error en el servidor: " . $e->getMessage()]));
        return;
    }
});


Flight::route('POST /add-mod-saga', function() {
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    
    Auth::init();
    date_default_timezone_set('America/Mexico_City');
    $request = json_decode(file_get_contents("php://input"), true);
    
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    $userId = $authData->data->id; // Obtener el ID del usuario desde el token
    $userRol = $authData->data->rol;
    
    if($userRol !=4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para realizar esta acción."]));
        return;
    }
    
    // Obtener los datos de POST
    $id_saga = $request['id_saga'] ?? 1;
    $id_tipo = $request['id_tipo'] ?? 1;
    $id_mod = $request['id_mod'] ?? 1;
    // Extraer arrays si existen
    // Si es válido, se sanitiza

    try {
        // Conectar a la base de datos
        $db = Flight::db();
        
        // Verificar si el usuario ya existe
        $stmt = $db->prepare("SELECT * FROM mods_registrados_sagas WHERE id_mod = ? AND id_saga = ? AND tipo = ?");
        $stmt->execute([$id_mod, $id_saga, $id_tipo]);
        if ($stmt->fetch()) {
            Flight::halt(400, json_encode(["error" => "El mod ya existe en la saga con el tipo seleccionado."]));
            return;
        }

        // Insertar el usuario en la base de datos
        $stmt = $db->prepare("INSERT INTO mods_registrados_sagas (id_mod, id_saga, tipo) VALUES (?,?, ?)");
        $stmt->execute([$id_mod, $id_saga, $id_tipo]);
        
        Flight::json(["message" => "Mod y saga registrada correctamente!"]);
    } catch (Exception $e) {
        Flight::halt(500, json_encode(["error" => "Error en el servidor: " . $e->getMessage()]));
        return;
    }
});

// solo updates

Flight::route('POST /mod/@id/update-logo', function($id) {
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    // Conectar a la base de datos
    Auth::init();
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }

    $userId = $authData->data->rol;
    if($userId != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para realizar esta acción."]));
        return;
    }
    
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "id del mod inválido"]));
        return;
    }
    $idMod = $id;
    
    $db = Flight::db();
    
    // Verificar si se envió un archivo
    if (!isset($_FILES['file'])) {
        Flight::json(["error" => "No se ha enviado ningún archivo"], 400);
        return;
    }

    $file = $_FILES['file'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        Flight::json(["error" => "Error al subir el archivo: " . $file['error']], 400);
        return;
    }
    
    $smtLog = $db->prepare("SELECT * FROM mods WHERE id = ?");
    $smtLog->execute([$idMod]);
    $rseultLogo = $smtLog->fetch(PDO::FETCH_ASSOC);
    
    if(!$rseultLogo){
        Flight::json(["error" => "No se encontró ningun mod"], 404);
        return;
    }

    $uploadDir = 'images/mods/'.$rseultLogo['id'];
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = 'logo_' . uniqid() . '.' . $extension;
    
    // Crear la carpeta si no existe
   if(!file_exists($uploadDir)){
        mkdir($uploadDir, 0777, true);
    }
    // Obtener la ruta actual desde la base de datos
    $smtImagenLogo = $db->prepare("SELECT * FROM imagenes_mod WHERE id_mod = ? AND id_tipo_imagen = 1");
    $smtImagenLogo->execute([$rseultLogo['id']]);
    $rseultLogoDir= $smtImagenLogo->fetch(PDO::FETCH_ASSOC);
    $newFilePathSql = $uploadDir."/". $newFileName;
    $webpFilePath = "images/mods/" . $idMod."/". pathinfo($newFileName, PATHINFO_FILENAME) . ".webp"; // Ruta WebP

    if ($rseultLogoDir) {
        $existingFilePath = $rseultLogoDir['url'];
        // Verificar si el archivo existe en el servidor
        if($rseultLogoDir["url"]!="gui/Imagen-no-disponible.jpg"){
            if (file_exists($existingFilePath)) {
                unlink($existingFilePath); // Eliminar archivo existente
            }
        }
        if (move_uploaded_file($file['tmp_name'],  $newFilePathSql)) {
            // Actualizar la ruta en la base de datos sin eliminar el registro
             if (convertirAWebP($newFilePathSql, $webpFilePath)) {
                $updateStmt = $db->prepare("UPDATE imagenes_mod SET url = ? WHERE id = ?");
                $updateStmt->execute([$newFilePathSql, $rseultLogoDir['id']]);
                Flight::json(["message" => "Logo reemplazado correctamente. Recomendamos recargar la página para ver los datos actualizados."]);
             }else{
                 Flight::json(["error" => "Error al subir el archivo"], 500);
                return;
             }
        } else {
            Flight::json(["error" => "Error al subir el archivo"], 500);
            return;
        }
    }else{
        if(move_uploaded_file($file['tmp_name'], $newFilePathSql)){
            if (convertirAWebP($newFilePathSql, $webpFilePath)) {
                $db = Flight::db();
                $stmt2 = $db->prepare("INSERT INTO imagenes_mod (id_mod, id_tipo_imagen, url) VALUES (?, ?, ?)");
                $stmt2->execute([$id,1, $webpFilePath]);
                Flight::json(["message" => "Logo reemplazado correctamente. Recomendamos recargar la página para ver los datos actualizados."]);
            }else{
                Flight::json(["error" => "Error al subir el archivo"], 500);
                return;
            }
                 
        }
    }
});

Flight::route('POST /mod/@id/update-front-page', function($id) {
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    // Conectar a la base de datos
    Auth::init();
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }

    $userId = $authData->data->rol;
    if($userId != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para realizar esta acción."]));
        return;
    }
    
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "id del mod inválido"]));
        return;
    }
    $idMod = $id;
    
    $db = Flight::db();
    
    // Verificar si se envió un archivo
    if (!isset($_FILES['file'])) {
        Flight::json(["error" => "No se ha enviado ningún archivo"], 400);
        return;
    }

    $file = $_FILES['file'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        Flight::json(["error" => "Error al subir el archivo: " . $file['error']], 400);
        return;
    }
    
    $smtLog = $db->prepare("SELECT * FROM mods WHERE id = ?");
    $smtLog->execute([$idMod]);
    $rseultLogo = $smtLog->fetch(PDO::FETCH_ASSOC);
    
    if(!$rseultLogo){
        Flight::json(["error" => "No se encontró ningun mod"], 404);
        return;
    }

    $uploadDir = 'images/mods/'.$rseultLogo['id'];
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = 'portada_' . uniqid() . '.' . $extension;
    
    // Crear la carpeta si no existe
   if(!file_exists($uploadDir)){
        mkdir($uploadDir, 0777, true);
    }
    // Obtener la ruta actual desde la base de datos
    $smtImagenLogo = $db->prepare("SELECT * FROM imagenes_mod WHERE id_mod = ? AND id_tipo_imagen = 3");
    $smtImagenLogo->execute([$rseultLogo['id']]);
    $rseultLogoDir= $smtImagenLogo->fetch(PDO::FETCH_ASSOC);
    $newFilePathSql = $uploadDir."/". $newFileName;
    $webpFilePath = "images/mods/" . $idMod."/". pathinfo($newFileName, PATHINFO_FILENAME) . ".webp"; // Ruta WebP

    if ($rseultLogoDir) {
        $existingFilePath = $rseultLogoDir['url'];
        // Verificar si el archivo existe en el servidor
        if($rseultLogoDir["url"]!="gui/Imagen-no-disponible.jpg"){
            if (file_exists($existingFilePath)) {
                unlink($existingFilePath); // Eliminar archivo existente
            }
        }
        if (move_uploaded_file($file['tmp_name'],  $newFilePathSql)) {
            // Actualizar la ruta en la base de datos sin eliminar el registro
             if (convertirAWebP($newFilePathSql, $webpFilePath)) {
                $updateStmt = $db->prepare("UPDATE imagenes_mod SET url = ? WHERE id = ?");
                $updateStmt->execute([$newFilePathSql, $rseultLogoDir['id']]);
                Flight::json(["message" => "Portada reemplazada correctamente. Recomendamos recargar la página para ver los datos actualizados."]);
             }else{
                 Flight::json(["error" => "Error al subir el archivo"], 500);
                return;
             }
        } else {
            Flight::json(["error" => "Error al subir el archivo al servidor"], 500);
            return;
        }
    }else{
        if(move_uploaded_file($file['tmp_name'], $newFilePathSql)){
            if (convertirAWebP($newFilePathSql, $webpFilePath)) {
                $db = Flight::db();
                $stmt2 = $db->prepare("INSERT INTO imagenes_mod (id_mod, id_tipo_imagen, url) VALUES (?, ?, ?)");
                $stmt2->execute([$id,3, $webpFilePath]);
                Flight::json(["message" => "Portada reemplazada correctamente. Recomendamos recargar la página para ver los datos actualizados."]);
            }else{
                Flight::json(["error" => "Error al subir el archivo"], 500);
                return;
            }
                 
        }
    }
});

Flight::route('POST /mod/@id/update-front-page', function($id) {
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    // Conectar a la base de datos
    Auth::init();
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }

    $userId = $authData->data->rol;
    if($userId != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para realizar esta acción."]));
        return;
    }
    
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "id del mod inválido"]));
        return;
    }
    $idMod = $id;
    
    $db = Flight::db();
    
    // Verificar si se envió un archivo
    if (!isset($_FILES['file'])) {
        Flight::json(["error" => "No se ha enviado ningún archivo"], 400);
        return;
    }

    $file = $_FILES['file'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        Flight::json(["error" => "Error al subir el archivo: " . $file['error']], 400);
        return;
    }
    
    $smtLog = $db->prepare("SELECT * FROM mods WHERE id = ?");
    $smtLog->execute([$idMod]);
    $rseultLogo = $smtLog->fetch(PDO::FETCH_ASSOC);
    
    if(!$rseultLogo){
        Flight::json(["error" => "No se encontró ningun mod"], 404);
        return;
    }

    $uploadDir = 'images/mods/'.$rseultLogo['id'];
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = 'logo_' . uniqid() . '.' . $extension;
    
    // Crear la carpeta si no existe
   if(!file_exists($uploadDir)){
        mkdir($uploadDir, 0777, true);
    }
    // Obtener la ruta actual desde la base de datos
    $smtImagenLogo = $db->prepare("SELECT * FROM imagenes_mod WHERE id_mod = ? AND id_tipo_imagen = 1");
    $smtImagenLogo->execute([$rseultLogo['id']]);
    $rseultLogoDir= $smtImagenLogo->fetchAll(PDO::FETCH_ASSOC);
    $newFilePathSql = $uploadDir."/". $newFileName;
    $webpFilePath = "images/mods/" . $idMod."/". pathinfo($newFileName, PATHINFO_FILENAME) . ".webp"; // Ruta WebP

    if ($rseultLogoDir) {
        $existingFilePath = $rseultLogoDir['url'];
        // Verificar si el archivo existe en el servidor
        if($rseultLogoDir["url"]!="gui/Imagen-no-disponible.jpg"){
            if (file_exists($existingFilePath)) {
                unlink($existingFilePath); // Eliminar archivo existente
            }
        }
        if (move_uploaded_file($file['tmp_name'],  $newFilePathSql)) {
            // Actualizar la ruta en la base de datos sin eliminar el registro
             if (convertirAWebP($newFilePathSql, $webpFilePath)) {
                $updateStmt = $db->prepare("UPDATE imagenes_mod SET url = ? WHERE id = ?");
                $updateStmt->execute([$newFilePathSql, $rseultLogoDir['id']]);
                Flight::json(["message" => "Logo reemplazado correctamente. Recomendamos recargar la página para ver los datos actualizados."]);
             }else{
                 Flight::json(["error" => "Error al subir el archivo"], 500);
                return;
             }
        } else {
            Flight::json(["error" => "Error al subir el archivo"], 500);
            return;
        }
    }else{
        if(move_uploaded_file($file['tmp_name'], $newFilePathSql)){
            if (convertirAWebP($newFilePathSql, $webpFilePath)) {
                $db = Flight::db();
                $stmt2 = $db->prepare("INSERT INTO imagenes_mod (id_mod, id_tipo_imagen, url) VALUES (?, ?, ?)");
                $stmt2->execute([$id,1, $webpFilePath]);
                Flight::json(["message" => "Logo reemplazado correctamente. Recomendamos recargar la página para ver los datos actualizados."]);
            }else{
                Flight::json(["error" => "Error al subir el archivo"], 500);
                return;
            }
                 
        }
    }
});

Flight::route('PUT /mod/@id/change-description', function($id){
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    // Obtener datos del frontend
    Auth::init();
    $request = json_decode(file_get_contents("php://input"), true);

    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    
    $rolUser = $authData->data->rol;
    
    if($rolUser != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para esta acción."]));
        return;
    }
     // Conectar a la base de datos
    $db = Flight::db();
    
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "id del mod inválido"]));
        return;
    }
    $idMod = $id;

    $descriptionMod = $request['descripcion'] ?? 'Sin descripción.';

    // Validar que los datos no estén vacíos
    
    
    // Actualizar la contraseña en la base de datos
    $stmt = $db->prepare("UPDATE mods SET descripcion = ? WHERE id = ?");
    if ($stmt->execute([$descriptionMod, $idMod])) {
        Flight::json(["message" => "Descripción actualizada correctamente."]);
    } else {
        Flight::halt(500, json_encode(["error" => "Error al actualizar la descripción."]));
        return;
    }
});

Flight::route('PUT /mod/@id/change-link-pc', function($id){
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    // Obtener datos del frontend
    Auth::init();
    $request = json_decode(file_get_contents("php://input"), true);
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    
     $rolUser = $authData->data->rol;
    
    if($rolUser != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para esta acción."]));
        return;
    }
     // Conectar a la base de datos
    $db = Flight::db();
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "id del mod inválido"]));
        return;
    }
    $idMod = $id;
    
    $mod = $db->prepare("SELECT * FROM mods WHERE id = ? ");
    $mod->execute([$idMod]);
    $infoMod = $mod->fetch(PDO::FETCH_ASSOC);
    $nombreMod = $infoMod['nombre'];
    $slugMod = $infoMod["slug"];
    
    
    $linkPc = $request['link_pc'] ?? null;
    
    if(empty($linkPc)){
        $linkPc = null;
    }
    
    $searchLink = null;
    
    $searchLink = $db->prepare("SELECT id FROM enlaces WHERE id_mod = ? AND id_tipo = 2");
    if (!$searchLink) {
        $error = $db->errorInfo();
        Flight::halt(500, json_encode(["error" => "Error al preparar búsqueda de enlace: " . $error[2]]));
        return;
    }

    $searchLink->execute([$idMod]);
    $idEnlace = $searchLink->fetchColumn();
    
    if($idEnlace){
        $stmt = $db->prepare("UPDATE enlaces SET url = ? WHERE id = ?");
        if ($stmt->execute([$linkPc,$idEnlace] )) {
            
            if(!is_null($linkPc)){
                
                $smt3 = $db->prepare("SELECT * FROM imagenes_mod WHERE id_mod = ? AND id_tipo_imagen = 1");
                $smt3->execute([$idMod]);
                $imagenes = $smt3->fetch(PDO::FETCH_ASSOC);
                $logo = $imagenes["url"];
                
                $stmt4 = $db->query("SELECT * FROM subscriptions");
                $subscriptions = $stmt4->fetchAll(PDO::FETCH_ASSOC);
            
                $vapidKeys = [
                    'subject' => 'mailto:soporte@dokidokispanish.club',
                    'publicKey' => 'BIuTU82pi4qBD-sNaJNezG1kGs_cA5-Qb_ZaVQ1ZCj7PxvoqBSDe9q33cZO4BmVZRL-97GAYzu18rPxf7lSw1pk',
                    'privateKey' => 'rlwhiDtAYDeELyYC5fEDRtk3jIIjPTcryTsLXzKa6oQ',
                ];
            
                $webPush = new WebPush([
                    'VAPID' => $vapidKeys
                ]);
                $site_url = getenv('URL_SITE');
                // Enviar notificaciones a todas las suscripciones
                foreach ($subscriptions as $sub) {
                    $subscription = Subscription::create([
                        'endpoint' => $sub['endpoint'],
                        'keys' => [
                            'p256dh' => $sub['p256dh'],
                            'auth' => $sub['auth'],
                        ],
                    ]);
                    $webPush->queueNotification($subscription, json_encode([
                        'title' => "¡Un mod fue editado!",
                        'body' => "El enlace de pc fue modificado en el mod: ". $nombreMod. "!",
                        'icon' => "https://api.dokidokispanish.club/". $logo,
                        'data' => ['url' => "{$site_url}/mods/". $slugMod] // 🔹 Agregamos la URL
                    ]));
                }
            
                foreach ($webPush->flush() as $report) {
                    
                }
            }
            Flight::json(["message" => "Enlace actualizado correctamente."]);
        } else {
            Flight::halt(500, json_encode(["error" => "Error al actualizar el enlace."]));
            return;
        }
    }else{
        $stmt = $db->prepare("INSERT INTO enlaces (id_tipo, id_mod, url) VALUES (?,?,?)");
        if($stmt->execute([2, $idMod,$linkPc])){
            Flight::json(["message" => "Enlace asignado correctamente."]);
        } else {
            Flight::halt(500, json_encode(["error" => "Error al agregar el enlace."]));
            return;
        }
    }
});

Flight::route('PUT /mod/@id/change-link-android', function($id){
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    // Obtener datos del frontend
    Auth::init();
    $request = json_decode(file_get_contents("php://input"), true);
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    
    $rolUser = $authData->data->rol;
    
    if($rolUser != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para esta acción."]));
        return;
    }
    
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "id del mod inválido"]));
        return;
    }
    
     // Conectar a la base de datos
    $db = Flight::db();
    
    $idMod = $id;
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "id del mod inválido"]));
        return;
    }
    $linkAPK = $request['link_android'] ?? null;
    
    if(empty($linkAPK)){
        $linkAPK = null;
    }
    
    $mod = $db->prepare("SELECT * FROM mods WHERE id = ? ");
    $mod->execute([$idMod]);
    $infoMod = $mod->fetch(PDO::FETCH_ASSOC);
    
     $searchLink = null;
    $searchLink = $db->prepare("SELECT id FROM enlaces WHERE id_mod = ? AND id_tipo = 1");
    $searchLink->execute([$idMod]);
    $idEnlace = $searchLink->fetchColumn();
    
    if($idEnlace){
        $stmt = $db->prepare("UPDATE enlaces SET url = ? WHERE id = ?");
        if ($stmt->execute([$linkAPK,$idEnlace] )) {
            if(!is_null($linkAPK)){
                
                $smt3 = $db->prepare("SELECT * FROM imagenes_mod WHERE id_mod = ? AND id_tipo_imagen = 1");
                $smt3->execute([$idMod]);
                $imagenes = $smt3->fetch(PDO::FETCH_ASSOC);
                
                $stmt4 = $db->query("SELECT * FROM subscriptions");
                $subscriptions = $stmt4->fetchAll(PDO::FETCH_ASSOC);
            
                $vapidKeys = [
                    'subject' => 'mailto:soporte@dokidokispanish.club',
                    'publicKey' => 'BIuTU82pi4qBD-sNaJNezG1kGs_cA5-Qb_ZaVQ1ZCj7PxvoqBSDe9q33cZO4BmVZRL-97GAYzu18rPxf7lSw1pk',
                    'privateKey' => 'rlwhiDtAYDeELyYC5fEDRtk3jIIjPTcryTsLXzKa6oQ',
                ];
            
                $webPush = new WebPush([
                    'VAPID' => $vapidKeys
                ]);

                $site_url = getenv('URL_SITE');
            
                // Enviar notificaciones a todas las suscripciones
                foreach ($subscriptions as $sub) {
                    $subscription = Subscription::create([
                        'endpoint' => $sub['endpoint'],
                        'keys' => [
                            'p256dh' => $sub['p256dh'],
                            'auth' => $sub['auth'],
                        ],
                    ]);
                    $webPush->queueNotification($subscription, json_encode([
                        'title' => "¡Un mod fue editado!",
                        'body' => "El enlace de android fue modificado en el mod: ". $infoMod['nombre']. "!",
                        'icon' => "https://api.dokidokispanish.club/". $imagenes['url'],
                        'data' => ['url' => "{$site_url}/mods/". $infoMod['slug']] // 🔹 Agregamos la URL
                    ]));
                }
            
                foreach ($webPush->flush() as $report) {
                    
                }
            }
            Flight::json(["message" => "Enlace actualizado correctamente."]);
        } else {
            Flight::halt(500, json_encode(["error" => "Error al actualizar el enlace."]));
            return;
        }
    }else{
        $stmt = $db->prepare("INSERT INTO enlaces (id_tipo, id_mod, url) VALUES (?,?,?)");
        if($stmt->execute([1, $idMod,$linkAPK])){
            Flight::json(["message" => "Enlace asignado correctamente."]);
        } else {
            Flight::halt(500, json_encode(["error" => "Error al agregar el enlace."]));
            return;
        }
    }
});

Flight::route('PUT /mod/@id/change-nsfw', function($id){
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    // Obtener datos del frontend
    Auth::init();
    $request = json_decode(file_get_contents("php://input"), true);
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    
    $rolUser = $authData->data->rol;
    
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "id del mod inválido"]));
        return;
    }
    
    if($rolUser != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para esta acción."]));
        return;
    }
     // Conectar a la base de datos
    $db = Flight::db();
    
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "id del mod inválido"]));
        return;
    }
    
    $isNSFW = $request['NSFW'] ?? false;
    $isNSFW= (int)$isNSFW;
    
    if (!preg_match('/^[0-1]+$/', $isNSFW)) {
        Flight::halt(400, json_encode(["error" => "Opción inválido"]));
        return;
    }
    
    $stmt = $db->prepare("UPDATE mods SET isNSFW = ? WHERE id = ?");
    if ($stmt->execute([$isNSFW,$id] )) {
        Flight::json(["message" => "Ahora el mod es para mayores de edad."]);
    } else {
        Flight::halt(500, json_encode(["error" => "Error al actualizar la restricción."]));
        return;
    }
});

Flight::route('PUT /mod/@id/change-date', function($id){
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    // Obtener datos del frontend
    Auth::init();
    $request = json_decode(file_get_contents("php://input"), true);
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    
    $rolUser = $authData->data->rol;
    
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "id del mod inválido"]));
        return;
    }
    
    if($rolUser != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para esta acción."]));
        return;
    }
     // Conectar a la base de datos
    $db = Flight::db();
    
    $creado = $request['creado'] ?? date("Y-m-d h:m:s");
    
    if (!preg_match('/^[0-9-: ]+$/', $creado)) {
        Flight::halt(400, json_encode(["error" => "Fecha del mod inválido"]));
        return;
    }
    
    
    $stmt = $db->prepare("UPDATE mods SET created = ? WHERE id = ?");
    if ($stmt->execute([$creado,$id] )) {
        Flight::json(["message" => "Fecha modificada correctamente."]);
    } else {
        Flight::halt(500, json_encode(["error" => "Error al actualizar la fecha."]));
        return;
    }
});

Flight::route('PUT /mod/@id/change-focus', function($id){
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    // Obtener datos del frontend
    Auth::init();
    $request = json_decode(file_get_contents("php://input"), true);
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    
    $userId = $authData->data->id; // Obtener el ID del usuario desde el token
     $rolUser = $authData->data->rol;
    
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "id del mod inválido"]));
        return;
    }
    
    if($rolUser != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para esta acción."]));
        return;
    }
     // Conectar a la base de datos
    $db = Flight::db();
    
    $enfoque = $request['enfoque'];
    
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "Enfoque del mod inválido"]));
        return;
    }
    
    
    $stmt = $db->prepare("UPDATE mods SET id_enfoque = ? WHERE id = ?");
    if ($stmt->execute([$enfoque,$id] )) {
        Flight::json(["message" => "Enfoque modificado correctamente."]);
    } else {
        Flight::halt(500, json_encode(["error" => "Error al actualizar el enfoque."]));
        return;
    }
});

Flight::route('PUT /mod/@id/change-duration', function($id){
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    // Obtener datos del frontend
    Auth::init();
    $request = json_decode(file_get_contents("php://input"), true);
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    
    $rolUser = $authData->data->rol;
    
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "id del mod inválido"]));
        return;
    }
    
    if($rolUser != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para esta acción."]));
        return;
    }
     // Conectar a la base de datos
    $db = Flight::db();
    
    $duracion = $request['duracion'];
    
    if (!preg_match('/^[0-9]+$/', $duracion)) {
        Flight::halt(400, json_encode(["error" => "Duración del mod inválido"]));
        return;
    }
    
    
    $stmt = $db->prepare("UPDATE mods SET id_duracion = ? WHERE id = ?");
    if ($stmt->execute([$duracion,$id] )) {
        Flight::json(["message" => "Duración modificada correctamente."]);
    } else {
        Flight::halt(500, json_encode(["error" => "Error al actualizar la duración."]));
        return;
    }
});

Flight::route('PUT /mod/@id/change-state', function($id){
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    // Obtener datos del frontend
    Auth::init();
    $request = json_decode(file_get_contents("php://input"), true);
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    
    $rolUser = $authData->data->rol;
    
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "id del mod inválido"]));
        return;
    }
    
    if($rolUser != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para esta acción."]));
        return;
    }
     // Conectar a la base de datos
    $db = Flight::db();
    
    $estado= $request['estado'] ?? 1;
    
    if (!preg_match('/^[0-9]+$/', $estado)) {
        Flight::halt(400, json_encode(["error" => "Estado del mod inválido"]));
        return;
    }
    
    $stmt = $db->prepare("UPDATE mods SET id_estado = ? WHERE id = ?");
    if ($stmt->execute([$estado,$id] )) {
        Flight::json(["message" => "Estado del mod modificado correctamente."]);
    } else {
        Flight::halt(500, json_encode(["error" => "Error al actualizar la duración."]));
        return;
    }
});

Flight::route('PUT /mod/@id/change-porteador', function($id){
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    // Obtener datos del frontend
    Auth::init();
    $request = json_decode(file_get_contents("php://input"), true);
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    
    $rolUser = $authData->data->rol;
    
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "id del mod inválido"]));
        return;
    }
    
    if($rolUser != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para esta acción."]));
        return;
    }
     // Conectar a la base de datos
    $db = Flight::db();
    
    if(!isset($request['id_porteador'])){
        Flight::halt(401, json_encode(["error" => "No puedes dejar esta opción como invalida."]));
        return;
    }
    
    $estado= $request['id_porteador'];
    
    if (!preg_match('/^[0-9]+$/', $estado)) {
        Flight::halt(400, json_encode(["error" => "Estado del mod inválido"]));
        return;
    }
    
    $stmt = $db->prepare("UPDATE mods SET id_porteador = ? WHERE id = ?");
    if ($stmt->execute([$estado,$id] )) {
        Flight::json(["message" => "Porteador modificado correctamente."]);
    } else {
        Flight::halt(500, json_encode(["error" => "Error al actualizar la duración."]));
        return;
    }
});

Flight::route('PUT /mod/@id/change-active', function($id){
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    // Obtener datos del frontend
    Auth::init();
    $request = json_decode(file_get_contents("php://input"), true);
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    
    $rolUser = $authData->data->rol;
    
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "id del mod inválido"]));
        return;
    }
    
    if($rolUser != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para esta acción."]));
        return;
    }
     // Conectar a la base de datos
    $db = Flight::db();
    
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "id del mod inválido"]));
        return;
    }
    
    $isNSFW = $request['active'] ?? false;
    $isNSFW= (int)$isNSFW;
    
    if (!preg_match('/^[0-1]+$/', $isNSFW)) {
        Flight::halt(400, json_encode(["error" => "Opción inválido"]));
        return;
    }
    
    $stmt = $db->prepare("UPDATE mods SET isPublic = ? WHERE id = ?");
    if ($stmt->execute([$isNSFW,$id] )) {
        Flight::json(["message" => "El mod a cambiado de visibilidad"]);
    } else {
        Flight::halt(500, json_encode(["error" => "Error al actualizar la visibilidad."]));
        return;
    }
});

Flight::route('PUT /update-category/@id', function($id){
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    // Obtener datos del frontend
    Auth::init();
    $request = json_decode(file_get_contents("php://input"), true);
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    
    $userRol = $authData->data->rol;
    
    if($userRol != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para realizar esta acción."]));
        return;
    }
     // Conectar a la base de datos
    $db = Flight::db();
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "id de la categoria no valida"]));
        return;
    }
    $idMod = $id;
    
    $mod = $db->prepare("SELECT * FROM categroies WHERE id = ? ");
    $mod->execute([$idMod]);
    $infoMod = $mod->fetch(PDO::FETCH_ASSOC);
    $nombreMod = $infoMod['categoria'];
    $slugMod = $infoMod["slug"];
    
    
    $nameCategory= $request['category'] ?? "categoria";
    $active= $request['active'] ?? true;
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', str_replace(":", "-", str_replace(",", "",str_replace(".","",str_replace(" ", "-", strtolower(trim($nameCategory)))))));
    
    
    $stmt = $db->prepare("UPDATE categories SET categoria = ?, slug= ?, active= ? WHERE id = ?");
    if ($stmt->execute([$nameCategory,$slug, $active] )) {
        Flight::json(["message" => "Categoria actualizada correctamente."]);
    } else {
        Flight::halt(500, json_encode(["error" => "Error al actualizar la categoria."]));
        return;
    }
    
});

Flight::route('PUT /update-category-mod/@i_categoryd/@id_mod', function($id_category, $id_mod){
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    // Obtener datos del frontend
    Auth::init();
    $request = json_decode(file_get_contents("php://input"), true);
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    
    $userRol = $authData->data->rol;
    
    if($userRol != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para realizar esta acción."]));
        return;
    }
     // Conectar a la base de datos
    $db = Flight::db();
    if (!preg_match('/^[0-9]+$/', $id_category)) {
        Flight::halt(400, json_encode(["error" => "id de la categoria no valida"]));
        return;
    }
    if (!preg_match('/^[0-9]+$/', $id_mod)) {
        Flight::halt(400, json_encode(["error" => "id del mod no valido"]));
        return;
    }
    
    $mod = $db->prepare("SELECT * FROM mod_category WHERE id_mod = ? AND id_category = ?");
    $mod->execute([$id_mod, $id_category]);
    if (!$mod->fetch()) {
            Flight::halt(400, json_encode(["error" => "La categoria no existe."]));
            return;
        }
    $infoMod = $mod->fetch(PDO::FETCH_ASSOC);
    $id_category = $infoMod['id'];
    
    $active= $request['active'] ?? true;
    
    $stmt = $db->prepare("UPDATE mod_category SET active = ? WHERE id = ?");
    if ($stmt->execute([$active, $id_category] )) {
        Flight::json(["message" => "Categoria del mod actualizado correctamente."]);
    } else {
        Flight::halt(500, json_encode(["error" => "Error al actualizar el mod."]));
        return;
    }
    
});