<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 12/03/15
 * Time: 03:05 PM
 */

class Opcion {

    public $days_to_show;
    public $start_time;
    public $end_time;
    public $min_time;
    public $max_time;

    /**
     * Devuélve las reglas de validación para un campo específico o el arreglo de reglas por defecto.
     *
     * @param string $field     Nombre del campo del que se quiere las reglas de validación.
     * @param int $ignore_id    ID del elemento que se está editando, si es el caso.
     * @return array
     */
    public static function getValidationRules($field = null, $ignore_id = 0) {
        $rules = array(
            'days_to_show'  => 'required|array'
            'start_time'    => array('required', 'regex:/(0[0-9]|1[0-2]):([0-5][0-9]) (AM|PM)/'),
            'end_time'      => array('required', 'regex:/(0[0-9]|1[0-2]):([0-5][0-9]) (AM|PM)/'),
            'min_time'      => array('required', 'regex:/(0[0-9]|1[0-2]):([0-5][0-9]) (AM|PM)/'),
            'max_time'      => array('required', 'regex:/(0[0-9]|1[0-2]):([0-5][0-9]) (AM|PM)/'),
        );
        if ($field === null) {
            return $rules;
        }
        return $rules[$field];
    }


    public function save($values) {
        $this->days_to_show = implode($values['days_to_show']);
        $this->start_time = Functions::ampmto24($values['start_time']);
        $this->end_time = Functions::ampmto24($values['end_time']);
        $this->min_time = Functions::ampmto24($values['min_time']);
        $this->max_time = Functions::ampmto24($values['max_time']);

        $f = fopen(public_path() . '/user_config.php', 'w');
        if ($f) {
            $content = <<<EOT
$options = array(
    'days_to_show' = '{$this->days_to_show}',
    'start_time' = '{$this->start_time}',
    'end_time' = '{$this->end_time}',
    'min_time' = '{$this->min_time}',
    'max_time' = '{$this->max_time}'
);
EOT;
            fwrite($f, $content);
            fclose($f);
        }
    }


    public function load() {
        $options = array(
            'days_to_show' = '1, 2, 3, 4, 5, 6',
            'start_time' = '08:00',
            'end_time' = '18:00',
            'min_time' = '06:00:00',
            'max_time' = '22:00:00'
        );
        @include (public_path() . '/user_config.php');
        $this->days_to_show = $options['days_to_show'];
        $this->start_time = $options['start_time'];
        $this->end_time = $options['end_time'];
        $this->min_time = $options['min_time'];
        $this->max_time = $options['max_time'];
    }

}