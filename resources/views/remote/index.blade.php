@extends('layouts.dashboard',[
'page_title'=>'',
'menu'=>1,
'breadcrumb'=>[
'設備管理' => '',
'遠端操作' => Request::url()

]
])



@section('style')
@parent
   <link href="/css/datatables.min.css" rel="stylesheet">

@stop


@section('section')




<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">遠端操作</h3>
        <div class="box-tools pull-right">
        

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
                            <th>名稱</th>
                            <th>描述</th>
                            <th>區域</th>
                            <th>群組</th>
                            <th>類型</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($devices as $key => $device)
                        <tr>
                            <td>{{$device->ip}}</td>
                            <td>{{$device->name}}</td>
                            <td>{{$device->description}}</td>
                            <td>{{$groups[$device->group_id]}}</td>
                             <td>{{$device->family}}</td>
                            <td>{{$device->type}}</td>
                            <td>
                                <button class="btn btn-primary" target_id="{{$device->id}}" relay="1"  action=1 onclick="action(this)">開門</button>
                                <button class="btn btn-warning" target_id="{{$device->id}}" relay="2"  action=1 onclick="action(this)">關門</button>
                                <button class="btn btn-success" target_id="{{$device->id}}" relay="3"  action=1 onclick="action(this)">一般用電:開</button>
                                <button class="btn btn-danger"  target_id="{{$device->id}}" relay="3"  action=0 onclick="action(this)">一般用電:關</button>
                                <button class="btn btn-success" target_id="{{$device->id}}" relay="4"  action=1 onclick="action(this)">冷氣用電:開</button>
                                <button class="btn btn-danger"  target_id="{{$device->id}}" relay="4"  action=0 onclick="action(this)">冷氣用電:關</button>
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
    <script>
        $('.dataTable').dataTable();

        function action($obj){
            $($obj).attr('disabled', true);
            var device_id = $($obj).attr('target_id');
            var relay = $($obj).attr('relay');
            var ac = $($obj).attr('action');
            var url = '/remote/set-status/'+device_id+'?';//1=4&2=4&3=4&4=4';
            if(relay==1||relay==2){
                url = url+relay+'=5';
            }
            else{
                if(ac==1){
                    url = url+relay+'=255';
                }
                else{
                    url = url+relay+'=0';
                }
            }

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