@extends('layouts.dashboard',[
'page_title'=>'',
'menu'=>1,
'breadcrumb'=>[
'客戶管理' => url('/customer/index'),
'客戶紀錄' => Request::url()

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
        <h3 class="box-title">{{$customer->phone.' - '.$customer->name}} 租借紀錄</h3>
       
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-lg-12">
                <table class='table dataTable'>
                    <thead>
                        <tr>
                            <th>租借日期</th>
                            <th>租借地點</th>
                            <th>租借時段</th>
                            <th>備註</th>
                            <th>是否租借冷氣</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rt_data as $key => $data)
                        <tr>
                            <td>{{$data[3]}}</td>
                            <td>{{$data[2]}}</td>
                            <td>{{$data[4]}}</td>
                            <td>{{$data[5]}}</td>
                            <td>{{$data[6]}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
               
            </div>
            <!-- /.box-body -->

        </div>
    </div>
    <!-- /.box-body -->


   

</div>


@stop




@section('script')
    <script src="/js/chosen/chosen.jquery.min.js"></script>
    <script src="/js/datatables.min.js"></script>
    <script src="/js/bootstrap-dialog.min.js"></script>
    <script>
        $('.dataTable').dataTable({
            "pageLength": 100,
            "order": [[ 0, "desc" ]]
        }
           
        );
    </script>

@stop