@extends('layouts.admin')

@section('titulo')
Panel de Administración
@stop

@section('cabecera')
{{ HTML::style('js/select2/select2.min.custom.css') }}
{{-- HTML::style('js/bootstrap-datepicker/css/datepicker.css') --}}
{{ HTML::style('js/pickadate/themes/default.css') }}
{{ HTML::style('js/pickadate/themes/default.date.css') }}
{{ HTML::style('js/pickadate/themes/default.time.css') }}
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
                <li>Citas</li>
            </ul>
            <!-- /BREADCRUMBS -->
            <!-- HEAD -->
            {{ $frm->header('Citas', $total, 'fa-calendar-o') }}
            <!-- /HEAD -->
        </div>
    </div>
</div>
<!-- /PAGE HEADER -->

<!-- MAIN CONTENT -->
<div class="row">
    <div class="col-sm-12">

        <!-- SEARCH -->
        {{ $frm->panelOpen('search', 'Buscar', 'fa-search', '', array('collapse')) }}
        <form id="frm_data_search" class="form-horizontal" role="form" action="{{ URL::route('admin_citas_buscar_get') }}">
            {{-- $frm->search('search', 'search', 'Ingrese su búsqueda') --}}
            {{ $frm->date('search', null, Lang::get('citas.date'), 'day') }}
            {{ $frm->remoteSelect('buscar_doctor_id', null, Lang::get('citas.doctor'), URL::route('admin_doctores_list')) }}
            {{ $frm->remoteSelect('buscar_paciente_id', null, Lang::get('citas.patient'), URL::route('admin_pacientes_list')) }}
            <div class="vertical-spaced">
            {{ $frm->submit('<i class="fa fa-search"></i>&nbsp;' . Lang::get('global.search')) }}
            </div>

            {{ $frm->hidden('search_query', null, 'search-query') }}
            {{ $frm->hidden('search_page', null, 'search-page') }}
            {{ Form::token() }}
        </form>
        <br>
        <form id="frm_data_results" role="form" action="{{ URL::route('admin_citas_info_get') }}">
            <div class="search-results-holder">

            </div>
            {{ Form::token() }}
        </form>
        {{ $frm->panelClose() }}


        <!-- VIEW -->
        {{ $frm->panelOpen('view', 'Información', 'fa-info-circle', 'blue hidden', array('refresh','collapse','remove')) }}
        <form id="frm_data_view" class="form-horizontal" role="form" method="post" action="{{ URL::route('admin_citas_accion_post') }}">
            <div class="content">

            </div>

            {{ Form::token() }}
        </form>
        <form id="frm_info_get" method="get" action="{{ URL::route('admin_citas_info_get') }}">
            {{ Form::token() }}
        </form>
        {{ $frm->panelClose() }}


        <!-- CREATE NEW -->
        {{ $frm->panelOpen('create', 'Nuevo', 'fa-plus', 'primary hidden', array('collapse','remove')) }}
        <form id="frm_data_new" class="form-horizontal" role="form" method="post" action="{{ URL::route('admin_citas_registrar_post') }}">
            {{ $frm->date('fecha', null, Lang::get('citas.date'), 'day') }}
            {{ $frm->time('hora_inicio', null, Lang::get('citas.time_start')) }}
            {{ $frm->time('hora_fin', null, Lang::get('citas.time_end')) }}
            {{ $frm->remoteSelect('doctor_id', null, Lang::get('citas.doctor'), URL::route('admin_doctores_list')) }}
            {{ $frm->remoteSelect('paciente_id', null, Lang::get('citas.patient'), URL::route('admin_pacientes_list')) }}
            <br><br><br>
            {{ Form::token() }}
            {{ $frm->submit('Guardar') }}
        </form>
        {{ $frm->panelClose() }}


        <!-- EDIT -->
        {{ $frm->panelOpen('edit', 'Modificar', 'fa-pencil', 'orange hidden', array('collapse','remove')) }}
        <form id="frm_data_edit" class="form-horizontal" role="form" action="{{ URL::route('admin_citas_editar_post') }}">
            {{ $frm->id() }}
            {{ $frm->date('fecha', null, Lang::get('citas.date'), 'day') }}
            {{ $frm->time('hora_inicio', null, Lang::get('citas.time_start')) }}
            {{ $frm->time('hora_fin', null, Lang::get('citas.time_end')) }}
            {{ $frm->remoteSelect('doctor_id', null, Lang::get('citas.doctor'), URL::route('admin_doctores_list')) }}
            {{ $frm->remoteSelect('paciente_id', null, Lang::get('citas.patient'), URL::route('admin_pacientes_list')) }}

            {{ Form::token() }}
            {{ $frm->submit('Guardar', 'btn-warning') }}
        </form>
        <form id="frm_data_get" method="get" action="{{ URL::route('admin_citas_datos_get') }}">
            {{ Form::token() }}
        </form>
        {{ $frm->panelClose() }}

    </div>
</div>

<!-- SAMPLE BOX CONFIGURATION MODAL FORM-->
<div class="modal fade" id="box-config-search" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Configuración de búsqueda</h4>
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
{{-- HTML::script('js/bootstrap-datepicker/js/bootstrap-datepicker.js') --}}
{{ HTML::script('js/pickadate/picker.js') }}
{{ HTML::script('js/pickadate/picker.date.js') }}
{{ HTML::script('js/pickadate/picker.time.js') }}
{{ HTML::script('js/bootstrap-inputmask/bootstrap-inputmask.min.js') }}
<?php if (Config::get('app.locale') != 'en') : ?>
    {{ HTML::script('js/select2/select2_locale_' . Config::get('app.locale') . '.js') }}
    {{ HTML::script('js/pickadate/translations/' . Config::get('app.locale') . '.js') }}
<?php endif; ?>
{{ HTML::script('js/jquery-knob/js/jquery.knob.js') }}
{{ HTML::script('js/panel.js') }}
<script>
    var url_update_counter = "{{ URL::route('admin_citas_count_get') }}";

    $(document).ready(function() {
        App.init('{{ Config::get('app.locale') }}'); //Initialise plugins and elements

        {{ $frm->script() }}

    });
</script>
@stop