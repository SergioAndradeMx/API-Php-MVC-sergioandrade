<?php
//vista 
// requiere el archivo vistaapi
require_once "VistaApi.php";

/**
 * Clase para imprimir en la salida respuestas con formato JSON
 */
// crea una clase llamada vistajson, la clase vistajson hereda de la otra clases vistaapi
class VistaJson extends VistaApi{
    
    public function __construct($estado = 400){
        $this->estado = $estado;
    }

    /**
     * Imprime el cuerpo de la respuesta y el código de respuesta
     * @param mixed $cuerpo de la respuesta a enviar
     */
    public function imprimir($cuerpo): void
    {
        if ($this->estado) {
            //son los estatus, son los numero de erro, creacion, ok 
            http_response_code($this->estado);
        }
        header('Content-Type: application/json; charset=utf8');
        echo json_encode($cuerpo, JSON_PRETTY_PRINT);
        exit;
    }
}