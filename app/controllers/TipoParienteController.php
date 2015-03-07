<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 29/12/14
 * Time: 01:13 PM
 */

class TipoParienteController extends BaseController {

    const PAGE_LIMIT = 5;

    const MODEL = 'TipoPariente';

    const LANG_FILE = 'pacientes';

    const TITLE_FIELD = 'parentesco_m';

    /** Navegacion **/

    /**
     * Muestra la página de administración de Parentescos
     * @return mixed
     */
    public function paginaAdmin() {
        if (Auth::user()->admin) {
            $model = self::MODEL;
            $tipos = $model::get();//->toArray();
            $total = $this->getTotalItems();
            return View::make('admin.parentescos')->with(
                array(
                    'active_menu' => 'pacientes',
                    'total' => $total,
                    'tipos' => $tipos
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
        if ($item->reciproco != $item->id) {
            $model = self::MODEL;
            $reciprocal = $model::find($item->reciproco);
            if ($reciprocal) {
                if ((int)$reciprocal->reciproco == 0) {
                    $reciprocal->reciproco = $item->id;
                    $reciprocal->save();
                }
            }
        }
        return true; //needs to return true to output json
    }

    /**
     * Datos adicionales que se envian al solicitar la información del registro para editar
     * @param $item
     */
    public function additionalData($item) {
        $reciproco_lbl = '';
        if ($item->reciproco > 0) {
            if ($item->reciproco == $item->id) {
                $reciproco_lbl = $item->parentesco_m . ' / ' . $item->parentesco_f;
            }
            else {
                $model = self::MODEL;
                $reciproco = $model::find($item->reciproco);
                if ($reciproco) {
                    $reciproco_lbl = $reciproco->parentesco_m . ' / ' . $reciproco->parentesco_f;
                }
                else {
                    //if reciprocal is not found, then it was deleted, so I set it to 0 and save it
                    $item->reciproco = 0;
                    $item->save();
                    $this->setReturn('reciproco', 0);
                }
            }
        }
        $this->setReturn('reciproco_lbl', $reciproco_lbl);
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
        $output .= $frm->view('relationship', Lang::get(self::LANG_FILE . '.relationship'), $item->parentesco_m . ' / ' . $item->parentesco_f);
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

    public function listSeekAlt() {
        return $this->listSeek(array('parentesco_m', 'parentesco_f'), ' / ');
    }

}