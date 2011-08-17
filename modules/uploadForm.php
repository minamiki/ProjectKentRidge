<!-- form for uploading file
When user click submit button, go to another page. Here the specified page is import.php
-->
<div id = "uploadingForm">
<form enctype="multipart/form-data" 
  action="import.php" method="post">
  <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
  <table width="600">
  <tr>
  <td>File name:</td>
  <td><input type="file" name="file" /></td>
  <td><input type="submit" value="Upload"/></td>
  </tr>
  </table>
  </form>
  </div>