@extends('layouts.dashboard',[
'page_title'=>'',
'menu'=>1,
'breadcrumb'=>[
'密碼變更' => '',

]
])



@section('style')
@parent

@stop


@section('section')




<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">密碼變更</h3>
        <div class="box-tools pull-right">
            <!-- <button type="button" class="btn btn-box-tool " data-title="Hide" data-widget="collapse" title="Hide"><i class="fa fa-minus"></i></button> -->


        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-lg-12">
                <form id='postform' action="/account/reset" method="POST" enctype="multipart/form-data">
                  
                            <div class="form-group col-lg-12">
                                <label class="control-label">新密碼</label>
                                <div>
                                    <input id="pwd" name="pwd" type="password" class="form-control" >

                                </div>
                            </div>

                            <div class="form-group col-lg-12">
                                <label class="control-label">確認密碼</label>
                                <div>
                                    <input id="dpwd" name="dpwd" type="password" class="form-control">

                                </div>
                            </div>


                </form>
            </div>
            <!-- /.box-body -->

        </div>
    </div>
    <!-- /.box-body -->


     <div class="box-footer">

       
           
               
                    <button id="btnSave" class="btn btn-success">確定</button>
                    <a class="btn btn-default" href="/home">取消</a>
      
    </div>

</div>


@stop




@section('script')
<script>
    $('#btnSave').click(function(){
        var pwd = $('#pwd').val();
        var dpwd = $('#dpwd').val();
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

</script>

@stop