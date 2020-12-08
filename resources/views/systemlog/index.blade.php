@extends('layouts.dashboard',[
'page_title'=>'',
'menu'=>1,
'breadcrumb'=>[
'系統管理' => '',
'系統紀錄' => Request::url()

]
])



@section('style')
@parent
    <link href="/css/datatables.min.css" rel="stylesheet">
    <style>
        div.dt-buttons {
            position: relative;
            float: left;
        }
        button.dt-button, div.dt-button, a.dt-button, input.dt-button {
            position: relative;
            display: inline-block;
            box-sizing: border-box;
            margin-right: 0.333em;
            margin-bottom: 0.333em;
            padding: 0.5em 1em;
            border: 1px solid rgba(0,0,0,0.3);
            border-radius: 2px;
            cursor: pointer;
            font-size: 0.88em;
            line-height: 1.6em;
            color: black;
            white-space: nowrap;
            overflow: hidden;
            background-color: rgba(0,0,0,0.1);
            background: -webkit-linear-gradient(top, rgba(230,230,230,0.1) 0%, rgba(0,0,0,0.1) 100%);
            background: -moz-linear-gradient(top, rgba(230,230,230,0.1) 0%, rgba(0,0,0,0.1) 100%);
            background: -ms-linear-gradient(top, rgba(230,230,230,0.1) 0%, rgba(0,0,0,0.1) 100%);
            background: -o-linear-gradient(top, rgba(230,230,230,0.1) 0%, rgba(0,0,0,0.1) 100%);
            background: linear-gradient(to bottom, rgba(230,230,230,0.1) 0%, rgba(0,0,0,0.1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0,StartColorStr='rgba(230, 230, 230, 0.1)', EndColorStr='rgba(0, 0, 0, 0.1)');
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            text-decoration: none;
            outline: none;
            text-overflow: ellipsis;
        }
    </style>
@stop


@section('section')




<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">系統紀錄</h3>
        <div class="box-tools pull-right">
           
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-lg-12">
                <table class='table dataTable'>
                   
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
    <script src="/js/dataTables.buttons.min.js"></script>
    <script src="/js/jszip.min.js"></script>
    <script src="/js/buttons.html5.min.js"></script>
    <script src="/js/moment.min.js"></script>
    <script>
        var rt_data = <?php echo json_encode($rt_data); ?>;
         $('.dataTable').DataTable({
            pageLength: 50,
            data: rt_data,
            columns: [
                {
                    title: "時間",
                },
                {
                    title: "人員",
                },
                {
                    title: "裝置",
                },
                {
                    title: "動作",
                },
                
                {
                    title: "內容",
                },
            ],
            dom: 'Bfrtip',
            order: [[ 0, 'desc' ]],
            buttons: [
                            {
                                extend: 'excel',
                                title: '系統紀錄_'+moment().format("YYYYMMDD")
                                
                            }
                        ],
         });
    </script>

@stop