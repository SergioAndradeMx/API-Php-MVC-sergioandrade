<?php
// son las conexion de los archivos
require_once 'Models/pepe.php';
require_once 'View/ExceptionApi.php';
// es la clase 
class controllerpepe
{

    /**
     * TODO: Método que trae una película por su id o todas las películas.
     * @throws ExcepcionApi
     */
    public function index($id){
        // Si el id no es null entones retornara un dato.
        if (!is_null($id)){
            // Retorno el resultado.
            return pepe::getOne($id);
            // De lo contrario retornara todos.
        }else{
            // Retorno todos los datos.
            return pepe::getAll();
        }
    }

    /**
     * TODO: Método que me va a servir para insertar películas.
     * @return string
     * @throws ExcepcionApi
     */
    //esta haciendo un post en la function store que esta en index.php
    public function store(): string{
        $cuerpo = file_get_contents('php://input');
        //lo convierte en "array y arreglo" lo de cuerpo, y lo guarda en params 
        $params = json_decode($cuerpo);
        // esta llamando un metodo de la clase pepe, que esta en modelos pepe.php
        return pepe::insertOne($params);
    }

    /**
     * TODO: Método para modificar una película.
     * @param $id
     * @return string
     * @throws ExcepcionApi
     */
    public function edit($id): string{
        if (!is_null($id)){
            $cuerpo = file_get_contents('php://input');
            $params = json_decode($cuerpo);
            return pepe::update($params, $id);
        }else{
            throw new ExcepcionApi(400, "Se requiere el id");
        }
    }

    /**
     * TODO: Método para eliminar una película.
     * @param $id
     * @return string
     * @throws ExcepcionApi
     */
    public function delete($id): string{
        if (!is_null($id)){
            return pepe::destroy($id);
        }else{
            throw new ExcepcionApi(400, "Se requiere el id");
        }
    }

    /**
     * @throws ExcepcionApi
     */
    public function pdf(): void
    {
        // Creo la instancia
        $pdf = new PDF();
        $pdf->titulo("Lista de pepe");
        // Creo la pagina.
        $pdf->AddPage();
        $data = pepe::pdf();
        $pdf->SetFont('Arial', '', 14);
        $pdf->SetWidths(array(10, 50, 60, 30,30));
        $pdf->SetAligns(array("C", "C", "C", "C", "C"));
        $pdf->Row(array ("No", 'nombre', 'pelicula_id'));
        $contador = 1;
        $pdf->SetAligns(array("C", "C", "C", "C","C"));
        foreach ($data as $row) {
            $pdf->Row(array($contador++, utf8_decode($row['nombre']), utf8_decode($row['pelicula_id'])));
        }
        // Muestro el PDF.
        $pdf->Output();

    }
}