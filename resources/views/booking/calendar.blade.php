@extends('layouts.dashboard',[
'page_title'=>'',
'menu'=>1,
'breadcrumb'=>[
'租借管理' => '',
'行事曆' => Request::url()

]
])



@section('style')
@parent
    <link href="/css/bootstrap-datepicker3.min.css" rel="stylesheet">
    <link href="/css/chosen/chosen.min.css" rel="stylesheet">
    <link href="/css/fullcalendar/main.min.css" rel="stylesheet">
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
        <h3 class="box-title">行事曆</h3>
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
                       
                        </select>
                    </div>

                </div>


                <div class="form-group col-lg-12">
                    <label class="control-label">月份</label>
                    <div>
                      
                        <input  id="date" name="date" value="{{date('Y-m')}}" class="form-control datepicker">
                          

                        </input>

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
        
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div id='calendar'>
            </div>

        </div>
    </div>
    <!-- /.box-body -->
</div>


 


@stop




@section('script')
    <script src="/js/chosen/chosen.jquery.min.js"></script>
    <script src="/js/bootstrap-datepicker.min.js"></script>
    <script src="/js/fullcalendar/main.min.js"></script>
    <script src="/js/fullcalendar/locales-all.min.js"></script>
    <script>
        var devices = <?php echo json_encode($devices); ?>;
        
        $.fn.datepicker.dates['en'] = {
            days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
            daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
            daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
            months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            monthsShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
            today: "Today",
            clear: "Clear",
            format: "mm/dd/yyyy",
            titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
            weekStart: 0
        };

        $('.chosen').chosen({
            width:"100%",
            allow_single_deselect:true
        });

        $('#group').on('change',function(){
            $('#device').empty();
            $.each(devices[$('#group').val()],function(k,f){
                    var newOption = $('<option value="'+f.id+'">'+f.name+'</option>');
                    $('#device').append(newOption);
            });
          
            $('#device').trigger("chosen:updated");
        });


        $('.datepicker').datepicker({
                    'startView': 2,
                    'minViewMode': 1,
                    'autoclose': true,
                    'format':'yyyy-mm'
        });

        $('#group').trigger('change');


        $('#search').on('click',function(){
            var group = $('#group').val();
            var device = $('#device').val();
            var date = $('#date').val();
            // var sp_pick = $('#sp_pick').val();
            var url = '/booking/calendar'
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    'group':group,
                    'device':device,
                    'date':date
                },
                success: function(result){
                    $('#calendar').empty();
                    var calendarEl = document.getElementById('calendar');
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        locale: 'zh-tw',
                        headerToolbar: {
                            left: '',
                            center: 'title',
                            right: ''
                        },
                        initialDate: date+'-01',
                        eventClassNames: 'myclassname',
                        editable: false,
                        dayMaxEvents: true, 
                        displayEventTime:true,
                        displayEventEnd:true,
                        eventTimeFormat:{
                            hour: 'numeric',
                            minute: '2-digit',
                            meridiem: false,
                            hour12: false
                        },
                        events:result
                    });
                    calendar.render();
                }
            });
        });
             
 
       



    </script>
  




@stop