<?php
require_once "config.php";
require_once "function.php";
$access_token = get_access_token();
if ( ! $access_token)
{
    echo "Access token has expired.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>啊嗚的 miiiCasa 照片集</title>
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.9.0/build/reset-fonts-grids/reset-fonts-grids.css">
<link rel="stylesheet" href="style.css">
<script type="text/javascript" src="http://yui.yahooapis.com/3.5.0pr4/build/yui/yui-min.js"></script>
<script src="demo.js"></script>
</head>
<body class="yui3-skin-sam">
    <div id="doc3" class="yui-t3">
        <div id="hd">
            <h1>一頁 miiiCasa - 啊嗚的照片集</h1>
        </div>
        <div id="bd">
            <div id="yui-main">
                <div class="yui-b" id="content">
                    <em>請點選左側的相簿...</em>
                </div>
            </div>
            <div class="yui-b">
<?php
$devices = get_device_list($access_token);
if ( ! count($devices))
{
    echo "No device exists.";
    exit;
}
foreach ($devices as $device) :
    $device_id = $device["device_id"];
?>
                    <div class="device">
                        <h2><?php echo $device["device_annotate"]; ?></h2>
<?php
    $storages = get_storage_list($access_token, $device_id);
    if (count($storages) === 0)
    {
        echo '<p class="empty">此裝置目前沒有任何 Storage</p>';
    }
    foreach ($storages as $storage) :
?>
                        <div class="storage">
                            <h3><?php echo $storage["model"]; ?></h3>
                            <div class="partition">
<?php
        $mountpoints = $storage["mountpoints"];
        foreach ($mountpoints as $mountpoint_data) : ?>
                            <h4><?php echo $mountpoint_data["mountpoint"]; ?></h4>
                            <ul>
<?php
            $mountpoint = $mountpoint_data["mountpoint"];
            $files = get_file_list($access_token, $device_id, $mountpoint, "/");
            foreach ($files as $file):
                if ($file["type"] != 2):
                    continue;
                endif;
                $file_name = $file["name"];
                $mtime = $file["mtime"];
                $img_url = get_photo_cover($access_token, $device_id, $mountpoint, "/", $file_name);
                $site_url = get_site_url($device_id, $mountpoint, $file_name, $mtime);
?>
                                <li>
                                    <a href="<?php echo $site_url; ?>&access_token=<?php echo $access_token; ?>" target="_blank" class="folder-link">
                                        <img src="<?php echo $img_url; ?>" width="100" height="100">
                                        <span></span>
                                    </a>
                                    <h5><?php echo $file_name; ?></h5>
                                </li>
<?php       endforeach; ?>
                            </ul>
                        </div>
<?php endforeach; ?>
                    </div>
<?php endforeach; ?>
                </div>
<?php endforeach; ?>
            </div><!-- #yui-main (end) -->
        </div><!-- #bd (end) -->
    </div><!-- #doc3 (end) -->

    <div id="panel">
        <div class="hd"></div>
        <div class="bd"></div>
        <div class="ft"></div>
    </div>

</body>
</html>
