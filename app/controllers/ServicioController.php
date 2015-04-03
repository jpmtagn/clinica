<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 2/27/2015
 * Time: 09:54 AM
 */

class ServicioController extends BaseController {

    const PAGE_LIMIT = 5;

    const MODEL = 'Servicio';

    const LANG_FILE = 'servicio';

    const TITLE_FIELD = 'nombre';

    /** Navegacion **/

    /**
     * Muestra la página de administración
     * @return mixed
     */
    public function paginaAdmin() {
        if (Auth::user()->admin) {
            //$model = self::MODEL;
            $consultorios = Functions::arrayIt(Consultorio::get(), 'id', 'nombre', array('area', 'nombre'));
            $categorias = Functions::arrayIt(ServicioCategoria::get(), 'id', 'nombre');
            $duraciones = array(
                '0' => '',
                '10' => '10m',
                '20' => '20m',
                '30' => '30m',
                '40' => '40m',
                '50' => '50m',
                '60' => '1h',
                '70' => '',
                '80' => '1h 20m',
                '90' => '',
                '100' => '1h 40m',
                '110' => '',
                '120' => '2h',
                '130' => '',
                '140' => '2h 20m',
                '150' => '',
                '160' => '2h 40m',
                '170' => '',
                '180' => '3h'
            );
            $total = $this->getTotalItems();
            return View::make('admin.servicios')->with(
                array(
                    'active_menu' => 'servicio',
                    'total' => $total,
                    'consultorios' => $consultorios,
                    'duraciones' => $duraciones,
                    'categorias' => $categorias
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
        //CONSULTORIOS
        $items = isset($_POST['consultorios']) ? array_map('intval', Input::get('consultorios')) : false;
        if ($items) {
            $item->consultorios()->sync( $items );
        }
        return true; //needs to return true to output json
    }

    /**
     * Datos adicionales que se envian al solicitar la información del registro para editar
     * @param $item
     */
    public function additionalData($item) {
        $this->setReturn('consultorios', Functions::arrayIt($item->consultorios, 'id', 'nombre'));
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

        $consultorios = $item->consultorios->lists('nombre');

        //left panel
        //$output .= $frm->halfPanelOpen(true);
        $output .= $frm->view('nombre', Lang::get(self::LANG_FILE . '.name'), $item->nombre);
        if ($item->categoria) {
            $output .= $frm->view('categoria', Lang::get(self::LANG_FILE . '.category'), $item->categoria->nombre);
        }
        if (!empty($item->descripcion)) {
            $output .= $frm->view('descripcion', Lang::get(self::LANG_FILE . '.description'), $item->descripcion);
        }
        $output .= $frm->view('duracion', Lang::get(self::LANG_FILE . '.duration'), Functions::minToHours($item->duracion));
        $output .= $frm->view('consultorio', Lang::get('consultorio.title_' . Functions::singlePlural('single', 'plural', count($consultorios))), implode(', ', $consultorios));
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