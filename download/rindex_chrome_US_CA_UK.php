<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>Routes index</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="css/index20.css" type="text/css" />
</head>
<body>
<?= lp::get_gbahit(isset($cmp)?$cmp:false, isset($marker)?$marker:false, isset($tbType)?$tbType:false)?>

<div class="content">
    <h3>Routes index for <?= ucwords(preg_replace(array('#^rindex_#', '#_#'), array('', ' '), basename(__FILE__, '.php'))) ?></h3>

	<PRE><?php print_r($route->install); ?></PRE>

	<p><a href="<?= $route->download_url() ?>">Download</a></p>

</div>
</body>
</html>
