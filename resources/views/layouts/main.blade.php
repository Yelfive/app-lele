<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-26
 */
?>
<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-25
 */
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')-后台管理系统</title>
    <link rel="stylesheet" href="/css/app.css">
    <!-- FontAwesome Styles-->
    <link href="/css/font-awesome.css" rel="stylesheet"/>
    <!-- Morris Chart Styles-->
    <link href="/js/morris/morris-0.4.3.min.css" rel="stylesheet"/>
    <!-- Custom Styles-->
    <link href="/css/custom-styles.css" rel="stylesheet"/>
    <!-- Google Fonts-->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'/>
    <link rel="stylesheet" href="/js/Lightweight-Chart/cssCharts.css">
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>

<div id="wrapper">

    <nav class="navbar navbar-default top-navbar" role="navigation">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/admin/site"><strong><i class="icon fa fa-plane"></i> Home</strong></a>

            <div id="sideNav" href="">
                {{--<i class="fa fa-bars icon"></i>--}}
            </div>
        </div>

        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                    <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    {{--<li><a href="#"><i class="fa fa-user fa-fw"></i>我的资料</a></li>--}}
                    {{--<li class="divider"></li>--}}
                    <li><a href="/admin/session/delete"><i class="fa fa-sign-out fa-fw"></i>退出登陆</a></li>
                </ul>
                <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->
        </ul>
    </nav>

    <nav class="navbar-default navbar-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav" id="main-menu">
                @foreach([
                    ['/admin/users', 'users', '用户管理'],
                    ['/admin/settings', 'gears', '系统设置'],
                ] as list($url, $icon, $label))
                    <li>
                        <a @if($url === '/' . Request::path())class="active-menu" @endif href="{{$url}}">
                            <i class="fa fa-{{$icon}}"></i>{{$label}}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </nav>

    <div id="page-wrapper">
        <div class="header">
            <h1 class="page-header">
                @yield('title')
                <small>@yield('description')</small>
            </h1>
            @hasSection('breadcrumb')
                <ol class="breadcrumb">
                    @yield('breadcrumb')
                    <li class="active">@yield('title')</li>
                </ol>
            @endif
        </div>
        <div id="page-inner">
            <div>
                @yield('content')
            </div>
            <footer>
                <p>Copyright &copy; 2017.All rights reserved. Powered by
                    <a target="_blank" href="mailto:yelfivehuang@gmail.com">Felix Huang</a>
                </p>
            </footer>
        </div>
        <!-- /. PAGE INNER  -->
    </div>
    <!-- /. PAGE WRAPPER  -->
</div>
<!-- /. WRAPPER  -->
<!-- JS Scripts-->
<!-- jQuery Js -->
<script src="/js/jquery-1.10.2.js"></script>
<!-- Bootstrap Js -->
<script src="/js/bootstrap.min.js"></script>
<script src="/js/image-preview/fk.image-preview.js"></script>

<script>
    $(function () {
        fk('#page-inner img').imagePreview();
    });
</script>
</body>
</html>

