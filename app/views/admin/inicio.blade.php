@extends('layouts.admin')

@section('titulo')
    Panel de Administración
@stop

@section('contenido')
<!-- PAGE HEADER-->
<div class="row">
    <div class="col-sm-12">
        <div class="page-header">
            <!-- BREADCRUMBS -->
            <ul class="breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a href="index.html">Home</a>
                </li>
                <li>Inicio</li>
            </ul>
            <!-- /BREADCRUMBS -->
            <div class="clearfix">
                <h3 class="content-title pull-left">Inicio</h3>
            </div>
            <div class="description">Información general y estadísticas</div>
        </div>
    </div>
</div>
<!-- /PAGE HEADER -->
@stop

@section('scripts')
<script>
    jQuery(document).ready(function() {
        //App.setPage("index");  //Set current page
        App.init(); //Initialise plugins and elements
    });
</script>
@stop