@extends('layouts.dashboard',[
'page_title'=>'',
'menu'=>1,
'breadcrumb'=>[
'系統管理' => '',
'操作紀錄查詢' => Request::url()

]
])



@section('style')
@parent
    <link href="/css/datatables.min.css" rel="stylesheet">
    <link href="/css/daterangepicker.css" rel="stylesheet">
    <link href="/css/chosen/chosen.min.css" rel="stylesheet">
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
        <h3 class="box-title">操作紀錄查詢</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <form id='postform' action="{{url('/booking')}}" method="POST" enctype="multipart/form-data">
                <div class="form-group col-lg-12">
                    <label class="control-label">所屬區域</label>
                    <div>
                        <select  id="group" name="group" class="form-control chosen">
                            @foreach($groups as $key => $value)
                                <option value="{{$key}}">{{$value}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>



                <div class="form-group col-lg-12">
                    <label class="control-label">地點</label>
                    <div>
                        <select  id="device" name="device" class="form-control chosen">
                            <option value=""></option>
                        </select>
                    </div>

                </div>


                <div class="form-group col-lg-12">
                    <label class="control-label">日期</label>
                    <div>
                        <input  id="date" name="date" class="form-control">
                        </input>
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label">紀錄類型</label>
                    <div>
                        <select id="log_type" name="log_type" class="form-control chosen" multiple >
                            <option value="swipe_event">刷卡事件</option>
                            <option value="device_control">遠端操作</option>  
                            <option value="phone_control">手機操作</option>
                        </select>
                    </div>
                </div>


                <div class= "col-lg-12">
                    <div style="float:right">
                        <input type="button" id="search" class="btn btn-primary" value="查詢"></input>
                    </div>
                </div>

                <!-- /.box-body -->
            </form>
        </div>
    </div>
    <!-- /.box-body -->
</div>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">查詢結果</h3>
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
   

</div>


@stop




@section('script')
    <script src="/js/chosen/chosen.jquery.min.js"></script>
    <script src="/js/datatables.min.js"></script>
    <script src="/js/dataTables.buttons.min.js"></script>
    <script src="/js/jszip.min.js"></script>
    <script src="/js/buttons.html5.min.js"></script>
    <script src="/js/moment.min.js"></script>
    <script src="/js/daterangepicker.js"></script>
    <script>
        var devices = <?php echo json_encode($devices); ?>;
        var _datatable = null;
        $('.chosen').chosen({
            width:"100%",
            allow_single_deselect:true
        });

        $('#group').on('change', function(evt, params) {
            $('#device').empty();
            $('#device').append('<option value=""></option>');
            if(devices[$('#group').val()] != null){
                $.each(devices[$('#group').val()],function(k,f){
                    $('#device').append('<option value="'+f.id+'">'+f.name+'</option>');
                });
            }
            $('#device').trigger("chosen:updated");
           
        });

        var date = $('#date').daterangepicker({
            "autoApply": true,
            "linkedCalendars": false,
            "showCustomRangeLabel": false,
            locale:{
                format:'YYYY-MM-DD',
                "monthNames": [
                    "一月",
                    "二月",
                    "三月",
                    "四月",
                    "五月",
                    "六月",
                    "七月",
                    "八月",
                    "九月",
                    "十月",
                    "十一月",
                    "十二月"
                ],
                "daysOfWeek": [
                    "日",
                    "一",
                    "二",
                    "三",
                    "四",
                    "五",
                    "六"
                ],

            },
        });



        $('#search').on('click',function(){
            var group = $('#group').val();
            var device = $('#device').val();
            var startDate = $('#date').val().split(" - ")[0];
            var endDate = $('#date').val().split(" - ")[1];
            var log_type = $('#log_type').val();
            var url = '/systemlog/control_log'
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                'group':group,
                'device':device,
                'startDate':startDate,
                'endDate':endDate,
                'log_type':log_type
                },
                success: function(result){
                    console.log(result);
                    if(_datatable==null){
                        _datatable = $('.dataTable').DataTable({
                            pageLength: 100,
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
                    }
                    _datatable.clear();
                    _datatable.rows.add(result);
                    _datatable.draw();
                }
            });
        });





        @if(0)
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
        @endif
    </script>

@stop