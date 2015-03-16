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
        $doctor = User::find($doctor_id);
        if ($doctor) {
            $doctor = $doctor->paciente;
            return View::make('admin.disponibilidad_doctor')->with(array(
                'doctor_id' => $doctor_id,
                'doctor_nombre' => Functions::firstNameLastName($doctor->nombre, $doctor->apellido)
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


    public function getDisponibilidad($doctor_id) {
        $cal_start = strtotime(Input::get('start'));
        $cal_end = strtotime(Input::get('end'));
        $items_json = array();
        $doctor = User::find($doctor_id);
        if ($doctor) {
            $items = $doctor->disponibilidad()->fromDateToDate($cal_start, $cal_end)->get();

            $cal_start_w = date('N', $cal_start) - 1;
            if ($cal_start_w > 0) { //not a monday
                $cal_start = strtotime('-' . $cal_start_w . ' days', $cal_start);
            }

            foreach ($items as $item) {
                $color = '#849917';

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

}