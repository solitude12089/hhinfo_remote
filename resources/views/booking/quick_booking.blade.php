@extends('layouts.dashboard',[
'page_title'=>'',
'menu'=>1,
'breadcrumb'=>[
'租借管理' => '',
'快速預約' => Request::url()

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
            width:100px;
            max-width:100px;
            min-width:100px;
            overflow:hidden;
            height:50px;
            max-height:50px;
            min-height:50px;
            text-align:center;
        }

        td{
            width:100px;
            max-width:100px;
            min-width:100px;
            overflow:hidden;
            height:50px;
            max-height:50px;
            min-height:50px;
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

        .modal-content{
            width:450px;
        }
        .expired{
            background-color:darkgray;

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
        <h3 class="box-title">快速預約</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">

      
            <form id='bookingform'action="/booking/booking" method="POST" enctype="multipart/form-data">
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

                <div class="form-group col-lg-12">
                        <label class="control-label">租借人</label>
                        <div>
                            <select  id="customer" name="customer[]" class="form-control chosen-select" multiple>
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

                <div id='hidden_select' hidden>
                </div>

                <div id='selectRange' hidden>
                </div>



                <div class= "col-lg-12">
                    <div style="float:right">
                        <input type="button" id="booking" class="btn btn-primary" value="預約"></input>
                    </div>
                </div>

                <!-- /.box-body -->
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
            width:"100%"
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

        $('#time_start_h').on('change', function(evt, params) {
            limit_time();
        });

        $('#time_start_i').on('change', function(evt, params) {
            limit_time();
        })

        function limit_time(){
            var start_moment = moment(moment().format('YYYY-MM-DD')+' '+$('#time_start_h').find('option:selected').val()+':'+$('#time_start_i').find('option:selected').val()); 
            var end_moment = moment(moment().format('YYYY-MM-DD')+' '+$('#time_end_h').find('option:selected').val()+':'+$('#time_end_i').find('option:selected').val()); 
            var t_end_moment = start_moment.add(30, 'minutes'); 
            $('#time_end_h').find('option').filter(function() {
                        return parseInt($(this).val()) < t_end_moment.format('H');
                }).attr('disabled','disabled');

            $('#time_end_h').find('option').filter(function() {
                        return parseInt($(this).val()) >= t_end_moment.format('H');
            }).removeAttr('disabled');
            if(t_end_moment > end_moment){
                $('#time_end_h').find('option:selected').removeAttr("selected");
                $('#time_end_i').find('option:selected').removeAttr("selected");
                $('#time_end_h').find('option').filter(function() {
                        return $(this).val() == t_end_moment.format('H');
                }).attr('selected','selected');
                $('#time_end_i').find('option').filter(function() {
                        return $(this).val() == t_end_moment.format('mm');
                }).attr('selected','selected');
            }
        }
        limit_time();
     

      

      



      



        $('#booking').click(function(){

            if($('#group').val()==''||$('#group').val()==null){
                alert('請選擇區域');
                return;
            }
            if($('#family').val()==''||$('#family').val()==null){
                alert('請選擇群組');
                return;
            }
            if($('#device').val()==''||$('#device').val()==null){
                alert('請選擇設備名稱');
                return;
            }
            var sp_time_s = zeroPad($('#time_start_h').val())+':'+$('#time_start_i').val();
            var sp_time_e = zeroPad($('#time_end_h').val())+':'+$('#time_end_i').val();

            if(sp_time_s == sp_time_e){
                alert('選擇時段有誤 請重新選擇.');
                return;
            }
            if($('#customer').val()==''||$('#customer').val()==null){
                alert('請選擇租借人');
                return;
            }


            
            var startDate = $('#date').val().split(" - ")[0];
            var endDate = $('#date').val().split(" - ")[1];

         

            $('#hidden_select').empty();
            $('#selectRange').empty();
            var _now_d = startDate;

            while(_now_d <= endDate){
                if(($('#sp_pick').val()).length!=0){
                    if($.inArray(moment(_now_d).weekday().toString(), $('#sp_pick').val()) > -1){
                        //加
                        var _now_t = sp_time_s;
                        while(_now_t != sp_time_e){
                            $('#hidden_select').append('<td class="idle-select" date="'+_now_d+'" start="'+_now_t+'" end="'+timeAdd(_now_t)+'" place="'+$('#device').val()+'"></td>');
                            _now_t = timeAdd(_now_t);
                        }
                    }
                }else{
                    //加
                    var _now_t = sp_time_s;
                    while(_now_t != sp_time_e){
                        $('#hidden_select').append('<td class="idle-select" date="'+_now_d+'" start="'+_now_t+'" end="'+timeAdd(_now_t)+'" place="'+$('#device').val()+'"></td>');
                        _now_t = timeAdd(_now_t);
                    }
                }
               
                _now_d = dateAdd(_now_d);
            }

            var t = $('.idle-select');
            if(t.length==0){
                alert('請點選預約時段.');
                return;
            }
            
            
            var eventList = groupByRanges(t);
            var select_customer = $("#customer option:selected" );
            var msg = '<div>租借人 :';
            $.each(select_customer,function(k,v){
                _msg = '<label>'+$(v).text()+'</label>';
                msg = msg+'<div>'+_msg+'</div>';
            });

            msg = msg+'<div>是否租用冷氣 : <label>'+$("#aircontrol option:selected" ).text()+'<label></div>\
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
            if(_time=='23:30'){
                return '24:00';
            }
            return moment(moment().format('YYYY-MM-DD')+' '+_time).add(30, 'm').format('HH:mm');
        }
        function dateAdd(_day){
            return moment(_day).add(1, 'd').format('YYYY-MM-DD');
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

        function zeroPad(numberStr) {
            return numberStr.padStart(2, "0");
        }



    </script>





@stop