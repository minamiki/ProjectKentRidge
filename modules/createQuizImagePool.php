<table border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td><span class="formDesc">OR click on a picture below to use it as the result picture</span></td>
  </tr>
  <tr>
    <td><?php // return uploaded images
$result = $_GET['resultNumber'];
$count = 1;
foreach(glob("../quiz_images/".$_GET['unikey']."*") as $filename){ ?>
<a href="javascript:;" onClick="selectImage(<?php echo $result; ?>, '<?php echo str_replace("'", "\\'", basename($filename)); ?>')"><img src="../quiz_images/imgcrop.php?w=80&h=60&f=<?php echo basename($filename); ?>" width="80" height="60" id="r<?php echo $result; ?>i<?php echo $count; ?>" class="selectImage"></a>
<?php $count++; } ?></td>
  </tr>
</table>
