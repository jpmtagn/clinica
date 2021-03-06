<?php
    $active_menu = isset($active_menu) ? $active_menu : '';

    function activeClassIf($menu_name, $active_menu, $create_class = true) {
        if ($active_menu == $menu_name) {
            if ($create_class) {
                return ' class="active"';
            }
            else {
                return ' active';
            }
        }
        return '';
    }

    $user = Auth::user();
    $username = $user->paciente;
    if ($username) {
    	$username = Functions::firstNameLastName($username->nombre, $username->apellido);
    }
    else {
    	$username = explode(chr(64), $user->nombre);
    	$username = $username[0] . ' &nbsp; &nbsp; <i class="fa fa-exclamation-triangle"></i> (' . Lang::get('usuarios.hint_my_account') . ')';
    }

    $doctores = Doctor::getAll();

    if (User::canSeeNotifications($user)) {
        $notifications = ActionLog::latest()->get();
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>Citas | @yield('titulo')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <meta name="description" content="Sistema de Cita del Spa Médico Chilemex">
    <meta name="author" content="Alfredo">
    <link href="{{ URL::asset('img/favicon/favicon.ico') }}" rel="shortcut icon"  type="image/x-icon">
    <link href="{{ URL::asset('img/favicon/favicon-48.png') }}" rel="apple-touch-icon" />
    <link href="{{ URL::asset('img/favicon/favicon-120.png') }}" rel="apple-touch-icon" sizes="120x120" />
    <link href="{{ URL::asset('img/favicon/favicon-152.png') }}" rel="apple-touch-icon" sizes="152x152" />
    <!-- STYLESHEETS -->
    {{ HTML::style('css/admin.css') }}
    {{ HTML::style('css/themes/default.css') }}<!-- id="skin-switcher" -->
    {{ HTML::style('css/responsive.css') }}
    <!--[if lt IE 9]>
    {{ HTML::script('js/flot/excanvas.min.js') }}
    {{ HTML::script('js/ie-hacks/html5.js') }}
    {{ HTML::script('js/ie-hacks/css3-mediaqueries.js') }}
    <![endif]-->
    {{ HTML::style('font-awesome/css/font-awesome.min.css') }}
    <!-- ANIMATE -->
    {{ HTML::style('css/animatecss/animate.min.css') }}
    <!-- DATE RANGE PICKER -->
    {{-- HTML::style('js/bootstrap-daterangepicker/daterangepicker-bs3.css') --}}
    <!-- TODO -->
    {{-- HTML::style('js/jquery-todo/css/styles.css') --}}
    <!-- FULL CALENDAR -->
    {{-- HTML::style('js/fullcalendar/fullcalendar.min.css') --}}
    <!-- GRITTER -->
    {{-- HTML::style('js/gritter/css/jquery.gritter.css') --}}
    <!-- FONTS -->
    {{-- HTML::style('http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700') --}}
    @yield('cabecera')
</head>
<body>
<!-- HEADER -->
<header class="navbar clearfix" id="header">
	<div class="container">
		<div class="navbar-brand">
			<!-- COMPANY LOGO -->
			<a href="{{ URL::route('admin_inicio') }}">
			<img src="{{ URL::asset('img/logo/logo.png') }}" alt="" class="img-responsive" height="30" width="120">
			</a>
			<!-- /COMPANY LOGO -->
			<!-- TEAM STATUS FOR MOBILE -->
			<div class="visible-xs">
				<a href="#" class="team-status-toggle switcher btn dropdown-toggle">
				<i class="fa fa-user-md"></i>
				</a>
			</div>
			<!-- /TEAM STATUS FOR MOBILE -->
            @if (User::showMenu($user))
			<!-- SIDEBAR COLLAPSE -->
			<div id="sidebar-collapse" class="sidebar-collapse btn">
				<i class="fa fa-bars"
					data-icon1="fa fa-bars"
					data-icon2="fa fa-bars" ></i>
			</div>
			<!-- /SIDEBAR COLLAPSE -->
            @endif
		</div>
		<!-- NAVBAR LEFT -->
		<ul class="nav navbar-nav pull-left hidden-xs" id="navbar-left">
			<li class="dropdown">
				<a href="#" class="team-status-toggle dropdown-toggle tip-bottom" data-toggle="tooltip" title="{{ Lang::get('usuarios.view_doctor') }}">
				<i class="fa fa-user-md"></i>
				<span class="name">{{ Lang::get('usuarios.doctor') }}</span>
				<i class="fa fa-angle-down"></i>
				</a>
			</li>
		</ul>
		<!-- /NAVBAR LEFT -->
		<!-- BEGIN TOP NAVIGATION MENU -->
		<ul class="nav navbar-nav pull-right">
		    @if (User::canSeeNotifications($user))
		    <!-- BEGIN NOTIFICATION DROPDOWN -->
            <li class="dropdown" id="header-notification">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bell"></i>
                    <!--span class="badge">7</span-->
                </a>
                <ul class="dropdown-menu notification">
                    <li class="dropdown-title">
                        <span><i class="fa fa-bell"></i>Notificaciones</span>
                    </li>
                    @foreach ($notifications as $notification)
                        {{ AForm::notificationItem($notification) }}
                    @endforeach
                    <li class="footer">
                        <a href="{{ URL::route('admin_log') }}">Ver más notificaciones <i class="fa fa-arrow-circle-right"></i></a>
                    </li>
                </ul>
            </li>
            <!-- END NOTIFICATION DROPDOWN -->
            @endif
            <!-- BEGIN USER LOGIN DROPDOWN -->
            <li class="dropdown user" id="header-user">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    @if (User::avatar())
                    <img src="{{ URL::asset('img/avatars/s/' . User::avatar()) }}" alt="">
                    @else
					<img src="{{ URL::asset('img/avatars/s/default.jpg') }}" alt="">
                    @endif
                    <span class="username">{{ $username }}</span>
                    <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="{{ URL::route('mi_cuenta') }}"><i class="fa fa-cog"></i> {{ Lang::get('usuarios.my_account') }}</a></li>
                    <li><a href="{{ URL::route('cerrar_sesion') }}"><i class="fa fa-power-off"></i> {{ Lang::get('usuarios.close_session') }}</a></li>
                </ul>
            </li>
            <!-- END USER LOGIN DROPDOWN -->
		</ul>
		<!-- END TOP NAVIGATION MENU -->
	</div>
	<!-- TEAM STATUS -->
    <div class="container team-status" id="team-status">
		<div id="scrollbar">
			<div class="handle">
			</div>
		</div>
		<div id="teamslider">
            <ul class="team-list">
                @foreach ($doctores as $doctor)
				    {{
				        AForm::userStatus(
                            $doctor->nombre,
                            $doctor->apellido,
                            $doctor->atendidos,
                            $doctor->pendientes,
                            URL::asset('img/avatars/s/' . (!empty($doctor->avatar) ? $doctor->avatar : 'default.jpg')),
                            URL::route('inicio_doctor', array('doctor_id' => $doctor->usuario_id)),
                            $doctor->usuario_id
                        )
                     }}
                @endforeach
			</ul>
		</div>
	</div>
	<!-- /TEAM STATUS -->
</header>
<!--/HEADER -->
<!-- PAGE -->
<section id="page">
    @if (User::showMenu($user))
	<!-- SIDEBAR -->
	<div id="sidebar" class="sidebar">
		<div class="sidebar-menu nav-collapse">
			<ul>
				<!-- inicio -->
				<li{{ activeClassIf('inicio', $active_menu) }}>
					<a href="{{ URL::route('admin_inicio') }}">
					    <i class="fa fa-fw fa-home"></i> <span class="menu-text">Inicio</span>
					    <span class="selected"></span>
					</a>
				</li>

				<!-- personas -->
				<?php if (false) : ?>
				<li class="has-sub{{ activeClassIf('pacientes', $active_menu, false) }}">
                    <a href="javascript:;">
                        <i class="fa fa-fw fa-users"></i>
                        <span class="menu-text">Personas</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub">
                        <li><a href="{{ URL::route('admin_pacientes') }}"><span class="sub-menu-text">Administrar</span></a></li>
                        <li><a href="{{ URL::route('admin_parentescos') }}"><span class="sub-menu-text">Parentescos</span></a></li>
                    </ul>
                </li>
                <?php endif; ?>
                @if (User::canAdminPersonas($user))
				<!-- personas (no parentescos) -->
				<li{{ activeClassIf('pacientes', $active_menu) }}>
                    <a href="{{ URL::route('admin_pacientes') }}">
                        <i class="fa fa-fw fa-users"></i>
                        <span class="menu-text">Personas</span>
                    </a>
                </li>
                @endif

				@if (User::canAdminUsuarios($user))
                <!-- usuarios -->
				<li{{ activeClassIf('usuarios', $active_menu) }}>
                    <a href="{{ URL::route('admin_usuarios') }}">
                        <i class="fa fa-fw fa-key"></i>
                        <span class="menu-text">Usuarios</span>
                    </a>
                </li>
                @endif

				@if (User::canAdminCitas($user))
                <!-- citas -->
				<li class="has-sub{{ activeClassIf('citas', $active_menu, false) }}">
                    <a href="javascript:;">
                        <i class="fa fa-fw fa-calendar-o"></i>
                        <span class="menu-text">Citas</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub">
                        <li><a href="{{ URL::route('admin_citas') }}"><span class="sub-menu-text">Buscar</span></a></li>
                        <li><a href="{{ URL::route('admin_calendario') }}"><span class="sub-menu-text">Calendario</span></a></li>
                    </ul>
                </li>
                @endif

				@if (User::canAdminLugares($user))
                <!-- areas / consultorios -->
				<li class="has-sub{{ activeClassIf('area', $active_menu, false) }}">
                    <a href="javascript:;">
                        <i class="fa fa-fw fa-cube"></i>
                        <span class="menu-text">Lugares</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub">
                        <li><a href="{{ URL::route('admin_areas') }}"><span class="sub-menu-text">{{ Lang::get('area.title_plural') }}</span></a></li>
                        <li><a href="{{ URL::route('admin_consultorios') }}"><span class="sub-menu-text">{{ Lang::get('consultorio.title_plural') }}</span></a></li>
                    </ul>
                </li>
                @endif

                @if (User::canAdminTratamientos($user))
                <!-- servicios -->
                <li class="has-sub{{ activeClassIf('servicio', $active_menu, false) }}">
                    <a href="javascript:;">
                        <i class="fa fa-fw fa-check-square-o"></i>
                        <span class="menu-text">{{ Lang::get('servicio.title_plural') }}</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub">
                        <li><a href="{{ URL::route('admin_servicio_categorias') }}"><span class="sub-menu-text">{{ Lang::get('servicio.categories') }}</span></a></li>
                        <li><a href="{{ URL::route('admin_servicios') }}"><span class="sub-menu-text">{{ Lang::get('servicio.title_plural') }}</span></a></li>
                    </ul>
                </li>
                @endif

                @if (User::canAdminEquipos($user))
                <!-- equipos -->
                <li{{ activeClassIf('equipo', $active_menu) }}>
                    <a href="{{ URL::route('admin_equipos') }}">
                        <i class="fa fa-fw fa-plug"></i>
                        <span class="menu-text">{{ Lang::get('equipo.title_plural') }}</span>
                    </a>
                </li>
                @endif

                @if (User::canAdminOpciones($user))
                <!-- opciones -->
                <li{{ activeClassIf('opciones', $active_menu) }}>
                    <a href="{{ URL::route('admin_config') }}">
                        <i class="fa fa-fw fa-cog"></i>
                        <span class="menu-text">{{ Lang::get('global.settings') }}</span>
                    </a>
                </li>
                @endif
			</ul>
			<!-- /SIDEBAR MENU -->
		</div>
	</div>
	<!-- /SIDEBAR -->
    @endif
	<div id="main-content"{{ !User::showMenu($user) ? ' style="margin-left:0 !important"' : '' }}>
		<div class="container">
			<div class="row">
				<div id="content" class="col-lg-12">
					@yield('contenido')
				</div>
				<!--/CONTENT-->
			</div>
		</div>
	</div>
</section>
<!--/PAGE -->

<!-- JAVASCRIPTS -->
<!-- Placed at the end of the document so the pages load faster -->
<!-- JQUERY -->
{{ HTML::script('js/jquery/jquery-2.0.3.min.js') }}
<!-- JQUERY UI-->
{{ HTML::script('js/jquery-ui/js/jquery-ui-1.11.3.custom.js') }}
<!-- BOOTSTRAP -->
{{ HTML::script('bootstrap-dist/js/bootstrap.min.js') }}

<!-- DATE RANGE PICKER -->
{{-- HTML::script('js/bootstrap-daterangepicker/moment.min.js') --}}

{{-- HTML::script('js/bootstrap-daterangepicker/daterangepicker.min.js') --}}
<!-- SLIMSCROLL -->
{{ HTML::script('js/jQuery-slimScroll-1.3.0/jquery.slimscroll.min.js') }}
{{ HTML::script('js/jQuery-slimScroll-1.3.0/slimScrollHorizontal.min.js') }}
<!-- BLOCK UI -->
{{ HTML::script('js/jQuery-BlockUI/jquery.blockUI.min.js') }}
<!-- SPARKLINES -->
{{ HTML::script('js/sparklines/jquery.sparkline.min.js') }}
<!-- EASY PIE CHART -->
{{-- HTML::script('js/jquery-easing/jquery.easing.min.js') --}}
{{-- HTML::script('js/easypiechart/jquery.easypiechart.min.js') --}}
<!-- FLOT CHARTS -->
{{-- HTML::script('js/flot/jquery.flot.min.js') --}}
{{-- HTML::script('js/flot/jquery.flot.time.min.js') --}}
{{-- HTML::script('js/flot/jquery.flot.selection.min.js') --}}
{{-- HTML::script('js/flot/jquery.flot.resize.min.js') --}}
{{-- HTML::script('js/flot/jquery.flot.pie.min.js') --}}
{{-- HTML::script('js/flot/jquery.flot.stack.min.js') --}}
{{-- HTML::script('js/flot/jquery.flot.crosshair.min.js') --}}
<!-- TODO -->
{{-- HTML::script('js/jquery-todo/js/paddystodolist.js') --}}
<!-- TIMEAGO -->
{{-- HTML::script('js/timeago/jquery.timeago.min.js') --}}
<!-- FULL CALENDAR -->
{{-- HTML::script('js/fullcalendar/fullcalendar.min.js') --}}
<!-- COOKIE -->
{{ HTML::script('js/jQuery-Cookie/jquery.cookie.min.js') }}
<!-- GRITTER -->
{{-- HTML::script('js/gritter/js/jquery.gritter.min.js') --}}
<!-- CUSTOM SCRIPT -->
{{ HTML::script('js/script.js') }}
<script type="text/javascript">

    function updateDoctorsStatus() {
        $.ajax({
            type: 'GET',
            url: '{{ URL::route('update_doctors_status') }}',
            dataType: 'json'
        }).done(function(data) {
            if (data['ok'] == 1) {
                $users = $('#teamslider').find('ul.team-list').find('li');
                $.each($users, function(i, o) {
                    var $o = $(this);
                    var id = $o.attr('id');
                    if (typeof data[id] != 'undefined') {
                        $o.find('span.badge.badge-green').html( data[id].atendidos );
                        $o.find('span.badge.badge-red').html( data[id].pendientes );
                        $o.find('div.progress-bar.progress-bar-success').width( data[id].p_atendido + '%' );
                        $o.find('div.progress-bar.progress-bar-danger').width( data[id].p_pendiente + '%' );
                    }
                });
            }
        }).fail(function(data) {
            console.log(data); //failed
        });
    }

    $(document).ready(function() {
        $('a.team-status-toggle').click(function() {
            if (!$('#team-status').hasClass('open')) {
                updateDoctorsStatus();
            }
        });

        @if (User::canSeeNotifications($user))
        /*$('a.notification').click(function(e) {
            //TODO
            e.preventDefault();
            return false;
        });*/
        @endif
    });

</script>
@yield('scripts')
<!-- /JAVASCRIPTS -->
</body>
</html>