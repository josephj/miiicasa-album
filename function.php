<?php
function redirect()
{
    $auth_url = AUTH_URL . "?client_id=" . API_KEY . "&response_type=code&redirect_uri=" . REDIRECT_URL . "&scope=user_space&state=true";
    header("Location: " . $auth_url);
}
function save_token($data = array())
{
    if (count($data))
    {
        $data["modified"] = time();
        $result = json_encode($data);
    }
    else
    {
        $result = "";
    }
    file_put_contents(TOKEN_FILE, $result);
}

function make_request($url, $query)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function get_access_token()
{
    // File not exists.
    if ( ! file_exists(TOKEN_FILE))
    {
        return FALSE;
    }

    $data = file_get_contents(TOKEN_FILE, TRUE);
    $data = trim($data);
    $data = json_decode($data, TRUE);

    // Format error. (This shouldn't happen)
    if ( ! is_array($data) || ! isset($data["access_token"]))
    {
        return FALSE;
    }

    // Access token is still valid.
    $expire = intval($data["modified"]) + intval($data["expires_in"]);
    if (time() < $expire)
    {
        return $data["access_token"];
    }

    // Expired. Use refresh token method to get new token.
    $fields = array(
        "grant_type=refresh_token",
        "client_id=" . API_KEY,
        "client_secret=" . SECRET_KEY,
        "redirect_uri=" . REDIRECT_URL,
        "refresh_token=" . $data["refresh_token"],
    );
    $data = make_request(TOKEN_URL, implode($fields, "&"));
    $data = json_decode($data, TRUE);

    // Quit if error exists.
    if (isset($data) && isset($data["error"]))
    {
        return FALSE;
    }

    save_token($data);
    return $data["access_token"];
}
function get_device_list($access_token)
{
    $url     = API_URL . "/space/getDeviceList";
    $query   = "access_token={$access_token}";
    $data    = make_request($url, $query);
    $data    = json_decode($data, TRUE);
    return $data["devices"];
}

function get_storage_list($access_token, $device_id)
{
    $url   = API_URL . "/space/getStorageList";
    $query = "access_token={$access_token}&device_id={$device_id}";
    $data  = make_request($url, $query);
    $data  = json_decode($data, TRUE);
    return (isset($data["storages"])) ? $data["storages"] : array();
}

function get_file_list($access_token, $device_id, $mountpoint, $path)
{
    $path = "$mountpoint/miiiCasa_Photos$path";
    $query = array(
        "access_token=$access_token",
        "device_id=$device_id",
        "path=$path",
    );
    $url = API_URL . "/space/getFileList";
    $data = make_request($url, implode("&", $query));
    $data = json_decode($data, TRUE);
    $files = $data["files"];
    return $files;
}

function get_photo_cover($access_token, $device_id, $mountpoint, $path = "/", $folder_name)
{
    $path = "{$mountpoint}/miiiCasa_Photos{$path}";
    $path.= "{$folder_name}/.miiithumbs/cover_100.jpg";
    $path = urlencode($path);
    $query = array(
        "access_token=$access_token",
        "device_id=$device_id",
        "fullfilename=$path",
    );
    $query = implode("&", $query);
    $method = "space/getFile";
    return API_URL . "$method?$query";
}

function get_site_url($device_id, $mountpoint, $file_name, $mtime)
{
    $mountpoint = substr($mountpoint, 1);
    $link = array(
        "http://www.miiicasa.com/space/photos/lists?",
        "&did=$device_id",
        "&s=$mountpoint",
        "&d=/$file_name",
        "&mt=$mtime",
    );
    return implode("", $link);
}
?>
