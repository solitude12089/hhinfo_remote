@extends('layouts.dashboard',[
'page_title'=>'',
'menu'=>1,
'breadcrumb'=>[
'管理員功能' => '',
'帳號管理' => Request::url()

]
])



@section('style')
@parent
    <link href="/css/chosen/chosen.min.css" rel="stylesheet">
    <link href="/css/datatables.min.css" rel="stylesheet">
@stop


@section('section')




<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">帳號管理</h3>
        <div class="box-tools pull-right">
            <a class="btn btn-primary btn-xs" data-toggle="modal" data-target="#ajax-modal" href="/admin/account/create">建立帳號</a>

        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-lg-12">
                <table class='table dataTable'>
                    <thead>
                        <tr>
                            <th>帳號</th>
                            <th>姓名</th>
                            <th>權限</th>
                            <th>區域</th>
                            <th>狀態</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $key => $value)
                        <tr>
                            <td>{{$value->account}}</td>
                            <td>{{$value->name}}</td>
                            <td>{{$roles[$value->role]}}</td>
                            <td>
                                @if($value->userGroupList!=null)
                                    @foreach($value->userGroupList as $k => $v)
                                        <tag class='btn btn-xs btn-success'>{{$groups[$v->group_id]}}</tag>
                                       
                                    @endforeach
                                @endif
                               
                            </td>
                            <td>
                                @if($value->status==1)
                                    <label style="color:green">啟用</label>
                                @else
                                    <label style="color:red">關閉</label>
                                @endif
                            </td>
                            <td>
                                <a data-toggle="modal" data-target="#ajax-modal" href="/admin/account/edit/{{$value->id}}">編輯</a>
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
    <script src="/js/datatables.min.js"></script>
    <script>
         $('.dataTable').dataTable();
    </script>
@stop