@extends('layouts.modal')
@section('modal-title')
編輯使用者
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
                        <label class="control-label">帳號</label>
                        <div>
                            <input id="account" name="account"  class="form-control" value="{{$user->account}}">

                        </div>
                    </div>

                     <div class="form-group col-lg-12">
                        <label class="control-label">姓名</label>
                        <div>
                            <input id="name" name="name"  class="form-control" value="{{$user->name}}" >

                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label">區域</label>
                        <div>
                            <select  id="group" name="group[]" class="form-control chosen" multiple>
                                @foreach($groups as $key => $value)
                              
                                    @if(in_array($value->id,$user->userGroupList->pluck('group_id')->toArray()))
                                        <option value="{{$value->id}}" selected>{{$value->name}}</option>
                                    @else
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                    @endif
                                    
                                @endforeach

                            </select>

                        </div>
                    </div>


                    <div class="form-group col-lg-12">
                        <label class="control-label">密碼</label>
                        <div>
                            <input id="pwd" name="pwd" type="password" class="form-control" value="nochange">

                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label">確認密碼</label>
                        <div>
                            <input id="dpwd" name="dpwd" type="password" class="form-control" value="nochange">

                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label">角色</label>
                        <div>
                            <select  id="role" name="role" class="form-control">
                                @foreach($roles as $key => $value)
                                    <option value="{{$value->id}}" {{$value->id==$user->role?'selected':''}}>{{$value->name}}</option>
                                @endforeach

                            </select>
                          

                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label">狀態</label>
                        <div>
                            <select  id="status" name="status" class="form-control">
                                
                                <option value="1" {{$user->status=="1"?'selected':''}}>啟用</option>
                                <option value="9" {{$user->status=="9"?'selected':''}}>關閉</option>

                            </select>
                          

                        </div>
                    </div>


        </form>
    </div>

</div>



    



<script>
    $('#btnSave').click(function(){
        var account = $('#account').val();
        var name = $('#name').val();
        var pwd = $('#pwd').val();
        var dpwd = $('#dpwd').val();

         if(account==''){
            alert('帳號不可為空值.');
            return;
        }

          if(name==''){
            alert('姓名不可為空值.');
            return;
        }

        if(pwd==''){
            alert('密碼不可為空值.');
            return;
        }
        if(dpwd==''){
            alert('確認密碼不可為空值.');
            return;
        }
        if(pwd!=dpwd){
            alert('密碼不相符.');
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




