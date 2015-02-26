<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 26/02/15
 * Time: 02:41 PM
 */

class Consultorio extends Eloquent {

    public $timestamps = false;

    protected $fillable = array(
        'nombre',
        'descripcion',
        'capacidad',
        'area_id'
    );

    protected $table = 'consultorio';

    protected $searchable = array(
        'nombre',
        'descripcion'
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
            'id'            => 'integer|min:1',
            'nombre'        => 'required|max:45',
            'descripcion'   => 'max:255',
            'capacidad'     => 'integer|min:1',
            'area_id'       => 'required|integer|exists:area,id'
        );
        if ($field === null) {
            return $rules;
        }
        return $rules[$field];
    }

    //RELACIONES:
    public function area() {
        return $this->belongsTo('Area', 'area_id', 'id');
    }

    public function servicios() {
        return $this->belongsToMany('Servicio', 'consultorio_servicio', 'consultorio_id', 'servicio_id');
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