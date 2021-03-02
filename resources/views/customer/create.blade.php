@extends('layouts.modal')

@section('modal-style')
<style>
    /* .modal-body{
        min-height: 300px;
    } */
</style>
@stop

@section('modal-title')
新增客戶
@stop
@section('modal-buttons')
<button id="btnSave" class="btn btn-success">確定</button>
<button class="btn btn-default" data-dismiss="modal" aria-label="Close">取消</button>
@stop

@section('modal-body')



   

<div class="row">
    <div class="col-lg-12">
        <form id='postform' action="{{url('/customer/store')}}" method="POST" enctype="multipart/form-data">
          
                    <div class="form-group col-lg-12">
                        <label class="control-label">電話 (ex:0912345678 or 0227861020)</label>
                        <div>
                            <input id="phone" name="phone" oninput="value=value.replace(/[^\d]/g,'')" class="form-control" >

                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label">姓名</label>
                        <div>
                            <input id="name" name="name"  class="form-control" >

                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label">悠遊卡號</label>
                        <div>
                            <div id="card_gulp">
                               
                            </div>
                            <div>
                                <input id="card_uuid_input"  class="form-control"  oninput="value=value.replace(/[^\d]/g,'')" style="width: 90%;display: inline;"/>
                                <input class="btn btn-success" type="button" onclick="insert(this)" value="新增"/>
                            </div>
                        </div>
                    </div>
                  
        </form>
    </div>

</div>



    


<script>
    $('#btnSave').click(function(){
        if($('#card_uuid_input').val().length!=0){
            insert($('#card_uuid_input'));
        }
        var phone = $('#phone').val();
        var name = $('#name').val();
        var card_uuids = [];
        $.each($('[name ="card_uuid[]"]'),function(k,v){
            card_uuids.push($(v).val());
        });

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
        var url = '/customer/checkcardid';
        $.ajax({
                type: 'POST',
                url: url,
                data: {
                    'id':null,
                    'card_ids':card_uuids,
                    'phone':phone
                    
                },
                success: function(result){
                    if(result.result==1){
                        $('#postform').submit();
                    }
                    if(result.result==2){
                        $msg = '';
                        $.each(result.checks,function(k,v){
                            $msg += '<div>該卡號 : '+v.card_uuid+' 已被 '+v.customer.phone+' - '+v.customer.name+' 註冊</div>'
                        });
                        $msg += '<div>是否強制綁定卡號至該用戶???</div>';
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
                    if(result.result==0){
                        alert(result.msg);
                        return;
                    }
                }
        });
    
        //$('#postform').submit();
        
       
    });

    function remove(obj){
        $(obj).parent().remove();
    }

    function insert(obj){
        var card_uuid = $('#card_uuid_input').val();
        if(card_uuid.length!=0){
            if(card_uuid.length!=10 &&  card_uuid.length!=14){
                alert('卡號長度只允許 10、14 碼.');
                return;
            }
            var html = '<div class="btn btn-info">'+card_uuid+'\
                        <input type="button" class="btn btn-xs btn-danger" onclick="remove(this)" value="x"/>\
                        <input type="hidden" name="card_uuid[]" value="'+card_uuid+'">\
                    </div>';
            $('#card_gulp').append(html);
            $('#card_uuid_input').val("");
        }
      
    }

   


</script>
@stop




