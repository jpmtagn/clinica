<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 25/01/15
 * Time: 04:25 PM
 */

class CitaController extends BaseController {

    const PAGE_LIMIT = 5;

    const MODEL = 'Cita';

    const LANG_FILE = 'citas';

    const TITLE_FIELD = 'fecha';

    /** Navegacion **/

    /**
     * Muestra la página de administración
     * @return mixed
     */
    public function paginaAdmin() {
        if (Auth::user()->admin) {
            //$model = self::MODEL;
            $total = $this->getTotalItems();
            return View::make('admin.citas')->with(
                array(
                    'active_menu' => 'citas',
                    'total' => $total
                )
            );
        }
        return View::make('admin.inicio');
    }

    /**
     * Muestra la página del Calendario
     * @return mixed
     */
    public function paginaCalendario() {
        if (Auth::user()->admin || User::is('doctor')) {
            //$total = $this->getTotalItems();
            $model = self::MODEL;
            $events = $model::with('paciente')->latestOnes()->get();
            return View::make('admin.calendario')->with(
                array(
                    'active_menu' => 'citas',
                    'events' => $events
                    //'total' => $total
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
        $start = $inputs['hora_inicio'];
        $end = $inputs['hora_fin'];
        if (!empty($start) && !empty($end)) {
            //check that the End Time is greater than the Start Time
            if (strtotime($start) > strtotime($end)) {
                $this->setError(Lang::get(self::LANG_FILE . '.time_mismatch'));
                return false;
            }
            //check that the specified time is not busy for the given doctor
            $start = Functions::justTime($start, false);
            $end = Functions::justTime($end, false);
            $model = self::MODEL;
            $model::where(function ($sql_query) use ($start, $end) {
                //TODO: fix this, figure out the conditions for checking overlaping times...
                $sql_query->where('hora_inicio', '>', $start);
                $sql_query->Where('hora_inicio', '<', $start);
            });
        }
        return true;
    }

    /**
     * Proceso adicional al editar / crear un nuevo registro
     * @param $item
     */
    public function editarRelational($item) {
        $paciente = $item->paciente;
        $this->setReturn('titulo', Functions::firstNameLastName($paciente->nombre, $paciente->apellido));
        $this->setReturn('inicio', Functions::explodeDateTime($item->hora_inicio));
        $this->setReturn('fin', empty($item->hora_fin) ? '' : Functions::explodeDateTime($item->hora_fin));
        $this->setReturn('dia_completo', empty($item->hora_fin) ? '1' : '0');
        $this->setReturn('estado', isset($item->estado) ? $item->estado : '1');
        return true; //needs to return true to output json
    }

    /**
     * Datos adicionales que se envian al solicitar la información del registro para editar
     * @param $item
     */
    public function additionalData($item) {
        $doctor = $item->doctor->paciente;
        $paciente = $item->paciente;
        $this->setReturn('doctor_id_lbl', Functions::firstNameLastName($doctor->nombre, $doctor->apellido));
        $this->setReturn('paciente_id_lbl', Functions::firstNameLastName($paciente->nombre, $paciente->apellido));
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

        $doctor = $item->doctor->paciente;
        $patient = $item->paciente;

        //left panel
        $output .= $frm->halfPanelOpen(true);
            $output .= $frm->view('fecha', Lang::get(self::LANG_FILE . '.date'), '<i class="fa fa-calendar-o"></i>&nbsp; ' . Functions::longDateFormat($item->fecha));
            if (!empty($item->hora_fin)) {
                $output .= $frm->view('start_time', Lang::get(self::LANG_FILE . '.time_start'), '<i class="fa fa-clock-o"></i>&nbsp; ' . Functions::justTime($item->hora_inicio) . ' - ' . Functions::justTime($item->hora_fin));
            }
            $output .= $frm->view('patient', Lang::get(self::LANG_FILE . '.patient'), '<i class="fa fa-wheelchair"></i>&nbsp; ' . strtoupper($patient->apellido) . ', ' . $patient->nombre);
            $output .= $frm->view('doctor', Lang::get(self::LANG_FILE . '.doctor'), '<i class="fa fa-user-md"></i>&nbsp; ' . strtoupper($doctor->apellido) . ', ' . $doctor->nombre);
            $output .= $frm->view('record_date', Lang::get(self::LANG_FILE . '.record_date'), '<i>' . Functions::longDateFormat($item->created_at) . '</i>');
        $output .= $frm->halfPanelClose();
        //right panel
        $output .= $frm->halfPanelOpen(false, 6, 'text-center');
            if (!empty($item->hora_inicio)) {
                $remaining = Functions::remainingTime($item->hora_inicio);
                if ($remaining->invert) {
                    //if it is a matter of minutes, show minutes counter
                    if ($remaining->y == 0 && $remaining->m == 0 && $remaining->d == 0 && $remaining->h == 0) {
                        if ($remaining->i > 0) {
                            $output .= $frm->remainingTime($remaining->i, 60000);
                            $this->setReturn('script', $frm->script());
                        }
                    }
                    else {
                        $output .=  ($remaining->y ? (Functions::singlePlural(Lang::get('citas.year'), Lang::get('citas.years'), $remaining->y, true) . ', ') : '') . 
                                    ($remaining->m ? (Functions::singlePlural(Lang::get('citas.month'), Lang::get('citas.months'), $remaining->m, true) . ', ') : '') . 
                                    ($remaining->d ? (Functions::singlePlural(Lang::get('citas.day'), Lang::get('citas.days'), $remaining->d, true) . ', ') : '') . 
                                    ($remaining->h ? (Functions::singlePlural(Lang::get('citas.hour'), Lang::get('citas.hours'), $remaining->h, true) . ', ') : '') . 
                                    ($remaining->i ? (Functions::singlePlural(Lang::get('citas.minute'), Lang::get('citas.minutes'), $remaining->i, true)) : '');
                    }
                }
            }
        $output .= $frm->halfPanelClose(true);

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