<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-26
 *
 * @var fk\utility\Pagination\LengthAwarePaginator $paginator
 */
?>

@extends('layouts.main')

@section('title', '用户管理')

@section('description', '用户列表')

@section('breadcrumb')
    <li><a href="">Home</a></li>
@endsection

@section('content')
    <form action="" method="get" class="input-group">
        <input type="text"
               class="form-control"
               placeholder="输入手机号/乐乐号/姓名"
               name="keyword"
               value="{{$_GET['keyword']??''}}"
        >
        <span class="input-group-btn">
            <button class="btn btn-primary"><i class="fa fa-search"></i>搜索</button>
        </span>
    </form>
    <table class="table">
        <thead>
        <tr>
            <td>ID</td>
            <td>姓名</td>
            <td>头像</td>
            <td>手机号</td>
            <td>乐乐号</td>
            <td>注册时间</td>
            <td>操作</td>
        </tr>
        </thead>
        <tbody>
        @foreach($paginator->getIterator() as $item)
            <tr>
                <td>{{$item->id}}</td>
                <td>{{$item->nickname}}</td>
                <td><img class="avatar sm" src="/images/avatar/{{$item->avatar}}"></td>
                <td>{{$item->mobile}}</td>
                <td>{{$item->account}}</td>
                <td>{{$item->created_at}}</td>
                <td>
                    <a href="/admin/user/{{$item->id}}"><i class="fa fa-eye"></i></a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$paginator->links()}}
    <div style="width:150px;">
        <form method="get" class="input-group">
            <input type="number" step="1" class="form-control" name="page" value="{{$_GET['page'] ?? 1}}">
            <span class="input-group-btn">
                <button class="btn btn-default">跳转</button>
            </span>
        </form>
    </div>
    </li>
@endsection