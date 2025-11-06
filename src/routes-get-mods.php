<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//funciones

function comprobar($datos){
    $array= [];

    foreach ($datos as $row){
        
        if(!empty($row['url_img'])){
           $row['url_img'] = "https://www.dokidokispanish.club/".$row['url_img'];
        }elseif(is_null($row['url_img'])){
            $row['url_img']="";
        }
        if(!empty($row['url_logo'])){
           $row['url_logo'] = "https://www.dokidokispanish.club/".$row['url_logo'];
        }elseif(is_null($row['url_logo'])){
            $row['url_logo']="https://www.dokidokispanish.club/assets/gui/window_icon.png";
        }
        if(!empty($row['url_cap2'])){
           $row['url_cap2'] = "https://www.dokidokispanish.club/".$row['url_cap2'];
        }
        if(!empty($row['url_cap3'])){
           $row['url_cap3'] = "https://www.dokidokispanish.club/".$row['url_cap3'];
        }
        if(!empty($row['url_cap4'])){
           $row['url_cap4'] = "https://www.dokidokispanish.club/".$row['url_cap4'];
        }
        if(is_null($row['porteador'])){
            $row['porteador'] = "";
        }
        if(is_null($row['fecha_creacion'])){
            $row['fecha_creacion'] = "";
        }
        if(is_null($row['texto_extras'])){
            $row['texto_extras'] = "";
        }
        if(is_null($row['link_extras'])){
            $row['link_extras'] = "";
        }
        if(is_null($row['link_youtube'])){
            $row['link_youtube'] = "";
        }
        if(is_null($row['titulo_youtube'])){
            $row['titulo_youtube'] = "";
        }
        if(is_null($row['link_PC'])){
            $row['link_PC'] = "";
        }
        if(is_null($row['link_android'])){
            $row['link_android'] = "";
        }
        if(is_null($row['seleccion_editor'])){
            $row['seleccion_editor'] = "";
        }
        $row['descripcion']= str_replace("\r", "", $row['descripcion']);
        $row['descripcion']= str_replace("\n", "", $row['descripcion']);
        $row['descripcion']= str_replace("<br>", "\n", $row['descripcion']);
        $row['descripcion']= str_replace("<b>", "**", $row['descripcion']);
        $row['descripcion']= str_replace("</b>", "**", $row['descripcion']);
        $row['descripcion']= str_replace("<i>", "*", $row['descripcion']);
        $row['descripcion']= str_replace("</i>", "*", $row['descripcion']);
        
        $array[] = [
            "id"=>$row['id'],
            "url_sitio"=>"https://www.dokidokispanish.club/mod-".$row['id'],
            "nombre"=>$row['nombre'],
            "descripcion"=>$row['descripcion'],
            "genero"=>$row['genero'],
            "genero2"=>$row['genero2'],
            "duracion"=>$row['duracion'],
            "estado"=>$row['estado'],
            "enfoque"=>$row['enfoque_personaje'],
            "url_img"=> $row['url_img'],
            "url_logo"=> $row['url_logo'],
            "url_img2"=> $row['url_cap2'],
            "url_img3"=> $row['url_cap3'],
            "url_img4"=> $row['url_cap4'],
            "seleccion"=>$row['seleccion_editor'],
            "traductor"=>$row['traductor'],
            "creador"=>$row['creador'],
            "porteador"=>$row['porteador'],
            "link_pc"=>$row['link_PC'],
            "link_android"=>$row['link_android'],
            "link_externo_web"=>$row['link_externo'],
            "link_youtube"=>$row['link_youtube'],
            "titulo_video_yt" => $row['titulo_youtube'],
            "contenido_nsfw"=>$row['nsfw'],
            "tipo_mod"=>$row['tipo'],
            "visitas"=>$row['visitas']
        ];
    }
    
    return $array;
    
}

function comprobarSong($datos){
    $array= [];

    foreach ($datos as $row){
        
        
        $array[] = [
            "id"=>$row['id'],
            "src"=>"https://www.dokidokispanish.club/assets/audios/".$row['url'],
            "title"=>$row['nombre'],
            "details"=>$row['descripcion'],
            "image" => "https://www.dokidokispanish.club/".$row['image'],
            "enlace" => $row['enlace_externo']
        ];
    }
    
    return $array;
    
}
function comprobarGenero($datos){
    $array= [];

    foreach ($datos as $row){
        $array[] = [
            "id"=>$row['id'],
            "genero"=>$row['genero']
        ];
    }
    return $array;
}

function comprobarTipo($datos){
    $array= [];

    foreach ($datos as $row){
        $array[] = [
            "id"=>$row['id'],
            "tipo"=>$row['tipo']
        ];
    }
    return $array;
}

function comprobarSaga($datos){
    $array= [];

    foreach ($datos as $row){
        $array[] = [
            "id"=>$row['id'],
            "titulo"=>$row['titulo']
        ];
    }
    return $array;
}

function comprobarEnfoque($datos){
    $array= [];

    foreach ($datos as $row){
        $array[] = [
            "id"=>$row['id'],
            "enfoque"=>$row['personaje']
        ];
    }
    return $array;
}
function comprobarDuracion($datos){
    $array= [];

    foreach ($datos as $row){
        $array[] = [
            "id"=>$row['id'],
            "duracion"=>$row['duracion']
        ];
    }
    return $array;
}
function comprobarEstado($datos){
    $array= [];

    foreach ($datos as $row){
        $array[] = [
            "id"=>$row['id'],
            "estado"=>$row['estado']
        ];
    }
    return $array;
}

function comprobarImages($datos){
    $array= [];

    foreach ($datos as $row){
        
        
        $array[] = [
            "id"=>$row['id'],
            "src"=>"https://www.dokidokispanish.club/".$row['url_image'],
            "title"=>$row['nombre'],
            "creador" => $row['creador']
        ];
    }
    
    return $array;
}
//rutas

Flight::route('GET /random-mod', function() {
    $db = Flight::db();

    $stmt = $db->prepare("SELECT id FROM mods WHERE isPublic = 1"); // Paginación inicial para evitar sobrecarga LIMIT 50
    $stmt->execute();
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($ids)) {
        Flight::json(['error' => 'No se encontraron IDs']);
        return;
    }
    
    $idAleatorio = $ids[array_rand($ids)];
    
    
    $stmt2 = $db->prepare("SELECT * FROM mods WHERE id = ? AND isPublic = 1");
    $stmt2->execute([$idAleatorio]);
    $modInfo = $stmt2->fetch(PDO::FETCH_ASSOC);
    
    $slugMod = $modInfo['slug'];


    Flight::json([
        "response" => "success",
        "total_rows" => 1,
        "id_mod" => $idAleatorio,
        "slug" => $slugMod,
    ]);
});

Flight::route('GET /mods', function() {
    $db = Flight::db();

    $query = "SELECT 
                m.id, 
                m.nombre, 
                m.descripcion,
                d.duracion, 
                e.estado, 
                en.personaje, 
                m.isNSFW, 
                t.tipo, 
                m.slug, 
                m.created, 
                m.visitas, 
                m.isPublic,
                IFNULL(GROUP_CONCAT(DISTINCT g.genero ORDER BY g.genero SEPARATOR ', '), '') AS generos,
                IFNULL(GROUP_CONCAT(DISTINCT CONCAT(i.id_tipo_imagen, '|', i.url) ORDER BY i.url SEPARATOR ', '), '') AS imagenes,
                IFNULL(GROUP_CONCAT(DISTINCT CONCAT(enlace.id_tipo, '|', enlace.url) ORDER BY enlace.url SEPARATOR ', '), '') AS enlaces,
                IFNULL(GROUP_CONCAT(DISTINCT CONCAT(c.id, '|', c.slug, '|', c.categoria, '|', c.active) 
                    ORDER BY c.categoria SEPARATOR ', '), '') AS categorias
            FROM mods m
            LEFT JOIN duracion_mods d ON m.id_duracion = d.id
            LEFT JOIN estado_mods e ON m.id_estado = e.id
            LEFT JOIN enfoque_mods en ON m.id_enfoque = en.id
            LEFT JOIN tipo_mod t ON m.id_tipo = t.id
            LEFT JOIN generos_mod gm ON m.id = gm.id_mod
            LEFT JOIN tipo_genero g ON gm.id_genero = g.id
            LEFT JOIN imagenes_mod i ON m.id = i.id_mod
            LEFT JOIN enlaces enlace ON m.id = enlace.id_mod
            LEFT JOIN mod_category mc ON m.id = mc.id_mod
            LEFT JOIN categories c ON mc.id_category = c.id
            WHERE m.isPublic = 1
            GROUP BY m.id
            "; // Paginación inicial para evitar sobrecarga LIMIT 50

    $stmt = $db->prepare($query);
    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($mods as &$mod) {
        $mod['isNSFW'] = (bool)$mod["isNSFW"];
        $mod['isPublic'] = (bool)$mod["isPublic"];
        $mod['generos'] = $mod['generos'] ? explode(', ', $mod['generos']) : [];
        
        $logo = null;
        $portada = null;

        if ($mod['imagenes']) {
            $imagenesArray = explode(', ', $mod['imagenes']);
            foreach ($imagenesArray as $img) {
                list($tipo, $url) = explode('|', $img);
                $tipo = (int) $tipo;
                
                if(empty($url)){
                    $fullUrl= "https://api.dokidokispanish.club/gui/Imagen-no-disponible.jpg";
                }else{
                $fullUrl = "https://api.dokidokispanish.club/" . $url;
                }

                if ($tipo === 1) {
                    $logo = $fullUrl;
                } elseif ($tipo === 3) {
                    $portada = $fullUrl;
                }
            }
        }

        $mod['logo'] = $logo;
        $mod['portada'] = $portada;

        $pc = null;
        $android = null;
        if ($mod['enlaces']) {
            $enlacesArray = explode(', ', $mod['enlaces']);
            foreach ($enlacesArray as $enlace) {
                list($tipo, $url) = explode('|', $enlace);
                if ($tipo == 1) {
                    $android = $url;
                } elseif ($tipo == 2) {
                    $pc = $url;
                }
            }
        }
        
        $categoriasArray = [];
        if ($mod['categorias']) {
            $categoriasParts = explode(', ', $mod['categorias']);
            foreach ($categoriasParts as $cat) {
                list($id, $slug, $categoria, $activo) = explode('|', $cat);
                $categoriasArray[] = [
                    "id" => (int) $id,
                    "slug" => $slug,
                    "categoria" => $categoria,
                    "activo" => (bool) $activo
                ];
            }
        }
        $mod['categorias'] = $categoriasArray;

        $mod['pc'] = $pc;
        $mod['android'] = $android;

        unset($mod['enlaces']);
        unset($mod['imagenes']);
    }

    Flight::json([
        "response" => "success",
        "total_rows" => count($mods),
        "results" => $mods,
    ]);
});

Flight::route('GET /list-mods', function() {
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
    $db = Flight::db();

    $query = "SELECT 
                m.id, 
                m.nombre,
                t.tipo, 
                m.slug, 
                m.created, 
                m.visitas, 
                m.isPublic,
                IFNULL(GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN modder.id_user IS NULL THEN modder.nombre 
                        ELSE u1.alias 
                    END
                    ORDER BY u1.alias SEPARATOR ', '), '') AS creadores,
                IFNULL(GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN traductor.id_user IS NULL THEN traductor.nombre 
                        ELSE u2.alias 
                    END
                    ORDER BY u2.alias SEPARATOR ', '), '') AS traductores,
                IFNULL(GROUP_CONCAT(DISTINCT CONCAT(i.id_tipo_imagen, '|', i.url) ORDER BY i.url SEPARATOR ', '), '') AS imagenes,
                IFNULL(GROUP_CONCAT(DISTINCT CONCAT(c.id, '|', c.slug, '|', c.categoria, '|', c.active) 
                    ORDER BY c.categoria SEPARATOR ', '), '') AS categorias
            FROM mods m
            LEFT JOIN tipo_mod t ON m.id_tipo = t.id
            LEFT JOIN modders modder ON m.id = modder.id_mod AND modder.isActive = 1
            LEFT JOIN users u1 ON modder.id_user = u1.id
            LEFT JOIN traductores traductor ON m.id = traductor.id_mod AND traductor.isActive = 1
            LEFT JOIN users u2 ON traductor.id_user = u2.id
            LEFT JOIN imagenes_mod i ON m.id = i.id_mod
            LEFT JOIN mod_category mc ON m.id = mc.id_mod
            LEFT JOIN categories c ON mc.id_category = c.id
            GROUP BY m.id
            "; // Paginación inicial para evitar sobrecarga LIMIT 50

    $stmt = $db->prepare($query);
    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($mods as &$mod) {
        $mod['creadores'] = $mod['creadores'] ? explode(', ', $mod['creadores']) : [];
        $mod['traductores'] = $mod['traductores'] ? explode(', ', $mod['traductores']) : [];
        
        $logo = null;

        if ($mod['imagenes']) {
            $imagenesArray = explode(', ', $mod['imagenes']);
            foreach ($imagenesArray as $img) {
                list($tipo, $url) = explode('|', $img);
                $tipo = (int) $tipo;
                
                if(empty($url)){
                    $fullUrl= "https://api.dokidokispanish.club/gui/Imagen-no-disponible.jpg";
                }else{
                $fullUrl = "https://api.dokidokispanish.club/" . $url;
                }

                if ($tipo === 1) {
                    $logo = $fullUrl;
                }
            }
        }

        $mod['logo'] = $logo;

        unset($mod['imagenes']);
        $categoriasArray = [];
        if ($mod['categorias']) {
            $categoriasParts = explode(', ', $mod['categorias']);
            foreach ($categoriasParts as $cat) {
                list($id, $slug, $categoria, $activo) = explode('|', $cat);
                $categoriasArray[] = [
                    "id" => (int) $id,
                    "slug" => $slug,
                    "categoria" => $categoria,
                    "activo" => (bool) $activo
                ];
            }
        }
        $mod['categorias'] = $categoriasArray;
    }

    Flight::json([
        "response" => "success",
        "total_rows" => count($mods),
        "results" => $mods,
    ]);
});

Flight::route('GET /mods/list/byUserID/@id', function($id) {
    $db = Flight::db();

    $query = "
        SELECT 
            m.id, 
            m.nombre,
            e.estado, 
            t.tipo, 
            m.slug,
            m.descripcion,
            m.visitas,
            IFNULL(GROUP_CONCAT(DISTINCT CONCAT(i.id_tipo_imagen, '|', i.url) ORDER BY i.url SEPARATOR ', '), '') AS imagenes,
            IFNULL(GROUP_CONCAT(DISTINCT CONCAT(c.id, '|', c.slug, '|', c.categoria, '|', c.active)
                ORDER BY c.categoria SEPARATOR ', '), '') AS categorias
        FROM mods m
        LEFT JOIN estado_mods e ON m.id_estado = e.id
        LEFT JOIN tipo_mod t ON m.id_tipo = t.id
        LEFT JOIN traductores tr ON m.id = tr.id_mod
        LEFT JOIN modders mo ON m.id = mo.id_mod
        LEFT JOIN mod_category mc ON m.id = mc.id_mod
        LEFT JOIN categories c ON mc.id_category = c.id
        LEFT JOIN imagenes_mod i ON m.id = i.id_mod
        WHERE m.isPublic = 1
          AND (tr.id_user = :id OR mo.id_user = :id)
        GROUP BY m.id
    ";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Procesar categorías de cada mod
    foreach ($mods as &$mod) {
        $logo = null;
        $portada = null;

        if ($mod['imagenes']) {
            $imagenesArray = explode(', ', $mod['imagenes']);
            foreach ($imagenesArray as $img) {
                list($tipo, $url) = explode('|', $img);
                $tipo = (int) $tipo;
                
                if(empty($url)){
                    $fullUrl= "https://api.dokidokispanish.club/gui/Imagen-no-disponible.jpg";
                }else{
                $fullUrl = "https://api.dokidokispanish.club/" . $url;
                }

                if ($tipo === 1) {
                    $logo = $fullUrl;
                } elseif ($tipo === 3) {
                    $portada = $fullUrl;
                }
            }
        }

        $mod['logo'] = $logo;
        $mod['portada'] = $portada;
        
        
        $categoriasArray = [];

        if (!empty($mod['categorias'])) {
            $categoriasParts = explode(', ', $mod['categorias']);
            foreach ($categoriasParts as $cat) {
                list($catId, $slug, $categoria, $activo) = explode('|', $cat);
                $categoriasArray[] = [
                    "id" => (int) $catId,
                    "slug" => $slug,
                    "categoria" => $categoria,
                    "activo" => (bool) $activo
                ];
            }
        }

        $mod['categorias'] = $categoriasArray; // reemplazar cadena por array
        unset($mod['imagenes']);
    }
    
    

    Flight::json([
        "response" => "success",
        "total_rows" => count($mods),
        "results" => $mods,
    ]);
});
Flight::route('GET /total-mods', function() {
    $db = Flight::db();

    $query = "SELECT id FROM mods WHERE isPublic = 1"; // Paginación inicial para evitar sobrecarga LIMIT 50

    $stmt = $db->prepare($query);
    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);


    Flight::json([
        "response" => "success",
        "total_rows" => count($mods),
    ]);
});
Flight::route('GET /mods/sagas', function() {
    $db = Flight::db();

    $query = "SELECT 
                s.id AS saga_id,
                s.titulo AS saga_nombre,
                s.slug
            FROM todas_las_sagas s
            ";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sagas = [];
    foreach ($rows as $row) {
        $sagaId = $row['saga_id'];
        if (!isset($sagas[$sagaId])) {
            $sagas[$sagaId] = [
                "id" => $sagaId,
                "titulo_saga" => $row['saga_nombre'],
                "slug_saga" => $row['slug'],
            ];
        }
    }

    Flight::json([
        "response" => "success",
        "total_rows" => count($sagas),
        "results" => array_values($sagas)
    ]);
});

Flight::route('GET /mods/sagas/exist-mod/byID/@id', function($id) {
    $db = Flight::db();

    $query = "
        SELECT 
            s.id AS saga_id,
            s.titulo AS saga_nombre,
            s.slug AS saga_slug
        FROM mods_registrados_sagas mrs
        INNER JOIN todas_las_sagas s ON mrs.id_saga = s.id
        WHERE mrs.id_mod = :id_mod
    ";

    $stmt = $db->prepare($query);
    $stmt->execute([":id_mod" => $id]);
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$rows) {
        Flight::halt(400, json_encode([
            "error" => "No se encontró ninguna saga para este mod."
        ]));
        return;
    }
    
    $query = "SELECT 
                s.id AS saga_id,
                s.titulo AS saga_nombre,
                ts.tipo AS saga_tipo,
                s.slug as slug,
                m.id AS mod_id, 
                m.nombre AS mod_nombre, 
                m.descripcion AS mod_descripcion,
                d.duracion, 
                e.estado, 
                en.personaje, 
                m.isNSFW, 
                t.tipo AS mod_tipo, 
                m.slug, 
                m.created, 
                m.visitas, 
                m.isPublic,
                GROUP_CONCAT(DISTINCT g.genero ORDER BY g.genero SEPARATOR ', ') AS generos,
                GROUP_CONCAT(DISTINCT CONCAT(i.id_tipo_imagen, '|', i.url) ORDER BY i.url SEPARATOR ', ') AS imagenes,
                GROUP_CONCAT(DISTINCT CONCAT(enlace.id_tipo, '|', enlace.url) ORDER BY enlace.url SEPARATOR ', ') AS enlaces
            FROM todas_las_sagas s
            JOIN mods_registrados_sagas ms ON s.id = ms.id_saga
            JOIN mods m ON ms.id_mod = m.id
            LEFT JOIN tipo_mod_saga ts ON ms.tipo = ts.id
            LEFT JOIN duracion_mods d ON m.id_duracion = d.id
            LEFT JOIN estado_mods e ON m.id_estado = e.id
            LEFT JOIN enfoque_mods en ON m.id_enfoque = en.id
            LEFT JOIN tipo_mod t ON m.id_tipo = t.id
            LEFT JOIN generos_mod gm ON m.id = gm.id_mod
            LEFT JOIN tipo_genero g ON gm.id_genero = g.id
            LEFT JOIN imagenes_mod i ON m.id = i.id_mod
            LEFT JOIN enlaces enlace ON m.id = enlace.id_mod
            WHERE s.id = ? AND m.id != ?
            GROUP BY s.id, s.titulo, ts.tipo, m.id, m.nombre, m.descripcion, d.duracion, e.estado, en.personaje, m.isNSFW, t.tipo, m.slug, m.created, m.visitas, m.isPublic
            ORDER BY s.titulo, m.nombre";

    // Si se busca un mod específico por slug, agregamos la condición

    $stmt = $db->prepare($query);
    $stmt->execute([$rows['saga_id'], $id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if(count($rows)==0){
         Flight::halt(400, json_encode(["error" => "La saga no contiene nigun mod asociado"]));
        return;
    }

    $sagas = [];
    foreach ($rows as $row) {
        $sagaId = $row['saga_id'];
        if (!isset($sagas[$sagaId])) {
            $sagas[$sagaId] = [
                "id" => $sagaId,
                "titulo_saga" => $row['saga_nombre'],
                "slug_saga" => $row['slug'],
                "mods" => []
            ];
        }

        $logo = null;
        $portada = null;
        $capturas = [];
        if ($row['imagenes']) {
            foreach (explode(', ', $row['imagenes']) as $img) {
                list($tipo, $url) = explode('|', $img);
                $fullUrl = "https://api.dokidokispanish.club/" . $url;
                if ((int) $tipo === 1) $logo = $fullUrl;
                elseif ((int) $tipo === 2) $capturas[] = $fullUrl;
                else $portada = $fullUrl;
            }
        }

        $sagas[$sagaId]['mods'][] = [
            "id" => $row['mod_id'],
            "nombre" => $row['mod_nombre'],
            "descripcion" => $row['mod_descripcion'],
            "duracion" => $row['duracion'],
            "estado" => $row['estado'],
            "isNSFW" => $row['isNSFW'],
            "tipo_en_la_saga" => $row['saga_tipo'], // Se ha movido aquí
            "slug" => $row['slug'],
            "created" => $row['created'],
            "visitas" => $row['visitas'],
            "isPublic" => $row['isPublic'],
            "generos" => $row['generos'] ? explode(', ', $row['generos']) : [],
            "logo" => $logo,
            "portada" => $portada,
            "capturas" => $capturas,
        ];
    }

    Flight::json([
        "response" => "success",
        "results" => $sagas[1]
    ]);
    
});

Flight::route('GET /mods/saga/@slug', function($slug)  {
    
    if(!$slug){
        Flight::halt(400, json_encode(["error" => "No hay un slug"]));
        return;
    }
    
    if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
        Flight::halt(400, json_encode(["error" => "Slug inválido"]));
        return;
    }
    // Si es válido, se sanitiza
    $slug = filter_var($slug, FILTER_SANITIZE_SPECIAL_CHARS);
    
    $db = Flight::db();

    $query = "SELECT 
                s.id AS saga_id,
                s.titulo AS saga_nombre,
                ts.tipo AS saga_tipo,
                s.slug as slug,
                m.id AS mod_id, 
                m.nombre AS mod_nombre, 
                m.descripcion AS mod_descripcion,
                d.duracion, 
                e.estado, 
                en.personaje, 
                m.isNSFW, 
                t.tipo AS mod_tipo, 
                m.slug, 
                m.created, 
                m.visitas, 
                m.isPublic,
                GROUP_CONCAT(DISTINCT g.genero ORDER BY g.genero SEPARATOR ', ') AS generos,
                GROUP_CONCAT(DISTINCT CONCAT(i.id_tipo_imagen, '|', i.url) ORDER BY i.url SEPARATOR ', ') AS imagenes,
                GROUP_CONCAT(DISTINCT CONCAT(enlace.id_tipo, '|', enlace.url) ORDER BY enlace.url SEPARATOR ', ') AS enlaces
            FROM todas_las_sagas s
            JOIN mods_registrados_sagas ms ON s.id = ms.id_saga
            JOIN mods m ON ms.id_mod = m.id
            LEFT JOIN tipo_mod_saga ts ON ms.tipo = ts.id
            LEFT JOIN duracion_mods d ON m.id_duracion = d.id
            LEFT JOIN estado_mods e ON m.id_estado = e.id
            LEFT JOIN enfoque_mods en ON m.id_enfoque = en.id
            LEFT JOIN tipo_mod t ON m.id_tipo = t.id
            LEFT JOIN generos_mod gm ON m.id = gm.id_mod
            LEFT JOIN tipo_genero g ON gm.id_genero = g.id
            LEFT JOIN imagenes_mod i ON m.id = i.id_mod
            LEFT JOIN enlaces enlace ON m.id = enlace.id_mod
            WHERE ? LIKE CONCAT(s.slug, '%')
            GROUP BY s.id, s.titulo, ts.tipo, m.id, m.nombre, m.descripcion, d.duracion, e.estado, en.personaje, m.isNSFW, t.tipo, m.slug, m.created, m.visitas, m.isPublic
            ORDER BY s.titulo, m.nombre";

    // Si se busca un mod específico por slug, agregamos la condición

    $stmt = $db->prepare($query);
    $stmt->execute([$slug]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if(count($rows)==0){
         Flight::halt(400, json_encode(["error" => "La saga no contiene nigun mod asociado"]));
        return;
    }

    $sagas = [];
    foreach ($rows as $row) {
        $sagaId = $row['saga_id'];
        if (!isset($sagas[$sagaId])) {
            $sagas[$sagaId] = [
                "id" => $sagaId,
                "titulo_saga" => $row['saga_nombre'],
                "slug_saga" => $row['slug'],
                "mods" => []
            ];
        }

        $logo = null;
        $portada = null;
        $capturas = [];
        if ($row['imagenes']) {
            foreach (explode(', ', $row['imagenes']) as $img) {
                list($tipo, $url) = explode('|', $img);
                $fullUrl = "https://api.dokidokispanish.club/" . $url;
                if ((int) $tipo === 1) $logo = $fullUrl;
                elseif ((int) $tipo === 2) $capturas[] = $fullUrl;
                else $portada = $fullUrl;
            }
        }

        $sagas[$sagaId]['mods'][] = [
            "id" => $row['mod_id'],
            "nombre" => $row['mod_nombre'],
            "descripcion" => $row['mod_descripcion'],
            "duracion" => $row['duracion'],
            "estado" => $row['estado'],
            "isNSFW" => $row['isNSFW'],
            "tipo_en_la_saga" => $row['saga_tipo'], // Se ha movido aquí
            "slug" => $row['slug'],
            "created" => $row['created'],
            "visitas" => $row['visitas'],
            "isPublic" => $row['isPublic'],
            "generos" => $row['generos'] ? explode(', ', $row['generos']) : [],
            "logo" => $logo,
            "portada" => $portada,
            "capturas" => $capturas,
        ];
    }

    Flight::json([
        "response" => "success",
        "total_rows" => count($sagas),
        "results" => $sagas[1]
    ]);
});

Flight::route('GET /mod/@slug', function($slug) {
     if(!$slug){
        Flight::halt(400, json_encode(["error" => "No hay un slug"]));
        return;
    }
    
    if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
        Flight::halt(400, json_encode(["error" => "Slug inválido"]));
        return;
    }
    // Si es válido, se sanitiza
    $slug = filter_var($slug, FILTER_SANITIZE_SPECIAL_CHARS);
    $db = Flight::db();

    $query = "SELECT 
                m.id, 
                m.nombre, 
                m.descripcion,
                d.duracion, 
                e.estado, 
                en.personaje, 
                m.isNSFW, 
                t.tipo, 
                m.slug, 
                m.created, 
                m.visitas, 
                m.isPublic,
                m.id_porteador,
                u3.id AS porteador_id,
                u3.alias AS porteador_alias,
                u3.user AS porteador_nombre,
                u3.email AS porteador_correo,
                u3.url_logo AS porteador_avatar,
                IFNULL(GROUP_CONCAT(DISTINCT g.genero ORDER BY g.genero SEPARATOR ', '), '') AS generos,

                IFNULL(GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN modder.id_user IS NULL 
                            THEN CONCAT(modder.id, '|', 'NULL', '|', modder.nombre) 
                        ELSE CONCAT(modder.id, '|', u1.id, '|', u1.alias) 
                    END
                    SEPARATOR ','
                ), '') AS creadores,
                
                -- Traductores (id|id_user|nombre)
                IFNULL(GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN traductor.id_user IS NULL 
                            THEN CONCAT(traductor.id, '|', 'NULL', '|', traductor.nombre) 
                        ELSE CONCAT(traductor.id, '|', u2.id, '|', u2.alias) 
                    END
                    SEPARATOR ','
                ), '') AS traductores,

                IFNULL(GROUP_CONCAT(DISTINCT CONCAT(i.id_tipo_imagen, '|', i.url) ORDER BY i.url SEPARATOR ', '), '') AS imagenes,
                IFNULL(GROUP_CONCAT(DISTINCT CONCAT(enlace.id_tipo, '|', enlace.url) ORDER BY enlace.url SEPARATOR ', '), '') AS enlaces,
                IFNULL(GROUP_CONCAT(DISTINCT CONCAT(c.id, '|', c.slug, '|', c.categoria, '|', c.active) 
                    ORDER BY c.categoria SEPARATOR ', '), '') AS categorias
            FROM mods m
            LEFT JOIN duracion_mods d ON m.id_duracion = d.id
            LEFT JOIN estado_mods e ON m.id_estado = e.id
            LEFT JOIN enfoque_mods en ON m.id_enfoque = en.id
            LEFT JOIN tipo_mod t ON m.id_tipo = t.id
            LEFT JOIN users u3 ON m.id_porteador = u3.id
            LEFT JOIN generos_mod gm ON m.id = gm.id_mod
            LEFT JOIN tipo_genero g ON gm.id_genero = g.id
            LEFT JOIN modders modder ON m.id = modder.id_mod AND modder.isActive = 1
            LEFT JOIN users u1 ON modder.id_user = u1.id
            LEFT JOIN traductores traductor ON m.id = traductor.id_mod AND traductor.isActive = 1
            LEFT JOIN users u2 ON traductor.id_user = u2.id
            LEFT JOIN imagenes_mod i ON m.id = i.id_mod
            LEFT JOIN enlaces enlace ON m.id = enlace.id_mod
            LEFT JOIN mod_category mc ON m.id = mc.id_mod
            LEFT JOIN categories c ON mc.id_category = c.id
            WHERE m.isPublic = 1
            GROUP BY m.id
            ";

    if ($slug) {
        $query .= " HAVING m.slug = :slug";
    }

    $stmt = $db->prepare($query);

    if ($slug) {
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
    }
    $stmt->execute();
    $mod = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!empty($mod['id_porteador']) && !empty($mod['porteador_id'])) {
        $mod['porteador'] = [
                "id" => 1,
                "id_user" => (int)$mod['porteador_id'],
                "nombre" => $mod['porteador_alias']
            ];
    } else {
        unset(
            $mod['id_porteador'],
            $mod['porteador_id'],
            $mod['porteador_alias'],
            $mod['porteador_nombre'],
            $mod['porteador_correo'],
            $mod['porteador_avatar']
        );
    }

    
    $mod['isNSFW'] = (bool)$mod['isNSFW'];
    $mod['isPublic'] = (bool)$mod['isPublic'];
    
    // Generos como arreglo
    $mod['generos'] = $mod['generos'] ? explode(', ', $mod['generos']) : [];
    // Creadores como arreglo de objetos {id, nombre}
    if ($mod['creadores']) {
        $creadoresArray = explode(',', $mod['creadores']);
        $creadores = [];
        foreach ($creadoresArray as $c) {
            list($id, $id_user, $nombre) = explode('|', $c, 3);
            $creadores[] = [
                "id" => (int) $id,
                "id_user" => $id_user !== "NULL" ? (int) $id_user : null,
                "nombre" => $nombre
            ];
        }
        $mod['creadores'] = $creadores;
    } else {
        $mod['creadores'] = [];
    }
    
    // Traductores como arreglo de objetos {id, id_user, nombre}
    if ($mod['traductores']) {
        $traductoresArray = explode(',', $mod['traductores']);
        $traductores = [];
        foreach ($traductoresArray as $t) {
            list($id, $id_user, $nombre) = explode('|', $t, 3);
            $traductores[] = [
                "id" => (int) $id,
                "id_user" => $id_user !== "NULL" ? (int) $id_user : null,
                "nombre" => $nombre
            ];
        }
        $mod['traductores'] = $traductores;
    } else {
        $mod['traductores'] = [];
    }
    // Procesar imágenes
    $logo = null;
    $portada = null;
    $capturas = [];
    if ($mod['imagenes']) {
        $imagenesArray = explode(', ', $mod['imagenes']);
        foreach ($imagenesArray as $img) {
            list($tipo, $url) = explode('|', $img);
            $tipo = (int) $tipo;
            
            if(empty($url)){
                $fullUrl = "https://api.dokidokispanish.club/gui/Imagen-no-disponible.jpg";
            } else {
                $fullUrl = "https://api.dokidokispanish.club/" . $url;
            }
            if ($tipo === 1) {
                $logo = $fullUrl;
            } elseif ($tipo === 2) {
                $capturas[] = $fullUrl;
            } else {
                $portada = $fullUrl;
            }
        }
    }
    $mod['logo'] = $logo;
    $mod['capturas'] = $capturas;
    $mod['portada'] = $portada;
    // Procesar enlaces
    $pc = null;
    $android = null;
    if ($mod['enlaces']) {
        $enlacesArray = explode(', ', $mod['enlaces']);
        foreach ($enlacesArray as $enlace) {
            list($tipo, $url) = explode('|', $enlace);
            if ($tipo == 1) {
                $android = $url;
            } elseif ($tipo == 2) {
                $pc = $url;
            }
        }
    }
    $mod['pc'] = $pc;
    $mod['android'] = $android;
    unset($mod['enlaces']);
    unset($mod['imagenes']);
    
    $categoriasArray = [];
    if ($mod['categorias']) {
        $categoriasParts = explode(', ', $mod['categorias']);
        foreach ($categoriasParts as $cat) {
            list($id, $slug, $categoria, $activo) = explode('|', $cat);
            $categoriasArray[] = [
                "id" => (int) $id,
                "slug" => $slug,
                "categoria" => $categoria,
                "activo" => (bool) $activo
            ];
        }
    }
    $mod['categorias'] = $categoriasArray;
    

    Flight::json([
        "response" => "success",
        "total_rows" => count($mod),
        "results" => $mod,
    ]);
});

Flight::route('GET /mod/admin/@slug', function($slug) {
     if(!$slug){
        Flight::halt(400, json_encode(["error" => "No hay un slug"]));
        return;
    }
    
    if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
        Flight::halt(400, json_encode(["error" => "Slug inválido"]));
        return;
    }
    // Si es válido, se sanitiza
    $slug = filter_var($slug, FILTER_SANITIZE_SPECIAL_CHARS);
    $db = Flight::db();

    $query = "SELECT 
                m.id, 
                m.nombre, 
                m.descripcion,
                d.duracion, 
                e.estado, 
                en.personaje, 
                m.isNSFW, 
                t.tipo, 
                m.slug, 
                m.created, 
                m.visitas, 
                m.isPublic,
                m.id_porteador,
                IFNULL(GROUP_CONCAT(DISTINCT g.genero ORDER BY g.genero SEPARATOR ', '), '') AS generos,
                IFNULL(GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN modder.id_user IS NULL 
                            THEN CONCAT(modder.id, '|', 'NULL', '|', modder.nombre) 
                        ELSE CONCAT(modder.id, '|', u1.id, '|', u1.alias) 
                    END
                    SEPARATOR ','
                ), '') AS creadores,
                IFNULL(GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN traductor.id_user IS NULL 
                            THEN CONCAT(traductor.id, '|', 'NULL', '|', traductor.nombre) 
                        ELSE CONCAT(traductor.id, '|', u2.id, '|', u2.alias) 
                    END
                    SEPARATOR ','
                ), '') AS traductores,

                IFNULL(GROUP_CONCAT(DISTINCT CONCAT(i.id_tipo_imagen, '|', i.url) ORDER BY i.url SEPARATOR ', '), '') AS imagenes,
                IFNULL(GROUP_CONCAT(DISTINCT CONCAT(enlace.id_tipo, '|', enlace.url) ORDER BY enlace.url SEPARATOR ', '), '') AS enlaces,
                IFNULL(GROUP_CONCAT(DISTINCT CONCAT(c.id, '|', c.slug, '|', c.categoria, '|', c.active) 
                    ORDER BY c.categoria SEPARATOR ', '), '') AS categorias
            FROM mods m
            LEFT JOIN duracion_mods d ON m.id_duracion = d.id
            LEFT JOIN estado_mods e ON m.id_estado = e.id
            LEFT JOIN enfoque_mods en ON m.id_enfoque = en.id
            LEFT JOIN tipo_mod t ON m.id_tipo = t.id
            LEFT JOIN generos_mod gm ON m.id = gm.id_mod
            LEFT JOIN tipo_genero g ON gm.id_genero = g.id
            LEFT JOIN modders modder ON m.id = modder.id_mod AND modder.isActive = 1
            LEFT JOIN users u1 ON modder.id_user = u1.id
            LEFT JOIN traductores traductor ON m.id = traductor.id_mod AND traductor.isActive = 1
            LEFT JOIN users u2 ON traductor.id_user = u2.id
            LEFT JOIN imagenes_mod i ON m.id = i.id_mod
            LEFT JOIN enlaces enlace ON m.id = enlace.id_mod
            LEFT JOIN mod_category mc ON m.id = mc.id_mod
            LEFT JOIN categories c ON mc.id_category = c.id
            GROUP BY m.id
            ";

    if ($slug) {
        $query .= " HAVING m.slug = :slug";
    }

    $stmt = $db->prepare($query);

    if ($slug) {
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
    }
    $stmt->execute();
    $mod = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (is_null($mod['id_porteador'])) {
        unset($mod['id_porteador']);
    }

    
    $mod['isNSFW'] = (bool)$mod['isNSFW'];
    $mod['isPublic'] = (bool)$mod['isPublic'];
    // Generos como arreglo
    $mod['generos'] = $mod['generos'] ? explode(', ', $mod['generos']) : [];
    // Creadores como arreglo de objetos {id, nombre}
    if ($mod['creadores']) {
        $creadoresArray = explode(',', $mod['creadores']);
        $creadores = [];
        foreach ($creadoresArray as $c) {
            list($id, $id_user, $nombre) = explode('|', $c, 3);
            $creadores[] = [
                "id" => (int) $id,
                "id_user" => $id_user !== "NULL" ? (int) $id_user : null,
                "nombre" => $nombre
            ];
        }
        $mod['creadores'] = $creadores;
    } else {
        $mod['creadores'] = [];
    }
    
    // Traductores como arreglo de objetos {id, id_user, nombre}
    if ($mod['traductores']) {
        $traductoresArray = explode(',', $mod['traductores']);
        $traductores = [];
        foreach ($traductoresArray as $t) {
            list($id, $id_user, $nombre) = explode('|', $t, 3);
            $traductores[] = [
                "id" => (int) $id,
                "id_user" => $id_user !== "NULL" ? (int) $id_user : null,
                "nombre" => $nombre
            ];
        }
        $mod['traductores'] = $traductores;
    } else {
        $mod['traductores'] = [];
    }
    // Procesar imágenes
    $logo = null;
    $portada = null;
    $capturas = [];
    if ($mod['imagenes']) {
        $imagenesArray = explode(', ', $mod['imagenes']);
        foreach ($imagenesArray as $img) {
            list($tipo, $url) = explode('|', $img);
            $tipo = (int) $tipo;
            
            if(empty($url)){
                $fullUrl = "https://api.dokidokispanish.club/gui/Imagen-no-disponible.jpg";
            } else {
                $fullUrl = "https://api.dokidokispanish.club/" . $url;
            }
            if ($tipo === 1) {
                $logo = $fullUrl;
            } elseif ($tipo === 2) {
                $capturas[] = $fullUrl;
            } else {
                $portada = $fullUrl;
            }
        }
    }
    $mod['logo'] = $logo;
    $mod['capturas'] = $capturas;
    $mod['portada'] = $portada;
    // Procesar enlaces
    $pc = null;
    $android = null;
    if ($mod['enlaces']) {
        $enlacesArray = explode(', ', $mod['enlaces']);
        foreach ($enlacesArray as $enlace) {
            list($tipo, $url) = explode('|', $enlace);
            if ($tipo == 1) {
                $android = $url;
            } elseif ($tipo == 2) {
                $pc = $url;
            }
        }
    }
    $mod['pc'] = $pc;
    $mod['android'] = $android;
    unset($mod['enlaces']);
    unset($mod['imagenes']);
    
    $categoriasArray = [];
    if ($mod['categorias']) {
        $categoriasParts = explode(', ', $mod['categorias']);
        foreach ($categoriasParts as $cat) {
            list($id, $slug, $categoria, $activo) = explode('|', $cat);
            $categoriasArray[] = [
                "id" => (int) $id,
                "slug" => $slug,
                "categoria" => $categoria,
                "activo" => (bool) $activo
            ];
        }
    }
    $mod['categorias'] = $categoriasArray;
    

    Flight::json([
        "response" => "success",
        "total_rows" => count($mod),
        "results" => $mod,
    ]);
});

Flight::route('GET /mod/id(/@id)', function($id = null) {
    $db = Flight::db();

    $query = "SELECT 
                m.id, 
                m.nombre, 
                m.descripcion,
                d.duracion, 
                e.estado, 
                en.personaje, 
                m.isNSFW, 
                t.tipo, 
                m.slug, 
                m.created, 
                m.visitas, 
                GROUP_CONCAT(DISTINCT g.genero ORDER BY g.genero SEPARATOR ', ') AS generos,
                GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN modder.id_user IS NULL THEN modder.nombre 
                        ELSE u1.alias 
                    END
                    ORDER BY CASE 
                        WHEN modder.id_user IS NULL THEN modder.nombre 
                        ELSE u1.alias 
                    END SEPARATOR ', ') AS creadores,
                GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN traductor.id_user IS NULL THEN traductor.nombre 
                        ELSE u2.alias 
                    END
                    ORDER BY CASE 
                        WHEN traductor.id_user IS NULL THEN traductor.nombre 
                        ELSE u2.alias 
                    END SEPARATOR ', ') AS traductores,
                GROUP_CONCAT(DISTINCT CONCAT(i.id_tipo_imagen, '|', i.url) ORDER BY i.url SEPARATOR ', ') AS imagenes,
                GROUP_CONCAT(DISTINCT CONCAT(enlace.id_tipo, '|', enlace.url) ORDER BY enlace.url SEPARATOR ', ') AS enlaces
            FROM mods m
            LEFT JOIN duracion_mods d ON m.id_duracion = d.id
            LEFT JOIN estado_mods e ON m.id_estado = e.id
            LEFT JOIN enfoque_mods en ON m.id_enfoque = en.id
            LEFT JOIN tipo_mod t ON m.id_tipo = t.id
            LEFT JOIN generos_mod gm ON m.id = gm.id_mod
            LEFT JOIN tipo_genero g ON gm.id_genero = g.id
            LEFT JOIN modders modder ON m.id = modder.id_mod AND modder.isActive = 1
            LEFT JOIN users u1 ON modder.id_user = u1.id
            LEFT JOIN traductores traductor ON m.id = traductor.id_mod AND traductor.isActive = 1
            LEFT JOIN users u2 ON traductor.id_user = u2.id
            LEFT JOIN imagenes_mod i ON m.id = i.id_mod
            LEFT JOIN enlaces enlace ON m.id = enlace.id_mod
            WHERE m.isPublic = 1
            GROUP BY m.id, m.nombre, d.duracion, e.estado, en.personaje, m.isNSFW, t.tipo, m.slug, m.created, m.visitas, m.isPublic";

    // Si se busca un mod específico por slug, agregamos la condición
    if ($id) {
        $query .= " HAVING m.id = :id";
    }

    $stmt = $db->prepare($query);

    if ($id) {
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    }

    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convertir cadenas separadas por comas en arrays
    foreach ($mods as &$mod) {
        $mod['generos'] = $mod['generos'] ? explode(', ', $mod['generos']) : [];
        $mod['creadores'] = $mod['creadores'] ? explode(', ', $mod['creadores']) : [];
        $mod['traductores'] = $mod['traductores'] ? explode(', ', $mod['traductores']) : [];
        // Inicializar variables
        $logo = null;
        $capturas = [];
        $portada = null;

        if ($mod['imagenes']) {
            $imagenesArray = explode(', ', $mod['imagenes']);
            foreach ($imagenesArray as $img) {
                list($tipo, $url) = explode('|', $img);
                $fullUrl = "https://api.dokidokispanish.club/" . $url;

                if ($tipo == 1) {
                    $logo = $fullUrl; // Solo un logo
                } elseif ($tipo == 2) {
                    $capturas[] = $fullUrl; // Varias capturas
                } elseif($tipo == 3){
                    $portada = $fullUrl;
                }
            }
        }

        // Asignar los valores
        $mod['logo'] = $logo;
        $mod['capturas'] = $capturas;
        $mod['portada'] = $portada ?? '';
        
        $pc = null;
        $android = null;
        if($mod['enlaces']){
            $enlacesArray = explode(', ', $mod['enlaces']);
            foreach ($enlacesArray as $enlace){
                list($tipo, $url) = explode('|', $enlace);
                
                if($tipo== 1){
                    $android = $url;
                }elseif($tipo==2){
                    $pc = $url;
                }
            }
        }
        
        $mod['pc'] = $pc;
        $mod['android'] = $android;
        unset($mod['enlaces']);

        // Eliminar la clave antigua 'imagenes'
        unset($mod['imagenes']);
    }

    Flight::json([
        "response" => "success",
        "total_rows" => $stmt->rowCount(),
        "results" => $id ? ($mods[0] ?? null) : $mods, // Si hay slug, devolver solo un objeto, si no, un array
    ]);
});

Flight::route('GET /search-mods', function() {
    $db = Flight::db();

    $query = "SELECT slug, nombre FROM mods WHERE isPublic = 1";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convertir cadenas separadas por comas en arrays
    Flight::json([
        "response"=> "success",
        "total_rows" => $stmt->rowCount(),
        "results"=> $mods,
    ]);
});

Flight::route('GET /mods/translated-mods', function() {
    $db = Flight::db();

    $query = "SELECT 
                m.id, 
                m.nombre, 
                m.descripcion,
                d.duracion, 
                e.estado, 
                en.personaje, 
                m.isNSFW, 
                t.tipo, 
                m.slug, 
                m.created, 
                m.visitas, 
                GROUP_CONCAT(DISTINCT g.genero ORDER BY g.genero SEPARATOR ', ') AS generos,
                GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN modder.id_user IS NULL THEN modder.nombre 
                        ELSE u1.alias 
                    END
                    ORDER BY CASE 
                        WHEN modder.id_user IS NULL THEN modder.nombre 
                        ELSE u1.alias 
                    END SEPARATOR ', ') AS creadores,
                GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN traductor.id_user IS NULL THEN traductor.nombre 
                        ELSE u2.alias 
                    END
                    ORDER BY CASE 
                        WHEN traductor.id_user IS NULL THEN traductor.nombre 
                        ELSE u2.alias 
                    END SEPARATOR ', ') AS traductores,
                GROUP_CONCAT(DISTINCT CONCAT(i.id_tipo_imagen, '|', i.url) ORDER BY i.url SEPARATOR ', ') AS imagenes,
                GROUP_CONCAT(DISTINCT CONCAT(enlace.id_tipo, '|', enlace.url) ORDER BY enlace.url SEPARATOR ', ') AS enlaces
            FROM mods m
            LEFT JOIN duracion_mods d ON m.id_duracion = d.id
            LEFT JOIN estado_mods e ON m.id_estado = e.id
            LEFT JOIN enfoque_mods en ON m.id_enfoque = en.id
            LEFT JOIN tipo_mod t ON m.id_tipo = t.id
            LEFT JOIN generos_mod gm ON m.id = gm.id_mod
            LEFT JOIN tipo_genero g ON gm.id_genero = g.id
            LEFT JOIN modders modder ON m.id = modder.id_mod
            LEFT JOIN users u1 ON modder.id_user = u1.id
            LEFT JOIN traductores traductor ON m.id = traductor.id_mod
            LEFT JOIN users u2 ON traductor.id_user = u2.id
            LEFT JOIN imagenes_mod i ON m.id = i.id_mod
            LEFT JOIN enlaces enlace ON m.id = enlace.id_mod
            WHERE m.id_tipo = 1 AND m.isPublic = 1
            GROUP BY m.id, m.nombre, d.duracion, e.estado, en.personaje, m.isNSFW, t.tipo, m.slug, m.created, m.visitas, m.isPublic";

    // Si se busca un mod específico por slug, agregamos la condición
    $stmt = $db->prepare($query);
    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convertir cadenas separadas por comas en arrays
    foreach ($mods as &$mod) {
        $mod['generos'] = $mod['generos'] ? explode(', ', $mod['generos']) : [];
        $mod['creadores'] = $mod['creadores'] ? explode(', ', $mod['creadores']) : [];
        $mod['traductores'] = $mod['traductores'] ? explode(', ', $mod['traductores']) : [];
       // Inicializar variables
        $logo = null;
        $capturas = [];
        $portada = null;

        if ($mod['imagenes']) {
            $imagenesArray = explode(', ', $mod['imagenes']);
            foreach ($imagenesArray as $img) {
                list($tipo, $url) = explode('|', $img);
                $fullUrl = "https://api.dokidokispanish.club/" . $url;

                if ($tipo == 1) {
                    $logo = $fullUrl; // Solo un logo
                } elseif ($tipo == 2) {
                    $capturas[] = $fullUrl; // Varias capturas
                } elseif($tipo == 3){
                    $portada = $fullUrl;
                }
            }
        }

        // Asignar los valores
        $mod['logo'] = $logo;
        $mod['capturas'] = $capturas;
        $mod['portada'] = $portada ?? '';
        
        $pc = null;
        $android = null;
        if($mod['enlaces']){
            $enlacesArray = explode(', ', $mod['enlaces']);
            foreach ($enlacesArray as $enlace){
                list($tipo, $url) = explode('|', $enlace);
                
                if($tipo== 1){
                    $android = $url;
                }elseif($tipo==2){
                    $pc = $url;
                }
            }
        }
        
        $mod['pc'] = $pc;
        $mod['android'] = $android;
        unset($mod['enlaces']);

        // Eliminar la clave antigua 'imagenes'
        unset($mod['imagenes']);
    }

    Flight::json([
        "response" => "success",
        "total_rows" => $stmt->rowCount(),
        "results" => $mods, // Si hay slug, devolver solo un objeto, si no, un array
    ]);
});

Flight::route('GET /mods/mods-recents', function() {
    $db = Flight::db();

    $query = "SELECT 
                m.id, 
                m.nombre, 
                m.descripcion,
                d.duracion, 
                e.estado, 
                en.personaje, 
                m.isNSFW, 
                t.tipo, 
                m.slug, 
                m.created, 
                m.visitas, 
                GROUP_CONCAT(DISTINCT g.genero ORDER BY g.genero SEPARATOR ', ') AS generos,
                GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN modder.id_user IS NULL THEN modder.nombre 
                        ELSE u1.alias 
                    END
                    ORDER BY CASE 
                        WHEN modder.id_user IS NULL THEN modder.nombre 
                        ELSE u1.alias 
                    END SEPARATOR ', ') AS creadores,
                GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN traductor.id_user IS NULL THEN traductor.nombre 
                        ELSE u2.alias 
                    END
                    ORDER BY CASE 
                        WHEN traductor.id_user IS NULL THEN traductor.nombre 
                        ELSE u2.alias 
                    END SEPARATOR ', ') AS traductores,
                GROUP_CONCAT(DISTINCT CONCAT(i.id_tipo_imagen, '|', i.url) ORDER BY i.url SEPARATOR ', ') AS imagenes,
                GROUP_CONCAT(DISTINCT CONCAT(enlace.id_tipo, '|', enlace.url) ORDER BY enlace.url SEPARATOR ', ') AS enlaces
            FROM mods m
            LEFT JOIN duracion_mods d ON m.id_duracion = d.id
            LEFT JOIN estado_mods e ON m.id_estado = e.id
            LEFT JOIN enfoque_mods en ON m.id_enfoque = en.id
            LEFT JOIN tipo_mod t ON m.id_tipo = t.id
            LEFT JOIN generos_mod gm ON m.id = gm.id_mod
            LEFT JOIN tipo_genero g ON gm.id_genero = g.id
            LEFT JOIN modders modder ON m.id = modder.id_mod AND modder.isActive = 1
            LEFT JOIN users u1 ON modder.id_user = u1.id
            LEFT JOIN traductores traductor ON m.id = traductor.id_mod AND traductor.isActive = 1
            LEFT JOIN users u2 ON traductor.id_user = u2.id
            LEFT JOIN imagenes_mod i ON m.id = i.id_mod
            LEFT JOIN enlaces enlace ON m.id = enlace.id_mod
            WHERE m.isPublic = 1
            GROUP BY m.id, m.nombre, d.duracion, e.estado, en.personaje, m.isNSFW, t.tipo, m.slug, m.created, m.visitas, m.isPublic";

    // Si se busca un mod específico por slug, agregamos la condición
    $query .= " ORDER BY m.id DESC LIMIT 10";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convertir cadenas separadas por comas en arrays
    foreach ($mods as &$mod) {
        $mod['generos'] = $mod['generos'] ? explode(', ', $mod['generos']) : [];
        $mod['creadores'] = $mod['creadores'] ? explode(', ', $mod['creadores']) : [];
        $mod['traductores'] = $mod['traductores'] ? explode(', ', $mod['traductores']) : [];
        // Inicializar variables
        $logo = null;
        $capturas = [];
        $portada = null;

        if ($mod['imagenes']) {
            $imagenesArray = explode(', ', $mod['imagenes']);
            foreach ($imagenesArray as $img) {
                list($tipo, $url) = explode('|', $img);
                $fullUrl = "https://api.dokidokispanish.club/" . $url;

                if ($tipo == 1) {
                    $logo = $fullUrl; // Solo un logo
                } elseif ($tipo == 2) {
                    $capturas[] = $fullUrl; // Varias capturas
                } elseif($tipo== 3){
                    $portada = $fullUrl;
                }
            }
        }

        // Asignar los valores
        $mod['logo'] = $logo;
        $mod['capturas'] = $capturas;
        $mod['portada'] = $portada ?? '';
        
        $pc = null;
        $android = null;
        if($mod['enlaces']){
            $enlacesArray = explode(', ', $mod['enlaces']);
            foreach ($enlacesArray as $enlace){
                list($tipo, $url) = explode('|', $enlace);
                
                if($tipo== 1){
                    $android = $url;
                }elseif($tipo==2){
                    $pc = $url;
                } elseif($tipo == 3){
                    $tipo = $fullUrl;
                }
            }
        }
        
        $mod['pc'] = $pc;
        $mod['android'] = $android;
        $mod['portada'] = $portada ?? '';
        unset($mod['enlaces']);

        // Eliminar la clave antigua 'imagenes'
        unset($mod['imagenes']);
    }

    Flight::json([
        "response" => "success",
        "total_rows" => $stmt->rowCount(),
        "results" => $mods, // Si hay slug, devolver solo un objeto, si no, un array
    ]);
});

Flight::route('GET /last-update', function() {
    $db = Flight::db();

    $query = "SELECT * FROM updates u ORDER BY u.id DESC LIMIT 1";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $mods = $stmt->fetch(PDO::FETCH_ASSOC);

    Flight::json([
        "response" => "success",
        "total_rows" => $stmt->rowCount(),
        "results" => $mods, // Si hay slug, devolver solo un objeto, si no, un array
    ]);
});

Flight::route('GET /all-updates', function() {
    $db = Flight::db();

    $query = "SELECT * FROM updates u ORDER BY u.id DESC";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    Flight::json([
        "response" => "success",
        "total_rows" => $stmt->rowCount(),
        "results" => $mods, // Si hay slug, devolver solo un objeto, si no, un array
    ]);
});

Flight::route('GET /mods/top-rated', function() {
    $db = Flight::db();

    // Consulta con promedio de estrellas
    $query = "
        SELECT 
            m.id, 
            m.nombre, 
            m.descripcion,
            d.duracion, 
            e.estado, 
            en.personaje, 
            m.isNSFW, 
            t.tipo, 
            m.slug, 
            m.created, 
            m.visitas,
            AVG(p.stars) AS promedio_stars, 
            COUNT(p.id) AS total_valoraciones,

            GROUP_CONCAT(DISTINCT g.genero ORDER BY g.genero SEPARATOR ', ') AS generos,
            GROUP_CONCAT(DISTINCT 
                CASE 
                    WHEN modder.id_user IS NULL THEN modder.nombre 
                    ELSE u1.alias 
                END
                ORDER BY CASE 
                    WHEN modder.id_user IS NULL THEN modder.nombre 
                    ELSE u1.alias 
                END SEPARATOR ', ') AS creadores,
            GROUP_CONCAT(DISTINCT 
                CASE 
                    WHEN traductor.id_user IS NULL THEN traductor.nombre 
                    ELSE u2.alias 
                END
                ORDER BY CASE 
                    WHEN traductor.id_user IS NULL THEN traductor.nombre 
                    ELSE u2.alias 
                END SEPARATOR ', ') AS traductores,
            GROUP_CONCAT(DISTINCT CONCAT(i.id_tipo_imagen, '|', i.url) ORDER BY i.url SEPARATOR ', ') AS imagenes,
            GROUP_CONCAT(DISTINCT CONCAT(enlace.id_tipo, '|', enlace.url) ORDER BY enlace.url SEPARATOR ', ') AS enlaces

        FROM mods m
        LEFT JOIN duracion_mods d ON m.id_duracion = d.id
        LEFT JOIN estado_mods e ON m.id_estado = e.id
        LEFT JOIN enfoque_mods en ON m.id_enfoque = en.id
        LEFT JOIN tipo_mod t ON m.id_tipo = t.id
        LEFT JOIN generos_mod gm ON m.id = gm.id_mod
        LEFT JOIN tipo_genero g ON gm.id_genero = g.id
        LEFT JOIN modders modder ON m.id = modder.id_mod AND modder.isActive = 1
        LEFT JOIN users u1 ON modder.id_user = u1.id
        LEFT JOIN traductores traductor ON m.id = traductor.id_mod AND traductor.isActive = 1
        LEFT JOIN users u2 ON traductor.id_user = u2.id
        LEFT JOIN imagenes_mod i ON m.id = i.id_mod
        LEFT JOIN enlaces enlace ON m.id = enlace.id_mod
        LEFT JOIN puntuation p ON m.id = p.id_mod

        WHERE m.isPublic = 1
        GROUP BY 
            m.id, m.nombre, d.duracion, e.estado, en.personaje, m.isNSFW, 
            t.tipo, m.slug, m.created, m.visitas, m.isPublic

        HAVING promedio_stars IS NOT NULL
        ORDER BY promedio_stars DESC, total_valoraciones DESC
        LIMIT 10
    ";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Procesamiento igual al de /mods-recents
    foreach ($mods as &$mod) {
        $mod['promedio_stars'] = round((float) $mod['promedio_stars'], 2);
        $mod['total_valoraciones'] = (int) $mod['total_valoraciones'];

        $mod['generos'] = $mod['generos'] ? explode(', ', $mod['generos']) : [];
        $mod['creadores'] = $mod['creadores'] ? explode(', ', $mod['creadores']) : [];
        $mod['traductores'] = $mod['traductores'] ? explode(', ', $mod['traductores']) : [];

        $logo = null;
        $capturas = [];
        $portada = null;

        if ($mod['imagenes']) {
            $imagenesArray = explode(', ', $mod['imagenes']);
            foreach ($imagenesArray as $img) {
                list($tipo, $url) = explode('|', $img);
                $fullUrl = "https://api.dokidokispanish.club/" . $url;

                if ($tipo == 1) {
                    $logo = $fullUrl;
                } elseif ($tipo == 2) {
                    $capturas[] = $fullUrl;
                } elseif ($tipo == 3) {
                    $portada = $fullUrl;
                }
            }
        }

        $mod['logo'] = $logo;
        $mod['capturas'] = $capturas;
        $mod['portada'] = $portada ?? '';

        $pc = null;
        $android = null;
        if ($mod['enlaces']) {
            $enlacesArray = explode(', ', $mod['enlaces']);
            foreach ($enlacesArray as $enlace) {
                list($tipo, $url) = explode('|', $enlace);
                if ($tipo == 1) {
                    $android = $url;
                } elseif ($tipo == 2) {
                    $pc = $url;
                }
            }
        }

        $mod['pc'] = $pc;
        $mod['android'] = $android;

        unset($mod['imagenes'], $mod['enlaces']);
    }

    Flight::json([
        "response" => "success",
        "total_rows" => $stmt->rowCount(),
        "results" => $mods
    ]);
});

Flight::route('GET /mods/translated-mods-recents', function() {
    $db = Flight::db();

    $query = "SELECT 
                m.id, 
                m.nombre, 
                m.descripcion,
                d.duracion, 
                e.estado, 
                en.personaje, 
                m.isNSFW, 
                t.tipo, 
                m.slug, 
                m.created, 
                m.visitas, 
                GROUP_CONCAT(DISTINCT g.genero ORDER BY g.genero SEPARATOR ', ') AS generos,
                GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN modder.id_user IS NULL THEN modder.nombre 
                        ELSE u1.alias 
                    END
                    ORDER BY CASE 
                        WHEN modder.id_user IS NULL THEN modder.nombre 
                        ELSE u1.alias 
                    END SEPARATOR ', ') AS creadores,
                GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN traductor.id_user IS NULL THEN traductor.nombre 
                        ELSE u2.alias 
                    END
                    ORDER BY CASE 
                        WHEN traductor.id_user IS NULL THEN traductor.nombre 
                        ELSE u2.alias 
                    END SEPARATOR ', ') AS traductores,
                GROUP_CONCAT(DISTINCT CONCAT(i.id_tipo_imagen, '|', i.url) ORDER BY i.url SEPARATOR ', ') AS imagenes,
                GROUP_CONCAT(DISTINCT CONCAT(enlace.id_tipo, '|', enlace.url) ORDER BY enlace.url SEPARATOR ', ') AS enlaces
            FROM mods m
            LEFT JOIN duracion_mods d ON m.id_duracion = d.id
            LEFT JOIN estado_mods e ON m.id_estado = e.id
            LEFT JOIN enfoque_mods en ON m.id_enfoque = en.id
            LEFT JOIN tipo_mod t ON m.id_tipo = t.id
            LEFT JOIN generos_mod gm ON m.id = gm.id_mod
            LEFT JOIN tipo_genero g ON gm.id_genero = g.id
            LEFT JOIN modders modder ON m.id = modder.id_mod AND modder.isActive = 1
            LEFT JOIN users u1 ON modder.id_user = u1.id
            LEFT JOIN traductores traductor ON m.id = traductor.id_mod AND traductor.isActive = 1
            LEFT JOIN users u2 ON traductor.id_user = u2.id
            LEFT JOIN imagenes_mod i ON m.id = i.id_mod
            LEFT JOIN enlaces enlace ON m.id = enlace.id_mod
            WHERE m.id_tipo = 1 AND m.isPublic = 1
            GROUP BY m.id, m.nombre, d.duracion, e.estado, en.personaje, m.isNSFW, t.tipo, m.slug, m.created, m.visitas, m.isPublic";

    // Si se busca un mod específico por slug, agregamos la condición
    $query .= " ORDER BY m.created DESC LIMIT 10";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convertir cadenas separadas por comas en arrays
    foreach ($mods as &$mod) {
        $mod['generos'] = $mod['generos'] ? explode(', ', $mod['generos']) : [];
        $mod['creadores'] = $mod['creadores'] ? explode(', ', $mod['creadores']) : [];
        $mod['traductores'] = $mod['traductores'] ? explode(', ', $mod['traductores']) : [];
        // Inicializar variables
        $logo = null;
        $capturas = [];
        $portada = null;

        if ($mod['imagenes']) {
            $imagenesArray = explode(', ', $mod['imagenes']);
            foreach ($imagenesArray as $img) {
                list($tipo, $url) = explode('|', $img);
                $fullUrl = "https://api.dokidokispanish.club/" . $url;

                if ($tipo == 1) {
                    $logo = $fullUrl; // Solo un logo
                } elseif ($tipo == 2) {
                    $capturas[] = $fullUrl; // Varias capturas
                } elseif($tipo== 3){
                    $portada = $fullUrl;
                }
            }
        }

        // Asignar los valores
        $mod['logo'] = $logo;
        $mod['capturas'] = $capturas;
        $mod['portada'] = $portada ?? '';
        
        $pc = null;
        $android = null;
        if($mod['enlaces']){
            $enlacesArray = explode(', ', $mod['enlaces']);
            foreach ($enlacesArray as $enlace){
                list($tipo, $url) = explode('|', $enlace);
                
                if($tipo== 1){
                    $android = $url;
                }elseif($tipo==2){
                    $pc = $url;
                } elseif($tipo == 3){
                    $tipo = $fullUrl;
                }
            }
        }
        
        $mod['pc'] = $pc;
        $mod['android'] = $android;
        $mod['portada'] = $portada ?? '';
        unset($mod['enlaces']);

        // Eliminar la clave antigua 'imagenes'
        unset($mod['imagenes']);
    }

    Flight::json([
        "response" => "success",
        "total_rows" => $stmt->rowCount(),
        "results" => $mods, // Si hay slug, devolver solo un objeto, si no, un array
    ]);
});

Flight::route('GET /mods/community-mods', function() {
    $db = Flight::db();

    $query = "SELECT 
                m.id, 
                m.nombre, 
                m.descripcion,
                d.duracion, 
                e.estado, 
                en.personaje, 
                m.isNSFW, 
                t.tipo, 
                m.slug, 
                m.created, 
                m.visitas, 
                GROUP_CONCAT(DISTINCT g.genero ORDER BY g.genero SEPARATOR ', ') AS generos,
                GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN modder.id_user IS NULL THEN modder.nombre 
                        ELSE u1.alias 
                    END
                    ORDER BY CASE 
                        WHEN modder.id_user IS NULL THEN modder.nombre 
                        ELSE u1.alias 
                    END SEPARATOR ', ') AS creadores,
                GROUP_CONCAT(DISTINCT CONCAT(i.id_tipo_imagen, '|', i.url) ORDER BY i.url SEPARATOR ', ') AS imagenes,
                GROUP_CONCAT(DISTINCT CONCAT(enlace.id_tipo, '|', enlace.url) ORDER BY enlace.url SEPARATOR ', ') AS enlaces
            FROM mods m
            LEFT JOIN duracion_mods d ON m.id_duracion = d.id
            LEFT JOIN estado_mods e ON m.id_estado = e.id
            LEFT JOIN enfoque_mods en ON m.id_enfoque = en.id
            LEFT JOIN tipo_mod t ON m.id_tipo = t.id
            LEFT JOIN generos_mod gm ON m.id = gm.id_mod
            LEFT JOIN tipo_genero g ON gm.id_genero = g.id
            LEFT JOIN modders modder ON m.id = modder.id_mod AND modder.isActive = 1
            LEFT JOIN users u1 ON modder.id_user = u1.id
            LEFT JOIN imagenes_mod i ON m.id = i.id_mod
            LEFT JOIN enlaces enlace ON m.id = enlace.id_mod
            WHERE m.id_tipo = 2 AND m.isPublic = 1
            GROUP BY m.id, m.nombre, d.duracion, e.estado, en.personaje, m.isNSFW, t.tipo, m.slug, m.created, m.visitas, m.isPublic";

    // Si se busca un mod específico por slug, agregamos la condición
    $stmt = $db->prepare($query);
    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convertir cadenas separadas por comas en arrays
    foreach ($mods as &$mod) {
        $mod['generos'] = $mod['generos'] ? explode(', ', $mod['generos']) : [];
        $mod['creadores'] = $mod['creadores'] ? explode(', ', $mod['creadores']) : [];
        // Inicializar variables
        $logo = null;
        $capturas = [];
        $portada = null;

        if ($mod['imagenes']) {
            $imagenesArray = explode(', ', $mod['imagenes']);
            foreach ($imagenesArray as $img) {
                list($tipo, $url) = explode('|', $img);
                $fullUrl = "https://api.dokidokispanish.club/" . $url;

                if ($tipo == 1) {
                    $logo = $fullUrl; // Solo un logo
                } elseif ($tipo == 2) {
                    $capturas[] = $fullUrl; // Varias capturas
                } elseif($tipo == 3){
                    $portada = $fullUrl;
                }
            }
        }

        // Asignar los valores
        $mod['logo'] = $logo;
        $mod['capturas'] = $capturas;
        $mod['portada'] = $portada ?? '';
        
        $pc = null;
        $android = null;
        if($mod['enlaces']){
            $enlacesArray = explode(', ', $mod['enlaces']);
            foreach ($enlacesArray as $enlace){
                list($tipo, $url) = explode('|', $enlace);
                
                if($tipo== 1){
                    $android = $url;
                }elseif($tipo==2){
                    $pc = $url;
                }
            }
        }
        
        $mod['pc'] = $pc;
        $mod['android'] = $android;
        unset($mod['enlaces']);

        // Eliminar la clave antigua 'imagenes'
        unset($mod['imagenes']);
    }

    Flight::json([
        "response" => "success",
        "total_rows" => $stmt->rowCount(),
        "results" => $mods, // Si hay slug, devolver solo un objeto, si no, un array
    ]);
});

Flight::route('GET /mods/community-mods-recents', function() {
    $db = Flight::db();

    $query = "SELECT 
                m.id, 
                m.nombre, 
                m.descripcion,
                d.duracion, 
                e.estado, 
                en.personaje, 
                m.isNSFW, 
                t.tipo, 
                m.slug, 
                m.created, 
                m.visitas, 
                GROUP_CONCAT(DISTINCT g.genero ORDER BY g.genero SEPARATOR ', ') AS generos,
                GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN modder.id_user IS NULL THEN modder.nombre 
                        ELSE u1.alias 
                    END
                    ORDER BY CASE 
                        WHEN modder.id_user IS NULL THEN modder.nombre 
                        ELSE u1.alias 
                    END SEPARATOR ', ') AS creadores,
                GROUP_CONCAT(DISTINCT CONCAT(i.id_tipo_imagen, '|', i.url) ORDER BY i.url SEPARATOR ', ') AS imagenes,
                GROUP_CONCAT(DISTINCT CONCAT(enlace.id_tipo, '|', enlace.url) ORDER BY enlace.url SEPARATOR ', ') AS enlaces
            FROM mods m
            LEFT JOIN duracion_mods d ON m.id_duracion = d.id
            LEFT JOIN estado_mods e ON m.id_estado = e.id
            LEFT JOIN enfoque_mods en ON m.id_enfoque = en.id
            LEFT JOIN tipo_mod t ON m.id_tipo = t.id
            LEFT JOIN generos_mod gm ON m.id = gm.id_mod
            LEFT JOIN tipo_genero g ON gm.id_genero = g.id
            LEFT JOIN modders modder ON m.id = modder.id_mod
            LEFT JOIN users u1 ON modder.id_user = u1.id
            LEFT JOIN imagenes_mod i ON m.id = i.id_mod
            LEFT JOIN enlaces enlace ON m.id = enlace.id_mod
            WHERE m.id_tipo = 2 AND m.isPublic = 1
            GROUP BY m.id, m.nombre, d.duracion, e.estado, en.personaje, m.isNSFW, t.tipo, m.slug, m.created, m.visitas, m.isPublic";

    // Si se busca un mod específico por slug, agregamos la condición
    $query .= " ORDER BY m.id DESC LIMIT 10";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convertir cadenas separadas por comas en arrays
    foreach ($mods as &$mod) {
        $mod['generos'] = $mod['generos'] ? explode(', ', $mod['generos']) : [];
        $mod['creadores'] = $mod['creadores'] ? explode(', ', $mod['creadores']) : [];
        // Inicializar variables
        $logo = null;
        $capturas = [];
        $portada= null;

        if ($mod['imagenes']) {
            $imagenesArray = explode(', ', $mod['imagenes']);
            foreach ($imagenesArray as $img) {
                list($tipo, $url) = explode('|', $img);
                $fullUrl = "https://api.dokidokispanish.club/" . $url;

                if ($tipo == 1) {
                    $logo = $fullUrl; // Solo un logo
                } elseif ($tipo == 2) {
                    $capturas[] = $fullUrl; // Varias capturas
                } elseif($tipo == 3){
                    $portada = $fullUrl;
                }
            }
        }

        // Asignar los valores
        $mod['logo'] = $logo;
        $mod['capturas'] = $capturas;
        $mod['portada'] = $portada ?? '';
        
        $pc = null;
        $android = null;
        if($mod['enlaces']){
            $enlacesArray = explode(', ', $mod['enlaces']);
            foreach ($enlacesArray as $enlace){
                list($tipo, $url) = explode('|', $enlace);
                
                if($tipo== 1){
                    $android = $url;
                }elseif($tipo==2){
                    $pc = $url;
                }
            }
        }
        
        $mod['pc'] = $pc;
        $mod['android'] = $android;
        unset($mod['enlaces']);

        // Eliminar la clave antigua 'imagenes'
        unset($mod['imagenes']);
    }

    Flight::json([
        "response" => "success",
        "total_rows" => $stmt->rowCount(),
        "results" =>$mods, // Si hay slug, devolver solo un objeto, si no, un array
    ]);
});

Flight::route('GET /mods/selection', function() {
    $db = Flight::db();

    $query = "SELECT 
                m.id, 
                m.nombre, 
                m.descripcion,
                d.duracion, 
                e.estado, 
                en.personaje, 
                m.isNSFW, 
                t.tipo, 
                m.slug, 
                m.created, 
                m.visitas, 
                GROUP_CONCAT(DISTINCT g.genero ORDER BY g.genero SEPARATOR ', ') AS generos,
                GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN modder.id_user IS NULL THEN modder.nombre 
                        ELSE u1.alias 
                    END
                    ORDER BY CASE 
                        WHEN modder.id_user IS NULL THEN modder.nombre 
                        ELSE u1.alias 
                    END SEPARATOR ', ') AS creadores,
                GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN traductor.id_user IS NULL THEN traductor.nombre 
                        ELSE u2.alias 
                    END
                    ORDER BY CASE 
                        WHEN traductor.id_user IS NULL THEN traductor.nombre 
                        ELSE u2.alias 
                    END SEPARATOR ', ') AS traductores,
                GROUP_CONCAT(DISTINCT CONCAT(i.id_tipo_imagen, '|', i.url) ORDER BY i.url SEPARATOR ', ') AS imagenes,
                GROUP_CONCAT(DISTINCT CONCAT(enlace.id_tipo, '|', enlace.url) ORDER BY enlace.url SEPARATOR ', ') AS enlaces
            FROM mods m
            LEFT JOIN duracion_mods d ON m.id_duracion = d.id
            LEFT JOIN estado_mods e ON m.id_estado = e.id
            LEFT JOIN enfoque_mods en ON m.id_enfoque = en.id
            LEFT JOIN tipo_mod t ON m.id_tipo = t.id
            LEFT JOIN generos_mod gm ON m.id = gm.id_mod
            LEFT JOIN tipo_genero g ON gm.id_genero = g.id
            LEFT JOIN modders modder ON m.id = modder.id_mod
            LEFT JOIN users u1 ON modder.id_user = u1.id
            LEFT JOIN traductores traductor ON m.id = traductor.id_mod
            LEFT JOIN users u2 ON traductor.id_user = u2.id
            LEFT JOIN imagenes_mod i ON m.id = i.id_mod
            LEFT JOIN enlaces enlace ON m.id = enlace.id_mod
            WHERE m.isSelection = 1 AND m.isPublic = 1
            GROUP BY m.id, m.nombre, d.duracion, e.estado, en.personaje, m.isNSFW, t.tipo, m.slug, m.created, m.visitas, m.isPublic";

    // Si se busca un mod específico por slug, agregamos la condición
    $stmt = $db->prepare($query);
    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convertir cadenas separadas por comas en arrays
    foreach ($mods as &$mod) {
        $mod['generos'] = $mod['generos'] ? explode(', ', $mod['generos']) : [];
        $mod['creadores'] = $mod['creadores'] ? explode(', ', $mod['creadores']) : [];
        $mod['traductores'] = $mod['traductores'] ? explode(', ', $mod['traductores']) : [];
        // Inicializar variables
        $logo = null;
        $capturas = [];
        $portada = null;

        if ($mod['imagenes']) {
            $imagenesArray = explode(', ', $mod['imagenes']);
            foreach ($imagenesArray as $img) {
                list($tipo, $url) = explode('|', $img);
                $fullUrl = "https://api.dokidokispanish.club/" . $url;

                if ($tipo == 1) {
                    $logo = $fullUrl; // Solo un logo
                } elseif ($tipo == 2) {
                    $capturas[] = $fullUrl; // Varias capturas
                } elseif($tipo == 3){
                    $portada = $fullUrl;
                }
            }
        }

        // Asignar los valores
        $mod['logo'] = $logo;
        $mod['capturas'] = $capturas;
        $mod['portada'] = $portada ?? '';
        
        $pc = null;
        $android = null;
        if($mod['enlaces']){
            $enlacesArray = explode(', ', $mod['enlaces']);
            foreach ($enlacesArray as $enlace){
                list($tipo, $url) = explode('|', $enlace);
                
                if($tipo== 1){
                    $android = $url;
                }elseif($tipo==2){
                    $pc = $url;
                }
            }
        }
        
        $mod['pc'] = $pc;
        $mod['android'] = $android;
        unset($mod['enlaces']);

        // Eliminar la clave antigua 'imagenes'
        unset($mod['imagenes']);
    }

    Flight::json([
        "response" => "success",
        "total_rows" => $stmt->rowCount(),
        "results" => $mods, // Si hay slug, devolver solo un objeto, si no, un array
    ]);
});

Flight::route('GET /mods/most-watched-mods', function() {
    $db = Flight::db();

    $query = "SELECT 
                m.id, 
                m.nombre, 
                m.descripcion,
                d.duracion, 
                e.estado, 
                en.personaje, 
                m.isNSFW, 
                t.tipo, 
                m.slug, 
                m.created, 
                m.visitas, 
                GROUP_CONCAT(DISTINCT g.genero ORDER BY g.genero SEPARATOR ', ') AS generos,
                GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN modder.id_user IS NULL THEN modder.nombre 
                        ELSE u1.alias 
                    END
                    ORDER BY CASE 
                        WHEN modder.id_user IS NULL THEN modder.nombre 
                        ELSE u1.alias 
                    END SEPARATOR ', ') AS creadores,
                GROUP_CONCAT(DISTINCT 
                    CASE 
                        WHEN traductor.id_user IS NULL THEN traductor.nombre 
                        ELSE u2.alias 
                    END
                    ORDER BY CASE 
                        WHEN traductor.id_user IS NULL THEN traductor.nombre 
                        ELSE u2.alias 
                    END SEPARATOR ', ') AS traductores,
                GROUP_CONCAT(DISTINCT CONCAT(i.id_tipo_imagen, '|', i.url) ORDER BY i.url SEPARATOR ', ') AS imagenes,
                GROUP_CONCAT(DISTINCT CONCAT(enlace.id_tipo, '|', enlace.url) ORDER BY enlace.url SEPARATOR ', ') AS enlaces
            FROM mods m
            LEFT JOIN duracion_mods d ON m.id_duracion = d.id
            LEFT JOIN estado_mods e ON m.id_estado = e.id
            LEFT JOIN enfoque_mods en ON m.id_enfoque = en.id
            LEFT JOIN tipo_mod t ON m.id_tipo = t.id
            LEFT JOIN generos_mod gm ON m.id = gm.id_mod
            LEFT JOIN tipo_genero g ON gm.id_genero = g.id
            LEFT JOIN modders modder ON m.id = modder.id_mod
            LEFT JOIN users u1 ON modder.id_user = u1.id
            LEFT JOIN traductores traductor ON m.id = traductor.id_mod
            LEFT JOIN users u2 ON traductor.id_user = u2.id
            LEFT JOIN imagenes_mod i ON m.id = i.id_mod
            LEFT JOIN enlaces enlace ON m.id = enlace.id_mod
            WHERE m.isPublic = 1
            GROUP BY m.id, m.nombre, d.duracion, e.estado, en.personaje, m.isNSFW, t.tipo, m.slug, m.created, m.visitas, m.isPublic";

    // Si se busca un mod específico por slug, agregamos la condición
    $query .= " ORDER BY m.visitas DESC LIMIT 10";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convertir cadenas separadas por comas en arrays
    foreach ($mods as &$mod) {
        $mod['generos'] = $mod['generos'] ? explode(', ', $mod['generos']) : [];
        $mod['creadores'] = $mod['creadores'] ? explode(', ', $mod['creadores']) : [];
        $mod['traductores'] = $mod['traductores'] ? explode(', ', $mod['traductores']) : [];
        // Inicializar variables
        $logo = null;
        $capturas = [];
        $portada = null;

        if ($mod['imagenes']) {
            $imagenesArray = explode(', ', $mod['imagenes']);
            foreach ($imagenesArray as $img) {
                list($tipo, $url) = explode('|', $img);
                $fullUrl = "https://api.dokidokispanish.club/" . $url;

                if ($tipo == 1) {
                    $logo = $fullUrl; // Solo un logo
                } elseif ($tipo == 2) {
                    $capturas[] = $fullUrl; // Varias capturas
                } elseif($tipo == 3){
                    $portada = $fullUrl;
                }
            }
        }

        // Asignar los valores
        $mod['logo'] = $logo;
        $mod['capturas'] = $capturas;
        $mod['portada'] = $portada ?? '';
        
        $pc = null;
        $android = null;
        if($mod['enlaces']){
            $enlacesArray = explode(', ', $mod['enlaces']);
            foreach ($enlacesArray as $enlace){
                list($tipo, $url) = explode('|', $enlace);
                
                if($tipo== 1){
                    $android = $url;
                }elseif($tipo==2){
                    $pc = $url;
                }
            }
        }
        
        $mod['pc'] = $pc;
        $mod['android'] = $android;
        unset($mod['enlaces']);

        // Eliminar la clave antigua 'imagenes'
        unset($mod['imagenes']);
    }

    Flight::json([
        "response" => "success",
        "total_rows" => $stmt->rowCount(),
        "results" => $mods, // Si hay slug, devolver solo un objeto, si no, un array
    ]);
});

Flight::route('GET /categories-mod', function () {
    
    //conectamos a la base de datos y preparamos la query
    $sentencia = Flight::db()->prepare("SELECT * FROM `categories`");
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


Flight::route('GET /terminated-mods/@id_usuario', function($id_usuario) {
    $db = Flight::db();
    $query = "SELECT 
                m.id, 
                m.nombre, 
                m.descripcion,
                d.duracion, 
                e.estado, 
                en.personaje, 
                m.isNSFW, 
                t.tipo, 
                m.slug, 
                m.created, 
                m.visitas, 
                m.isPublic,
                IFNULL(GROUP_CONCAT(DISTINCT g.genero ORDER BY g.genero SEPARATOR ', '), '') AS generos,
                IFNULL(GROUP_CONCAT(DISTINCT CONCAT(i.id_tipo_imagen, '|', i.url) ORDER BY i.url SEPARATOR ', '), '') AS imagenes,
                IFNULL(GROUP_CONCAT(DISTINCT CONCAT(enlace.id_tipo, '|', enlace.url) ORDER BY enlace.url SEPARATOR ', '), '') AS enlaces,
                IFNULL(GROUP_CONCAT(DISTINCT CONCAT(c.id, '|', c.slug, '|', c.categoria, '|', c.active) 
                    ORDER BY c.categoria SEPARATOR ', '), '') AS categorias
            FROM terminated_mods tm
            INNER JOIN mods m ON tm.id_mod = m.id
            LEFT JOIN duracion_mods d ON m.id_duracion = d.id
            LEFT JOIN estado_mods e ON m.id_estado = e.id
            LEFT JOIN enfoque_mods en ON m.id_enfoque = en.id
            LEFT JOIN tipo_mod t ON m.id_tipo = t.id
            LEFT JOIN generos_mod gm ON m.id = gm.id_mod
            LEFT JOIN tipo_genero g ON gm.id_genero = g.id
            LEFT JOIN imagenes_mod i ON m.id = i.id_mod
            LEFT JOIN enlaces enlace ON m.id = enlace.id_mod
            LEFT JOIN mod_category mc ON m.id = mc.id_mod
            LEFT JOIN categories c ON mc.id_category = c.id
            WHERE tm.id_usuario = :id_usuario
            GROUP BY m.id
            ";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ——— MISMA ESTRUCTURA DE PROCESAMIENTO QUE LA RUTA ORIGINAL ——— //
    foreach ($mods as &$mod) {
        $mod['isNSFW'] = (bool)$mod["isNSFW"];
        $mod['isPublic'] = (bool)$mod["isPublic"];
        $mod['generos'] = $mod['generos'] ? explode(', ', $mod['generos']) : [];
        
        $logo = null;
        $portada = null;

        if ($mod['imagenes']) {
            $imagenesArray = explode(', ', $mod['imagenes']);
            foreach ($imagenesArray as $img) {
                list($tipo, $url) = explode('|', $img);
                $tipo = (int) $tipo;
                $fullUrl = empty($url)
                    ? "https://api.dokidokispanish.club/gui/Imagen-no-disponible.jpg"
                    : "https://api.dokidokispanish.club/" . $url;

                if ($tipo === 1) $logo = $fullUrl;
                elseif ($tipo === 3) $portada = $fullUrl;
            }
        }

        $mod['logo'] = $logo;
        $mod['portada'] = $portada;

        $pc = null;
        $android = null;
        if ($mod['enlaces']) {
            $enlacesArray = explode(', ', $mod['enlaces']);
            foreach ($enlacesArray as $enlace) {
                list($tipo, $url) = explode('|', $enlace);
                if ($tipo == 1) $android = $url;
                elseif ($tipo == 2) $pc = $url;
            }
        }
        
        $categoriasArray = [];
        if ($mod['categorias']) {
            $categoriasParts = explode(', ', $mod['categorias']);
            foreach ($categoriasParts as $cat) {
                list($id, $slug, $categoria, $activo) = explode('|', $cat);
                $categoriasArray[] = [
                    "id" => (int) $id,
                    "slug" => $slug,
                    "categoria" => $categoria,
                    "activo" => (bool) $activo
                ];
            }
        }

        $mod['categorias'] = $categoriasArray;
        $mod['pc'] = $pc;
        $mod['android'] = $android;

        unset($mod['enlaces'], $mod['imagenes']);
    }

    Flight::json([
        "response" => "success",
        "total_rows" => count($mods),
        "results" => $mods,
    ]);
});


//utilizamos un metodo get
Flight::route('GET /wallpapers', function () {
    
    //conectamos a la base de datos y preparamos la query
    $sentencia = Flight::dbAntigua()->prepare("SELECT * FROM `fondos` ");
    //ejecutamos la query
    $sentencia->execute();
    //guardamos los valores de la query
    $datos=$sentencia->fetchAll();

    $array = comprobarImages($datos);
    //los formateamos a Json
    
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $sentencia->rowCount(),
        "results"=> $array,
    ]);
});

Flight::route('GET /mods/traductions_and_mods', function () {
    
    //conectamos a la base de datos y preparamos la query
    $sentencia = Flight::dbAntigua()->prepare("SELECT * FROM `mods` WHERE activo = 1 AND tipo = 'Traduccion' OR tipo= 'Mod'");
    //ejecutamos la query
    $sentencia->execute();
    //guardamos los valores de la query
    $datos=$sentencia->fetchAll();

    $array = comprobar($datos);
    //los formateamos a Json
    
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $sentencia->rowCount(),
        "results"=> $array,
    ]);
});

Flight::route('GET /mods/options/duration', function () {
    
    //conectamos a la base de datos y preparamos la query
    $sentencia = Flight::db()->prepare("SELECT * FROM `duracion_mods`");
    //ejecutamos la query
    $sentencia->execute();
    //guardamos los valores de la query
    $datos=$sentencia->fetchAll();

    $array = comprobarDuracion($datos);
    //los formateamos a Json
    
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $sentencia->rowCount(),
        "results"=> $array,
    ]);
});

Flight::route('GET /mods/options/status', function () {
    
    //conectamos a la base de datos y preparamos la query
    $sentencia = Flight::db()->prepare("SELECT * FROM `estado_mods`");
    //ejecutamos la query
    $sentencia->execute();
    //guardamos los valores de la query
    $datos=$sentencia->fetchAll();

    $array = comprobarEstado($datos);
    //los formateamos a Json
    
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $sentencia->rowCount(),
        "results"=> $array,
    ]);
});

Flight::route('GET /mods/options/type', function () {
    
    //conectamos a la base de datos y preparamos la query
    $sentencia = Flight::db()->prepare("SELECT * FROM `tipo_mod`");
    //ejecutamos la query
    $sentencia->execute();
    //guardamos los valores de la query
    $datos=$sentencia->fetchAll();

    $array = comprobarTipo($datos);
    //los formateamos a Json
    
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $sentencia->rowCount(),
        "results"=> $array,
    ]);
});

Flight::route('GET /mods/options/all-sagas', function () {
    
    //conectamos a la base de datos y preparamos la query
    $sentencia = Flight::db()->prepare("SELECT * FROM `todas_las_sagas`");
    //ejecutamos la query
    $sentencia->execute();
    //guardamos los valores de la query
    $datos=$sentencia->fetchAll();

    $array = comprobarSaga($datos);
    //los formateamos a Json
    
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $sentencia->rowCount(),
        "results"=> $array,
    ]);
});

Flight::route('GET /mods/options/tipo-mod-sagas', function () {
    
    //conectamos a la base de datos y preparamos la query
    $sentencia = Flight::db()->prepare("SELECT * FROM `tipo_mod_saga`");
    //ejecutamos la query
    $sentencia->execute();
    //guardamos los valores de la query
    $datos=$sentencia->fetchAll();

    $array = comprobarTipo($datos);
    //los formateamos a Json
    
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $sentencia->rowCount(),
        "results"=> $array,
    ]);
});

Flight::route('GET /mods/options/focus-on', function () {
    
    //conectamos a la base de datos y preparamos la query
    $sentencia = Flight::db()->prepare("SELECT * FROM `enfoque_mods`");
    //ejecutamos la query
    $sentencia->execute();
    //guardamos los valores de la query
    $datos=$sentencia->fetchAll();

    $array = comprobarEnfoque($datos);
    //los formateamos a Json
    
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $sentencia->rowCount(),
        "results"=> $array,
    ]);
});

Flight::route('GET /mods/options/genere', function () {
    
    //conectamos a la base de datos y preparamos la query
    $sentencia = Flight::db()->prepare("SELECT * FROM `tipo_genero`");
    //ejecutamos la query
    $sentencia->execute();
    //guardamos los valores de la query
    $datos=$sentencia->fetchAll();

    $array = comprobarGenero($datos);
    //los formateamos a Json
    
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $sentencia->rowCount(),
        "results"=> $array,
    ]);
});

Flight::route('GET /songs', function () {
    
    //conectamos a la base de datos y preparamos la query
    $sentencia = Flight::dbAntigua()->prepare("SELECT * FROM `songs`");
    //ejecutamos la query
    $sentencia->execute();
    //guardamos los valores de la query
    $datos=$sentencia->fetchAll();

    $array = comprobarSong($datos);
    //los formateamos a Json
    
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $sentencia->rowCount(),
        "results"=> $array,
    ]);
});



Flight::route('GET /mods/name/@name', function ($name) {
    //$name = limpiar_cadena($name);
    if($name=="Doki Doki Literature Club"){
         $array[] = [
            "id"=> 0,
            "url_sitio"=>"https://www.dokidokispanish.club/Doki-Doki-Literature-Club",
            "nombre"=>"Doki Doki Literature Club",
            "descripcion"=>"¡Bienvenido al Club de Literatura! Siempre ha sido un sueño para mí hacer algo especial con las cosas que amo. ¡Ahora que eres miembro del club, puedes ayudarme a hacer realidad ese sueño en este lindo juego!\n\nTodos los días están llenos de charlas y actividades divertidas con todas mis adorables y únicas miembros del club:\n\nSayori, la joven rayito de sol que valora la felicidad;\nNatsuki, la chica engañosamente linda que tiene un carácter contundente;\nYuri, la tímida y misteriosa que solo encuentra consuelo en el mundo de los libros;\n... Y, por supuesto, ¡Monika, la líder del club! ¡Esa soy yo!\n\nEstoy muy emocionada de que hagas amigos con todos y ayudes al Club de Literatura a convertirse en un lugar más íntimo para todos mis miembros. Pero , ¿prometes pasar más tiempo conmigo? ",
            "url_logo"=>"https://www.dokidokispanish.club/assets/gui/window_icon.png",
            "url_img"=>"https://www.dokidokispanish.club/assets/ddlc/gui/ddlc_banner.png"
        ];
        $sentencia = 1;
        Flight::json([
        "response"=> "success",
        "total_rows" => $sentencia,
        "results" => $array,
    ]);
    }else{
    //conectamos a la base de datos y preparamos la query
    $sentencia = Flight::dbAntigua()->prepare("SELECT * FROM `mods` WHERE `nombre` LIKE '%$name%' ");
    
    //ejecutamos la query
    $sentencia->execute();
    //guardamos los valores de la query
    $datos=$sentencia->fetchAll();
    //los formateamos a Json
    $array = comprobar($datos);
    //los formateamos a Json
    
    //$headers = getToken();
    Flight::json([
        "response"=> "success",
        "total_rows" => $sentencia->rowCount(),
        "results" => $array,
    ]);
    }
});

