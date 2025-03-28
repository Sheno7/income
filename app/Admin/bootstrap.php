<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

use App\Models\Setting;
use Encore\Admin\Admin;

Encore\Admin\Form::forget(['map', 'editor']);
$doller= @Setting::where('key','doller')->first()->value ?? 1;
Admin::html('<script> var doller = "'.$doller.'";</script>');
Admin::js('admin.js');
