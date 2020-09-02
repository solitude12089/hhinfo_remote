@extends('layouts.dashboard',[
'page_title'=>'',
'menu'=>1,
'breadcrumb'=>[
'客戶管理' => '',
'全區卡設定' => Request::url()

]
])



@section('style')
@parent
    <link href="/css/datatables.min.css" rel="stylesheet">

@stop


@section('section')




<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">全區卡設定</h3>
        <div class="box-tools pull-right">
            <a class="btn btn-primary btn-xs" data-toggle="modal" data-target="#ajax-modal" href="/customer/create-spcard">新增規則</a>

        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-lg-12">
                <table class='table dataTable'>
                    <thead>
                        <tr>
                            <th>電話</th>
                            <th>名稱</th>
                            <th>區域</th>
                            <th>群組</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                      
                        @foreach($spcards as $key => $spcard)
                        <tr>
                            <td>{{$spcard->customer->phone}}</td>
                            <td>{{$spcard->customer->name}}</td>
                            <td>{{$spcard->group->name}}</td>
                            <td>
                                @foreach($spcard->family as $key => $value)
                                    <tag class='btn btn-xs btn-success'>{{$value}}</tag>
                                @endforeach
                            </td>
                            <td>
                                <a data-toggle="modal" data-target="#ajax-modal" href="/customer/edit-spcard/{{$spcard->id}}">編輯</a>
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
    <script src="/js/datatables.min.js"></script>



@stop