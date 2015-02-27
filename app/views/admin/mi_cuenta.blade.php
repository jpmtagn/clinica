@extends('layouts.admin')

@section('titulo')
Panel de Administración
@stop

@section('cabecera')
{{-- HTML::style('js/dropzone/dropzone.min.css') --}}
{{ HTML::style('js/pickadate/themes/default.css') }}
{{ HTML::style('js/pickadate/themes/default.date.css') }}
{{ HTML::style('js/select2/select2.min.custom.css') }}

<style type="text/css">
	body.droppable .profile-avatar-wrap {
	  border: 5px dashed lightblue;
	  z-index: 9999;
	}
	.profile-avatar-wrap {
	  width: 256px/*33.33%*/;
	  float: left;
	  margin: 0 20px 5px 0;
	  position: relative;
	  pointer-events: none;
	  border: 5px solid transparent;
	}
	.profile-avatar-wrap:after {
	  /* Drag Prevention */
	  content: "";
	  position: absolute;
	  top: 0;
	  left: 0;
	  width: 100%;
	  height: 100%;
	}
	.profile-avatar-wrap img {
	  width: 100%;
	  display: block;
	}
</style>
@stop

@section('contenido')
<?php 
	$frm = new AForm;
	if (isset($field_values) && is_array($field_values)) {
		$frm->setValues( $field_values );
		$record_id = $field_values['id'];
	}
	else {
		$record_id = '';
	}
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
                <li>{{ Lang::get('usuarios.my_account') }}</li>
            </ul>
            <!-- /BREADCRUMBS -->
            <!-- HEAD -->
            {{-- $frm->header('Personas', $total, 'fa-wheelchair') --}}
            <!-- /HEAD -->
        </div>
    </div>
</div>
<!-- /PAGE HEADER -->

<!-- MAIN CONTENT -->
<div class="row">
    <div class="col-sm-12">

        <!-- EDIT -->
        {{ $frm->panelOpen('edit', 'Modificar', 'fa-pencil', 'orange', array('collapse','remove')) }}
        <form id="frm_data_edit_profile" class="form-horizontal" role="form" enctype="multipart/form-data" method="post" action="{{ URL::route('admin_pacientes_editar_post') }}">
	        <div class="row">
	        	<div class="col-md-8">
		            {{ $frm->id( $record_id ) }}
		            
		            {{ $frm->text('nombre', null, Lang::get('pacientes.name'), "", true) }}
		            {{ $frm->text('apellido', null, Lang::get('pacientes.lastname'), "", true) }}
		            {{ $frm->text('dni', null, Lang::get('pacientes.dni'), "", true, array('[vejVEJ]{1}-{1}[0-9]{7,9}', 'Ej. V-123456789')); }}
		            {{ $frm->date('fecha_nacimiento', null, Lang::get('pacientes.birthdate')) }}
		            {{ $frm->select('sexo', null, Lang::get('pacientes.gender'), $genders) }}
		            {{ $frm->select('estado_civil', null, Lang::get('pacientes.marital_status'), $marital_statuses) }}
		            {{ $frm->text('direccion', null, Lang::get('pacientes.address')) }}
		            {{ $frm->tagSelect('telefonos', null, Lang::get('pacientes.phone')) }}
		            {{ $frm->tagSelect('correos', null, Lang::get('pacientes.email')) }}
		            {{ $frm->hidden('telefonos_check', 'telefonos_check', "", $field_values['telefonos']) }}
		            {{ $frm->hidden('correos_check', 'correos_check', "", $field_values['correos']) }}

		            {{ Form::token() }}
			    </div>
			    <div class="col-md-4 text-center">
					<div class="profile-avatar-wrap">
						@if (!empty($field_values['avatar']))
						<img src="{{ URL::asset('img/avatars/s/' . $field_values['avatar']) }}" id="profile-avatar" alt="">
						@else
						<img src="{{ URL::asset('img/avatars/s/default.jpg') }}" id="profile-avatar" alt="">
						@endif
					</div>
					<div class="clearfix">
						<input type="hidden" name="MAX_FILE_SIZE" value="500000" />
						<input type="file" id="avatar" name="avatar">
					</div>
				</div>
			</div>
			<div class="row">
	        	<div class="col-md-8">
	        		<br>
					{{ $frm->submit('Guardar', 'btn-warning') }}
				</div>
			</div>
        </form>
        <!--form id="frm_data_get" method="get" action="{{ URL::route('admin_pacientes_datos_get') }}">
            {{-- Form::token() --}}
        </form-->
        {{ $frm->panelClose() }}

    </div>
</div>

<!-- /MAIN CONTENT -->
@stop

@section('scripts')
{{-- HTML::script('js/dropzone/dropzone.min.js') --}}
{{ HTML::script('js/avatar/resample.js') }}
{{ HTML::script('js/avatar/avatar.js') }}
{{ HTML::script('js/select2/select2.js') }}
{{ HTML::script('js/pickadate/picker.js') }}
{{ HTML::script('js/pickadate/picker.date.js') }}
<?php if (Config::get('app.locale') != 'en') : ?>
{{ HTML::script('js/select2/select2_locale_' . Config::get('app.locale') . '.js') }}
{{ HTML::script('js/pickadate/translations/' . Config::get('app.locale') . '.js') }}
<?php endif; ?>
{{ HTML::script('js/bootstrap-inputmask/bootstrap-inputmask.min.js') }}
{{ HTML::script('js/panel.js') }}
<script>
    //var url_update_counter = "{{ URL::route('admin_pacientes_count_get') }}";

    function afterUpdatingRecords() {

    }

    function beforePanelCreate() {

    }

    //Dropzone.autoDiscover = false;

    $(document).ready(function() {
        App.init('{{ Config::get('app.locale') }}'); //Initialise plugins and elements

        /*$('#my_photo_dropzone').dropzone({
        	url: '{{ URL::route('admin_pacientes_editar_post') }}',
        	dictDefaultMessage: 'Arrastra aquí tu foto'
        });*/
	    /*var myDropzone = new Dropzone("#my_photo_dropzone");
		myDropzone.on("addedfile", function(file) {
			// Maybe display some more file information on your page
		});*/

        {{ $frm->script() }}

    });
</script>
@stop