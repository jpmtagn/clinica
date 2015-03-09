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


Route::pattern('doctor_id', '[0-9]+');


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

    Route::get('admin/consultorios', array(
        'as' => 'admin_consultorios',
        'uses' => 'ConsultorioController@paginaAdmin'
    ));

    Route::get('admin/servicios', array(
        'as' => 'admin_servicios',
        'uses' => 'ServicioController@paginaAdmin'
    ));

    Route::get('admin/equipos', array(
        'as' => 'admin_equipos',
        'uses' => 'EquipoController@paginaAdmin'
    ));

    Route::get('admin/disponibilidad/{doctor_id}', array(
        'as' => 'disponibilidad_doctor',
        'uses' => 'DisponibilidadController@paginaAdminDisponibilidad'
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

    Route::get('admin/consultorio/total', array(
        'as' => 'admin_consultorio_count_get',
        'uses' => 'ConsultorioController@totalGet'
    ));

    Route::get('admin/servicio/total', array(
        'as' => 'admin_servicio_count_get',
        'uses' => 'ServicioController@totalGet'
    ));

    Route::get('admin/equipo/total', array(
        'as' => 'admin_equipo_count_get',
        'uses' => 'EquipoController@totalGet'
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

    Route::get('admin/consultorios/list', array(
        'as' => 'admin_consultorios_list',
        'uses' => 'ConsultorioController@listSeek'
    ));

    Route::get('admin/servicios/list', array(
        'as' => 'admin_servicios_list',
        'uses' => 'ServicioController@listSeek'
    ));

    //calendar
    Route::get('calendario/citas', array(
        'as' => 'calendar_source',
        'uses' => 'CitaController@getCitas'
    ));

    Route::get('calendario_disponibilidad/{doctor_id}', array(
        'as' => 'disponibilidad_calendar_source',
        'uses' => 'DisponibilidadController@getDisponibilidad'
    ));

    //information requests
    Route::get('cita/dia_hora', array(
        'as' => 'cita_datetime_inf_get',
        'uses' => 'CitaController@getInfoDateTime'
    ));

    Route::get('cita/doctor', array(
        'as' => 'cita_doctor_inf_get',
        'uses' => 'CitaController@getInfoDoctor'
    ));

    Route::get('cita/paciente', array(
        'as' => 'cita_patient_inf_get',
        'uses' => 'CitaController@getInfoPatient'
    ));

    Route::get('cita/servicio', array(
        'as' => 'cita_service_inf_get',
        'uses' => 'CitaController@getInfoService'
    ));

    Route::get('cita/consultorio', array(
        'as' => 'cita_office_inf_get',
        'uses' => 'CitaController@getInfoOffice'
    ));

    Route::get('cita/info', array(
        'as' => 'cita_all_inf_get',
        'uses' => 'CitaController@getAllInfo'
    ));

    Route::get('cita/consultorio_disponible', array(
        'as' => 'get_available_offices',
        'uses' => 'CitaController@getAvailableOffice'
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

        //cambiar contraseña
        Route::post('admin/mi_cuenta/cambiar_password', array(
            'as' => 'change_password_post',
            'uses' => 'UserController@changePasswordPost'
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

        //validacion
        Route::post('admin/citas/chequear', array(
            'as' => 'admin_citas_check_availability_post',
            'uses' => 'CitaController@checkAvailabilityPost'
        ));


        // PAGINA AREAS

        //buscar
        Route::get('admin/areas/buscar', array(
            'as' => 'admin_areas_buscar_get',
            'uses' => 'AreaController@buscarGet'
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

        
        // PAGINA CONSULTORIOS

        //buscar
        Route::get('admin/consultorio/buscar', array(
            'as' => 'admin_consultorio_buscar_get',
            'uses' => 'ConsultorioController@buscarGet'
        ));

        //info
        Route::get('admin/consultorio/info', array(
            'as' => 'admin_consultorio_info_get',
            'uses' => 'ConsultorioController@infoGet'
        ));

        //datos para editar
        Route::get('admin/consultorio/datos', array(
            'as' => 'admin_consultorio_datos_get',
            'uses' => 'ConsultorioController@datosGet'
        ));

        //acciones (eliminar)
        Route::post('admin/consultorio/accion', array(
            'as' => 'admin_consultorio_accion_post',
            'uses' => 'ConsultorioController@accionPost'
        ));

        //editar
        Route::post('admin/consultorio/editar', array(
            'as' => 'admin_consultorio_editar_post',
            'uses' => 'ConsultorioController@editarPost'
        ));

        //registrar
        Route::post('admin/consultorio/registrar', array(
            'as' => 'admin_consultorio_registrar_post',
            'uses' => 'ConsultorioController@registrarPost'
        ));


        // PAGINA SERVICIOS

        //buscar
        Route::get('admin/servicio/buscar', array(
            'as' => 'admin_servicio_buscar_get',
            'uses' => 'ServicioController@buscarGet'
        ));

        //info
        Route::get('admin/servicio/info', array(
            'as' => 'admin_servicio_info_get',
            'uses' => 'ServicioController@infoGet'
        ));

        //datos para editar
        Route::get('admin/servicio/datos', array(
            'as' => 'admin_servicio_datos_get',
            'uses' => 'ServicioController@datosGet'
        ));

        //acciones (eliminar)
        Route::post('admin/servicio/accion', array(
            'as' => 'admin_servicio_accion_post',
            'uses' => 'ServicioController@accionPost'
        ));

        //editar
        Route::post('admin/servicio/editar', array(
            'as' => 'admin_servicio_editar_post',
            'uses' => 'ServicioController@editarPost'
        ));

        //registrar
        Route::post('admin/servicio/registrar', array(
            'as' => 'admin_servicio_registrar_post',
            'uses' => 'ServicioController@registrarPost'
        ));


        // PAGINA EQUIPOS

        //buscar
        Route::get('admin/equipo/buscar', array(
            'as' => 'admin_equipo_buscar_get',
            'uses' => 'EquipoController@buscarGet'
        ));

        //info
        Route::get('admin/equipo/info', array(
            'as' => 'admin_equipo_info_get',
            'uses' => 'EquipoController@infoGet'
        ));

        //datos para editar
        Route::get('admin/equipo/datos', array(
            'as' => 'admin_equipo_datos_get',
            'uses' => 'EquipoController@datosGet'
        ));

        //acciones (eliminar)
        Route::post('admin/equipo/accion', array(
            'as' => 'admin_equipo_accion_post',
            'uses' => 'EquipoController@accionPost'
        ));

        //editar
        Route::post('admin/equipo/editar', array(
            'as' => 'admin_equipo_editar_post',
            'uses' => 'EquipoController@editarPost'
        ));

        //registrar
        Route::post('admin/equipo/registrar', array(
            'as' => 'admin_equipo_registrar_post',
            'uses' => 'EquipoController@registrarPost'
        ));


        // PAGINA CALENDARIO

        //acciones
        Route::post('admin/cita/accion', array(
            'as' => 'cita_actions_post',
            'uses' => 'CitaController@calendarActionPost'
        ));


        // PAGINA DISPONIBILIDAD

        //editar
        Route::post('admin/disponibilidad/editar', array(
            'as' => 'admin_disponibilidad_editar_post',
            'uses' => 'DisponibilidadController@editarPost'
        ));

        //acciones
        Route::post('admin/disponibilidad/accion', array(
            'as' => 'disponibilidad_actions_post',
            'uses' => 'DisponibilidadController@calendarActionPost'
        ));

    });

    Route::get('cerrar_sesion', array(
        'as' => 'cerrar_sesion',
        'uses' => 'UserController@cerrarSesion'
    ));

});