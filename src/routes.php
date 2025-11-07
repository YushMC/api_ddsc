<?php
require_once 'flags.php';


use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use WebPConvert\WebPConvert;

function generarCorreoHTML($nombre, $titulo, $mensaje, $textoBoton, $urlBoton, $codigo = null) {
    // Escapar valores por seguridad
    $nombre = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
    $titulo = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
    $mensaje = htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');
    $textoBoton = htmlspecialchars($textoBoton, ENT_QUOTES, 'UTF-8');
    $urlBoton = htmlspecialchars($urlBoton, ENT_QUOTES, 'UTF-8');
    $codigo = $codigo ? htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8') : null;

    // Código opcional
    $bloqueCodigo = $codigo
        ? "<h5 style='font-size:14px; font-weight:normal; color:#555; text-align:center; margin-top:20px;'>También puedes ingresar los siguientes dígitos: <strong style='color:#d56bd7;'>{$codigo}</strong></h5>"
        : "";

    // Logo DDSC
    $site_url = getenv('URL_SITE');
    $logoUrl = "{$site_url}/images/Logo_DDSC.png";
    $comunidadUrl = "{$site_url}/images/banner_comunidad.jpg";

    return <<<HTML
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{$titulo}</title>
  </head>
  <body style="margin:0; padding:0; background-color:#f4f4f4; font-family:Arial,Helvetica,sans-serif; color:#333333;">
    <!-- Preheader oculto -->
    <span style="display:none; font-size:1px; color:#f4f4f4; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden;">{$titulo}</span>

    <!-- Contenedor principal centrado -->
    <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation" style="background-color:#f4f4f4; padding:40px 0;">
      <tr>
        <td align="center">
          <table width="500" border="0" cellspacing="0" cellpadding="0" role="presentation" style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 0 8px rgba(0,0,0,0.08);">
            <!-- Header con logo y saludo -->
            <tr>
              <td style="padding:20px 10px; text-align:center; background:#ffffff;">
                <img src="{$logoUrl}" alt="Logo DDSC" width="50" style="vertical-align:middle; display:inline-block; margin-right:8px;">
                <span style="font-size:1.4rem; font-weight:bold; color:#333; vertical-align:middle;">¡Hola, {$nombre}!</span>
              </td>
            </tr>

            <!-- Imagen principal -->
            <tr>
              <td>
                <img src="{$comunidadUrl}" alt="Banner de la comunidad" width="500" style="width:100%; display:block; border:0;">
              </td>
            </tr>

            <!-- Cuerpo -->
            <tr>
              <td style="padding:24px; font-size:16px; line-height:1.5; color:#333;">
                <h1 style="font-size:22px; font-weight:700; margin:0 0 12px 0;">{$titulo}</h1>
                <p style="margin:0 0 16px 0; color:#444;">{$mensaje}</p>

                <!-- Botón -->
                <div style="text-align:center; margin:24px 0;">
                  <a href="{$urlBoton}" target="_blank"
                    style="
                      display:inline-block;
                      padding:12px 24px;
                      background-color:#d56bd7;
                      color:#ffffff;
                      font-weight:600;
                      border-radius:6px;
                      text-decoration:none;
                    ">
                    {$textoBoton}
                  </a>
                </div>

                {$bloqueCodigo}
              </td>
            </tr>

            <!-- Footer -->
            <tr>
              <td style="text-align:center; padding:16px; font-size:12px; color:#888888; background:#ffffff;">
                © Doki Doki Spanish Club Web
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
HTML;
}

function comprobarInfoUsers($datos) {
    $esUnico = false; // bandera

    // Verifica si $datos es un único registro (tiene 'id')
    if (isset($datos['id'])) {
        $datos = [$datos]; 
        $esUnico = true;
    }

    $array = [];
    foreach ($datos as $row) {
        $array[] = [
            "id" => $row['id'],
            "nombre" => $row['user'],
            "alias" => $row['alias'],
            "descripcion" => $row['description'] ?? "Soy un miembro del club!",
            "nacionalidad" => getBandera($row["country"]),
            "slug"=>$row['slug'],
            "id_rol"=>$row["id_rol"],
            "rol_nombre" => $row['rol_nombre'],
            "url_logo" => "https://api.dokidokispanish.club/" . $row['url_logo'],
            "url_banner" => "https://api.dokidokispanish.club/" . $row['url_banner'],
            "url_fondo_pantalla" => "https://api.dokidokispanish.club/" . $row['url_wallpaper'],
        ];
    }

    // Si originalmente era un solo dato, devolver solo ese objeto
    return $esUnico ? $array[0] : $array;
}
function comprobarInfoUsers2($datos){
    $array= [];
    // Verifica si $datos es un array y tiene registros
    
    foreach ($datos as $row){
        $array[] = [
            "id"=>$row['id'],
            "nombre"=>$row['alias'],
            "descripcion" => $row['description'] ?? "Soy un miembro del club!",
            "slug"=>$row['slug'],
            "nacionalidad" => getBandera($row["country"]),
            "rol"=>$row['rol_nombre'],
            "url_logo" => "https://api.dokidokispanish.club/".$row['url_logo'],
            "url_banner"=> "https://api.dokidokispanish.club/".$row['url_banner'],
            "url_fondo_pantalla"=> "https://api.dokidokispanish.club/".$row['url_wallpaper'],
            ];
    }
    return $array;
}
function comprobarInfoUsers3($datos){
    $array= [];
    // Verifica si $datos es un array y tiene registros
    
    foreach ($datos as $row){
        $array[] = [
            "id"=>$row['id'],
            "logo" => "https://api.dokidokispanish.club/".$row['url_logo'],
            "nombre"=>$row['user'],
            "correo"=>$row['email'],
            "alias"=>$row['alias'],
            "slug"=>$row['slug'],
            "nacionalidad" => $row["country"],
            "rol"=>$row['rol_nombre'],
            "verificado"=>(bool)$row['verify'],
            "activo"=>(bool)$row['isVisible'],
            
            ];
    }
    return $array;
}
function comprobarBanners($datos){
    $array= [];
    // Verifica si $datos es un array y tiene registros
    
    foreach ($datos as $row){
        $array[] = [
            "id"=>$row['id'],
            "img"=>$row['img'],
            "text" => $row['text'],
            "enlace"=>$row['enlace']
            ];
    }
    return $array;
}

Flight::route('GET /', function() {
    Flight::json(["mensaje" => "API funcionando"]);
});
//revisar si existe el token
Flight::route('GET /session', function() {
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');

    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    Auth::init();
    
    try {
        $decoded = Auth::verificarToken($token);
        Flight::json(["token_decodificado" => $decoded->data]); // Retornar datos del usuario
    } catch (Exception $e) {
        Flight::halt(401, json_encode(["error" => "Token inválido"]));
        return;
    }
});

Flight::route('GET /total-uploaders', function() {
    $db = Flight::db();

    $query = "SELECT id FROM users WHERE id_rol <> 1 AND isVisible = 1";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);


    Flight::json([
        "response" => "success",
        "total_rows" => count($mods),
    ]);
});

function toBool($value): bool {
    return (int)$value === 1;
}

Flight::route('GET /roles', function() {
    $db = Flight::db();

    $query = "
        SELECT 
            r.id AS rol_id, 
            r.nombre AS rol_nombre
        FROM roles r
    ";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agrupamos los permisos dentro de cada rol
    $roles = [];
    foreach ($rows as $row) {
        $id = $row['rol_id'];

        if (!isset($roles[$id])) {
            $roles[$id] = [
                "id" => $row['rol_id'],
                "rol" => $row['rol_nombre'],
            ];
        }
    }

    Flight::json([
        "response" => "success",
        "total_rows" => count($roles),
        "results" => array_values($roles),
    ]);
});

Flight::route('GET /rol/id/@id', function($id) {
    $db = Flight::db();

    $query = "
        SELECT 
            r.id AS rol_id, 
            r.nombre AS rol_nombre, 
            p.id AS permiso_id, 
            p.id_rol, 
            p.read_mod, 
            p.edit_mod, 
            p.add_mod, 
            p.delete_mod, 
            p.user_actions, 
            p.info_actions, 
            p.rol_actions, 
            p.community_actions
        FROM roles r
        LEFT JOIN permissions p ON r.id = p.id_rol
        WHERE r.id = ?
    ";

    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        Flight::json([
            "response" => "error",
            "message" => "Rol no encontrado"
        ]);
        return;
    }

    // Tomamos el id y nombre del rol de la primera fila
    $role = [
        "id" => $rows[0]['rol_id'],
        "rol" => $rows[0]['rol_nombre'],
        "permissions" => null
    ];

    // Recorremos permisos
    foreach ($rows as $row) {
        if ($row['permiso_id']) {
            $role["permissions"] = [
                "read_mod" => toBool($row['read_mod']),
                "edit_mod" => toBool($row['edit_mod']),
                "add_mod" => toBool($row['add_mod']),
                "delete_mod" => toBool($row['delete_mod']),
                "user_actions" => toBool($row['user_actions']),
                "info_actions" => toBool($row['info_actions']),
                "rol_actions" => toBool($row['rol_actions']),
                "community_actions" => toBool($row['community_actions']),
            ];
            break; // 🔑 si solo quieres el primero
        }
    }

    Flight::json([
        "response" => "success",
        "total_rows" => count($role),
        "results" => $role,
    ]);
});

Flight::route('GET /user-info/@slug', function($slug) {
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    
    // Obtener datos del frontend
    Auth::init();
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    
    $rolUser = $authData->data->rol;
    
    if ($rolUser != 4) {
        Flight::halt(400, json_encode(["error" => "No autorizado"]));
        return;
    }
    
    $db = Flight::db();
    $stmt = $db->prepare("
        SELECT u.*, r.nombre AS rol_nombre 
        FROM users u
        INNER JOIN roles r ON u.id_rol = r.id
        WHERE u.slug = ?
        ");
    $stmt->execute([$slug]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $user['user_password'] = "";
    $user['verify'] = (bool)$user['verify'];
    $user['isVisible'] = (bool)$user['isVisible'];
         // Verificar si el usuario no existe
    if (!$user) {
        Flight::halt(404, json_encode(["error" => "El usuario no existe."]));
        return;
    }
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $stmt->rowCount(),
        "results"=> $user,
    ]);
    
    
});

Flight::route('GET /countries', function () {
    
    //conectamos a la base de datos y preparamos la query
    $sentencia = Flight::db()->prepare("SELECT * FROM `countries`");
    //ejecutamos la query
    $sentencia->execute();
    //guardamos los valores de la query
    $datos=$sentencia->fetchAll(PDO::FETCH_ASSOC);

    //los formateamos a Json
    
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $sentencia->rowCount(),
        "results"=> $datos,
    ]);
});

Flight::route('GET /check-use-name/@slug', function($slug) {
    $db = Flight::db();

    $query = "SELECT id FROM users WHERE user = ?"; // Paginación inicial para evitar sobrecarga LIMIT 50

    $stmt = $db->prepare($query);
    $stmt->execute([$slug]);
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if(count($mods)==0){
        Flight::json([
        "response" => "success",
        "isAvaiable" => true,
        ]);
    }else{
        Flight::json([
        "response" => "success",
        "is_available" => false,
    ]);
    }
});

Flight::route('GET /total-users', function() {
    $db = Flight::db();

    $query = "SELECT id FROM users"; // Paginación inicial para evitar sobrecarga LIMIT 50

    $stmt = $db->prepare($query);
    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);


    Flight::json([
        "response" => "success",
        "total_rows" => count($mods),
    ]);
});


Flight::route('GET /user/@slug', function($slug) {
    $db = Flight::db();
    $stmt = $db->prepare("
        SELECT u.*, r.nombre AS rol_nombre 
        FROM users u
        INNER JOIN roles r ON u.id_rol = r.id
        WHERE u.slug = ?
        ");
    $stmt->execute([$slug]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
         // Verificar si el usuario no existe
    if (!$user) {
        Flight::halt(404, json_encode(["error" => "El usuario no existe."]));
        return;
    }
    
    $array = comprobarInfoUsers($user);
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $stmt->rowCount(),
        "results"=> $array,
    ]);
    
});

Flight::route('GET /user/id/@id', function($id) {
    $db = Flight::db();
    $stmt = $db->prepare("
        SELECT u.*, r.nombre AS rol_nombre 
        FROM users u
        INNER JOIN roles r ON u.id_rol = r.id
        WHERE u.id = ?
        ");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
         // Verificar si el usuario no existe
    if (!$user) {
        Flight::halt(404, json_encode(["error" => "El usuario no existe."]));
        return;
    }
    
    $array = comprobarInfoUsers($user);
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $stmt->rowCount(),
        "results"=> $array,
    ]);
    
    
});

Flight::route('GET /info-page', function() {
    $db = Flight::db();
    $stmt = $db->prepare("SELECT * FROM info");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
         // Verificar si el usuario no existe
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $stmt->rowCount(),
        "results"=> $user,
    ]);
});


Flight::route('GET /banners', function() {
    $db = Flight::db();
    $stmt = $db->prepare("SELECT * FROM `banners`");
    $stmt->execute();
    $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    //$array = comprobarBanners($user);
    Flight::json([
        "response"=> "success",
        "total_rows" => $stmt->rowCount(),
        "results"=> $user,
    ]);
    
    
});

Flight::route('GET /users', function() {
    $db = Flight::db();
    $stmt = $db->prepare("
        SELECT u.*, r.nombre AS rol_nombre 
        FROM users u
        INNER JOIN roles r ON u.id_rol = r.id
        WHERE u.isVisible = 1
        ");
    $stmt->execute();
    $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $array = comprobarInfoUsers2($user);
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $stmt->rowCount(),
        "results"=> $array,
    ]);
    
    
});


Flight::route('GET /users-admin', function() {
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    
    Auth::init();
    
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    $userId = $authData->data->id; // Obtener el ID del usuario desde el token
    $userRol = $authData->data->rol;
    
    if($userRol != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para realizar esta acción."]));
        return;
    }
    $db = Flight::db();
    $stmt = $db->prepare("
        SELECT u.*, r.nombre AS rol_nombre 
        FROM users
        ");
    $stmt->execute();
    $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $array = comprobarInfoUsers3($user);
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $stmt->rowCount(),
        "results"=> $array,
    ]);
    
    
});

Flight::route('GET /users/team-ddsc', function() {
    $db = Flight::db();
    $stmt = $db->prepare("
        SELECT u.*, r.nombre AS rol_nombre 
        FROM users u
        INNER JOIN roles r ON u.id_rol = r.id
        WHERE u.isVisible = 1 AND (u.id_rol = 4 OR u.id_rol = 5)
    ");
    $stmt->execute();
    $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $array = comprobarInfoUsers2($user);

    Flight::json([
        "response"=> "success",
        "total_rows" => $stmt->rowCount(),
        "results"=> $array,
    ]); 
});

//ruta de login
Flight::route('POST /login', function() {
    $request = json_decode(file_get_contents("php://input"), true);

    $usuario = $request['email'] ?? '';
    $password = $request['password'] ?? '';

    // Validar que el usuario solo contenga letras y números
    if (!preg_match('/^[a-zA-Z0-9_@.-]+$/', $usuario)) {
        Flight::halt(400, json_encode(["error" => "Correo inválido"]));
        return;
    }

    // Si es válido, se sanitiza
    $usuario = filter_var($usuario, FILTER_SANITIZE_SPECIAL_CHARS);

    // Si es válido, se sanitiza
    $password = filter_var($password, FILTER_SANITIZE_SPECIAL_CHARS);

     // Conectar a la base de datos
     $db = Flight::db();
     $stmt = $db->prepare("
        SELECT u.*, r.nombre AS rol_nombre 
        FROM users u
        INNER JOIN roles r ON u.id_rol = r.id
        WHERE u.email = ?
    ");
    $stmt->execute([$usuario]);
     $user = $stmt->fetch(PDO::FETCH_ASSOC);

     // Verificar si el usuario no existe
    if (!$user) {
        Flight::halt(404, json_encode(["error" => "El usuario no existe. NOTA: Este sistema de cuentas no es retrocompatible con el sitio web principal, para acceder es necesario crear una nueva cuenta!."]));
        return;
    }

     if (!$user['verify']) {
        Flight::halt(403, json_encode(["error" => "Debes verificar tu cuenta antes de iniciar sesión."]));
        return;
    }

    // Verificar usuario y contraseña
    if (password_verify($password, $user['user_password'])) {

        Auth::init();
        $token = Auth::generarToken(
            [
                "id" => $user["id"],
                "nombre" => $user["user"],
                "alias" => $user["alias"],
                "slug" => $user["slug"],
                "rol" => $user["id_rol"],
                "rol_nombre" => $user["rol_nombre"], // Ahora también obtenemos el nombre del rol
                "url_logo" => "https://api.dokidokispanish.club/".$user["url_logo"],
                "url_banner" => "https://api.dokidokispanish.club/".$user["url_banner"],
                "url_fondo" => "https://api.dokidokispanish.club/".$user["url_wallpaper"],
                "descripcion" => $user["description"],
                "url_bandera" => getBandera($user["country"])
                ]
        );
        /*
        // Enviar como una cookie HTTP-Only
        setcookie("token", $token, [
            "expires" => time() + (60 * 60 * 24 * 3), // Expira en 3 días
            "path" => "/",
            "domain" => "", // Puedes establecerlo según el dominio
            "secure" => true, // Habilítalo solo en HTTPS
            "httponly" => true,
            "samesite" => "Strict"
        ]);
        */
        

        Flight::json(["message" => "Autenticación exitosa", "token" => $token]);
    } else {
        Flight::halt(401, json_encode(["error" => "Credenciales inválidas."]));
        return;
    }
});

Flight::route('POST /recovery-code', function (){
    $request = json_decode(file_get_contents("php://input"), true);
    $codigo = $request['recovery_code'] ?? null;
    
    if (!$codigo) {
        Flight::halt(400, json_encode(["error" => "Código no proporcionado."]));
        return;
    }
    try {
        $db = Flight::db();
        
        $stmt = $db->prepare("SELECT * FROM recovery_user_codes WHERE code_recovery = ?");
        $stmt->execute([$codigo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            Flight::halt(400, json_encode(["error" => "Código inválido."]));
            return;
        }
        if($user['isUsed']){
            Flight::halt(400, json_encode(["error" => "Código ya usado."]));
            return;
        }
        // Marcar la cuenta como verificada
        $stmt2 = $db->prepare("UPDATE recovery_user_codes SET isUsed = 1 WHERE id = ? AND code_recovery = ?");
        $stmt2->execute([$user['id'],$codigo]);
        
        $stmt3 = $db->prepare("
        SELECT u.*, r.nombre AS rol_nombre 
        FROM users u
        INNER JOIN roles r ON u.id_rol = r.id
        WHERE u.id = ?
        ");
        $stmt3->execute([$user['id_usuario']]);
        $user2 = $stmt3->fetch(PDO::FETCH_ASSOC);
    
         // Verificar si el usuario no existe
        if (!$user2) {
            Flight::halt(404, json_encode(["error" => "El usuario no existe."]));
            return;
        }
        
        Auth::init();
        $token = Auth::generarToken(
            [
                "id" => $user2["id"],
                "nombre" => $user2["user"],
                "rol" => $user2["id_rol"],
                "rol_nombre" => $user2["rol_nombre"], // Ahora también obtenemos el nombre del rol
                "url_logo" => "https://api.dokidokispanish.club/".$user2["url_logo"],
                "url_banner" => "https://api.dokidokispanish.club/".$user2["url_banner"],
                "url_fondo" => "https://api.dokidokispanish.club/".$user2["url_wallpaper"],
                "descripcion" => $user2["description"],
                "url_bandera" => getBandera($user2["country"])
                ]
        );
        /*
        // Enviar como una cookie HTTP-Only
        setcookie("token", $token, [
            "expires" => time() + (60 * 60 * 24 * 3), // Expira en 3 días
            "path" => "/",
            "domain" => "", // Puedes establecerlo según el dominio
            "secure" => true, // Habilítalo solo en HTTPS
            "httponly" => true,
            "samesite" => "Strict"
        ]);
        */
        
        Flight::json(["message" => "Código correcto, recomendamos cambiar tu contraseña.", "token" => $token]);
    } catch (Exception $e) {
        Flight::halt(500, json_encode(["error" => "Error en el servidor: " . $e->getMessage()]));
        return;
    }
});

Flight::route('POST /verify-user', function() {
    $request = json_decode(file_get_contents("php://input"), true);
    $codigo = $request['code'] ?? null;
    $email = $request['email'] ?? null;

    if (!$codigo || !$email) {
        Flight::halt(400, json_encode(["error" => "Código o email no proporcionados."]));
        return;
    }

    try {
        $db = Flight::db();
        
        // Buscar usuario con ese código y email
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND verify_code = ?");
        $stmt->execute([$email, $codigo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            Flight::halt(400, json_encode(["error" => "Código inválido."]));
            return;
        }

        // Marcar la cuenta como verificada
        $stmt = $db->prepare("UPDATE users SET verify = 1, verify_code = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        $emailUser = $user['email'];
        $idUser = $user['id'];
        $nameUser = $user['user'];
        
        $code1 = generarCodigoRecuperacion();
        $code2 = generarCodigoRecuperacion();
        $code3 = generarCodigoRecuperacion();
        
        $stmt2 = $db->prepare("INSERT INTO recovery_user_codes (id_usuario, code_recovery, isUsed) VALUES (?, ?, ?)");
        $stmt2->execute([$idUser, $code1, 0]);
        
        $stmt3 = $db->prepare("INSERT INTO recovery_user_codes (id_usuario, code_recovery, isUsed) VALUES (?, ?, ?)");
        $stmt3->execute([$idUser, $code2, 0]);
        
        $stmt4 = $db->prepare("INSERT INTO recovery_user_codes (id_usuario, code_recovery, isUsed) VALUES (?, ?, ?)");
        $stmt4->execute([$idUser, $code3, 0]);
        
        try{
            $usuarioMail=$user['user'];
            $mailToSend = sendEmail("Codigos de recuperacion para tu cuenta en DDSC: {$usuarioMail}!",
            $usuarioMail,
            "Tu cuenta se verificó correctamente",
            "los siguientes códigos de recuperación son de solo un uso, NO LOS COMPARTAS CON NADIE!. 1. {$code1} 2. {$code2} 3. {$code3}",
            "Ir al sitio",
            getenv('URL_SITE'),
            $emailUser
        );
            
            if($mailToSend){
                Flight::json(["message" => "Cuenta verificada exitosamente. Se han enviado los codigos de recuperación, por favor revisa tu correo electrónico e inicia sesión."]);
            }else{
                Flight::halt(500, json_encode(["error" => "Error al enviar el correo: "]));
                return;
            }
        }catch(Exception $e){
            Flight::halt(500, json_encode(["error" => "Error al enviar el correo: "]));
            return;
        }
    } catch (Exception $e) {
        Flight::halt(500, json_encode(["error" => "Error en el servidor: " . $e->getMessage()]));
        return;
    }
});

Flight::route('PUT /change-password-recovery', function() {
    $request = json_decode(file_get_contents("php://input"), true);
    $codigo = $request['code'] ?? null;
    $email = $request['email'] ?? null;
    $password = $request['password'] ?? null;
    $password = filter_var($password, FILTER_SANITIZE_SPECIAL_CHARS);
    $codigo = filter_var($codigo, FILTER_SANITIZE_SPECIAL_CHARS);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Flight::halt(400, json_encode(["error" => "Correo inválido"]));
        return;
    }
    $options = ['cost' => 10]; // Cost recomendado: 10-14
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT, $options);

    try {
        $db = Flight::db();
        
        // Buscar usuario con ese código y email
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND verify_code = ?");
        $stmt->execute([$email, $codigo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            Flight::halt(400, json_encode(["error" => "Código inválido."]));
            return;
        }

        // Marcar la cuenta como verificada
        $stmt = $db->prepare("UPDATE users SET user_password= ? ,verify_code = NULL WHERE id = ?");
        $stmt->execute([$hashedPassword,$user['id']]);
        
        $emailUser = $user['email'];
        $idUser = $user['id'];
        $nameUser = $user['user'];
        
        try{
            $mailToSend = sendEmail("Contraseña cambiada: {$nameUser}!",
            $nameUser,
            "Tu cuenta cambio de contraseña",
            "Si no reconoces estos cambios, contacta con nuestro soporte, para atender tu caso!",
            "Ir al sitio",
            getenv('URL_SITE'),
            $emailUser
        );
            
            if($mailToSend){
                Flight::json(["message" => "Tu contraseñña fue cambiada correctamente."]);
            }else{
                Flight::halt(500, json_encode(["error" => "Error al enviar el correo: "]));
                return;
            }
        }catch(Exception $e){
            Flight::halt(500, json_encode(["error" => "Error al enviar el correo: "]));
            return;
        }
    } catch (Exception $e) {
        Flight::halt(500, json_encode(["error" => "Error en el servidor: " . $e->getMessage()]));
        return;
    }
});

Flight::route('PUT /recovery-password', function() {
    $request = json_decode(file_get_contents("php://input"), true);
    $email = $request['email'] ?? null;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Flight::halt(400, json_encode(["error" => "Correo inválido"]));
        return;
    }

    try {
        $db = Flight::db();
        
        // Buscar usuario con ese código y email
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            Flight::halt(400, json_encode(["error" => "El correo no existe."]));
            return;
        }
        $codigoVerificacion = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT); 

        // Marcar la cuenta como verificada
        $stmt = $db->prepare("UPDATE users SET verify_code = ? WHERE id = ?");
        $stmt->execute([$codigoVerificacion,$user['id']]);
        
        $emailUser = $user['email'];
        $nameUser = $user['user'];
        $site_url = getenv('URL_SITE');
        try{
            $mailToSend = sendEmail("Solicitud de cambio de contraseña {$nameUser}!",
            $nameUser,
            "Cambia tu contraseña!",
            "Para realizar tu cambio de contraseña es importante ir al siguiente enlace.",
            "Cambiar contraseña",
            "{$site_url}/recuperar?email={$emailUser}&code={$codigoVerificacion}",
            $emailUser
        );
            
            if($mailToSend){
                Flight::json(["message" => "Se envió un correo para continuar con tu solicitud"]);
            }else{
                Flight::halt(500, json_encode(["error" => "Error al enviar el correo: "]));
                return;
            }
        }catch(Exception $e){
            Flight::halt(500, json_encode(["error" => "Error al enviar el correo: "]));
            return;
        }
    } catch (Exception $e) {
        Flight::halt(500, json_encode(["error" => "Error en el servidor: " . $e->getMessage()]));
        return;
    }
});

Flight::route('PUT  /verify-user/id/@id_user', function($id_user) {
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
    
    if($userRol != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para realizar esta acción."]));
        return;
    }
    
    if (!preg_match('/^[0-9]+$/', $id_user)) {
        Flight::halt(400, json_encode(["error" => "Id inválido"]));
        return;
    }

    try {
        $db = Flight::db();
        
        // Buscar usuario con ese código y email
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND verify = 1");
        $stmt->execute([$id_user]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            Flight::halt(400, json_encode(["error" => "Accion inválida ya esta verificado."]));
            return;
        }

        // Marcar la cuenta como verificada
        $stmt = $db->prepare("UPDATE users SET verify = 1, verify_code = NULL WHERE id = ?");
        $stmt->execute([$id_user]);
        
        
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id_user]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $emailUser = $user['email'];
        $idUser = $user['id'];
        $nameUser = $user['user'];
        
        $code1 = generarCodigoRecuperacion();
        $code2 = generarCodigoRecuperacion();
        $code3 = generarCodigoRecuperacion();
        
        $stmt2 = $db->prepare("INSERT INTO recovery_user_codes (id_usuario, code_recovery, isUsed) VALUES (?, ?, ?)");
        $stmt2->execute([$idUser, $code1, 0]);
        
        $stmt3 = $db->prepare("INSERT INTO recovery_user_codes (id_usuario, code_recovery, isUsed) VALUES (?, ?, ?)");
        $stmt3->execute([$idUser, $code2, 0]);
        
        $stmt4 = $db->prepare("INSERT INTO recovery_user_codes (id_usuario, code_recovery, isUsed) VALUES (?, ?, ?)");
        $stmt4->execute([$idUser, $code3, 0]);
        
        try{
            $usuarioMail=$user['user'];
            $mailToSend = sendEmail("Codigos de recuperacion para tu cuenta en DDSC: {$nameUser}!",
            $nameUser,
            "Tu cuenta se verificó correctamente",
            "los siguientes códigos de recuperación son de solo un uso, NO LOS COMPARTAS CON NADIE!. 1. {$code1} 2. {$code2} 3. {$code3}",
            "Ir al sitio",
            getenv('URL_SITE'),
            $usuarioMail
        );
            
            if($mailToSend){
                Flight::json(["message" => "Cuenta verificada exitosamente. Se han enviado los codigos de recuperación al correo del usuario."]);
            }else{
                Flight::halt(500, json_encode(["error" => "Error al enviar el correo: "]));
                return;
            }
        }catch(Exception $e){
            Flight::halt(500, json_encode(["error" => "Error al enviar el correo: "]));
            return;
        }
    } catch (Exception $e) {
        Flight::halt(500, json_encode(["error" => "Error en el servidor: " . $e->getMessage()]));
        return;
    }
});

Flight::route('PUT  /change-user-email/id/@id_user', function($id_user) {
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
    
    if($userRol != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para realizar esta acción."]));
        return;
    }
    
    if (!preg_match('/^[0-9]+$/', $id_user)) {
        Flight::halt(400, json_encode(["error" => "Id inválido"]));
        return;
    }
    $request = json_decode(file_get_contents("php://input"), true);
    
    $email = $request['email'];
    if(empty($email)){
        Flight::halt(400, json_encode(["error" => "Email inválido"]));
        return;
    }

    try {
        $db = Flight::db();

        // Marcar la cuenta como verificada
        $stmt = $db->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->execute([$email,$id_user]);
        
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id_user]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $codigoVerificacion = $user['verify_code'];
        $user = $user['user'];
        $site_url = getenv('URL_SITE');
        try{
            $mailToSend = sendEmail("Confirma tu cuenta DDSC, {$user}!",
            $user,
            "Verifica tu Cuenta",
            "Para acceder a tu cuenta de Doki Doki Spanish Club, es necesario verificar tu cuenta con el siguiente enlace.",
            "Verificar Cuenta",
            "{$site_url}/verificar?email={$email}&code={$codigoVerificacion}",
            $email
        );
            if($mailToSend){
                Flight::json(["message" => "Usuario registrado. Ingresa a tu correo para verificar y acceder la cuenta."]);
            }else{
                Flight::halt(500, json_encode(["error" => "Error al enviar el correo: "]));
            return;
            }
        }catch(Exception $e){
            Flight::halt(500, json_encode(["error" => "Error al enviar el correo: "]));
            return;
        }
    } catch (Exception $e) {
        Flight::halt(500, json_encode(["error" => "Error en el servidor: " . $e->getMessage()]));
        return;
    }
});

Flight::route('PUT  /change-user-rol/id/@id_user', function($id_user) {
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    
    Auth::init();
    
    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }
    $userId = $authData->data->id; // Obtener el ID del usuario desde el token
    $userRol = $authData->data->rol;
    
    if($userRol != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para realizar esta acción."]));
        return;
    }
    
    if (!preg_match('/^[0-9]+$/', $id_user)) {
        Flight::halt(400, json_encode(["error" => "Id inválido"]));
        return;
    }
    
    $request = json_decode(file_get_contents("php://input"), true);
    
    $rol = $request['rol']??1;
    
    if (!preg_match('/^[0-9]+$/', $rol)) {
        Flight::halt(400, json_encode(["error" => "Rol inválido"]));
        return;
    }

    try {
        $db = Flight::db();
    
        // Marcar la cuenta como verificada
        $stmt = $db->prepare("UPDATE users SET id_rol = ? WHERE id = ?");
        $stmt->execute([$rol,$id_user]);
        
        
        if($stmt->execute([$rol,$id_user])){
            Flight::json(["message" => "Rol cambiado correctamnete."]);
        }else{
            Flight::halt(500, json_encode(["error" => "Error al cambiar de rol"]));
            return;
        }
       
    } catch (Exception $e) {
        Flight::halt(500, json_encode(["error" => "Error en el servidor: " . $e->getMessage()]));
        return;
    }
});

Flight::route('PUT /change-user-visibility/id/@id_user', function($id_user){
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
    
    if (!preg_match('/^[0-9]+$/', $id_user)) {
        Flight::halt(400, json_encode(["error" => "id del mod inválido"]));
        return;
    }
    
    
    if($rolUser != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para esta acción."]));
        return;
    }
     // Conectar a la base de datos
    $db = Flight::db();
    
    $request = json_decode(file_get_contents("php://input"), true);
    
    $isActive = $request['active'] ?? 1;
    
    if (!preg_match('/^[0-1]+$/', $isActive)) {
        Flight::halt(400, json_encode(["error" => "Opción inválido"]));
        return;
    }
    
    $stmt = $db->prepare("UPDATE users SET isVisible = ? WHERE id = ?");
    if ($stmt->execute([$isActive,$id_user] )) {
        Flight::json(["message" => "El usuario cambio de visibilidad."]);
    } else {
        Flight::halt(500, json_encode(["error" => "Error al actualizar la restricción."]));
        return;
    }
});

Flight::route('POST /register-user', function() {
    
    // Obtener los datos enviados desde el frontend
    $request = json_decode(file_get_contents("php://input"), true);

    // Validar que los datos requeridos estén presentes || !isset($request['token'])
    if (!isset($request['user']) || !isset($request['password']) || !isset($request['email']) || !isset($request['country']) ) {
        Flight::halt(400, json_encode(["error" => "Faltan datos obligatorios."]));
        return;
    }
    // $token = $request['token'];
    $user = $request['user'];
    $password = $request['password'];
    $email = $request['email'];
    $country = $request['country'];
    $dominio = explode('@', $email)[1]; // Extrae el dominio

    if (!in_array($dominio, ['gmail.com', 'outlook.com', 'hotmail.com', 'traduction-club.live'])) {
       Flight::halt(400, json_encode(["error" => "Correo electrónico no valido."]));
        return;
    }
    /*
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$token");
    $responseData = json_decode($response, true);

    if (!$responseData['success']) {
        Flight::halt(400, json_encode(["error" => "Captcha incorrecto."]));
    }
    
    */

    // Validar que el usuario solo contenga letras y números
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $user)) {
        Flight::halt(400, json_encode(["error" => "Nombre de usuario inválido"]));
        return;
    }
    // Si es válido, se sanitiza
    $user = filter_var($user, FILTER_SANITIZE_SPECIAL_CHARS);


    // Validar que el usuario solo contenga letras y números
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[_*#$%-])[A-Za-z\d_*#$%-]{8,12}$/', $password)) {
        Flight::halt(400, json_encode(["error" => "La contraseña debe tener entre 8 a 12 caracteres, incluyendo letras, números, y puede contener al menos uno de los siguientes caracteres especiales _ * # $ % -."]));
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Flight::halt(400, json_encode(["error" => "Correo inválido"]));
        return;
    }


    // Validar que el usuario solo contenga letras y números
    
    $alias = $user;

    $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', str_replace(",", "",str_replace(".","",str_replace(" ", "-", strtolower(trim($alias))))));

    // Hashear la contraseña antes de guardarla
    $options = ['cost' => 10]; // Cost recomendado: 10-14
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT, $options);

    try {
        // Conectar a la base de datos
        $db = Flight::db();
        
        // Verificar si el usuario ya existe
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            Flight::halt(400, json_encode(["error" => "El correo electrónico ya existe."]));
            return;
        }

        $stmt = $db->prepare("SELECT id FROM users WHERE user = ?");
        $stmt->execute([$user]);
        if ($stmt->fetch()) {
            Flight::halt(400, json_encode(["error" => "No es posible guardar el nombre de usuario debido a un registro previo."]));
            return;
        }

        $codigoVerificacion = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT); // Código de 6 dígitos

        // Insertar el usuario en la base de datos
        $stmt = $db->prepare("INSERT INTO users (user, user_password,alias, slug, country, email, verify, verify_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user, $hashedPassword,  $alias,$slug, $country, $email, 0, $codigoVerificacion]);

        // Responder con éxito
        // mail($email, "Tu código de verificación", "Tu código es: $codigoVerificacion");
        $site_url = getenv('URL_SITE');
        try{
            $mailToSend = sendEmail("Confirma tu cuenta DDSC, {$user}!",
            $user,
            "Verifica tu Cuenta",
            "Para acceder a tu cuenta de Doki Doki Spanish Club, es necesario verificar tu cuenta con el siguiente enlace.",
            "Verificar Cuenta",
            "{$site_url}/verificar?email={$email}&code={$codigoVerificacion}",
            $email
        );
            if($mailToSend){
                Flight::json(["message" => "Usuario registrado. Ingresa a tu correo para verificar y acceder la cuenta."]);
            }else{
                Flight::halt(500, json_encode(["error" => "Error al enviar el correo: " ]));
            return;
            }
        }catch(Exception $e){
            Flight::halt(500, json_encode(["error" => "Error al enviar el correo: " ]));
            return;
        }
    } catch (Exception $e) {
        Flight::halt(500, json_encode(["error" => "Error en el servidor: " . $e->getMessage()]));
        return;
    }
});

Flight::route('POST /update-photo-team/@id_team', function($id_team) {
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

    $userId = $authData->data->id; // Obtener el ID del usuario desde el token
    
    if (!preg_match('/^[0-9]+$/', $id_team)) {
        Flight::halt(400, json_encode(["error" => "Opción inválido"]));
        return;
    }
    
    $db = Flight::db();
    
    $stmt = $db->prepare("SELECT * FROM membership_teams WHERE id_user = ? AND id_team = ?, AND main=1");
    $stmt->execute([$userId,$id_team]);
    
    if (!$stmt->execute([$userId,$id_team])) {
        Flight::json(["error" => "No tienes los permisos para realizar esta acción, solo el miembro principal. "], 400);
        return;
    }
    
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
    
    

    $uploadDir = 'images/teams/'.$id_team;
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = 'photo_' . uniqid() . '.' . $extension;
    
    // Crear la carpeta si no existe
   if(!file_exists($uploadDir)){
        mkdir($uploadDir, 0777, true);
    }

    // Obtener la ruta actual desde la base de datos
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $existingFilePath = $result['url_logo'];
        // Verificar si el archivo existe en el servidor
        if($result["url_logo"]!="gui/Imagen-no-disponible.jpg"){
            if (file_exists($existingFilePath)) {
                unlink($existingFilePath); // Eliminar archivo existente
            }
        }
    }else{
        Flight::json(["error" => "No se puede actualizar el logo, no existe en el servidor"], 404);
        return;
    }

    // Definir nueva ruta
    $newFilePath = $uploadDir."/" . $newFileName;
    $webpFilePath = "$uploadDir/" . pathinfo($newFileName, PATHINFO_FILENAME) . ".webp"; // Ruta WebP
    $newFilePathSql = "images/teams/". $id_team."/". pathinfo($newFileName, PATHINFO_FILENAME) . ".webp"; // Ruta WebP en BD

    // Mover el nuevo archivo
    if (move_uploaded_file($file['tmp_name'],  $newFilePath)) {
        
        if (convertirAWebP($newFilePath, $webpFilePath)) {
            // Actualizar la ruta en la base de datos sin eliminar el registro
            $updateStmt = $db->prepare("UPDATE teams SET url_logo = ? WHERE id = ?");
            $updateStmt->execute([$newFilePathSql, $userId]);
            Flight::json(["message" => "Logo del equipo reemplazado correctamente"]);
        } else {
            Flight::json(["error" => "Error en la conversión a WebP"], 500);
        }
        
    } else {
        Flight::json(["error" => "Error al subir el archivo"], 500);
        return;
    }
});

Flight::route('PUT /change-description-team/@id_team', function($id_team){
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
    
    if (!preg_match('/^[0-9]+$/', $id_team)) {
        Flight::halt(400, json_encode(["error" => "Opción inválido"]));
        return;
    }

    $userId = $authData->data->id; // Obtener el ID del usuario desde el token
    // Conectar a la base de datos
    $db = Flight::db();
    
    $stmt = $db->prepare("SELECT * FROM membership_teams WHERE id_user = ? AND id_team = ?, AND main=1");
    $stmt->execute([$userId,$id_team]);
    
    if (!$stmt->execute([$userId,$id_team])) {
        Flight::json(["error" => "No tienes los permisos para realizar esta acción, solo el miembro principal. "], 400);
        return;
    }

    $description = $request['descripcion'] ?? '¡Hola somos un equipo!';
    
    if (!preg_match('/^[\p{L}\p{N}\p{P}\p{Zs}\n\r]{10,200}$/u', $description)) {
        Flight::halt(400, json_encode(["error" => "La descripción no es válida. Debe tener entre 10 y 200 caracteres y solo caracteres permitidos."]));
        return;
    }
    
    
    $stmt = $db->prepare("UPDATE teams SET description = ? WHERE id = ?");
    if ($stmt->execute([$description, $id_team])) {
        Flight::json(["message" => "Información actualizada correctamente"]);
    } else {
        Flight::halt(500, json_encode(["error" => "Error al actualizar la informacion de la descripción."]));
        return;
    }
});

Flight::route('POST /update-photo-team-admin/@id_team', function($id_team) {
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

    $userId = $authData->data->id; // Obtener el ID del usuario desde el token
    $userRol = $authData->data->rol;
    
    if($userRol != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para realizar esta acción."]));
        return;
    }
    
    if (!preg_match('/^[0-9]+$/', $id_team)) {
        Flight::halt(400, json_encode(["error" => "Opción inválido"]));
        return;
    }
    
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
    
    

    $uploadDir = 'images/teams/'.$id_team;
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = 'photo_' . uniqid() . '.' . $extension;
    
    // Crear la carpeta si no existe
   if(!file_exists($uploadDir)){
        mkdir($uploadDir, 0777, true);
    }

    // Obtener la ruta actual desde la base de datos
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $existingFilePath = $result['url_logo'];
        // Verificar si el archivo existe en el servidor
        if($result["url_logo"]!="gui/Imagen-no-disponible.jpg"){
            if (file_exists($existingFilePath)) {
                unlink($existingFilePath); // Eliminar archivo existente
            }
        }
    }else{
        Flight::json(["error" => "No se puede actualizar el logo, no existe en el servidor"], 404);
        return;
    }

    // Definir nueva ruta
    $newFilePath = $uploadDir."/" . $newFileName;
    $webpFilePath = "$uploadDir/" . pathinfo($newFileName, PATHINFO_FILENAME) . ".webp"; // Ruta WebP
    $newFilePathSql = "images/teams/". $id_team."/". pathinfo($newFileName, PATHINFO_FILENAME) . ".webp"; // Ruta WebP en BD

    // Mover el nuevo archivo
    if (move_uploaded_file($file['tmp_name'],  $newFilePath)) {
        
        if (convertirAWebP($newFilePath, $webpFilePath)) {
            // Actualizar la ruta en la base de datos sin eliminar el registro
            $updateStmt = $db->prepare("UPDATE teams SET url_logo = ? WHERE id = ?");
            $updateStmt->execute([$newFilePathSql, $userId]);
            Flight::json(["message" => "Logo del equipo reemplazado correctamente"]);
        } else {
            Flight::json(["error" => "Error en la conversión a WebP"], 500);
        }
        
    } else {
        Flight::json(["error" => "Error al subir el archivo"], 500);
        return;
    }
});

Flight::route('PUT /change-description-team-admin/@id_team', function($id_team){
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
    $userRol = $authData->data->rol;
    
    if($userRol != 4){
        Flight::halt(401, json_encode(["error" => "No tienes los permisos para realizar esta acción."]));
        return;
    }
    
    // Conectar a la base de datos
    $db = Flight::db();
    
    if (!preg_match('/^[0-9]+$/', $id_team)) {
        Flight::halt(400, json_encode(["error" => "Opción inválido"]));
        return;
    }

    $description = $request['descripcion'] ?? '¡Hola somos un equipo!';
    
    if (!preg_match('/^[\p{L}\p{N}\p{P}\p{Zs}\n\r]{10,200}$/u', $description)) {
        Flight::halt(400, json_encode(["error" => "La descripción no es válida. Debe tener entre 10 y 200 caracteres y solo caracteres permitidos."]));
        return;
    }
    
    
    $stmt = $db->prepare("UPDATE teams SET description = ? WHERE id = ?");
    if ($stmt->execute([$description, $id_team])) {
        Flight::json(["message" => "Información actualizada correctamente"]);
    } else {
        Flight::halt(500, json_encode(["error" => "Error al actualizar la informacion de la descripción."]));
        return;
    }
});

Flight::route('POST /register-user-admin', function() {
    
    // Obtener los datos enviados desde el frontend
    $request = json_decode(file_get_contents("php://input"), true);

    // Validar que los datos requeridos estén presentes || !isset($request['token'])
    if (!isset($request['user']) || !isset($request['password']) || !isset($request['email']) || !isset($request['country']) || !isset($request['rol'])) {
        Flight::halt(400, json_encode(["error" => "Faltan datos obligatorios."]));
        return;
    }
    // $token = $request['token'];
    $user = $request['user'];
    $password = $request['password'];
    $email = $request['email'];
    $country = $request['country'];
    $dominio = explode('@', $email)[1]; // Extrae el dominio

    if (!in_array($dominio, ['gmail.com', 'outlook.com', 'hotmail.com'])) {
       Flight::halt(400, json_encode(["error" => "Correo electrónico no valido."]));
        return;
    }

    // Validar que el usuario solo contenga letras y números
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $user)) {
        Flight::halt(400, json_encode(["error" => "Nombre de usuario inválido"]));
        return;
    }
    // Si es válido, se sanitiza
    $user = filter_var($user, FILTER_SANITIZE_SPECIAL_CHARS);


    // Validar que el usuario solo contenga letras y números
    if (!preg_match('/^[a-zA-Z0-9_*#$%-]{8,}$/', $password)) {
        Flight::halt(400, json_encode(["error" => "La contraseña debe tener al menos 8 caracteres y contener letras y números."]));
        return;
    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Flight::halt(400, json_encode(["error" => "Correo inválido"]));
        return;
    }


    // Validar que el usuario solo contenga letras y números
    
    $alias = $user;

    $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', str_replace(",", "",str_replace(".","",str_replace(" ", "-", strtolower(trim($alias))))));

    // Hashear la contraseña antes de guardarla
    $options = ['cost' => 10]; // Cost recomendado: 10-14
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT, $options);

    try {
        // Conectar a la base de datos
        $db = Flight::db();
        
        // Verificar si el usuario ya existe
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            Flight::halt(400, json_encode(["error" => "El correo electrónico ya existe."]));
            return;
        }

        $stmt = $db->prepare("SELECT id FROM users WHERE user = ?");
        $stmt->execute([$user]);
        if ($stmt->fetch()) {
            Flight::halt(400, json_encode(["error" => "No es posible guardar el nombre de usuario debido a un registro previo."]));
            return;
        }


        // Insertar el usuario en la base de datos
        $stmt = $db->prepare("INSERT INTO users (user, user_password,alias, slug, country, email, verify) VALUES (?, ?, ?, ?, ?, ?, ?)");

        // Responder con éxito
        // mail($email, "Tu código de verificación", "Tu código es: $codigoVerificacion");

        if($stmt->execute([$user,$hashedPassword,$alias,$slug,1,$email,1])){
            Flight::json(["message" => "Usuario registrado. Ingresa a tu correo para verificar y acceder la cuenta."]);
        }else{
            Flight::halt(500, json_encode(["error" => "Error al enviar el correo: " ]));
        }
    } catch (Exception $e) {
        Flight::halt(500, json_encode(["error" => "Error en el servidor: " . $e->getMessage()]));
        return;
    }
});

Flight::route('PUT /change-password', function(){
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
    $userName = $authData->data->nombre;
    $oldPassword = $request['old_pass'] ?? '';
    $newPassword = $request['new_pass'] ?? '';

    // Validar que los datos no estén vacíos
    if (empty($oldPassword) || empty($newPassword)) {
        Flight::halt(400, json_encode(["error" => "Se requieren ambas contraseñas."]));
        return;
    }

    // Validar nueva contraseña (mínimo 8 caracteres, letras y números)
    if (!preg_match('/^[a-zA-Z0-9_*#$%^&]{8,}$/', $newPassword)) {
        Flight::halt(400, json_encode(["error" => "La nueva contraseña debe tener al menos 8 caracteres y contener letras y números."]));
        return;
    }
    
    if (!preg_match('/^[a-zA-Z0-9_*#$%^&]{8,}$/', $oldPassword)) {
        Flight::halt(400, json_encode(["error" => "La nueva actual debe tener al menos 8 caracteres y contener letras y números."]));
        return;
    }

    // Conectar a la base de datos
    $db = Flight::db();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user2 = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario existe
    if (!$user2) {
        Flight::halt(404, json_encode(["error" => "Usuario no encontrado."]));
        return;
    }
    
    $userMail = $user2['email'];

    // Verificar contraseña actual
    if (!password_verify($oldPassword, $user2['user_password'])) {
        Flight::halt(401, json_encode(["error" => "La contraseña actual es incorrecta."]));
        return;
    }

    // Hashear la nueva contraseña con un costo de 12
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => 10]);

    // Actualizar la contraseña en la base de datos
    $stmt = $db->prepare("UPDATE users SET user_password = ? WHERE id = ?");
    if ($stmt->execute([$hashedPassword, $userId])) {
        try{
            $mailToSend = sendEmail("Tu contraseña fue cambiada {$userName}!",
            $userName,
            "Tu contraseña fue actualizada",
             "Tu contraseña se cambió correctamente. Si no realizaste este cambio, contacta a soporte por nuestras redes sociales (Discord: Team DDSC Web).",
            "Ir al Sitio",
            getenv('URL_SITE'),
            $userMail
        );
            if($mailToSend){
                Flight::json(["message" => "Contraseña actualizada correctamente. La nueva contraseña será necesaria en el siguiente inicio de sesión."]);
            }else{
                Flight::halt(500, json_encode(["error" => "Error al enviar el correo: "]));
                return;
            }
        }catch(Exception $e){
            Flight::halt(500, json_encode(["error" => "Error al enviar el correo: "]));
            return;
        }
        
    } else {
        Flight::halt(500, json_encode(["error" => "Error al actualizar la contraseña."]));
        return;
    }
});

Flight::route('PUT /seset-password', function(){
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
    $userName = $authData->data->nombre;
    $newPassword = $request['new_pass'] ?? '';

    // Validar que los datos no estén vacíos
    if (empty($newPassword)) {
        Flight::halt(400, json_encode(["error" => "Se una nueva contraseña."]));
        return;
    }

    // Validar nueva contraseña (mínimo 8 caracteres, letras y números)
    if (!preg_match('/^[a-zA-Z0-9_*#$%^&]{8,}$/', $newPassword)) {
        Flight::halt(400, json_encode(["error" => "La nueva contraseña debe tener al menos 8 caracteres y contener letras y números."]));
        return;
    }
    

    // Conectar a la base de datos
    $db = Flight::db();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user2 = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario existe
    if (!$user2) {
        Flight::halt(404, json_encode(["error" => "Usuario no encontrado."]));
        return;
    }

    // Hashear la nueva contraseña con un costo de 12
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => 10]);

    // Actualizar la contraseña en la base de datos
    $stmt = $db->prepare("UPDATE users SET user_password = ? WHERE id = ?");
    if ($stmt->execute([$hashedPassword, $userId])) {
        Flight::json(["message" => "Contraseña actualizada correctamente. La nueva contraseña será necesaria en el siguiente inicio de sesión."]);
    } else {
        Flight::halt(500, json_encode(["error" => "Error al actualizar la contraseña."]));
        return;
    }
});

Flight::route('PUT /change-description-user', function(){
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

    $description = $request['descripcion'] ?? '¡Hola soy un miembro del club!';
    
    if (!preg_match('/^[\p{L}\p{N}\p{P}\p{Zs}\n\r]{10,200}$/u', $description)) {
        Flight::halt(400, json_encode(["error" => "La descripción no es válida. Debe tener entre 10 y 200 caracteres y solo caracteres permitidos."]));
        return;
    }
    
    // Conectar a la base de datos
    $db = Flight::db();
    
    $stmt2 = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt2->execute([$userId ]);
    $user2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    

    // Verificar si el usuario existe
    if (!$user2) {
        Flight::halt(404, json_encode(["error" => "Usuario no encontrado."]));
        return;
    }


    
    $stmt = $db->prepare("UPDATE users SET description = ? WHERE id = ?");
    if ($stmt->execute([$description, $userId])) {
        Flight::json(["message" => "Información actualizada correctamente. Recomendamos volver a iniciar sesión para ver los cambios."]);
    } else {
        Flight::halt(500, json_encode(["error" => "Error al actualizar la informacion de la descripción."]));
        return;
    }
});

Flight::route('POST /update-photo-user', function() {
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

    $userId = $authData->data->id; // Obtener el ID del usuario desde el token
    
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

    $uploadDir = 'images/photos/'.$userId;
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = 'photo_' . uniqid() . '.' . $extension;
    
    // Crear la carpeta si no existe
   if(!file_exists($uploadDir)){
        mkdir($uploadDir, 0777, true);
    }

    // Obtener la ruta actual desde la base de datos
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $existingFilePath = $result['url_logo'];
        // Verificar si el archivo existe en el servidor
        if($result["url_logo"]!="gui/Imagen-no-disponible.jpg"){
            if (file_exists($existingFilePath)) {
                unlink($existingFilePath); // Eliminar archivo existente
            }
        }
    }else{
        Flight::json(["error" => "No se puede actualizar el logo, no existe en el servidor"], 404);
        return;
    }

    // Definir nueva ruta
    $newFilePath = $uploadDir."/" . $newFileName;
    $webpFilePath = "$uploadDir/" . pathinfo($newFileName, PATHINFO_FILENAME) . ".webp"; // Ruta WebP
    $newFilePathSql = "images/photos/". $userId."/". pathinfo($newFileName, PATHINFO_FILENAME) . ".webp"; // Ruta WebP en BD

    // Mover el nuevo archivo
    if (move_uploaded_file($file['tmp_name'],  $newFilePath)) {
        
        if (convertirAWebP($newFilePath, $webpFilePath)) {
            // Actualizar la ruta en la base de datos sin eliminar el registro
            $updateStmt = $db->prepare("UPDATE users SET url_logo = ? WHERE id = ?");
            $updateStmt->execute([$newFilePathSql, $userId]);
            Flight::json(["message" => "Foto de perfil reemplazada correctamente. Recomendamos volver a iniciar sesión para ver los datos actualizados."]);
        } else {
            Flight::json(["error" => "Error en la conversión a WebP"], 500);
        }
        
    } else {
        Flight::json(["error" => "Error al subir el archivo"], 500);
        return;
    }
});

Flight::route('POST /update-wallpaper', function() {
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

    $userId = $authData->data->id; // Obtener el ID del usuario desde el token
    
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

    $uploadDir = 'images/photos/'.$userId;
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = 'wallpaper_' . uniqid() . '.' . $extension;
    
    // Crear la carpeta si no existe
   if(!file_exists($uploadDir)){
        mkdir($uploadDir, 0777, true);
    }

    // Obtener la ruta actual desde la base de datos
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $existingFilePath = $result['url_wallpaper'];
        // Verificar si el archivo existe en el servidor
        if($result["url_wallpaper"]!="gui/Winter_Neva.png"){
            if (file_exists($existingFilePath)) {
                unlink($existingFilePath); // Eliminar archivo existente
            }
        }
    }else{
        Flight::json(["error" => "No se puede actualizar el fondo de pantalla, no existe en el servidor"], 404);
        return;
    }

    // Definir nueva ruta
    $newFilePath = $uploadDir."/" . $newFileName;
    $fileBaseName = "wallpaper_" . uniqid();
    $webpFilePath = "$uploadDir/" . $fileBaseName . ".webp"; // Ruta WebP
    
    $newFilePathSql = "images/photos/". $userId."/". $fileBaseName . ".webp"; // Ruta WebP en BD

    // Mover el nuevo archivo
    if (move_uploaded_file($file['tmp_name'],  $newFilePath)) {
        
    
        if (convertirAWebP($newFilePath, $webpFilePath)) {
            $updateStmt = $db->prepare("UPDATE users SET url_wallpaper = ? WHERE id = ?");
            $updateStmt->execute([$newFilePathSql, $userId]);
        Flight::json(["message" => "Archivo de fondo de pantalla reemplazado correctamente. Recomendamos volver a iniciar sesión para ver los datos actualizados."]);
        } else {
            Flight::json(["error" => "Error al convertir la imagen"], 500);
        }
         
    } else {
        Flight::json(["error" => "Error al subir el archivo"], 500);
    }
});

Flight::route('POST /update-banner-user', function() {
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    // Obtener el token desde la cookie HTTP-Only
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    Auth::init();

    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }

    $userId = $authData->data->id; // Obtener el ID del usuario desde el token
    
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

    $uploadDir = 'images/photos/'.$userId;
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = 'banner_' . uniqid() . '.' . $extension;
    
    // Crear la carpeta si no existe
   if(!file_exists($uploadDir)){
        mkdir($uploadDir, 0777, true);
    }

    // Obtener la ruta actual desde la base de datos
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $existingFilePath = $result['url_banner'];
        // Verificar si el archivo existe en el servidor
        if($result["url_wallpaper"]!="gui/Winter_Neva.png"){
            if (file_exists($existingFilePath)) {
                unlink($existingFilePath); // Eliminar archivo existente
            }
        }
    }else{
        Flight::json(["error" => "No se puede actualizar el banner, no existe en el servidor"], 404);
        return;
    }

    // Definir nueva ruta
    $newFilePath = $uploadDir."/" . $newFileName;
    $webpFilePath = "$uploadDir/" . pathinfo($newFileName, PATHINFO_FILENAME) . ".webp"; // Ruta WebP
    $newFilePathSql = "images/photos/". $userId."/". pathinfo($newFileName, PATHINFO_FILENAME) . ".webp"; // Ruta WebP en BD

    // Mover el nuevo archivo
    if (move_uploaded_file($file['tmp_name'],  $newFilePath)) {
        
        if (convertirAWebP($newFilePath, $webpFilePath)) {
            // Actualizar la ruta en la base de datos sin eliminar el registro
            $updateStmt = $db->prepare("UPDATE users SET url_banner = ? WHERE id = ?");
            $updateStmt->execute([$newFilePathSql, $userId]);
            Flight::json(["message" => "Archivo de banner reemplazado correctamente. Recomendamos volver a iniciar sesión para ver los datos actualizados."]);
        } else {
            Flight::json(["error" => "Error en la conversión a WebP"], 500);
        }
        
    } else {
        Flight::json(["error" => "Error al subir el archivo"], 500);
    }
});
/*
Flight::route('GET /vapid-public-key', function() {
    Flight::json(["publicKey" => "BIuTU82pi4qBD-sNaJNezG1kGs_cA5-Qb_ZaVQ1ZCj7PxvoqBSDe9q33cZO4BmVZRL-97GAYzu18rPxf7lSw1pk"]);
});
*/

Flight::route('POST /add-mod-terminated', function(){
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

    $description = $request['id_mod'] ?? 1;
    
    if (!preg_match('/^[0-9]$/u', $description)) {
        Flight::halt(400, json_encode(["error" => "El id del mod no es valido."]));
        return;
    }
    
    // Conectar a la base de datos
    $db = Flight::db();
    
    $stmt2 = $db->prepare("SELECT * FROM terminated_mods WHERE id_mod = ? AND id_usuario = ?");
    $stmt2->execute([$description,$userId ]);
    $user2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    

    // Verificar si el usuario existe
    if ($user2) {
        Flight::halt(404, json_encode(["error" => "Ya existe un registro previo."]));
        return;
    }


    
    $stmt = $db->prepare("INSERT INTO terminated_mods (id_mod, id_usuario) VALUES (?, ?)");
    if ($stmt->execute([$description, $userId])) {
        Flight::json(["message" => "Mod agregado correctamente"]);
    } else {
        Flight::halt(500, json_encode(["error" => "Error al agregarel mod."]));
        return;
    }
});


Flight::route('POST /subscribe', function() {
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    
    Auth::init();

    $authData = Auth::verificarToken($token); // Verificar y decodificar el token

    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
        return;
    }

    $userId = $authData->data->id; // Obtener el ID del usuario desde el token
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['endpoint'], $data['keys']['p256dh'], $data['keys']['auth'])) {
        Flight::halt(400, json_encode(["error" => "Datos de suscripción inválidos"]));
    }

    $db = Flight::db();

    // Verificar si la suscripción ya existe
    $stmt = $db->prepare("SELECT id FROM subscriptions WHERE user_id = ? AND endpoint = ?");
    $stmt->execute([$userId, $data['endpoint']]);
    $existingSubscription = $stmt->fetch(PDO::FETCH_ASSOC);
    
    
    // Si ya existe, no insertes una nueva
    if ($existingSubscription) {
        Flight::json(["message" => "Suscripción ya existe."]);
        return; // Salir sin insertar el registro
    }
    
    
    // Si no existe, insertamos la nueva suscripción
    $stmt = $db->prepare("INSERT INTO subscriptions (user_id, endpoint, p256dh, auth) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $data['endpoint'], $data['keys']['p256dh'], $data['keys']['auth']]);

    Flight::json(["message" => "Suscripción guardada"]);
});

Flight::route('POST /send-push', function() {
    
    // Auth::init();
    $data = json_decode(file_get_contents("php://input"), true);
    
    
    
    // Obtener el token desde la cookie HTTP-Only
    // if (!isset($_COOKIE['token'])) {
    //     Flight::halt(401, json_encode(["error" => "No estás autenticado."]));
    // }
    // $token = $_COOKIE['token']; // Extraer el token de la cookie
    // $authData = Auth::verificarToken($token); // Verificar y decodificar el token
    // if (!$authData) {
    //     Flight::halt(401, json_encode(["error" => "Token inválido o expirado."]));
    // }
    // $userId = $authData->data->id; // Obtener el ID del usuario desde el token
    // 
    
    $titleNoti = $data['title'] ?? null;
    $bodyNoti = $data['body'] ?? null;
    $enlaceNoti = !empty($data["url_noti"]) ? $data["url_noti"] : getenv('URL_SITE');
    $iconNoti = !empty($data["icon_noti"]) ? $data["icon_noti"] : 'https://www.dokidokispanish.club/assets/gui/Logo_DDSC.png';
    
    // Validar que los datos obligatorios no estén vacíos
    if (empty($titleNoti) || empty($bodyNoti)) {
        Flight::halt(400, json_encode(["error" => "Los campos 'title' y 'body' son obligatorios."]));
        return;
    }
    
    $db = Flight::db();
    $stmt = $db->query("SELECT * FROM subscriptions");
    $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $vapidKeys = [
        'subject' => 'mailto:soporte@dokidokispanish.club',
        'publicKey' => 'BIuTU82pi4qBD-sNaJNezG1kGs_cA5-Qb_ZaVQ1ZCj7PxvoqBSDe9q33cZO4BmVZRL-97GAYzu18rPxf7lSw1pk',
        'privateKey' => 'rlwhiDtAYDeELyYC5fEDRtk3jIIjPTcryTsLXzKa6oQ',
    ];
    
    $webPush = new WebPush([
        'VAPID' => $vapidKeys
    ]);
    
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
            'title' => $titleNoti,
            'body' => $bodyNoti,
            'icon' => $iconNoti,
            'data' => ['url' => $enlaceNoti] // 🔹 Agregamos la URL
        ]));
    }
    
    
    
    foreach ($webPush->flush() as $report) {
        if ($report->isSuccess()) {
            echo "✅ Notificación enviada a {$report->getRequest()->getUri()}\n";
        } else {
            echo "❌ Error enviando notificación: {$report->getReason()}\n";
        }
    }
    
    // Enviar respuesta JSON con el resultado
    Flight::json([
        "message" => "Notificaciones enviadas",
    ]);
    
});

//funciones

function generarCodigoRecuperacion($longitudSegmento = 8, $segmentos = 3) {
    $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $codigo = [];
    $max = strlen($caracteres) - 1;

    for ($i = 0; $i < $segmentos; $i++) {
        $segmento = '';
        for ($j = 0; $j < $longitudSegmento; $j++) {
            $segmento .= $caracteres[random_int(0, $max)];
        }
        $codigo[] = $segmento;
    }

    return implode('-', $codigo);
}

// comentarios


Flight::route('GET /comments/byIDMod/@id', function($id) {
    $db = Flight::db();
    
    if (!$id) {
        Flight::halt(400, json_encode(["error" => "No hay un ID"]));
        return;
    }
    
    if (!preg_match('/^[0-9]+$/', $id)) {
        Flight::halt(400, json_encode(["error" => "Id del mod no válido"]));
        return;
    }

    // Verificar si el mod existe
    $smtSlug = $db->prepare("SELECT id FROM mods WHERE id = ?");
    $smtSlug->execute([$id]);
    $mod = $smtSlug->fetch(PDO::FETCH_ASSOC);

    if (!$mod) {
        Flight::halt(404, json_encode(["error" => "Mod no encontrado"]));
        return;
    }

    // Obtener comentarios con IDs de usuario
    $stmt = $db->prepare("
        SELECT 
            c.id,
            c.comment,
            c.stars,
            c.created,
            u.id AS usuario_id
        FROM puntuation c
        JOIN users u ON c.id_user = u.id
        WHERE c.id_mod = :id
        ORDER BY c.created DESC
    ");
    $stmt->bindParam(':id', $mod['id'], PDO::PARAM_INT);
    $stmt->execute();
    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$comentarios) {
        Flight::json(["response" => "success", "results" => ["stats" => [], "comments" => []]]);
        return;
    }

    // Obtener IDs únicos de usuarios que comentaron
    $idsUsuarios = array_unique(array_column($comentarios, 'usuario_id'));

    // Traer la información de usuarios (misma lógica que /users)
    $in  = str_repeat('?,', count($idsUsuarios) - 1) . '?';
    $queryUsers = "
        SELECT u.*, r.nombre AS rol_nombre
        FROM users u
        INNER JOIN roles r ON u.id_rol = r.id
        WHERE u.id IN ($in) AND u.isVisible = 1
    ";
    $stmtUsers = $db->prepare($queryUsers);
    $stmtUsers->execute($idsUsuarios);
    $usuarios = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

    // Procesar con tu función personalizada
    $usuarios = comprobarInfoUsers2($usuarios);

    // Indexar por ID para fácil acceso
    $mapUsuarios = [];
    foreach ($usuarios as $u) {
        $mapUsuarios[$u['id']] = $u;
    }

    // Añadir info de usuario a cada comentario
    foreach ($comentarios as &$c) {
        $c['usuario'] = $mapUsuarios[$c['usuario_id']] ?? null;
        unset($c['usuario_id']);
    }

    // Obtener promedio y distribución de estrellas
    $avgStmt = $db->prepare("
        SELECT 
            ROUND(AVG(stars), 2) AS promedio,
            COUNT(*) AS total,
            SUM(stars = 5) AS estrellas_5,
            SUM(stars = 4) AS estrellas_4,
            SUM(stars = 3) AS estrellas_3,
            SUM(stars = 2) AS estrellas_2,
            SUM(stars = 1) AS estrellas_1
        FROM puntuation
        WHERE id_mod = :id
    ");
    $avgStmt->bindParam(':id', $mod['id'], PDO::PARAM_INT);
    $avgStmt->execute();
    $stats = $avgStmt->fetch(PDO::FETCH_ASSOC);

    Flight::json([
        "response" => "success",
        "results" => [
            'stats' => [
                'media' => (float)$stats['promedio'],
                'total' => (int)$stats['total'],
                'dist' => [
                    '1' => (int)$stats['estrellas_1'],
                    '2' => (int)$stats['estrellas_2'],
                    '3' => (int)$stats['estrellas_3'],
                    '4' => (int)$stats['estrellas_4'],
                    '5' => (int)$stats['estrellas_5'],
                ]
            ],
            'comments' => $comentarios
        ]
    ]);
});


Flight::route('POST /create-comment', function() {
    $req = Flight::request();

    // Leer el token directamente del header Authorization
    $token = $req->getHeader('Authorization');
    
    if (!$token) {
        Flight::halt(401, json_encode(["error" => "No autenticado"]));
        return;
    }
    
    Auth::init();

    $authData = Auth::verificarToken($token);
    if (!$authData) {
        Flight::halt(401, json_encode(["error" => "Token inválido o expirado, vuelve a iniciar sesión."]));
        return;
    }

    $userId = $authData->data->id;
    $alias = $authData->data->alias;
    $db = Flight::db();
    $data = Flight::request()->data;
    
    if (!preg_match('/^[\p{L}\p{N}\p{P}\p{Zs}\n\r]{10,200}$/u', $data['comment'])) {
        Flight::halt(400, json_encode(["error" => "El comentario no es válido. Debe tener entre 10 y 200 caracteres y solo caracteres permitidos."]));
        return;
    }
    
    $stmtMods = $db->prepare("SELECT * FROM puntuation WHERE id_mod = :idMod AND id_user = :id_user");
    $stmtMods->execute([':idMod' => $data['id_mod'], ":id_user"=>$userId]);
    if($stmtMods->fetch()){
        Flight::halt(401, json_encode(["error" => "Ya existe una valoración a este mod"]));
        return;
    }

    $stmt = $db->prepare("INSERT INTO puntuation (id_user, id_mod, comment, stars) VALUES (:id_user, :id_mod, :contenido, :stars)");
    $result = $stmt->execute([
        ':id_user' => $userId,
        ':id_mod' => $data['id_mod'],
        ':contenido' => $data['comment'],
        ':stars' => isset($data['stars']) ? $data['stars'] : 1
    ]);

    if (!$result) {
        die(json_encode(['error' => 'No se pudo guardar el comentario', 'debug' => $stmt->errorInfo()]));
    }

    $stmtMods = $db->prepare("SELECT * FROM mods WHERE id = :idMod");
    $stmtMods->execute([':idMod' => $data['id_mod']]);
    $infoMods = $stmtMods->fetch();

    $stmtLogo = $db->prepare("SELECT * FROM imagenes_mod WHERE id = :idMod AND id_tipo_imagen = 1");
    $stmtLogo->execute([':idMod' => $data['id_mod']]);
    $infoLogo = $stmtLogo->fetch();

    $stmtCreadoresMods = $db->prepare("SELECT id_user FROM modders WHERE id = :idMod");
    $stmtCreadoresMods->execute([':idMod' => $data['id_mod']]);
    $infoCreadoresMods = $stmtCreadoresMods->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($infoCreadoresMods)) {
        $userIds = array_column($infoCreadoresMods, 'id_user');
        if (!empty($userIds)) {
            $placeholders = implode(',', array_fill(0, count($userIds), '?'));
            $stmt = $db->prepare("SELECT * FROM subscriptions WHERE user_id IN ($placeholders)");
            $stmt->execute($userIds);
            $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Número de suscripciones encontradas: " . count($subscriptions));

            if (!empty($subscriptions)) {
                enviarNotificaciones($subscriptions, 'Tienes un nuevo comentario!', 'Se ha detectado un nuevo comentario de: '.$alias.' en tu mod: '.$infoMods['nombre'], $infoLogo, $infoMods);
            }
        }
    }
    Flight::json([
        "response" => "success",
        "message" =>"Valoración guardada correctamente."
    ]);
});

function enviarNotificaciones($subscriptions, $title, $body, $infoLogo, $infoMods) {
    $vapidKeys = [
        'subject' => 'mailto:soporte@dokidokispanish.club',
        'publicKey' => 'BIuTU82pi4qBD-sNaJNezG1kGs_cA5-Qb_ZaVQ1ZCj7PxvoqBSDe9q33cZO4BmVZRL-97GAYzu18rPxf7lSw1pk',
        'privateKey' => 'rlwhiDtAYDeELyYC5fEDRtk3jIIjPTcryTsLXzKa6oQ',
    ];

    $webPush = new WebPush(['VAPID' => $vapidKeys]);
    $enviadas = 0;
    $site_url = getenv('URL_SITE');

    foreach ($subscriptions as $sub) {
        $subscription = Subscription::create([
            'endpoint' => $sub['endpoint'],
            'keys' => ['p256dh' => $sub['p256dh'], 'auth' => $sub['auth']],
        ]);

        $webPush->queueNotification($subscription, json_encode([
            'title' => $title,
            'body' => $body,
            'icon' => "https://api.dokidokispanish.club/" . ($infoLogo['url'] ?? 'default.png'),
            'data' => ['url' => "{$site_url}/mods/" . $infoMods['slug']]
        ]));

        // queueNotification() no devuelve valor; contamos las notificaciones encoladas por cada llamada
        $enviadas++;
    }

    error_log("Notificaciones en cola: " . $enviadas);

    foreach ($webPush->flush() as $report) {
        $endpoint = $report->getRequest()->getUri()->__toString();
        if ($report->isSuccess()) {
            error_log("[ÉXITO] Notificación enviada a: $endpoint");
        } else {
            error_log("[ERROR] Falló el envío a $endpoint: " . $report->getReason());
        }
    }
}