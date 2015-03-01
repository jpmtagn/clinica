<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 27/02/15
 * Time: 03:30 PM
 */

class Equipo extends Eloquent {

    public $timestamps = false;

    protected $fillable = array(
        'nombre',
        'cantidad',
        'descripcion',
        'inamovible'
    );

    protected $table = 'equipo';

    protected $searchable = array(
        'nombre',
        'descripcion'
    );

    protected $booleans = array(
        'inamovible'
    );

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
            'id'            => 'integer|min:1',
            'nombre'        => 'required|max:45',
            'cantidad'      => 'integer|min:0',
            'descripcion'   => 'max:255',
            'inamovible'    => 'in:0,1'
        );
        if ($field === null) {
            return $rules;
        }
        return $rules[$field];
    }

    //RELACIONES:
    public function servicios() {
        return $this->belongsToMany('Servicio', 'equipo_servicio', 'equipo_id', 'servicio_id');
    }


    //ASIGNACIONES:


    //FILTROS:


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