<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>登陆</title>
    <link rel="stylesheet" href="css/app.css">
    {{--<link rel="stylesheet" href="css/bootstrap.css">--}}
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
<form action="admin/session" method="post">
    <div class="title">后台管理系统</div>
    <div>
        <div class="item">
            {{ csrf_field() }}
            <input type="text" name="username" placeholder="用户名" class="form-control">
            @if (isset($errors))
                <span>{{implode(',', $errors->get('username'))}}</span>
            @endif
        </div>
        <div class="item">
            <input type="password" name="password" placeholder="密码" class="form-control">
            @if (isset($errors))
                <span>{{implode(',', $errors->get('password'))}}</span>
            @endif
        </div>
    </div>
    <div>
        <button class="btn btn-block btn-danger">登陆</button>
    </div>
</form>
</body>
</html>