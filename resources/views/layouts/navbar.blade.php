        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <div id="logo"></div>

                <button id="menu-toggle2" data-toggle="button" class="">
                    <i class="fa fa-bars fa-fw"></i>
                </button>

                <a class="navbar-brand" href="{{ url ('') }}">{{config('site.site_name')}}</a>
            </div>



            <!-- /.navbar-header -->
            <ul class="nav navbar-top-links navbar-right">
                <!--menu toggle button -->

                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>
                        @if(Auth::check())
                            {{Auth::user()->name}}
                        @endif
                        <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        
                        </li>
                            <li><a href="{{ url ('/account/reset') }}"><i class="fa fa-sign-out fa-fw"></i> 密碼變更</a>
                            <li><a href="{{ url ('/logout') }}"><i class="fa fa-sign-out fa-fw"></i> 登出</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>










                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->
          
            @include('layouts.sidebar',[ 'menu_open'=> $menu_open])
             
               
                <!-- /.navbar-static-side -->
        </nav>
   