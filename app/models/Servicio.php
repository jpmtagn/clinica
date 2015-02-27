<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 26/02/15
 * Time: 02:57 PM
 */

class Servicio extends Eloquent {

    public $timestamps = false;

    protected $fillable = array(
        'nombre',
        'descripcion',
        'duracion'
    );

    protected $table = 'servicio';

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
            'duracion'      => 'integer|min:0|max:1440'
        );
        if ($field === null) {
            return $rules;
        }
        return $rules[$field];
    }

    //RELACIONES:
    public function citas() {
        return $this->belongsToMany('Cita', 'cita_servicio', 'servicio_id', 'cita_id');
    }

    public function consultorios() {
        return $this->belongsToMany('Consultorio', 'consultorio_servicio', 'servicio_id', 'consultorio_id');
    }

    public function equipos() {
        return $this->belongsToMany('Equipo', 'equipo_servicio', 'servicio_id', 'equipo_id');
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