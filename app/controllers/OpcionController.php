<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 03/12/15
 * Time: 03:09 PM
 */

class OpcionController extends BaseController {

	public paginaAdminOpciones() {
		if (Auth::user()->admin) {
            $days = array(
            	1 => 'Lunes',
            	2 => 'Martes',
            	3 => 'Mi'
            	4 =>
            	5 =>
            	6 =>
            	7 =>
        	);
            return View::make('admin.equipos')->with(
                array(
                    'active_menu' => 'opcion',
                    'days' => $days
                )
            );
        }
        return View::make('admin.inicio');
	}

	public function save() {
		$validator = Validator::make(Input::all(),
            $model::getValidationRules();
        );

        if ($validator->passes()) {
			Opcion::save( Input::all() );
			return $this->setSuccess( Lang::get('global.saved_msg') );
        }

        return $this->setError( Lang::get('global.wrong_action') );
	}

	public function load() {
		Opcion::load();
	}

}