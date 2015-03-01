<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 26/02/15
 * Time: 03:50 PM
 */

class AreaController extends BaseController {

    const PAGE_LIMIT = 5;

    const MODEL = 'Area';

    const LANG_FILE = 'area';

    const TITLE_FIELD = 'nombre';

    /** Navegacion **/

    /**
     * Muestra la página de administración
     * @return mixed
     */
    public function paginaAdmin() {
        if (Auth::user()->admin) {
            //$model = self::MODEL;
            $total = $this->getTotalItems();
            return View::make('admin.area')->with(
                array(
                    'active_menu' => 'area',
                    'total' => $total
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
            if (!empty($item->descripcion)) {
                $output .= $frm->view('descripcion', Lang::get(self::LANG_FILE . '.description'), $item->descripcion);
            }
            $output .= $frm->view('total', Lang::get('global.total') . ' ' . Lang::get('consultorio.title_plural'), $item->consultorios->count());
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