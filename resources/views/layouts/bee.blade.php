<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
    <meta name="description" content="考勤">
    <meta name="csrf-token" content="{{ csrf_token() }}">   
    <title>@yield('app_label') @lang('common.website.title')</title>
    <link rel="stylesheet" href="{{ config('app.asset_url') }}/css/vendor.min.css">
    <link rel="stylesheet" href="{{ config('app.asset_url') }}/css/elephant.min.css">
    <link rel="stylesheet" href="{{ config('app.asset_url') }}/css/application.min.css">
    <style type="text/css">
      body {
        font-family: "Microsoft YaHei"!important;
      }
    </style>
</head>
<body class="layout layout-header-fixed">
  <div class="layout-header">
    <div class="navbar navbar-default">
      <div class="container">
          <div class="navbar-header">
            <a class="navbar-brand navbar-brand-center" href="#">
              HR Logo
            </a>
            <button class="navbar-toggler visible-xs-block collapsed" type="button" data-toggle="collapse" data-target="#sidenav"  aria-expanded="false">
              <span class="sr-only">Toggle navigation</span>
              <span class="bars">
                <span class="bar-line bar-line-1 out"></span>
                <span class="bar-line bar-line-2 out"></span>
                <span class="bar-line bar-line-3 out"></span>
              </span>
              <span class="bars bars-x">
                <span class="bar-line bar-line-4"></span>
                <span class="bar-line bar-line-5"></span>
              </span>
            </button>
            <button class="navbar-toggler visible-xs-block collapsed" type="button" data-toggle="collapse" data-target="#navbar"  aria-expanded="false">
              <span class="sr-only">Toggle navigation</span>
              <span class="arrow-up"></span>
              <span class="ellipsis ellipsis-vertical">
                <img class="ellipsis-object" width="32" height="32" src="img/0180441436.jpg" alt="Teddy Wilson">
              </span>
            </button>
          </div>
          <div class="navbar-toggleable">
            <nav id="navbar" class="navbar-collapse collapse">
              <button class="sidenav-toggler hidden-xs" title="Collapse sidenav ( [ )" aria-expanded="false" type="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="bars">
                  <span class="bar-line bar-line-1 out"></span>
                  <span class="bar-line bar-line-2 out"></span>
                  <span class="bar-line bar-line-3 out"></span>
                  <span class="bar-line bar-line-4 in"></span>
                  <span class="bar-line bar-line-5 in"></span>
                  <span class="bar-line bar-line-6 in"></span>
                </span>
              </button>
              <ul class="nav navbar-nav navbar-right">
                <li class="visible-xs-block">
                  <h4 class="navbar-text text-center">Hi, Admin</h4>
                </li>
                <li class="dropdown hidden-xs">
                  <button class="navbar-account-btn" data-toggle="dropdown" aria-haspopup="true">
                    <img class="rounded" width="36" height="36" src="img/0180441436.jpg" alt="Teddy Wilson"> Admin
                    <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-right">
                    <li class="navbar-upgrade-version">Version: 1.0.0</li>
                    <li class="divider"></li>
                    <li><a href="#">个人信息</a></li>
                    <li><a href="#">退出</a></li>
                  </ul>
                </li>
                <li class="visible-xs-block">
                  <a href="#">
                    <span class="icon icon-user icon-lg icon-fw"></span>
                    个人信息
                  </a>
                </li>
                <li class="visible-xs-block">
                  <a href="#">
                    <span class="icon icon-power-off icon-lg icon-fw"></span>
                    退出
                  </a>
                </li>
              </ul>
            </nav>
          </div>
      </div>
    </div>
  </div>
  <div class="container">
      @yield('body')
  </div> 
<!--   <div class="layout-footer m-x-0">
      <div class="container text-center">
          <p class="m-y-sm">Copyright © {{date('Y')}}<span class="m-x-sm">SexyPhoenix</span> 开发维护</p>
      </div>
  </div> -->
  <script src="{{config('app.asset_url')}}/js/vendor.min.js"></script>
  <script src="{{config('app.asset_url')}}/js/beeapp.min.js"></script> 

  <script src="{{config('app.asset_url')}}/js/elephant.min.js"></script> 
  <script src="{{config('app.asset_url')}}/js/application.min.js"></script>
  <script type="text/javascript">
    
  </script>    
</body>
</html>