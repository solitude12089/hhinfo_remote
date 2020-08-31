@extends('layouts.plane')

@section('body')
{{-- module --}}
<div id="ajax-modal" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
         <div class="modal-content">
         </div>
    </div>
</div>

<div id="wrapper" class="{{ (!isset($menu)||$menu) ? '' : 'sidebar-disable'}} {{isset($menu_open)&&$menu_open ? 'toggled':''}}">
        
        {{-- 頁首 --}}
        @include('layouts.navbar',[
            'menu_open'=> isset($menu_open)&&$menu_open ? 1 : 0
            ])

        <div id="page-wrapper">
            <a id="btn-prevpage" 
            @if(isset($prevpage)&&$prevpage!=false)
            href="{{$prevpage}}"
            @else
            href="javascript:window.history.back();"
            @endif
            ><i class="fa fa-arrow-left"></i></a>
            @if(isset($breadcrumb))
            <ol class="breadcrumb">
                <i class="fa fa-dashboard"></i>&nbsp;
                    <li ><a href="/">Dashboard</a></li>
                    
                    @foreach($breadcrumb as $key=>$val) 
                        @if(is_int($key))
                            <li class='active'>{{$val}}</li>
                        @else
                            @if(is_array($val))
                            <li class='active'><span class='{{$val['class'] or ''}}'>
                                @if(isset($val['icon']))
                                <i class="fa {{$val['icon'] or ''}}"></i>&nbsp;
                                @endif
                                {{$key}}</span></li>
                            @else 
                            <li ><a href="{{$val}}">{{$key}}</a></li>
                            @endif
                        @endif
                    @endforeach
                      
            </ol>
            @endif


	   <div class="container-fluid" style="padding-top: 10px">
            @if((isset($page_title)&&$page_title)||(isset($synctime)&&$synctime))
            <div class="row" style="margin-top: 0px; margin-bottom:0px;">
               
                <div class="col-xs-12" style="">
                    
                <div style="overflow: auto;position: relative;">

                
                <h1 class="page-title" style="display: inline-block;">{{$page_title or ''}}</h1>
                

                <p class="pull-right" style="position:absolute;bottom:0;right:0;margin-bottom: 5px">{{ isset($synctime)&&$synctime ? 'Last sync:'.$synctime : ''}}</p>

                </div>
                
                </div>
                
            </div>
            @endif
            @if(trim($__env->yieldContent('toolbar_left'))||trim($__env->yieldContent('toolbar_right')))
            <div class="row">
                <div class="col-xs-12" style="margin-bottom: 10px;">
                    <div class="toolbar" style="background: rgba(0,0,0,0.05);padding: 10px;margin:5px 0px 10px 0px;">
                        <span>
                            @yield('toolbar_left')
                        </span>
                        <div class="pull-right">
                            @yield('toolbar_right')
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
			@endif

            {{-- 主要內容--}}
			@yield('section')

            
            </div>
            
        </div>{{-- /#page-wrapper --}}

</div>{{-- /#wrapper --}}
@stop

