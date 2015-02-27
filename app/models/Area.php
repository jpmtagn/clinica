<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 26/02/15
 * Time: 02:33 PM
 */

class Area extends Eloquent {

    public $timestamps = false;

    protected $fillable = array(
        'nombre',
        'descripcion'
    );

    protected $table = 'area';

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
            'descripcion'   => 'max:255'
        );
        if ($field === null) {
            return $rules;
        }
        return $rules[$field];
    }

    //RELACIONES:
    public function consultorios() {
        return $this->hasMany('Consultorio', 'area_id', 'id');
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