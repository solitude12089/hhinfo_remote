@extends('layouts.modal')
@section('modal-style')
<style>
    .modal-body{
        min-height: 700px;
    }
</style>
@stop
@section('modal-title')
編輯設備
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
                        <label class="control-label">IP</label>
                        <div>
                            <input id="IP" name="IP"  class="form-control" value="{{$device->ip}}" >

                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label">名稱</label>
                        <div>
                            <input id="name" name="name"  class="form-control"  value="{{$device->name}}"  >

                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label">描述</label>
                        <div>
                            <input id="description" name="description"  class="form-control"  value="{{$device->description}}" >

                        </div>
                    </div>


                    <div class="form-group col-lg-12">
                        <label class="control-label">區域</label>
                        <div>
                            <select  id="group" name="group" class="form-control chosen">
                                @foreach($groups as $key => $value)
                                    @if($key==$device->group_id)
                                        <option value="{{$key}}" selected>{{$value}}</option>
                                    @else
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endif
                                @endforeach

                            </select>

                        </div>
                    </div>

                    
                    <div class="form-group col-lg-12">
                        <label class="control-label">群組</label>
                        <div>
                            <input id="family" name="family"  class="form-control"  value="{{$device->family}}">
                        </div>
                    </div>


                    <div class="form-group col-lg-12">
                        <label class="control-label">類型</label>
                        <div>
                            <select  id="type" name="type" class="form-control chosen">
                                <option value="一般" {{$device->type=="一般"?"selected":""}}>一般</option>
                                <option value="鐵捲門" {{$device->type=="鐵捲門"?"selected":""}}>鐵捲門</option>
                                <option value="公用鐵捲門" {{$device->type=="公用鐵捲門"?"selected":""}}>公用鐵捲門</option>

                            </select>

                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label">狀態</label>
                        <div>
                            <select  id="status" name="status" class="form-control chosen">
                                
                                <option value="1" {{$device->status=="1"?'selected':''}}>啟用</option>
                                <option value="9" {{$device->status=="9"?'selected':''}}>關閉</option>

                            </select>
                          

                        </div>
                    </div>

                 

        </form>
    </div>

</div>



    


<script>
    $('#btnSave').click(function(){
        var IP = $('#IP').val();
        var name = $('#name').val();
        var group = $('#group').val();
        var family = $('#family').val();

         if(IP==''){
            alert('IP不可為空值.');
            return;
        }

          if(name==''){
            alert('名稱不可為空值.');
            return;
        }

        if(group==''){
            alert('區域不可為空值.');
            return;
        }
        if(family==''){
            alert('群組不可為空值.');
            return;
        }
        $('#postform').submit();
    });

    $('.chosen').chosen({
        width:"100%",
        allow_single_deselect:true
    });

    $('#group').on('change',function(){
        var source = [];
        if(familys[$('#group').val()]!=null){
            source=familys[$('#group').val()];
        }
        $( "#family" ).autocomplete({
                source:source
        });
    });
    $('#group').trigger('change');


</script>
@stop



