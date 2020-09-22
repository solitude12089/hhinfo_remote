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
    <link href="/css/bootstrap-dialog.min.css" rel="stylesheet">
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
                                <a class="btn btn-info btn-xs" data-toggle="modal" data-target="#ajax-modal" href="/customer/edit-spcard/{{$spcard->id}}">編輯</a>
                                <button class="btn btn-danger btn-xs" value="{{$spcard->id}}" onclick="remove(this)" >刪除</button>
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


    
    <form id='remove_from' action="{{url('/customer/remove-spcard')}}" method="POST" enctype="multipart/form-data" hidden>
            <input id="remove_id" name='remove_id'>
    </form>
  

</div>


@stop




@section('script')
    <script src="/js/datatables.min.js"></script>
    <script src="/js/bootstrap-dialog.min.js"></script>
    <script>
    
        function remove(obj){
            console.log(obj);
            var name = $($(obj).parents('tr').children('td')[1]).text();
            BootstrapDialog.confirm({
                title: '確認刪除',
                message: '是否刪除 使用者 : '+name+' 全區卡設定?',
                type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                closable: true, // <-- Default value is false
                draggable: true, // <-- Default value is false
                btnCancelLabel: '取消', // <-- Default value is 'Cancel',
                btnOKLabel: '確定', // <-- Default value is 'OK',
                btnOKClass: 'btn-warning', // <-- If you didn't specify it, dialog type will be used,
                callback: function(result) {
                    console.log(result);
                    // result will be true if button was click, while it will be false if users close the dialog directly.
                    if(result) {
                        $('#remove_id').val($(obj).val());
                        $('#remove_from').submit();
                        
                    }
                }
            });

        }

    
    
    
    
    
    </script>


@stop