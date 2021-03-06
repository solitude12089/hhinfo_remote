@extends('layouts.dashboard',[
'page_title'=>'',
'menu'=>1,
'breadcrumb'=>[
'租借管理' => '',
'預約租借' => Request::url()

]
])



@section('style')
@parent
    <link href="/css/chosen/chosen.min.css" rel="stylesheet">
    <link href="/css/daterangepicker.css" rel="stylesheet">
    <link href="/css/datatables.min.css" rel="stylesheet">
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
        fieldset {
            overflow:auto;
        }
        th{
            width:25px;
            max-width:25px;
            min-width:25px;
            overflow:hidden;
            height:25px;
            max-height:25px;
            min-height:25px;
            text-align:center;
        }

        td{
            width:25px;
            max-width:25px;
            min-width:25px;
            overflow:hidden;
            height:25px;
            max-height:25px;
            min-height:25px;
            text-align:center;
        }
        h5{
            color: red;
        }
        .pick-row{
            height: 25px;
        }


        .idle-select{
            background-color: green;
        }

        .modal-content{
            width:450px;
        }
        .expired{
            background-color:darkgray;

        }
        .full{
            background-color:#B22222;
        }

        .my_th{
            height: 30px;
        }

        .my_chosen{
            height: 34px;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.42857143;
            color: #555;
            border: 1px solid #ccc;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
            box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
            -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
            -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
            transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        }
        .my_chosen > option:disabled {
            background: #ccc;
        }

    </style>

@stop


@section('section')


<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">預約租借</h3>
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
                    <label class="control-label">群組</label>
                    <div>
                        <select  id="family" name="family" class="form-control chosen">
                            <option value=""></option>
                        </select>
                    </div>

                </div>

              
                <div class="form-group col-lg-12">
                    <label class="control-label">名稱</label>
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
                    <label class="control-label">選擇時段</label>
                    <div>
                        <select class="my_chosen" id="time_start_h" name="time_start[]" class="form-control">
                            @foreach($hours as $key => $value)
                                @if($value!='24')
                                    @if($value=='8')
                                    <option selected value="{{$value}}">{{$value}}</option>
                                    @else
                                    <option value="{{$value}}">{{$value}}</option>
                                    @endif
                                @endif
                            @endforeach
                        </select>
                        <select class="my_chosen" id="time_start_i" name="time_start[]" class="form-control">
                            <option value="00">00</option>
                            <option value="30">30</option>
                        </select>
                        <label style="padding: 10px 30px;"> ~ </label>
                        <select class="my_chosen" id="time_end_h" name="time_end[]" class="form-control">
                            @foreach($hours as $key => $value)
                                @if($value=='22')
                                <option selected value="{{$value}}">{{$value}}</option>
                                @else
                                <option value="{{$value}}">{{$value}}</option>
                                @endif
                            @endforeach
                        </select>
                        <select class="my_chosen" id="time_end_i" name="time_end[]" class="form-control">
                            <option value="00">00</option>
                            <option value="30">30</option>
                        </select>
                       

                    </div>

                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label">特殊篩選-每逢</label>
                    <div>
                        <select  id="sp_pick" name="sp_pick[]" class="form-control chosen" multiple>

                            <option value="1">星期一</option>
                            <option value="2">星期二</option>
                            <option value="3">星期三</option>
                            <option value="4">星期四</option>
                            <option value="5">星期五</option>
                            <option value="6">星期六</option>
                            <option value="0">星期日</option>

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
            <button class="btn btn-primary btn-xs" id="btn_booking">確認預約</button>

        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <form id='bookingform' action="/booking/booking" method="POST" enctype="multipart/form-data">
                <div class="form-group col-lg-12">
                        <label class="control-label">租借人</label>
                        <div>
                            <select  id="customer" name="customer" class="form-control chosen-select">
                                <option value=""></option>
                            </select>

                        </div>

                </div>

                <div class="form-group col-lg-12">
                        <label class="control-label">是否租用冷氣</label>
                        <div>
                            <select  id="aircontrol" name="aircontrol" class="form-control chosen">
                                <option value="0">否</option>
                                <option value="1">是</option>
                            </select>

                        </div>

                </div>

                <div id='result'>
                </div>


                <div id='selectRange' hidden>
                </div>

             </form>

        </div>
    </div>
    <!-- /.box-body -->
</div>





@stop




@section('script')
    <script src="/js/chosen/chosen.jquery.min.js"></script>
    <script src="/js/chosen/chosen_ajax.js"></script>
    <script src="/js/moment.min.js"></script>
    <script src="/js/daterangepicker.js"></script>
    <script src="/js/bootstrap-dialog.min.js"></script>
    <script>
       
        var devices = <?php echo json_encode($devices); ?>;
        var deviceMap = <?php echo json_encode($deviceMap); ?>;
        var timeRanges = <?php echo json_encode($timeRanges); ?>;

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
            ajax_min_chars: 3                               // Minimum characters to send ajax request
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
            "minDate":  moment().format('YYYY-MM-DD'),
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

        var time = $('#time').daterangepicker({
            startDate: "08:00",
            endDate: "22:00",
            timePicker: true,
            timePicker24Hour: true,
            timePickerIncrement: 30,
            timePickerSeconds: false,
            
            locale: {
                format: 'HH:mm'
            }
        }).on('show.daterangepicker', function (ev, picker) {
            picker.container.find(".calendar-table").hide();
        });




        
        $('#group').on('change', function(evt, params) {
            console.log( $('#group').val());
            $('#family').empty();
            $('#device').empty();
            if(devices[$('#group').val()] != null){
                $.each(devices[$('#group').val()],function(k,f){
                    $('#family').append('<option value="'+k+'">'+k+'</option>');
                })
              
            }
            $('#family').trigger("chosen:updated");
            $('#device').trigger("chosen:updated");
            if(devices[$('#group').val()] != null){
                $('#family').trigger('change');
            }
        });
        $('#family').on('change', function(evt, params) {
            console.log( $('#family').val());
            $('#device').empty();
            if(devices[$('#group').val()][ $('#family').val()] != null){
                $.each(devices[$('#group').val()][ $('#family').val()],function(k,f){
                    $('#device').append('<option value="'+f.id+'">'+f.name+'</option>');
                })
              
            }
            $('#device').trigger("chosen:updated");
          
        });
        $('#group').trigger('change');


        $('#time_start_h').on('change', function(evt, params) {
            console.log( $('#time_start_h').val());
            var start_h = $('#time_start_h').find('option:selected').val();
            var end_h =$('#time_end_h').find('option:selected').val();
            var target_h = parseInt(start_h)+1;
           
            $('#time_end_h').find('option').filter(function() {
                        return $(this).val() < target_h;
                }).attr('disabled','disabled');

            $('#time_end_h').find('option').filter(function() {
                        return $(this).val() >= target_h;
            }).removeAttr('disabled');


            if(end_h<target_h){
                $('#time_end_h').find('option:selected').removeAttr("selected");
            
                $('#time_end_h').find('option').filter(function() {
                        return $(this).val() == target_h;
                }).attr('selected','selected');
                
                $('#time_end_h').trigger('change');
                
            }

        });

        $('#time_end_h').on('change', function(evt, params) {
            var end_h =$('#time_end_h').find('option:selected').val();
            if(end_h=='24'){
                $('#time_end_i').val('00');
                $('#time_end_i').find('option:eq(1)').attr('disabled','disabled');
            }
            else{
                $('#time_end_i').find('option:eq(1)').removeAttr('disabled');
            }

        });
      



        $('#search').on('click',function(){
            var group = $('#group').val();
            var device = $('#device').val();
            var startDate = $('#date').val().split(" - ")[0];
            var endDate = $('#date').val().split(" - ")[1];
            var sp_pick = $('#sp_pick').val();
            var sp_time_s = $('#time_start_h').val()+':'+$('#time_start_i').val();
            var sp_time_e = $('#time_end_h').val()+':'+$('#time_end_i').val();
            var url = '/booking/search'
            $.ajax({
              type: 'POST',
              url: url,
              data: {
                'group':group,
                'device':device,
                'startDate':startDate,
                'endDate':endDate,
                'sp_pick':sp_pick,
                'sp_time_s':sp_time_s,
                'sp_time_e':sp_time_e
              },
              success: function(result){
                $('#result').empty();
                $('#result').append(result);

                $('.idle').click(function(){
                    console.log(this);
                    if($(this).hasClass('idle-select')){
                        $(this).removeClass('idle-select');
                    }
                    else{
                        $(this).addClass('idle-select');
                    }

                });
                // console.log(result);
              }
            });
        });


        $('#btn_booking').click(function(){
            console.log('click');
            var t = $('.idle-select');
            if(t.length==0){
                alert('請點選預約時段.');
                return;
            }
            if($('#customer').val()==''||$('#customer').val()==null){
                alert('請選擇租借人');
                return;
            }
            
            var eventList = groupByRanges(t);

            var msg = '<div>租借人 : <label>'+$("#customer option:selected" ).text()+'<label></div>\
                    <div>是否租用冷氣 : <label>'+$("#aircontrol option:selected" ).text()+'<label></div>\
                    <div>預約時段 : </div><div>';
            $.each(eventList,function(k,v){
                msg = msg +'<div><label>'+ deviceMap[v.device].family+'-'+deviceMap[v.device].name +' , ' + v.date + ' : ' + timeRanges[v.start].start + ' ~ '+  timeRanges[v.end].end + '</label></div>';
            });
            msg = msg + '<div>備註</div>\
                        <div><textarea cols="50" rows="5"></textarea></div>';

            BootstrapDialog.confirm({
                title: '確認預約',
                message: msg,
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
                        var note = this.dialog.getModalBody().find('textarea').val();
                        $('#selectRange').append('<input value="'+note+'" name="note"/>');
                        $.each(t,function(k,v){
                            $('#selectRange').append('<input value="'+$(v).attr('start')+'" name="booking['+$(v).attr('place')+']['+$(v).attr('date')+'][]"/>');
                        });

                        $('#bookingform').submit();

                    }
                }
            });



            // BootstrapDialog.confirm({
            //     title: '確認預約',
            //     message: msg,
            //     type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
            //     closable: true, // <-- Default value is false
            //     draggable: true, // <-- Default value is false
            //     btnCancelLabel: '取消', // <-- Default value is 'Cancel',
            //     btnOKLabel: '確定', // <-- Default value is 'OK',
            //     btnOKClass: 'btn-warning', // <-- If you didn't specify it, dialog type will be used,
            //     callback: function(result) {
            //         console.log(result);
            //         // result will be true if button was click, while it will be false if users close the dialog directly.
            //         if(result) {
            //             var note = this.dialog.getModalBody().find('textarea').val();
            //             $('#selectRange').append('<input value="'+note+'" name="note"/>');
            //             $.each(eventList,function(k,v){
            //                 $('#selectRange').append('<input value="'+timeRanges[v.start].start+'" name="booking['+v.device+']['+v.date+']['+k+'][]"/>');
            //                 $('#selectRange').append('<input value="'+timeRanges[v.end].end+'" name="booking['+v.device+']['+v.date+']['+k+'][]"/>');
                          
            //             });

            //             $('#bookingform').submit();

            //         }
            //     }
            // });
        });

        function timeAdd(_time){
            return moment(moment().format('YYYY-MM-DD')+' '+_time).add(30, 'm').format('HH:mm');
        }

        function groupByRanges(t){
            var rtrr = [];
            $.each(t,function(k,v){
                if(rtrr[$(v).attr('place')]==null){
                    rtrr[$(v).attr('place')]=[];
                }
                if(rtrr[$(v).attr('place')][$(v).attr('date')]==null){
                    rtrr[$(v).attr('place')][$(v).attr('date')]=[];
                }
                rtrr[$(v).attr('place')][$(v).attr('date')].push($(v).attr('start'));
            });
            var eventList = [];
            for(k_device in rtrr){
                for(k_date in rtrr[k_device]){
                        var event=[];
                        for(k in rtrr[k_device][k_date]){
                            k = parseInt(k);
                            var start = rtrr[k_device][k_date][k];
                            if(rtrr[k_device][k_date][k+1]!=null && timeAdd(start)==rtrr[k_device][k_date][k+1]){
                                if(Object.keys(event).length==0){
                                    event['date'] = k_date;
                                    event['device'] = k_device;
                                    event['start'] = start;
                                }
                            }
                            else{
                                if(Object.keys(event).length!=0){
                                    event['end'] = start;
                                    eventList.push(event);
                                    event = [];
                                }
                                else{
                                        event['date'] = k_date;
                                        event['device'] = k_device;
                                        event['start'] = start;
                                        event['end'] = start;
                                        eventList.push(event);
                                        event = [];
                                }
                            }
                        }
                }
            }
            return eventList;
        }


        function selectAll(obj){
            var target = $(obj).val();
            if(obj.checked){
                $('.idle-'+target).addClass('idle-select');
            }
            else{
                $('.idle-'+target).removeClass('idle-select');
            }
        }





    </script>





@stop