<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 29/12/14
 * Time: 01:06 PM
 */

class Paciente extends Eloquent {

    public $timestamps = true;

    protected $fillable = array(
        'nombre',
        'apellido',
        'tdni',
        'dni',
        'fecha_nacimiento',
        'sexo',
        'estado_civil',
        'direccion',
        'usuario_id'
    );

    protected $table = 'paciente';

    protected $searchable = array(
        'nombre',
        'apellido',
        'dni'
    );

    protected $booleans = array(

    );

    protected $deletable_models = array(

    );

    public static function getMaritalStatuses($item = null) {
        $elems = array(
            0 => 'single',
            1 => 'married',
            2 => 'divorced',
            3 => 'widower'
        );
        if ($item === null) {
            return $elems;
        }
        return $elems[$item];
    }

    public static function getGenders($item = null) {
        $elems = array(
            0 => 'female',
            1 => 'male'
        );
        if ($item === null) {
            return $elems;
        }
        return $elems[$item];
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
            'id'                => 'integer|min:0',
            'nombre'            => 'required|alpha_spaces|max:45',
            'apellido'          => 'required|alpha_spaces|max:45',
            //'dni'               => 'required|regex:/^[vejVEJ]{1}-{1}[0-9]{7,9}$/|unique:paciente,dni,' . (int)$ignore_id,
            'tdni'              => 'in:V,E,J',
            'dni'               => 'required|regex:/^[0-9]{7,9}$/|unique:paciente,dni,' . (int)$ignore_id,
            'fecha_nacimiento'  => 'date_format:Y-m-d',
            'sexo'              => 'in:0,1',
            'estado_civil'      => 'in:0,1,2,3', //soltero, casado, divorciado, viudo
            'direccion'         => 'max:255',
            'usuario_id'        => 'integer|exists:usuario,id|unique:paciente,usuario_id,' . (int)$ignore_id,

            'telefonos'         => 'regex:/[0-9,]+/',
            'avatar'            => 'image'
        );
        if ($field === null) {
            return $rules;
        }
        return $rules[$field];
    }

    public function setDniAttribute($value)
    {
        $this->attributes['dni'] = strtoupper($value);
    }

    public function setUsuarioIdAttribute($value)
    {
        $value = (int)$value;
        $this->attributes['usuario_id'] = $value == 0 ? null : $value;
    }

    public function setSexoAttribute($value)
    {
        $this->attributes['sexo'] = (int)$value;
    }

    public function getDniAttribute()
    {
        $tdni = $this->attributes['tdni'];
        return (!empty($tdni) ? strtoupper($tdni) : 'V') . '-' . preg_replace('/[^0-9]/', '', $this->attributes['dni']);
    }

    public function getNombreAttribute()
    {
        return ucwords($this->attributes['nombre']);
    }

    public function getApellidoAttribute()
    {
        return ucwords($this->attributes['apellido']);
    }

    //RELACIONES:
    public function tipoPariente() {
        return $this->belongsToMany('TipoPariente', 'paciente_tipo_pariente', 'paciente_id', 'tipo_pariente_id');
    }

    public function tipoContacto() {
        return $this->belongsToMany('TipoContacto', 'paciente_tipo_contacto', 'paciente_id', 'tipo_contacto_id');
    }

    public function usuario() {
        return $this->belongsTo('User', 'usuario_id', 'id');
    }

    public function cita() {
        return $this->hasMany('Cita', 'paciente_id', 'id');
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