@extends('layouts.dashboard',[
'page_title'=>'',
'menu'=>1,
'breadcrumb'=>[
'租借管理' => '',
'預約查詢' => Request::url()

]
])



@section('style')
@parent

    <link href="/css/chosen/chosen.min.css" rel="stylesheet">
    <link href="/css/daterangepicker.css" rel="stylesheet">
    <!-- //<link href="/css/datatables.min.css" rel="stylesheet"> -->
    <link href="/css/jquery-ui.css" rel="stylesheet">
    <link href="/css/bootstrap-dialog.min.css" rel="stylesheet">

    <style>
        fieldset {
            border:0;
            padding:10px;
            margin-bottom:10px;
            background:#EEE;
        }
        legend {
            padding:5px 10px;
            background-color:#4F709F;
            color:#FFF;
        }
        th{
            text-align:center;
        }

        td{
            min-height: 50px;
            text-align:center;
        }
        h5{
            color: red;
        }
        .pick-row{
            height: 50px;
        }


        .idle-select{
            background-color: green;
        }

    </style>

@stop


@section('section')


<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">預約查詢</h3>
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
                    <label class="control-label">預約日期</label>
                    <div>
                        <input  id="date" name="date" class="form-control">
                        </input>
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label">租借人</label>
                    <div>
                        <select  id="customer" name="customer" class="form-control chosen-select">
                            <option value=""></option>
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
            <button class="btn btn-danger btn-xs"  onclick="remove()">刪除</button>

        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        
        <div class="row">
            <table id='rt_table'>
            </table>

        </div>
    </div>
    <!-- /.box-body -->
</div>

<form id='remove_from' action="{{url('/booking/remove')}}" method="POST" enctype="multipart/form-data" hidden>
        <input id="remove_id" name='remove_id'>
</form>




@stop




@section('script')

    <script src="/js/datatables.min.js"></script>
    <script src="/js/chosen/chosen.jquery.min.js"></script>
    <script src="/js/chosen/chosen_ajax.js"></script>
    <script src="/js/moment.min.js"></script>
    <script src="/js/daterangepicker.js"></script>
    <script src="/js/jquery-ui.js"></script>
    <script src="/js/bootstrap-dialog.min.js"></script>

    <script>
        var devices = <?php echo json_encode($devices); ?>;
      
        $('.chosen').chosen({
            width:"100%",
            allow_single_deselect:true
        });
        $(".chosen-select").chosen_ajax({
            width:"100%",
            allow_single_deselect: true, // Normal chosen option
            // Ajax options
            ajax_base_url: "/customer/list",  // Mandatory
            ajax_method: "POST",                            // Default GET
            ajax_data: {
                phone: $('#customer').val()
            },       // To set extra data + {search field}
            ajax_min_chars: 4                               // Minimum characters to send ajax request
        });

        $(".chosen-select").on('change', function(evt, params) {
            if(params==null){
                $('#customer').val("");
            }
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


        $('#group').on('change',function(){
            $('#device').empty();
            var newOption = $('<option value=""></option>');
            $('#device').append(newOption);
            $.each(devices[$('#group').val()],function(k,f){
                    var newOption = $('<option value="'+f.id+'">'+f.name+'</option>');
                    $('#device').append(newOption).trigger('chosen:updated');
            });
        });




        $('#group').trigger('change');

        var datatable = null;
        $('#search').on('click',function(){
            var group = $('#group').val();
            var device = $('#device').val();
            var startDate = $('#date').val().split(" - ")[0];
            var endDate = $('#date').val().split(" - ")[1];
            var customer = $('#customer').val();



            $.ajax({
                url: '/booking/query',
                type: "POST",
                data: {
                    'group':group,
                    'device':device,
                    'startDate':startDate,
                    'endDate':endDate,
                    'customer':customer,
                },
            }).done(function(result) {
                    if(datatable==null){
                        datatable = $('#rt_table').DataTable({
                            pageLength: 50,
                            data: result,
                            columns: [
                                {
                                    title:"",
                                    width:"5%",
                                },
                                {
                                    title: "姓名",
                                   
                                },
                                {
                                    title: "電話",
                                   
                                },
                                {
                                    title: "租借地點",
                                },
                                {
                                    title: "租借日期"
                                },
                                {
                                    title: "租借時段"
                                },
                                {
                                    title: "是否租借冷氣"
                                },
                             
                            ]
                        });
                    }
                    else{
                        datatable.clear();
                        datatable.rows.add(result);
                        datatable.draw();
                    }
                   
            });
        });


        function remove(){
           
            $removes = $('[name ="remove[]"]:checked');
            if($removes.length==0){
                alert('請勾選刪除項目');
                return;
            }
            $msg = '';
            $.each($removes,function(k,v){
               
              
                $device = $($(v).parents('tr').children('td').get(3)).html();
                $user = $($(v).parents('tr').children('td').get(1)).html();
                $phone =  $($(v).parents('tr').children('td').get(2)).html();
                $date = $($(v).parents('tr').children('td').get(4)).html();
                $time = $($(v).parents('tr').children('td').get(5)).html();
                $msg =  $msg+'<div>租借人:'+$user+' - '+$phone+'</div>\
                    <div>租借日期:'+$date+'</div>\
                    <div>租借時間:'+$time+'</div>\
                    <div>租借地點:'+$device+'</div><br>';
            });
            BootstrapDialog.confirm({
                title: '刪除確認',
                message: $msg,
                type: BootstrapDialog.TYPE_DANGER, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                closable: true, // <-- Default value is false
                draggable: true, // <-- Default value is false
                btnCancelLabel: '取消', // <-- Default value is 'Cancel',
                btnOKLabel: '確定', // <-- Default value is 'OK',
                btnOKClass: 'btn-danger', // <-- If you didn't specify it, dialog type will be used,
                callback: function(result) {
                    
                    if(result) {
                        $('#remove_from').empty;
                        $.each($removes,function(k,v){
                            $('#remove_from').append(' <input id="remove_id" name="remove_id[]" value="'+$(v).val()+'">');
                        });
                        $('#remove_from').submit();
                        
                    }
                }
            });

        }





    </script>





@stop