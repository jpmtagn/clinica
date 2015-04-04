<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 08/03/15
 * Time: 03:58 PM
 */

class DisponibilidadController extends BaseController {

    const PAGE_LIMIT = 5;

    const MODEL = 'Disponibilidad';

    const LANG_FILE = 'disponibilidad';

    const TITLE_FIELD = 'inicio';

    /** Navegacion **/

    /**
     * Muestra la página de disponibilidad del doctor
     * @param $doctor_id
     * @return mixed
     */
    public function paginaAdminDisponibilidad($doctor_id) {
        if (User::canViewDisponibilidadState($doctor_id)) {

        }
        elseif (User::is(User::ROL_DOCTOR)) {
            $doctor_id = Auth::user()->id;
        }
        else {
            $doctor_id = 0;
        }
        $doctor = User::find($doctor_id);
        if ($doctor) {
            $doctor = $doctor->paciente;
            $options = Opcion::load();
            return View::make('admin.disponibilidad_doctor')->with(array(
                'doctor_id' => $doctor_id,
                'doctor_nombre' => Functions::firstNameLastName($doctor->nombre, $doctor->apellido),
                'read_only' => !User::canChangeDisponibilidadState($doctor_id),
                'options' => $options
            ));
        }
        return Redirect::route('inicio');
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
        return '';
    }

    /**
     * Código HTML que se envía al realizar una búsqueda
     * @param $records
     * @param $search_fields
     * @return string
     */
    public function buscarReturnHtml($records, $search_fields) {
        //return AForm::searchResults($records, 'inicio', array(array(Lang::get(self::LANG_FILE . '.patient'),'nombre_paciente'), array(Lang::get(self::LANG_FILE . '.doctor'),'nombre_doctor')));
        return '';
    }


    /*public function getDisponibilidad($doctor_id = 0) {
        if ($doctor_id == 0) {
            $doctor_id = (int)Input::get('doctor_id');
            if ($doctor_id == 0) return '[]';
        }
        $cal_start = strtotime(Input::get('start'));
        $cal_end = strtotime(Input::get('end'));
        //only fetch if range not larger than a week
        if ($cal_end - $cal_start > 604800000) { //604800000 = 1000 * 60 * 60 * 24 * 7
            return '[]';
        }
        $items_json = array();
        $doctor = User::find($doctor_id);
        if ($doctor) {
            $items = $doctor->disponibilidad()->fromDateToDate($cal_start, $cal_end)->get();

            $cal_start_w = date('N', $cal_start) - 1;
            if ($cal_start_w > 0) { //not a monday
                $cal_start = strtotime('-' . $cal_start_w . ' days', $cal_start);
            }
            
            $color = '#849917';//'#fff';

            foreach ($items as $item) {

                $start = strtotime($item->inicio);
                $end = strtotime($item->fin);

                $dws = date('N', $start) - 1;
                $dwe = date('N', $end) - 1;

                $start = date('Y-m-d', $dws > 0 ? strtotime('+' . $dws . ' days', $cal_start) : $start) . ' ' . date('H:i:s', $start);
                $end = date('Y-m-d', $dwe > 0 ? strtotime('+' . $dwe . ' days', $cal_start) : $end) . ' ' . date('H:i:s', $end);

                $items_json[] = <<<EOT
                {
                    "id": "{$item->id}",
                    "title": "",
                    "start": "{$start}",
                    "end": "{$end}",
                    "allDay": false,
                    "backgroundColor": "{$color}",
                    "state_id": "{$item->disponible}"
                }
EOT;
            }
        }

        return '[' . implode(',', $items_json) . ']';
    }*/


    public function getDisponibilidad($doctor_id = 0, $editable = false) {
        if ($doctor_id == 0) {
            $doctor_id = (int)Input::get('doctor_id');
            if ($doctor_id == 0) return '[]';
        }
        
        $cal_start = Input::get('start');
        $cal_end = Input::get('end');
        
        $items_json = array();
        $doctor = User::find($doctor_id);
        if ($doctor) {
            $items = $doctor->disponibilidad()->fromDateToDate($cal_start, $cal_end)->get();

            if (count($items)) {
                foreach ($items as $item) {
                    $start = $item->inicio;
                    $end = $item->fin;

                    if ($editable) {
                        $items_json[] = <<<EOT
                        {
                            "id": "{$item->id}",
                            "start": "{$start}",
                            "end": "{$end}"
                        }
EOT;
                    }
                    else {
                        $items_json[] = <<<EOT
                        {
                            "start": "{$start}",
                            "end": "{$end}",
                            "rendering": "inverse-background"
                        }
EOT;
                    }
                }
            }
            elseif (!$editable) {
                //nothing available, disable all calendar
                $items_json[] = <<<EOT
                {
                    "start": "{$cal_start} 00:00:00",
                    "end": "{$cal_end} 23:59:59",
                    "rendering": "background"
                }
EOT;
            }
        }

        return '[' . implode(',', $items_json) . ']';
    }

    public function getDisponibilidadEditable($doctor_id) {
        return $this->getDisponibilidad($doctor_id, true);
    }


    public function duplicar($disponibilidad_id, $fecha) {
        $model = self::MODEL;
        $item = $model::find($disponibilidad_id);
        if ($item) {
            $date_start = strtotime($item->inicio);
            $date_end = strtotime($item->fin);
            $diff = $date_end - $date_start;

            $end_date = strtotime($fecha . ' 23:59:59');

            if ($date_start < $end_date) {
                $new_items = array();
                $next_date = strtotime('+1 week', $date_start);
                while ($next_date <= $end_date) {
                    $new_item = array(
                        'inicio' => date('Y-m-d H:i:s', $next_date),
                        'fin' => date('Y-m-d H:i:s', $next_date + $diff),
                        'usuario_id' => $item->usuario_id,
                        'disponible' => $item->disponible,
                        'fijo'  => $item->fijo
                    );
                    $new_items[] = $new_item;
                    $next_date = strtotime('+1 week', $next_date);
                }
                $times = count($new_items);
                if ($times) {
                    Disponibilidad::insert($new_items);
                }
                return $times;
            }
        }
        return false;
    }


    public function calendarActionPost() {
        $disponibilidad_id = (int)Input::get('disponibilidad_id');
        $action = Input::get('action');
        $val = Input::get('val');

        if ($disponibilidad_id > 0) {
            $model = self::MODEL;
            $item = $model::find($disponibilidad_id);
            if (!User::canChangeDisponibilidadState($item->usuario_id)) {
                return $this->setError(Lang::get('global.no_permission'));
            }
            switch ($action) {
                case 'set_state':
                    $val = (int)$val;
                    if ($val < 0 || $val > 1) $val = 0;
                    $item->disponible = $val;
                    $item->save();
                    $this->setReturn('disponibilidad_id', $disponibilidad_id);
                    $this->setReturn('state', $item->disponible);
                    break;
                case 'get_state':
                    $this->setReturn('disponibilidad_id', $disponibilidad_id);
                    $this->setReturn('state', $item->disponible);
                    break;
                case 'delete':
                    $item->delete();
                    $this->setReturn('disponibilidad_id', $disponibilidad_id);
                    $this->setReturn('state', '-1');
            }
        }
        return $this->returnJson();
    }


    public function duplicarPost() {
         $validator = Validator::make(Input::all(),
            array(
                'disponibilidad_id' => 'required|integer|min:1',
                'fecha'             => 'required|date_format:Y-m-d'
            )
        );
        if ($validator->passes()) {
            $disponibilidad_id = (int)Input::get('disponibilidad_id');
            $fecha = Input::get('fecha');

            $times = $this->duplicar($disponibilidad_id, $fecha);

            if ($times !== false) {
                return $this->setSuccess( Lang::get('disponibilidad.duplicated_msg') . ' ' . Functions::singlePlural(Lang::get('disponibilidad.times_single'), Lang::get('disponibilidad.times_plural'), $times, true) );
            }
            else {
                return $this->setError( Lang::get('global.unable_perform_action') );
            }
        }
        return $this->setError( Lang::get('global.wrong_action') );
    }

}