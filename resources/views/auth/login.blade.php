<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>لاگ ان</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body { background: #f0f2f5; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif; }
        .login-container { max-width: 400px; margin: 60px auto; background: #fff; border-radius: 8px; box-shadow: 0 0 20px rgba(0,0,0,0.08); padding: 30px; }
        .login-title { text-align: center; margin-bottom: 25px; color: #2c3e50; font-size: 2em; }
        .form-group label { float: right; }
        .btn-block { width: 100%; }
        .alert { margin-top: 10px; }
        .login-bottom { background: #2c3e50; color: #fff; text-align: center; padding: 12px 0; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; margin-top: 20px; font-size: 1em; }
    </style>
</head>
<body>
    <div class="login-container">
    <h3 class="login-title">پروگریس پورٹل </h3>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="username">صارف نام</label>
                <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">پاسورڈ</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            <button type="submit" class="btn btn-primary btn-block">لاگ ان کریں</button>
        </form>
                    @php
                        $setting = DB::table('settings')->first();
                    @endphp
        <div class="login-bottom" dir="ltr">
                            © {{ date('Y') }} 
                            @if($setting && $setting->footer_text)
                                . {!! $setting->footer_text !!}
                            @else
                                . Revenue & Estate Department Khyber Pakhtunkhwa
                            @endif
        </div>
    </div>
</body>
</html>
