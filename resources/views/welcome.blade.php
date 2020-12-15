<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway';
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 24px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
            
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @if (Auth::guest())
                    <a href="{{ route('login') }}">Login</a>
                      
                    @else
                        <a href="{{ url('/logout') }}">Logout</a>
                    @endif


                </div>
            @endif



            <div class="content">
                <div class="links" >
                    @if (Auth::check()&&Auth::user()->role==9)
                    <a  href="/admin/account">管理員功能</a>
                    @endif
                    <a  href="/customer/index">客戶管理</a>
                    <a  href="/remote/index">遠端操作</a>
                    <a  href="/booking/index">租借功能</a>
                    @if (Auth::check()&&Auth::user()->role==9)
                    <a  href="/systemlog/index">系統紀錄</a>
                    @endif
                 
                </div>
                <div style="padding: 100px;">
                    <img src="/img/index.png" width="250px"></img>
                </div>

               
            </div>
        </div>
    </body>
</html>
