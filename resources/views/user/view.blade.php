<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-26
 */
?>

@extends('layouts.main')

@section('title', '用户详情')

@section('breadcrumb')
    <li><a href="/admin/users/">Home</a></li>
    <li><a href="/admin/users">用户管理</a></li>
    <li class="active">用户详情</li>
@endsection

@section('description', $user->nickname)

@section('content')
    <table class="table table-bordered">
        <tbody>
        <tr>
            <td>ID</td>
            <td>{{$user->id}}</td>
        </tr>
        <tr>
            <td>姓名</td>
            <td>{{$user->nickname}}</td>
        </tr>
        <tr>
            <td>头像</td>
            <td><img class="avatar lg" src="/images/avatar/{{$user->avatar}}"></td>
        </tr>
        <tr>
            <td>乐乐号</td>
            <td>{{$user->account}}</td>
        </tr>
        <tr>
            <td>性别</td>
            <td>{{$user->sex}}</td>
        </tr>
        <tr>
            <td>地址</td>
            <td>{{$user->address}}</td>
        </tr>
        <tr>
            <td>注册时间</td>
            <td>{{$user->created_at}}</td>
        </tr>
        </tbody>
    </table>
@endsection