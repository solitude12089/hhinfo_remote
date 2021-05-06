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
   <link href="/css/bootstrap-dialog.min.css" rel="stylesheet">
   <style>
        .my_col{
            text-align: center;
        }
   </style>
@stop


@section('section')



<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">遠端操作</h3>
        <div class="box-tools pull-right">
            <a class="btn btn-primary btn-xs" href="/remote/sync-status">即時同步</a>

        </div>
     
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-lg-12">
                <table class='table dataTable' style="width:100%">
                @if(0)
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
                @endif
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
    <script src="/js/bootstrap-dialog.min.js"></script>
    <script>
       var table = $('.dataTable').DataTable({
            autoWidth: true,
            ajax: "/remote/devicelist",
            columns: [
                {   title: "區域",
                    data: "group.name" 
                },
                {   title: "群組",
                    data: "family" 
                },
                {   title: "名稱",
                    data: "name" 
                },
                {   title: "描述",
                    data: "description" 
                },

                {   title: "模式",
                    data: function(k,v){
                        if(k.mode =="手動"){
                            rtsv = '<button class="btn btn-xs btn-success" onclick="btn_mode_change(this)"  msgtitle="'+k.group.name+'-'+k.family+'-'+k.name+'" target_id="'+k.id+'">手動</button>';
                        }
                        else{
                            rtsv = '<button class="btn btn-xs btn-warning" onclick="btn_mode_change(this)"  msgtitle="'+k.group.name+'-'+k.family+'-'+k.name+'" target_id="'+k.id+'">預約</button>';
                        }
                        return rtsv;
                    } 
               
                },
                {   title: "用途",
                    data: "style" 
                },
                {   title: "類型",
                    data: "type" 
                },
                
               
              
                {   title: "大門控制",
                    className: "my_col",
                    data: function(k,v){
                        rtsv = '<button class="btn btn-xs btn-success" onclick="btn_click(this)" msgtitle="'+k.group.name+'-'+k.family+'-'+k.name+'" target_id="'+k.id+'" relay="1">開</button>';
                        if(k.type=='鐵捲門'){
                            rtsv = rtsv + ' <button class="btn btn-xs btn-danger" onclick="btn_click(this)" msgtitle="'+k.group.name+'-'+k.family+'-'+k.name+'" target_id="'+k.id+'" relay="2">關</button>';
                        }
                        return rtsv;
                    } 
                },
                {   title: "電燈控制",
                    className: "my_col",
                    data: function(k,v){
                        if(k.r3 =="1"){
                            rtsv = '<button class="btn btn-xs btn-success" onclick="btn_click(this)"  msgtitle="'+k.group.name+'-'+k.family+'-'+k.name+'" target_id="'+k.id+'" relay="3">開</button>';
                        }
                        else{
                            rtsv = '<button class="btn btn-xs btn-danger" onclick="btn_click(this)"  msgtitle="'+k.group.name+'-'+k.family+'-'+k.name+'" target_id="'+k.id+'" relay="3">關</button>';
                        }
                        return rtsv;
                    } 
                },
                {   
                    title: "冷氣控制",
                    className: "my_col",
                    data: function(k,v){
                        if(k.r4 =="1"){
                            rtsv = '<button class="btn btn-xs btn-success" onclick="btn_click(this)"  msgtitle="'+k.group.name+'-'+k.family+'-'+k.name+'" target_id="'+k.id+'" relay="4">開</button>';
                        }
                        else{
                            rtsv = '<button class="btn btn-xs btn-danger" onclick="btn_click(this)"  msgtitle="'+k.group.name+'-'+k.family+'-'+k.name+'" target_id="'+k.id+'" relay="4">關</button>';
                        }
                        return rtsv;
                    } 
                },
                {   title: "門位狀態",
                    className: "my_col",
                    data: function(k,v){
                        rtsv = '';
                       
                        if(k.s1 =="0"){
                            rtsv = '<button class="btn btn-xs btn-success"  target_id="'+k.id+'" relay="4">開</button>';
                        }
                        else{
                            rtsv = '<button class="btn btn-xs btn-danger" " target_id="'+k.id+'" relay="4">關</button>';
                        }
                      
                        return rtsv;
                    } 
                }
                // ,
                // {   title: "電閘狀態",
                //     className: "my_col",
                //     data: function(k,v){
                //         rtsv = '';
                //         if(k.s2 =="0"){
                //             rtsv = '<button class="btn btn-xs btn-danger" " target_id="'+k.id+'" relay="4">關</button>';
                          
                //         }
                //         else{
                //             rtsv = '<button class="btn btn-xs btn-success"  target_id="'+k.id+'" relay="4">開</button>';
                //         }
                      
                //         return rtsv;
                //     } 
                // }
              
              
            ]
        });

        setInterval( function () {
            table.ajax.reload();
        }, 5000 );

        
        



        function btn_mode_change(obj){
            var my = $(obj);
            var rname = '';
            if(my.text()=='手動'){
                rname = '預約';
            }
            else{
                rname = '手動';
            }
            
            var msg = '是否切換 "' + my.attr('msgtitle')+'" 為 "'+rname+'" 模式?'

            BootstrapDialog.confirm({
                title: '確認',
                message: msg,
                type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                closable: true, // <-- Default value is false
                draggable: true, // <-- Default value is false
                btnCancelLabel: '取消', // <-- Default value is 'Cancel',
                btnOKLabel: '確定', // <-- Default value is 'OK',
                btnOKClass: 'btn-warning', // <-- If you didn't specify it, dialog type will be used,
                callback: function(result) {
                    if(result) {
                      
                        var device_id = my.attr('target_id');
                        var url = '/remote/change-mode/'+device_id+'?mode='+rname;
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
                }
            });

            }









        function btn_click(obj){

            var my = $(obj);
            var relay = my.attr('relay');
            var ac ='';
            if(relay<'3'){
                ac = my.text()=='開'?'1':'0';
            }else{
                ac = my.text()=='開'?'0':'1';
            }
          
            var rname = '';
            var msg = '';
            if(relay=="1"){
                rname = '大門';
            }
            if(relay=="2"){
                rname = '大門';
            }
            if(relay=="3"){
                rname = '電燈';
            }
            if(relay=="4"){
                rname = '冷氣';
            }

            if(ac=='0'){
                msg = '是否關閉 '+my.attr('msgtitle')+' '+rname+' ?'; 
            }
            else{
                msg = '是否開啟 '+my.attr('msgtitle')+' '+rname+' ?'; 
            }

          
            BootstrapDialog.confirm({
                title: '確認',
                message: msg,
                type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                closable: true, // <-- Default value is false
                draggable: true, // <-- Default value is false
                btnCancelLabel: '取消', // <-- Default value is 'Cancel',
                btnOKLabel: '確定', // <-- Default value is 'OK',
                btnOKClass: 'btn-warning', // <-- If you didn't specify it, dialog type will be used,
                callback: function(result) {
                    if(result) {
                        console.log(result);
                        var device_id = my.attr('target_id');
                        var relay = my.attr('relay');
                        var url = '/remote/set-status/'+device_id+'?';
                        var ac = my.text()=='開'?'0':'1';
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
                }
            });
            // console.log(obj)
            // var device_id = $(obj).attr('target_id');
            // var relay = $(obj).attr('relay');
            // var url = '/remote/set-status/'+device_id+'?';//1=4&2=4&3=4&4=4';
            // if(relay==1||relay==2){
            //     url = url+relay+'=5';
            // }
        }

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