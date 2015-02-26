<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 05/01/15
 * Time: 10:40 AM
 */

class TipoContacto extends Eloquent {

    public $timestamps = false;

    protected $fillable = array(
        'tipo'
    );

    protected $table = 'tipo_contacto';

    protected $searchable = array(
        'tipo'
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
            'id'    => 'integer|min:1',
            'tipo'  => 'required|alpha_spaces|max:20|unique:tipo_contacto,tipo,' . (int)$ignore_id
        );
        if ($field != null) {
            return $rules[$field];
        }
        else {
            return $rules;
        }
    }

    //RELACIONES:
    public function paciente() {
        return $this->belongsToMany('Paciente', 'paciente_tipo_contacto', 'tipo_contacto_id', 'paciente_id');
    }


    //SCOPES:
    public function scopeTelefonos($query) {
        return $query->where('tipo_contacto_id', '=', 1);
    }

    public function scopeCorreos($query) {
        return $query->where('tipo_contacto_id', '=', 2);
    }


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