@extends('layouts.modal')

@section('modal-style')
<link href="/css/chosen/chosen.min.css" rel="stylesheet">
<style>
    .modal-body{
        min-height: 500px;
    }
</style>
@stop

@section('modal-title')
新增規則
@stop
@section('modal-buttons')
<button id="btnSave" class="btn btn-success">確定</button>
<button class="btn btn-default" data-dismiss="modal" aria-label="Close">取消</button>
@stop

@section('modal-body')





<div class="row">
    <div class="col-lg-12">
        <form id='postform' action="{{Request::url()}}" method="POST" enctype="multipart/form-data">
                    <div class="form-group col-lg-12">
                        <label class="control-label">選擇用戶</label>
                        <div>
                            <select  id="customer" name="customer" class="form-control chosen-select" value={{$spcard->customer_id}}>
                                <option value="{{$spcard->customer_id}}">{{$spcard->customer->phone.' - '.$spcard->customer->name}}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label">區域</label>
                        <div>
                            <select  id="group" name="group" class="form-control chosen">
                                @foreach($groups as $key => $value)
                                    <option value="{{$key}}" {{$key==$spcard->group_id?"selected":""}}>{{$value}}</option>
                                @endforeach

                            </select>

                        </div>
                    </div>


                    <div class="form-group col-lg-12">
                        <label class="control-label">授權群組</label>
                        <div>
                            <select  id="family" name="family[]" class="form-control chosen" multiple>
                            

                            </select>

                        </div>
                    </div>


                    <div class="form-group col-lg-12">
                        <label class="control-label">特殊權限</label>
                        <div>
                            <select  id="authority" name="authority[]" class="form-control chosen" multiple>
                                @if(in_array('3',$spcard->authority))
                                    <option value="3" selected>電燈權限</option>
                                @else
                                    <option value="3" >電燈權限</option>
                                @endif

                                @if(in_array('4',$spcard->authority))
                                    <option value="4" selected>冷氣權限</option>
                                @else
                                    <option value="4" >冷氣權限</option>
                                @endif
                               
                                

                            </select>

                        </div>
                    </div>


        </form>
    </div>

</div>





<script src="/js/chosen/chosen.jquery.min.js"></script>
<script src="/js/chosen/chosen_ajax.js"></script>
<script>
    var familys = <?php echo json_encode($familys); ?>;
    var spcard = <?php echo json_encode($spcard); ?>;
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
    $('#group').on('change',function(){
        $('#family').empty();
        $.each(familys[$('#group').val()],function(k,f){
                var newOption = $('<option value="'+f+'">'+f+'</option>');
                $('#family').append(newOption);
        });
        $('#family').trigger('chosen:updated');
    });
    
    $('#group').trigger('change');
    $('#family').val(spcard.family).trigger('chosen:updated');
 
    $('#btnSave').click(function(){
        var customer = $('#customer').val();
        var group = $('#group').val();
        var family = $('#family').val();
        if(customer==null||customer==''){
            alert('用戶不可為空');
            return;
        }
       
        if(family.length==0){
            alert('授權群組不可為空.');
            return;
        }
        $('#postform').submit();
   });




</script>
@stop
