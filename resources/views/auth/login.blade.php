<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>لاگ ان - سیٹلمینٹ اف لینڈ ریکارڈز  دیر کلام</title>
    @php
        $setting = DB::table('settings')->first();
    @endphp
    <link rel="shortcut icon" type="image/x-icon" href="@if($setting && $setting->logo_path){{ url('assets/logo/' . $setting->logo_path) }}@else{{ url('public/notika/img/logo/logo.png') }}@endif">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #e8f5e8 0%, #2d7d2d 100%);
            font-family: 'Noto Sans Arabic', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("@if($setting && $setting->logo_path){{ url('assets/logo/' . $setting->logo_path) }}@else{{ url('public/notika/img/logo/logo.png') }}@endif") repeat;
            background-size: 50px 50px;
            opacity: 0.09;
            pointer-events: none;
        }
        .login-container {
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.1);
            padding: 40px;
            z-index: 999999999999999;
            position: relative;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-logo {
            max-width: 100px;
            height: auto;
        }
        .login-title {
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        .login-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            height: 50px;
            border-radius: 6px;
            border: 1px solid #ddd;
            padding: 0 15px;
            font-size: 16px;
        }
        .form-control:focus {
            border-color: #1877f2;
            box-shadow: 0 0 0 2px rgba(24, 119, 242, 0.2);
        }
        .btn-login {
            width: 100%;
            height: 50px;
            background: #1877f2;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-login:hover {
            background: #166fe5;
        }
        .alert {
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .login-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            body::before {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="logo-container">
                @if($setting && $setting->logo_path)
                    <img src="{{ url('assets/logo/' . $setting->logo_path) }}" alt="Logo" class="login-logo" />
                @else
                    <img src="{{ url('public/notika/img/logo/logo.png') }}" alt="Logo" class="login-logo" />
                @endif
            </div>
            
           

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <input type="text" name="username" id="username" class="form-control" placeholder="صارف نام" value="{{ old('username') }}" required autofocus dir="ltr">
                </div>

                <div class="form-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="پاسورڈ" required dir="ltr">
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <button type="submit" class="btn-login">لاگ ان کریں</button>
            </form>

            <div style="text-align: center; margin-top: 20px;">
                <button type="button" class="btn btn-link" data-toggle="modal" data-target="#forgotPasswordModal" style="color: #1877f2; text-decoration: none;">پاسورڈ بھول گئے؟</button>
            </div>

            <!-- Forgot Password Modal -->
            <div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="forgotPasswordModalLabel">پاسورڈ بھول گئے</h4>
                        </div>
                        <form id="forgotPasswordForm" method="POST" action="{{ route('forgot.password.post') }}">
                            @csrf
                            <div class="modal-body">
                                <p>اپنا ای میل درج کریں تاکہ پاسورڈ ری سیٹ کا لنک حاصل کریں</p>
                                <div id="forgotPasswordMessage"></div>
                                <div class="form-group">
                                    <input type="email" name="email" id="forgotEmail" class="form-control" placeholder="ای میل" required dir="ltr">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">منسوخ کریں</button>
                                <button type="submit" class="btn btn-primary" id="forgotSubmitBtn">لنک بھیجیں</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="login-footer">
                @php
                    $setting = DB::table('settings')->first();
                @endphp
                <p dir="ltr">
                    © {{ date('Y') }}
                    @if($setting && $setting->footer_text)
                        . {!! $setting->footer_text !!}
                    @else
                        . Revenue & Estate Department Khyber Pakhtunkhwa
                    @endif
                </p>
            </div>
        </div>
    <script>
        $(document).ready(function() {
            $('#forgotPasswordForm').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var submitBtn = $('#forgotSubmitBtn');
                var messageDiv = $('#forgotPasswordMessage');

                // Clear previous messages
                messageDiv.html('');

                // Disable submit button
                submitBtn.prop('disabled', true).text('براہ کرم انتظار کریں...');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            messageDiv.html('<div class="alert alert-success">' + response.message + '</div>');
                            form[0].reset();
                            setTimeout(function() {
                                $('#forgotPasswordModal').modal('hide');
                            }, 2000);
                        } else {
                            messageDiv.html('<div class="alert alert-danger">' + response.message + '</div>');
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON;
                        if (errors && errors.errors) {
                            var errorMsg = '';
                            for (var key in errors.errors) {
                                errorMsg += errors.errors[key][0] + '<br>';
                            }
                            messageDiv.html('<div class="alert alert-danger">' + errorMsg + '</div>');
                        } else {
                            messageDiv.html('<div class="alert alert-danger">ای میل بھیجنے میں خرابی ہوئی ہے</div>');
                        }
                    },
                    complete: function() {
                        // Re-enable submit button
                        submitBtn.prop('disabled', false).text('لنک بھیجیں');
                    }
                });
            });

            // Clear messages when modal is closed
            $('#forgotPasswordModal').on('hidden.bs.modal', function() {
                $('#forgotPasswordMessage').html('');
                $('#forgotPasswordForm')[0].reset();
            });
        });
    </script>
    </div>
</body>
</html>
