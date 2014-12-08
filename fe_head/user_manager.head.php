<?php require_once("auth.php"); ?>

<script type="text/javascript">

var admin_uname = "<?php echo(NavTools::getServerAdmin()); ?>";

var _user_data_array = [];
var _currentValues = [];
var _user_roles_array = $.parseJSON('<?php echo(json_encode($ne_user_roles)); ?>') ;
var _user_modus_array = $.parseJSON('<?php echo(json_encode($ne_user_modus)); ?>') ;
var _empty_user_data_array = $.parseJSON('<?php echo(json_encode(get_ne_user_params_simple())); ?>');
var _not_editable_user_params_array = $.parseJSON('<?php echo(json_encode(get_ne_user_params_not_editable())); ?>');
var _user_params_full = $.parseJSON('<?php echo(json_encode($ne_user_params)); ?>');
var _somethingChanged = false;
var _check_boxes_status = [];
var FileTreeObj = null;

var server_main_path = "<?php echo ($_SERVER['DOCUMENT_ROOT']); ?>";

</script>
