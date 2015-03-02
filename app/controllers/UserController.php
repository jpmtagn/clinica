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

    const TITLE_FIELD = 'correo';

    /** Navegacion **/

    /**
     * Muestra la página por defecto
     * @return mixed
     */
    public function mostrarDefault() {
        return 'Here you are.';
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
        return View::make('admin.inicio');
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
        return View::make('admin.inicio');
    }

    /**
     * Procesa los datos ingresados por el usuario para el inicio de sesión
     * @return mixed
     */
    public function iniciarSesionPost() {
        $validator = Validator::make(Input::all(),
            array(
                'correo'        => 'required',
                'password'      => 'required'
            )
        );
        if ($validator->passes()) {
            $credentials = array(
                'correo'        => Input::get('correo'),
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
        $output .= $frm->view('correo', Lang::get(self::LANG_FILE . '.email'), $item->correo);
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
                'correo'        => Auth::user()->correo,
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
                'correo'    => 'admin@defecto',
                'password'  => 'cli_0123', //<-- cambiar
                'activo'    => 1,
                'admin'     => 1
            ));
        }
        return Redirect::route('inicio_sesion');
    }

} 