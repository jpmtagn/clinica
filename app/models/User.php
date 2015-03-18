<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'usuario';

    const ROL_DOCTOR = 1;
    const ROL_RECEPCIONIST = 2;
    const ROL_PATIENT = 3;


    protected $fillable = array(
        'correo',
        'password',
        'contrasena_tmp',
        'activo',
        'admin'
    );

    protected $searchable = array(
        'correo'
    );

    protected $booleans = array(
        'activo',
        'admin'
    );

    protected $deletable_models = array(
        'roles' => 'many'
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
            'correo'    => 'required|max:255|unique:usuario,correo,' . (int)$ignore_id,
            'password'  => 'required',
            'password2' => 'same:password',
            'activo'    => 'in:on,1,0',
            'admin'     => 'in:on,1,0'
        );
        if ($field != null) {
            return $rules[$field];
        }
        else {
            return $rules;
        }
    }


    public function scopeNoActivado($query) {
        return $query->where('activo', '=', 0);
    }


    //RELACIONES:
    public function roles() {
        return $this->belongsToMany('Rol', 'usuario_rol', 'usuario_id', 'rol_id');
    }

    public function paciente() {
        return $this->hasOne('Paciente', 'usuario_id', 'id');
    }

    public function cita() {
        return $this->hasMany('Cita', 'doctor_id', 'id');
    }

    public function disponibilidad() {
        return $this->hasMany('Disponibilidad', 'usuario_id', 'id');
    }


    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = strlen($value) > 0 ? Hash::make($value) : '';
    }

    public function setActivoAttribute($value)
    {
        $this->attributes['activo'] = $value ? 1 : 0;
    }

    public function setAdminAttribute($value)
    {
        $this->attributes['admin'] = $value ? 1 : 0;
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

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'contrasena_tmp', 'remember_token');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return 'remember_token';
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->correo;
	}

    
    /**
    * Checks if the current user has a certain role
    *
    * @return boolean
    */
    public static function is($role, $with_id = null) {
        $user = Auth::user();
        if ($with_id !== null && $user->id != $with_id) return false;
        if (!is_array($role)) {
            $roles = array();
            $roles[] = $role;
            $is_num = is_int($role);
        }
        else {
            $roles = $role;
            $is_num = is_int(reset($role));
        }

        if ($is_num) {
            $user_roles = $user->roles->lists('id');
        }
        else {
            $user_roles = $user->roles->lists('nombre');
        }
        return in_array($role, $user_roles);
    }


    /**
    * Returns avatar name if the user has one
    *
    * @return mixed
    */
    public static function avatar() {
        $hasAvatar = Session::get('user_avatar', null);
        if ($hasAvatar !== null) {
            return $hasAvatar;
        }
        else {
            $patient = Auth::user()->paciente;
            $hasAvatar = $patient ? $patient->avatar : false;
            Session::set('user_avatar', !empty($hasAvatar) ? $hasAvatar : ($hasAvatar = '0'));
            return $hasAvatar;
        }
    }


    // ACCESS CONTROL

    public static function canChangeDisponibilidadState($user_id) {
        $user = Auth::user();
        return ($user->admin || $user->id == $user_id);
    }

    public static function canViewDoctorPage($user_id) {
        $user = Auth::user();
        return ($user->admin || $user->id == $user_id || User::is(User::ROL_RECEPCIONIST));
    }

    public static function canAddCitas($user = null) {
        if ($user === null) $user = Auth::user();
        return ($user->admin || User::is(User::ROL_RECEPCIONIST));
    }

    public static function canViewAllCitas($user = null) {
        if ($user === null) $user = Auth::user();
        return ($user->admin || User::is(User::ROL_RECEPCIONIST));
    }

    public static function canConfirmOrCancelCita($user = null) {
        if ($user === null) $user = Auth::user();
        return ($user->admin || User::is(User::ROL_RECEPCIONIST));
    }

    public static function canChangeCitaStateToDone($user = null) {
        if ($user === null) $user = Auth::user();
        return ($user->admin || User::is(array(User::ROL_RECEPCIONIST, User::ROL_DOCTOR)));
    }

    //menu
    public static function showMenu($user = null) {
        if ($user === null) $user = Auth::user();
        return (bool)$user->admin;
    }

    public static function canAdminPersonas($user = null) {
        if ($user === null) $user = Auth::user();
        return (bool)$user->admin;
    }

    public static function canAdminUsuarios($user = null) {
        if ($user === null) $user = Auth::user();
        return (bool)$user->admin;
    }

    public static function canAdminCitas($user = null) {
        if ($user === null) $user = Auth::user();
        return (bool)$user->admin;
    }

    public static function canAdminLugares($user = null) {
        if ($user === null) $user = Auth::user();
        return (bool)$user->admin;
    }

    public static function canAdminTratamientos($user = null) {
        if ($user === null) $user = Auth::user();
        return (bool)$user->admin;
    }

    public static function canAdminEquipos($user = null) {
        if ($user === null) $user = Auth::user();
        return (bool)$user->admin;
    }

    public static function canAdminOpciones($user = null) {
        if ($user === null) $user = Auth::user();
        return (bool)$user->admin;
    }

}