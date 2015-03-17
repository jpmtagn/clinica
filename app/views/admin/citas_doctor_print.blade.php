@extends('layouts.print')

@section('titulo')
    Citas de {{ $doctor_name }} para {{ $date }}
@stop

@section('cabecera')
    <style type="text/css">
        
    </style>
@stop

@section('contenido')
<div class="row">
    <div class="col-md-12">
        <h4>{{ $doctor_name }} - {{ $date }}</h4>
    </div>
</div>
<div class="row">
    <div id="citas" class="col-md-12">
        {{ $citas }}
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">

    jQuery(document).ready(function() {
        
    });
</script>
@stop