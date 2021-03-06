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
            //$servicios = Functions::arrayIt(Servicio::get(), 'id', 'nombre');
            $servicios = Functions::arrayIt(Servicio::getWithEquipments(), 'id', 'nombre', 'equipos');
            //$consultorios = Functions::arrayIt(Consultorio::get(), 'id', 'nombre');
            $consultorios = Functions::arrayIt(Consultorio::get(), 'id', 'nombre', array('area', 'nombre'));
            $estados = Functions::langArray(self::LANG_FILE, Cita::state());
            return View::make('admin.citas')->with(
                array(
                    'active_menu' => 'citas',
                    'total' => $total,
                    'servicios' => $servicios,
                    'consultorios' => $consultorios,
                    'estados' => $estados
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
        //$total = $this->getTotalItems();
        //$model = self::MODEL;
        //$events = $model::with('paciente')->latestOnes()->get();
        $doctores = Doctor::getAll();
        $areas = Functions::arrayIt(Area::get(), 'id', 'nombre');
        $consultorios = Functions::arrayIt(Consultorio::get(), 'id', 'nombre', array('area', 'nombre'));
        $genders = Functions::langArray('pacientes', Paciente::getGenders());
        $marital_statuses = Functions::langArray('pacientes', Paciente::getMaritalStatuses());
        $doctor_letters = Doctor::getFirstNameLetters();
        $categorias_servicios = ServicioCategoria::get();
        $options = Opcion::load();
        return View::make('admin.calendario')->with(
            array(
                'active_menu' => 'citas',
                //'events' => $events
                //'total' => $total,
                'doctores' => $doctores,
                'areas' => $areas,
                'consultorios' => $consultorios,
                'estados' => Functions::langArray( self::LANG_FILE, Cita::state() ),
                'genders' => $genders,
                'marital_statuses' => $marital_statuses,
                'doctor_letters' => $doctor_letters,
                'categorias_servicios' => $categorias_servicios,
                'options' => $options,
                'read_only' => !User::canAddCitas()
            )
        );
    }

    /**
    * This function will be called after the model validation has passed successfully
    * @param $inputs
    * @return boolean
    */
    public function afterValidation($inputs) {
        //checking that the user has permission
        if (!User::canAddCitas()) {
            $this->setError(Lang::get('global.no_permission'));
            return false;
        }

        //will log the current user id with the record
        Input::merge(array('usuario_id' => Auth::user()->id));

        //if the doctor_id (to pick one) has not been sent then is sending just the time (ie dragging & dropping)
        //I will then find the object so I can get the missing attributes
        if (!isset($_POST['doctor_id'])) {
            $model = self::MODEL;
            $item = $model::find((int)Input::get('id'));
            if ($item) {
                if (!User::canChangeDisponibilidadState() && $item->estado != Cita::UNCONFIRMED) {
                    $this->setError(Lang::get('global.no_permission'));
                    return false;
                }
                $fields = $item->getFillable();
                $missing = array();
                foreach ($fields as $field) {
                    if (isset($inputs[$field])) continue;
                    $missing[$field] = $item->$field;
                }
                Input::merge($missing);
            }
            else {
                $this->setError(Lang::get('global.not_found'));
                return false;
            }
        }

		Session::set('input_fecha', Input::get('fecha'));
        $service = Input::get('servicio_id');
        if ($service > 0) {
            $service = Servicio::find($service);
            $duration = $service->duracion;
        }
        else {
            $duration = 0;
        }
        $start = Input::get('hora_inicio');
        //$end = Input::get('hora_fin'];

        if (!empty($start) && Input::get('doctor_id', false)) {
            $date = Input::get('fecha');
            $doctor_id = Input::get('doctor_id');
            $service_id = Input::get('servicio_id');
            $office_id = Input::get('consultorio_id');
            $patient_id = Input::get('paciente_id');
            $cita_id = (int)Input::get('id');

            $ignore_warning = Input::get('ignore_warning', false);
            $ignore_warning_all = Input::get('ignore_warning_all', false);
            $warning_key = (int)Input::get('warning_key', 0);

            $model = self::MODEL;
            $start = $date . ' ' . Functions::ampmto24($start);
            $end = Functions::addMinutes($start, $duration, 'h:i A');
            Input::merge(array('hora_fin' => $end)); //<-- replaces the input end time with the start time + treatment duration
            $end = $date . ' ' . Functions::ampmto24($end);

            if ($ignore_warning_all) return true;

            //WARNINGS

            //get availables for the user
            if (!($ignore_warning && $warning_key == 1)) {
                $available = Disponibilidad::forDateTime($start, $end, $doctor_id)->count();
                if (!$available) {
                    $this->setReturn('warning_key', '1');
                    $this->setReturn('bad', 'doctor');
                    $this->setReturn('overlapping', '0');
                    $this->setError(Lang::get(self::LANG_FILE . '.unavailable_doctor'));
                    return false;
                }
            }

            $equipments = Servicio::find($service_id);
            if ($equipments) $equipments = $equipments->equipos;
            $equipments = $equipments ? $equipments->lists('id') : false;

            $uses_equipment = (is_array($equipments) && count($equipments)) ? true : false;

            //get overlapping
            //$overlapping = $model::whereRaw('estado <> 3 AND ((hora_inicio < ? AND hora_inicio > ?) OR (hora_fin > ? AND hora_fin < ?))', array($end, $start, $start, $end))->get();
            $overlapping = $model::notCancelled()->between($start, $end)->get();
            foreach ($overlapping as $ol) {
                if ($cita_id == $ol->id) continue; //not going to validate against itself
                //check that the specified doctor is not busy for the given time
                if (!($ignore_warning && $warning_key == 2)) {
                    if ($ol->doctor_id == $doctor_id) {
                        $this->setReturn('warning_key', '2');
                        $this->setReturn('bad', 'doctor');
                        $this->setReturn('overlapping', $ol->id);
                        $this->setError(Lang::get(self::LANG_FILE . '.overlap_doctor'));
                        return false;
                    }
                }
                //check that the specified office is not busy for the given time
                if (!($ignore_warning && $warning_key == 3)) {
                    $office_in_use = 0;
                    foreach ($overlapping as $o) {
                        if ($cita_id == $o->id) continue; //not going to validate against itself
                        if ($o->consultorio_id == $office_id) {
                            $office_in_use++;
                        }
                    }
                    if ($office_in_use > 0) {
                        $office = Consultorio::find($office_id);
                        if ($office_in_use >= $office->capacidad) {
                            $this->setReturn('warning_key', '3');
                            $this->setReturn('bad', 'office');
                            $this->setReturn('overlapping', $ol->id);
                            $this->setError(Lang::get(self::LANG_FILE . '.overlap_office'));
                            return false;
                        }
                    }
                }
                //check that the specified service's equipment is available
                if ($uses_equipment && is_array($equipments) && count($equipments) && !($ignore_warning && $warning_key == 4)) {
                    //find the overlapping service's equipments
                    $ol_equipments = $ol->equipos;
                    $ol_equipments = $ol_equipments ? $ol_equipments->lists('id') : false;

                    if (is_array($ol_equipments)) {
                        //will check each equipment with the posible ones for the selected service
                        foreach ($ol_equipments as $ol_equipment) {
                            if (in_array($ol_equipment, $equipments)) {
                                //if found will then remove it from the selected service's equipment list
                                $equipments = array_diff($equipments, array($ol_equipment));
                                break;
                            }
                        }
                    }
                }
                //check that the specified patient is not overlapping himself
                if (!($ignore_warning && $warning_key == 5)) {
                    if ($ol->paciente_id == $patient_id) {
                        $this->setReturn('warning_key', '5');
                        $this->setReturn('bad', 'patient');
                        $this->setReturn('overlapping', $ol->id);
                        $this->setError(Lang::get(self::LANG_FILE . '.overlap_patient'));
                        return false;
                    }
                }
            }

            if ($uses_equipment && count($equipments) == 0) {
                $this->setReturn('warning_key', '4');
                $this->setReturn('bad', 'service');
                $this->setError(Lang::get(self::LANG_FILE . '.overlap_equipment'));
                return false;
            }
        }
        return true;
    }

    /**
     * Proceso adicional al editar / crear un nuevo registro
     * @param $item
     * @return bool
     */
    public function editarRelational($item) {
        $paciente = $item->paciente;
        $this->setReturn('titulo', $paciente ? Functions::firstNameLastName($paciente->nombre, $paciente->apellido) : '-');
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
        $office = $item->consultorio;

        //left panel
        $output .= $frm->halfPanelOpen(true);
            $output .= $frm->view('fecha', Lang::get(self::LANG_FILE . '.date'), '<i class="fa fa-calendar-o"></i>&nbsp; ' . Functions::longDateFormat($item->fecha));
            if (!empty($item->hora_fin)) {
                $output .= $frm->view('start_time', Lang::get(self::LANG_FILE . '.time_start'), '<i class="fa fa-clock-o"></i>&nbsp; ' . Functions::justTime($item->hora_inicio) . ' - ' . Functions::justTime($item->hora_fin));
            }
            $output .= $frm->view('patient', Lang::get(self::LANG_FILE . '.patient'), '<i class="fa fa-user"></i>&nbsp; ' . strtoupper($patient->apellido) . ', ' . $patient->nombre);
            $output .= $frm->view('doctor', Lang::get(self::LANG_FILE . '.doctor'), '<i class="fa fa-user-md"></i>&nbsp; ' . strtoupper($doctor->apellido) . ', ' . $doctor->nombre);
            $output .= $frm->view('office', Lang::get(self::LANG_FILE . '.office'), '<i class="fa fa-cube"></i>&nbsp; ' . $office->nombre . ' (' . $office->area->nombre . ')');
            $output .= $frm->view('record_date', Lang::get(self::LANG_FILE . '.record_date'), '<i>' . Functions::longDateFormat($item->created_at) . '</i>');
        $output .= $frm->halfPanelClose();
        //right panel
        $output .= $frm->halfPanelOpen(false, 6, 'text-center');
            if (!empty($item->hora_inicio)) {
                $remaining = Functions::remainingTime($item->hora_inicio);
                if ($item->hora_inicio > date('Y-m-d H:i')) { //(!$remaining->invert) {
                    //if it is a matter of minutes, show minutes counter
                    if ($remaining->y == 0 && $remaining->m == 0 && $remaining->d == 0 && $remaining->h == 0) {
                        if ($remaining->i > 0) {
                            $output .= $frm->remainingTime($remaining->i, 60000);
                            $this->setReturn('script', $frm->script());
                        }
                    }
                    else {
                        $output .=  ($remaining->y ? (Functions::singlePlural(Lang::get('global.year'), Lang::get('global.years'), $remaining->y, true) . ' ') : '') .
                                    ($remaining->m ? (Functions::singlePlural(Lang::get('global.month'), Lang::get('global.months'), $remaining->m, true) . ' ') : '') .
                                    ($remaining->d ? (Functions::singlePlural(Lang::get('global.day'), Lang::get('global.days'), $remaining->d, true) . ' ') : '') .
                                    ($remaining->h ? (Functions::singlePlural(Lang::get('global.hour'), Lang::get('global.hours'), $remaining->h, true) . ' ') : '') .
                                    ($remaining->i ? (Functions::singlePlural(Lang::get('global.minute'), Lang::get('global.minutes'), $remaining->i, true)) : '');
                    }
                }
            }
        $output .= $frm->halfPanelClose(true);

        $output .= $frm->controlButtons();


        return $output;
    }

    public function searchByFields($date, $doctor_id = 0, $paciente_id = 0, $servicio_id = 0, $consultorio_id = 0) {
        $model = self::MODEL;
        $records = new $model;

        $records = $records->orderBy('hora_inicio', 'DESC')->take(100);

        //if the date is specified
        if (strlen($date) > 0) {
            $records = $records->where('fecha', '=', $date);
        }
        //if the doctor is specified
        if ($doctor_id > 0) {
            $records = $records->where('doctor_id', '=', $doctor_id);
        }
        //if the patient is specified
        if ($paciente_id > 0) {
            $records = $records->where('paciente_id', '=', $paciente_id);
        }
        //if the service is specified
        if ($servicio_id > 0) {
            $records = $records->where('servicio_id', '=', $servicio_id);
        }
        //if the office is specified
        if ($consultorio_id > 0) {
            $records = $records->where('consultorio_id', '=', $consultorio_id);
        }
        $records = $records->get();

        return $records;
    }

    public function buscarGetAlt() {
        $validator = Validator::make(Input::all(),
            array(
                'search_query'          => '',
                /*'search_page'           => 'required|integer|min:1',*/
                'buscar_doctor_id'      => 'integer|min:1',
                'buscar_paciente_id'    => 'integer|min:1',
                'buscar_servicio_id'    => 'integer|min:0',
                'buscar_consultorio_id' => 'integer|min:0'
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
                $servicio_id = (int)Input::get('buscar_servicio_id');
                $consultorio_id = (int)Input::get('buscar_consultorio_id');

                $records = $this->searchByFields($query, $doctor_id, $paciente_id, $servicio_id, $consultorio_id);
                
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
    public function buscarReturnHtml($records, $search_fields, $show_doctor = true, $show_service = false, $show_office = false) {
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
                //$id = $record->id;
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
                //  date & time
                $row = Functions::inactiveIf('<h4><i class="fa fa-clock-o"></i> ' . $row . '</h4>', $record->estado == Cita::DONE);
                
                //  patient
                $patient = $record->paciente;//Paciente::find($record->paciente_id);
                $row .= '<br><b>' . Lang::get(self::LANG_FILE . '.patient') . '</b>: ' . Functions::firstNameLastName($patient->nombre, $patient->apellido);
                
                //  doctor
                if ($show_doctor) {
                    $doctor = User::find($record->doctor_id)->paciente;
                    $row .= '<br><b>' . Lang::get(self::LANG_FILE . '.doctor') . '</b>: ' . Functions::firstNameLastName($doctor->nombre, $doctor->apellido);
                }

                //  treatment
                if ($show_service) {
                    $service = $record->servicio;//Servicio::find($record->paciente_id);
                    $row .= '<br><b>' . Lang::get(self::LANG_FILE . '.service') . '</b>: ' . $service->nombre;
                }

                //  location
                if ($show_office) {
                    $office = $record->consultorio;
                    $area = $office->area->nombre;
                    $row .= '<br><b>' . Lang::get(self::LANG_FILE . '.office') . '</b>: ' . $office->nombre . ' &nbsp; (' . $area . ')';
                }

                $output.= <<<EOT
                    <a class="list-group-item search-result" data-id="{$record->id}">{$row}</a>
EOT;
            }
        }

        return $output;
    }


    public function buscarReturnHtmlPrint($records, $show_date = true, $show_doctor = true, $show_service = true, $show_office = true) {
        $output = "";
        if (count($records)) {
            foreach ($records as $record) {
                //date & time
                if ($show_date) {
                    $row = Functions::longDateFormat($record->fecha);
                    if (!empty($record->hora_fin)) {
                        $row .= '&nbsp;&nbsp;(' . Functions::justTime($record->hora_inicio) . ')';
                    }
                }
                else {
                    if (!empty($record->hora_fin)) {
                        $row = Functions::justTime($record->hora_inicio);
                    }
                }
                $row = '<h4>' . $row . '</h4>';

                //  patient
                $patient = $record->paciente;//Paciente::find($record->paciente_id);
                $row .= '<b>' . Lang::get(self::LANG_FILE . '.patient') . '</b>: ' . Functions::firstNameLastName($patient->nombre, $patient->apellido);
                
                //  doctor
                if ($show_doctor) {
                    $doctor = User::find($record->doctor_id)->paciente;
                    $row .= '<br><b>' . Lang::get(self::LANG_FILE . '.doctor') . '</b>: ' . Functions::firstNameLastName($doctor->nombre, $doctor->apellido);
                }

                //  treatment
                if ($show_service) {
                    $service = $record->servicio;//Servicio::find($record->paciente_id);
                    $row .= '<br><b>' . Lang::get(self::LANG_FILE . '.service') . '</b>: ' . $service->nombre;
                }

                //  location
                if ($show_office) {
                    $office = $record->consultorio;
                    $area = $office->area->nombre;
                    $row .= '<br><b>' . Lang::get(self::LANG_FILE . '.office') . '</b>: ' . $office->nombre . ' &nbsp; (' . $area . ')';
                }

                $output.= '<br><br>' . $row;
            }
        }

        return $output;
    }


    public function getCitas() {
        $cal_start = Input::get('start');
        $cal_end = Input::get('end');
        $citas_json = array();
        if (User::canViewAllCitas()) {
            $citas = Cita::fromDate($cal_start)->toDate($cal_end)->get();
        }
        else {
            $citas = Auth::user()->cita()->fromDate($cal_start)->toDate($cal_end)->get();
        }

        $doctor_color = array();
        $doctor_index = Doctor::getIds();
        $colors = array('#2983AE', '#A64499', '#ED1B24', '#F78F1E', '#FEF200', '#00A88F0', '#0092CE', '#6B439B', '#E44097', '#F37020', '#FFC20F', '#8DFD07');

        foreach ($citas as $cita) {
            $paciente = $cita->paciente;

            $title = Functions::firstNameLastName($paciente->nombre, $paciente->apellido) . '<br>' .
                     '<i>' . $cita->servicio->nombre . '</i><br>' .
                     '<b>' . $cita->consultorio->nombre . '</b>';

            $title = str_replace('"', '',  $title);

            $start = $cita->hora_inicio;
            $end = !empty($cita->hora_fin) ? "\"end\": \"$cita->hora_fin\"," : '';
            $all_day = $end != '' ? 'false' : 'true';

            //$color = $cita->estado == 1 ? 'blue' : 'gray';
            //$colors = array('#2983ae', 'blue', 'red', 'yellow');
            if (!isset($doctor_color[$cita->doctor_id])) {
                $doctor_color[$cita->doctor_id] = array_search($cita->doctor_id, $doctor_index);//count($doctor_color);
            }
            $color = $colors[ $doctor_color[$cita->doctor_id] ];

            $atention = (($cita->estado != Cita::DONE && $cita->estado != Cita::CANCELLED) && strtotime($cita->hora_inicio) < time()) ? '1' : '0';
			
			$comment = $cita->nota;
			$comment = $comment ? Functions::nl2br(htmlentities($comment->contenido)) : '';

            $user = $cita->usuario_id > 0 ? User::find($cita->usuario_id) : '';
            if ($user) {
                $user_data = $user->paciente;
                if ($user_data) {
                    $user = Functions::firstNameLastName($user_data->nombre, $user_data->apellido, true);
                }
                else {
                    $user = $user->nombre;
                }
            }

            $citas_json[] = <<<EOT
            {
                "id": "{$cita->id}",
                "title": "{$title}",
                "start": "{$start}",{$end}
                "allDay": {$all_day},
                "backgroundColor": "{$color}",
                "doctor_id": "{$cita->doctor_id}",
                "patient_id": "{$cita->paciente_id}",
                "service_id": "{$cita->servicio_id}",
                "office_id": "{$cita->consultorio_id}",
                "state_id": "{$cita->estado}",
                "atention": "{$atention}",
				"comment": "{$comment}",
				"user": "{$user}"
            }
EOT;
        }

        return '[' . implode(',', $citas_json) . ']';
    }


    public function checkAvailabilityPost() {
        $model = self::MODEL;
        $validator = Validator::make(Input::all(),
            $model::getValidationRules()
        );
        if ($validator->passes()) {

        }
    }


    private function infoDateTime($date, $start, $end) {
        $remaining = Functions::remainingTime( $date . ' ' . Functions::ampmto24($start), 'all' );
        //send information
        $this->setReturn('fecha_inf', Functions::longDateFormat( $date ));
        $this->setReturn('restante', $remaining != '' ? (Lang::get(self::LANG_FILE . '.in') . ' ' . $remaining) : ('<i class="fa fa-exclamation-triangle"></i> &nbsp;' . Lang::get(self::LANG_FILE . '.passed_time')));
        $this->setReturn('hora_inf', $start);
        //send back data
        $this->setReturn('fecha', $date);
        $this->setReturn('hora_inicio', $start);
        $this->setReturn('hora_fin', $end);
    }

    private function infoDoctor($doctor_id) {
        $doctor = User::find($doctor_id);
        if ($doctor) $doctor = $doctor->paciente;
        //send information
        $this->setReturn('doctor_name_inf', $doctor ? Functions::firstNameLastName($doctor->nombre, $doctor->apellido) : Lang::get('global.not_found'));
        $this->setReturn('avatar_inf', ($doctor && $doctor->avatar) ? URL::asset('img/avatars/s/' . $doctor->avatar) : URL::asset('img/avatars/s/default.jpg'));
        //send back data
        $this->setReturn('doctor_id', $doctor_id);
    }

    private function infoPatient($patient_id) {
        $patient = Paciente::find($patient_id);
        $num_citas = Cita::total($patient_id)->count();
        //send information
        $this->setReturn('patient_name_inf', $patient ? (Functions::firstNameLastName($patient->nombre, $patient->apellido) . ' <span class="pull-right text-muted">' . $patient->dni . '</span>') : Lang::get('global.not_found'));
        $this->setReturn('record_inf', Lang::get(self::LANG_FILE . '.record_date_alt') . ' ' . Functions::longDateFormat($patient->created_at));
        $this->setReturn('num_citas_inf', Functions::singlePlural(Lang::get(self::LANG_FILE . '.title_single'), Lang::get(self::LANG_FILE . '.title_plural'), $num_citas, true));
        //send back data
        $this->setReturn('paciente_id', $patient_id);
    }

    private function infoService($service_id) {
        $service = Servicio::find($service_id);
        //send information
        if ($service) {
            $equipment = $service->equipos;
            if ($equipment) $equipment = $equipment->lists('nombre');
            $desc = $service->descripcion;
        }
        else {
            $equipment = false;
        }
        if ($equipment && is_array($equipment)) {
            $equipment = ' <span class="pull-right text-muted">' . reset($equipment) . '</span>';
        }
        else {
            $equipment = '';
        }
        $this->setReturn('service_name_inf', $service ? (ucfirst($service->nombre) . $equipment) : Lang::get('global.not_found'));
        $this->setReturn('duration_inf', $service ? Functions::minToHours($service->duracion) : '');
        //send back data
        $this->setReturn('duration', $service ? $service->duracion : '0');
        $this->setReturn('servicio_id', $service_id);
    }

    private function infoOffice($office_id) {
        $office = Consultorio::find($office_id);
        //send information
        $this->setReturn('office_name_inf', $office ? ucfirst($office->nombre) : Lang::get('global.not_found'));
        $this->setReturn('area_inf', $office ? ucfirst($office->area->nombre) : '');
        //send back data
        $this->setReturn('consultorio_id', $office_id);
    }


    public function getAllInfo() {
        $cita_id = (int)Input::get('id');
        $this->setReturn('cita_id', $cita_id);
        if ($cita_id > 0) {
            $model = self::MODEL;
            $cita = $model::find($cita_id);
            if ($cita) {
                $this->infoDateTime($cita->fecha, Functions::justTime($cita->hora_inicio), Functions::justTime($cita->hora_fin));
                $this->infoDoctor($cita->doctor_id);
                $this->infoPatient($cita->paciente_id);
                $this->infoService($cita->servicio_id);
                $this->infoOffice($cita->consultorio_id);
            }
        }
        return $this->returnJson();
    }

    public function getInfoDateTime() {
        if ($this->validateInputs()) {
            $this->infoDateTime(Input::get('fecha'), Input::get('hora_inicio'), Input::get('hora_fin'));
        }
        return $this->returnJson();
    }

    public function getInfoDoctor() {
        if ($this->validateInputs()) {
            $this->infoDoctor(Input::get('doctor_id'));
        }
        return $this->returnJson();
    }

    public function getInfoPatient() {
        if ($this->validateInputs()) {
            $this->infoPatient(Input::get('paciente_id'));
        }
        return $this->returnJson();
    }

    public function getInfoService() {
        if ($this->validateInputs()) {
            $this->infoService(Input::get('servicio_id'));
        }
        return $this->returnJson();
    }

    public function getInfoOffice() {
        if ($this->validateInputs()) {
            $this->infoOffice(Input::get('consultorio_id'));
        }
        return $this->returnJson();
    }


    public function getAvailableOffice() {
        $service_id = (int)Input::get('servicio_id', 0);
        $start = Input::get('hora_inicio', '');
        $date = Input::get('fecha', '');
        $offices = '';
        if ($service_id) {
            $service = Servicio::find($service_id);
            if ($service) {
                //$now = new DateTime('now', new DateTimeZone( Config::get('app.timezone') ));
                //$now = $now->getTimestamp();//strtotime($now->date);

                $start = $date . ' ' . Functions::ampmto24($start);
                $start_time = strtotime($start);
                //if ($start_time >= $now) {
                    $duration = $service->duracion;
                    $end = Functions::addMinutes($start_time, $duration);
                    $busy_offices = Cita::notCancelled()->between($start, $end)->lists('consultorio_id');
                    if ($service) {
                        $offices = '<div class="list-group">';
                        foreach ($service->consultorios as $office) {
                            $badge = !in_array($office->id, $busy_offices) ? Lang::get(self::LANG_FILE . '.available') : ('<i class="fa fa-exclamation-triangle"></i> &nbsp;' . Lang::get(self::LANG_FILE . '.not_available'));
                            $offices .= <<<EOT
                                <a href="#" class="list-group-item office-btn" attr-id="{$office->id}">
                                    {$office->nombre} - {$office->area->nombre}
                                    <span class="badge">{$badge}</span>
                                </a>
EOT;
                        }
                        $offices .= '</div>';
                    }
                //}
            }
        }
        $this->setReturn('office_btns', $offices);
        return $this->returnJson();
    }


    public function calendarActionPost() {
        $cita_id = (int)Input::get('cita_id');
        $action = Input::get('action');
        $val = Input::get('val');

        if ($cita_id > 0) {
            $model = self::MODEL;
            $item = $model::find($cita_id);
            switch ($action) {
                case 'set_state':
                    $allowed = false;
                    $val = (int)$val;
                    switch ($val) {
                        case Cita::UNCONFIRMED:
                            $allowed = true;
                            break;

                        case Cita::CONFIRMED:
                        case Cita::CANCELLED:
                            if (User::canConfirmOrCancelCita()) {
                                $allowed = true;
                            }
                            break;
                        
                        case Cita::DONE:
                            if (User::canChangeCitaStateToDone()) {
                                $allowed = true;
                            }
                            break;
                    }
                    //if ($val < 0 || $val > count($item->state())) $val = 0;
                    if ($allowed) {
                        $item->estado = $val;
                        $to_log = serialize($item->toArray());
                        ActionLog::log(self::MODEL . ' changed_state', $to_log, $item->id);
                        $item->save();
                    }
                    $this->setReturn('cita_id', $cita_id);
                    $this->setReturn('state', $item->estado);
                    break;

                case 'get_state':
                    $this->setReturn('cita_id', $cita_id);
                    $this->setReturn('state', $item->estado);
                    break;
            }
        }
        return $this->returnJson();
    }


    public function getNoteId() {
        $cita_id = (int)Input::get('cita_id');
        $note_id = 0;
        $note_content = '';
        $cita = Cita::find($cita_id);
        if ($cita) {
            $note = $cita->nota;
            if ($note) {
                $note_id = (int)$note->id;
                $note_content = $note->contenido;
            }
        }
        $this->setReturn('nota_id', $note_id);
        $this->setReturn('nota', $note_content);
        return $this->returnJson();
    }


    public function findInCalendar() {
        $query = trim(Input::get('query'));
        $page = 1;
        $search_fields = array(
            'nombre_paciente',
            'apellido_paciente',
            'cedula_paciente',
            'nombre_doctor',
            'apellido_doctor',
            'cedula_doctor'
        );
        $match_total = 0;

        if (strlen($query)) {
            $records = $this->buscarTabla('citas', $query, $page, $search_fields, $match_total, null, array('fecha', 'DESC'));

            if ($match_total > 0) {
                foreach ($records as $record) {
                    $this->setReturn('cita_id', $record->id);
                    $this->setReturn('fecha', str_replace(' ', 'T', $record->hora_inicio));
                    break; //only using first one
                }
            }
        }
        else {
            return $this->setError( Lang::get('global.not_found') );
        }
        return $this->returnJson();
    }


    public function getFullDate() {
        $date = Input::get('date');
        if (count(explode('-', $date)) == 3) {
            $this->setReturn('date', Functions::longDateFormat($date));
            return $this->returnJson();
        }
        return $this->setError(Lang::get('global.wrong_action'));
    }

}