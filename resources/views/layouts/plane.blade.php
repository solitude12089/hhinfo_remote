<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/skin.css" rel="stylesheet">
  
    <!-- Scripts -->
    @yield('style')
</head>
<body class="skin-v2">
    <script src="/js/app.js"></script>
    @yield('body')
    @yield('modal')
   

    <div class="alert-bar">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))
        <?php $alert_id = 'alert-' . uniqid(); ?>

        <p id="{{$alert_id}}" class="alert alert-{{ $msg }}" style="position:fixed;left:0px;width:100%;z-index:99999;margin-bottom: 0px;border-radius:0;"><i class="fa fa-exclamation-circle"></i> {!! Session::get('alert-' . $msg) !!} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        <script>
            $('#{{$alert_id}}').animate({
                top: 0
            }, 300);
            setTimeout(function() {
                $('#{{$alert_id}}').animate({
                    top: '-50px'
                }, 300, function() {
                    // Animation complete.
                    $(this).remove()
                });
            }, 5000);
        </script>
        @endif
        @endforeach
    </div>


    @yield('plugin')
   

    <!-- Scripts -->
   
    <script>
        $("#menu-toggle2").click(function(e) {
                e.preventDefault();
                $(".sidebar").toggleClass("off");
                $("#wrapper").toggleClass("toggled");

                $('#wrapper.toggled').find("#sidebar-wrapper").find(".collapse").collapse('hide');
                
        });
        $('body').on('hidden.bs.modal', '.modal', function () {
                $(this).removeData('bs.modal');
                $(this).find('.modal-content').html('');
        });

        // $("[data-target='#ajax-modal']").click(function(ev) {
        //     ev.preventDefault();
        //     var target = $(this).attr("href");

        //     // load the url and show modal on success
        //     $("#ajax-modal").load(target, function() { 
        //         $("#ajax-modal").modal("show"); 
        //     });
        // });
    </script>
    @yield('ui-script')
    @yield('script')
</body>
</html>
