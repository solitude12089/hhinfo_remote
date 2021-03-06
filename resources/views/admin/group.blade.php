@extends('layouts.dashboard',[
'page_title'=>'',
'menu'=>1,
'breadcrumb'=>[
'管理員功能' => '',
'區域管理' => Request::url()

]
])



@section('style')
@parent
    <link href="/css/chosen/chosen.min.css" rel="stylesheet">

@stop


@section('section')




<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">區域管理</h3>
        <div class="box-tools pull-right">
            <a class="btn btn-primary btn-xs" data-toggle="modal" data-target="#ajax-modal" href="/admin/group/create">建立區域</a>

        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-lg-12">
                <table class='table dataTable'>
                    <thead>
                        <tr>
                            <th>編號</th>
                            <th>名稱</th>
                            <th>成員</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groups as $key => $value)
                        <tr>
                            <td>{{$value->id}}</td>
                            <td>{{$value->name}}</td>
                            <td>
                                @if($value->userGroupList!=null)
                                    @foreach($value->userGroupList as $k => $v)
                                        <tag class='btn btn-xs btn-info'>{{$users[$v->user_id]}}</tag>
                                    @endforeach
                                @endif
                               
                            </td>
                           
                            <td>
                                <a data-toggle="modal" data-target="#ajax-modal" href="/admin/group/edit/{{$value->id}}">編輯</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
               
            </div>
            <!-- /.box-body -->

        </div>
    </div>
    <!-- /.box-body -->


     <div class="box-footer">

       
           
               
      
    </div>

</div>


@stop




@section('script')
    <script src="/js/chosen/chosen.jquery.min.js"></script>
@stop