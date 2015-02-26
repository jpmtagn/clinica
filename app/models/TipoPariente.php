<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 29/12/14
 * Time: 01:03 PM
 */

class TipoPariente extends Eloquent {

    public $timestamps = false;

    protected $fillable = array(
        'id',
        'parentesco_m',
        'parentesco_f',
        'reciproco'
    );

    protected $table = 'tipo_pariente';

    protected $searchable = array(
        'parentesco_m',
        'parentesco_f'
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
            'id'            => 'integer|min:1',
            'parentesco_m'  => 'required|alpha_spaces|max:45|unique:tipo_pariente,parentesco_m,' . (int)$ignore_id,
            'parentesco_f'  => 'required|alpha_spaces|max:45|unique:tipo_pariente,parentesco_f,' . (int)$ignore_id,
            'reciproco'     => 'integer|exists:tipo_pariente,id'
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
        return $this->belongsToMany('Paciente', 'paciente_tipo_pariente', 'tipo_pariente_id', 'paciente_id');
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