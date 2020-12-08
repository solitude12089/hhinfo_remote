@extends('layouts.dashboard',[
'page_title'=>'',
'menu'=>1,
'breadcrumb'=>[
'客戶管理' => '',
'客戶列表' => Request::url()

]
])



@section('style')
@parent
    <link href="/css/datatables.min.css" rel="stylesheet">
    <link href="/css/bootstrap-dialog.min.css" rel="stylesheet">
@stop


@section('section')



</div>
<div style="width: 500px" id="reader"></div>
</div>


@stop




@section('script')
    <script src="/js/html5-qrcode.min.js"></script>
    <script src="/js/datatables.min.js"></script>
    <script src="/js/bootstrap-dialog.min.js"></script>
    <script>
      



        var html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: 250 }
        );
                
        function onScanSuccess(qrCodeMessage) {
            // handle on success condition with the decoded message
            alert(qrCodeMessage);
            html5QrcodeScanner.clear();
            // ^ this will stop the scanner (video feed) and clear the scan area.
        }

        html5QrcodeScanner.render(onScanSuccess);
    </script>

@stop
