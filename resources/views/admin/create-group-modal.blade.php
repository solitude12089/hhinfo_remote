@extends('layouts.modal')

@section('modal-style')
<style>
    .modal-body{
        min-height: 500px;
    }
</style>
@stop

@section('modal-title')
建立區域
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
                        <label class="control-label">名稱</label>
                        <div>
                            <input id="name" name="name"  class="form-control" >

                        </div>
                    </div>

                     <div class="form-group col-lg-12">
                        <label class="control-label">成員</label>
                        <div>
                            <select  id="member" name="member[]" class="form-control chosen" multiple>
                                @foreach($users as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach

                            </select>

                        </div>
                    </div>

                  
        </form>
    </div>

</div>



    


<script>
    $('#btnSave').click(function(){
       
        var name = $('#name').val();
      

        if(name==''){
            alert('名稱不可為空值.');
            return;
        }

      
        $('#postform').submit();
    });

    $('.chosen').chosen({
        width:"100%",
        allow_single_deselect:true
    });


</script>
@stop




