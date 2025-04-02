<?php

namespace App\Helpers;

class Cast {

    public static function castParams(array $params, $castType = 'int' | 'float' | 'string' | 'bool', array $keysToCast = []) {
        // Si $keysToCast está vacío, se castea todos los elementos
        if (empty($keysToCast)) {
            $keysToCast = array_keys($params);
        }

        foreach ($keysToCast as $key) {
            if (isset($params[$key])) {
                switch ($castType) {
                case 'int':
                    // Castear a entero
                    $params[$key] = (int) $params[$key];
                    break;
                case 'float':
                    // Castear a float
                    $params[$key] = (float) $params[$key];
                    break;
                case 'string':
                    // Castear a string
                    $params[$key] = (string) $params[$key];
                    break;
                case 'bool':
                    // Castear a booleano
                    $params[$key] = (bool) $params[$key];
                    break;
                default:
                    // Si el tipo de casteo no es reconocido, no se hace nada
                    break;
                }
            }
        }
        return $params;
    }

}
