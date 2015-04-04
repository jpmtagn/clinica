<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 02/04/15
 * Time: 7:15 PM
 */

class ServicioCategoria extends Eloquent {

    public $timestamps = false;

    protected $fillable = array(
        'nombre'
    );

    protected $table = 'categoria_servicio';

    protected $searchable = array(
        'nombre'
    );

    protected $booleans = array(

    );

    protected $deletable_models = array(

    );

    /**
     * Devuélve las reglas de validación para un campo específico o el arreglo de reglas por defecto.
     *
     * @param string $field     Nombre del campo del que se quiere las reglas de validación.
     * @param int $ignore_id    ID del elemento que se está editando, si es el caso.
     * @return array
     */
    public static function getValidationRules($field = null, $ignore_id = 0) {
        $rules = array(
            'id'        => 'integer|min:1',
            'nombre'    => 'required|max:45|unique:categoria_servicio,nombre,' . (int)$ignore_id
        );
        if ($field != null) {
            return $rules[$field];
        }
        else {
            return $rules;
        }
    }

    //RELACIONES:
    public function servicios() {
        return $this->hasMany('Servicio', 'categoria_servicio_id', 'id');
    }


    //SCOPES:


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