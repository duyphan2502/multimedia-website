<?php
$errors = Session::get('errorMessages');
$messages = Session::get('successMessages');
$infos = Session::get('infoMessages');
$warnings = Session::get('warningMessages');
?>
{{--Flash message for errors--}}
@if($errors) @foreach($errors as $key => $row)
    <script type="text/javascript">
        Utility.showNotification('{{ $row }}', 'error');
    </script>
@endforeach @endif
{{--Flash message for errors--}}

{{--Flash message for messages--}}
@if($messages) @foreach($messages as $key => $row)
    <script type="text/javascript">
        Utility.showNotification('{{ $row }}', 'success');
    </script>
@endforeach @endif
{{--Flash message for messages--}}

{{--Flash message for infors--}}
@if($infos) @foreach($infos as $key => $row)
    <script type="text/javascript">
        Utility.showNotification('{{ $row }}', 'info');
    </script>
@endforeach @endif
{{--Flash message for infors--}}

{{--Flash message for warnings--}}
@if($warnings) @foreach($warnings as $key => $row)
    <script type="text/javascript">
        Utility.showNotification('{{ $row }}', 'warning');
    </script>
@endforeach @endif
{{--/=Flash message for warnings--}}