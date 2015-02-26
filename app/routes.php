<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// Display all SQL executed in Eloquent
//TODO: disable this!! (!)
/*Event::listen('illuminate.query', function($query, $bindings, $times)
{
    $f = fopen('eloquent_query_log.txt', 'a');
    fwrite($f, $query . '[' . implode('|',$bindings) . '](' . $times . ')' . PHP_EOL . PHP_EOL);
    fclose($f);
    //var_dump($query);
});*/



Route::get('/', array(
    'as' => 'inicio',
    'uses' => 'UserController@mostrarDefault' //<-- change this to any other default homepage
));


/**
 * Rutas disponibles cuando el usuario NO ha iniciado sesion
 */
Route::group(array('before' => 'guest'), function() {

    Route::get('inicio_sesion', array(
        'as' => 'inicio_sesion',
        'uses' => 'UserController@mostrarInicioSesion'
    ));

    /*Route::get('user_activate/{code}', array(
        'as' => 'activate_account',
        'uses' => 'AccountController@activateAccount'
    ));*/

    Route::group(array('before' => 'csrf'), function() {

        Route::post('inicio_sesion_post', array(
            'as' => 'inicio_sesion_post',
            'uses' => 'UserController@iniciarSesionPost'
        ));

    });

    /**
     * Esta ruta debe ser desactivada una vez que se crea el primer administrador (!)
     */
    Route::get('crear_admin_defecto', array(
        'as' => 'crear_admin_defecto',
        'uses' => 'UserController@crearAdminDefecto'
    ));

});



/**
 * Rutas disponibles cuando el usuario ha iniciado sesion
 */
Route::group(array('before' => 'auth'), function() {

    /**
     * Main Admin Navigation Bar
     */
    Route::get('admin/inicio', array(
        'as' => 'admin_inicio',
        'uses' => 'UserController@paginaAdminInicio'
    ));

    Route::get('admin/mi_cuenta', array(
        'as' => 'mi_cuenta',
        'uses' => 'PacienteController@paginaMiCuenta'
    ));

    Route::get('admin/usuarios', array(
        'as' => 'admin_usuarios',
        'uses' => 'UserController@paginaAdminUsuarios'
    ));

    Route::get('admin/parentescos', array(
        'as' => 'admin_parentescos',
        'uses' => 'TipoParienteController@paginaAdmin'
    ));

    Route::get('admin/pacientes', array(
        'as' => 'admin_pacientes',
        'uses' => 'PacienteController@paginaAdmin'
    ));

    Route::get('admin/citas', array(
        'as' => 'admin_citas',
        'uses' => 'CitaController@paginaAdmin'
    ));

    Route::get('admin/calendario', array(
        'as' => 'admin_calendario',
        'uses' => 'CitaController@paginaCalendario'
    ));

    Route::get('admin/areas', array(
        'as' => 'admin_areas',
        'uses' => 'AreaController@paginaAdmin'
    ));

    //total registros
    Route::get('admin/usuarios/total', array(
        'as' => 'admin_usuarios_count_get',
        'uses' => 'UserController@totalGet'
    ));

    Route::get('admin/tipos_parentescos/total', array(
        'as' => 'admin_tipos_parentescos_count_get',
        'uses' => 'TipoParienteController@totalGet'
    ));

    Route::get('admin/pacientes/total', array(
        'as' => 'admin_pacientes_count_get',
        'uses' => 'PacienteController@totalGet'
    ));

    Route::get('admin/citas/total', array(
        'as' => 'admin_citas_count_get',
        'uses' => 'CitaController@totalGet'
    ));


    Route::get('admin/areas/total', array(
        'as' => 'admin_areas_count_get',
        'uses' => 'AreaController@totalGet'
    ));

    //select searchs
    Route::get('admin/tipos_parentescos/list', array(
        'as' => 'admin_tipos_parentescos_list',
        'uses' => 'TipoParienteController@listSeekAlt'
    ));

    Route::get('admin/usuarios/list', array(
        'as' => 'admin_usuarios_list',
        'uses' => 'UserController@listSeek'
    ));

    Route::get('admin/pacientes/list', array(
        'as' => 'admin_pacientes_list',
        'uses' => 'PacienteController@listSeekAlt'
    ));

    Route::get('admin/doctores/list', array(
        'as' => 'admin_doctores_list',
        'uses' => 'PacienteController@listSeekDoctor'
    ));

    /**
     * Form Actions
     */
    Route::group(array('before' => 'csrf'), function() {

        // PAGINA USUARIOS

        //buscar
        Route::get('admin/usuarios/buscar', array(
            'as' => 'admin_usuarios_buscar_get',
            'uses' => 'UserController@buscarGet'
        ));

        //info
        Route::get('admin/usuarios/info', array(
            'as' => 'admin_usuarios_info_get',
            'uses' => 'UserController@infoGet'
        ));

        //datos para editar
        Route::get('admin/usuarios/datos', array(
            'as' => 'admin_usuarios_datos_get',
            'uses' => 'UserController@datosGet'
        ));

        //acciones (eliminar)
        Route::post('admin/usuarios/accion', array(
            'as' => 'admin_usuarios_accion_post',
            'uses' => 'UserController@accionPost'
        ));

        //editar
        Route::post('admin/usuarios/editar', array(
            'as' => 'admin_usuarios_editar_post',
            'uses' => 'UserController@editarPost'
        ));

        //registrar
        Route::post('admin/usuarios/registrar', array(
            'as' => 'admin_usuarios_registrar_post',
            'uses' => 'UserController@registrarPost'
        ));

        
        // PAGINA TIPO PARIENTES

        //buscar
        Route::get('admin/tipos_parentescos/buscar', array(
            'as' => 'admin_tipos_parentescos_buscar_get',
            'uses' => 'TipoParienteController@buscarGet'
        ));

        //info
        Route::get('admin/tipos_parentescos/info', array(
            'as' => 'admin_tipos_parentescos_info_get',
            'uses' => 'TipoParienteController@infoGet'
        ));

        //datos para editar
        Route::get('admin/tipos_parentescos/datos', array(
            'as' => 'admin_tipos_parentescos_datos_get',
            'uses' => 'TipoParienteController@datosGet'
        ));

        //acciones (eliminar)
        Route::post('admin/tipos_parentescos/accion', array(
            'as' => 'admin_tipos_parentescos_accion_post',
            'uses' => 'TipoParienteController@accionPost'
        ));

        //editar
        Route::post('admin/tipos_parentescos/editar', array(
            'as' => 'admin_tipos_parentescos_editar_post',
            'uses' => 'TipoParienteController@editarPost'
        ));

        //registrar
        Route::post('admin/tipos_parentescos/registrar', array(
            'as' => 'admin_tipos_parentescos_registrar_post',
            'uses' => 'TipoParienteController@registrarPost'
        ));

        
        // PAGINA PACIENTES

        //buscar
        Route::get('admin/pacientes/buscar', array(
            'as' => 'admin_pacientes_buscar_get',
            'uses' => 'PacienteController@buscarGet'
        ));

        //info
        Route::get('admin/pacientes/info', array(
            'as' => 'admin_pacientes_info_get',
            'uses' => 'PacienteController@infoGet'
        ));

        //datos para editar
        Route::get('admin/pacientes/datos', array(
            'as' => 'admin_pacientes_datos_get',
            'uses' => 'PacienteController@datosGet'
        ));

        //acciones (eliminar)
        Route::post('admin/pacientes/accion', array(
            'as' => 'admin_pacientes_accion_post',
            'uses' => 'PacienteController@accionPost'
        ));

        //editar
        Route::post('admin/pacientes/editar', array(
            'as' => 'admin_pacientes_editar_post',
            'uses' => 'PacienteController@editarPost'
        ));

        //registrar
        Route::post('admin/pacientes/registrar', array(
            'as' => 'admin_pacientes_registrar_post',
            'uses' => 'PacienteController@registrarPost'
        ));

        //registrar pariente
        Route::post('admin/pacientes/registrar_pariente', array(
            'as' => 'admin_pacientes_registrar_pariente_post',
            'uses' => 'PacienteController@registrarParientePost'
        ));

        
        // PAGINA CITAS

        //buscar
        Route::get('admin/citas/buscar', array(
            'as' => 'admin_citas_buscar_get',
            'uses' => 'CitaController@buscarGetAlt'
        ));

        //info
        Route::get('admin/citas/info', array(
            'as' => 'admin_citas_info_get',
            'uses' => 'CitaController@infoGet'
        ));

        //datos para editar
        Route::get('admin/citas/datos', array(
            'as' => 'admin_citas_datos_get',
            'uses' => 'CitaController@datosGet'
        ));

        //acciones (eliminar)
        Route::post('admin/citas/accion', array(
            'as' => 'admin_citas_accion_post',
            'uses' => 'CitaController@accionPost'
        ));

        //editar
        Route::post('admin/citas/editar', array(
            'as' => 'admin_citas_editar_post',
            'uses' => 'CitaController@editarPost'
        ));

        //registrar
        Route::post('admin/citas/registrar', array(
            'as' => 'admin_citas_registrar_post',
            'uses' => 'CitaController@registrarPost'
        ));

        
        // PAGINA AREAS

        //buscar
        Route::get('admin/areas/buscar', array(
            'as' => 'admin_areas_buscar_get',
            'uses' => 'AreaController@buscarGetAlt'
        ));

        //info
        Route::get('admin/areas/info', array(
            'as' => 'admin_areas_info_get',
            'uses' => 'AreaController@infoGet'
        ));

        //datos para editar
        Route::get('admin/areas/datos', array(
            'as' => 'admin_areas_datos_get',
            'uses' => 'AreaController@datosGet'
        ));

        //acciones (eliminar)
        Route::post('admin/areas/accion', array(
            'as' => 'admin_areas_accion_post',
            'uses' => 'AreaController@accionPost'
        ));

        //editar
        Route::post('admin/areas/editar', array(
            'as' => 'admin_areas_editar_post',
            'uses' => 'AreaController@editarPost'
        ));

        //registrar
        Route::post('admin/areas/registrar', array(
            'as' => 'admin_areas_registrar_post',
            'uses' => 'AreaController@registrarPost'
        ));

    });

    Route::get('cerrar_sesion', array(
        'as' => 'cerrar_sesion',
        'uses' => 'UserController@cerrarSesion'
    ));

});