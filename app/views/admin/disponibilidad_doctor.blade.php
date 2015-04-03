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
    /*#content {
        /*background: #fff url({{ URL::asset('img/bg/squairy_light.png') }}) repeat;* /
        background: #fff url({{ URL::asset('img/bg/gray_jean.png') }}) repeat;
    }

    .fc-view-container {
        background-color: #fff;
    }*/
</style>
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
                <li>
                    <a href="{{ URL::route('inicio_doctor', array('doctor_id' => $doctor_id)) }}">{{ Lang::get('global.general_inf') }}</a>
                </li>
                <li>
                    {{ Lang::get('usuarios.disponibility') }}
                </li>
            </ul>
            <!-- /BREADCRUMBS -->
            <div class="row">
                <div class="col-md-2">
                    <figure class="avatar">

                    </figure>
                </div>
                <div class="col-md-10">
                    <div class="clearfix">
                        <h3 class="content-title pull-left">{{ $doctor_nombre }}</h3>
                    </div>
                    <div class="description">{{ Lang::get('usuarios.disponibility') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /PAGE HEADER -->

<!-- MAIN CONTENT -->
<div class="row">
    <div class="col-sm-12">

        <!-- CALENDAR -->
        {{ $frm->panelOpen('calendar', Lang::get('disponibilidad.availability_of') . ' ' . $doctor_nombre, 'fa-calendar-o', '', array('collapse')) }}
        <div class="row">
            <div class="col-md-12 calendar-holder">
                <div class='full-calendar' id="disponible_calendar"></div>
            </div>
        </div>
        {{ $frm->panelClose() }}

    </div>
</div>

<!-- CREATE EVENT FORM -->
<form id="frm_data" class="form-horizontal hidden" role="form" method="post" autocomplete="off" action="{{ URL::route('admin_disponibilidad_editar_post') }}">
    <input type="hidden" name="id" id="disponibilidad_id" value="0">
    <input type="hidden" name="inicio" id="inicio">
    <input type="hidden" name="fin" id="fin">
    <input type="hidden" name="usuario_id" id="usuario_id" value="{{ $doctor_id }}">
    {{ Form::token() }}
</form>
<!-- /CREATE EVENT FORM -->


<!-- ACTIONS FORM -->
{{ $frm->modalOpen('actions_modal', Lang::get('citas.actions')) }}
    <div class="btn-toolbar" role="toolbar">
        <!--div id="states" class="btn-group btn-group-lg" role="group">
            <button id="state0" type="button" class="btn btn-default" attr-state_id="0" attr-type="danger">
                <i class="fa fa-4x fa-minus-circle"></i>
                <span>{{ Lang::get('disponibilidad.disable') }}</span>
            </button>
        </div-->
        <div class="btn-group btn-group-lg" role="group">
            <button id="duplicate_disponible" type="button" class="btn btn-default">
                <i class="fa fa-4x fa-copy"></i>
                <span>{{ Lang::get('disponibilidad.duplicate') }}</span>
            </button>
            <button id="delete_disponible" type="button" class="btn btn-default">
                <i class="fa fa-4x fa-trash"></i>
                <span>{{ Lang::get('disponibilidad.delete') }}</span>
            </button>
        </div>
    </div>
    <form id="frm_action" class="form-horizontal hidden" role="form" method="post" autocomplete="off" action="{{ URL::route('disponibilidad_actions_post') }}">
        <input type="hidden" name="disponibilidad_id" id="disponibilidad_id_action" value="0">
        <input type="hidden" name="action" id="action">
        <input type="hidden" name="val" id="val">
        <input type="hidden" name="usuario_id" id="usuario_id" value="{{ $doctor_id }}">
        {{ Form::token() }}
    </form>
{{ $frm->modalClose(null, null, false) }}
<!-- /ACTIONS FORM -->

<!-- DUPLICATE MODAL -->
{{ $frm->modalOpen('duplicate_modal', Lang::get('disponibilidad.duplicate_to')) }}
    <form id="frm_duplicate" class="form-horizontal" role="form" method="post" autocomplete="off" action="{{ URL::route('disponibilidad_duplicate_post') }}">
        {{ $frm->date('fecha', null, Lang::get('global.date')) }}
        <input type="hidden" name="disponibilidad_id" id="disponibilidad_id_duplicate" value="0">
        <input type="hidden" name="usuario_id" id="usuario_id" value="{{ $doctor_id }}">
        {{ Form::token() }}
        {{-- $frm->submit( Lang::get('global.ok') ) --}}
    </form>
{{ $frm->modalClose() }}
<!-- /DUPLICATE MODAL -->

{{ $frm->date('goto_date', null, null, 'day', 'hidden') }}

<!-- /MAIN CONTENT -->
@stop

@section('scripts')
{{ HTML::script('js/select2/select2.js') }}
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
{{ HTML::script('js/panel.js') }}
<script type="text/javascript">
    var url_update_counter = "{{ URL::route('admin_citas_count_get') }}";

    var dis_ID;

    function twoDigits(num) {
        return num < 10 ? ('0' + num) : num;
    }

    function dateToString(date) {
        return date.getUTCFullYear() + '-' + twoDigits(date.getUTCMonth()+1) + '-' + twoDigits(date.getUTCDate()) + ' ' + twoDigits(date.getUTCHours()) + ':' + twoDigits(date.getUTCMinutes()) + ':00';
    }

    function gotoDate(date) {
        if (typeof date != 'undefined' && date.length) {
            var $cal = $('#disponible_calendar');
            var top = $cal.find('.fc-scroller').eq(0).scrollTop();
            $cal.fullCalendar('gotoDate', date);
            $cal.find('.fc-scroller').eq(0).scrollTop(top);
            $cal.fullCalendar('scrollTo', parseInt(date.split('T')[1]), $cal);
        }
    }

    function fn_new_event(start, end, allDay) {
        var $frm = $('#frm_data');
        $frm.find('#disponibilidad_id').val('0');
        $frm.find('#inicio').val(dateToString(start._d));
        $frm.find('#fin').val(dateToString(end._d));
        submitForm($frm, function() {
            var $cal = $('#disponible_calendar');
            $cal.fullCalendar('refetchEvents');
            $cal.fullCalendar('unselect');
        });
    }

    function fn_drop_event(event) {
        var $frm = $('#frm_data');
        $frm.find('#disponibilidad_id').val(event.id);
        $frm.find('#inicio').val(dateToString(event.start._d));
        $frm.find('#fin').val(dateToString(event.end._d));
        submitForm($frm, function() {
            var $cal = $('#disponible_calendar');
            $cal.fullCalendar('refetchEvents');
            $cal.fullCalendar('unselect');
        });
    }

    function fn_render_event(event) {
        //console.log('rendered: ' + event.id);
    }

    function fn_render_all_events(view) {
        bindEventClick();
        //highlighting today
        var $today = $('.fc-today');
        if ($today.length) {
            $('.fc-day-header:nth-child(' + ($today.index() + 1) + ')').addClass('today-header');
        }
    }

    function bindEventClick() {
        @if (!$read_only)
        $('a.fc-event').click(function() {
            dis_ID = parseInt($(this).find('input.id').val()) || 0;
            if (dis_ID > 0) {
                getState(dis_ID);
                var $modal = $('#actions_modal');
                $modal.css('visibility', 'hidden');
                $modal.modal('show');
                setTimeout(function() {
                    setActionsModalWidth();
                    $modal.css('visibility', 'visible');
                }, 300);
            }
        });
        @endif
    }

    function showState(state) {
        var $btns = $('#states').find('button');
        $btns.removeClass('active btn-primary btn-danger btn-success').addClass('btn-default');
        var $btn = $('#state' + state);
        if ($btn.length) {
            $btn.removeClass('btn-default').addClass('active btn-' + $btn.attr('attr-type'));
        }
    }

    function applyState(data) {
        if (data['ok']) {
            showState( data['state'] );
            var $events = $('a.fc-event');
            $.each($events, function(i, e) {
                var $e = $(e);
                if ($e.find('input.id').val() == data['disponibilidad_id']) {
                    if ( data['state'] == '-1' ) { //deleted
                        $e.remove();
                    }
                    else {
                        $e.removeClass('state0 state1').addClass('state' + data['state']);
                    }
                    return false;
                }
            });
        }
    }

    function setState(disp_id, state) {
        showState(state);
        var $frm = $('#frm_action');
        $frm.find('input[name=disponibilidad_id]').val( disp_id );
        $frm.find('input[name=action]').val( 'set_state' );
        $frm.find('input[name=val]').val( state );
        submitForm( $frm, function($frm, data) {
            applyState(data);
        });
    }

    function getState(disp_id) {
        var $frm = $('#frm_action');
        $frm.find('input[name=disponibilidad_id]').val( disp_id );
        $frm.find('input[name=action]').val( 'get_state' );
        submitForm( $frm, function($frm, data) {
            if (data['ok']) {
                showState( data['state'] );
            }
        });
    }

    function deleteDisponibilidad(disp_id, $btn) {
        var $frm = $('#frm_action');
        $frm.find('input[name=disponibilidad_id]').val( disp_id );
        $frm.find('input[name=action]').val( 'delete' );
        submitForm( $frm, function($frm, data) {
            applyState(data);
            $btn.closest('.modal').modal('hide');
            $btn.removeClass('disabled');
        });
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

    $(document).ready(function() {
        App.init('{{ Config::get('app.locale') }}'); //Initialise plugins and elements

        {{ $frm->script() }}

        /* initialize the calendar
        -----------------------------------------------------------------*/
        $('#disponible_calendar').fullCalendar({
            'lang': '{{ Config::get('app.locale') }}',
            header: {
                left: 'prev,next today',
                center: 'title'/*,
                right: 'month,agendaWeek,agendaDay'*/
            },
            selectable: {{ !$read_only ? 'true' : 'false' }},
            selectHelper: {{ !$read_only ? 'true' : 'false' }},
            selectConstraint: {
                start: '00:00',
                end: '23:59',
                dow: [ {{ $options['days_to_show_str'] }} ]
            },
            eventStartEditable: {{ !$read_only ? 'true' : 'false' }},
            eventDurationEditable: {{ !$read_only ? 'true' : 'false' }},
            firstDay: 1,
            weekends: true,
            allDaySlot: false,
            defaultView: 'agendaWeek',
            timeFormat: 'h(:mm)t',
            axisFormat: 'h(:mm)t',
            slotDuration: '00:30:00',
            hiddenDays: [{{ $options['days_to_hide_str'] }}],
            businessHours: {
                start: '{{ $options['start_time'] }}',
                end: '{{ $options['end_time'] }}',
                dow: [ {{ $options['days_to_show_str'] }} ]
                // days of week. an array of zero-based day of week integers (0=Sunday)
            },
            minTime: '{{ $options['min_time'] }}',
            maxTime: '{{ $options['max_time'] }}',
            events: '{{ URL::route('disponibilidad_calendar_source_editable', array('doctor_id'=>$doctor_id)) }}',
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
            eventResize: function( event, delta, revertFunc, jsEvent, ui, view ) {
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
            editable: {{ $read_only ? 'false' : 'true' }},
            droppable: {{ $read_only ? 'false' : 'true' }}
        });
        //----- End calendar Initialization -----

        $('#states').find('button').click(function() {
            var $btn = $(this);
            if (!$btn.hasClass('active')) {
                setState(dis_ID, $btn.attr('attr-state_id'));
            }
            else {
                setState(dis_ID, 1);
            }
        });

        $('#delete_disponible').click(function() {
            var $btn = $(this);
            $btn.addClass('disabled');
            deleteDisponibilidad(dis_ID, $btn);
        });

        $('#duplicate_disponible').click(function() {
            var $modal = $('#duplicate_modal');
            $modal.modal('show');
        });

        $('#duplicate_modal').find('button.modal-btn-ok').click(function() {
            var $frm = $('#frm_duplicate');
            $frm.find('input[name=disponibilidad_id]').val( dis_ID );
            submitForm( $frm, function($frm, data) {
                if (data['ok'] == 1) {
                    alert(data['msg']);
                }
                submitFormDoneDefault($frm, data);
            });
        });

        //go to date button
        $('#disponible_calendar').find('.fc-toolbar').find('.fc-left').append($('<button id="goto_date_btn" class="fc-button fc-state-default fc-corner-left fc-corner-right" type="button">{{ Lang::get('citas.goto') }}</button>'));
        
        $('#goto_date_btn').click(function() {
            setTimeout(function() {
                $('#goto_date').pickadate('picker').open();
            }, 300);
        }).mouseenter(function() {
            $(this).addClass('fc-state-hover');
        }).mouseleave(function() {
            $(this).removeClass('fc-state-hover');
        });

        $('#goto_date').pickadate('picker').on('set', function() {
            var $dp = $('#goto_date').pickadate('picker');
            gotoDate( $dp.get('select', 'yyyy-mm-dd') );
        });

    });
</script>
@stop