@extends('layouts.admin')

@section('titulo')
Panel de Administración
@stop

@section('cabecera')
{{ HTML::style('js/select2/select2.min.custom.css') }}
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
                <li>Tipos de Parentescos</li>
            </ul>
            <!-- /BREADCRUMBS -->
            <!-- HEAD -->
            {{ $frm->header('Tipos de Parentescos', $total, 'fa-sitemap') }}
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
        <form id="frm_data_search" role="form" action="{{ URL::route('admin_tipos_parentescos_buscar_get') }}">
            {{-- $frm->search('search', 'search', 'Ingrese su búsqueda') --}}
            {{ $frm->hidden('search_query', null, 'search-query', '*') }}
            {{ $frm->hidden('search_page', null, 'search-page') }}
            {{ Form::token() }}
        </form>
        <form id="frm_data_results" role="form" action="{{ URL::route('admin_tipos_parentescos_info_get') }}">
            <div class="search-results-holder">
                <div class="list-group search-results">
                    @if (count($tipos))
                        @foreach ($tipos as $tipo)
                            <a class="list-group-item search-result" data-id="{{ $tipo->id }}">{{ $tipo->parentesco_m . ' / ' . $tipo->parentesco_f }}</a>
                        @endforeach
                    @else
                        <p>No hay registros</p>
                    @endif
                </div>
            </div>
            {{ Form::token() }}
        </form>
        {{ $frm->panelClose() }}


        <!-- VIEW -->
        {{ $frm->panelOpen('view', 'Información', 'fa-info-circle', 'blue hidden', array('refresh','collapse','remove')) }}
        <form id="frm_data_view" class="form-horizontal" role="form" method="post" action="{{ URL::route('admin_tipos_parentescos_accion_post') }}">
            <div class="content">

            </div>

            {{ Form::token() }}
        </form>
        <form id="frm_info_get" method="get" action="{{ URL::route('admin_tipos_parentescos_info_get') }}">
            {{ Form::token() }}
        </form>
        {{ $frm->panelClose() }}


        <!-- CREATE NEW -->
        {{ $frm->panelOpen('create', 'Nuevo', 'fa-plus', 'primary hidden', array('collapse','remove')) }}
        <form id="frm_data_new" class="form-horizontal" role="form" method="post" action="{{ URL::route('admin_tipos_parentescos_registrar_post') }}">
            {{ $frm->text('parentesco_m', 'parentesco_m', Lang::get('pacientes.relationship_m'), '', true) }}
            {{ $frm->text('parentesco_f', 'parentesco_f', Lang::get('pacientes.relationship_f'), '', true) }}
            {{ $frm->remoteSelect('reciproco', null, Lang::get('pacientes.relationship_inverse'), URL::route('admin_tipos_parentescos_list')) }}
            <br><br><br>
            {{ Form::token() }}
            {{ $frm->submit('Guardar') }}
        </form>
        {{ $frm->panelClose() }}


        <!-- EDIT -->
        {{ $frm->panelOpen('edit', 'Modificar', 'fa-pencil', 'orange hidden', array('collapse','remove')) }}
        <form id="frm_data_edit" class="form-horizontal" role="form" action="{{ URL::route('admin_tipos_parentescos_editar_post') }}">
            {{ $frm->id() }}
            {{ $frm->text('parentesco_m', 'parentesco_m', Lang::get('pacientes.relationship_m'), '', true) }}
            {{ $frm->text('parentesco_f', 'parentesco_f', Lang::get('pacientes.relationship_f'), '', true) }}
            {{ $frm->remoteSelect('reciproco', null, Lang::get('pacientes.relationship_inverse'), URL::route('admin_tipos_parentescos_list')) }}

            {{ Form::token() }}
            {{ $frm->submit('Guardar', 'btn-warning') }}
        </form>
        <form id="frm_data_get" method="get" action="{{ URL::route('admin_tipos_parentescos_datos_get') }}">
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
{{ HTML::script('js/panel.js') }}
<script>
    var url_update_counter = "{{ URL::route('admin_tipos_parentescos_count_get') }}";

    function afterUpdatingRecords() {
        Panel.search.find('*');
    }

    $(document).ready(function() {
        App.init(); //Initialise plugins and elements

        {{ $frm->script() }}

        Panel.search.bindResultsClickEvent();

    });
</script>
@stop