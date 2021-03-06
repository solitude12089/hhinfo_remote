@extends('layouts.dashboard',[
'page_title'=>'',
'menu'=>1,
'breadcrumb'=>[
'客戶管理' => '',
'客戶列表' => Request::url()

]
])



@section('style')
@parent
    <link href="/css/datatables.min.css" rel="stylesheet">
    <link href="/css/bootstrap-dialog.min.css" rel="stylesheet">
    <link href="/css/chosen/chosen.min.css" rel="stylesheet">
@stop


@section('section')




<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">客戶列表</h3>
        <div class="box-tools pull-right">
            <a class="btn btn-warning btn-xs" href="/customer/index?show_disable=1">顯示關閉客戶</a>
            <a class="btn btn-primary btn-xs" data-toggle="modal" data-target="#ajax-modal" href="/customer/create">新增客戶</a>

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
                            <th>卡號</th>
                            <th>狀態</th>
                            <th>最後修改者</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $key => $customer)
                        <tr>
                            <td>{{$customer->phone}}</td>
                            <td>{{$customer->name}}</td>
                            <td>
                                @if(count($customer->cardList)!=0)
                                    @foreach($customer->cardList as $key => $card)
                                        @if($card->card_uuid!="")
                                        <button class="btn btn-info" >{{$card->card_uuid}}</button>
                                        @endif
                                       
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if($customer->status==1)
                                    <label style="color:green">啟用</label>
                                @else
                                    <label style="color:red">關閉</label>
                                @endif
                            </td>

                            <td>
                                @if($customer->last_update_user!=null)
                                    {{$customer->last_update_user->name}}
                                @endif
                            </td>
                           
                            <td>
                                <a data-toggle="modal" data-target="#ajax-modal" href="/customer/{{$customer->id}}/edit">編輯</a>
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
    <script src="/js/bootstrap-dialog.min.js"></script>
    <script>
        $('.dataTable').dataTable();
    </script>

@stop