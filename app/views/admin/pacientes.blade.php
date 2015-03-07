@extends('layouts.admin')

@section('titulo')
Panel de Administración
@stop

@section('cabecera')
{{ HTML::style('js/select2/select2.min.custom.css') }}
{{-- HTML::style('js/bootstrap-datepicker/css/datepicker.css') --}}
{{ HTML::style('js/pickadate/themes/default.css') }}
{{ HTML::style('js/pickadate/themes/default.date.css') }}
@stop

@section('contenido')
<?php $frm = new AForm; ?>
<!-- PAGE HEADER-->
<div class="row">
    <div class="col-sm-12">
        <div class="page-header">
            <!-- BREADCRUMBS -->
            <ul class="breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a href="{{ URL::route('admin_inicio') }}">{{ Lang::get('global.home') }}</a>
                </li>
                <li>Pacientes</li>
            </ul>
            <!-- /BREADCRUMBS -->
            <!-- HEAD -->
            {{ $frm->header('Personas', $total, 'fa-users') }}
            <!-- /HEAD -->
        </div>
    </div>
</div>
<!-- /PAGE HEADER -->

<!-- MAIN CONTENT -->
<div class="row">
    <div class="col-sm-12">

        <!-- SEARCH -->
        {{ $frm->panelOpen('search', 'Buscar', 'fa-search', '', array('refresh','collapse')) }}
        <form id="frm_data_search" role="form" action="{{ URL::route('admin_pacientes_buscar_get') }}">
            {{ $frm->search('search', 'search', 'Ingrese su búsqueda') }}
            {{ $frm->hidden('search_query', null, 'search-query') }}
            {{ $frm->hidden('search_page', null, 'search-page') }}
            {{ Form::token() }}
        </form>
        <br>
        <form id="frm_data_results" role="form" action="{{ URL::route('admin_pacientes_info_get') }}">
            <div class="search-results-holder">
                <!--div class="list-group search-results">

                </div-->
            </div>
            {{ Form::token() }}
        </form>
        {{ $frm->panelClose() }}


        <!-- VIEW -->
        {{ $frm->panelOpen('view', 'Información', 'fa-info-circle', 'blue hidden', array('refresh','collapse','remove')) }}
        <form id="frm_data_view" class="form-horizontal" role="form" method="post" action="{{ URL::route('admin_pacientes_accion_post') }}">
            <div class="content">

            </div>

            {{ Form::token() }}
        </form>
        <form id="frm_info_get" method="get" action="{{ URL::route('admin_pacientes_info_get') }}">
            {{ Form::token() }}
        </form>
        {{ $frm->panelClose() }}


        <!-- CREATE NEW -->
        {{ $frm->panelOpen('create', 'Nuevo', 'fa-plus', 'primary hidden', array('collapse','remove')) }}
        <form id="frm_data_new" class="form-horizontal" role="form" method="post" action="{{ URL::route('admin_pacientes_registrar_post') }}">
            {{ $frm->text('nombre', null, Lang::get('pacientes.name'), "", true) }}
            {{ $frm->text('apellido', null, Lang::get('pacientes.lastname'), "", true) }}
            {{ $frm->text('dni', null, Lang::get('pacientes.dni'), "", true, array('[vejVEJ]{1}-{1}[0-9]{7,9}', 'Ej. V-123456789')); }}
            {{ $frm->date('fecha_nacimiento', null, Lang::get('pacientes.birthdate')) }}
            {{ $frm->select('sexo', null, Lang::get('pacientes.gender'), $genders) }}
            {{ $frm->select('estado_civil', null, Lang::get('pacientes.marital_status'), $marital_statuses) }}
            {{ $frm->text('direccion', null, Lang::get('pacientes.address')) }}
            {{ $frm->tagSelect('telefonos', null, Lang::get('pacientes.phone')) }}
            {{ $frm->tagSelect('correos', null, Lang::get('pacientes.email')) }}
            {{ $frm->remoteSelect('usuario_id', null, Lang::get('pacientes.user'), URL::route('admin_usuarios_list')) }}
            <br><br><br>
            {{ Form::token() }}
            {{ $frm->submit('Guardar') }}
        </form>
        {{ $frm->panelClose() }}

        <!-- CREATE NEW RELATIVE -->
        {{ $frm->panelOpen('create_relative', 'Nuevo Pariente', 'fa-plus', 'primary hidden', array('collapse','remove')) }}
        <form id="frm_data_new_relative" class="form-horizontal" role="form" method="post" action="{{ URL::route('admin_pacientes_registrar_pariente_post') }}">
            {{ $frm->remoteSelect('pariente_id', null, Lang::get('global.search'), URL::route('admin_pacientes_list')) }}
            {{-- $frm->button('btn_add_new_pariente', Lang::get('pacientes.add_new_relative'), 'fa-plus') --}}
            {{ $frm->hidden('tipo_pariente_id') }}
            {{ $frm->hidden('paciente_id') }}

            {{ Form::token() }}
            {{ $frm->submit('Guardar') }}
        </form>
        {{ $frm->panelClose() }}

        <!-- EDIT -->
        {{ $frm->panelOpen('edit', 'Modificar', 'fa-pencil', 'orange hidden', array('collapse','remove')) }}
        <form id="frm_data_edit" class="form-horizontal" role="form" action="{{ URL::route('admin_pacientes_editar_post') }}">
            {{ $frm->id() }}

            {{ $frm->text('nombre', null, Lang::get('pacientes.name'), "", true) }}
            {{ $frm->text('apellido', null, Lang::get('pacientes.lastname'), "", true) }}
            {{ $frm->text('dni', null, Lang::get('pacientes.dni'), "", true, array('[vejVEJ]{1}-{1}[0-9]{7,9}', 'Ej. V-123456789')); }}
            {{ $frm->date('fecha_nacimiento', null, Lang::get('pacientes.birthdate')) }}
            {{ $frm->select('sexo', null, Lang::get('pacientes.gender'), $genders) }}
            {{ $frm->select('estado_civil', null, Lang::get('pacientes.marital_status'), $marital_statuses) }}
            {{ $frm->text('direccion', null, Lang::get('pacientes.address')) }}
            {{ $frm->tagSelect('telefonos', null, Lang::get('pacientes.phone')) }}
            {{ $frm->tagSelect('correos', null, Lang::get('pacientes.email')) }}
            {{ $frm->remoteSelect('usuario_id', null, Lang::get('pacientes.user'), URL::route('admin_usuarios_list')) }}

            {{ Form::token() }}
            {{ $frm->submit('Guardar', 'btn-warning') }}
        </form>
        <form id="frm_data_get" method="get" action="{{ URL::route('admin_pacientes_datos_get') }}">
            {{ Form::token() }}
        </form>
        {{ $frm->panelClose() }}

    </div>
</div>

<!-- BOX MODAL FORM-->
<div class="modal fade" id="box-add-relative" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{{ Lang::get('pacientes.') }}</h4>
            </div>
            <div class="modal-body">
                Here goes box setting content.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">OK</button>
            </div>
        </div>
    </div>
</div>
<!-- /SAMPLE BOX CONFIGURATION MODAL FORM-->

<!-- /MAIN CONTENT -->
@stop

@section('scripts')
{{ HTML::script('js/select2/select2.js') }}
{{ HTML::script('js/pickadate/picker.js') }}
{{ HTML::script('js/pickadate/picker.date.js') }}
<?php if (Config::get('app.locale') != 'en') : ?>
{{ HTML::script('js/select2/select2_locale_' . Config::get('app.locale') . '.js') }}
{{ HTML::script('js/pickadate/translations/' . Config::get('app.locale') . '.js') }}
<?php endif; ?>
{{-- HTML::script('js/bootstrap-datepicker/js/bootstrap-datepicker.js') --}}
{{ HTML::script('js/bootstrap-inputmask/bootstrap-inputmask.min.js') }}
{{ HTML::script('js/panel.js') }}
<script>
    var url_update_counter = "{{ URL::route('admin_pacientes_count_get') }}";

    function afterUpdatingRecords() {

    }

    function dropDown_addRelative($a) {
        var $panel = $('#create_relative_panel');
        var tipo_pariente_id = $a.attr('menu-action');
        var paciente_id = Panel.view.form().find('input[name=id]').val();
        console.log('youve chosen type: ' + tipo_pariente_id + ' and patient id: ' + paciente_id);

        Panel.resetForm( $panel.find('form').eq(0) );

        $frm = $('#frm_data_new_relative');
        $frm.find('#tipo_pariente_id').val( tipo_pariente_id );
        $frm.find('#paciente_id').val( paciente_id );

        Panel.status.restore( $panel, 'Nuevo Pariente (' + $a.html() + ')', 'fa-plus');
        Panel.show( $panel );
    }

    function beforePanelCreate() {

    }

    function saveRelative() {
        var $panel = $('#create_relative_panel');
        var $frm = $('#frm_data_new_relative');
        var url = $frm.attr('action');
        Panel.status.saving($panel);
        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            data: $frm.serialize() // serializes the form's elements.
        }).done(function(data) {
            //console.log(data);
            if (data['ok']) {
                Panel.status.saved($panel);
            }
            else {
                Panel.status.error($panel, data['err']);
            }
        }).fail(function(data) {
            console.log(data); //failed
            Panel.status.error($panel, data);
        });
    }

    $(document).ready(function() {
        App.init('{{ Config::get('app.locale') }}'); //Initialise plugins and elements

        {{ $frm->script() }}

        $('#frm_data_new_relative').submit(function(e) {
            setTimeout(saveRelative(), 100);
            e.preventDefault();
            return false;
        });

    });
</script>
@stop