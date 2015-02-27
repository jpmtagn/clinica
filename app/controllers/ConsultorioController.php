<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 2/26/2015
 * Time: 10:35 PM
 */

class ConsultorioController extends BaseController {

    const PAGE_LIMIT = 5;

    const MODEL = 'Consultorio';

    const LANG_FILE = 'consultorio';

    const TITLE_FIELD = 'nombre';

    /** Navegacion **/

    /**
     * Muestra la página de administración
     * @return mixed
     */
    public function paginaAdmin() {
        if (Auth::user()->admin) {
            //$model = self::MODEL;
            $areas = Functions::arrayIt(Area::get(), 'id', 'nombre');
            $total = $this->getTotalItems();
            return View::make('admin.consultorio')->with(
                array(
                    'active_menu' => 'area',
                    'total' => $total,
                    'areas' => $areas
                )
            );
        }
        return View::make('admin.inicio');
    }

    /**
     * This function will be called after the model validation has passed successfully
     * @param $inputs
     * @return boolean
     */
    public function afterValidation($inputs) {
        return true;
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

        //left panel
        //$output .= $frm->halfPanelOpen(true);
        $output .= $frm->view('nombre', Lang::get(self::LANG_FILE . '.name'), $item->nombre);
        $output .= $frm->view('descripcion', Lang::get(self::LANG_FILE . '.description'), $item->descripcion);
        $output .= $frm->view('consultorio', Lang::get('area.title'), $item->area->nombre);
        //$output .= $frm->view('total', Lang::get('global.total') . ' ' . Lang::get('consultorio.title_plural'), $item->consultorios->count());
        //$output .= $frm->halfPanelClose();

        //right panel
        //$output .= $frm->halfPanelOpen(false, 6, 'text-center');

        //$output .= $frm->halfPanelClose(true);

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
        return AForm::searchResults($records, 'nombre');
    }

}