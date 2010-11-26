<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Oh noes! | uwdata.ca</title>
<?
  echo html::stylesheet(array('css/reset', 'css/common'), null, FALSE);
?>

<? if (!IN_PRODUCTION) { ?>
<style type="text/css">
<?php include Kohana::find_file('views', 'kohana_errors', FALSE, 'css') ?>
</style>
<? } ?>
</head>
<body>

<h1>Uh oh.</h1>
<h2><?php echo $message ?></h2>
<?php if ( !IN_PRODUCTION AND ! empty($line) AND ! empty($file)): ?>
<p><?php echo Kohana::lang('core.error_file_line', $file, $line) ?></p>
<?php endif ?>
<?php if ( !IN_PRODUCTION AND ! empty($trace)): ?>
<div id="framework_error" style="width:42em;margin:20px auto;">
<h3><?php echo Kohana::lang('core.stack_trace') ?></h3>
<?php echo $trace ?>
</div>
<?php endif ?>

</body>
</html>