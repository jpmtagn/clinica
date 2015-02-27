<?php

class AForm {
    protected $edit = false;
    protected $script;
    protected $values;

    public function __construct() {
        $this->script = "";
        $this->values = array();
    }

    public function setEdit($edit) {
        $this->edit = (bool)$edit;
    }

    public function setValues($values) {
        $this->values = $values;
    }

    public function clearValues() {
        $this->values = array();
    }

    public function text($name, $id = null, $label = null, $classes = "", $required = false, $validation_pattern = null) {
        if ($id == null) $id = $name;
        if ($label == null) $label = ucfirst($name);
        if ($this->edit) $id = $id . '_edit';
        $required = $required ? ' required' : '';
        $value = isset($this->values[$name]) ? ' value="' . $this->values[$name] . '"' : '';
        if (is_array($validation_pattern)) {
            $vp = ' pattern="' . $validation_pattern[0] . '" title="' . $validation_pattern[1] . '"';
        } else $vp = '';
        return <<<EOT
            <div class="form-group {$classes}">
                <label for="{$id}" class="col-md-2 control-label">{$label}</label>
                <div class="col-md-10">
                    <input type="text" id="{$id}" name="{$name}" class="form-control" placeholder="{$label}"{$vp}{$value}{$required}>
                </div>
            </div>
EOT;
    }

    public function email($name = 'correo', $id = null, $label = null, $classes = "") {
        if ($id == null) $id = $name;
        if ($label == null) $label = ucfirst($name);
        if ($this->edit) $id = $id . '_edit';
        return <<<EOT
            <div class="form-group {$classes}">
                <label for="{$id}" class="col-md-2 control-label">{$label}</label>
                <div class="col-sm-10">
                    <input type="email" id="{$id}" name="{$name}" class="form-control" placeholder="{$label}" required>
                </div>
            </div>
EOT;
    }

    public function password($name = 'password', $id = null, $label = null, $classes = "") {
        if ($id == null) $id = $name;
        if ($label == null) $label = ucfirst($name);
        if ($this->edit) $id = $id . '_edit';
        return <<<EOT
            <div class="form-group {$classes}">
                <label for="{$id}" class="col-md-2 control-label">{$label}</label>
                <div class="col-sm-10">
                    <input type="password" id="{$id}" name="{$name}" class="form-control" placeholder="{$label}" required>
                </div>
            </div>
EOT;
    }

    public function date($name, $id = null, $label = null, $type = 'year') {
        if ($id == null) $id = $name;
        if ($label === null) $label = ucfirst($name);
        if ($this->edit) $id = $id . '_edit';
        $value = isset($this->values[$name]) ? ' value="' . $this->values[$name] . '"' : '';
        return <<<EOT
          <div class="form-group">
             <label for="{$id}" class="col-md-2 control-label">{$label}</label>
             <div class="col-md-10">
                <div class="input-group">
                    <input type="text" id="{$id}" name="{$name}" class="form-control input-calendar-{$type}" data-mask="9999-99-99" placeholder="{$label}"{$value}>
                    <!--span class="input-group-btn">
                        <button class="btn btn-primary" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span-->
                </div>
            </div>
          </div>
EOT;
    }

    public function time($name, $id = null, $label = null) {
        if ($id == null) $id = $name;
        if ($label == null) $label = ucfirst($name);
        if ($this->edit) $id = $id . '_edit';
        return <<<EOT
          <div class="form-group">
             <label for="{$id}" class="col-md-2 control-label">{$label}</label>
             <div class="col-md-10">
                <div class="input-group">
                    <input type="text" id="{$id}" name="{$name}" class="form-control input-time" data-mask="99:99 aa" placeholder="{$label}">
                </div>
            </div>
          </div>
EOT;
    }

    public function checkbox($name, $label = null, $id = null, $classes = "") {
        if ($id == null) $id = $name;
        if ($label == null) $label = ucfirst($name);
        if ($this->edit) $id = $id . '_edit';
        return <<<EOT
            <div class="form-group {$classes}">
                <div class="col-sm-offset-2 col-sm-10">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="{$id}" name="{$name}"> {$label}
                        </label>
                    </div>
                </div>
            </div>
EOT;
    }

    public function search($name = 'search', $id = null, $label = null, $classes = "") {
        if ($id == null) $id = $name;
        if ($label == null) $label = ucfirst($name);
        if ($this->edit) $id = $id . '_edit';
        return <<<EOT
            <div class="input-group {$classes}">
                <input type="search" id="{$id}" name="{$name}" class="form-control" placeholder="{$label}" required>
                <span class="input-group-btn">
                    <button type="submit" id="{$id}_btn" class="btn btn-default">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
EOT;
    }

    public function multiselect($name, $id = null, $label = null, $options = array(), $options_key = null, $options_val = null, $options_selected = array()) {
        if ($id == null) $id = $name;
        if ($label == null) $label = ucfirst($name);
        if ($this->edit) $id = $id . '_edit';
        $value = isset($this->values[$name]) ? $this->values[$name] : null;
        $output = <<<EOT
            <div class="form-group">
                <label for="{$id}" class="col-md-2 control-label">{$label}</label>
                <select multiple="" id="{$id}" name="{$name}" class="multi-select col-sm-9 col-xs-10">
EOT;
        foreach($options as $id => $val) {
            if ($options_key != null) {
                $id = $val[$options_key];
            }
            if ($options_val != null) {
                $val = $val[$options_val];
            }
            if (in_array($val, $options_selected) || $id == $value) {
                $output.= <<<EOT
                    <option value="{$id}" selected>{$val}</option>
EOT;
            }
            else {
                $output.= <<<EOT
                    <option value="{$id}">{$val}</option>
EOT;
            }
        }
        $output.= <<<EOT
                </select>
            </div>
EOT;
        return $output;
    }

    public function select($name, $id = null, $label = null, $options = array(), $options_key = null, $options_val = null, $option_selected = null) {
        if ($id == null) $id = $name;
        if ($label == null) $label = ucfirst($name);
        if ($this->edit) $id = $id . '_edit';
        $value = isset($this->values[$name]) ? $this->values[$name] : null;
        $output = <<<EOT
            <div class="form-group">
                <label for="{$id}" class="col-md-2 control-label">{$label}</label>
                <select id="{$id}" name="{$name}" class="single-select col-sm-9 col-xs-10">
EOT;
        foreach($options as $id => $val) {
            if ($options_key != null) {
                $id = $val[$options_key];
            }
            if ($options_val != null) {
                $val = $val[$options_val];
            }
            if ($val == $option_selected || $id == $value) {
                $output.= <<<EOT
                    <option value="{$id}" selected>{$val}</option>
EOT;
            }
            else {
                $output.= <<<EOT
                    <option value="{$id}">{$val}</option>
EOT;
            }
        }
        $output.= <<<EOT
                </select>
            </div>
EOT;
        return $output;
    }

    public function remoteSelect($name, $id = null, $label = null, $route = "") {
        if ($id == null) $id = $name;
        if ($label == null) $label = ucfirst($name);
        if ($this->edit) $id = $id . '_edit';
        $output = <<<EOT
            <div class="form-group">
                <label for="{$id}" class="col-md-2 control-label">{$label}</label>
                <input type="hidden" id="{$id}" name="{$name}" class="select2ajax bigdrop col-md-9">
            </div>
EOT;
        $this->script.= <<<EOT
            $("#{$id}").select2({
                allowClear: true,
                placeholder: "{$label}",
                minimumInputLength: 1,
                ajax: {
                    url: "{$route}",
                    dataType: 'json',
                    type: 'GET',
                    data: function (term, page) {
                        return {
                            q: term,
                        };
                    },
                    results: function (data, page) {
                        var items = {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name,
                                    slug: item.name,
                                    id: item._id
                                }
                            })
                        };
                        console.log(items);
                        return items;
                    }
                },
                dropdownCssClass: "bigdrop"
            });
EOT;
        return $output;
    }

    public function tagSelect($name, $id = null, $label = null) {
        if ($id == null) $id = $name;
        if ($label == null) $label = ucfirst($name);
        if ($this->edit) $id = $id . '_edit';
        $value = isset($this->values[$name]) ? ' value="' . $this->values[$name] . '"' : '';
        $output = <<<EOT
            <div class="form-group">
                <label for="{$id}" class="col-md-2 control-label">{$label}</label>
                <input type="hidden" id="{$id}" name="{$name}" class="select2tags col-md-9"{$value}>
            </div>
EOT;
        /*$this->script.= <<<EOT
            $("#{$id}").select2({
				tags:[""]
			});
EOT;*/
        return $output;
    }

    public function hidden($name, $id = null, $classes = "", $value = null) {
        if ($id == null) $id = $name;
        if ($this->edit) $id = $id . '_edit';
        
        if (isset($this->values[$name])) {
            $value = ' value="' . $this->values[$name] . '"';
        }
        elseif ($value != null) {
            $value = ' value="' . $value . '"';
        }
        else {
            $value = '';
        }

        return <<<EOT
        <input type="hidden" name="{$name}" id="{$id}" class="{$classes}"{$value}>
EOT;
    }

    public function view($id, $label, $val = "", $fa_icon = null) {
        $fa_icon = $fa_icon != null ? '<i class="fa ' . $fa_icon . '"></i>' : '';
        return <<<EOT
            <div class="form-group">
                <label class="col-sm-3 control-label">
                    {$fa_icon}
                    {$label}
                </label>
                <div class="col-sm-9">
                    <div class="field-content" id="view_{$id}">{$val}</div>
                </div>
            </div>
EOT;
    }

    public function id($val = "", $editing = true) {
        $this->edit = $editing;
        return <<<EOT
            <input type="hidden" name="id" value="{$val}" class="record-id">
EOT;
    }

    public function submit($label = null, $class = 'btn-primary') {
        if ($label == null) $label = Lang::get('global.save');
        return <<<EOT
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn {$class}">{$label}</button>
                </div>
            </div>
EOT;
    }

    public function controlButtons($edit_lbl = null, $delete_lbl = null, $html = '') {
        if ($edit_lbl == null) $edit_lbl = 'Editar';
        if ($delete_lbl == null) $delete_lbl = 'Eliminar';
        return <<<EOT
            <div class="col-sm-offset-2 col-xs-offset-0 col-sm-3 col-xs-12 btn-group" style="margin-bottom:10px">
                <button type="submit" name="action_edit" class="btn btn-default">
                    <i class="fa fa-pencil"></i>
                    {$edit_lbl}
                </button>
                <button type="submit" name="action_delete" class="btn btn-danger">
                    <i class="fa fa-trash-o"></i>
                    {$delete_lbl}
                </button>
            </div>
            <div class="col-sm-7 col-xs-12">
                {$html}
            </div>
EOT;
    }

    public function dropDownButton($label, $options, $js_func_name, $id = null) {
        if ($id == null) $id = 'dropdownMenu1';
        $output = <<<EOT
            <div class="dropdown">
              <button class="btn btn-default dropdown-toggle" type="button" id="{$id}" data-toggle="dropdown" aria-expanded="true">
                {$label}
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu" role="menu" aria-labelledby="{$id}" id="{$id}_options">
EOT;
        foreach ($options as $key => $option) {
            if (is_array($option)) {
                $output.= '<li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:;" menu-action="' . reset($option) . '">' . next($option) . '</a></li>';
            }
            else {
                $output.= '<li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:;" menu-action="' . $key . '">' . $option . '</a></li>';
            }
        }
        $output.= <<<EOT
              </ul>
            </div>
EOT;

        $this->script.= <<<EOT
            $('#{$id}_options li a').click(function(e) {
                if (typeof {$js_func_name} == 'function') {
                    {$js_func_name}( $(this) );
                }
            });
EOT;

        return $output;
    }

    public function button($id, $label, $fa_icon = null) {
        $fa_icon = $fa_icon != null ? '<i class="fa ' . $fa_icon . '"></i>' : '';
        $output = <<<EOT
            <button id="{$id}" type="button" class="btn btn-default">
                {$fa_icon}
                {$label}
            </button>
EOT;
        return $output;
    }


    public static function searchResults($results, $field, $field2 = null, $badge = null, $badge_field = null, $badge_match = null) {
        $output = "";
        if (count($results)) {
            foreach ($results as $result) {
                $row = $result->$field;
                $id = $result->id;
                $output.= <<<EOT
                    <a class="list-group-item search-result" data-id="{$id}">{$row}
EOT;
                if ($badge != null && $badge_field != null) {
                    $badge_lbl = ($result->$badge_field == $badge_match || ($badge_match == null && $result->$badge_field == 1)) ? $badge : false;
                    if ($badge_lbl !== false) {
                        $output.= <<<EOT
                            &nbsp;<span class="badge">{$badge}</span>
EOT;
                    }
                }
                if ($field2 != null) {
                    if (is_array($field2)) {
                        foreach ($field2 as $f) {
                            if (is_array($f)) {
                                if (!empty($result->$f[1])) {
                                    $output.= '<br><b>' . $f[0] . '</b>: ' . $result->$f[1];
                                }
                            }
                            else {
                                if (!empty($result->$f)) {
                                    $output.= '<br>' . $result->$f;
                                }
                            }
                        }
                    }
                    else {
                        $row2 = $result->$field2;
                        $output.= <<<EOT
                            <br><b>{$row2}</b>
EOT;
                    }
                }
                $output.= '</a>';
            }
        }

        return $output;
    }

    public static function badge($label, $show = true) {
        if ($show) {
            return <<<EOT
            &nbsp;<span class="badge">{$label}</span>
EOT;
        }
        return '';
    }

    public function remainingTime($value, $refresh_interval = 1000, $id = null) {
        if ($id == null) {
            $id = 'a' . uniqid();
        }
        $this->script.= <<<EOT
            $('#{$id}').knob({
                min: 0,
                max: {$value} + 1,
                readOnly: true
            });

            var {$id} = setInterval(function() {
                var v = $('#{$id}').val();
                if (v > 0) {
                    $('#{$id}').val( v - 1 ).trigger('change');
                }
                if (v == 1) {
                    setTimeout(function() {
                        $('#{$id}').parent().fadeOut('slow', function() {
                            $(this).remove();
                        });
                    }, 1000);
                    clearInterval({$id});
                }
            }, {$refresh_interval});
            
EOT;
        return <<<EOT
            <input id="{$id}" type="text" value="{$value}" class="dial">
EOT;
    }

    public static function userStatus($nombre, $apellido, $atendidos, $pendientes, $avatar) {
        $title = Functions::firstNameLastName($nombre, $apellido);
        $t = $atendidos + $pendientes;
        if ($t > 0) {
            $p_atendido = (int)(($atendidos / $t) * 100);
            $p_pendiente = 100 - $p_atendido; //(int)($pendientes / $t);
        }
        else {
            $p_atendido = 0;
            $p_pendiente = 0;
        }
        $atendidos_lbl = Functions::singlePlural(Lang::get('pacientes.done_singular'), Lang::get('pacientes.done_plural'), $atendidos);
        $pendientes_lbl = Functions::singlePlural(Lang::get('pacientes.pending_singular'), Lang::get('pacientes.pending_plural'), $pendientes);
        return <<<EOT
        <li><!-- class="current"-->
            <a href="javascript:void(0);">
                <span class="image">
                    <img src="{$avatar}" alt="" />
                </span>
                <span class="title">
                    {$title}
                </span>
                <div class="progress">
                    <div class="progress-bar progress-bar-success" style="width: {$p_atendido}%">
                        <span class="sr-only">{$p_atendido}% Completado</span>
                    </div>
                    <!--div class="progress-bar progress-bar-warning" style="width: 20%">
                        <span class="sr-only">20% Complete (warning)</span>
                    </div-->
                    <div class="progress-bar progress-bar-danger" style="width: {$p_pendiente}%">
                        <span class="sr-only">{$p_pendiente}% Completado</span>
                    </div>
                </div>
                <span class="status">
                    <div class="field">
                        <span class="badge badge-green">{$atendidos}</span> {$atendidos_lbl}
                        <span class="pull-right fa fa-check"></span>
                    </div>
                    <!--div class="field">
                        <span class="badge badge-orange">3</span> in-progress
                        <span class="pull-right fa fa-adjust"></span>
                    </div-->
                    <div class="field">
                        <span class="badge badge-red">{$pendientes}</span> {$pendientes_lbl}
                        <span class="pull-right fa fa-list-ul"></span>
                    </div>
                </span>
            </a>
        </li>
EOT;
    }


    public function header($title, $total, $icon) {
        $registros_lbl = Functions::singlePlural('registro', 'registros', $total);
        return <<<EOT
        <div class="row">
            <div class="col-sm-4">
                <div class="clearfix">
                    <h3 class="content-title pull-left">{$title}</h3>
                </div>
                <div class="description">{$title} registrados</div>
            </div>
            <div class="col-sm-8">
                <div class="dashbox panel panel-default">
                    <div class="panel-body">
                        <div class="panel-left red">
                            <i class="fa {$icon} fa-3x"></i>
                        </div>
                        <div class="panel-right">
                            <div class="pull-left">
                                <div id="total_records" class="number">{$total}</div>
                                <div class="title">{$registros_lbl}</div>
                            </div>
                            <div class="pull-left hidden-xs">
                                <button type="button" class="btn-add-new btn btn-primary btn-lg btn-custom">
                                    <span class="visible-sm" title="Agregar nuevo">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    <span class="hidden-sm">
                                        <i class="fa fa-plus"></i>
                                        Agregar nuevo
                                    </span>
                                </button>
                            </div>
                            <!--span class="label label-success">
                                26% <i class="fa fa-arrow-up"></i>
                            </span-->
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <button type="button" class="btn-add-new btn btn-primary btn-lg btn-custom visible-xs">
                        <i class="fa fa-plus"></i>
                        Agregar nuevo
                    </button>
                </div>
            </div>
        </div>
EOT;
    }

    public function script($jquery = false) {
        if (!$jquery) {
            return $this->script;
        }
        else {
            return <<<EOT
                $(document).ready(function() {
                    {$this->script}
                });
EOT;
        }
    }

    public function halfPanelOpen($first = false, $col = 6) {
        return ($first ? '<div class="row">' : '') . '<div class="col-md-' . $col . '">';
    }

    public function halfPanelClose($last = false) {
        return '</div>' . ($last ? '</div>' : '');
    }


    public function panelOpen($name, $label, $fa_icon, $classes = "primary", $tools = null) {
        $head = <<<EOT
            <div id="{$name}_panel" class="box border {$classes}">
            <div class="box-title">
                <h4>
                    <i id="{$name}_icon" class="fa {$fa_icon} panel_icon"></i>
                    <span id="{$name}_lbl" class="panel_lbl">{$label}</span>
                </h4>
                <div class="tools">
EOT;
        if (is_array($tools)) {
            foreach ($tools as $tool) {
                switch ($tool) {
                    case 'config':
                        $head .= <<<EOT
                    <a href="#box-config-{$name}" data-toggle="modal" class="config">
                        <i class="fa fa-cog"></i>
                    </a>
EOT;
                        break;

                    case 'refresh':
                        $head .= <<<EOT
                    <a href="javascript:;" class="reload">
                        <i class="fa fa-refresh"></i>
                    </a>
EOT;
                        break;

                    case 'collapse':
                        $head .= <<<EOT
                    <a href="javascript:;" class="collapse">
                        <i class="fa fa-chevron-up"></i>
                    </a>
EOT;
                        break;

                    case 'remove':
                        $head .= <<<EOT
                    <a href="javascript:;" class="remove">
                        <i class="fa fa-times"></i>
                    </a>
EOT;
                        break;



                }
            }
        }
        return <<<EOT
                {$head}
                </div>
            </div>
            <div class="box-body">
EOT;

    }

    public function panelClose() {
        return <<<EOT
                <div class="clearfix"></div>
            </div>
        </div>
EOT;

    }

    
    public function modalOpen($id, $title) {
        return <<<EOT
            <div class="modal fade" id="{$id}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">{$title}</h4>
                        </div>
                        <div class="modal-body">
EOT;

    }

    public function modalClose($ok = null, $close = null) {
        if ($ok == null) $ok = Lang::get('global.ok');
        if ($close == null) $close = Lang::get('global.close');
        return <<<EOT
                            <div class="alert alert-danger alert-dismissible modal-alert hidden" role="alert">
                              <button type="button" class="close" data-hide="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              <i class="fa fa-exclamation-circle"></i>&nbsp; 
                              <span class="sr-only">Error:</span>
                              <span class="msg"></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{$close}</button>
                            <button type="button" class="btn btn-primary" id="new_event_ok">{$ok}</button>
                        </div>
                    </div>
                </div>
            </div>
EOT;
    }

}