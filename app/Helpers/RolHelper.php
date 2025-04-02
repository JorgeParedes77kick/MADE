<?php
namespace App\Helpers;
class RolHelper {

    public static $ADMIN = 1;
    public static $COORDINADOR = 2;
    public static $MONITOR = 3;
    public static $LIDER = 4;
    public static $ALUMNO = 5;

    public static function isValidRol(Array $validRoles) {
        $rol_id = session('rol_id');
        return in_array($rol_id, $validRoles);
    }

}
