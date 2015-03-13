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

    $username = Auth::user()->paciente;
    if ($username) {
    	$username = Functions::firstNameLastName($username->nombre, $username->apellido);
    }
    else {
    	$username = explode(chr(64), Auth::user()->correo);
    	$username = $username[0];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>Clínica | @yield('titulo')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="Alfredo">
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
				<i class="fa fa-users"></i>
				</a>
			</div>
			<!-- /TEAM STATUS FOR MOBILE -->
			<!-- SIDEBAR COLLAPSE -->
			<div id="sidebar-collapse" class="sidebar-collapse btn">
				<i class="fa fa-bars"
					data-icon1="fa fa-bars"
					data-icon2="fa fa-bars" ></i>
			</div>
			<!-- /SIDEBAR COLLAPSE -->
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
                <?php
                    $doctores = Doctor::getAll();
                ?>
                @foreach ($doctores as $doctor)
				    {{
				        AForm::userStatus(
                            $doctor->nombre,
                            $doctor->apellido,
                            $doctor->atendidos,
                            $doctor->pendientes,
                            URL::asset('img/avatars/s/' . (!empty($doctor->avatar) ? $doctor->avatar : 'default.jpg')),
                            URL::route('disponibilidad_doctor', array('doctor_id' => $doctor->usuario_id),
                            $doctor->usuario_id)
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
				<!-- personas (no parentescos) -->
				<li{{ activeClassIf('pacientes', $active_menu) }}>
                    <a href="{{ URL::route('admin_pacientes') }}">
                        <i class="fa fa-fw fa-users"></i>
                        <span class="menu-text">Personas</span>
                    </a>
                </li>

				<!-- usuarios -->
				<li{{ activeClassIf('usuarios', $active_menu) }}>
                    <a href="{{ URL::route('admin_usuarios') }}">
                        <i class="fa fa-fw fa-key"></i>
                        <span class="menu-text">Usuarios</span>
                    </a>
                </li>

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

                <!-- servicios -->
                <li{{ activeClassIf('servicio', $active_menu) }}>
                    <a href="{{ URL::route('admin_servicios') }}">
                        <i class="fa fa-fw fa-check-square-o"></i>
                        <span class="menu-text">{{ Lang::get('servicio.title_plural') }}</span>
                    </a>
                </li>

                <!-- equipos -->
                <li{{ activeClassIf('equipo', $active_menu) }}>
                    <a href="{{ URL::route('admin_equipos') }}">
                        <i class="fa fa-fw fa-plug"></i>
                        <span class="menu-text">{{ Lang::get('equipo.title_plural') }}</span>
                    </a>
                </li>

                <!-- opciones -->
                <li{{ activeClassIf('opciones', $active_menu) }}>
                    <a href="{{ URL::route('admin_config') }}">
                        <i class="fa fa-fw fa-cog"></i>
                        <span class="menu-text">{{ Lang::get('global.settings') }}</span>
                    </a>
                </li>
			</ul>
			<!-- /SIDEBAR MENU -->
		</div>
	</div>
	<!-- /SIDEBAR -->
	<div id="main-content">
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
                        $o.find('div.progress-bar.progress-bar-danger').width( data[id].p_pendiente+ '%' );
                    }
                });
            }
        }).fail(function(data) {
            console.log(data); //failed
        });
    }

    $(document).ready(function() {
        $('a.team-status-toggle').click(function() {
            updateDoctorsStatus();
        });
    });

</script>
@yield('scripts')
<!-- /JAVASCRIPTS -->
</body>
</html>