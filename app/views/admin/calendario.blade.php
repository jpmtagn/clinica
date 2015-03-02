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
        {{ $frm->panelOpen('calendar', Lang::get('citas.calendar'), 'fa-calendar', '', array('collapse')) }}
        <div class="row">
            <div class="col-md-2">
                <div class="input-group">
                     <input type="text" value="" class="form-control" placeholder="{{ Lang::get('global.insert_search') }}" id="search_event_query" />
                     <span class="input-group-btn">
                        <a href="#" id="search_event_btn" class="btn btn-default"><i class="fa fa-search"></i></a>
                     </span>
                </div>
                <div class="divide-20"></div>
                <div class="external-events">
                    <h4>{{ Lang::get('usuarios.doctor') }}</h4>
                    <div id="event-box">
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
                    </div>
                </div>
            </div>
            <div class="col-md-10 calendar-holder">
                <div class='full-calendar'></div>
            </div>
        </div>
        {{ $frm->panelClose() }}

    </div>
</div>

<!-- NEW EVENT FORM -->
{{ $frm->modalOpen('new_event_form', Lang::get('citas.new_event')) }}
   <form id="frm_data_new_event" class="form-horizontal" role="form" method="post" action="{{ URL::route('admin_citas_registrar_post') }}">
        {{ $frm->date('fecha', null, Lang::get('citas.date'), 'day') }}
        {{ $frm->time('hora_inicio', null, Lang::get('citas.time_start')) }}
        {{ $frm->time('hora_fin', null, Lang::get('citas.time_end')) }}
        {{ $frm->remoteSelect('doctor_id', null, Lang::get('citas.doctor'), URL::route('admin_doctores_list')) }}
        {{ $frm->remoteSelect('paciente_id', null, Lang::get('citas.patient'), URL::route('admin_pacientes_list')) }}
        {{ Form::token() }}
    </form>
{{ $frm->modalClose() }}
<!-- /NEW EVENT FORM-->

<!-- EDIT EVENT FORM -->
<form id="frm_data_edit" class="form-horizontal hidden" role="form" method="post" action="{{ URL::route('admin_citas_editar_post') }}">
    {{ $frm->id() }}
    {{ $frm->date('fecha', null, Lang::get('citas.date'), 'day') }}
    {{ $frm->time('hora_inicio', null, Lang::get('citas.time_start')) }}
    {{ $frm->time('hora_fin', null, Lang::get('citas.time_end')) }}
    {{ Form::token() }}
</form>
<!-- /EDIT EVENT FORM -->

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

    function getCalendarEvents() {
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();

        return [
                {
                    title: 'All Day Event',
                    start: new Date(y, m, 1),
                    backgroundColor: Theme.colors.blue,
                },
                {
                    title: 'Long Event',
                    start: new Date(y, m, d-5),
                    end: new Date(y, m, d-2),
                    backgroundColor: Theme.colors.red,
                },
                {
                    id: 999,
                    title: 'Repeating Event',
                    start: new Date(y, m, d-3, 16, 0),
                    allDay: false,
                    backgroundColor: Theme.colors.yellow,
                },
                {
                    id: 999,
                    title: 'Repeating Event',
                    start: new Date(y, m, d+4, 16, 0),
                    allDay: false,
                    backgroundColor: Theme.colors.primary,
                },
                {
                    title: 'Meeting',
                    start: new Date(y, m, d, 10, 30),
                    allDay: false,
                    backgroundColor: Theme.colors.green,
                },
                {
                    title: 'Lunch',
                    start: new Date(y, m, d, 12, 0),
                    end: new Date(y, m, d, 14, 0),
                    allDay: false,
                    backgroundColor: Theme.colors.red,
                },
                {
                    title: 'Birthday Party',
                    start: new Date(y, m, d+1, 19, 0),
                    end: new Date(y, m, d+1, 22, 30),
                    allDay: false,
                    backgroundColor: Theme.colors.gray,
                },
                {
                    title: 'Click for Google',
                    start: new Date(y, m, 28),
                    end: new Date(y, m, 29),
                    url: 'http://google.com/',
                    backgroundColor: Theme.colors.green,
                }
               ];
    }

    function fn_new_event(start, end, allDay) {
        var $modal = $('#new_event_form');
        //setting date
        setDatePicker($modal.find('#fecha'), start._d);
        //setting start
        setTimePicker($modal.find('#hora_inicio'), start._d);
        //setting end
        setTimePicker($modal.find('#hora_fin'), end._d);

        $modal.modal('show');
    }

    function fn_drop_event(event) {
        console.log(event);
        var $frm = $('#frm_data_edit');
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
    }

    function fn_render_event(event) {
        console.log('rendered: ' + event.id);
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
    }

    function submitFormDone($frm, data) {
        submitFormDoneDefault($frm, data);
        if (data['ok'] == 1) {
            var $cal = $('.full-calendar');
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
    }

    function highlightActiveDoctors() {
        var $actives = $('a.filter-doctor.active');
        if ($actives.length == 0) {
            $('a.fc-event').removeClass('event-faded');
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
                    }
                });
            });
        }
    }

    $(document).ready(function() {
        App.init('{{ Config::get('app.locale') }}'); //Initialise plugins and elements

        {{ $frm->script() }}

        $('#new_event_ok').click(function() {
            submitForm( $('#frm_data_new_event'), submitFormDone );
        });

        $.each($('.full-calendar'), function(i, o) {
            $(o).fullCalendar( 'addEventSource', {
                 url: '{{ URL::route('calendar_source') }}'
                 //events: getCalendarEvents()
            });
        });

        $('a.filter-doctor').click(function(e) {
            var $a = $(this);
            var id = $a.attr('attr-id');
            $a.toggleClass('active');
            highlightActiveDoctors();
            e.preventDefault();
            return false;
        });

    });
</script>
@stop