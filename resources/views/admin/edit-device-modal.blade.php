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
                        <label class="control-label">內部IP</label>
                        <div>
                            <input id="local_ip" name="local_ip"  value="{{$device->local_ip}}" class="form-control" >

                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label">IP Mode</label>
                        <div>
                            <select  id="ip_mode" name="ip_mode" class="form-control chosen">
                                <option value="固定" {{$device->ip_mode=="固定"?"selected":""}}>固定</option>
                                <option value="Port Forwarding" {{$device->ip_mode=="Port Forwarding"?"selected":""}}>Port Forwarding</option>
                            </select>

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
                        <label class="control-label">模式</label>
                        <div>
                            <select  id="mode" name="mode" class="form-control chosen">
                                <option value="手動" {{$device->mode=="手動"?"selected":""}}>手動</option>
                                <option value="預約" {{$device->mode=="預約"?"selected":""}}>預約</option>
                            </select>

                        </div>
                    </div>


                    <div class="form-group col-lg-12">
                        <label class="control-label">用途</label>
                        <div>
                            <select  id="type" name="style" class="form-control chosen">
                                <option value="一般" {{$device->style=="一般"?"selected":""}}>一般</option>
                                <option value="公用" {{$device->style=="公用"?"selected":""}}>公用</option>
                            </select>

                        </div>
                    </div>


                    <div class="form-group col-lg-12">
                        <label class="control-label">類型</label>
                        <div>
                            <select  id="type" name="type" class="form-control chosen">
                                <option value="一般" {{$device->type=="一般"?"selected":""}}>一般</option>
                                <option value="鐵捲門" {{$device->type=="鐵捲門"?"selected":""}}>鐵捲門</option>
                            </select>

                        </div>
                    </div>

                    
                    <div class="form-group col-lg-12">
                        <label class="control-label">預約</label>
                        <div>
                            <select  id="is_booking" name="is_booking" class="form-control chosen">
                                <option value="1" {{$device->is_booking=="1"?"selected":""}}>可</option>
                                <option value="0" {{$device->is_booking=="0"?"selected":""}}>不可</option>
                            </select>

                        </div>
                    </div>

                     
                    <div class="form-group col-lg-12">
                        <label class="control-label">核心版本</label>
                        <div>
                            <select  id="kernel" name="kernel" class="form-control chosen">
                                <option value="漢軍" {{$device->kernel=="漢軍"?"selected":""}}>漢軍</option>
                                <option value="PI" {{$device->kernel=="PI"?"selected":""}}>PI</option>
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
    $('#style').on('change',function(){
        var current = $('#style').val();
        if(current=='公用'){
            $('#is_booking').val("0").trigger("chosen:updated");
        }
        else{
            $('#is_booking').val("1").trigger("chosen:updated");
        }
    });


</script>
@stop




