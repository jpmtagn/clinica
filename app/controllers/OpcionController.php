<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 03/12/15
 * Time: 03:09 PM
 */

class OpcionController extends BaseController {

	public function paginaAdminOpciones() {
		if (Auth::user()->admin) {
            $days = array(
            	1 => Lang::get('global.mon_l'),
            	2 => Lang::get('global.tue_l'),
            	3 => Lang::get('global.wed_l'),
            	4 => Lang::get('global.thu_l'),
            	5 => Lang::get('global.fri_l'),
            	6 => Lang::get('global.sat_l'),
            	0 => Lang::get('global.sun_l')
        	);
        	$field_values = Opcion::load();
            return View::make('admin.opciones')->with(
                array(
                    'active_menu' => 'opcion',
                    'days' => $days,
					'field_values' => $field_values
                )
            );
        }

        return View::make('admin.inicio');
	}

	public function save() {
		$validator = Validator::make(Input::all(),
            Opcion::getValidationRules()
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