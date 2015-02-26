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
        //$output .= $frm->halfPanelClose();
        
        //right panel
        //$output .= $frm->halfPanelOpen(false, 6, 'text-center');
            
        //$output .= $frm->halfPanelClose(true);

        $output .= $frm->controlButtons();


        return $output;
    }

    public function buscarGetAlt() {
        $validator = Validator::make(Input::all(),
            array(
                'search_query'          => '',
                /*'search_page'           => 'required|integer|min:1',*/
                'buscar_doctor_id'      => 'integer|min:1',
                'buscar_paciente_id'    => 'integer|min:1'
            )
        );
        if ($validator->passes()) {
            $query  = Input::get('search_query');
            $page   = Input::get('search_page');

            //1. searches by text input
                /*$search_fields = array('fecha', 'nombre_paciente', 'apellido_paciente', 'cedula_paciente', 'nombre_doctor', 'apellido_doctor', 'cedula_doctor');
                $match_total = 0;

                $records = $this->buscarTabla('citas', $query, $page, $search_fields, $match_total );
                $total = count($records);*/

            //2. searches by rows
                $doctor_id = (int)Input::get('buscar_doctor_id');
                $paciente_id = (int)Input::get('buscar_paciente_id');

                $model = self::MODEL;

                $records = new $model;

                //if the date is specified
                if (strlen($query) > 0) {
                    $records = $records::where('fecha', '=', $query);
                }
                //if the doctor is specified
                if ($doctor_id > 0) {
                    $records = $records->where('doctor_id', '=', $doctor_id);
                }
                //if the patient is specified
                if ($paciente_id > 0) {
                    $records = $records->where('paciente_id', '=', $paciente_id);
                }
                $records = $records->get();
                
                $total = count($records);
                $match_total = $total;


            $this->setReturn('total', $match_total);
            $this->setReturn('total_page', $total);
            $this->setReturn('results', $this->buscarReturnHtml($records, array()));
            return $this->returnJson();
        }
        return $this->setError( Lang::get('global.wrong_action') );
    }

    /**
     * Código HTML que se envía al realizar una búsqueda
     * @param $records
     * @param $search_fields
     * @return string
     */
    public function buscarReturnHtml($records, $search_fields) {
        //return AForm::searchResults($records, 'hora_inicio', array(array(Lang::get(self::LANG_FILE . '.patient'),'nombre_paciente'), array(Lang::get(self::LANG_FILE . '.doctor'),'nombre_doctor')));

        $output = "";
        if (count($records)) {
            foreach ($records as $record) {
                //1. searches by text input
                /*$row = Functions::longDateFormat($record->fecha);
                if (!empty($record->hora_inicio)) {
                    $row .= AForm::badge(Functions::justTime($record->hora_inicio));
                }
                $row = '<h4><i class="fa fa-clock-o"></i> ' . $row . '</h4>';
                $id = $record->id;
                $row .= '<br><b>' . Lang::get(self::LANG_FILE . '.patient') . '</b>: ' .  Functions::firstNameLastName($record->nombre_paciente, $record->apellido_paciente);
                $row .= '<br><b>' . Lang::get(self::LANG_FILE . '.doctor') . '</b>: ' .  Functions::firstNameLastName($record->nombre_doctor, $record->apellido_doctor);
                $output.= <<<EOT
                    <a class="list-group-item search-result" data-id="{$id}">{$row}</a>
EOT;*/
                //2. searches by rows
                $row = Functions::longDateFormat($record->fecha);
                if (!empty($record->hora_fin)) {
                    $row .= AForm::badge(Functions::justTime($record->hora_inicio));
                }
                $row = '<h4><i class="fa fa-clock-o"></i> ' . $row . '</h4>';
                $id = $record->id;
                $patient = Paciente::find($record->paciente_id);
                $doctor = User::find($record->doctor_id)->paciente;
                $row .= '<br><b>' . Lang::get(self::LANG_FILE . '.patient') . '</b>: ' .  Functions::firstNameLastName($patient->nombre, $patient->apellido);
                $row .= '<br><b>' . Lang::get(self::LANG_FILE . '.doctor') . '</b>: ' .  Functions::firstNameLastName($doctor->nombre, $doctor->apellido);
                $output.= <<<EOT
                    <a class="list-group-item search-result" data-id="{$id}">{$row}</a>
EOT;
            }
        }

        return $output;
    }

}