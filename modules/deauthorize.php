<?php
// Deauthorize Callback
require('variables.php');
require('member.php');

// We set the users's isActive flag to 0

// here you'll get the user id who is removing or deauthorize your application
if(isset($_REQUEST['signed_request'])){
$data = parse_signed_request($_REQUEST['signed_request'], $FB_SECRET);
$member_id = $data['user_id'];

$member = new Member($member_id);
$member->deauthorize();
}else{
	echo "Nothing to do";
}
 
/* These methods are provided by facebook
 
http://developers.facebook.com/docs/authentication/canvas
 
*/
function parse_signed_request($signed_request, $secret) {
  list($encoded_sig, $payload) = explode('.', $signed_request, 2);
 
  // decode the data
  $sig = base64_url_decode($encoded_sig);
  $data = json_decode(base64_url_decode($payload), true);
 
  if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
    error_log('Unknown algorithm. Expected HMAC-SHA256');
    return null;
  }
 
  // check sig
  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
  if ($sig !== $expected_sig) {
    error_log('Bad Signed JSON signature!');
    return null;
  }
 
  return $data;
}
 
function base64_url_decode($input) {
  return base64_decode(strtr($input, '-_', '+/'));
}
?>