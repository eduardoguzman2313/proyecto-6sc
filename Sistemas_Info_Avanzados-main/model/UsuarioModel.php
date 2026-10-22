<?php
// ===========================
// UsuarioModel.php — MODEL (POO)
// Programación 4: Aplicaciones Web — UAE
// ===========================
// El "guardia" del sistema. Ahora también sabe editar datos de
// un usuario existente y activar/desactivar su cuenta.

require_once "Conexion.php";

class UsuarioModel {

    private $pdo;

    public function __construct() {
        $conexion  = new Conexion();
        $this->pdo = $conexion->getConexion();
    }

    // ── Registrar un usuario nuevo (lo hace el Admin desde Usuarios) ──
    // $permisos llega como un arreglo (ej: ['compras', 'kardex']) y aquí
    // lo convertimos a texto separado por comas para guardarlo en la BD.
    public function registrar($cedula, $nombre, $direccion, $email, $telefono, $passwordPlano, $rol, $esAdmin, $permisos) {

        $passwordEncriptada = password_hash($passwordPlano, PASSWORD_DEFAULT);
        $permisosTexto       = implode(",", $permisos);

        $sql  = "INSERT INTO usuarios (cedula, nombre, direccion, email, telefono, password, rol, es_admin, permisos, estado)
                 VALUES (:cedula, :nombre, :direccion, :email, :telefono, :password, :rol, :es_admin, :permisos, 'activo')";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":cedula"    => $cedula,
            ":nombre"    => $nombre,
            ":direccion" => $direccion,
            ":email"     => $email,
            ":telefono"  => $telefono,
            ":password"  => $passwordEncriptada,
            ":rol"       => $rol,
            ":es_admin"  => $esAdmin ? 1 : 0,
            ":permisos"  => $permisosTexto
        ]);

        return true;
    }

    // ── Buscar un usuario por su email (para login) ──
    public function obtenerPorEmail($email) {
        $sql  = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":email" => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ── Buscar un usuario por su ID (para precargar el formulario de editar) ──
    public function obtenerPorId($id) {
        $sql  = "SELECT id, cedula, nombre, direccion, email, telefono, rol, es_admin, permisos, estado, fecha_registro
                 FROM usuarios WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ── Verificar credenciales al iniciar sesión ──
    public function verificarCredenciales($email, $passwordPlano) {
        $usuario = $this->obtenerPorEmail($email);

        if (!$usuario) {
            return false;
        }

        if (password_verify($passwordPlano, $usuario["password"])) {
            return $usuario;
        }

        return false;
    }

    // ── Cambiar la contraseña del PROPIO usuario (con sesión iniciada) ──
    // Exige la contraseña actual: así, aunque alguien encuentre una sesión
    // abierta, no puede cambiar la contraseña sin conocer la anterior.
    // Devuelve: "ok" | "actual_incorrecta" | "no_existe"
    public function cambiarPasswordPropia($idUsuario, $passwordActualPlano, $passwordNuevaPlano) {
        // obtenerPorId() no trae la contraseña (a propósito), así que la consultamos aquí
        $stmt = $this->pdo->prepare("SELECT password FROM usuarios WHERE id = :id");
        $stmt->execute([":id" => $idUsuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$usuario) {
            return "no_existe";
        }
        if (!password_verify($passwordActualPlano, $usuario["password"])) {
            return "actual_incorrecta";
        }

        $passwordEncriptada = password_hash($passwordNuevaPlano, PASSWORD_DEFAULT);

        $sql  = "UPDATE usuarios SET password = :password WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":password" => $passwordEncriptada,
            ":id"       => $idUsuario
        ]);

        return "ok";
    }

    // ── Editar los datos de un usuario (solo el Admin) ──
    // No se cambia la contraseña aquí — cada usuario la cambia desde "Cambiar contraseña"
    public function actualizar($id, $cedula, $nombre, $direccion, $email, $telefono, $rol, $esAdmin, $permisos, $estado) {
        $permisosTexto = implode(",", $permisos);

        $sql = "UPDATE usuarios
                SET cedula = :cedula, nombre = :nombre, direccion = :direccion,
                    email = :email, telefono = :telefono, rol = :rol,
                    es_admin = :es_admin, permisos = :permisos, estado = :estado
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":cedula"    => $cedula,
            ":nombre"    => $nombre,
            ":direccion" => $direccion,
            ":email"     => $email,
            ":telefono"  => $telefono,
            ":rol"       => $rol,
            ":es_admin"  => $esAdmin ? 1 : 0,
            ":permisos"  => $permisosTexto,
            ":estado"    => $estado,
            ":id"        => $id
        ]);

        return true;
    }

    // ── Traer todos los usuarios registrados (para el Admin) ──
    // Nunca retornamos la columna password.
    public function obtenerTodos() {
        $sql = "SELECT id, cedula, nombre, direccion, email, telefono, rol, es_admin, permisos, estado, fecha_registro
                FROM usuarios
                ORDER BY fecha_registro DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
