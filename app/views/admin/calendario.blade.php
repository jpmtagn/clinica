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
{{ HTML::style('js/fullcalendar/fullcalendar.min.css') }}
<style type="text/css">
    #content {
        /*background: #fff url({{ URL::asset('img/bg/squairy_light.png') }}) repeat;*/
        background: #fff url({{ URL::asset('img/bg/gray_jean.png') }}) repeat;
    }

    .fc-view-container {
        background-color: #fff;
    }

    #filter_accordion .panel {
        background: none !important;
        border: 0 none !important;
        box-shadow: none !important;
    }

    #filter_accordion h4 a {
        text-decoration: none;
    }

    #filter_accordion .list-group-item:first-child {
        border-radius: 0;
    }
</style>
@stop

@section('contenido')
<?php $frm = new AForm; ?>
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
                <div class="input-group">
                     <input type="text" value="" class="form-control" placeholder="{{ Lang::get('global.insert_search') }}" id="search_event_query" />
                     <span class="input-group-btn">
                        <a href="#" id="search_event_btn" class="btn btn-default"><i class="fa fa-search"></i></a>
                     </span>
                </div>

                <div class="divide-20"></div>

                {{ $frm->accordionOpen('filter_accordion') }}
                    {{ $frm->accordionItemOpen(Lang::get('usuarios.doctor')) }}
                        <div class="list-group">
                            @foreach ($doctores as $doctor)
                                <a href="#" class="list-group-item filter-doctor" attr-id="{{ $doctor->usuario_id }}"> <!-- active -->
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

        {{ Form::token() }}
    </form>
{{ $frm->modalClose() }}
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
        </form>
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
                {{ $frm->text('dni', null, Lang::get('pacientes.dni'), "", true, array('[vejVEJ]{1}-{1}[0-9]{7,9}', 'Ej. V-123456789')); }}
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
            {{ $frm->select('servicio_id', null, Lang::get('citas.service'), $servicios) }}
        </form>
    {{ $frm->modalClose() }}
    <!-- /NEW SERVICE FORM -->

    <!-- NEW OFFICE FORM -->
    {{ $frm->modalOpen('new_event_office_modal', Lang::get('citas.set') . ' ' . Lang::get('consultorio.title_single')) }}
        <div id="available_offices_holder"></div>
        <form id="frm_new_event_office_inf" class="form-horizontal" role="form" method="get" autocomplete="off" action="{{ URL::route('cita_office_inf_get') }}">
            {{ $frm->select('consultorio_id', null, Lang::get('consultorio.title_single'), $consultorios) }}
        </form>
    {{ $frm->modalClose() }}
    <!-- /NEW OFFICE FORM -->

<!-- ACTIONS FORM -->
{{ $frm->modalOpen('actions_modal', Lang::get('citas.actions')) }}
    <div class="btn-toolbar" role="toolbar">
        <div id="states" class="btn-group btn-group-lg" role="group">
            <button id="state{{ Cita::CONFIRMED }}" type="button" class="btn btn-default" attr-state_id="{{ Cita::CONFIRMED }}" attr-type="primary">
                <i class="fa fa-4x fa-check-circle-o"></i>
                <span>{{ Lang::get('citas.confirmed') }}</span>
            </button>
            <button id="state{{ Cita::CANCELLED }}" type="button" class="btn btn-default" attr-state_id="{{ Cita::CANCELLED }}" attr-type="danger">
                <i class="fa fa-4x fa-user-times"></i>
                <span>{{ Lang::get('citas.cancelled') }}</span>
            </button>
            <button id="state{{ Cita::DONE }}" type="button" class="btn btn-default" attr-state_id="{{ Cita::DONE }}" attr-type="success">
                <i class="fa fa-4x fa-check"></i>
                <span>{{ Lang::get('citas.done') }}</span>
            </button>
        </div>
        <div class="btn-group btn-group-lg" role="group">
            <button id="add_note" type="button" class="btn btn-default">
                <i class="fa fa-4x fa-comment"></i>
                <span>{{ Lang::get('citas.add_note') }}</span>
            </button>
            <button id="edit_cita" type="button" class="btn btn-default">
                <i class="fa fa-4x fa-pencil"></i>
                <span>{{ Lang::get('citas.edit') }}</span>
            </button>
        </div>
    </div>
    <form id="frm_action" class="form-horizontal hidden" role="form" method="post" autocomplete="off" action="{{ URL::route('cita_actions_post') }}">
        {{ $frm->hidden('cita_id', 'cita_id_action') }}
        {{ $frm->hidden('action', 'cita_action') }}
        {{ $frm->hidden('val', 'action_val') }}
        {{ Form::token() }}
    </form>
{{ $frm->modalClose(null, null, false) }}
<!-- /ACTIONS FORM -->

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
<form id="frm_get_data_edit" class="form-horizontal hidden" role="form" method="get" autocomplete="off" action="{{ URL::route('cita_all_inf_get') }}">
    <input type="hidden" name="id" value="0">
</form>
<!-- /GET EDIT EVENT FORM -->

<!-- /MAIN CONTENT -->
@stop

@section('scripts')
{{ HTML::script('js/select2/select2.js') }}
{{-- HTML::script('js/bootstrap-datepicker/js/bootstrap-datepicker.js') --}}
{{ HTML::script('js/pickadate/picker.js') }}
{{ HTML::script('js/pickadate/picker.date.js') }}
{{ HTML::script('js/pickadate/picker.time.js') }}
{{ HTML::script('js/bootstrap-inputmask/bootstrap-inputmask.min.js') }}
{{ HTML::script('js/fullcalendar/lib/moment.min.js') }}
{{ HTML::script('js/fullcalendar/fullcalendar.js') }} <!-- customized -->
<?php if (Config::get('app.locale') != 'en') : ?>
    {{ HTML::script('js/select2/select2_locale_' . Config::get('app.locale') . '.js') }}
    {{ HTML::script('js/pickadate/translations/' . Config::get('app.locale') . '.js') }}
    {{ HTML::script('js/fullcalendar/lang/' . Config::get('app.locale') . '.js') }}
<?php endif; ?>
{{ HTML::script('js/panel.js') }}
<script type="text/javascript">
    var url_update_counter = "{{ URL::route('admin_citas_count_get') }}";

    var cita_ID;

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
        var $cal = $('#main_calendar');
        $cal.fullCalendar( 'removeEvents', 0);
        $('a.stateundefined').remove();

    }

    function fn_new_event(start, end, allDay) {
        var $cal = $('#main_calendar');
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
        $modal.modal('show');
    }

    /*function fn_drop_event(event) {
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

        $frm.submit();
    }*/

    function fn_render_event(event) {
        //console.log('rendered: ' + event.id);
    }

    function fn_render_all_events(view) {
        //updates count of events per doctors
        var $a = $('a.filter-doctor');
        $.each($a, function(i, o) {
            var id = $(o).attr('attr-id') || '0';
            window['total_' + id] = 0;
        });
        var $events = $('a.fc-event');
        $.each($events, function(i, o) {
            var id = $(o).find('input.doctor_id').val();
            window['total_' + id] = (parseInt(window['total_' + id]) || 0) + 1;
        });
        $.each($a, function(i, o) {
            var $o = $(o);
            var id = $o.attr('attr-id') || '0';
            var total = window['total_' + id];
            if (total > 0 && id > 0) {
                $o.find('span.badge').html(total).removeClass('hidden');
            }
            else {
                $o.find('span.badge').addClass('hidden')
            }
        });
        highlightActiveDoctors();
        bindEventClick();
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
        //$('#cita_office_inf').html( data['duration_inf'] );

        $('#consultorio_id_hidden').val( data['consultorio_id'] );
    }

    function submitFormDone($frm, data) {
        submitFormDoneDefault($frm, data);
        $('.status-icon').removeClass('bad');
        if (data['ok'] == 1) {
            var $cal = $('#main_calendar');
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
        }
        else {
            $('#icon_' + data['bad']).addClass('bad');
        }
    }

    function bindOfficeButtons() {
        $('.office-btn').click(function() {
            var $btn = $(this);
            var id = parseInt($btn.attr('attr-id')) || 0;
            if (id > 0) {
                $('#consultorio_id').select2('val', id);
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
                    bindOfficeButtons();
                }
                else {
                    $holder.html('');
                }
            }).fail(function(data) {
                console.log(data); //failed
            });
        }
    }

    function highlightActiveDoctors() {
        var $actives = $('a.filter-doctor.active');
        if ($actives.length == 0) {
            $('a.fc-event').removeClass('event-faded wide');
        }
        else {
            $('a.fc-event').addClass('event-faded');
            $.each($actives, function(i, o) {
                var $o = $(o);
                var $events = $('a.fc-event');
                $.each($events, function(j, e) {
                    var $e = $(e);
                    if ($e.find('input.doctor_id').val() == $o.attr('attr-id')) {
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

    function bindEventClick() {
        $('a.fc-event').click(function() {
            cita_ID = parseInt($(this).find('input.id').val()) || 0;
            if (cita_ID > 0) {
                getState(cita_ID);
                $('#actions_modal').modal('show');
            }
        });
    }

    function showState(state) {
        var $btns = $('#states').find('button');
        $btns.removeClass('active btn-primary btn-danger btn-success').addClass('btn-default');
        var $btn = $('#state' + state);
        if ($btn.length) {
            $btn.removeClass('btn-default').addClass('active btn-' + $btn.attr('attr-type'));
        }
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
                /*var $cal = $('#main_calendar');
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

    $(document).ready(function() {
        App.init('{{ Config::get('app.locale') }}'); //Initialise plugins and elements

        {{ $frm->script() }}

        /* initialize the calendar
        -----------------------------------------------------------------*/
        $('#main_calendar').fullCalendar({
            'lang': '{{ Config::get('app.locale') }}',
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            selectable: true,
            selectConstraint: {
                start: '00:00',
                end: '23:59',
                dow: [ 1, 2, 3, 4, 5, 6 ]
            },
            selectHelper: true,
            eventStartEditable: false,
            eventDurationEditable: false,
            firstDay: 1,
            weekends: true,
            allDaySlot: false,
            defaultView: 'agendaWeek',
            timeFormat: 'h(:mm)t',
            axisFormat: 'h(:mm)t',
            slotDuration: '00:10:00',
            hiddenDays: [0],
            businessHours: {
                start: '08:00',
                end: '18:00',
                dow: [ 1, 2, 3, 4, 5 ]
                // days of week. an array of zero-based day of week integers (0=Sunday)
            },
            minTime: '06:00:00',
            maxTime: '22:00:00',
            events: '{{ URL::route('calendar_source') }}',
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
            editable: true,
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
            var $cal = $('#main_calendar');
            var height = Math.floor( $(this).height() - $cal.offset().top ) - 80;
            console.log(height);
            $cal.fullCalendar('option', 'contentHeight', height);
        }).resize();


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

        $('#open_offices_modal').click(function() {
            getAvailableOffices( $('#servicio_id_hidden').val(), $('#fecha_hidden').val(), $('#hora_inicio_hidden').val() );
        });

        $('#open_patients_modal').click(function() {
            $('#paciente_id').select2('val', '');
            showHideNewPatient();
        });

        $('#paciente_id').on("change", function(e) {
            showHideNewPatient();
        });

        /*$.each($('.full-calendar'), function(i, o) {
            $(o).fullCalendar( 'addEventSource', {
                 url: '{{-- URL::route('calendar_source') --}}'
            });
        });*/

        $('#states').find('button').click(function() {
            var $btn = $(this);
            if (!$btn.hasClass('active')) {
                setState(cita_ID, $btn.attr('attr-state_id'));
            }
            else {
                setState(cita_ID, 0);
            }
        });

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

        $('a.filter-doctor').click(function(e) {
            var $a = $(this);
            var id = $a.attr('attr-id');
            $a.toggleClass('active').siblings().removeClass('active');
            highlightActiveDoctors();
            e.preventDefault();
            return false;
        });

    });
</script>
@stop