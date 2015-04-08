@extends('layouts.admin')

@section('titulo')
Calendario
@stop

@section('cabecera')
{{ HTML::style('js/select2/select2.min.custom.css') }}
{{-- HTML::style('js/bootstrap-datepicker/css/datepicker.css') --}}
{{ HTML::style('js/pickadate/themes/default.css') }}
{{ HTML::style('js/pickadate/themes/default.date.css') }}
{{ HTML::style('js/pickadate/themes/default.time.css') }}
{{ HTML::style('js/fullcalendar/fullcalendar.min.css') }}
{{ HTML::style('js/gritter/css/jquery.gritter.css') }}
<style type="text/css">
    body {
        overflow: hidden;
    }
    #content {
        /*background: #fff url({{ URL::asset('img/bg/squairy_light.png') }}) repeat;*/
        background: #fff url({{ URL::asset('img/bg/gray_jean.png') }}) repeat;
    }

    .fc-view-container {
        background-color: #fff;
    }

    .filter-accordion .panel {
        background: none !important;
        border: 0 none !important;
        box-shadow: none !important;
    }

    .filter-accordion h4 a {
        text-decoration: none;
    }

    .filter-accordion .list-group-item:first-child {
        border-radius: 0;
    }

    .bring-to-front {
        z-index: 1000 !important;
    }

    .ampm-separation {
        border-top-style: double !important;
        border-top-width: 4px !important;
    }

    a.fc-event.availability {
        opacity: .3;
        border-radius: 0;
        border: 0;
        z-index: -200 !important;
        pointer-events: none !important;
        width: 100% !important;
    }

    a.fc-event.availability .fc-time {
        display: none;
    }

    @if (!Auth::user()->admin)
    a.fc-event.state3 {
        pointer-events: none !important;
    }
    @endif
</style>
@stop

@section('contenido')
<?php
    $user = Auth::user();
    $frm = new AForm;
?>
<?php if (false) : ?>
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
                <li>Calendario</li>
            </ul>
            <!-- /BREADCRUMBS -->
            <!-- HEAD -->
            {{-- $frm->header('Calendario', null, 'fa-calendar') --}}
            <!-- /HEAD -->
        </div>
    </div>
</div>
<!-- /PAGE HEADER -->
<?php else : ?>
<br>
<?php endif; ?>

<!-- MAIN CONTENT -->
<div class="row">
    <div class="col-sm-12">

        <!-- CALENDAR -->
        {{-- $frm->panelOpen('calendar', Lang::get('citas.calendar'), 'fa-calendar', '', array('collapse')) --}}
        <div class="row">
            <div class="col-md-2">
                @if (User::canViewAllCitas())
                <div class="input-group">
                     <input type="text" value="" class="form-control" placeholder="{{ Lang::get('citas.search_by_dni') }}" id="search_event_query" />
                     <span class="input-group-btn">
                        <a href="#" id="search_event_btn" class="btn btn-default"><i class="fa fa-search"></i></a>
                     </span>
                </div>

                <div class="divide-20"></div>

                <!-- terapeutas -->
                {{ $frm->accordionOpen('filter_accordion') }}
                    {{ $frm->accordionItemOpen(Lang::get('usuarios.doctor')) }}
                        <div class="list-group">
                            @foreach ($doctores as $doctor)
                                <a href="#" class="list-group-item group-filter filter-doctor" attr-id="{{ $doctor->usuario_id }}"> <!-- active -->
                                    @if (!empty($doctor->avatar))
                                        <img class="avatar-thumb" src="{{ URL::asset('img/avatars/s/' . $doctor->avatar) }}" alt="">
                                    @else
                                        <img class="avatar-thumb" src="{{ URL::asset('img/avatars/s/default.jpg') }}" alt="">
                                    @endif
                                    {{ Functions::firstNameLastName($doctor->nombre, '') }}
                                    <span class="badge hidden">0</span>
                                </a>
                            @endforeach
                        </div>
                    {{ $frm->accordionItemClose() }}
                {{ $frm->accordionClose() }}
                @endif

                <!-- estados -->
                {{ $frm->accordionOpen('filter_state_accordion') }}
                    {{ $frm->accordionItemOpen(Lang::get('citas.state')) }}
                        <div class="list-group">
                            @foreach ($estados as $id => $nombre)
                                <a href="#" class="list-group-item group-filter filter-state" attr-id="{{ $id }}">
                                    {{ $nombre }}
                                    <span class="badge hidden">0</span>
                                </a>
                            @endforeach
                        </div>
                    {{ $frm->accordionItemClose() }}
                {{ $frm->accordionClose() }}

                <!-- cabinas -->
                {{ $frm->accordionOpen('filter_office_accordion') }}
                    {{ $frm->accordionItemOpen(Lang::get('consultorio.title_single')) }}
                        <div class="list-group">
                            @foreach ($consultorios as $id => $nombre)
                                <a href="#" class="list-group-item group-filter filter-office" attr-id="{{ $id }}">
                                    {{ $nombre }}
                                    <span class="badge hidden">0</span>
                                </a>
                            @endforeach
                        </div>
                    {{ $frm->accordionItemClose() }}
                {{ $frm->accordionClose() }}

            </div>
            <div class="col-md-10 calendar-holder">
                <div class='full-calendar' id="main_calendar"></div>
            </div>
        </div>
        {{-- $frm->panelClose() --}}

    </div>
</div>

<!-- NEW EVENT FORM -->
{{ $frm->modalOpen('new_event_form', Lang::get('citas.new_event')) }}
   <form id="frm_data_new_event" class="form-horizontal" role="form" method="post" autocomplete="off" action="{{ URL::route('admin_citas_registrar_post') }}">
        <input type="hidden" name="id" id="cita_id" value="0">
        <div class="list-group">
            <!-- date & time -->
            <a href="#" class="list-group-item" data-toggle="modal" data-target="#new_event_date_time_modal">
                <div class="row form-item">
                    <div class="col-sm-2 col-xs-2">
                        <div class="status-icon" id="icon_time">
                            <i class="fa fa-4x fa-clock-o"></i>
                        </div>
                    </div>
                    <div class="col-sm-10 col-xs-10">
                        <h4 class="list-group-item-heading" id="cita_date_time">...</h4>
                        <p class="list-group-item-text" id="cita_date_time_remaining"></p>
                        {{ $frm->hidden('fecha', 'fecha_hidden') }}
                        {{ $frm->hidden('hora_inicio', 'hora_inicio_hidden') }}
                        {{ $frm->hidden('hora_fin', 'hora_fin_hidden') }}
                    </div>
                </div>
            </a>

            <!-- doctor -->
            <a href="#" class="list-group-item" data-toggle="modal" data-target="#new_event_doctor_modal">
                <div class="row form-item">
                    <div class="col-sm-2 col-xs-2">
                        <div class="status-icon" id="icon_doctor">
                            <i class="fa fa-4x fa-user-md"></i>
                        </div>
                    </div>
                    <div class="col-sm-10 col-xs-10">
                        <div class="row">
                            <div class="col-sm-10">
                                <h4 class="list-group-item-heading" id="cita_doctor_name">{{ Lang::get('global.select') }}</h4>
                                <p class="list-group-item-text" id="cita_doctor_inf"></p>
                                {{ $frm->hidden('doctor_id', 'doctor_id_hidden') }}
                            </div>
                            <div class="col-sm-2 hidden-xs">
                                <img class="avatar-thumb" id="cita_doctor_avatar" src="{{ URL::asset('img/avatars/s/default.jpg') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <!-- patient -->
            <a href="#" id="open_patients_modal" class="list-group-item" data-toggle="modal" data-target="#new_event_patient_modal">
                <div class="row form-item">
                    <div class="col-sm-2 col-xs-2">
                        <div class="status-icon" id="icon_patient">
                            <i class="fa fa-4x fa-user"></i>
                        </div>
                    </div>
                    <div class="col-sm-10 col-xs-10">
                        <h4 class="list-group-item-heading" id="cita_patient_name">{{ Lang::get('global.select') }}</h4>
                        <p class="list-group-item-text" id="cita_patient_inf"></p>
                        {{ $frm->hidden('paciente_id', 'paciente_id_hidden') }}
                    </div>
                </div>
            </a>

            <!-- services -->
            <a href="#" class="list-group-item" data-toggle="modal" data-target="#new_event_service_modal">
                <div class="row form-item">
                    <div class="col-sm-2 col-xs-2">
                        <div class="status-icon" id="icon_service">
                            <!--i class="fa fa-4x fa-check-square-o"></i-->
                            <figure class="icon treatment"></figure>
                        </div>
                    </div>
                    <div class="col-sm-10 col-xs-10">
                        <h4 class="list-group-item-heading" id="cita_service_name">{{ Lang::get('global.select') }}</h4>
                        <p class="list-group-item-text" id="cita_service_inf"></p>
                        {{ $frm->hidden('servicio_id', 'servicio_id_hidden') }}
                    </div>
                </div>
            </a>

            <!-- office -->
            <a href="#" id="open_offices_modal" class="list-group-item" data-toggle="modal" data-target="#new_event_office_modal">
                <div class="row form-item">
                    <div class="col-sm-2 col-xs-2">
                        <div class="status-icon" id="icon_office">
                            <!--i class="fa fa-4x fa-cube"></i-->
                            <figure class="icon door"></figure>
                        </div>
                    </div>
                    <div class="col-sm-10 col-xs-10">
                        <h4 class="list-group-item-heading" id="cita_office_name">{{ Lang::get('global.select') }}</h4>
                        <p class="list-group-item-text" id="cita_office_inf"></p>
                        {{ $frm->hidden('consultorio_id', 'consultorio_id_hidden') }}
                    </div>
                </div>
            </a>
        </div>
        <input type="hidden" name="warning_key" id="warning_key">
        <input type="hidden" name="ignore_warning" id="ignore_warning_submit" value="0">
        <input type="hidden" name="ignore_warning_all" id="ignore_warning_all_submit" value="0">

        {{ Form::token() }}
    </form>
<?php
    $custom_footer = <<<EOT
    <div class="alert alert-danger alert-dismissible modal-alert hidden" role="alert">
        <button type="button" class="close" data-hide="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <i class="fa fa-exclamation-circle"></i>&nbsp; 
        <span class="sr-only">Error:</span>
        <span class="msg"></span>
EOT;
    if ($user->admin) {
        $lbl_ignore = Lang::get('citas.ignore_warning');
        $lbl_ignore_all = Lang::get('citas.ignore_all_warnings');
        $custom_footer .= <<<EOT
        <div class="hidden" id="warning_ignore_options">
            <br>
            <br><input type="checkbox" id="ignore_warning" value="1"><label for="ignore_warning">&nbsp;{$lbl_ignore}</label>
            <br><input type="checkbox" id="ignore_warning_all" value="1"><label for="ignore_warning_all">&nbsp;{$lbl_ignore_all}</label>
        </div>
EOT;
    }
    $custom_footer .= <<<EOT
    </div>
EOT;
?>
{{ $frm->modalClose(null, null, true, $custom_footer) }}
<!-- /NEW EVENT FORM-->

    <!-- NEW DATE TIME FORM -->
    {{ $frm->modalOpen('new_event_date_time_modal', Lang::get('citas.set_date_time')) }}
        <form id="frm_new_event_date_time_inf" class="form-horizontal" role="form" method="get" autocomplete="off" action="{{ URL::route('cita_datetime_inf_get') }}">
            {{ $frm->date('fecha', null, Lang::get('citas.date'), 'day') }}
            {{ $frm->time('hora_inicio', null, Lang::get('citas.time_start')) }}
            {{ $frm->time('hora_fin', null, Lang::get('citas.time_end'), 'hidden') }}
        </form>
    {{ $frm->modalClose() }}
    <!-- /NEW DATE TIME FORM -->

    <!-- NEW DOCTOR FORM -->
    {{ $frm->modalOpen('new_event_doctor_modal', Lang::get('citas.set') . ' ' . Lang::get('usuarios.doctor')) }}
        <form id="frm_new_event_doctor_inf" class="form-horizontal" role="form" method="get" autocomplete="off" action="{{ URL::route('cita_doctor_inf_get') }}">
            {{ $frm->remoteSelect('doctor_id', null, Lang::get('citas.doctor'), URL::route('admin_doctores_list')) }}
            <nav>
              <ul class="pagination pagination-sm">
                @foreach ($doctor_letters as $letter)
                <li>
                    <a class="doctor-letter-index" href="#">{{ strtoupper($letter) }}</a>
                </li>
                @endforeach
              </ul>
            </nav>
        </form>
        <div id="doctors_by_letter_holder"></div>
    {{ $frm->modalClose() }}
    <!-- /NEW DOCTOR FORM -->

    <!-- NEW PATIENT FORM -->
    {{ $frm->modalOpen('new_event_patient_modal', Lang::get('citas.set') . ' ' . Lang::get('pacientes.title_single')) }}
        <form id="frm_new_event_patient_inf" class="form-horizontal" role="form" method="get" autocomplete="off" action="{{ URL::route('cita_patient_inf_get') }}">
            {{ $frm->remoteSelect('paciente_id', null, Lang::get('citas.patient'), URL::route('admin_pacientes_list')) }}
        </form>
        <br>
        <div id="new_patient">
            <h4 class="text-center">{{ Lang::get('citas.new_patient') }}</h4>
            <form id="frm_data_new_patient" class="form-horizontal" role="form" method="post" autocomplete="off" action="{{ URL::route('admin_pacientes_registrar_post') }}">
                {{ $frm->text('nombre', null, Lang::get('pacientes.name'), "", true) }}
                {{ $frm->text('apellido', null, Lang::get('pacientes.lastname'), "", true) }}
                {{-- $frm->text('dni', null, Lang::get('pacientes.dni'), "", true, array('[vejVEJ]{1}-{1}[0-9]{7,9}', 'Ej. V-123456789')); --}}
                {{ $frm->dni('dni', null, Lang::get('pacientes.dni'), "", true); }}
                {{ $frm->date('fecha_nacimiento', null, Lang::get('pacientes.birthdate')) }}
                {{ $frm->select('sexo', null, Lang::get('pacientes.gender'), $genders) }}
                {{ $frm->select('estado_civil', null, Lang::get('pacientes.marital_status'), $marital_statuses) }}
                {{ $frm->text('direccion', null, Lang::get('pacientes.address')) }}
                {{ $frm->tagSelect('telefonos', null, Lang::get('pacientes.phone')) }}
                {{ $frm->tagSelect('correos', null, Lang::get('pacientes.email')) }}
                {{ Form::token() }}
            </form>
        </div>
    {{ $frm->modalClose() }}
    <!-- /NEW PATIENT FORM -->

    <!-- NEW SERVICE FORM -->
    {{ $frm->modalOpen('new_event_service_modal', Lang::get('citas.set') . ' ' . Lang::get('servicio.title_single')) }}
        <form id="frm_new_event_service_inf" class="form-horizontal" role="form" method="get" autocomplete="off" action="{{ URL::route('cita_service_inf_get') }}">
            {{ $frm->remoteSelect('servicio_id', null, Lang::get('citas.service'), URL::route('admin_servicios_list')) }}
            <nav>
                <ul class="pagination pagination-sm">
                @foreach ($categorias_servicios as $cat)
                    <li>
                        <a class="service-category-index" attr-id="{{ $cat->id }}" href="#">{{ $cat->nombre }}</a>
                    </li>
                @endforeach
                    <li>
                        <a class="service-category-index" attr-id="0" href="#"><i>{{ Lang::get('servicio.all_categories') }}</i></a>
                    </li>
                </ul>
            </nav>
        </form>
        <div id="service_by_category_holder"></div>
    {{ $frm->modalClose() }}
    <!-- /NEW SERVICE FORM -->

    <!-- NEW OFFICE FORM -->
    {{ $frm->modalOpen('new_event_office_modal', Lang::get('citas.set') . ' ' . Lang::get('consultorio.title_single')) }}
        <div id="available_offices_holder"></div>
        <form id="frm_new_event_office_inf" class="form-horizontal{{ !User::canSelectUnavailableOffices() ? ' hidden': '' }}" role="form" method="get" autocomplete="off" action="{{ URL::route('cita_office_inf_get') }}">
            <input type="hidden" id="consultorio_id" name="consultorio_id" value="0">
            {{ $frm->select('area_id', null, Lang::get('area.title_single'), $areas) }}
            {{ $frm->select('consultorio_id_select', null, Lang::get('consultorio.title_single'), $consultorios) }}
        </form>
    {{ $frm->modalClose() }}
    <!-- /NEW OFFICE FORM -->

<!-- ACTIONS FORM -->
{{ $frm->modalOpen('actions_modal', Lang::get('citas.actions')) }}
    <div class="btn-toolbar" role="toolbar">
        <div id="states" class="btn-group btn-group-lg" role="group">
            <button id="state{{ Cita::UNCONFIRMED }}" type="button" class="btn btn-default" attr-state_id="{{ Cita::UNCONFIRMED }}" attr-type="warning">
                <i class="fa fa-4x fa-circle-o"></i>
                <span>{{ Lang::get('citas.unconfirmed') }}</span>
            </button>
            <button id="state{{ Cita::CONFIRMED }}" type="button" class="btn btn-default{{ !User::canConfirmOrCancelCita($user) ? ' disabled' : '' }}" attr-state_id="{{ Cita::CONFIRMED }}" attr-type="primary">
                <i class="fa fa-4x fa-check-circle-o"></i>
                <span>{{ Lang::get('citas.confirmed') }}</span>
            </button>
            <button id="state{{ Cita::CANCELLED }}" type="button" class="btn btn-default{{ !User::canConfirmOrCancelCita($user) ? ' disabled' : '' }}" attr-state_id="{{ Cita::CANCELLED }}" attr-type="danger">
                <i class="fa fa-4x fa-user-times"></i>
                <span>{{ Lang::get('citas.cancelled') }}</span>
            </button>
            <button id="state{{ Cita::DONE }}" type="button" class="btn btn-default{{ !User::canChangeCitaStateToDone($user) ? ' disabled' : '' }}" attr-state_id="{{ Cita::DONE }}" attr-type="success">
                <i class="fa fa-4x fa-check"></i>
                <span>{{ Lang::get('citas.done') }}</span>
            </button>
        </div>
        <div class="btn-group btn-group-lg" role="group">
            <button id="add_note" type="button" class="btn btn-default">
                <i class="fa fa-4x fa-comment"></i>
                <span>{{ Lang::get('citas.add_note') }}</span>
            </button>
            @if (User::canAddCitas($user))
            <button id="edit_cita" type="button" class="btn btn-default">
                <i class="fa fa-4x fa-pencil"></i>
                <span>{{ Lang::get('citas.edit') }}</span>
            </button>
            @endif
            @if (User::canDeleteCitas($user))
            <button id="delete_cita" type="button" class="btn btn-default">
                <i class="fa fa-4x fa-trash"></i>
                <span>{{ Lang::get('global.delete') }}</span>
            </button>
            @endif
        </div>
    </div>
    <form id="frm_action" class="hidden" role="form" method="post" autocomplete="off" action="{{ URL::route('cita_actions_post') }}">
        {{ $frm->hidden('cita_id', 'cita_id_action') }}
        {{ $frm->hidden('action', 'cita_action') }}
        {{ $frm->hidden('val', 'action_val') }}
        {{ Form::token() }}
    </form>
    @if (User::canDeleteCitas($user))
    <form id="frm_action_delete" class="hidden" role="form" method="post" autocomplete="off" action="{{ URL::route('admin_citas_accion_post') }}">
        {{ $frm->hidden('id', 'cita_id_delete') }}
        {{ $frm->hidden('action', 'delete_action', '', 'action_delete') }}
        {{ Form::token() }}
    </form>
    @endif
{{ $frm->modalClose(null, null, false) }}
<!-- /ACTIONS FORM -->

<!-- NOTE FORM -->
{{ $frm->modalOpen('note_modal', Lang::get('citas.notes')) }}
    <form id="frm_note" class="form-horizontal" role="form" method="post" autocomplete="off" action="{{ URL::route('admin_cita_editar_nota_post') }}">
        <?php $frm->displayLabels(false); ?>
        {{ $frm->hidden('id', 'note_id') }}
        {{ $frm->textarea('contenido', null, '') }}
        {{ $frm->hidden('cita_id', 'cita_id_note') }}
        {{ Form::token() }}
    </form>
{{ $frm->modalClose() }}
<!-- /NOTE FORM -->

<!-- MOVE EVENT FORM -->
<form id="frm_data_move" class="form-horizontal hidden" role="form" method="post" autocomplete="off" action="{{ URL::route('admin_citas_editar_post') }}">
    {{ $frm->id() }}
    {{ $frm->date('fecha', null, Lang::get('citas.date'), 'day') }}
    {{ $frm->time('hora_inicio', null, Lang::get('citas.time_start')) }}
    {{ $frm->time('hora_fin', null, Lang::get('citas.time_end')) }}
    {{ Form::token() }}
</form>
<!-- /MOVE EVENT FORM -->

<!-- GET EDIT EVENT FORM -->
<form id="frm_get_data_edit" class="hidden" role="form" method="get" autocomplete="off" action="{{ URL::route('cita_all_inf_get') }}">
    <input type="hidden" name="id" value="0">
</form>
<!-- /GET EDIT EVENT FORM -->

<!-- GET AVAILABILITY FORM -->
<form id="frm_get_availability" class="hidden" role="form" method="get" autocomplete="off" action="{{ URL::route('disponibilidad_calendar_source') }}">
    <input type="hidden" name="doctor_id" value="0">
    <input type="hidden" name="start" value="">
    <input type="hidden" name="end" value="">
</form>
<!-- /GET AVAILABILITY FORM -->

<!-- SEARCH EVENT FORM -->
<form id="frm_get_search" class="hidden" role="form" method="get" autocomplete="off" action="{{ URL::route('calendar_search') }}">
    <input type="hidden" name="query" value="">
</form>
<!-- /SEARCH EVENT FORM -->

<!-- AREA OFFICES FORM -->
<form id="frm_get_area_offices" class="hidden" role="form" method="get" autocomplete="off" action="{{ URL::route('get_area_offices') }}">
    <input type="hidden" name="area_id" value="0">
</form>
<!-- /AREA OFFICES FORM -->

{{ $frm->date('goto_date', null, null, 'day', 'hidden') }}

<!-- /MAIN CONTENT -->
@stop

@section('scripts')
{{ HTML::script('js/select2/select2.js') }}
{{-- HTML::script('js/bootstrap-datepicker/js/bootstrap-datepicker.js') --}}
{{ HTML::script('js/pickadate/picker.js') }}
{{ HTML::script('js/pickadate/picker.date.js') }}
{{ HTML::script('js/pickadate/picker.time.js') }}
{{ HTML::script('js/bootstrap-inputmask/bootstrap-inputmask.min.js') }}
{{ HTML::script('js/jquery-easing/jquery.easing.min.js') }}
{{ HTML::script('js/fullcalendar/lib/moment.min.js') }}
{{ HTML::script('js/fullcalendar/fullcalendar.js') }} <!-- customized -->
<?php if (Config::get('app.locale') != 'en') : ?>
    {{ HTML::script('js/select2/select2_locale_' . Config::get('app.locale') . '.js') }}
    {{ HTML::script('js/pickadate/translations/' . Config::get('app.locale') . '.js') }}
    {{ HTML::script('js/fullcalendar/lang/' . Config::get('app.locale') . '.js') }}
<?php endif; ?>
{{ HTML::script('js/gritter/js/jquery.gritter.min.js') }}
{{ HTML::script('js/panel.js') }}
<script type="text/javascript">
    var url_update_counter = "{{ URL::route('admin_citas_count_get') }}";

    var cita_ID;

    var availability_items;

    var creating_new_event = false;
    var showing_availability = false;
    var loading_availability_timer = false;
    var availability_source = '';

    var cal_top = 0;
    var loading_new_view = false;

    function setDateTime(start, end) {
        var $frm = $('#frm_new_event_date_time_inf');
        //setting date
        setDatePicker($frm.find('#fecha'), start._d);
        //setting start
        setTimePicker($frm.find('#hora_inicio'), start._d);
        //setting end
        setTimePicker($frm.find('#hora_fin'), end._d);

        submitForm( $frm, submitDateTimeFormDone, null, 'GET' );
    }

    function setDoctor() {
        var $actives = $('a.filter-doctor.active');
        if ($actives.length == 1) {
            var $frm = $('#frm_new_event_doctor_inf');
            var id = $actives.eq(0).attr('attr-id');
            $frm.find('input[name=doctor_id]').val(id);
            submitForm( $frm, submitDoctorFormDone, null, 'GET' );
        }
        else {
            /*var data = {};
            data['doctor_name_inf'] = '{{ Lang::get('global.select') }}';
            data['avatar_inf'] = '{{ URL::asset('img/avatars/s/default.jpg') }}';
            data['doctor_id'] = 0;
            submitDoctorFormDone(null, data);*/
        }
    }

    function hideNewEventPlaceHolder() {
        $main_calendar.fullCalendar( 'removeEvents', 0);
        $('a.stateundefined').remove();

    }

    function fn_new_event(start, end, allDay) {
        window.creating_new_event = true;
        var $cal = $main_calendar;
        $cal.fullCalendar('renderEvent', {
                id: 0,
                title: '*',
                start: start,
                end: end,
            },
            true // make the event "stick"
        );
        $cal.fullCalendar('unselect');
        var $modal = $('#new_event_form');
        $modal.find('input[name=id]').val('0');
        setDateTime(start, end);
        setDoctor();
        $modal.find('.modal-alert').hide();
        $modal.modal('show');
    }

    function fn_drop_event(event) {
        console.log(event);
        var $frm = $('#frm_data_move');
        //setting id
        $frm.find('.record-id').val( event['id'] );
        //setting date
        setDatePicker($frm.find('#fecha_edit'), event['start']._d);
        //setting start
        setTimePicker($frm.find('#hora_inicio_edit'), event['start']._d);
        //setting end
        if (!event['allDay']) {
            setTimePicker($frm.find('#hora_fin_edit'), event['end']._d);
        }
        else {
            setTimePicker($frm.find('#hora_fin_edit'), null);
        }

        submitForm($frm, function($frm, data) {
            if (data['ok'] != 1) {
                //alert(data['err']);
                $.gritter.add({
                	title: 'Advertencia',
                	text: data['err'],
                    image: '{{ URL::asset('img/noti_error.png') }}'
                });
                $main_calendar.fullCalendar('refetchEvents');
            }
        });
    }

    function updateCountPer(name) {
        //updates count of events per filter
        var $a = $('a.filter-' + name);
        $.each($a, function(i, o) {
            var id = $(o).attr('attr-id') || '0';
            window['total_' + name + id] = 0;
        });
        var $events = $('a.fc-event');
        $.each($events, function(i, o) {
            var $o = $(o);
            if (!$o.hasClass('availability')) {
                var id = $o.find('input.' + name + '_id').val();
                window['total_' + name + id] = (parseInt(window['total_' + name + id]) || 0) + 1;
                window['color_' + name + id] = $o.css('background-color');
            }
        });
        $.each($a, function(i, o) {
            var $o = $(o);
            var id = $o.attr('attr-id') || '0';
            var total = window['total_' + name + id];
            var color = window['color_' + name + id];
            if (total > 0 && id >= 0) {
                if (name == 'doctor') {
                    $o.find('span.badge').html(total).css('background-color', color).css('color', '#fff').removeClass('hidden');
                }
                else {
                    $o.find('span.badge').html(total).removeClass('hidden');
                }
            }
            else {
                $o.find('span.badge').addClass('hidden');
            }
        });
    }

    function fn_render_event(event) {
        //console.log('rendered: ' + event.id);
    }

    function fn_render_all_events(view) {
        if (!window.creating_new_event) {
            updateCountPer('doctor');
            updateCountPer('office');
            updateCountPer('state');
            highlightActive('doctor');
            highlightActive('office');
            highlightActive('state');
            bindEventClick();
    		$('.tip').tooltip();
            //styling morning / afternoon separation
            $main_calendar.find('#hour_12').siblings('.fc-widget-content').addClass('ampm-separation');
            if (window['cal_top'] > 0) {
                $main_calendar.find('.fc-scroller').eq(0).scrollTop( window['cal_top'] );
            }
            window['loading_new_view'] = false;
            //gets the calendar top after scrolling
            $main_calendar.find('.fc-scroller').scroll(function() {
                if (!window['loading_new_view']) {
                    window['cal_top'] = $main_calendar.find('.fc-scroller').eq(0).scrollTop();
                }
            });
        }
        else {
            window.creating_new_event = false;
        }
        //highlighting today
        var $today = $('.fc-today');
        if ($today.length) {
            $('.fc-day-header:nth-child(' + ($today.index() + 1) + ')').addClass('today-header');
        }
    }

    function submitDateTimeFormDone($frm, data) {
        $('#cita_date_time').html( data['fecha_inf'] + ' &nbsp; <span class="badge">' + data['hora_inf'] + '</span>' );
        $('#cita_date_time_remaining').html( data['restante'] );

        $('#fecha_hidden').val( data['fecha'] );
        $('#hora_inicio_hidden').val( data['hora_inicio'] );
        $('#hora_fin_hidden').val( data['hora_fin'] );
    }

    function submitDoctorFormDone($frm, data) {
        $('#cita_doctor_name').html( data['doctor_name_inf'] );
        //$('#cita_doctor_inf').html(  );
        $('#cita_doctor_avatar').attr('src', data['avatar_inf']);

        $('#doctor_id_hidden').val( data['doctor_id'] );
    }

    function submitPatientFormDone($frm, data) {
        $('#cita_patient_name').html( data['patient_name_inf'] );
        $('#cita_patient_inf').html( data['record_inf'] + '  (' + data['num_citas_inf'] + ')');

        $('#paciente_id_hidden').val( data['paciente_id'] );
    }

    function submitNewPatientFormDone($frm, data) {
        submitFormDoneDefault($frm, data);
        if (data['ok']) {
            $('#paciente_id').val( data['created_id'] );
            var $inf_frm = $('#frm_new_event_patient_inf');
            submitForm( $inf_frm, submitPatientFormDone, null, 'GET' );
            $inf_frm.closest('.modal').modal('hide');
            Panel.resetForm( $frm );
        }
    }

    function submitServiceFormDone($frm, data) {
        $('#cita_service_name').html( data['service_name_inf'] );
        $('#cita_service_inf').html( data['duration_inf'] );

        $('#servicio_id_hidden').val( data['servicio_id'] );
    }

    function submitOfficeFormDone($frm, data) {
        $('#cita_office_name').html( data['office_name_inf'] );
        $('#cita_office_inf').html( data['area_inf'] );

        $('#consultorio_id_hidden').val( data['consultorio_id'] );
    }

    function submitNoteFormDone($frm, data) {
        submitFormDoneDefault($frm, data);
        if (data['ok'] == 1) {
            $frm.closest('.modal').modal('hide');
            $main_calendar.fullCalendar('refetchEvents');
        }
    }

    function submitFormDone($frm, data) {
        submitFormDoneDefault($frm, data);
        $('.status-icon').removeClass('bad');
        if (data['ok'] == 1) {
            var $cal = $main_calendar;
            /*$cal.fullCalendar('renderEvent',
                {
                    title: data['titulo'],
                    start: new Date(data['inicio']['year'], data['inicio']['month'] - 1, data['inicio']['day'], isset(data['inicio']['hour']) ? data['inicio']['hour'] : 0, isset(data['inicio']['minutes']) ? data['inicio']['minutes'] : 0),
                    end: typeof data['fin'] == 'object' ? new Date(data['fin']['year'], data['fin']['month'] - 1, data['fin']['day'], data['fin']['hour'], data['fin']['minutes']) : null,
                    allDay: data['dia_completo'] == '1'
                },
                true // make the event "stick"
            );
            $cal.fullCalendar('unselect');*/
            $cal.fullCalendar( 'refetchEvents' );
            resetIgnoreWarningCheckboxes();
        }
        else {
            var $ignore_options = $('#warning_ignore_options');
            var $warning_key = $('#warning_key');
            if (data['bad']) {
                $('#icon_' + data['bad']).addClass('bad');
                if ($ignore_options.length) {
                    $ignore_options.removeClass('hidden');
                    $warning_key.val( data['warning_key'] );
                }
            }
            else {
                if ($ignore_options.length) {
                    $ignore_options.addClass('hidden');
                    $warning_key.val('0');
                }
            }
        }
    }

    function resetIgnoreWarningCheckboxes() {
        $('#warning_key').val(0);
        $('#ignore_warning_submit').val(0);
        $('#ignore_warning_all_submit').val(0);
        $('#ignore_warning').prop('checked', false);
        $('#ignore_warning_all').prop('checked', false);
    }

    function bindOfficeButtons() {
        $('.office-btn').click(function() {
            var $btn = $(this);
            var id = parseInt($btn.attr('attr-id')) || 0;
            if (id > 0) {
                $('#consultorio_id_select').select2('val', id);
                $('#consultorio_id').val(id);
                submitForm( $('#frm_new_event_office_inf'), submitOfficeFormDone, null, 'GET');
                $btn.closest('.modal').modal('hide');
            }
        });
    }

    function getAvailableOffices(service_id, cdate, start) {
        var $holder = $('#available_offices_holder');
        if ((parseInt(service_id) || 0) > 0) {
            $.ajax({
                type: 'GET',
                url: '{{ URL::route('get_available_offices') }}',
                dataType: 'json',
                data: { servicio_id : service_id, fecha: cdate, hora_inicio : start }
            }).done(function(data) {
                console.log(data);
                if (data['ok']) {
                    $holder.html( data['office_btns'] );
                    if ($('.office-btn').length == 0) {
                        if (parseInt($('#servicio_id_hidden').val()) > 0) {
                            $holder.html('<p class="text-center">{{ Lang::get('servicio.no_offices_attached_to_service') }}</p>');
                        }
                        else {
                            $holder.html('<p class="text-center">{{ Lang::get('servicio.no_offices_select_service_first') }}</p>');
                        }
                    }
                    else {
                        bindOfficeButtons();
                    }
                }
                else {
                    $holder.html('');
                }
            }).fail(function(data) {
                console.log(data); //failed
            });
        }
    }

    function highlightActive(name) {
        var $actives = $('a.filter-' + name + '.active');
        if ($actives.length == 0) {
            $('a.fc-event').removeClass('event-faded wide');
        }
        else {
            setTimeout(function() {
                $('a.group-filter').not('.filter-' + name).removeClass('active');
                $('a.fc-event').addClass('event-faded');
                $.each($actives, function(i, o) {
                    var $o = $(o);
                    var $events = $('a.fc-event');
                    $.each($events, function(j, e) {
                        var $e = $(e);
                        if ($e.find('input.' + name + '_id').val() == $o.attr('attr-id')) {
                            $e.removeClass('event-faded');
                            if ($actives.length == 1) {
                                $e.addClass('wide');
                            }
                            else {
                                $e.removeClass('wide');
                            }
                        }
                    });
                });
            }, 100);
            /*$('a.fc-event.event-faded').mouseenter(function() {
                $(this).hide();
            });*/
        }
    }

    function removeActive() {
        $('a.filter-doctor').removeClass('active');
        $('a.filter-office').removeClass('active');
        $('a.filter-state').removeClass('active');
    }

    function twoDigits(number) {
        if (number < 10) {
            return '0' + number;
        }
        return number;
    }

    function loadAvailability(id) {
        //remove old ones
        $('a.fc-event.availability').remove();
        $main_calendar.fullCalendar('removeEvents', 0);
        if (window['availability_source'].length) {
            $main_calendar.fullCalendar( 'removeEventSource', window['availability_source'] );
        }

        if (id > 0) {
            window['availability_source'] = '{{ URL::route('disponibilidad_calendar_source') }}/' + id;

            $main_calendar.fullCalendar( 'addEventSource', window['availability_source'] );
        }
    }

    function showHideNewPatient() {
        var paciente_id = parseInt($('#paciente_id').val()) || 0;
        var $new_patient_holder = $('#new_patient');
        if (paciente_id > 0) {
            $new_patient_holder.slideUp();
        }
        else {
            $new_patient_holder.removeClass('hidden').slideDown();
        }
    }

    function showAreaOffices(area_id) {
        var $frm = $('#frm_get_area_offices');
        $frm.find('input[name=area_id]').val( area_id );
        submitForm($frm, function($frm, data) {
            if (data['ok'] == 1) {
                var $office_select = $('#consultorio_id_select');
                $office_select.html('');
                var options = '';
                $.each(data['consultorios'], function(i, o) {
                    options += '<option value="' + o.id + '">' + o.nombre + '</option>';
                });
                $office_select.html(options);
                $office_select.select2('val', '');
            }
        });
    }

    function bindEventClick() {
        $('a.fc-event').click(function() {
            cita_ID = parseInt($(this).find('input.id').val()) || 0;
            if (cita_ID > 0) {
                getState(cita_ID);
                var $modal = $('#actions_modal')
                $modal.css('visibility', 'hidden');
                $modal.modal('show');
                setTimeout(function() {
                    setActionsModalWidth();
                    $modal.css('visibility', 'visible');
                }, 300);
            }
        });
    }

    function showState(state) {
        var $states = $('#states');
        var $btns = $states.find('button');
        $btns.removeClass('active btn-primary btn-danger btn-success btn-warning').addClass('btn-default');
        var $btn = $('#state' + state);
        if ($btn.length) {
            $btn.removeClass('btn-default disabled').addClass('active btn-' + $btn.attr('attr-type'));
        }
        @if (!User::canUndoCitaState())
        //disable edit if state equal done (1), confirmed (2) or cancelled (3)
        var $btn_edit = $('#edit_cita');
        if (state != {{ Cita::UNCONFIRMED }}) {
            $btn_edit.addClass('disabled');
        }
        else {
            $btn_edit.removeClass('disabled');
        }
        if (state == {{ Cita::DONE }} || state == {{ Cita::CANCELLED }}) {
            $btns.not('#state' + state).addClass('disabled');
        }
        else {
            @if (User::canConfirmOrCancelCita())
            $btns.removeClass('disabled');
            @endif
        }
        @endif
    }

    function setState(cita_id, state) {
        showState(state);
        var $frm = $('#frm_action');
        $frm.find('input[name=cita_id]').val( cita_id );
        $frm.find('input[name=action]').val( 'set_state' );
        $frm.find('input[name=val]').val( state );
        submitForm( $frm, function($frm, data) {
            if (data['ok']) {
                showState( data['state'] );
                /*var $cal = $main_calendar;
                $cal.fullCalendar( 'refetchEvents' );*/
                var $events = $('a.fc-event');
                $.each($events, function(i, e) {
                    var $e = $(e);
                    if ($e.find('input.id').val() == data['cita_id']) {
                        $e.removeClass('state0 state1 state2 state3').addClass('state' + data['state']);
                        return false;
                    }
                });
            }
        });
    }

    function getState(cita_id) {
        var $frm = $('#frm_action');
        $frm.find('input[name=cita_id]').val( cita_id );
        $frm.find('input[name=action]').val( 'get_state' );
        submitForm( $frm, function($frm, data) {
            if (data['ok']) {
                showState( data['state'] );
            }
        });
    }

    @if (User::canDeleteCitas($user))
    function delCita(cita_id) {
        var $frm = $('#frm_action_delete');
        $frm.find('input[name=id]').val( cita_id );
        //$frm.find('input[name=action]').val( 'action_delete' );
        submitForm( $frm, function($frm, data) {
            if (data['ok'] && data['deleted']) {
                /*var $cal = $main_calendar;
                $cal.fullCalendar( 'refetchEvents' );*/
                var $events = $('a.fc-event');
                $.each($events, function(i, e) {
                    var $e = $(e);
                    if ($e.find('input.id').val() == data['record']) {
                        $e.remove();
                        $main_calendar.fullCalendar('removeEvents', cita_id);
                        return false;
                    }
                });
            }
        });
    }
    @endif

    function bindDoctorByLetter() {
        var $holder = $('#doctors_by_letter_holder');
        $holder.find('a').click(function() {
            var $a = $(this);
            var id = parseInt($a.attr('data-id')) || 0;
            if (id > 0) {
                var $frm = $('#frm_new_event_doctor_inf');
                $frm.find('input[name=doctor_id]').val(id);
                submitForm( $frm, function($frm, data) {
                    submitDoctorFormDone($frm, data);
                    $holder.closest('.modal').modal('hide');
                }, null, 'GET');
            }
        });
    }

    function bindServiceByCategory() {
        var $holder = $('#service_by_category_holder');
        $holder.find('a').click(function() {
            var $a = $(this);
            var id = parseInt($a.attr('data-id')) || 0;
            if (id > 0) {
                var $frm = $('#frm_new_event_service_inf');
                $frm.find('input[name=servicio_id]').val(id);
                submitForm( $frm, function($frm, data) {
                    submitServiceFormDone($frm, data);
                    $holder.closest('.modal').modal('hide');
                }, null, 'GET');
            }
        });
    }

    function emphasizeEvent(event_id) {
        var $events = $('a.fc-event');
        $.each($events, function(i, o) {
            var $o = $(o);
            var id = $o.find('input.id').val();
            if (id == event_id) {
                setTimeout(function() {
                    $o.addClass('bring-to-front animated tada');
                }, 1000);
                setTimeout(function() {
                    $o.removeClass('bring-to-front animated tada');
                }, 4000);
                return false;
            }
        });
    }

    function gotoDate(date) {
        if (typeof date != 'undefined' && date.length) {
            var $cal = $main_calendar;
            var top = $cal.find('.fc-scroller').eq(0).scrollTop();
            $cal.fullCalendar('gotoDate', date);
            $cal.find('.fc-scroller').eq(0).scrollTop(top);
            $cal.fullCalendar('scrollTo', parseInt(date.split('T')[1]), $cal);
        }
    }

    function doFind(query) {
        if (query.length > 0) {
            var $frm = $('#frm_get_search');
            $frm.find('input[name=query]').val( query );
            submitForm($frm, function($frm, data) {
                if (data['ok'] == 1) {
                    removeActive();
                    gotoDate( data['fecha'] );
                    setTimeout(function() {
                        emphasizeEvent( data['cita_id'] );
                    }, 2000);
                }
            }, null, 'GET');
        }
    }
    
    function setActionsModalWidth() {
        var $modal = $('#actions_modal').find('.modal-dialog');
        var $btns = $modal.find('.btn-toolbar').find('.btn-group');
        var width = 0;
        
        $.each($btns, function(i, o) {
          width += $(o).outerWidth();
        });

        if (width > 0) $modal.width( width + 50 );
    }

    /*function checkAvailability() {
        var $frm = $('#frm_data_new_event');
        var url = '{{ URL::route('admin_citas_check_availability_post') }}';
        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            data: $frm.serialize() // serializes the form's elements.
        }).done(function(data) {
            console.log(data);
            if (data['ok']) {

            }
        }).fail(function(data) {
            console.log(data); //failed
        });
    }*/


    var $main_calendar = $('#main_calendar');

    $(document).ready(function() {
        App.init('{{ Config::get('app.locale') }}'); //Initialise plugins and elements

        {{ $frm->script() }}

        /* initialize the calendar
        -----------------------------------------------------------------*/
        $main_calendar.fullCalendar({
            'lang': '{{ Config::get('app.locale') }}',
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            selectable: {{ !$read_only ? 'true' : 'false' }},
            selectHelper: {{ !$read_only ? 'true' : 'false' }},
            selectConstraint: {
                start: '00:00',
                end: '23:59',
                dow: [ {{ $options['days_to_show_str'] }} ]
            },
            eventStartEditable: {{ !$read_only ? 'true' : 'false' }},
            eventDurationEditable: false,
            firstDay: 1,
            weekends: true,
            allDaySlot: false,
            defaultView: 'agendaWeek',
            timeFormat: 'h(:mm)t',
            axisFormat: 'h(:mm)t',
            slotDuration: '00:10:00',
            hiddenDays: [{{ $options['days_to_hide_str'] }}],
            businessHours: {
                start: '{{ $options['start_time'] }}',
                end: '{{ $options['end_time'] }}',
                dow: [ {{ $options['days_to_show_str'] }} ]
                // days of week. an array of zero-based day of week integers (0=Sunday)
            },
            minTime: '{{ $options['min_time'] }}',
            maxTime: '{{ $options['max_time'] }}',
            events: '{{ URL::route('calendar_source') }}',
            @if (!$read_only)
            select: function(start, end, allDay) {
                if (typeof fn_new_event == 'function') {
                    fn_new_event(start, end, allDay);
                }
            },
            eventDrop: function( event, delta, revertFunc, jsEvent, ui, view ) {
                if (typeof fn_drop_event == 'function') {
                    fn_drop_event(event);
                }
            },
            @endif
            eventRender: function( event, element, view ) {
                if (typeof fn_render_event == 'function') {
                    fn_render_event(event);
                }
            },
            eventAfterAllRender: function( view ) {
                if (typeof fn_render_all_events == 'function') {
                    fn_render_all_events(view);
                }
            },
            viewDestroy: function() {
                window['loading_new_view'] = true;
            },
            editable: {{ $read_only ? 'false' : 'true' }},
            droppable: false/*, // this allows things to be dropped onto the calendar !!!
            drop: function(date, allDay) { // this function is called when something is dropped

                // retrieve the dropped element's stored Event Object
                var originalEventObject = $(this).data('eventObject');

                // we need to copy it, so that multiple events don't have a reference to the same object
                var copiedEventObject = $.extend({}, originalEventObject);

                // assign it the date that was reported
                copiedEventObject.start = date;
                copiedEventObject.allDay = allDay;

                // render the event on the calendar
                // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
                $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);

                // is the "remove after drop" checkbox checked?
                if ($('#drop-remove').is(':checked')) {
                    // if so, remove the element from the "Draggable Events" list
                    $(this).remove();
                }

            },
            events: (typeof getCalendarEvents == 'function' ? getCalendarEvents() : [])*/
        });
        //----- End calendar Initialization -----

        $(window).resize(function() {
            var $cal = $main_calendar;
            var height = Math.floor( $(this).height() - $cal.offset().top ) - 80;
            $cal.fullCalendar('option', 'contentHeight', height);
        }).resize();

        //go to date button
        $main_calendar.find('.fc-toolbar').find('.fc-left').append($('<button id="goto_date_btn" class="fc-button fc-state-default fc-corner-left fc-corner-right" type="button">{{ Lang::get('citas.goto') }}</button>'));
        
        $('#goto_date_btn').click(function() {
            setTimeout(function() {
                $('#goto_date_edit').pickadate('picker').open();
            }, 300);
        }).mouseenter(function() {
            $(this).addClass('fc-state-hover');
        }).mouseleave(function() {
            $(this).removeClass('fc-state-hover');
        });

        $('#goto_date_edit').pickadate('picker').on('set', function() {
            var $dp = $('#goto_date_edit').pickadate('picker');
            gotoDate( $dp.get('select', 'yyyy-mm-dd') );
        });


        $('#new_event_form').on('hidden.bs.modal', function() {
            hideNewEventPlaceHolder();
        }).find('button.modal-btn-ok').click(function() {
            var $form = $(this).closest('.modal').find('form').eq(0);
            //new one
            if (parseInt($form.find('input[name=id]').val()) == 0) {
                submitForm( $form, submitFormDone );
            }
            //existing one
            else {
                submitForm( $form, submitFormDone, null, null, null, '{{ URL::route('admin_citas_editar_post') }}' );
            }
        });

        // getting date time inf
        $('#new_event_date_time_modal').find('button.modal-btn-ok').click(function() {
            var $modal = $(this).closest('.modal');
            var $form = $modal.find('form').eq(0);
            submitForm( $form, submitDateTimeFormDone, null, 'GET' );
            $modal.modal('hide');
        });

        // getting doctor inf
        $('#new_event_doctor_modal').find('button.modal-btn-ok').click(function() {
            var $modal = $(this).closest('.modal');
            var $form = $modal.find('form').eq(0);
            submitForm( $form, submitDoctorFormDone, null, 'GET' );
            $modal.modal('hide');
        });

        // getting patient inf
        $('#new_event_patient_modal').find('button.modal-btn-ok').click(function() {
            var paciente_id = parseInt($('#paciente_id').val()) || 0;
            var $frm;
            if (paciente_id > 0) {
                var $modal = $(this).closest('.modal');
                $frm = $modal.find('form').eq(0);
                submitForm( $frm, submitPatientFormDone, null, 'GET' );
                $modal.modal('hide');
            }
            else {
                $frm = $('#frm_data_new_patient');
                submitForm( $frm, submitNewPatientFormDone );
            }
        });

        // getting service inf
        $('#new_event_service_modal').find('button.modal-btn-ok').click(function() {
            var $modal = $(this).closest('.modal');
            var $form = $modal.find('form').eq(0);
            submitForm( $form, submitServiceFormDone, null, 'GET' );
            $modal.modal('hide');
        });

        // getting office inf
        $('#new_event_office_modal').find('button.modal-btn-ok').click(function() {
            var $modal = $(this).closest('.modal');
            var $form = $modal.find('form').eq(0);
            submitForm( $form, submitOfficeFormDone, null, 'GET' );
            $modal.modal('hide');
        });

        // saving note
        $('#note_modal').find('button.modal-btn-ok').click(function() {
            var $modal = $(this).closest('.modal');
            var $form = $modal.find('form').eq(0);
            submitForm( $form, submitNoteFormDone );
            $modal.modal('hide');
        });

        $('#open_offices_modal').click(function() {
            getAvailableOffices( $('#servicio_id_hidden').val(), $('#fecha_hidden').val(), $('#hora_inicio_hidden').val() );
        });

        $('#open_patients_modal').click(function() {
            $('#paciente_id').select2('val', '');
            showHideNewPatient();
        });

        $('#paciente_id').on('change', function() {
            showHideNewPatient();
        });

        $('#area_id').on('change', function() {
            showAreaOffices( $(this).val() );
        }).select2('val', '');

        $('#consultorio_id_select').on('change', function() {
            $('#consultorio_id').val( $(this).val() );
        }).change();

        /*$.each($('.full-calendar'), function(i, o) {
            $(o).fullCalendar( 'addEventSource', {
                 url: '{{-- URL::route('calendar_source') --}}'
            });
        });*/

        $('#ignore_warning').change(function() {
            $('#ignore_warning_submit').val( $(this).is(':checked') ? '1' : '0' );
        });

        $('#ignore_warning_all').change(function() {
            $('#ignore_warning_all_submit').val( $(this).is(':checked') ? '1' : '0' );
        });

        $('#states').find('button').click(function() {
            var $btn = $(this);
            var state = $btn.attr('attr-state_id');
            if (state == {{ Cita::DONE }} || state == {{ Cita::CANCELLED }}) {
                if (!confirm('{{ Lang::get('citas.confirm_change_state') }}')) {
                    return false;
                }
            }
            setState(cita_ID, state);
        });

        /*$('#dni').blur(function() {
            var $this = $(this);
            var val = $this.val();
            if (val.length > 3) {
                if (val.slice(1,2) != '-') {
                    $this.val( 'V-' + val.replace(/\D/g,'') );
                }
            }
        });*/

        $('#edit_cita').click(function() {
            var $btn = $(this);
            $btn.addClass('disabled');
            var $frm = $('#frm_get_data_edit');
            $frm.find('input[name=id]').val(cita_ID);
            submitForm( $frm, function($frm, data) {
                var $modal = $('#new_event_form');
                $modal.find('input[name=id]').val( data['cita_id'] );
                submitDateTimeFormDone($frm, data);
                submitDoctorFormDone($frm, data);
                submitPatientFormDone($frm, data);
                submitServiceFormDone($frm, data);
                submitOfficeFormDone($frm, data);
                $modal.modal('show');

                $btn.closest('.modal').modal('hide');
                $btn.removeClass('disabled');

                $frm = $('#new_event_date_time_modal');
                //setting date
                setDatePicker($frm.find('#fecha'), data['fecha']);
                //setting start
                setTimePicker($frm.find('#hora_inicio'), data['hora_inicio']);
                //setting end
                setTimePicker($frm.find('#hora_fin'), data['hora_fin']);
            });

        });

        $('#delete_cita').click(function() {
            if (confirm('¿Está seguro que quiere eliminar la cita?')) {
                var $btn = $(this);
                $btn.addClass('disabled');
                delCita(cita_ID);
                $btn.closest('.modal').modal('hide');
                $btn.removeClass('disabled');
            }
        });

        $('#add_note').click(function() {
            var $btn = $(this);
            var $modal = $('#note_modal');

            $btn.closest('.modal').modal('hide');

            $.ajax({
                type: 'GET',
                url: '{{ URL::route('get_cita_note') }}',
                dataType: 'json',
                data: { 'cita_id' : cita_ID }
            }).done(function(data) {
                if (data['ok'] == 1) {
                    $modal.find('input[name=id]').val(parseInt(data['nota_id']) || 0);
                    $modal.find('textarea[name=contenido]').val( data['nota'] );
                }
            }).fail(function(data) {
                console.log(data); //failed
            });


            $modal.find('input[name=cita_id]').val(cita_ID);
            $modal.modal('show');
        });

        $('a.doctor-letter-index').click(function(e) {
            var letter = $(this).html();

            $.ajax({
                type: 'GET',
                url: '{{ URL::route('get_doctor_by_letter') }}',
                dataType: 'json',
                data: { 'letter' : letter }
            }).done(function(data) {
                if (data['ok'] == 1) {
                    $('#doctors_by_letter_holder').html( data['html'] );
                    bindDoctorByLetter();
                }
            }).fail(function(data) {
                console.log(data); //failed
            });

            e.preventDefault();
            return false;
        });

        $('a.service-category-index').click(function(e) {
            var cat = $(this).attr('attr-id');

            $.ajax({
                type: 'GET',
                url: '{{ URL::route('get_service_by_category') }}',
                dataType: 'json',
                data: { 'category_id' : cat }
            }).done(function(data) {
                if (data['ok'] == 1) {
                    $('#service_by_category_holder').html( data['html'] );
                    bindServiceByCategory();
                }
            }).fail(function(data) {
                console.log(data); //failed
            });

            e.preventDefault();
            return false;
        });

        $('a.filter-doctor').click(function(e) {
            var view = $main_calendar.fullCalendar('getView').name;
            var $a = $(this);
            var id = parseInt($a.attr('attr-id')) || 0;
            $a.toggleClass('active').siblings().removeClass('active');

            if (view == 'agendaWeek' || view == 'agendaDay') {
                loadAvailability($a.hasClass('active') ? id : 0);
            }

            highlightActive('doctor');
            e.preventDefault();
            return false;
        });

        $('a.filter-office').click(function(e) {
            var $a = $(this);
            var id = $a.attr('attr-id');
            $a.toggleClass('active').siblings().removeClass('active');
            highlightActive('office');
            e.preventDefault();
            return false;
        });

        $('a.filter-state').click(function(e) {
            var $a = $(this);
            var id = $a.attr('attr-id');
            $a.toggleClass('active').siblings().removeClass('active');
            highlightActive('state');
            e.preventDefault();
            return false;
        });

        $('#search_event_btn').click(function(e) {
            var query = $('#search_event_query').val();
            if (query.length > 0) {
                doFind( query );
            }
            e.preventDefault();
            return false;
        });

        $('#search_event_query').keydown(function (e) {
            if (e.keyCode == 13) {
                $('#search_event_btn').click();
            }
        });



        //auto refreshes every 10 minutes
        setInterval(function() {
            var modals_opened = $('.modal.fade.in').length;
            if (!modals_opened) {
                $main_calendar.fullCalendar('refetchEvents');
            }
        }, 600000);

    });
</script>
@stop