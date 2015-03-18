<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 24/01/15
 * Time: 02:30 PM
 */

class Doctor /*extends Paciente*/ {

    public static function getAll() {
        /*$role = Rol::find(1); //doctors

        $items = $role->users()->with('paciente')->get();

        return $items;*/

        $user = Auth::user();

        if (User::canViewDoctorPage(null)) {
            $items = DB::table('doctor')->get();
        }
        elseif (User::is(User::ROL_DOCTOR)) {
            $items = DB::table('doctor')->where('usuario_id', '=', $user->id)->get();
        }
        else {
            $items = array();
        }
        /*foreach ($items as $item) {
            //$item = $item->paciente;
            var_dump($item->nombre . ' ' . $item->apellido);
        }
        die();*/

        return $items;
    }

    public static function getIds() {
        return DB::table('doctor')->orderBy('usuario_id')->lists('usuario_id');
    }

    public static function getByLetter($letter) {
        return DB::table('doctor')->where('nombre', 'LIKE', $letter . '%')->get();
    }

    public static function getFirstNameLetters() {
        return DB::table('doctor')
                ->select(DB::raw('substr(nombre, 1, 1) AS "inicial"'))
                ->groupBy('inicial')
                ->orderBy('inicial')
                ->lists('inicial');
    }

}