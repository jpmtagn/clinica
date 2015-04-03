<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 02/04/15
 * Time: 07:28 PM
 */

class ServicioCategoriaController extends BaseController {

    const PAGE_LIMIT = 5;

    const MODEL = 'ServicioCategoria';

    const LANG_FILE = 'servicio';

    const TITLE_FIELD = 'nombre';

    /** Navegacion **/

    /**
     * Muestra la página de administración de Parentescos
     * @return mixed
     */
    public function paginaAdmin() {
        if (Auth::user()->admin) {
            $total = $this->getTotalItems();
            return View::make('admin.servicio_categoria')->with(
                array(
                    'active_menu' => 'servicio',
                    'total' => $total
                )
            );
        }
        return View::make('admin.inicio');
    }

    /**
     * Proceso adicional al editar / crear un nuevo registro
     * @param $item
     * @return bool
     */
    public function editarRelational($item) {

        return true; //needs to return true to output json
    }

    /**
     * Datos adicionales que se envian al solicitar la información del registro para editar
     * @param $item
     */
    public function additionalData($item) {

    }

    /**
     * Código HTML que se envía al solicitar la información del registro para visualizar
     * @param $item
     * @return string
     */
    public function outputInf( $item ) {
        $frm = new AForm;
        $output = "";
        $output .= $frm->id( $item->id );
        $output .= $frm->hidden('action');
        $output .= $frm->view('nombre', Lang::get(self::LANG_FILE . '.category'), $item->nombre);
        $output .= $frm->controlButtons();

        return $output;
    }

    /**
     * Código HTML que se envía al realizar una búsqueda
     * @param $records
     * @param $search_fields
     * @return string
     */
    public function buscarReturnHtml($records, $search_fields) {
        return AForm::searchResults($records, reset($search_fields));
    }

}