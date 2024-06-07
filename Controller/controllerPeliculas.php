<?php
// son los archivo que ocupamos
require_once 'Models/peliculas.php';
require_once 'View/ExceptionApi.php';
//creo la clase controllerPeliculas
class controllerPeliculas
{

    /**
     * TODO: Método que trae una película por su id o todas las películas.
     * @throws ExcepcionApi
     */
    public function index($id,$id2): array
    {
        if (!is_null($id2)){
            return Peliculas::getid2($id,$id2);
        }
        // Si el id no es null entones retornara un dato. 
        else if (!is_null($id)) {
            // Retorno el resultado.
            return Peliculas::getOne($id);
            // De lo contrario retornara todos.
        } else {
            // Retorno todos los datos.
            return Peliculas::getAll();
        }
    }

    /**
     * TODO: Método que me va a servir para insertar películas.
     * @return string
     * @throws ExcepcionApi
     */
    //posh es para incertar datos
    public function store(): string{
        //body que me muestras
        $cuerpo = file_get_contents('php://input');
        $params = json_decode($cuerpo);
        //esta mandando a llamar los datos de pelicula 
        return Peliculas::insertOne($params);
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
            //solo me trae un id especifico que se encuentra en la tabla
            return Peliculas::update($params, $id);
        }else{
            // me dice que ocupo un id 
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
        //checa si id tiene un valo
        if (!is_null($id)){
            //elimina un id
            return Peliculas::destroy($id);
        }else{
        //muestra el mensaje id
            throw new ExcepcionApi(400, "Se requiere el id");
        }
    }

    /**
     * @throws ExcepcionApi
     */
    //esta es la funcion pdf 
    public function pdf(): void
    {
        // Creo la instancia
        $pdf = new PDF();
        //este es el titulo que muestra en el pdf
        $pdf->titulo("Lista de peliculas");
        // Creo la pagina.
        $pdf->AddPage();
        //manda a  llamar la clase pdf de la funsion pdf
        $data = Peliculas::pdf();
        //el tipo de letra y el tamaño
        $pdf->SetFont('Arial', '', 14);
        //son las posiciones 
        $pdf->SetWidths(array(10, 50, 60, 30,30));
        //esta centrando
        $pdf->SetAligns(array("C", "C", "C", "C", "C"));
        //son los datos que van en la tabla
        $pdf->Row(array ("No", 'Titulo', 'Director',utf8_decode("Año estreno"),"Genero"));
        //contador 1
        $contador = 1;
        //centra
        $pdf->SetAligns(array("C", "C", "C", "C","C"));
        // Inicia un bucle foreach para iterar sobre cada elemento en el array $data. Cada elemento se asigna a la variable $row en cada iteración.
        foreach ($data as $row) {
            //me agrega esto datos a la tabla pdf
            $pdf->Row(array($contador++, utf8_decode($row['titulo']), utf8_decode($row['director']),$row['anio_estreno'], utf8_decode($row['nombre'])));
        }
        // Muestro el PDF.
        $pdf->Output();

    }

}