<?php
//aqui especifico las rutas de los archivos que voy a ocupar
// Importamos la base de datos.
require_once 'BD/ConexionBD.php';
// llama a la vista exceptionapi
require_once 'View/ExceptionApi.php';
// llama a la vista, vistajson
require_once 'View/VistaJson.php';
// llama al controladorusuarios
require_once 'Controller/controllerUsuarios.php';
// ocupo el archivo usuarios
require_once 'Models/Usuario.php';

// se esta creando un objecto vistajson de la funcion vistajson que se encuetra en vistajson
$vista = new VistaJson();
//este mensaje te lo muestra para el error de rutas que no son validas y te muestra el mensaje
const ESTADO_RUTA_NO_VALIDA = 403;
//este error te lo muestra cuando escribes mal el url
const METHOD_NOT_ALLOWED = 405;
// Las rutas permitidas.
$rutas = [
    //'nombre' => 'controllernombre',+
    'pepe' => 'controllerpepe',
    'actores' => 'controllerActores',
    'generos' => 'controllerGeneros',
    'peliculas' => 'controllerPeliculas',
    'usuarios' => 'controllerUsuarios'
];
// Si hay una excepción esto la obtiene
//si hay un error se ejecuta esta exception, usara el archivo de vistajson.php
set_exception_handler(function ($exception) use ($vista) {
    //el formato que va a mosrar para el error
    $cuerpo = array(
        "estado" => $exception->estado,
        "mensaje" => $exception->getMessage()
    );
    //si no tiene datos da estado 500
    if (!$exception->estado) {
        $vista->estado = 500;

    } else {
        $vista->estado = $exception->estado;
    }
    // imprime el vista
    $vista->imprimir($cuerpo);
}
);



// Obtengo el modelo, con una variable super global
// donde get esta definiendo model y si no es indifinido o nulo, manda a llamar peliculas
$Model = $_GET['model'] ?? 'peliculas';
//var_dump($Model);
//exit;
// Valido que si no esta entonces manda un error
//si no en model rutas esta mal escrito en el url te muestra en mensaje
if (!array_key_exists($Model, $rutas)) {
    // te muestra el estado 403
    throw new ExcepcionApi(ESTADO_RUTA_NO_VALIDA, "No se reconoce el recurso al que intentas acceder: " . $Model);
} else {
    // Importamos la clase dependiendo de cual se cumpla en la ruta
    //aqui dice que ocupa los controladores.
    require_once 'Controller/' . $rutas[$Model] . '.php';
    //var_dump($rutas[$Model]);
    //exit;
    // Creación de un objeto el  cual me ayudara a llamar al objetrocontroller.
    $objetoController = new $rutas[$Model];
    // Obtener el método.
    //$_SERVER['REQUEST_METHOD']: Esto obtiene el método HTTP utilizado para acceder a la página (GET, POST, PUT, DELETE).
//var_dump(strtolower($_SERVER['REQUEST_METHOD']));
    //echo "<br/>";
    //var_dump($_SERVER["REMOTE_PORT"]);
    //exit;
    //strtolower: Convierte el método HTTP a minúsculas para facilitar la comparación posterior.
    $metodo = strtolower($_SERVER['REQUEST_METHOD']);
    //$_GET['id'] ?? null; Usa el operador de fusión de null para asignar el valor de $_GET['id'] a $id si existe, o null si no.
   //aqui get id sino le asigno nulo esto se ve en la htaccess en la reglas
    $id = $_GET['id'] ?? null;
     //if (empty($id)) $id = null; Garantiza que $id sea null si está vacío.
     //empty pregunta si es cadena vacia 
    if (empty ($id)) $id = null;
    //la variable respuesta se usara mas adelante para almacenar la respuesta del controlador
    //respues almacena lo que me regresa la base de datos
    $respuesta = "";
    // Retorno mi respuesta en un array Asociativo.
    $arrayDevolver = [
        'estado' => '',
        'cuerpo' => '',
    ];
    $id2 = $_GET['id2'] ?? null;
    if (empty ($id2)) $id2 = null;
    // ########################### REVISAR ####################################################
    //cabecera
    //Aquí se utiliza el operador de fusión de null (??) para asignar el valor de $_GET['pdf'] a la variable $pdf si existe, o null si no.
    $pdf = $_GET['pdf'] ?? null;
    //Esta línea verifica si $pdf está vacío lo que incluye null, false, una cadena vacía
     //y lo reasigna a null si es así. En este caso, es redundante porque $pdf ya se inicializa a null si $_GET['pdf'] no existe.
    if (empty ($pdf)) $pdf = null;
    //esta línea comprueba si $pdf no es null. Si $pdf tiene algún valor, el bloque de código dentro del if se ejecuta.
    if (!is_null($pdf)) {
        //Aquí se verifica si el método HTTP utilizado es GET. 
        if ($metodo === 'get'){
            //Esta línea llama a un método estático autenticar de la clase Usuario. 
            //Este método  verifica si el usuario está autenticado y tiene los permisos necesarios.
            Usuario::autenticar();
            // Este método entrega un archivo pdf.
            //->esto llama a una funcio (pdf)
            $objetoController->pdf();
        }else{
            //Si el método HTTP no es GET, se lanza una excepción ExcepcionApi indicando que el método no está permitido. 
            //METHOD_NOT_ALLOWED probablemente es una constante definida en alguna parte del código que representa el código de error correspondiente.
            throw new ExcepcionApi(METHOD_NOT_ALLOWED, "URL no valida: ");
        }
      
        //asta aqui explicar el metodo pdf
        //hacer el metodo pdf para el controladorpepe
    } else {
        
        // #######################################################
        switch ($metodo) {
            case 'get':
                // Valido que me haya enviado el token
                Usuario::autenticar();
                // Mando a llamar al método index para que haga lo siguiente.
                //index se va a la funcion de los controladres 
                $respuesta = $objetoController->index($id,$id2);
                // muestra el estado 200
                $vista->estado = 200;
                break;
            case 'post':
                $respuesta = $objetoController->store();
                // Mandamos que se ha creado correctamente.
                $vista->estado = 201;
                break;
            case 'put':
                // Valido que me haya enviado el token
                Usuario::autenticar();
                // Mando a llamar al método edit para modificar dependiendo.
                $respuesta = $objetoController->edit($id);
                $vista->estado = 200;
                break;
            case 'delete':
                // Valido que me haya enviado el token
                Usuario::autenticar();
                // Mando a llamar al método delete para eliminar dependiendo.
                $respuesta = $objetoController->delete($id);
                $vista->estado = 200;
                break;
            default:
                // esta creando un opjecto del archivo exceptionapi para decir url no valida.
                throw new ExcepcionApi(METHOD_NOT_ALLOWED, "URL no valida: " . $rutas[$Model]);
                break;
        }
        // Si todo salio bien le envió todo ok
        $arrayDevolver['estado'] = $vista->estado;
        // Le asigno la respuesta al arreglo asociativo
        $arrayDevolver['cuerpo'] = $respuesta;
        // Imprimir mi respuesta.
        $vista->imprimir($arrayDevolver);
    }

}

