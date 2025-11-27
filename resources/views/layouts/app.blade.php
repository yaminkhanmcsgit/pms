<!DOCTYPE html>
<html lang="ur" dir="">
 @php
 $setting = DB::table('settings')->first();
 @endphp
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Land Record System')</title>
    <link rel="shortcut icon" type="image/x-icon" href="@if($setting && $setting->logo_path){{ url('assets/logo/' . $setting->logo_path) }}@else{{ url('public/notika/img/logo/logo.png') }}@endif">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
   <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu&display=swap" rel="stylesheet">


    <!-- Notika CSS -->
    <link rel="stylesheet" href="{{ url('public/notika/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/notika/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/notika/css/owl.carousel.css') }}">
    <link rel="stylesheet" href="{{ url('public/notika/css/owl.theme.css') }}">
    <link rel="stylesheet" href="{{ url('public/notika/css/owl.transitions.css') }}">
    <link rel="stylesheet" href="{{ url('public/notika/css/meanmenu/meanmenu.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/notika/css/animate.css') }}">
    <link rel="stylesheet" href="{{ url('public/notika/css/normalize.css') }}">
    <link rel="stylesheet" href="{{ url('public/notika/css/scrollbar/jquery.mCustomScrollbar.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/notika/css/jvectormap/jquery-jvectormap-2.0.3.css') }}">
    <link rel="stylesheet" href="{{ url('public/notika/css/notika-custom-icon.css') }}">
    <link rel="stylesheet" href="{{ url('public/notika/css/wave/waves.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/notika/css/main.css') }}">
    <link rel="stylesheet" href="{{ url('public/notika/style.css') }}">
    <link rel="stylesheet" href="{{ url('public/notika/css/responsive.css') }}">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
     <script src="{{ url('public/notika/js/vendor/jquery-1.12.4.min.js') }}"></script>
    <!-- Modernizr JS -->
    <script src="{{ url('public/notika/js/vendor/modernizr-2.8.3.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
    <!-- Urdu Transliteration JS (UrduWriter) -->
    <script src="https://cdn.jsdelivr.net/gh/urduwriter/urduwriter@master/urduwriter.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.UrduWriter) {
            UrduWriter.enable('.urdu-input');
        }
        // Force lang and font for all .urdu-input fields
        var urduInputs = document.querySelectorAll('.urdu-input');
        urduInputs.forEach(function(el) {
            el.setAttribute('lang', 'ur');
            el.style.fontFamily = "'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif";
            el.style.direction = 'rtl';
        });
    });
    </script>

    <style>
        body {
  background: #f0f2f5;
  font-family: 'Noto Nastaliq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', serif;
  padding-bottom: 60px;
}

.form-group {
  float: right;
}

/* Urdu input fields */
.urdu-input, .urdu-input * {
  font-family: 'Noto Nastaliq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', serif !important;
  direction: rtl !important;
  unicode-bidi: embed !important;
}

.urdu-input,
.urdu-input *,
.dropdown-menu,
td,
th,
select,
option {
  line-height: 2.2em !important;
  vertical-align: middle !important;
}
select,
option {
  font-family: 'Noto Nastaliq Urdu', 'Jameel Noori Nastaleeq', serif !important;
  direction: rtl;
  text-align: right;
  font-size: 0.7rem !important;       /* Smaller font */
  line-height: 1.6em !important;      /* Adequate height */
  padding-top: 2px !important;
  padding-bottom: 2px !important;
  height: auto !important;
  vertical-align: middle;
}
ul.notika-menu-wrap li a {
    
    padding: 15px 13px !important;
    
}




        /* Center table headings and values */
        table th, table td {
            text-align: center !important;
            padding: 3px !important;
        }
        table td {
            white-space: nowrap;
        }
        .footer-copyright-area {
            position: fixed;
            bottom: 0;
            width: 100%;
            z-index: 1000;
        }
        @media (max-width: 700px) {
            .dashboard-panels {
                flex-direction: column !important;
            }
            .dashboard-panels > div {
                width: 100% !important;
                margin-bottom: 10px !important;
            }
        }

        .mean-container .mean-bar::after {

    content: "";

}

@media print {
    a, button , .dashboard-panels .panel { display: none !important; }
    body, html { width: 100%; margin: 0; padding: 0; }
    .container { width: 100% !important; max-width: none !important; margin: 0 !important; padding: 0 !important; }
}
.container{width:100% !important;}



    </style>

    
</head>

<body>
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    
    <!-- Combined Header and Menu Area -->
    <div class="header-menu-area" style="background-color: #5cb85c;">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-6">
                   
                    <div class="logo-area" style="padding:0">
                        <a href="#">
                            @if($setting && $setting->logo_path)
                                <img src="{{ url('assets/logo/' . $setting->logo_path) }}" alt="Logo" style="max-height:60px;" />
                            @else
                                <img src="{{ url('public/notika/img/logo/logo.png') }}" alt="Logo" style="max-height:60px;" />
                            @endif
                        </a>
                    </div>
                </div>
                <div class="col-lg-7 col-md-7 hidden-xs hidden-sm">
                    <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro rtl-menu justify-content-center">
                        <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}">
                                <i class="notika-icon notika-house"></i> ڈیش بورڈ
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('operators.*') ? 'active' : '' }}">
                            <a href="{{ route('operators.index') }}">
                                <i class="notika-icon notika-support"></i> صارفین
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                            <a href="{{ route('employees.index') }}">
                                <i class="notika-icon notika-social"></i> ملازمین
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('completion_process.*') ? 'active' : '' }}">
                            <a href="{{ route('completion_process.index') }}">
                                <i class="notika-icon notika-edit"></i> تکمیلی کام
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('partal.*') ? 'active' : '' }}">
                            <a href="{{ route('partal.index') }}">
                                <i class="notika-icon notika-form"></i> پڑتال
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('grievances.*') ? 'active' : '' }}">
                            <a href="{{ route('grievances.index') }}">
                                <i class="notika-icon notika-flag"></i> شکایات
                            </a>
                        </li>
                        <li class="dropdown desktop-dropdown-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="notika-icon notika-more"></i> دیگر <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="{{ request()->routeIs('reports') ? 'active' : '' }}">
                                    <a href="{{ route('reports') }}">
                                        <i class="notika-icon notika-bar-chart"></i> رپورٹس
                                    </a>
                                </li>
                                @if(session('role_id') == 1)
                                <li class="{{ request()->routeIs('contactus.*') ? 'active' : '' }}">
                                    <a href="{{ route('contactus.index') }}">
                                        <i class="notika-icon notika-mail"></i> رابطہ
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('news.*') ? 'active' : '' }}">
                                    <a href="{{ route('news.index') }}">
                                        <i class="notika-icon notika-form"></i> خبریں
                                    </a>
                                </li>
                                @endif
                                <li class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                                    <a href="{{ route('settings.edit') }}">
                                        <i class="fa fa-gear"></i> ترتیبات
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                    <div class="header-top-menu">
                        <ul class="nav navbar-nav notika-top-nav justify-content-end justify-content-center justify-content-lg-end">
                            <li class="nav-item nc-al">
                                <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle">
                                    <span><i class="notika-icon notika-alarm"></i></span>
                                    <div class="spinner4 spinner-4" style="top:-2px;left:32px;"></div>
                                    <div class="ntd-ctn" style="top:3px;left:39px;"><span>3</span></div>
                                </a>
                                <div role="menu" class="dropdown-menu message-dd notification-dd animated zoomIn">
                                    <div class="hd-mg-tt">
                                        <h2>Notification</h2>
                                    </div>
                                    <div class="hd-message-info">
                                        <!-- Notifications list -->
                                        <a href="#">
                                            <div class="hd-message-sn">
                                                <div class="hd-message-img">
                                                    <img src="{{ url('public/notika/img/post/1.jpg') }}" alt="" />
                                                </div>
                                                <div class="hd-mg-ctn">
                                                    <h3>David Belle</h3>
                                                    <p>Cum sociis natoque penatibus et magnis dis parturient montes</p>
                                                </div>
                                            </div>
                                        </a>
                                        <!-- More notifications -->
                                    </div>
                                    <div class="hd-mg-va">
                                        <a href="#">View All</a>
                                    </div>
                                </div>
                            </li>
                            @if(Session::has('operator_id'))
                            <li class="nav-item dropdown">
                                <a href="#" id="profileDropdown" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle">
                                    <span><i class="fa fa-user-circle"></i></span>
                                    <span style="margin-right:8px;">{{ Session::get('operator_name') }}</span>
                                </a>
                                <div role="menu" class="dropdown-menu dropdown-menu-right animated fadeIn" style="min-width:180px; padding:15px;">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-block">لاگ آوٹ</button>
                                    </form>
                                    <hr>
                                    <a href="#" class="btn btn-info btn-block" onclick="$('#changePassModal').modal('show');return false;">پاسورڈ تبدیل کریں</a>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Combined Header and Menu Area -->

    <!-- Mobile Menu start -->
    <div class="mobile-menu-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="mobile-menu">
                        <nav id="dropdown">
                            <ul class="mobile-menu-nav">
                              <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a href="{{ route('dashboard') }}">
            <i class="notika-icon notika-house"></i> ڈیش بورڈ
        </a>
    </li>
    <li class="{{ request()->routeIs('operators.*') ? 'active' : '' }}">
        <a href="{{ route('operators.index') }}">
            <i class="notika-icon notika-support"></i> صارفین
        </a>
    </li>
    <li class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
        <a href="{{ route('employees.index') }}">
            <i class="notika-icon notika-social"></i> ملازمین
        </a>
    </li>
    <li class="{{ request()->routeIs('completion_process.*') ? 'active' : '' }}">
        <a href="{{ route('completion_process.index') }}">
            <i class="notika-icon notika-edit"></i> تکمیلی کام
        </a>
    </li>
    <li class="{{ request()->routeIs('partal.*') ? 'active' : '' }}">
        <a href="{{ route('partal.index') }}">
            <i class="notika-icon notika-form"></i> پڑتال
        </a>
    </li>
    <li class="{{ request()->routeIs('grievances.*') ? 'active' : '' }}">
        <a href="{{ route('grievances.index') }}">
            <i class="notika-icon notika-flag"></i> شکایات
        </a>
    </li>
                        <li class="{{ request()->routeIs('reports') ? 'active' : '' }}">
                            <a href="{{ route('reports') }}">
                                <i class="notika-icon notika-bar-chart"></i> رپورٹس
                            </a>
                        </li>
                        @if(session('role_id') == 1)
                        <li class="{{ request()->routeIs('contactus.*') ? 'active' : '' }}">
                            <a href="{{ route('contactus.index') }}">
                                <i class="notika-icon notika-mail"></i> رابطہ
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('news.*') ? 'active' : '' }}">
                            <a href="{{ route('news.index') }}">
                                <i class="notika-icon notika-newspaper"></i> خبریں
                            </a>
                        </li>
                        @endif
                   
                         <li class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                                        <a href="{{ route('settings.edit') }}">
                                               <i class="fa fa-gear"></i>  ترتیبات
                                        </a>
                         </li>
                                <!-- More mobile menu items -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mobile Menu end -->

<style>
/* Force right-to-left for Notika menu */
.rtl-menu {
    direction: rtl !important;
    display: flex !important;
    justify-content: flex-end !important;
}

.rtl-menu li {
    float: right !important;
    margin-left: 10px; /* space between items */
}

.rtl-menu li a {
    text-align: right !important;
    color: white !important;
    white-space: nowrap !important;
}

.rtl-menu li a i {
    color: white !important;
}

/* Active tab styling */
.rtl-menu li.active a {
    font-weight: bold;
    background: none !important;
    padding: 15px 10px !important;
}

/* Dropdown submenu styling */
.desktop-dropdown-menu .dropdown-menu li {
    display: block !important;
    width: 100%;
}

.desktop-dropdown-menu .dropdown-menu li a {
    color: #333 !important;
}

.desktop-dropdown-menu .dropdown-menu li a i {
    color: #333 !important;
}
</style>



<!-- Main Menu area End-->

<!-- Main Menu area End-->

    <!-- Main Menu area End-->
    
        <!-- Main Content -->
        <div class="content">
            <br><br>
                @yield('content')
        </div>
        <!-- Main Content End -->


    <!-- Change Password Modal (Bootstrap 3 compatible, at end of body) -->
    <div class="modal fade" id="changePassModal" tabindex="-1" role="dialog" aria-labelledby="changePassModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="row1">
                <form method="POST" action="{{ url('/change-password') }}">
                    @csrf
                    <div class="modal-header" style="background:#2c3e50; color:#fff;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="changePassModalLabel">پاسورڈ تبدیل کریں</h4>
                    </div>
                    <div class="modal-body">
                        <div id="changePassMsg"></div>
                        <div class="form-group col-md-12" style="margin-bottom:20px;">
                            <label for="old_password">پرانا پاسورڈ</label><br>
                            <input type="password" name="old_password" id="old_password" class="form-control" required>
                        </div>
                        <div class="form-group col-md-12" style="margin-bottom:20px;">
                            <label for="new_password">نیا پاسورڈ</label><br>
                            <input type="password" name="new_password" id="new_password" class="form-control" required>
                        </div>
                        <div class="form-group col-md-12" style="margin-bottom:0;">
                            <label for="confirm_password">تصدیق پاسورڈ</label><br>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer" style="text-align:right;">
                        <div class="col-md-12">
                        <button type="button" class="btn btn-default" data-dismiss="modal">بند کریں</button>
                        <button type="submit" class="btn btn-primary">محفوظ کریں</button>
                     </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Start Footer area-->
    <div class="footer-copyright-area" dir="ltr" style="background:#d4edda;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="footer-copy-right" >
                        <p style="color:#333;">
                            © {{ date('Y') }}
                            @if($setting && $setting->footer_text)
                                . {!! $setting->footer_text !!}
                            @else
                                . Revenue & Estate Department Khyber Pakhtunkhwa
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Footer area-->
    
    <!-- jquery -->
   
    <!-- bootstrap JS -->
    <script src="{{ url('public/notika/js/bootstrap.min.js') }}"></script>
    <!-- wow JS -->
    <script src="{{ url('public/notika/js/wow.min.js') }}"></script>
    <!-- price-slider JS -->
    <script src="{{ url('public/notika/js/jquery-price-slider.js') }}"></script>
    <!-- owl.carousel JS -->
    <script src="{{ url('public/notika/js/owl.carousel.min.js') }}"></script>
    <!-- scrollUp JS -->
    <script src="{{ url('public/notika/js/jquery.scrollUp.min.js') }}"></script>
    <!-- meanmenu JS -->
    <script src="{{ url('public/notika/js/meanmenu/jquery.meanmenu.js') }}"></script>
    <!-- counterup JS -->
    <script src="{{ url('public/notika/js/counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ url('public/notika/js/counterup/waypoints.min.js') }}"></script>
    <script src="{{ url('public/notika/js/counterup/counterup-active.js') }}"></script>
    <!-- mCustomScrollbar JS -->
    <script src="{{ url('public/notika/js/scrollbar/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <!-- jvectormap JS -->
    <script src="{{ url('public/notika/js/jvectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ url('public/notika/js/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ url('public/notika/js/jvectormap/jvectormap-active.js') }}"></script>
    <!-- sparkline JS -->
    <script src="{{ url('public/notika/js/sparkline/jquery.sparkline.min.js') }}"></script>
    <script src="{{ url('public/notika/js/sparkline/sparkline-active.js') }}"></script>
    <!-- flot JS -->
    <script src="{{ url('public/notika/js/flot/jquery.flot.js') }}"></script>
    <script src="{{ url('public/notika/js/flot/jquery.flot.resize.js') }}"></script>
    <script src="{{ url('public/notika/js/flot/curvedLines.js') }}"></script>
    <script src="{{ url('public/notika/js/flot/flot-active.js') }}"></script>
    <!-- knob JS -->
    <script src="{{ url('public/notika/js/knob/jquery.knob.js') }}"></script>
    <script src="{{ url('public/notika/js/knob/jquery.appear.js') }}"></script>
    <script src="{{ url('public/notika/js/knob/knob-active.js') }}"></script>
    <!-- wave JS -->
    <script src="{{ url('public/notika/js/wave/waves.min.js') }}"></script>
    <script src="{{ url('public/notika/js/wave/wave-active.js') }}"></script>
    <!-- todo JS -->
    <script src="{{ url('public/notika/js/todo/jquery.todo.js') }}"></script>
    <!-- plugins JS -->
    <script src="{{ url('public/notika/js/plugins.js') }}"></script>
    <!-- Chat JS -->
    <script src="{{ url('public/notika/js/chat/moment.min.js') }}"></script>
    <script src="{{ url('public/notika/js/chat/jquery.chat.js') }}"></script>
    <!-- main JS -->
    <script src="{{ url('public/notika/js/main.js') }}"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <!-- tawk chat JS -->

    <!-- Global AJAX Loading Overlay -->
    <div id="globalAjaxLoader" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; text-align: center;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.3);">
            <div class="ajax-spinner" style="width: 40px; height: 40px; margin: 0 auto;">
                <div class="double-bounce1"></div>
                <div class="double-bounce2"></div>
            </div>
            <div style="margin-top: 15px; font-size: 16px; color: #333; font-family: 'Noto Nastaliq Urdu', serif;">
                براہ کرم انتظار کریں...
            </div>
        </div>
    </div>

    <style>
    .ajax-spinner {
        position: relative;
    }
    .ajax-spinner .double-bounce1, .ajax-spinner .double-bounce2 {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background-color: #337ab7;
        opacity: 0.6;
        position: absolute;
        top: 0;
        left: 0;
        animation: sk-bounce 2.0s infinite ease-in-out;
    }
    .ajax-spinner .double-bounce2 {
        animation-delay: -1.0s;
    }
    @keyframes sk-bounce {
        0%, 80%, 100% {
            transform: scale(0);
            opacity: 0.5;
        }
        40% {
            transform: scale(1);
            opacity: 1;
        }
    }
    </style>

    <script>
    $(function() {
        // Global AJAX setup for loading indicator
        var ajaxCallCount = 0;

        $(document).ajaxStart(function() {
            ajaxCallCount++;
            if (ajaxCallCount > 0) {
                $('#globalAjaxLoader').fadeIn(200);
            }
        });

        $(document).ajaxStop(function() {
            ajaxCallCount--;
            if (ajaxCallCount <= 0) {
                ajaxCallCount = 0;
                $('#globalAjaxLoader').fadeOut(200);
            }
        });

        // Handle change password form
        $('#changePassModal form').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $msg = $('#changePassMsg');
            $msg.html('');
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                headers: {'X-CSRF-TOKEN': $('input[name="_token"]', $form).val()},
                success: function(res) {
                    if(res.success) {
                        $msg.html('<div class="alert alert-success" style="text-align:right;">' + res.success + '</div>');
                        $form[0].reset();
                    } else if(res.errors) {
                        var html = '<div class="alert alert-danger" style="text-align:right;"><ul style="margin:0; padding-right:18px;">';
                        $.each(res.errors, function(i, err) {
                            html += '<li>' + err + '</li>';
                        });
                        html += '</ul></div>';
                        $msg.html(html);
                    }
                },
                error: function(xhr) {
                    var res = xhr.responseJSON;
                    if(res && res.errors) {
                        var html = '<div class="alert alert-danger" style="text-align:right;"><ul style="margin:0; padding-right:18px;">';
                        $.each(res.errors, function(i, err) {
                            html += '<li>' + err + '</li>';
                        });
                        html += '</ul></div>';
                        $msg.html(html);
                    } else {
                        $msg.html('<div class="alert alert-danger" style="text-align:right;">سرور کی خرابی</div>');
                    }
                }
            });
        });
    });
    </script>

    @stack('scripts')
<script>
// Helper: Populate a dropdown with options
function populateDropdown(dropdown, items, valueKey, textKey, selectedValue) {
    console.log('Populating dropdown:', dropdown.id, 'with', items.length, 'items. Selected value:', selectedValue);
    dropdown.innerHTML = '<option value="">منتخب کریں</option>';
    items.forEach(function(item) {
        var selected = selectedValue && item[valueKey] == selectedValue ? 'selected' : '';
        if (selected) {
            console.log('Pre-selected:', dropdown.id, 'value:', item[valueKey], 'text:', item[textKey]);
        }
        dropdown.innerHTML += `<option value="${item[valueKey]}" ${selected}>${item[textKey]}</option>`;
    });
   
   
}    
    var districtDropdown = document.getElementById('zila_id');
    var tehsilDropdown = document.getElementById('tehsil_id');
    var mozaDropdown = document.getElementById('moza_id');
    // Debug: log initial selected values
    if (districtDropdown) {
        console.log('Initial district selected:', districtDropdown.getAttribute('data-selected'));
    }
    if (tehsilDropdown) {
        console.log('Initial tehsil selected:', tehsilDropdown.getAttribute('data-selected'));
    }
    if (mozaDropdown) {
        console.log('Initial moza selected:', mozaDropdown.getAttribute('data-selected'));
    }

// District change handler: fetch tehsils for selected district
function onDistrictChange(districtId, tehsilDropdownId, selectedTehsilId) {
    var tehsilDropdown = document.getElementById(tehsilDropdownId);
    if (!districtId) {
        populateDropdown(tehsilDropdown, [], 'tehsil_id', 'tehsilNameUrdu');
        return;
    }
    fetch("{{ url('api/tehsils') }}?district_id=" + districtId)
        .then(res => res.json())
        .then(data => {
            populateDropdown(tehsilDropdown, data, 'tehsil_id', 'tehsilNameUrdu', selectedTehsilId);
            // Optionally trigger tehsil change if editing
            if (typeof onTehsilChange === 'function') {
                onTehsilChange(tehsilDropdown.value, 'moza_id');
            }
        });
}

// Tehsil change handler: fetch mozas for selected tehsil
function onTehsilChange(tehsilId, mozaDropdownId, selectedMozaId) {
    var mozaDropdown = document.getElementById(mozaDropdownId);
    if (!tehsilId) {
        populateDropdown(mozaDropdown, [], 'moza_id', 'mozaNameUrdu');
        return;
    }
    fetch("{{ url('api/mozas') }}?tehsil_id=" + tehsilId)
        .then(res => res.json())
        .then(data => {
            populateDropdown(mozaDropdown, data, 'moza_id', 'mozaNameUrdu', selectedMozaId);
        });
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fade out alerts
    var alerts = document.querySelectorAll('.alert-success, .alert-danger');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() { alert.style.display = 'none'; }, 500);
        }, 3000);
    });

    // Populate district dropdowns on page load
    var districtDropdown = document.getElementById('zila_id');
    var tehsilDropdown = document.getElementById('tehsil_id');
    var mozaDropdown = document.getElementById('moza_id');
    if (districtDropdown) {
        fetch("{{ url('api/districts') }}")
            .then(res => res.json())
            .then(data => {
                var selectedDistrict = districtDropdown.getAttribute('data-selected') || districtDropdown.value;
                populateDropdown(districtDropdown, data, 'zila_id', 'zilaNameUrdu', selectedDistrict);
                // Cascade: populate tehsils
                if (selectedDistrict && tehsilDropdown) {
                    var selectedTehsil = tehsilDropdown.getAttribute('data-selected') || tehsilDropdown.value;
                    fetch("{{ url('api/tehsils') }}?district_id=" + selectedDistrict)
                        .then(res => res.json())
                        .then(tehsils => {
                            populateDropdown(tehsilDropdown, tehsils, 'tehsil_id', 'tehsilNameUrdu', selectedTehsil);
                            // Cascade: populate mozas
                            if (selectedTehsil && mozaDropdown) {
                                var selectedMoza = mozaDropdown.getAttribute('data-selected') || mozaDropdown.value;
                                fetch("{{ url('api/mozas') }}?tehsil_id=" + selectedTehsil)
                                    .then(res => res.json())
                                    .then(mozas => {
                                        populateDropdown(mozaDropdown, mozas, 'moza_id', 'mozaNameUrdu', selectedMoza);
                                    });
                            }
                        });
                }
            });
    }
});
</script>
</body>
</html>