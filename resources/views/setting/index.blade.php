<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-26
 */

?>

@extends('layouts.main')

@section('title', '系统设置')

@section('description', '平台相关设置')

@section('breadcrumb')
    <li><a href="/admin/users">Home</a></li>
@endsection

@section('content')
    <form action="/admin/settings/save" method="post">
        {{csrf_field()}}
        <table class="table">
            <tbody>
            @foreach ($settings as list($key, $name, $value, $hint))
                <tr>
                    <td class="setting name">{{$name}}</td>
                    <td><input type="text" class="form-control" value="{{$value}}" name="Settings[{{$key}}]"></td>
                    <td class="hint">{!!$hint!!}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2">
                    <button class="btn btn-success">保存</button>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
@endsection