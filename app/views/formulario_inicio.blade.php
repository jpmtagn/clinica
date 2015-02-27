@extends('layouts.master')

@section('titulo')
    {{ Lang::get('formulario_inicio.title') }}
@stop

@section('atributos_body')
class="login"
@stop

@section('contenido')
<!-- PAGE -->
<section id="page">

    <!-- HEADER -->
    <header>
        <!-- NAV-BAR -->
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <div id="logo">
                        <a href="index.html"><img src="img/logo/logo-alt.png" height="40" alt="logo name" /></a>
                    </div>
                </div>
            </div>
        </div>
        <!--/NAV-BAR -->
    </header>
    <!--/HEADER -->

    <!-- LOGIN -->
    <section id="login" class="visible">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <div class="login-box-plain">
                        <!-- title -->
                        <h2 class="bigintro">{{ Lang::get('formulario_inicio.sing_in') }}</h2>

                        @if (Session::has('fail'))
                        <!-- error message -->
                        <div class="alert alert-danger">
                            <a class="close" aria-hidden="true" href="#" data-dismiss="alert">×</a>
                            {{ Session::get('msg') }}
                        </div>
                        @else
                        <div class="divide-40"></div>
                        @endif

                        <form role="form" method="post" action="{{ URL::route('inicio_sesion_post') }}">
                            <!-- correo -->
                            <div class="form-group">
                                <label for="correo">{{ Lang::get('formulario_inicio.email') }}</label>
                                <i class="fa fa-envelope"></i>
                                <input value="admin@defecto" type="email" id="correo" name="correo" class="form-control" value="{{ Functions::retrieve('correo') }}" required>
                            </div>
                            <!-- contraseña -->
                            <div class="form-group">
                                <label for="password">{{ Lang::get('formulario_inicio.password') }}</label>
                                <i class="fa fa-lock"></i>
                                <input value="cli_0123" type="password" id="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-actions">
                                <!-- recordarme -->
                                <label class="checkbox">
                                    <input type="checkbox" id="rememberme" name="rememberme" class="uniform" value=""> {{ Lang::get('formulario_inicio.remember_me') }}
                                </label>

                                {{ Form::token() }}

                                <!-- boton iniciar -->
                                <button type="submit" class="btn btn-danger">{{ Lang::get('formulario_inicio.submit') }}</button>
                            </div>
                        </form>
                        <div class="login-helpers">
                            <a href="#" onclick="swapScreen('forgot');return false;">{{ Lang::get('formulario_inicio.forgot_password') }}</a> <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/LOGIN -->

    <!-- FORGOT PASSWORD -->
    <section id="forgot">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <div class="login-box-plain">
                        <h2 class="bigintro">{{ Lang::get('formulario_inicio.reset_password') }}</h2>
                        <div class="divide-40"></div>
                        <form role="form">
                            <!-- correo -->
                            <div class="form-group">
                                <label for="correo_restablecer">{{ Lang::get('formulario_inicio.enter_email') }}</label>
                                <i class="fa fa-envelope"></i>
                                <input type="email" id="correo_restablecer" name="correo" class="form-control" required>
                            </div>
                            <!-- boton restablecer -->
                            <div class="form-actions">
                                <button type="submit" class="btn btn-info">{{ Lang::get('formulario_inicio.send_reset') }}</button>
                            </div>
                        </form>
                        <div class="login-helpers">
                            <a href="#" onclick="swapScreen('login');return false;">{{ Lang::get('formulario_inicio.back_login') }}</a> <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- FORGOT PASSWORD -->

</section>
<!--/PAGE -->
@stop

@section('scripts')

<script>
    jQuery(document).ready(function() {
        App.setPage("login");  //Set current page
        App.init(); //Initialise plugins and elements
        $('#correo').focus();
    });
</script>
@stop