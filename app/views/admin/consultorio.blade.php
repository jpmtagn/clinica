@extends('layouts.admin')

@section('titulo')
Panel de Administración
@stop

@section('cabecera')
{{ HTML::style('js/select2/select2.min.custom.css') }}
@stop

@section('contenido')
<?php
    $frm = new AForm;
    $key = 'consultorio';
?>
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
                <li>{{ Lang::get($key . '.title_plural') }}</li>
            </ul>
            <!-- /BREADCRUMBS -->
            <!-- HEAD -->
            {{ $frm->header(Lang::get($key . '.title_plural'), $total, 'fa-cubes') }}
            <!-- /HEAD -->
        </div>
    </div>
</div>
<!-- /PAGE HEADER -->

<!-- MAIN CONTENT -->
<div class="row">
    <div class="col-sm-12">

        <!-- SEARCH -->
        {{ $frm->panelOpen('search', Lang::get('global.search'), 'fa-search', '', array('refresh','collapse')) }}
        <form id="frm_data_search" class="form-horizontal" role="form" action="{{ URL::route('admin_' . $key . '_buscar_get') }}">
            {{ $frm->search('search', 'search', Lang::get('global.insert_search')) }}

            {{ $frm->hidden('search_query', null, 'search-query') }}
            {{ $frm->hidden('search_page', null, 'search-page') }}
            {{ Form::token() }}
        </form>
        <br>
        <form id="frm_data_results" role="form" action="{{ URL::route('admin_' . $key . '_info_get') }}">
            <div class="search-results-holder">

            </div>
            {{ Form::token() }}
        </form>
        {{ $frm->panelClose() }}


        <!-- VIEW -->
        {{ $frm->panelOpen('view', Lang::get('global.inf'), 'fa-info-circle', 'blue hidden', array('refresh','collapse','remove')) }}
        <form id="frm_data_view" class="form-horizontal" role="form" method="post" action="{{ URL::route('admin_' . $key . '_accion_post') }}">
            <div class="content">

            </div>

            {{ Form::token() }}
        </form>
        <form id="frm_info_get" method="get" action="{{ URL::route('admin_' . $key . '_info_get') }}">
            {{ Form::token() }}
        </form>
        {{ $frm->panelClose() }}


        <!-- CREATE NEW -->
        {{ $frm->panelOpen('create', Lang::get('global.new'), 'fa-plus', 'primary hidden', array('collapse','remove')) }}
        <form id="frm_data_new" class="form-horizontal" role="form" method="post" action="{{ URL::route('admin_' . $key . '_registrar_post') }}">
            {{ $frm->text('nombre', null, Lang::get('consultorio.name'), '', true) }}
            {{ $frm->text('descripcion', null, Lang::get('consultorio.description')) }}
            {{ $frm->number('capacidad', null, Lang::get('consultorio.capacity')) }}
            {{ $frm->select('area_id', null, Lang::get('area.title_single'), $areas) }}
            <br>
            {{ Form::token() }}
            {{ $frm->submit() }}
        </form>
        {{ $frm->panelClose() }}


        <!-- EDIT -->
        {{ $frm->panelOpen('edit', Lang::get('global.modify'), 'fa-pencil', 'orange hidden', array('collapse','remove')) }}
        <form id="frm_data_edit" class="form-horizontal" role="form" action="{{ URL::route('admin_' . $key . '_editar_post') }}">
            {{ $frm->id() }}
            {{ $frm->text('nombre', null, Lang::get('consultorio.name'), '', true) }}
            {{ $frm->text('descripcion', null, Lang::get('consultorio.description')) }}
            {{ $frm->number('capacidad', null, Lang::get('consultorio.capacity')) }}
            {{ $frm->select('area_id', null, Lang::get('area.title_single'), $areas) }}

            {{ Form::token() }}
            {{ $frm->submit(null, 'btn-warning') }}
        </form>
        <form id="frm_data_get" method="get" action="{{ URL::route('admin_' . $key . '_datos_get') }}">
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
<?php if (Config::get('app.locale') != 'en') : ?>
    {{ HTML::script('js/select2/select2_locale_' . Config::get('app.locale') . '.js') }}
<?php endif; ?>
{{ HTML::script('js/panel.js') }}
<script>
    var url_update_counter = "{{ URL::route('admin_' . $key . '_count_get') }}";

    $(document).ready(function() {
        App.init('{{ Config::get('app.locale') }}'); //Initialise plugins and elements

        {{ $frm->script() }}

    });
</script>
@stop