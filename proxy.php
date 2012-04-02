<?php
require_once "config.php";
require_once "function.php";

$access_token = get_access_token();
$path         = $_GET["fullfilename"];
$device_id    = $_GET["device_id"];
$url          = API_URL . "space/getFileList";
$query        = "access_token=$access_token&device_id=$device_id&path=" . urlencode($path);
$data         = make_request($url, $query);
$data         = json_decode($data, TRUE);
$files        = $data["files"];
?>
<ul>
<?php
foreach ($files as $file):
    $mtime = $file["mtime"];
    $size = $file["size"];
    $ext = $file["ext"];
    $file_name = $file["name"];
    $name = str_replace(".$ext", "", $file_name);
    $url = API_URL . "space/getFile";
    $url.= "?access_token=$access_token&device_id=$device_id&fullfilename=";
    $large_thumb_url = $url . urlencode("$path/.miiithumbs/{$name}_{$mtime}_{$size}_l.jpg");
    $small_thumb_url = $url . urlencode("$path/.miiithumbs/{$name}_{$mtime}_{$size}.jpg");
?>
<li>
    <a href="<?php echo $large_thumb_url; ?>" target="_blank" class="photo-link" title="<?php echo $file["name"]; ?>">
        <img src="<?php echo $small_thumb_url; ?>" width="100" height="100">
        <span></span>
    </a>
</li>
<?php endforeach; ?>
</ul>
