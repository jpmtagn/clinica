<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 30/12/14
 * Time: 04:26 PM
 */

class PacienteController extends BaseController {

    const PAGE_LIMIT = 5;

    const MODEL = 'Paciente';

    const LANG_FILE = 'pacientes';

    const TITLE_FIELD = 'nombre';

    /** Navegacion **/

    /**
     * Muestra la página de administración de Pacientes
     * @return mixed
     */
    public function paginaAdmin() {
        if (Auth::user()->admin) {
            //$model = self::MODEL;
            $total = $this->getTotalItems();
            $genders = Functions::langArray(self::LANG_FILE, Paciente::getGenders());
            $marital_statuses = Functions::langArray(self::LANG_FILE, Paciente::getMaritalStatuses());
            return View::make('admin.pacientes')->with(
                array(
                    'active_menu' => 'pacientes',
                    'total' => $total,
                    'genders' => $genders,
                    'marital_statuses' => $marital_statuses
                )
            );
        }
        return View::make('admin.inicio');
    }

    /**
     * Muestra la página de Mi Cuenta del usuario que inicio sesión
     * @return mixed
     */
    public function paginaMiCuenta() {
        $genders = Functions::langArray('pacientes', Paciente::getGenders());
        $marital_statuses = Functions::langArray('pacientes', Paciente::getMaritalStatuses());

        $user = Auth::user()->paciente;
        if ($user) {
            $field_values = $user->toArray();
            
            //TODO: this can be improved if you make just one query instead of two
            $phones = $user->tipoContacto()->telefonos()->get(array('tipo_contacto_id', 'contacto'))->lists('contacto');
            $emails = $user->tipoContacto()->correos()->get(array('tipo_contacto_id', 'contacto'))->lists('contacto');

            $field_values = array_merge($field_values, array(
                'telefonos' => implode(',', $phones),
                'correos'   => implode(',', $emails)
            ));
        }
        else {
            $field_values = null;
        }

        return View::make('admin.mi_cuenta')->with(
            array(
                'active_menu' => '',
                'total' => null,
                'genders' => $genders,
                'marital_statuses' => $marital_statuses,
                'field_values' => $field_values
            )
        );
    }

    /**
     * Proceso adicional al editar / crear un nuevo registro
     * @param $item
     */
    public function editarRelational($item) {
        if (Input::get('telefonos_check') != Input::get('telefonos') || Input::get('correos_check') != Input::get('correos')) {
            $item->tipoContacto()->sync( array() );

            //telefonos
            $phones = isset($_POST['telefonos']) ? explode(',', Input::get('telefonos')) : false;
            if ($phones) {
                foreach ($phones as $key => $val) {
                    if (strlen(trim($val)) > 0) {
                        $phones[$key] = array('tipo_contacto_id' => 1, 'contacto' => $val); //1 = phone
                    }
                    else {
                        unset($phones[$key]);
                    }
                }
                $item->tipoContacto()->attach( $phones );
            }

            //correos
            $emails = isset($_POST['correos']) ? explode(',', Input::get('correos')) : false;
            if ($emails) {
                foreach ($emails as $key => $val) {
                    if (strlen(trim($val)) > 0) {
                        $emails[$key] = array('tipo_contacto_id' => 2, 'contacto' => $val); //2 = email
                    }
                    else {
                        unset($emails[$key]);
                    }
                }
                $item->tipoContacto()->attach( $emails );
            }
        }

        //avatar
        if (Input::hasFile('avatar')) {
            $file = Input::file('avatar');
            if ($file->isValid()) {
                $extension = strtolower($file->getClientOriginalExtension());
                //if (in_array($extension, array('jpg', 'jpeg', 'png'))) {
                    $destination_path = 'img/avatars';
                    $filename = uniqid() . '.' . $extension;//$file->getClientOriginalName();
                    while (file_exists($destination_path . '/s/' . $filename)) {
                        $filename = uniqid() . '.' . $extension;
                    }
                    $moved = $file->move($destination_path, $filename);
                    if ($moved) {
                        Functions::smart_resize_image($destination_path . '/' . $filename, null, 256, 256, false, $destination_path . '/s/' . $filename, false);
                        $item->avatar = $filename;
                        $item->save();
                    }
                //}
            }
            //return $this->paginaMiCuenta();
            return Redirect::route('mi_cuenta');
        }
        return true;
    }

    /**
     * Datos adicionales que se envian al solicitar la información del registro para editar
     * @param $item
     */
    public function additionalData($item) {
        $telefonos = array();
        $correos = array();
        $this->getContactInfo($item, $telefonos, $correos);

        $user = $item->usuario;
        if ($user) {
            $this->setReturn('usuario_id_lbl', $item->usuario->correo);
        }
        $this->setReturn('telefonos', $telefonos);
        $this->setReturn('correos', $correos);
    }

    /**
     * Código HTML que se envía al solicitar la información del registro para visualizar
     * @param $item
     * @return string
     */
    public function outputInf($item) {
        $telefonos = array();
        $correos = array();
        $this->getContactInfo($item, $telefonos, $correos, true);

        $relatives = $item->tipoPariente()->select(array('tipo_pariente_id', 'pariente_id'))->get();

        $frm = new AForm;
        $output = "";
        $output .= $frm->id( $item->id );
        $output .= $frm->hidden('action');

        $output .= $frm->halfPanelOpen(true, 7);
        $output .= $frm->view('name', Lang::get(self::LANG_FILE . '.name'), strtoupper($item->apellido) . ', ' . $item->nombre);
        $output .= $frm->view('dni', Lang::get(self::LANG_FILE . '.dni'), $item->dni);
        $output .= $frm->view('birthdate', Lang::get(self::LANG_FILE . '.birthdate'), Functions::shortDateFormat($item->fecha_nacimiento));
        $output .= $frm->view('gender', Lang::get(self::LANG_FILE . '.gender'), Lang::get(self::LANG_FILE . '.' . Paciente::getGenders($item->sexo)));
        $output .= $frm->view('marital_status', Lang::get(self::LANG_FILE . '.marital_status'), Lang::get(self::LANG_FILE . '.' . Paciente::getMaritalStatuses($item->estado_civil)));
        $output .= $frm->view('address', Lang::get(self::LANG_FILE . '.address'), $item->direccion);
        $user = $item->usuario;
        if ($user) {
            $output .= $frm->view('user', Lang::get(self::LANG_FILE . '.user'), $user->correo . AForm::badge(Lang::get('usuarios.admin'),$user->admin));
        }
        $output .= $frm->halfPanelClose();

        $output .= $frm->halfPanelOpen(false, 5);
        $output .= $frm->view('contact_phones', Lang::get(self::LANG_FILE . '.phone'), $telefonos, 'fa-phone');
        $output .= $frm->view('contact_emails', Lang::get(self::LANG_FILE . '.email'), $correos, 'fa-envelope');

        if (count($relatives)) {
            $output .= '<br><label><b>' . Lang::get(self::LANG_FILE . '.relatives') . '</b></label>';
            $i = 0;
            foreach($relatives as $relative) {
                $relative_patient = Paciente::find($relative->pariente_id);
                if ($relative_patient) {
                    $output .= $frm->view('relative' . $i, $relative_patient->sexo == 1 ? $relative->parentesco_m : $relative->parentesco_f, $relative_patient->nombre . ' ' . $relative_patient->apellido . ' (' . $relative_patient->dni . ')');
                    $i++;
                }
            }
        }

        //$output .= $frm->dropDownButton(Lang::get(self::LANG_FILE . '.add_relative'), TipoPariente::get()->toArray(), 'dropDown_addRelative') . '<br>';
        $output .= $frm->halfPanelClose(true);

        $output .= $frm->controlButtons(null, null, $frm->dropDownButton(Lang::get(self::LANG_FILE . '.add_relative'), TipoPariente::get()->toArray(), 'dropDown_addRelative'));

        $this->setReturn('script', $frm->script());

        return $output;
    }

    /**
     * Código HTML que se envía al realizar una búsqueda
     * @param $records
     * @param $search_fields
     * @return string
     */
    public function buscarReturnHtml($records, $search_fields) {
        return AForm::searchResults($records, 'nombre', 'apellido');
    }

    private function getContactInfo($item, &$telefonos, &$correos, $formatted = false) {
        $contacts = $item->tipoContacto()->get(array('tipo_contacto_id', 'contacto'));//->lists('contacto');
        foreach ($contacts as $contact) {
            if ($contact->tipo_contacto_id == 1) {
                if ($formatted) {
                    $telefonos[] = Functions::formatPhone($contact->contacto);
                }
                else {
                    $telefonos[] = $contact->contacto;
                }
            }
            elseif ($contact->tipo_contacto_id == 2) {
                if ($formatted) {
                    $correos[] = '<a href="mailto:' . $contact->contacto . '">' . $contact->contacto . '</a>';
                }
                else {
                    $correos[] = $contact->contacto;
                }
            }
        }
        if ($formatted) {
            $telefonos = implode('<br>', $telefonos);
            $correos = implode('<br>', $correos);
        }
        else {
            $telefonos = implode(',', $telefonos);
            $correos = implode(',', $correos);
        }
    }


    public function registrarParientePost() {
        //pariente_id=9&tipo_pariente_id=3&paciente_id=8
        $validator = Validator::make(Input::all(), array(
                'pariente_id'       => 'integer|min:1',
                'tipo_pariente_id'  => 'integer|min:1',
                'paciente_id'       => 'integer|min:1'
            )
        );

        if ($validator->passes()) {
            $model = self::MODEL;
            $paciente_id = (int)Input::get('paciente_id');
            $paciente = $model::find( $paciente_id );
            if ($paciente) {
                $tipo_pariente_id = (int)Input::get('tipo_pariente_id');
                $tipo_pariente = TipoPariente::find( $tipo_pariente_id );
                if ($tipo_pariente) {
                    $pariente_id = (int)Input::get('pariente_id');
                    $paciente->tipoPariente()->attach(
                        $tipo_pariente_id,
                        array('pariente_id' => $pariente_id)
                    );
                    //save the inverse
                    $tipo_pariente_inv = (int)$tipo_pariente->reciproco;
                    if ($tipo_pariente_inv > 0) {
                        Paciente::find($pariente_id)->tipoPariente()->attach(
                            $tipo_pariente_inv,
                            array('pariente_id' => $paciente_id)
                        );
                    }
                    return $this->setSuccess( Lang::get('global.saved_msg') );
                }
            }
            return $this->setError( Lang::get('global.not_found') );
        }

        return $this->setError($validator->messages()->first());
    }


    public function afterValidation($values) {
        $id = (int)Input::get('id');
        if ($id > 0) {
            //if the user to be modified is not the current one and the current one is not an admin then abort
            if ($id != Auth::user()->paciente->id && !Auth::user()->admin) {
                return false;
            }
        }
        return true;
    }


    public function listSeekAlt() {
        return $this->listSeek(array('nombre', 'apellido'), ' ');
    }

    public function listSeekDoctor() {
        /*$role = Rol::find(1); //doctors

        $items = $role->users()->with('paciente')->get();

        return $items;*/
        $query = Input::get('q');
        $query = explode(' ', $query);

        $search_fields = array('nombre', 'apellido', 'dni');

        $records = DB::table('doctor');

        foreach($query as $q) {
            $q = trim($q);
            if (strlen($q) > 1) {
                $records = $records->where(function ($sql_query) use ($search_fields, $q) {
                    $first_query = true;
                    foreach($search_fields as $attr) {
                        if ($first_query) {
                            $sql_query->where($attr, 'LIKE', '%' . $q . '%');
                            $first_query = false;
                        }
                        else {
                            $sql_query->orWhere($attr, 'LIKE', '%' . $q . '%');
                        }
                    }
                });
            }
        }

        $records = $records->get();

        $list = array();
        foreach($records as $record) {
            $list[] = json_encode(array(
                'name' => Functions::firstNameLastName($record->nombre, $record->apellido),
                '_id' => $record->usuario_id
            ));
        }

        return '[' . implode(',', $list) . ']';
    }

}