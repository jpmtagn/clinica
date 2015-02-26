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

        $items = DB::table('doctor')->get();
        /*foreach ($items as $item) {
            //$item = $item->paciente;
            var_dump($item->nombre . ' ' . $item->apellido);
        }
        die();*/

        return $items;
    }

} 