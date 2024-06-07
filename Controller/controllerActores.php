<?php
require_once 'Models/Actor.php';
require_once 'View/ExceptionApi.php';
require_once 'Utilidad/PDF.php';
class controllerActores
{
    /**
     * TODO: Método que nos va a servir para obtener todos los actores o un actor por su ID.
     * @throws ExcepcionApi
     */
    public function index($id,$id2): array
    {
        if (!is_null($id2)){
            return Actor::getid2($id,$id2);
        }
        // Si el id no es null entones retornara un dato. 
        else if (!is_null($id)) {
            // Retorno el resultado.
            return Actor::getOne($id);
            // De lo contrario retornara todos.
        } else {
            // Retorno todos los datos.
            return Actor::getAll();
        }
    }

    /**
     * TODO: Método para crear un nuevo actor.
     * @throws ExcepcionApi
     */
    public function store()
    {
        $cuerpo = file_get_contents('php://input');
        $params = json_decode($cuerpo);
        return Actor::insertOne($params);
    }

    /**
     * TODO: Método para actualizar un valor.
     * @throws ExcepcionApi
     */
    public function edit($id)
    {
        // Si el id no es null entonces mandar actualizar un valor.
        if (!is_null($id)){
            $cuerpo = file_get_contents('php://input');
            $params = json_decode($cuerpo);
            return Actor::update($params, $id);
        }else{
            throw new ExcepcionApi(400, "Se requiere el id");
        }
    }

    /**
     * TODO: Método para eliminar un actor.
     * @throws ExcepcionApi
     */
    public function delete($id): string
    {
        if (!is_null($id)){
            return Actor::destroy($id);
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
        $pdf->titulo("Lista de actores");
        // Creo la pagina.
        $pdf->AddPage();
        $data = Actor::pdf();
        $pdf->SetFont('Arial', '', 14);
        $pdf->SetWidths(array(10, 50, 50, 20, 50));
        $pdf->SetAligns(array("C", "C", "C", "C", "C"));
        // Le asigno la cabeceras
        $pdf->Row(array("No", 'Nombre', 'Nacionalidad',"Edad" ,"Pelicula"));
        // Contador para que me diga numero 1 tal y asi.
        $contador = 1;
        $pdf->SetAligns(array("C", "L", "L", "C", "L"));
        foreach ($data as $row) {
            $pdf->Row(array($contador++, $row['nombre'], $row['nacionalidad'],$row['edad'] , $row['titulo']));
        }
        // Muestro el PDF.
        $pdf->Output();
    }
}