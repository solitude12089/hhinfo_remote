@extends('layouts.modal')

@section('modal-style')
<style>

</style>
@stop

@section('modal-title')
編輯區域
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
                    <input id="name" name="name"  class="form-control" value="{{$group->name}}" >

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

  


</script>
@stop




