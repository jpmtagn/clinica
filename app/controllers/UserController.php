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
            if (Auth::attempt($credentials, Input::get('rememberme')=='on')) {
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


    /*public function buscarGet() {
        $validator = Validator::make(Input::all(),
            array(
                'search_query' => 'required',
                'search_page'  => 'required|integer|min:1'
            )
        );
        if ($validator->passes()) {
            $query  = Input::get('search_query');
            $page   = Input::get('search_page');
            $search_fields = array();
            $match_total = 0;

            $records = $this->buscar( $query, $page, self::MODEL, $search_fields, $match_total );

            $this->setReturn('total', $match_total);
            $this->setReturn('total_page', count($records));
            $this->setReturn('results', AForm::searchResults($records, $search_fields[0], null, 'Admin', 'admin', 1));
            return $this->returnJson();
        }
        return $this->setError( Lang::get('global.wrong_action') );
    }*/

    /*public function infoGet() {
        $validator = Validator::make(Input::all(),
            array(
                'id' => 'required|integer|min:1'
            )
        );

        if ($validator->passes()) {
            $id = Input::get('id');
            $user = $this->fetchData( $id );

            $this->setReturn('title', Lang::get(self::LANG_FILE . '.inf_for') . $user->correo);
            $this->setReturn('results', $this->outputInf( $user ));
        }
        else {
            return $this->setError(Lang::get('global.not_found'));
        }

        return $this->returnJson();
    }*/

    /*public function datosGet() {
        $validator = Validator::make(Input::all(),
            array(
                'id' => 'required|integer|min:1'
            )
        );

        if ($validator->passes()) {
            $id = Input::get('id');
            $user = $this->fetchData( $id );

            $this->addToOutput( $user->toArray() );
            $this->additionalData( $user );
        }
        else {
            return $this->setError(Lang::get('global.not_found'));
        }

        return $this->returnJson();
    }*/

    /*public function accionPost() {
        $validator = Validator::make(Input::all(),
            array(
                'id' => 'required|integer|min:1'/*,
                'action'  => 'in:action_edit,action_delete'* /
            )
        );
        if ($validator->passes()) {
            $id = Input::get('id');
            $action = Input::get('action');
            switch ($action) {
                case 'action_delete':
                    $this->delete( $id );
                    $this->setReturn('deleted', 1);
                    return $this->setSuccess(Lang::get('global.del_msg'));
                    break;

                default:
                    $this->setError(Lang::get('global.wrong_action'));
            }
        }
        else {
            return $this->setError(Lang::get('global.not_found'));
        }

        return $this->returnJson();
    }*/

    /*public function editarPost() {
        $item = $this->editar(self::MODEL);
        if ($item != false) {
            $this->editarRelational($item);
        }
        return $this->returnJson();
    }*/

    /*public function registrarPost() {
        $item = $this->registrar(self::MODEL);
        if ($item != false) {
            $this->editarRelational($item);
        }
        return $this->returnJson();
    }*/

    /*public function totalGet() {
        $this->setReturn('total', $this->getTotalItems());
        return $this->returnJson();
    }*/


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

    /*private function fetchData($id) {
        $model = self::MODEL;
        $item = $model::findOrFail($id);

        //$this->addToOutput( $item->toArray() );

        if ($item) {
            return $item;//$this->outputInf( $item );
        }

        return false;
    }*/

    /*private function delete($id) {
        /*$model = self::MODEL;
        $item = $model::findOrFail($id);

        //deleting related models
        //$item->roles()->detach();
        foreach($item->getDeletableModels() as $rel_model => $type) {
            if ($type == 'many') {
                $item->$rel_model()->detach();
            }
            else {
                $item->$rel_model()->delete(); // ???
            }
        }
        $item->delete();* /
        //$model::destroy($id);
    }*/

    /*private function getTotalItems() {
        $model = self::MODEL;
        return $model::count();
    }*/

    /*public function listSeek() {
        $q = Input::get('q');
        $search_fields = '';
        $total = 0;
        $field = self::TITLE_FIELD;
        $records = $this->buscar($q, 1, $search_fields, $total);

        $list = array();
        foreach($records as $record) {
            $list[] = json_encode(array(
                'name' => $record->$field,
                '_id' => $record->id
            ));
        }

        return '[' . implode(',', $list) . ']';

        /*return <<<EOT

        [{
            "name": "SomeName",
            "_id": "SomeId"
        },
        {
            "name": "SomeName",
            "_id": "SomeId"
        }]
EOT;* /
    }*/


    /**
     * Cierra la sesión para el usuario actual
     * @return mixed
     */
    public function cerrarSesion() {
        Auth::logout();
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