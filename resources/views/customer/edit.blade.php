@extends('layouts.modal')

@section('modal-style')
<style>
   
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
        <form id='postform' action="{{url('/customer/'.$customer->id.'/update')}}" method="POST" enctype="multipart/form-data">
                    <div class="form-group col-lg-12">
                        <label class="control-label">電話 (ex:0912345678 or 0227861020)</label>
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
   
    var c_id = <?php echo $customer->id; ?>;
    $('#btnSave').click(function(){
        console.log('ZZZ');
        var phone = $('#phone').val();
        var name = $('#name').val();
        var card_uuid = $('#card_uuid').val();
        if(phone==''){
            alert('電話不可為空值.');
            return;
        }
        if(name==''){
            alert('姓名不可為空值.');
            return;
        }
        re = /^[0]{1}[0-9]{9}$/;
        if(!re.test(phone)){
            alert('電話格式錯誤.');
            return;
        }

        if(card_uuid!=''){
            var url = '/customer/checkcardid';

            $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        'id':c_id,
                        'card_id':card_uuid,
                    },
                    success: function(result){
                        if(result==1){
                            $('#postform').submit();
                        }
                        else{
                            $msg = '<div>該卡號已被 '+result.phone+' - '+result.name+' 註冊</div>\
                            <div>是否強制綁定卡號至該用戶???</div>';
                            BootstrapDialog.confirm({
                                title: '警告',
                                message: $msg,
                                type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                                closable: true, // <-- Default value is false
                                draggable: true, // <-- Default value is false
                                btnCancelLabel: '取消', // <-- Default value is 'Cancel',
                                btnOKLabel: '確定', // <-- Default value is 'OK',
                                btnOKClass: 'btn-warning', // <-- If you didn't specify it, dialog type will be used,
                                callback: function(result) {
                                    if(result) {
                                        $('#postform').submit();
                                    }
                                }
                            });
                          
                        }
                    }
            });
        }
        else{
            $('#postform').submit();
        }

    });

   


</script>
@stop




