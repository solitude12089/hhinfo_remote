@extends('layouts.dashboard',[
'page_title'=>'',
'menu'=>1,
'breadcrumb'=>[
'管理員功能' => '',
'設備管理' => Request::url()

]
])



@section('style')
@parent
    <link href="/css/chosen/chosen.min.css" rel="stylesheet">
    <link href="/css/jquery-ui.css" rel="stylesheet">
    <link href="/css/datatables.min.css" rel="stylesheet">
    <style>
        ul.ui-autocomplete {
            z-index: 1100;
        }
    </style>
@stop


@section('section')




<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">設備管理</h3>
        <div class="box-tools pull-right">
            <a class="btn btn-primary btn-xs" data-toggle="modal" data-target="#ajax-modal" href="/admin/device/create">建立設備</a>

        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-lg-12">
                <table class='table dataTable'>
                    <thead>
                        <tr>
                            <th>IP</th>
                            <th>內部IP</th>
                            <th>IP Mode</th>
                            <th>名稱</th>
                            <th>描述</th>
                            <th>群組</th>
                            <th>區域</th>
                            <th>模式</th>
                            <th>用途</th>
                            <th>類型</th>
                            <th>狀態</th>
                            <th>最後同步時間</th>
                            <th>Action</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($devices as $key => $device)
                        <tr>
                            <td>{{$device->ip}}</td>
                            <td>{{$device->local_ip}}</td>
                            <td>{{$device->ip_mode}}</td>
                            <td>{{$device->name}}</td>
                            <td>{{$device->description}}</td>
                            <td>{{$device->family}}</td>
                            <td>{{$groups[$device->group_id]}}</td>
                            <td>{{$device->mode}}</td>
                            <th>{{$device->style}}</th>
                            <th>{{$device->type}}</th>
                            <td>
                                @if($device->status==1)
                                    <label style="color:green">啟用</label>
                                @else
                                    <label style="color:red">關閉</label>
                                @endif
                            </td>

                            <td>{{$device->updated_at}}</td>
                            <td><button class="btn btn-primary" target_id="{{$device->id}}"  onclick="action(this)">校時</button></td>
                            <td>
                                <a data-toggle="modal" data-target="#ajax-modal" href="/admin/device/edit/{{$device->id}}">編輯</a>
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
<script src="/js/jquery-ui.js"></script>
<script src="/js/datatables.min.js"></script>

<script>
    $('.dataTable').dataTable();
    var familys = <?php echo json_encode($familys); ?>;
    function action($obj){
            $($obj).attr('disabled', true);
            var device_id = $($obj).attr('target_id');
            var url = '/remote/set-time/'+device_id;

            $.ajax({
                type: 'Get',
                url: url,
                success: function(result){
                    if(result.status==1){
                        alert('Successful.');
                    }
                    else{
                        alert('Fail.');
                    }
                    $($obj).attr('disabled', false);
                },
                error: function(){
                    alert('Fail.');
                    $($obj).attr('disabled', false);
                }
            });

            console.log(url);


    }
</script>

@stop
