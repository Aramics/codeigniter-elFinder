 
<link rel="stylesheet" type="text/css"
      href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css"/>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" media="screen"
      href="<?php echo base_url($asset_path.'css/elfinder.min.css'); ?>">

<?php if($theme=='material'):?>
<link rel="stylesheet" type="text/css" media="screen"
      href="<?php echo base_url($asset_path.'themes/Material/css/theme.css'); ?>">
<link rel="stylesheet" type="text/css" media="screen"
      href="<?php echo base_url($asset_path.'themes/Material/css/theme-light.css'); ?>">
<?php else: ?>
    <link rel="stylesheet" type="text/css" media="screen"
      href="<?php echo base_url($asset_path.'themes/windows-10/css/theme.css'); ?>">
<?php endif;?>

<script src="<?php echo base_url($asset_path.'js/elfinder.min.js'); ?>"></script>
<script src="<?php echo base_url($asset_path.'js/extras/editors.default.min.js'); ?>"></script>
<script src="<?php echo base_url($asset_path.'js/extras/quicklook.googledocs.min.js'); ?>"></script>
<style type="text/css">
    .panel-heading{display: none;}
</style>
<?php
$languages = $this->db->where('name', config_item('language'))->get('languages')->row();

?>
<script type="text/javascript" charset="utf-8">
    $().ready(function () {
        window.setTimeout(function () {
            var locale = "<?= $languages->code;?>";
            var _locale = locale;
            if (locale == 'pt') {
                _locale = 'pt_BR';
            }
            var elf = $('#elfinder').elfinder({
                // lang: 'ru',             // language (OPTIONAL)
                url: '<?= site_url()?>filemanager/elfinder_init',  // connector URL (REQUIRED)
                lang: _locale,
                height: 700,
                uiOptions: {
                    toolbar: [
                        ['back', 'forward'],
//                     ['mkdir'],
                        ['mkdir', 'mkfile', 'upload'],
                        ['open', 'download', 'getfile'],
                        ['info'],
                        ['quicklook'],
                        ['copy', 'cut', 'paste'],
                        ['rm'],
                        ['duplicate', 'rename', 'edit', 'resize'],
                        ['extract', 'archive'],
                        ['search'],
                        ['view'],
                    ],
                },
                customData: {'<?= $this->security->get_csrf_token_name();?>':'<?= $this->security->get_csrf_hash();?>'},
                

            }).elfinder('instance');
        }, 200);
    });
</script>

<!-- Element where elFinder will be created (REQUIRED) -->
<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title"><?= lang('filemanager') ?></div>
    </div>
    <div class="">
        <div id="elfinder"></div>
    </div>

</div>
