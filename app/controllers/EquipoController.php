<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 2/28/2015
 * Time: 12:06 PM
 */

class EquipoController extends BaseController {

    const PAGE_LIMIT = 5;

    const MODEL = 'Equipo';

    const LANG_FILE = 'equipo';

    const TITLE_FIELD = 'nombre';

    /** Navegacion **/

    /**
     * Muestra la página de administración
     * @return mixed
     */
    public function paginaAdmin() {
        if (Auth::user()->admin) {
            //$model = self::MODEL;
            $servicios = Functions::arrayIt(Servicio::get(), 'id', 'nombre');
            $total = $this->getTotalItems();
            return View::make('admin.equipos')->with(
                array(
                    'active_menu' => 'equipo',
                    'total' => $total,
                    'servicios' => $servicios,
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
        //SERVICIOS
        $items = isset($_POST['servicios']) ? array_map('intval', Input::get('servicios')) : false;
        if ($items) {
            $item->servicios()->sync( $items );
        }
        return true; //needs to return true to output json
    }

    /**
     * Datos adicionales que se envian al solicitar la información del registro para editar
     * @param $item
     */
    public function additionalData($item) {
        $this->setReturn('servicios', Functions::arrayIt($item->servicios, 'id', 'nombre'));
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

        $servicios = $item->servicios->lists('nombre');

        //left panel
        //$output .= $frm->halfPanelOpen(true);
        $output .= $frm->view('nombre', Lang::get(self::LANG_FILE . '.name'), $item->nombre);
        if (!empty($item->descripcion)) {
            $output .= $frm->view('descripcion', Lang::get(self::LANG_FILE . '.description'), $item->descripcion);
        }
        if (!empty($item->serial)) {
            $output .= $frm->view('serial', Lang::get(self::LANG_FILE . '.serial'), $item->serial);
        }
        //$output .= $frm->view('cantidad', Lang::get(self::LANG_FILE . '.quantity'), $item->cantidad);
        $output .= $frm->view('inamovible', Lang::get(self::LANG_FILE . '.immovable'), ucfirst(Lang::get('global.' . ($item->inamovible ? 'yes' : 'no'))));
        $output .= $frm->view('servicio', Lang::get('servicio.title_' . Functions::singlePlural('single', 'plural', count($servicios))), implode(', ', $servicios));
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