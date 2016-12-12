<?php
$config = [
	'button' => '<button class="btn btn-md btn-material-orange-900" {{attrs}}>{{text}}</button>',
	'checkbox' => '<div class="checkbox"><label class="switch" style="white-space: nowrap;"><input class="form-control" type="checkbox" name="{{name}}" value="{{value}}" {{attrs}}><span class="checkbox-material"><span class="check"></span></span></label></div>',
	'error' => '<div class="form-group has-error error-message"><label class="control-label">{{content}}</label></div>',
	'file' => '<input class="form-control" type="file" name="{{name}}" {{attrs}}>',
	'formGroup' => '{{label}}{{input}}',
	'input' => '<input class="form-control" type="{{type}}" name="{{name}}" {{attrs}}>',
	'inputContainer' => '<div class="form-group">{{content}}</div>',	
	'label' => '<label class="control-label">{{text}}</label>',
	'select' => '<select class="form-control" name="{{name}}" {{attrs}}>{{content}}</select>',
	'textarea' => '<textarea class="form-control" name="{{name}}" {{attrs}}>{{value}}</textarea>',
    // 'dateWidget' => '<label class="block" name="{{name}}">{{label}}</label><div class="input-control text" data-role="datepicker" data-preset="{{value}}"><input type="text" name="{{name}}" class="datepick form-control" ></div>'

    // 'dateWidget' => '<label class="block">{{label}}</label><div class="input-control text" data-role="datepicker" data-preset="{{value}}"><input type="text" {{attrs}} name="{{name}}" class="datepick form-control" ></div>'
];
