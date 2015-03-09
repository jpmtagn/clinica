<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 08/03/15
 * Time: 02:25 PM
 */

class Disponibilidad extends Eloquent {

    public $timestamps = false;

    protected $fillable = array(
        'id',
        'inicio',
        'fin',
        'disponible',
        'usuario_id'
    );

    protected $table = 'disponibilidad';

    protected $searchable = array(
        'inicio'
    );

    protected $booleans = array();

    protected $deletable_models = array();

    /**
     * Devuélve las reglas de validación para un campo específico o el arreglo de reglas por defecto.
     *
     * @param string $field     Nombre del campo del que se quiere las reglas de validación.
     * @param int $ignore_id    ID del elemento que se está editando, si es el caso.
     * @return array
     */
    public static function getValidationRules($field = null, $ignore_id = 0) {
        $rules = array(
            'id'                => 'integer|min:0',
            'inicio'            => 'required|date',//array('regex:/(0[0-9]|1[0-2]):([0-5][0-9]) (AM|PM)/'),
            'fin'               => 'required|date',//array('regex:/(0[0-9]|1[0-2]):([0-5][0-9]) (AM|PM)/'),
            'disponibilidad'    => 'in:0,1',
            'usuario_id'        => ($ignore_id == 0 ? 'required|' : '') . 'integer|exists:usuario,id'
        );
        if ($field === null) {
            return $rules;
        }
        return $rules[$field];
    }

    //RELACIONES:
    public function usuario() {
        return $this->belongsTo('User', 'usuario_id', 'id');
    }


    //ASIGNACIONES:
    public function setHoraInicioAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['hora_inicio'] = Functions::ampmto24($value) . ':00';
        }
        else {
            $this->attributes['hora_inicio'] = null;
        }
    }

    public function setHoraFinAttribute($value) {
        if (!empty($value)) {
            $this->attributes['hora_fin'] = Functions::ampmto24($value) . ':00';
        }
        else {
            $this->attributes['hora_fin'] = null;
        }
    }


    //FILTROS:
    public function scopeFromDate($query, $val) {
        $date = Functions::explodeDateTime($val, true);
        if (checkdate($date['month'], $date['day'], $date['year'])) {
            return $query->where('inicio', '>=', $val);
        }
        return $query;
    }

    public function scopeToDate($query, $val) {
        $date = Functions::explodeDateTime($val, true);
        if (checkdate($date['month'], $date['day'], $date['year'])) {
            return $query->where('fin', '<=', $val);
        }
        return $query;
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