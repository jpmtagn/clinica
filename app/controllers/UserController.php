<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 07/08/14
 * Time: 04:39 PM
 */

class UserController extends BaseController {

    const PAGE_LIMIT = 5;

    const MODEL = 'User';
    
    const LANG_FILE = 'usuarios';

    const TITLE_FIELD = 'nombre';

    /** Navegacion **/

    /**
     * Muestra la página por defecto
     * @return mixed
     */
    public function mostrarDefault() {
        if (Auth::check()) {
            return $this->paginaAdminInicio();
        }
        return $this->mostrarInicioSesion();
    }

    /**
     * Muestra el formulario de inicio de sesión
     * @return mixed
     */
    public function mostrarInicioSesion() {
        return View::make('formulario_inicio');
    }

    /**
     * Muestra la página de inicio del usuario que inicio sesión
     * @return mixed
     */
    public function paginaAdminInicio() {
        $user = Auth::user();
        if (!$user->admin && User::is(User::ROL_DOCTOR)) {
            return $this->paginaAdminInicioDoctor($user->id);
        }
        $total_patients = Paciente::count();
        $total_citas = Cita::count();
        $total_citas_today = Cita::forToday()->count();
        $total_citas_done = Cita::done()->count();
        $total_citas_cancelled = Cita::cancelled()->count();
        $chart_data_patient_month = DB::table('cita')
                                    ->selectRaw('MONTH(fecha) AS "mes", COUNT(fecha) AS "total"')
                                    ->where('estado', '=', 1)
                                    ->groupBy(DB::raw('MONTH(fecha)'))
                                    ->orderBy('fecha', 'ASC')
                                    ->take(12)
                                    ->get();

        return View::make('admin.inicio')->with(array(
            'total_patients' => $total_patients,
            'total_citas' => $total_citas,
            'total_citas_today' => $total_citas_today,
            'total_citas_done' => $total_citas_done,
            'total_citas_cancelled' => $total_citas_cancelled,
            'chart_data_patient_month' => $chart_data_patient_month
        ));
    }

    /**
     * Muestra la página de inicio para un usuario específico
     * @return mixed
     */
    public function paginaAdminInicioDoctor($doctor_id) {
        if (User::canViewDoctorPage($doctor_id) || User::is(User::ROL_DOCTOR, $doctor_id)) {
            $doctor = User::find($doctor_id);
            //$citas = $doctor->cita();

            $total_citas = $doctor->cita()->count();
            $total_citas_today = $doctor->cita()->forToday()->count();
            $total_citas_done = $doctor->cita()->done()->count();
            $total_citas_cancelled = $doctor->cita()->cancelled()->count();
            $chart_data_patient_month = DB::table('cita')
                                        ->selectRaw('MONTH(fecha) AS "mes", COUNT(fecha) AS "total"')
                                        ->where('estado', '=', 1)
                                        ->where('doctor_id', '=', $doctor_id)
                                        ->groupBy(DB::raw('YEAR(fecha), MONTH(fecha)'))
                                        ->orderBy('fecha', 'ASC')
                                        ->take(12)
                                        ->get();

            $doctor_name = $doctor->nombre; //username
            $doctor = $doctor->paciente;
            if ($doctor) {
                $doctor_name = Functions::firstNameLastName($doctor->nombre, $doctor->apellido); //full name
            }
            $doctor_avatar = URL::asset('img/avatars/s/' . ($doctor && !empty($doctor->avatar) ?  $doctor->avatar : 'default.jpg'));

            return View::make('admin.inicio_doctor')->with(array(
                'total_citas' => $total_citas,
                'total_citas_today' => $total_citas_today,
                'total_citas_done' => $total_citas_done,
                'total_citas_cancelled' => $total_citas_cancelled,
                'chart_data_patient_month' => $chart_data_patient_month,
                'doctor_name' => $doctor_name,
                'doctor_avatar' => $doctor_avatar,
                'doctor_id' => $doctor_id
            ));
        }
        return $this->mostrarDefault();
    }

    /**
     * Muestra la página de citas del día para un usuario específico
     * @return mixed
     */
    public function paginaAdminDoctorCitas($doctor_id) {
        if (User::canViewDoctorPage($doctor_id) || User::is(User::ROL_DOCTOR, $doctor_id)) {
            $doctor = User::find($doctor_id);
            
            $citas = $doctor->cita()->forToday()->orderBy('hora_inicio')->get();

            //uses the cita controller to use the buscar return template
            $controller = new CitaController();
            $citas = $controller->buscarReturnHtml($citas, array(), false);

            return View::make('admin.citas_doctor')->with(array(
                'doctor' => $doctor->paciente,
                'citas' => $citas,
                'doctor_id' => $doctor_id
            ));
        }
        return $this->mostrarDefault();
    }

    /**
     * Muestra la página de citas del día en versión de impresión para un usuario específico
     * @return mixed
     */
    public function paginaAdminDoctorCitasPrint($doctor_id) {
        if (User::canViewDoctorPage($doctor_id) || User::is(User::ROL_DOCTOR, $doctor_id)) {
            $doctor = User::find($doctor_id);
            
            $citas = $doctor->cita()->forToday()->orderBy('hora_inicio')->get();

            //uses the cita controller to use the buscar return template
            $controller = new CitaController();
            $citas = $controller->buscarReturnHtmlPrint($citas, false, false);

            $doctor = $doctor->paciente;

            return View::make('admin.citas_doctor_print')->with(array(
                'doctor_name' => Functions::firstNameLastName($doctor->nombre, $doctor->apellido),
                'citas' => $citas,
                'date' => Functions::longDateFormat(time(), true, false),
                'doctor_id' => $doctor_id
            ));
        }
        return $this->mostrarDefault();
    }

    /**
     * Muestra la página de usuarios
     * @return mixed
     */
    public function paginaAdminUsuarios() {
        if (Auth::user()->admin) {
            $total = $this->getTotalItems();
            $roles = RolController::getRoles();
            return View::make('admin.usuarios')->with(
                array(
                    'active_menu' => 'usuarios',
                    'total' => $total,
                    'roles' => $roles
                )
            );
        }
        return $this->mostrarDefault();//View::make('admin.inicio');
    }

    /**
     * Muestra la página de usuarios
     * @return mixed
     */
    public function paginaAdminOpciones() {
        if (Auth::user()->admin) {
            return View::make('admin.opciones')->with(
                array(
                    'active_menu' => 'opciones'
                )
            );
        }
        return $this->mostrarDefault();//View::make('admin.inicio');
    }

    /**
     * Procesa los datos ingresados por el usuario para el inicio de sesión
     * @return mixed
     */
    public function iniciarSesionPost() {
        $validator = Validator::make(Input::all(),
            array(
                'nombre'        => 'required',
                'password'      => 'required'
            )
        );
        if ($validator->passes()) {
            $credentials = array(
                'nombre'        => Input::get('nombre'),
                'password'      => Input::get('password'),
                'activo'        => 1
            );

            //los datos son correctos
            if (Auth::attempt($credentials, Input::get('rememberme', 0) == 1)) {
                return Redirect::route('admin_inicio');
            }
        }
        //los datos son incorrectos
        return Redirect::route('inicio_sesion')
            ->withInput(Input::except('password'))
            ->with(array(
                'fail' => 1,
                'msg' => Lang::get('formulario_inicio.fail_login')
            ));
    }


    public function editarRelational($item) {
        //ROLES
        $roles = isset($_POST['roles']) ? array_map('intval', Input::get('roles')) : false;
        if ($roles) {
            $item->roles()->sync( $roles ); //sync( Input::get('roles') )
        }
        return true; //needs to return true to output json
    }


    public function additionalData($item) {
        $this->setReturn('roles', Functions::langArray(self::LANG_FILE, $item->roles->toArray(), 'nombre', 'id'));
    }


    public function outputInf( $item ) {
        $roles = $item->roles->toArray();

        $frm = new AForm;
        $output = "";
        $output .= $frm->id( $item->id );
        $output .= $frm->hidden('action');
        $output .= $frm->view('nombre', Lang::get(self::LANG_FILE . '.username'), $item->nombre);
        $output .= $frm->view('admin', Lang::get(self::LANG_FILE . '.admin'), $item->admin ? Lang::get('global.yes') : Lang::get('global.no'));
        $output .= $frm->view('activo', Lang::get(self::LANG_FILE . '.active'), $item->activo ? Lang::get('global.yes') : Lang::get('global.no'));
        $output .= $frm->view('creado_el', Lang::get(self::LANG_FILE . '.record_date'), Functions::longDateFormat($item->created_at));
        $output .= $frm->view('roles', Lang::get(self::LANG_FILE . '.roles'), implode(', ', Functions::langArray(self::LANG_FILE . '', $roles, 'nombre')));
        $output .= $frm->controlButtons();

        return $output;
    }


    public function buscarReturnHtml($records, $search_fields) {
        return AForm::searchResults($records, reset($search_fields), null, 'Admin', 'admin', 1);
    }


    public function getDoctorStatuses() {
        $doctores = Doctor::getAll();
        $user_status = array();
        foreach ($doctores as $doctor) {
            $atendidos = $doctor->atendidos;
            $pendientes = $doctor->pendientes;
            $t = $atendidos + $pendientes;
            if ($t > 0) {
                $p_atendido = (int)(($atendidos / $t) * 100);
                $p_pendiente = 100 - $p_atendido; //(int)($pendientes / $t);
            }
            else {
                $p_atendido = 0;
                $p_pendiente = 0;
            }
            $user_status['user_status_' . $doctor->usuario_id] = array(
                'atendidos' => $atendidos,
                'pendientes' => $pendientes,
                'p_atendido' => $p_atendido,
                'p_pendiente' => $p_pendiente
            );
        }
        $user_status['ok'] = 1;
        return json_encode($user_status);
    }


    public function getDoctorByLetter() {
        $letter = Input::get('letter');
        $html = '';
        if (strlen($letter) > 0) {
            $items = Doctor::getByLetter($letter);
            foreach ($items as $item) {
                $nombre = Functions::firstNameLastName($item->nombre, $item->apellido);
                $html.= <<<EOT
                <a class="list-group-item search-result" data-id="{$item->usuario_id}">{$nombre}</a>
EOT;
            }
        }
        $this->setReturn('html', $html);
        return $this->returnJson();
    }


    public function changePasswordPost() {
        $validator = Validator::make(Input::all(),
            array(
                'password_current'  => 'required',
                'password'          => 'required',
                'password2'         => 'same:password'
            )
        );
        if ($validator->passes()) {
            $user = Auth::user();
            $credentials = array(
                'nombre'        => Auth::user()->nombre,
                'password'      => Input::get('password_current'),
                'activo'        => 1
            );
            if (Auth::validate($credentials)) {
                $user->password = Input::get('password');
                $user->save();
                return $this->setSuccess(Lang::get(self::LANG_FILE . '.password_changed'));
            }
            else {
                return $this->setError(Lang::get(self::LANG_FILE . '.wrong_current_password'));
            }
        }
        return $this->setError( $validator->messages()->first() );
    }

    /**
     * Cierra la sesión para el usuario actual
     * @return mixed
     */
    public function cerrarSesion() {
        Auth::logout();
        Session::forget('user_avatar');
        return Redirect::route('inicio_sesion');
    }

    /**
     * Crea un usuario para poder ingresar al sistema durante la instalación
     * @return mixed
     */
    public function crearAdminDefecto() {
        $user = User::count();
        if (!$user) {
            User::create(array(
                'id'        => 1,
                'nombre'    => 'admin',
                'password'  => 'cli_0123', //<-- cambiar
                'activo'    => 1,
                'admin'     => 1
            ));
        }
        return Redirect::route('inicio_sesion');
    }

}