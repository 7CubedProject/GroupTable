<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <title><?php echo html::specialchars($title) ?></title>
<?
  echo html::stylesheet(array('css/reset', 'css/home'), null, FALSE);
  echo html::stylesheet($css_files, null, FALSE);
?>
</head>
<body>
<?php echo $content ?>

<? echo html::script($js_foot_files, FALSE); ?>

</body>
</html>
