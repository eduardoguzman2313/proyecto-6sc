<?php
// ===========================
// permisos.php — MODEL (helper de seguridad)
// Programación 4: Aplicaciones Web — UAE
// ===========================
// $_SESSION['es_admin']  -> true/false. El Admin SIEMPRE ve todo,
//                           sin importar la lista de permisos.
// $_SESSION['permisos']  -> arreglo con los módulos permitidos,
//                           ej: ['compras', 'kardex']

// ── Función central: ¿puede este usuario ver este módulo? ──
// Se usa en TODOS los archivos protegidos, en vez de repetir
// la misma pregunta de "es admin" en cada uno.
function tienePermiso($modulo) {

    // El Admin tiene la llave maestra — no necesita permiso explícito
    if (!empty($_SESSION["es_admin"])) {
        return true;
    }

    // Para cualquier otro usuario, revisamos su lista de permisos
    $permisos = $_SESSION["permisos"] ?? [];
    return in_array($modulo, $permisos);
}
?>
