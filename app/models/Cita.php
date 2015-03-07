<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 25/01/15
 * Time: 02:23 PM
 */

class Cita extends Eloquent {

    public $timestamps = true;

    protected $fillable = array(
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
        'doctor_id',
        'paciente_id',
        'servicio_id',
        'consultorio_id'
    );

    protected $table = 'cita';

    protected $searchable = array(
        'fecha'
    );

    protected $booleans = array();

    protected $deletable_models = array();

    public static function state($index = null) {
        $states = array(
            0 => 'por_confirmar',
            1 => 'realizada',
            2 => 'confirmada',
            3 => 'cancelada'
        );
        if ($index === null) return $states;
        return $states[$index];
    }

    /**
     * Devuélve las reglas de validación para un campo específico o el arreglo de reglas por defecto.
     *
     * @param string $field     Nombre del campo del que se quiere las reglas de validación.
     * @param int $ignore_id    ID del elemento que se está editando, si es el caso.
     * @return array
     */
    public static function getValidationRules($field = null, $ignore_id = 0) {
        $rules = array(
            'id'                    => 'integer|min:1',
            'fecha'                 => 'required|date_format:Y-m-d',
            'hora_inicio'           => array('regex:/(0[0-9]|1[0-2]):([0-5][0-9]) (AM|PM)/'),
            'hora_fin'              => array('regex:/(0[0-9]|1[0-2]):([0-5][0-9]) (AM|PM)/'),
            'hora_inicio_submit'    => array('regex:/^([01][0-9]|2[0-3]):[0-5][0-9]$/'),
            'hora_fin_submit'       => array('regex:/^([01][0-9]|2[0-3]):[0-5][0-9]$/'),
            'estado'                => 'integer',
            'doctor_id'             => ($ignore_id == 0 ? 'required|' : '') . 'integer|exists:usuario,id',
            'paciente_id'           => ($ignore_id == 0 ? 'required|' : '') . 'integer|exists:paciente,id',
            'servicio_id'           => ($ignore_id == 0 ? 'required|' : '') . 'integer|exists:servicio,id',
            'consultorio_id'        => ($ignore_id == 0 ? 'required|' : '') . 'integer|exists:consultorio,id'
        );
        if ($field === null) {
            return $rules;
        }
        return $rules[$field];
    }

    //RELACIONES:
    public function paciente() {
        return $this->belongsTo('Paciente', 'paciente_id', 'id');
    }

    public function doctor() {
        return $this->belongsTo('User', 'doctor_id', 'id');
    }

    public function servicio() {
        return $this->belongsTo('Servicio', 'servicio_id', 'id');
    }

    public function consultorio() {
        return $this->belongsTo('Consultorio', 'consultorio_id', 'id');
    }


    //ASIGNACIONES:
    public function setHoraInicioAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['hora_inicio'] = $this->fecha . ' ' . Functions::ampmto24($value) . ':00';
        }
        else {
            $this->attributes['hora_inicio'] = null;
        }
    }

    public function setHoraFinAttribute($value) {
        if (!empty($value)) {
            $this->attributes['hora_fin'] = $this->fecha . ' ' . Functions::ampmto24($value) . ':00';
        }
        else {
            $this->attributes['hora_fin'] = null;
        }
    }


    //FILTROS:
    public function scopeLatestOnes($query) {
        return $query->where('fecha', '>', date('Y-m-d', strtotime("-1 week")));
    }

    public function scopeFromDate($query, $val) {
        $date = Functions::explodeDateTime($val, true);
        if (checkdate($date['month'], $date['day'], $date['year'])) {
            return $query->where('fecha', '>=', $val);
        }
        return $query;
    }

    public function scopeToDate($query, $val) {
        $date = Functions::explodeDateTime($val, true);
        if (checkdate($date['month'], $date['day'], $date['year'])) {
            return $query->where('fecha', '<=', $val);
        }
        return $query;
    }

    public function scopeBetween($query, $val1, $val2) {
        return $query->where(function ($query) use ($val1, $val2) {
            $query->where('hora_inicio', '>=', $val1)
                  ->where('hora_inicio', '<=', $val2);
        })->orWhere(function ($query) use ($val1, $val2) {
            $query->where('hora_fin', '>=', $val1)
                  ->where('hora_fin', '<=', $val2);
        });
    }

    public function scopeTotal($query, $val) {
        return $query->where('paciente_id', '=', $val)->where('estado', '=', '0');
    }


    //GETTERS:
    public function getSearchable() {
        return $this->searchable;
    }

    public function getBooleans() {
        return $this->booleans;
    }

    public function getDeletableModels() {
        return $this->deletable_models;
    }

}