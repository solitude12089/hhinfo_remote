@extends('layouts.modal')

@section('modal-style')
<style>
    .modal-body{
        min-height: 500px;
    }
</style>
@stop

@section('modal-title')
編輯客戶
@stop
@section('modal-buttons')
<button id="btnSave" class="btn btn-success">確定</button>
<button class="btn btn-default" data-dismiss="modal" aria-label="Close">取消</button>
@stop

@section('modal-body')



   

<div class="row">
    <div class="col-lg-12">
        <form id='postform' action="{{url('/customer/'.$customer->id)}}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_method" value="PUT">
                    <div class="form-group col-lg-12">
                        <label class="control-label">電話</label>
                        <div>
                            <input id="phone" name="phone"  class="form-control"  value="{{$customer->phone}}">

                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label">姓名</label>
                        <div>
                            <input id="name" name="name"  class="form-control"  value="{{$customer->name}}"> 

                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label">悠遊卡號</label>
                        <div>
                            <input id="card_uuid" name="card_uuid"  class="form-control"  value="{{$customer->card_uuid}}">

                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label">狀態</label>
                        <div>
                            <select  id="status" name="status" class="form-control chosen">
                                
                                <option value="1" {{$customer->status=="1"?'selected':''}}>啟用</option>
                                <option value="9" {{$customer->status=="9"?'selected':''}}>關閉</option>

                            </select>
                          

                        </div>
                    </div>
                  
        </form>
    </div>

</div>



    


<script>
    $('#btnSave').click(function(){
       
        var phone = $('#phone').val();
        var name = $('#name').val();

        if(phone==''){
            alert('電話不可為空值.');
            return;
        }
        if(name==''){
            alert('姓名不可為空值.');
            return;
        }

      
        $('#postform').submit();
    });

   


</script>
@stop




