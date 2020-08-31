@yield('modal-style')
<div class="box">
  <div id="modal-header" class="modal-header" data-confirm="{{ isset($confirm_close)?$confirm_close:""}}" data-confrim-txt="{{ isset($confirm_close_txt)?$confirm_close_txt:'Are you sure to Close?'}}">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">@yield('modal-title')</h4>
  </div>
  <div class="modal-body">
    @yield('modal-body')
  </div>
  @if(trim($__env->yieldContent('modal-buttons')))
  <div class="modal-footer">
    @section('modal-buttons')
    @show
  </div>
  @endif
</div>






<script type="text/javascript">
  $("#ajax-modal .modal-dialog").attr('class','modal-dialog');
  $("#ajax-modal .modal-dialog").addClass('{{isset($class)?$class:'modal-md'}}');
  $('#ajax-modal').on('hidden.bs.modal', function () {
    $("#ajax-modal .modal-dialog").attr('class','modal-dialog modal-sm');
  });
</script>
@yield('style')
@yield('plugin')
@yield('ui-script')
@yield('script')