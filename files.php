<?php
define('VERSION', '2.4.3');//版本号
define('APP_TITLE', 'TfM文件管理器');//标题
$use_auth=true;//使用登录/密码进行身份验证,设置true/false以启用/禁用它,独立于IP白名单和黑名单
//登录账号密码
$auth_users = array(
//'用户名'=>'密码'
'admin'=>'$2y$10$r4zyOicAs.2uX0INoCcYnezWHyEUjT.rqhneuH.jvR8Gc3jFQi4YO'
);

$theme='light';
$readonly_users=array(
'user'
);

$use_highlightjs=true;
$highlightjs_style='vs';
$edit_files=true;
$default_timezone='Etc/UTC';
$root_path=$_SERVER['DOCUMENT_ROOT'];//根目录
$root_url='';
$http_host=$_SERVER['HTTP_HOST'];
$directories_users=array();
$iconv_input_encoding='UTF-8';
$datetime_format='Y/m/d|H:i:s';//文件时间
$allowed_file_extensions='';//允许创建文件格式
$allowed_upload_extensions='';//允许上传文件格式
$exclude_items=array();//隐藏下列文件或者格式 array('myfile.html', 'personal-folder', '*.php', ...)
$online_viewer='fileview';
$sticky_navbar=true;
$max_upload_size_bytes=2048;//允许上传文件大小

/**
OFF=不检查连接IP,默认为OFF
AND=连接必须在白名单上,而不是黑名单上
OR=连接必须在白名单上,或者不在黑名单上
**/
$ip_ruleset='OFF';
$ip_silent=true;
$ip_whitelist=array(//白名单,ipv4或ipv6
'127.0.0.1',
'::1'
);
$ip_blacklist=array(//黑名单,ipv4或ipv6
'0.0.0.0',
'::'
);
define('MAX_UPLOAD_SIZE',$max_upload_size_bytes);
define('FM_THEME',$theme);
//要存储到会话的私钥和会话名称
if(!defined( 'FM_SESSION_ID')){
define('FM_SESSION_ID','易优天空');
}
$show_hidden_files=true;
$report_errors=false;
$hide_Cols=false;
$calc_folder=true;

if ($report_errors==true){
@ini_set('error_reporting',E_ALL);
@ini_set('display_errors', 1);
}else{
@ini_set('error_reporting',E_ALL);
@ini_set('display_errors', 0);
}
if(defined('FM_EMBED')){
$use_auth=false;
$sticky_navbar = false;
}else{
@set_time_limit(600);
date_default_timezone_set($default_timezone);
ini_set('default_charset', 'UTF-8');
if(version_compare(PHP_VERSION, '5.6.0', '<') && function_exists('mb_internal_encoding')){
mb_internal_encoding('UTF-8');
}
if(function_exists('mb_regex_encoding')){
mb_regex_encoding('UTF-8');
}
session_cache_limiter('');
session_name(FM_SESSION_ID );
function session_error_handling_function($code, $msg, $file, $line) {
if($code == 2) {
session_abort();
session_id(session_create_id());
@session_start();
}
}
set_error_handler('session_error_handling_function');
session_start();
restore_error_handler();
}
if(empty($auth_users)){
$use_auth = false;
}
$is_https=isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)|| isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https';
if (isset($_SESSION[FM_SESSION_ID]['logged']) && !empty($directories_users[$_SESSION[FM_SESSION_ID]['logged']])){
$wd=fm_clean_path(dirname($_SERVER['PHP_SELF']));
$root_url=$root_url.$wd.DIRECTORY_SEPARATOR.$directories_users[$_SESSION[FM_SESSION_ID]['logged']];
}
$root_url=fm_clean_path($root_url);
defined('FM_ROOT_URL')||define('FM_ROOT_URL',($is_https?'https':'http').'://'.$http_host.(!empty($root_url)?'/'.$root_url:''));
defined('FM_SELF_URL')||define('FM_SELF_URL',($is_https?'https':'http').'://'.$http_host.$_SERVER['PHP_SELF']);
if(isset($_GET['logout'])){
unset($_SESSION[FM_SESSION_ID]['logged']);
fm_redirect(FM_SELF_URL);
}
if($ip_ruleset !='OFF'){//验证连接IP
$clientIp=$_SERVER['REMOTE_ADDR'];
$proceed=false;
$whitelisted=in_array($clientIp, $ip_whitelist);
$blacklisted=in_array($clientIp, $ip_blacklist);
if($ip_ruleset=='AND'){
if($whitelisted==true && $blacklisted == false){
$proceed = true;
}}else if($ip_ruleset=='OR'){
if($whitelisted==true||$blacklisted==false){
$proceed=true;
}
}
if($proceed==false){
trigger_error('用户连接被拒绝从:' . $clientIp, E_USER_WARNING);
if($ip_silent==false){
fm_set_msg('拒绝访问,适用IP限制', 'error');
fm_show_header_login();
fm_show_message();
}
exit();
}
}

//登录配置
if($use_auth){
if(isset($_SESSION[FM_SESSION_ID]['logged'],$auth_users[$_SESSION[FM_SESSION_ID]['logged']])) {
}elseif(isset($_POST['fm_usr'], $_POST['fm_pwd'])) {
sleep(1);
if(function_exists('password_verify')){
if(isset($auth_users[$_POST['fm_usr']])&&isset($_POST['fm_pwd'])&&password_verify($_POST['fm_pwd'],$auth_users[$_POST['fm_usr']])){
$_SESSION[FM_SESSION_ID]['logged']=$_POST['fm_usr'];
fm_set_msg('欢迎登录使用TfM文件管理器！');
fm_redirect(FM_SELF_URL.'?p=');
}else{
unset($_SESSION[FM_SESSION_ID]['logged']);
fm_set_msg('登录失败,用户名或密码无效!','error');
fm_redirect(FM_SELF_URL);
}
}else{
fm_set_msg('不支持密码\u哈希,请升级PHP版本', 'error');;
}
}else{
unset($_SESSION[FM_SESSION_ID]['logged']);
fm_show_header_login();
?>
<section class="h-100">
<div class="container h-100">
                <div class="row justify-content-md-center h-100">
                    <div class="card-wrapper">
                        <div class="card fat <?php echo fm_get_theme(); ?>">
                            <div class="card-body">
                                <form class="form-signin" action="" method="post" autocomplete="off">
                                    <div class="form-group">
                                       <div class="brand">
                                            
                                        </div>
                                        <div class="text-center">
                                            <h1 class="card-title"><?php echo APP_TITLE; ?></h1>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="form-group">
                                        <label for="fm_usr">账 号:↓</label>
                                        <input type="text" placeholder="账号" class="form-control" id="fm_usr" name="fm_usr" required autofocus>
                                    </div>

                                    <div class="form-group">
                                        <label for="fm_pwd">密 码:↓</label>
                                        <input type="password" placeholder="密码" class="form-control" id="fm_pwd" name="fm_pwd" required>
                                    </div>

                                    <div class="form-group">
                                        <?php fm_show_message(); ?>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success btn-block mt-4" role="button">
                                            登 录 管 理
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="footer text-center">
                            &mdash;&mdash; &copy;
                            <a>2021 TfM文件管理器</a> &mdash;&mdash;
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php
        fm_show_footer_login();
        exit;
    }
}

// update root path
if ($use_auth && isset($_SESSION[FM_SESSION_ID]['logged'])) {
    $root_path = isset($directories_users[$_SESSION[FM_SESSION_ID]['logged']]) ? $directories_users[$_SESSION[FM_SESSION_ID]['logged']] : $root_path;
}

// clean and check $root_path
$root_path = rtrim($root_path, '\\/');
$root_path = str_replace('\\', '/', $root_path);
if (!@is_dir($root_path)) {
    echo "根路径 \"{$root_path}\" 找不到！</h1>";
    exit;
}

defined('FM_SHOW_HIDDEN') || define('FM_SHOW_HIDDEN', $show_hidden_files);
defined('FM_ROOT_PATH') || define('FM_ROOT_PATH', $root_path);
defined('FM_LANG') || define('FM_LANG', $lang);
defined('FM_FILE_EXTENSION') || define('FM_FILE_EXTENSION', $allowed_file_extensions);
defined('FM_UPLOAD_EXTENSION') || define('FM_UPLOAD_EXTENSION', $allowed_upload_extensions);
defined('FM_EXCLUDE_ITEMS') || define('FM_EXCLUDE_ITEMS', $exclude_items);
defined('FM_DOC_VIEWER') || define('FM_DOC_VIEWER', $online_viewer);
define('FM_READONLY', $use_auth && !empty($readonly_users) && isset($_SESSION[FM_SESSION_ID]['logged']) && in_array($_SESSION[FM_SESSION_ID]['logged'], $readonly_users));
define('FM_IS_WIN', DIRECTORY_SEPARATOR == '\\');

// always use ?p=
if (!isset($_GET['p']) && empty($_FILES)) {
    fm_redirect(FM_SELF_URL . '?p=');
}

// get path
$p = isset($_GET['p']) ? $_GET['p'] : (isset($_POST['p']) ? $_POST['p'] : '');

// clean path
$p = fm_clean_path($p);

// for ajax request - save
$input = file_get_contents('php://input');
$_POST = (strpos($input, 'ajax') != FALSE && strpos($input, 'save') != FALSE) ? json_decode($input, true) : $_POST;

// instead globals vars
define('FM_PATH', $p);
define('FM_USE_AUTH', $use_auth);
define('FM_EDIT_FILE', $edit_files);
defined('FM_ICONV_INPUT_ENC') || define('FM_ICONV_INPUT_ENC', $iconv_input_encoding);
defined('FM_USE_HIGHLIGHTJS') || define('FM_USE_HIGHLIGHTJS', $use_highlightjs);
defined('FM_HIGHLIGHTJS_STYLE') || define('FM_HIGHLIGHTJS_STYLE', $highlightjs_style);
defined('FM_DATETIME_FORMAT') || define('FM_DATETIME_FORMAT', $datetime_format);

unset($p, $use_auth, $iconv_input_encoding, $use_highlightjs, $highlightjs_style);

/*************************** ACTIONS ***************************/

// AJAX Request
if (isset($_POST['ajax']) && !FM_READONLY) {

    // save
    if (isset($_POST['type']) && $_POST['type'] == "save") {
        // get current path
        $path = FM_ROOT_PATH;
        if (FM_PATH != '') {
            $path .= '/' . FM_PATH;
        }
        // check path
        if (!is_dir($path)) {
            fm_redirect(FM_SELF_URL . '?p=');
        }
        $file = $_GET['edit'];
        $file = fm_clean_path($file);
        $file = str_replace('/', '', $file);
        if ($file == '' || !is_file($path . '/' . $file)) {
            fm_set_msg('文件找不到！', 'error');
            fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
        }
        header('X-XSS-Protection:0'); 
        $file_path = $path . '/' . $file;
        
        $writedata = $_POST['content'];
        $fd = fopen($file_path, "w");
        $write_results = @fwrite($fd, $writedata);
        fclose($fd);
        if ($write_results === false){ 
            header("HTTP/1.1 500 Internal Server Error");
            die("无法写入文件！-检查权限/所有权！");
        }
        die(true);
    }

    //search : get list of files from the current folder
    if(isset($_POST['type']) && $_POST['type']=="search") {
        $dir = FM_ROOT_PATH;
        $response = scan(fm_clean_path($_POST['path']), $_POST['content']);
        echo json_encode($response);
        exit();
    }
    
    // backup files
    if (isset($_POST['type']) && $_POST['type'] == "backup" && !empty($_POST['file'])) {
        $fileName = $_POST['file'];
        $fullPath = FM_ROOT_PATH . '/';
        if (!empty($_POST['path'])) {
            $relativeDirPath = fm_clean_path($_POST['path']);
            $fullPath .= "{$relativeDirPath}/";
        }
        $date = date("Ymd-His");
        $newFileName = "{$date}-{$fileName}";
        $fullyQualifiedFileName = $fullPath . $fileName;
        try {
            if (!file_exists($fullyQualifiedFileName)) {
                throw new Exception("文件 {$fileName} 找不到");
            }
            if (copy($fullyQualifiedFileName, $fullPath . $newFileName)) {
                echo "备份 {$newFileName} 副本成功！";
            } else {
                throw new Exception("无法复制文件 {$fileName}");
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }



    

    //upload using url
    if(isset($_POST['type']) && $_POST['type'] == "upload" && !empty($_REQUEST["uploadurl"])) {
        $path = FM_ROOT_PATH;
        if (FM_PATH != '') {
            $path .= '/' . FM_PATH;
        }

        $url = !empty($_REQUEST["uploadurl"]) && preg_match("|^http(s)?://.+$|", stripslashes($_REQUEST["uploadurl"])) ? stripslashes($_REQUEST["uploadurl"]) : null;
        $use_curl = false;
        $temp_file = tempnam(sys_get_temp_dir(), "upload-");
        $fileinfo = new stdClass();
        $fileinfo->name = trim(basename($url), ".\x00..\x20");

        $allowed = (FM_UPLOAD_EXTENSION) ? explode(',', FM_UPLOAD_EXTENSION) : false;
        $ext = strtolower(pathinfo($fileinfo->name, PATHINFO_EXTENSION));
        $isFileAllowed = ($allowed) ? in_array($ext, $allowed) : true;
        
        function event_callback ($message) {
            global $callback;
            echo json_encode($message);
        }

        function get_file_path () {
            global $path, $fileinfo, $temp_file;
            return $path."/".basename($fileinfo->name);
        }

        $err = false;

        if(!$isFileAllowed) {
            $err = array("message" => "File extension is not allowed");
            event_callback(array("fail" => $err));
            exit();
        }

        if (!$url) {
            $success = false;
        } else if ($use_curl) {
            @$fp = fopen($temp_file, "w");
            @$ch = curl_init($url);
            curl_setopt($ch, CURLOPT_NOPROGRESS, false );
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            @$success = curl_exec($ch);
            $curl_info = curl_getinfo($ch);
            if (!$success) {
                $err = array("message" => curl_error($ch));
            }
            @curl_close($ch);
            fclose($fp);
            $fileinfo->size = $curl_info["size_download"];
            $fileinfo->type = $curl_info["content_type"];
        } else {
            $ctx = stream_context_create();
            @$success = copy($url, $temp_file, $ctx);
            if (!$success) {
                $err = error_get_last();
            }
        }

        if ($success) {
            $success = rename($temp_file, get_file_path());
        }

        if ($success) {
            event_callback(array("done" => $fileinfo));
        } else {
            unlink($temp_file);
            if (!$err) {
                $err = array("message" => "Invalid url parameter");
            }
            event_callback(array("fail" => $err));
        }
    }

    exit();
}

// Delete file / folder
if (isset($_GET['del']) && !FM_READONLY) {
    $del = str_replace( '/', '', fm_clean_path( $_GET['del'] ) );
    if ($del != '' && $del != '..' && $del != '.') {
        $path = FM_ROOT_PATH;
        if (FM_PATH != '') {
            $path .= '/' . FM_PATH;
        }
        $is_dir = is_dir($path . '/' . $del);
        if (fm_rdelete($path . '/' . $del)) {
            $msg = $is_dir ? '文件夹 <b>%s</b> 删除!' : '文件 <b>%s</b> 删除！';
            fm_set_msg(sprintf($msg, fm_enc($del)));
        } else {
            $msg = $is_dir ? '文件夹 <b>%s</b> 未删除' : '文件 <b>%s</b> 未删除';
            fm_set_msg(sprintf($msg, fm_enc($del)), 'error');
        }
    } else {
        fm_set_msg('无效的文件或文件夹名！', 'error');
    }
    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

// Create folder
if (isset($_GET['new']) && isset($_GET['type']) && !FM_READONLY) {
    $type = $_GET['type'];
    $new = str_replace( '/', '', fm_clean_path( strip_tags( $_GET['new'] ) ) );
    if (fm_isvalid_filename($new) && $new != '' && $new != '..' && $new != '.') {
        $path = FM_ROOT_PATH;
        if (FM_PATH != '') {
            $path .= '/' . FM_PATH;
        }
        if ($_GET['type'] == "file") {
            if (!file_exists($path . '/' . $new)) {
                if(fm_is_valid_ext($new)) {
                    @fopen($path . '/' . $new, 'w') or die('无法打开文件:  ' . $new);
                    fm_set_msg(sprintf('文件"<b>%s</b>"创建成功！', fm_enc($new)));
                } else {
                    fm_set_msg('不允许文件扩展名！', 'error');
                }
            } else {
                fm_set_msg(sprintf('文件"<b>%s</b>"已经存在！', fm_enc($new)), 'alert');
            }
        } else {
            if (fm_mkdir($path . '/' . $new, false) === true) {
                fm_set_msg(sprintf('文件夹"<b>%s</b>"创建成功！', $new));
            } elseif (fm_mkdir($path . '/' . $new, false) === $path . '/' . $new) {
                fm_set_msg(sprintf('文件夹"<b>%s</b>"已经存在！', fm_enc($new)), 'alert');
            } else {
                fm_set_msg(sprintf('文件夹"<b>%s</b>"未创建(失败)！', fm_enc($new)), 'error');
            }
        }
    } else {
        fm_set_msg('文件或文件夹名称中的无效字符！', 'error');
    }
    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

// Copy folder / file
if (isset($_GET['copy'], $_GET['finish']) && !FM_READONLY) {
    // from
    $copy = $_GET['copy'];
    $copy = fm_clean_path($copy);
    // empty path
    if ($copy == '') {
        fm_set_msg('源路径未定义！', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }
    // abs path from
    $from = FM_ROOT_PATH . '/' . $copy;
    // abs path to
    $dest = FM_ROOT_PATH;
    if (FM_PATH != '') {
        $dest .= '/' . FM_PATH;
    }
    $dest .= '/' . basename($from);
    // move?
    $move = isset($_GET['move']);
    // copy/move/duplicate
    if ($from != $dest) {
        $msg_from = trim(FM_PATH . '/' . basename($from), '/');
        if ($move) { // Move and to != from so just perform move
            $rename = fm_rename($from, $dest);
            if ($rename) {
                fm_set_msg(sprintf('从 <b>%s</b> to <b>%s</b>', fm_enc($copy), fm_enc($msg_from)));
            } elseif ($rename === null) {
                fm_set_msg('具有此路径的文件或文件夹已经存在！', 'alert');

            } else {
                fm_set_msg(sprintf('从移动时出错 <b>%s</b> to <b>%s</b>', fm_enc($copy), fm_enc($msg_from)), 'error');
            }
        } else { // Not move and to != from so copy with original name
            if (fm_rcopy($from, $dest)) {
                fm_set_msg(sprintf('复制 <b>%s</b> 至 <b>%s</b>', fm_enc($copy), fm_enc($msg_from)));
            } else {
                fm_set_msg(sprintf('从 <b>%s</b> 复制至 <b>%s</b> 时出错！', fm_enc($copy), fm_enc($msg_from)), 'error');
            }
        }
    } else {
       if (!$move){ //Not move and to = from so duplicate
            $msg_from = trim(FM_PATH . '/' . basename($from), '/');
            $fn_parts = pathinfo($from);
            $extension_suffix = '';
            if(!is_dir($from)){
               $extension_suffix = '.'.$fn_parts['extension'];
            }
            //Create new name for duplicate
            $fn_duplicate = $fn_parts['dirname'].'/'.$fn_parts['filename'].'-'.date('YmdHis').$extension_suffix;
            $loop_count = 0;
            $max_loop = 1000;
            // Check if a file with the duplicate name already exists, if so, make new name (edge case...)
            while(file_exists($fn_duplicate) & $loop_count < $max_loop){
               $fn_parts = pathinfo($fn_duplicate);
               $fn_duplicate = $fn_parts['dirname'].'/'.$fn_parts['filename'].'-复制'.$extension_suffix;
               $loop_count++;
            }
            if (fm_rcopy($from, $fn_duplicate, False)) {
                fm_set_msg(sprintf('复制 <b>%s</b> 至 <b>%s</b>', fm_enc($copy), fm_enc($fn_duplicate)));
            } else {
                fm_set_msg(sprintf('从复制时出错 <b>%s</b> to <b>%s</b>', fm_enc($copy), fm_enc($fn_duplicate)), 'error');
            }
       }
       else{
           fm_set_msg('路径不能相等！', 'alert');
       }
    }
    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

// Mass copy files/ folders
if (isset($_POST['file'], $_POST['copy_to'], $_POST['finish']) && !FM_READONLY) {
    // from
    $path = FM_ROOT_PATH;
    if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
    }
    // to
    $copy_to_path = FM_ROOT_PATH;
    $copy_to = fm_clean_path($_POST['copy_to']);
    if ($copy_to != '') {
        $copy_to_path .= '/' . $copy_to;
    }
    if ($path == $copy_to_path) {
        fm_set_msg('路径不能相等！', 'alert');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }
    if (!is_dir($copy_to_path)) {
        if (!fm_mkdir($copy_to_path, true)) {
            fm_set_msg('无法创建目标文件夹！', 'error');
            fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
        }
    }
    // move?
    $move = isset($_POST['move']);
    // copy/move
    $errors = 0;
    $files = $_POST['file'];
    if (is_array($files) && count($files)) {
        foreach ($files as $f) {
            if ($f != '') {
                // abs path from
                $from = $path . '/' . $f;
                // abs path to
                $dest = $copy_to_path . '/' . $f;
                // do
                if ($move) {
                    $rename = fm_rename($from, $dest);
                    if ($rename === false) {
                        $errors++;
                    }
                } else {
                    if (!fm_rcopy($from, $dest)) {
                        $errors++;
                    }
                }
            }
        }
        if ($errors == 0) {
            $msg = $move ? '已移动选定的文件/夹！':'已复制选定的文件/夹！';
            fm_set_msg($msg);
        } else {
            $msg = $move ? '移动文件/夹时出错！' : '复制文件/夹时出错！';
            fm_set_msg($msg, 'error');
        }
    } else {
        fm_set_msg('未选择任何内容！', 'alert');
    }
    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

// Rename
if (isset($_GET['ren'], $_GET['to']) && !FM_READONLY) {
    // old name
    $old = $_GET['ren'];
    $old = fm_clean_path($old);
    $old = str_replace('/', '', $old);
    // new name
    $new = $_GET['to'];
    $new = fm_clean_path(strip_tags($new));
    $new = str_replace('/', '', $new);
    // path
    $path = FM_ROOT_PATH;
    if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
    }
    // rename
    if (fm_isvalid_filename($new) && $old != '' && $new != '') {
        if (fm_rename($path . '/' . $old, $path . '/' . $new)) {
            fm_set_msg(sprintf('<b>%s</b> 已经重命名为 <b>%s</b> ！', fm_enc($old), fm_enc($new)));
        } else {
            fm_set_msg(sprintf('重命名 <b>%s</b> 为 <b>%s</b> 时出错！', fm_enc($old), fm_enc($new)), 'error');
        }
    } else {
        fm_set_msg('文件名中的字符无效！', 'error');
    }
    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

// Download
if (isset($_GET['dl'])) {
    $dl = $_GET['dl'];
    $dl = fm_clean_path($dl);
    $dl = str_replace('/', '', $dl);
    $path = FM_ROOT_PATH;
    if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
    }
    if ($dl != '' && is_file($path . '/' . $dl)) {
        fm_download_file($path . '/' . $dl, $dl, 1024);
        exit;
    } else {
        fm_set_msg('找不到文件！', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }
}

// Upload
if (!empty($_FILES) && !FM_READONLY) {
    $override_file_name = false;
    $f = $_FILES;
    $path = FM_ROOT_PATH;
    $ds = DIRECTORY_SEPARATOR;
    if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
    }

    $errors = 0;
    $uploads = 0;
    $allowed = (FM_UPLOAD_EXTENSION) ? explode(',', FM_UPLOAD_EXTENSION) : false;
    $response = array (
        'status' => 'error',
        'info'   => '哎呀！再试一次！'
    );

    $filename = $f['file']['name'];
    $tmp_name = $f['file']['tmp_name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $isFileAllowed = ($allowed) ? in_array($ext, $allowed) : true;

    $targetPath = $path . $ds;
    if ( is_writable($targetPath) ) {
        $fullPath = $path . '/' . $_REQUEST['fullpath'];
        $folder = substr($fullPath, 0, strrpos($fullPath, "/"));

        if(file_exists ($fullPath) && !$override_file_name) {
            $ext_1 = $ext ? '.'.$ext : '';
            $fullPath = str_replace($ext_1, '', $fullPath) .'_'. date('ymdHis'). $ext_1;
        }

        if (!is_dir($folder)) {
            $old = umask(0);
            mkdir($folder, 0777, true);
            umask($old);
        }

        if (empty($f['file']['error']) && !empty($tmp_name) && $tmp_name != 'none' && $isFileAllowed) {
            if (move_uploaded_file($tmp_name, $fullPath)) {
                // Be sure that the file has been uploaded
                if ( file_exists($fullPath) ) {
                    $response = array (
                        'status'    => 'success',
                        'info' =>"文件上载成功！"
                    );
                } else {
                    $response = array (
                        'status' => 'error',
                        'info'   => '无法上载请求的文件！'
                    );
                }
            } else {
                $response = array (
                    'status'    => 'error',
                    'info'      => "上载文件时出错。上载的文件 $uploads",
                );
            }
        }
    } else {
        $response = array (
            'status' => 'error',
            'info'   => '指定的上载文件夹不可写！'
        );
    }
    // Return the response
    echo json_encode($response);
    exit();
}

// Mass deleting
if (isset($_POST['group'], $_POST['delete']) && !FM_READONLY) {
    $path = FM_ROOT_PATH;
    if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
    }

    $errors = 0;
    $files = $_POST['file'];
    if (is_array($files) && count($files)) {
        foreach ($files as $f) {
            if ($f != '') {
                $new_path = $path . '/' . $f;
                if (!fm_rdelete($new_path)) {
                    $errors++;
                }
            }
        }
        if ($errors == 0) {
            fm_set_msg('已删除选定的文件和文件夹!');
        } else {
            fm_set_msg('删除文件/夹时出错！', 'error');
        }
    } else {
        fm_set_msg('未选择任何内容！', 'alert');
    }

    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

// Pack files
if (isset($_POST['group']) && (isset($_POST['zip']) || isset($_POST['tar'])) && !FM_READONLY) {
    $path = FM_ROOT_PATH;
    $ext = 'zip';
    if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
    }

    //set pack type
    $ext = isset($_POST['tar']) ? 'tar' : 'zip';


    if (($ext == "zip" && !class_exists('ZipArchive')) || ($ext == "tar" && !class_exists('PharData'))) {
        fm_set_msg('无法使用压缩包操作！', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }

    $files = $_POST['file'];
    if (!empty($files)) {
        chdir($path);

        if (count($files) == 1) {
            $one_file = reset($files);
            $one_file = basename($one_file);
            $zipname = $one_file.'-'.date('Ymd-His').'.'.$ext;
        } else {
            $zipname = date('Ymd-His').'.'.$ext;
        }

        if($ext == 'zip') {
            $zipper = new FM_Zipper();
            $res = $zipper->create($zipname, $files);
        } elseif ($ext == 'tar') {
            $tar = new FM_Zipper_Tar();
            $res = $tar->create($zipname, $files);
        }

        if ($res) {
            fm_set_msg(sprintf('压缩包 <b>%s</b> 压缩成功！', fm_enc($zipname)));
        } else {
            fm_set_msg('压缩失败！', 'error');
        }
    } else {
        fm_set_msg('未选择任何内容！', 'alert');
    }

    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

// Unpack
if (isset($_GET['unzip']) && !FM_READONLY) {
    $unzip = $_GET['unzip'];
    $unzip = fm_clean_path($unzip);
    $unzip = str_replace('/', '', $unzip);
    $isValid = false;

    $path = FM_ROOT_PATH;
    if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
    }

    if ($unzip != '' && is_file($path . '/' . $unzip)) {
        $zip_path = $path . '/' . $unzip;
        $ext = pathinfo($zip_path, PATHINFO_EXTENSION);
        $isValid = true;
    } else {
        fm_set_msg('找不到文件！', 'error');
    }


    if (($ext == "zip" && !class_exists('ZipArchive')) || ($ext == "tar" && !class_exists('PharData'))) {
        fm_set_msg('无法使用压缩包操作！', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }

    if ($isValid) {
        //to folder
        $tofolder = '';
        if (isset($_GET['tofolder'])) {
            $tofolder = pathinfo($zip_path, PATHINFO_FILENAME);
            if (fm_mkdir($path . '/' . $tofolder, true)) {
                $path .= '/' . $tofolder;
            }
        }

        if($ext == "zip") {
            $zipper = new FM_Zipper();
            $res = $zipper->unzip($zip_path, $path);
        } elseif ($ext == "tar") {
            try {
                $gzipper = new PharData($zip_path);
                if (@$gzipper->extractTo($path,null, true)) {
                    $res = true;
                } else {
                    $res = false;
                }
            } catch (Exception $e) {
                //TODO:: need to handle the error
                $res = true;
            }
        }

        if ($res) {
            fm_set_msg('解压成功！');
        } else {
            fm_set_msg('解压失败！', 'error');
        }

    } else {
        fm_set_msg('找不到文件！', 'error');
    }
    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

// Change Perms (not for Windows)
if (isset($_POST['chmod']) && !FM_READONLY && !FM_IS_WIN) {
    $path = FM_ROOT_PATH;
    if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
    }

    $file = $_POST['chmod'];
    $file = fm_clean_path($file);
    $file = str_replace('/', '', $file);
    if ($file == '' || (!is_file($path . '/' . $file) && !is_dir($path . '/' . $file))) {
        fm_set_msg('找不到文件！', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }

    $mode = 0;
    if (!empty($_POST['ur'])) {
        $mode |= 0400;
    }
    if (!empty($_POST['uw'])) {
        $mode |= 0200;
    }
    if (!empty($_POST['ux'])) {
        $mode |= 0100;
    }
    if (!empty($_POST['gr'])) {
        $mode |= 0040;
    }
    if (!empty($_POST['gw'])) {
        $mode |= 0020;
    }
    if (!empty($_POST['gx'])) {
        $mode |= 0010;
    }
    if (!empty($_POST['or'])) {
        $mode |= 0004;
    }
    if (!empty($_POST['ow'])) {
        $mode |= 0002;
    }
    if (!empty($_POST['ox'])) {
        $mode |= 0001;
    }

    if (@chmod($path . '/' . $file, $mode)) {
        fm_set_msg('权限修改成功！');
    } else {
        fm_set_msg('权限修改失败！', 'error');
    }

    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

/*************************** /ACTIONS ***************************/

// get current path
$path = FM_ROOT_PATH;
if (FM_PATH != '') {
    $path .= '/' . FM_PATH;
}

// check path
if (!is_dir($path)) {
    fm_redirect(FM_SELF_URL . '?p=');
}

// get parent folder
$parent = fm_get_parent_path(FM_PATH);

$objects = is_readable($path) ? scandir($path) : array();
$folders = array();
$files = array();
$current_path = array_slice(explode("/",$path), -1)[0];
if (is_array($objects) && fm_is_exclude_items($current_path)) {
    foreach ($objects as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (!FM_SHOW_HIDDEN && substr($file, 0, 1) === '.') {
            continue;
        }
        $new_path = $path . '/' . $file;
        if (@is_file($new_path) && fm_is_exclude_items($file)) {
            $files[] = $file;
        } elseif (@is_dir($new_path) && $file != '.' && $file != '..' && fm_is_exclude_items($file)) {
            $folders[] = $file;
        }
    }
}

if (!empty($files)) {
    natcasesort($files);
}
if (!empty($folders)) {
    natcasesort($folders);
}

// upload form
if (isset($_GET['upload']) && !FM_READONLY) {
    fm_show_header(); // HEADER
    fm_show_nav_path(FM_PATH); // current path
    //get the allowed file extensions
    function getUploadExt() {
        $extArr = explode(',', FM_UPLOAD_EXTENSION);
        if(FM_UPLOAD_EXTENSION && $extArr) {
            array_walk($extArr, function(&$x) {$x = ".$x";});
            return implode(',', $extArr);
        }
        return '';
    }
    ?>

    <link href="https://cdn.bootcdn.net/ajax/libs/dropzone/5.7.2/dropzone.min.css" rel="stylesheet">
    <div class="path">

        <div class="card mb-2 fm-upload-wrapper <?php echo fm_get_theme(); ?>">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#fileUploader" data-target="#fileUploader"><i class="fa fa-arrow-circle-o-up"></i>上传文件</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#urlUploader" class="js-url-upload" data-target="#urlUploader"><i class="fa fa-link"></i>从网址上传</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <p class="card-text">
                    <a href="?p=<?php echo FM_PATH ?>" class="float-right"><i class="fa fa-chevron-circle-left go-back"></i>返 回</a>
                    目标文件夹: <?php echo fm_enc(fm_convert_win(FM_ROOT_PATH . '/' . FM_PATH)) ?>
                </p>

                <form action="<?php echo htmlspecialchars(FM_SELF_URL) . '?p=' . fm_enc(FM_PATH) ?>" class="dropzone card-tabs-container" id="fileUploader" enctype="multipart/form-data">
                    <input type="hidden" name="p" value="<?php echo fm_enc(FM_PATH) ?>">
                    <input type="hidden" name="fullpath" id="fullpath" value="<?php echo fm_enc(FM_PATH) ?>">
                    <div class="fallback">
                        <input name="file" type="file" multiple/>
                    </div>
                </form>

                <div class="upload-url-wrapper card-tabs-container hidden" id="urlUploader">
                    <form id="js-form-url-upload" class="form-inline" onsubmit="return upload_from_url(this);" method="POST" action="">
                        <input type="hidden" name="type" value="upload" aria-label="hidden" aria-hidden="true">
                        <input type="url" placeholder="文件链接地址" name="uploadurl" required class="form-control" style="width: 80%">
                        <button type="submit" class="btn btn-primary ml-3">上传</button>
                        <div class="lds-facebook"><div></div><div></div><div></div></div>
                    </form>
                    <div id="js-url-upload__list" class="col-9 mt-3"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.bootcdn.net/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
    <script>
        Dropzone.options.fileUploader = {
            timeout: 120000,
            maxFilesize: <?php echo MAX_UPLOAD_SIZE; ?>,
            acceptedFiles : "<?php echo getUploadExt() ?>",
            init: function () {
                this.on("sending", function (file, xhr, formData) {
                    let _path = (file.fullPath) ? file.fullPath : file.name;
                    document.getElementById("fullpath").value = _path;
                    xhr.ontimeout = (function() {
                        toast('Error: Server Timeout');
                    });
                }).on("success", function (res) {
                    let _response = JSON.parse(res.xhr.response);
                    if(_response.status == "error") {
                        toast(_response.info);
                    }
                }).on("error", function(file, response) {
                    toast(response);
                });
            }
        }
    </script>
    <?php
    fm_show_footer();
    exit;
}

// copy form POST
if (isset($_POST['copy']) && !FM_READONLY) {
    $copy_files = isset($_POST['file']) ? $_POST['file'] : null;
    if (!is_array($copy_files) || empty($copy_files)) {
        fm_set_msg('什么都没选！', 'alert');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }

    fm_show_header(); // HEADER
    fm_show_nav_path(FM_PATH); // current path
    ?>
    <div class="path">
        <div class="card <?php echo fm_get_theme(); ?>">
            <div class="card-header">
                <h6>移动/复制的文件/夹</h6>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <input type="hidden" name="p" value="<?php echo fm_enc(FM_PATH) ?>">
                    <input type="hidden" name="finish" value="1">
                    <?php
                    foreach ($copy_files as $cf) {
                        echo '<input type="hidden" name="file[]" value="' . fm_enc($cf) . '">' . PHP_EOL;
                    }
                    ?>
                    <p class="break-word">需要移动/复制的文件/夹:<br><b><?php echo implode('</b>, <b>', $copy_files) ?></b></p>
                    <p class="break-word">从:<?php echo fm_enc(fm_convert_win(FM_ROOT_PATH . '/' . FM_PATH)) ?><br>
                        <label for="inp_copy_to">移动/复制到:</label>
                        <?php echo FM_ROOT_PATH ?>/<input type="text" placeholder="所需要移动/复制的目录" name="copy_to" id="inp_copy_to" value="<?php echo fm_enc(FM_PATH) ?>">
                    </p>
                    <p class="custom-checkbox custom-control"><input type="checkbox" name="move" value="1" id="js-move-files" class="custom-control-input"><label for="js-move-files" class="custom-control-label" style="vertical-align: sub">移 动</label></p>
                    <p>
                        <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i>立即执行</button> &nbsp;
                        <b><a href="?p=<?php echo urlencode(FM_PATH) ?>" class="btn btn-outline-primary"><i class="fa fa-times-circle"></i>取消执行</a></b>
                    </p>
                </form>
            </div>
        </div>
    </div>
    <?php
    fm_show_footer();
    exit;
}

// copy form
if (isset($_GET['copy']) && !isset($_GET['finish']) && !FM_READONLY) {
    $copy = $_GET['copy'];
    $copy = fm_clean_path($copy);
    if ($copy == '' || !file_exists(FM_ROOT_PATH . '/' . $copy)) {
        fm_set_msg('找不到文件！', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }

    fm_show_header(); // HEADER
    fm_show_nav_path(FM_PATH); // current path
    ?>
    <div class="path">
        <p><b>复 制</b></p>
        <p class="break-word">
            源路径:<?php echo fm_enc(fm_convert_win(FM_ROOT_PATH . '/' . $copy)) ?><br>
            目标文件夹:<?php echo fm_enc(fm_convert_win(FM_ROOT_PATH . '/' . FM_PATH)) ?>
        </p>
        <p>
            <b><a href="?p=<?php echo urlencode(FM_PATH) ?>&amp;copy=<?php echo urlencode($copy) ?>&amp;finish=1"><i class="fa fa-check-circle"></i>复 制</a></b> &nbsp;
            <b><a href="?p=<?php echo urlencode(FM_PATH) ?>&amp;copy=<?php echo urlencode($copy) ?>&amp;finish=1&amp;move=1"><i class="fa fa-check-circle"></i>移 动</a></b> &nbsp;
            <b><a href="?p=<?php echo urlencode(FM_PATH) ?>"><i class="fa fa-times-circle"></i>取 消</a></b>
        </p>
        <p><i>选择文件夹:</i></p>
        <ul class="folders break-word">
            <?php
            if ($parent !== false) {
                ?>
                <li><a href="?p=<?php echo urlencode($parent) ?>&amp;copy=<?php echo urlencode($copy) ?>"><i class="fa fa-chevron-circle-left"></i>返 回</a></li>
                <?php
            }
            foreach ($folders as $f) {
                ?>
                <li>
                    <a href="?p=<?php echo urlencode(trim(FM_PATH . '/' . $f, '/')) ?>&amp;copy=<?php echo urlencode($copy) ?>"><i class="fa fa-folder-o"></i> <?php echo fm_convert_win($f) ?></a></li>
                <?php
            }
            ?>
        </ul>
    </div>
    <?php
    fm_show_footer();
    exit;
}

// file viewer
if (isset($_GET['view'])) {
    $file = $_GET['view'];
    $quickView = (isset($_GET['quickView']) && $_GET['quickView'] == 1) ? true : false;
    $file = fm_clean_path($file, false);
    $file = str_replace('/', '', $file);
    if ($file == '' || !is_file($path . '/' . $file) || in_array($file, $GLOBALS['exclude_items'])) {
        fm_set_msg('找不到文件！', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }

    if(!$quickView) {
        fm_show_header(); // HEADER
        fm_show_nav_path(FM_PATH); // current path
    }

    $file_url = FM_ROOT_URL . fm_convert_win((FM_PATH != '' ? '/' . FM_PATH : '') . '/' . $file);
    $file_path = $path . '/' . $file;

    $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    $mime_type = fm_get_mime_type($file_path);
    $filesize_raw = fm_get_size($file_path);
    $filesize = fm_get_filesize($filesize_raw);

    $is_zip = false;
    $is_gzip = false;
    $is_image = false;
    $is_audio = false;
    $is_video = false;
    $is_text = false;
    $is_onlineViewer = false;

    $view_title = '文件';
    $filenames = false; // for zip
    $content = ''; // for text
    $online_viewer = strtolower(FM_DOC_VIEWER);

    if($online_viewer && $online_viewer !== 'false' && in_array($ext, fm_get_onlineViewer_exts())){
        $is_onlineViewer = true;
    }
    elseif ($ext == 'zip' || $ext == 'tar') {
        $is_zip = true;
        $view_title ='压缩包';
        $filenames = fm_get_zif_info($file_path, $ext);
    } elseif (in_array($ext, fm_get_image_exts())) {
        $is_image = true;
        $view_title = '图片';
    } elseif (in_array($ext, fm_get_audio_exts())) {
        $is_audio = true;
        $view_title = '音乐';
    } elseif (in_array($ext, fm_get_video_exts())) {
        $is_video = true;
        $view_title = '视频';
    } elseif (in_array($ext, fm_get_text_exts()) || substr($mime_type, 0, 4) == 'text' || in_array($mime_type, fm_get_text_mimes())) {
        $is_text = true;
        $content = file_get_contents($file_path);
    }

    ?>
    <div class="row">
        <div class="col-12">
            <?php if(!$quickView) { ?>
                <p class="break-word"><b><?php echo $view_title ?> "<?php echo fm_enc(fm_convert_win($file)) ?>"</b></p>
                <p class="break-word">
                    完整路径↓:<br><?php echo fm_enc(fm_convert_win($file_path)) ?><br>
                    文件大小:<?php echo ($filesize_raw <= 1000) ? "$filesize_raw bytes" : $filesize; ?><br>
                    MIME类型:<?php echo $mime_type ?><br>
                    <?php
                    // ZIP info
                    if (($is_zip || $is_gzip) && $filenames !== false) {
                        $total_files = 0;
                        $total_comp = 0;
                        $total_uncomp = 0;
                        foreach ($filenames as $fn) {
                            if (!$fn['folder']) {
                                $total_files++;
                            }
                            $total_comp += $fn['compressed_size'];
                            $total_uncomp += $fn['filesize'];
                        }
                        ?>
                        压缩包内文件:<?php echo $total_files ?>个<br>
                        总大小:<?php echo fm_get_filesize($total_uncomp) ?><br>
                        压缩包内大小:<?php echo fm_get_filesize($total_comp) ?><br>
                        压缩率:<?php echo round(($total_comp / $total_uncomp) * 100) ?>%<br>
                        <?php
                    }
                    // Image info
                    if ($is_image) {
                        $image_size = getimagesize($file_path);
                        echo '图像大小: ' . (isset($image_size[0]) ? $image_size[0] : '0') . ' x ' . (isset($image_size[1]) ? $image_size[1] : '0') . '<br>';
                    }
                    // Text info
                    if ($is_text) {
                        $is_utf8 = fm_is_utf8($content);
                        if (function_exists('iconv')) {
                            if (!$is_utf8) {
                                $content = iconv(FM_ICONV_INPUT_ENC, 'UTF-8//IGNORE', $content);
                            }
                        }
                        echo '字符编码: ' . ($is_utf8?'UTF-8' : '8 BIT') . '<br>';
                    }
                    ?>
                </p>
                <p>
                    <b><a href="?p=<?php echo urlencode(FM_PATH) ?>&amp;dl=<?php echo urlencode($file) ?>"><i class="fa fa-cloud-download"></i>下 载</a></b> &nbsp;
                    <b><a href="<?php echo fm_enc($file_url) ?>" target="_blank"><i class="fa fa-external-link-square"></i>打 开</a></b>
                    &nbsp;
                    <?php
                    // ZIP actions
                    if (!FM_READONLY && ($is_zip || $is_gzip) && $filenames !== false) {
                        $zip_name = pathinfo($file_path, PATHINFO_FILENAME);
                        ?>
                        <b><a href="?p=<?php echo urlencode(FM_PATH) ?>&amp;unzip=<?php echo urlencode($file) ?>"><i class="fa fa-check-circle"></i>解 压</a></b> &nbsp;
                        <b><a href="?p=<?php echo urlencode(FM_PATH) ?>&amp;unzip=<?php echo urlencode($file) ?>&amp;tofolder=1"><i class="fa fa-check-circle"></i>解压到文件夹</a></b> &nbsp;
                        <?php
                    }
                    if ($is_text && !FM_READONLY) {
                        ?>
                        <b><a href="?p=<?php echo urlencode(trim(FM_PATH)) ?>&amp;edit=<?php echo urlencode($file) ?>" class="edit-file"><i class="fa fa-pencil-square"></i>普通编辑</a></b> &nbsp;
                        <b><a href="?p=<?php echo urlencode(trim(FM_PATH)) ?>&amp;edit=<?php echo urlencode($file) ?>&env=ace"class="edit-file"><i class="fa fa-pencil-square-o"></i>高级编辑</a></b> &nbsp;
                    <?php } ?>
                    <b><a href="?p=<?php echo urlencode(FM_PATH) ?>"><i class="fa fa-chevron-circle-left go-back"></i>返 回</a></b>
                </p>
                <?php
            }
            if($is_onlineViewer) {
                if($online_viewer =='xdocin') {
                    echo '<iframe src="https://view.xdocin.com/xdoc?_xdoc=' . fm_enc($file_url) . '" frameborder="no" style="width:100%;min-height:460px"></iframe>';
                } else if($online_viewer == 'fileview') {
                    echo '<iframe src="https://fileview.xrbk.top/onlinePreview?url=' . fm_enc($file_url) . '" frameborder="no" style="width:100%;min-height:460px"></iframe>';
                }
            } elseif ($is_zip) {
                // ZIP content
                if ($filenames !== false) {
                    echo '<code class="maxheight">';
                    foreach ($filenames as $fn) {
                        if ($fn['folder']) {
                            echo '<b>' . fm_enc($fn['name']) . '</b><br>';
                        } else {
                            echo $fn['name'] . ' (' . fm_get_filesize($fn['filesize']) . ')<br>';
                        }
                    }
                    echo '</code>';
                } else {
                    echo '<p>获取存档信息时出错！</p>';
                }
            } elseif ($is_image) {
                // Image content
                if (in_array($ext, array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'ico', 'svg'))) {
                    echo '<p><img src="' . fm_enc($file_url) . '" alt="" class="preview-img"></p>';
                }
            } elseif ($is_audio) {
                // Audio content
                echo '<p><audio src="' . fm_enc($file_url) . '" controls preload="metadata"></audio></p>';
            } elseif ($is_video) {
                // Video content
                echo '<div class="preview-video"><video src="' . fm_enc($file_url) . '" width="640" height="360" controls preload="metadata"></video></div>';
            } elseif ($is_text) {
                if (FM_USE_HIGHLIGHTJS) {
                    // highlight
                    $hljs_classes = array(
                        'shtml' => 'xml',
                        'htaccess' => 'apache',
                        'phtml' => 'php',
                        'lock' => 'json',
                        'svg' => 'xml',
                    );
                    $hljs_class = isset($hljs_classes[$ext]) ? 'lang-' . $hljs_classes[$ext] : 'lang-' . $ext;
                    if (empty($ext) || in_array(strtolower($file), fm_get_text_names()) || preg_match('#\.min\.(css|js)$#i', $file)) {
                        $hljs_class = 'nohighlight';
                    }
                    $content = '<pre class="with-hljs"><code class="' . $hljs_class . '">' . fm_enc($content) . '</code></pre>';
                } elseif (in_array($ext, array('php', 'php4', 'php5', 'phtml', 'phps'))) {
                    // php highlight
                    $content = highlight_string($content, true);
                } else {
                    $content = '<pre>' . fm_enc($content) . '</pre>';
                }
                echo $content;
            }
            ?>
        </div>
    </div>
    <?php
    if(!$quickView) {
        fm_show_footer();
    }
    exit;
}

// file editor
if (isset($_GET['edit'])) {
    $file = $_GET['edit'];
    $file = fm_clean_path($file, false);
    $file = str_replace('/', '', $file);
    if ($file == '' || !is_file($path . '/' . $file)) {
        fm_set_msg('找不到文件！', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }
    header('X-XSS-Protection:0');
    fm_show_header(); // HEADER
    fm_show_nav_path(FM_PATH); // current path

    $file_url = FM_ROOT_URL . fm_convert_win((FM_PATH != '' ? '/' . FM_PATH : '') . '/' . $file);
    $file_path = $path . '/' . $file;

    // normal editer
    $isNormalEditor = true;
    if (isset($_GET['env'])) {
        if ($_GET['env'] == "ace") {
            $isNormalEditor = false;
        }
    }

    // Save File
    if (isset($_POST['savedata'])) {
        $writedata = $_POST['savedata'];
        $fd = fopen($file_path, "w");
        @fwrite($fd, $writedata);
        fclose($fd);
        fm_set_msg('文件保存成功！');
    }

    $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    $mime_type = fm_get_mime_type($file_path);
    $filesize = filesize($file_path);
    $is_text = false;
    $content = ''; // for text

    if (in_array($ext, fm_get_text_exts()) || substr($mime_type, 0, 4) == 'text' || in_array($mime_type, fm_get_text_mimes())) {
        $is_text = true;
        $content = file_get_contents($file_path);
    }

    ?>
    <div class="path">
        <div class="row">
            <div class="col-xs-12 col-sm-5 col-lg-6 pt-1">
                <div class="btn-toolbar" role="toolbar">
                    <?php if (!$isNormalEditor) { ?>
                        <div class="btn-group js-ace-toolbar">
                            <button data-cmd="none" data-option="fullscreen" class="btn btn-sm btn-outline-secondary" id="js-ace-fullscreen" title="Fullscreen"><i class="fa fa-expand" title="Fullscreen"></i></button>
                            <button data-cmd="find" class="btn btn-sm btn-outline-secondary" id="js-ace-search" title="Search"><i class="fa fa-search" title="Search"></i></button>
                            <button data-cmd="undo" class="btn btn-sm btn-outline-secondary" id="js-ace-undo" title="Undo"><i class="fa fa-undo" title="Undo"></i></button>
                            <button data-cmd="redo" class="btn btn-sm btn-outline-secondary" id="js-ace-redo" title="Redo"><i class="fa fa-repeat" title="Redo"></i></button>
                            <button data-cmd="none" data-option="wrap" class="btn btn-sm btn-outline-secondary" id="js-ace-wordWrap" title="Word Wrap"><i class="fa fa-text-width" title="Word Wrap"></i></button>
                            <button data-cmd="none" data-option="help" class="btn btn-sm btn-outline-secondary" id="js-ace-goLine" title="Help"><i class="fa fa-question" title="Help"></i></button>
                            <select id="js-ace-mode" data-type="mode" title="Select Document Type" class="btn-outline-secondary border-left-0 d-none d-md-block"><option>-- Select Mode --</option></select>
                            <select id="js-ace-theme" data-type="theme" title="Select Theme" class="btn-outline-secondary border-left-0 d-none d-lg-block"><option>-- Select Theme --</option></select>
                            <select id="js-ace-fontSize" data-type="fontSize" title="Selct Font Size" class="btn-outline-secondary border-left-0 d-none d-lg-block"><option>-- Select Font Size --</option></select>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="edit-file-actions col-xs-12 col-sm-7 col-lg-6 text-right pt-1">
                <a title="返回" class="btn btn-sm btn-outline-primary" href="?p=<?php echo urlencode(trim(FM_PATH)) ?>&amp;view=<?php echo urlencode($file) ?>"><i class="fa fa-reply-all"></i>返 回</a>
                <a title="备份" class="btn btn-sm btn-outline-primary" href="javascript:void(0);" onclick="backup('<?php echo urlencode(trim(FM_PATH)) ?>','<?php echo urlencode($file) ?>')"><i class="fa fa-database"></i>备份副本</a>
                <?php if ($is_text) { ?>
                    <?php if ($isNormalEditor) { ?>
                        <a title="高级编辑" class="btn btn-sm btn-outline-primary" href="?p=<?php echo urlencode(trim(FM_PATH)) ?>&amp;edit=<?php echo urlencode($file) ?>&amp;env=ace"><i class="fa fa-pencil-square-o"></i>高级编辑</a>
                        <button type="button" class="btn btn-sm btn-outline-primary" name="Save" data-url="<?php echo fm_enc($file_url) ?>" onclick="edit_save(this,'nrl')"><i class="fa fa-floppy-o"></i>保 存</button>
                    <?php } else { ?>
                        <a title="普通编辑" class="btn btn-sm btn-outline-primary" href="?p=<?php echo urlencode(trim(FM_PATH)) ?>&amp;edit=<?php echo urlencode($file) ?>"><i class="fa fa-text-height"></i>普通编辑</a>
                        <button type="button" class="btn btn-sm btn-outline-primary" name="保存" data-url="<?php echo fm_enc($file_url) ?>" onclick="edit_save(this,'ace')"><i class="fa fa-floppy-o"></i>保 存</button>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
        <?php
        if ($is_text && $isNormalEditor) {
            echo '<textarea class="mt-2" id="normal-editor" rows="33" cols="120" style="width: 99.5%;">' . htmlspecialchars($content) . '</textarea>';
        } elseif ($is_text) {
            echo '<div id="editor" contenteditable="true">' . htmlspecialchars($content) . '</div>';
        } else {
            fm_set_msg('不支持文件扩展名！', 'error');
        }
        ?>
    </div>
    <?php
    fm_show_footer();
    exit;
}

// chmod (not for Windows)
if (isset($_GET['chmod']) && !FM_READONLY && !FM_IS_WIN) {
    $file = $_GET['chmod'];
    $file = fm_clean_path($file);
    $file = str_replace('/', '', $file);
    if ($file == '' || (!is_file($path . '/' . $file) && !is_dir($path . '/' . $file))) {
        fm_set_msg('找不到文件！', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }

    fm_show_header(); // HEADER
    fm_show_nav_path(FM_PATH); // current path

    $file_url = FM_ROOT_URL . (FM_PATH != '' ? '/' . FM_PATH : '') . '/' . $file;
    $file_path = $path . '/' . $file;

    $mode = fileperms($path . '/' . $file);

    ?>
    <div class="path">
        <div class="card mb-2 <?php echo fm_get_theme(); ?>">
            <h6 class="card-header">
                修改权限
            </h6>
            <div class="card-body">
                <p class="card-text">
                    完整路径:<?php echo $file_path ?><br>
                </p>
                <form action="" method="post">
                    <input type="hidden" name="p" value="<?php echo fm_enc(FM_PATH) ?>">
                    <input type="hidden" name="chmod" value="<?php echo fm_enc($file) ?>">

                    <table class="table compact-table <?php echo fm_get_theme(); ?>">
                        <tr>
                            <td></td>
                            <td><b>所有者</b></td>
                            <td><b>用户组</b></td>
                            <td><b>其他用户</b></td>
                        </tr>
                        <tr>
                            <td style="text-align: right"><b>读取权限</b></td>
                            <td><label><input type="checkbox" name="ur" value="1"<?php echo ($mode & 00400) ? ' checked' : '' ?>></label></td>
                            <td><label><input type="checkbox" name="gr" value="1"<?php echo ($mode & 00040) ? ' checked' : '' ?>></label></td>
                            <td><label><input type="checkbox" name="or" value="1"<?php echo ($mode & 00004) ? ' checked' : '' ?>></label></td>
                        </tr>
                        <tr>
                            <td style="text-align: right"><b>写入权限</b></td>
                            <td><label><input type="checkbox" name="uw" value="1"<?php echo ($mode & 00200) ? ' checked' : '' ?>></label></td>
                            <td><label><input type="checkbox" name="gw" value="1"<?php echo ($mode & 00020) ? ' checked' : '' ?>></label></td>
                            <td><label><input type="checkbox" name="ow" value="1"<?php echo ($mode & 00002) ? ' checked' : '' ?>></label></td>
                        </tr>
                        <tr>
                            <td style="text-align: right"><b>执行权限</b></td>
                            <td><label><input type="checkbox" name="ux" value="1"<?php echo ($mode & 00100) ? ' checked' : '' ?>></label></td>
                            <td><label><input type="checkbox" name="gx" value="1"<?php echo ($mode & 00010) ? ' checked' : '' ?>></label></td>
                            <td><label><input type="checkbox" name="ox" value="1"<?php echo ($mode & 00001) ? ' checked' : '' ?>></label></td>
                        </tr>
                    </table>

                    <p>
                        <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i>立即修改</button> &nbsp;
                        <b><a href="?p=<?php echo urlencode(FM_PATH) ?>" class="btn btn-outline-primary"><i class="fa fa-times-circle"></i>取消修改</a></b>
                    </p>
                </form>
            </div>
        </div>
    </div>
    <?php
    fm_show_footer();
    exit;
}

//--- FILEMANAGER MAIN
fm_show_header(); // HEADER
fm_show_nav_path(FM_PATH); // current path

// messages
fm_show_message();

$num_files = count($files);
$num_folders = count($folders);
$all_files_size = 0;
$tableTheme = (FM_THEME == "dark") ? "text-white bg-dark table-dark" : "bg-white";
?>
<form action="" method="post" class="pt-3">
    <input type="hidden" name="p" value="<?php echo fm_enc(FM_PATH) ?>">
    <input type="hidden" name="group" value="1">
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-sm <?php echo $tableTheme; ?>" id="main-table">
            <thead class="thead-white">
            <tr>
                <?php if (!FM_READONLY): ?>
                    <th style="width:3%" class="custom-checkbox-header">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="js-select-all-items" onclick="checkbox_toggle()">
                            <label class="custom-control-label" for="js-select-all-items"></label>
                        </div>
                    </th><?php endif; ?>
                <th>文件/夹名称</th>
                <th>大 小</th>
                <th>修改时间</th>
                <?php if (!FM_IS_WIN && !$hide_Cols): ?>
                    <th>读写权限</th>
                    <th>所有者</th><?php endif; ?>
                <th>操作选项</th>
            </tr>
            </thead>
            <?php
            // link to parent folder
            if ($parent !== false) {
                ?>
                <tr><?php if (!FM_READONLY): ?>
                    <td class="nosort"></td><?php endif; ?>
                    <td class="border-0"><a href="?p=<?php echo urlencode($parent) ?>"><i class="fa fa-chevron-circle-left go-back"></i>返回上一层</a></td>
                    <td class="border-0"></td>
                    <td class="border-0"></td>
                    <td class="border-0"></td>
                    <?php if (!FM_IS_WIN && !$hide_Cols) { ?>
                        <td class="border-0"></td>
                        <td class="border-0"></td>
                    <?php } ?>
                </tr>
                <?php
            }
            $ii = 3399;
            foreach ($folders as $f) {
                $is_link = is_link($path . '/' . $f);
                $img = $is_link ? 'icon-link_folder' : 'fa fa-folder-o';
                $modif_raw = filemtime($path . '/' . $f);
                $modif = date(FM_DATETIME_FORMAT, $modif_raw);
                if ($calc_folder) {
                    $filesize_raw = fm_get_directorysize($path . '/' . $f);
                    $filesize = fm_get_filesize($filesize_raw);
                }
                else {
                    $filesize_raw = "";
                    $filesize = '文件夹';
                }
                $perms = substr(decoct(fileperms($path . '/' . $f)), -4);
                if (function_exists('posix_getpwuid') && function_exists('posix_getgrgid')) {
                    $owner = posix_getpwuid(fileowner($path . '/' . $f));
                    $group = posix_getgrgid(filegroup($path . '/' . $f));
                } else {
                    $owner = array('name' => '?');
                    $group = array('name' => '?');
                }
                ?>
                <tr>
                    <?php if (!FM_READONLY): ?>
                        <td class="custom-checkbox-td">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="<?php echo $ii ?>" name="file[]" value="<?php echo fm_enc($f) ?>">
                            <label class="custom-control-label" for="<?php echo $ii ?>"></label>
                        </div>
                        </td><?php endif; ?>
                    <td>
                        <div class="filename"><a href="?p=<?php echo urlencode(trim(FM_PATH . '/' . $f, '/')) ?>"><i class="<?php echo $img ?>"></i> <?php echo fm_convert_win(fm_enc($f)) ?>
                            </a><?php echo($is_link ? ' &rarr; <i>' . readlink($path . '/' . $f) . '</i>' : '') ?></div>
                    </td>
                    <td data-sort="a-<?php echo str_pad($filesize_raw, 18, "0", STR_PAD_LEFT);?>">
                        <?php echo $filesize; ?>
                    </td>
                    <td data-sort="a-<?php echo $modif_raw;?>"><?php echo $modif ?></td>                                                                                                                           
                    <?php if (!FM_IS_WIN && !$hide_Cols): ?>
                        <td><?php if (!FM_READONLY): ?><a title="修改权限" href="?p=<?php echo urlencode(FM_PATH) ?>&amp;chmod=<?php echo urlencode($f) ?>"><?php echo $perms ?></a><?php else: ?><?php echo $perms ?><?php endif; ?>
                        </td>
                        <td><?php echo $owner['name'] . ':' . $group['name'] ?></td>
                    <?php endif; ?>
                    <td class="inline-actions"><?php if (!FM_READONLY): ?>
                            <a title="删除" href="?p=<?php echo urlencode(FM_PATH) ?>&amp;del=<?php echo urlencode($f) ?>" onclick="return confirm('确定删除该文件夹？\n \n ( <?php echo urlencode($f) ?> )');"> <i class="fa fa-trash-o" aria-hidden="true"></i></a>
                            <a title="重命名" href="#" onclick="rename('<?php echo fm_enc(FM_PATH) ?>', '<?php echo fm_enc(addslashes($f)) ?>');return false;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            <a title="复制到..." href="?p=&amp;copy=<?php echo urlencode(trim(FM_PATH . '/' . $f, '/')) ?>"><i class="fa fa-files-o" aria-hidden="true"></i></a>
                        <?php endif; ?>
                        <a title="直接地址" href="<?php echo fm_enc(FM_ROOT_URL . (FM_PATH != '' ? '/' . FM_PATH : '') . '/' . $f . '/') ?>" target="_blank"><i class="fa fa-link" aria-hidden="true"></i></a>
                    </td>
                </tr>
                <?php
                flush();
                $ii++;
            }
            $ik = 6070;
            foreach ($files as $f) {
                $is_link = is_link($path . '/' . $f);
                $img = $is_link ? 'fa fa-file-text-o' : fm_get_file_icon_class($path . '/' . $f);
                $modif_raw = filemtime($path . '/' . $f);
                $modif = date(FM_DATETIME_FORMAT, $modif_raw);
                $filesize_raw = fm_get_size($path . '/' . $f);
                $filesize = fm_get_filesize($filesize_raw);
                $filelink = '?p=' . urlencode(FM_PATH) . '&amp;view=' . urlencode($f);
                $all_files_size += $filesize_raw;
                $perms = substr(decoct(fileperms($path . '/' . $f)), -4);
                if (function_exists('posix_getpwuid') && function_exists('posix_getgrgid')) {
                    $owner = posix_getpwuid(fileowner($path . '/' . $f));
                    $group = posix_getgrgid(filegroup($path . '/' . $f));
                } else {
                    $owner = array('name' => '?');
                    $group = array('name' => '?');
                }
                ?>
                <tr>
                    <?php if (!FM_READONLY): ?>
                        <td class="custom-checkbox-td">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="<?php echo $ik ?>" name="file[]" value="<?php echo fm_enc($f) ?>">
                            <label class="custom-control-label" for="<?php echo $ik ?>"></label>
                        </div>
                        </td><?php endif; ?>
                    <td>
                        <div class="filename"> 
                        <?php
                           if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'ico', 'svg'))): ?>
                                <?php $imagePreview = fm_enc(FM_ROOT_URL . (FM_PATH != '' ? '/' . FM_PATH : '') . '/' . $f); ?>
                                <a href="<?php echo $filelink ?>" data-preview-image="<?php echo $imagePreview ?>" title="<?php echo $f ?>">
                           <?php else: ?>
                                <a href="<?php echo $filelink ?>" title="<?php echo $f ?>">
                            <?php endif; ?>
                                    <i class="<?php echo $img ?>"></i> <?php echo fm_convert_win($f) ?>
                                </a>
                                <?php echo($is_link ? ' &rarr; <i>' . readlink($path . '/' . $f) . '</i>' : '') ?>
                        </div>
                    </td>
                    <td data-sort=b-"<?php echo str_pad($filesize_raw, 18, "0", STR_PAD_LEFT); ?>"><span title="<?php printf('%s bytes', $filesize_raw) ?>">
                        <?php echo $filesize; ?>
                        </span></td>
                    <td data-sort="b-<?php echo $modif_raw;?>"><?php echo $modif ?></td>
                    <?php if (!FM_IS_WIN && !$hide_Cols): ?>
                        <td><?php if (!FM_READONLY): ?><a title="修改权限" href="?p=<?php echo urlencode(FM_PATH) ?>&amp;chmod=<?php echo urlencode($f) ?>"><?php echo $perms ?></a><?php else: ?><?php echo $perms ?><?php endif; ?>
                        </td>
                        <td><?php echo fm_enc($owner['name'] . ':' . $group['name']) ?></td>
                    <?php endif; ?>
                    <td class="inline-actions">
                        <a title="预览" href="<?php echo $filelink.'&quickView=1'; ?>" data-toggle="lightbox" data-gallery="tiny-gallery" data-title="<?php echo fm_convert_win($f) ?>" data-max-width="100%" data-width="100%"><i class="fa fa-eye"></i></a>
                        <?php if (!FM_READONLY): ?>
                            <a title="删除" href="?p=<?php echo urlencode(FM_PATH) ?>&amp;del=<?php echo urlencode($f) ?>" onclick="return confirm('确定删除该文件？\n \n ( <?php echo urlencode($f) ?> )');"> <i class="fa fa-trash-o"></i></a>
                            <a title="重命名" href="#" onclick="rename('<?php echo fm_enc(FM_PATH) ?>', '<?php echo fm_enc(addslashes($f)) ?>');return false;"><i class="fa fa-pencil-square-o"></i></a>
                            <a title="复制到..."
                               href="?p=<?php echo urlencode(FM_PATH) ?>&amp;copy=<?php echo urlencode(trim(FM_PATH . '/' . $f, '/')) ?>"><i class="fa fa-files-o"></i></a>
                        <?php endif; ?>
                        <a title="直接地址" href="<?php echo fm_enc(FM_ROOT_URL . (FM_PATH != '' ? '/' . FM_PATH : '') . '/' . $f) ?>" target="_blank"><i class="fa fa-link"></i></a>
                        <a title="下载" href="?p=<?php echo urlencode(FM_PATH) ?>&amp;dl=<?php echo urlencode($f) ?>"><i class="fa fa-download"></i></a>
                    </td>
                </tr>
                <?php
                flush();
                $ik++;
            }

            if (empty($folders) && empty($files)) {
                ?>
                <tfoot>
                    <tr><?php if (!FM_READONLY): ?>
                            <td></td><?php endif; ?>
                        <td colspan="<?php echo (!FM_IS_WIN && !$hide_Cols) ? '6' : '4' ?>"><em>文件夹为空</em></td>
                    </tr>
                </tfoot>
                <?php
            } else {
                ?>
                <tfoot>
                    <tr><?php if (!FM_READONLY): ?>
                            <td class="gray"></td><?php endif; ?>
                        <td class="gray" colspan="<?php echo (!FM_IS_WIN && !$hide_Cols) ? '6' : '4' ?>">
                            <?php echo '所有文件大小: <span class="badge badge-light">'.fm_get_filesize($all_files_size).'</span>' ?>
                            <?php echo '文件: <span class="badge badge-light">'.$num_files.'个</span>' ?>
                            <?php echo '文件夹: <span class="badge badge-light">'.$num_folders.'个</span>' ?>
                            <?php echo '使用内存: <span class="badge badge-light">'.fm_get_filesize(@memory_get_usage(true)).'</span>' ?>
                            <?php echo '可用空间: <span class="badge badge-light">'.fm_get_filesize(@disk_free_space($path)) .'</span>磁盘大小:<span class="badge badge-light">'.fm_get_filesize(@disk_total_space($path)).'</span>'; ?>
                        </td>
                    </tr>
                </tfoot>
                <?php
            }
            ?>
        </table>
    </div>

    <div class="row">
        <?php if (!FM_READONLY): ?>
        <div class="col-xs-12 col-sm-9">
            <ul class="list-inline footer-action">
                <li class="list-inline-item"> <a href="#/select-all" class="btn btn-small btn-outline-primary btn-2" onclick="select_all();return false;"><i class="fa fa-check-square"></i>全 选</a></li>
                <li class="list-inline-item"><a href="#/unselect-all" class="btn btn-small btn-outline-primary btn-2" onclick="unselect_all();return false;"><i class="fa fa-window-close"></i>取消全选</a></li>
                <li class="list-inline-item"><a href="#/invert-all" class="btn btn-small btn-outline-primary btn-2" onclick="invert_all();return false;"><i class="fa fa-th-list"></i>反向选择</a></li>
                <li class="list-inline-item"><input type="submit" class="hidden" name="delete" id="a-delete" value="Delete" onclick="return confirm('确认删除选定的文件和文件夹?')">
                    <a href="javascript:document.getElementById('a-delete').click();" class="btn btn-small btn-outline-primary btn-2"><i class="fa fa-trash"></i>删 除</a></li>
                <li class="list-inline-item"><input type="submit" class="hidden" name="zip" id="a-zip" value="zip" onclick="return confirm('确认ZIP压缩选定的文件和文件夹?')">
                    <a href="javascript:document.getElementById('a-zip').click();" class="btn btn-small btn-outline-primary btn-2"><i class="fa fa-file-archive-o"></i>ZIP压缩</a></li>
                <li class="list-inline-item"><input type="submit" class="hidden" name="tar" id="a-tar" value="tar" onclick="return confirm('确认TAR压缩选定的文件和文件夹?')">
                    <a href="javascript:document.getElementById('a-tar').click();" class="btn btn-small btn-outline-primary btn-2"><i class="fa fa-file-archive-o"></i>TAR压缩</a></li>
                <li class="list-inline-item"><input type="submit" class="hidden" name="copy" id="a-copy" value="Copy">
                    <a href="javascript:document.getElementById('a-copy').click();" class="btn btn-small btn-outline-primary btn-2"><i class="fa fa-files-o"></i>移动/复制</a></li>
            </ul>
        </div>
        <div class="col-3 d-none d-sm-block">TfM文件管理器(易优自用版)</div>
        <?php else: ?>
            <div class="col-12">TfM文件管理器(易优自用版)</div>
        <?php endif; ?>
    </div>

</form>

<?php
fm_show_footer();

//--- END

// Functions

/**
 * Check if the filename is allowed.
 * @param string $filename
 * @return bool
 */
function fm_is_file_allowed($filename)
{
    // By default, no file is allowed
    $allowed = false;

    if (FM_EXTENSION) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, explode(',', strtolower(FM_EXTENSION)))) {
            $allowed = true;
        }
    }

    return $allowed;
}

/**
 * Delete  file or folder (recursively)
 * @param string $path
 * @return bool
 */
function fm_rdelete($path)
{
    if (is_link($path)) {
        return unlink($path);
    } elseif (is_dir($path)) {
        $objects = scandir($path);
        $ok = true;
        if (is_array($objects)) {
            foreach ($objects as $file) {
                if ($file != '.' && $file != '..') {
                    if (!fm_rdelete($path . '/' . $file)) {
                        $ok = false;
                    }
                }
            }
        }
        return ($ok) ? rmdir($path) : false;
    } elseif (is_file($path)) {
        return unlink($path);
    }
    return false;
}

/**
 * Recursive chmod
 * @param string $path
 * @param int $filemode
 * @param int $dirmode
 * @return bool
 * @todo Will use in mass chmod
 */
function fm_rchmod($path, $filemode, $dirmode)
{
    if (is_dir($path)) {
        if (!chmod($path, $dirmode)) {
            return false;
        }
        $objects = scandir($path);
        if (is_array($objects)) {
            foreach ($objects as $file) {
                if ($file != '.' && $file != '..') {
                    if (!fm_rchmod($path . '/' . $file, $filemode, $dirmode)) {
                        return false;
                    }
                }
            }
        }
        return true;
    } elseif (is_link($path)) {
        return true;
    } elseif (is_file($path)) {
        return chmod($path, $filemode);
    }
    return false;
}

/**
 * Check the file extension which is allowed or not
 * @param string $filename
 * @return bool
 */
function fm_is_valid_ext($filename)
{
    $allowed = (FM_FILE_EXTENSION) ? explode(',', FM_FILE_EXTENSION) : false;

    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $isFileAllowed = ($allowed) ? in_array($ext, $allowed) : true;

    return ($isFileAllowed) ? true : false;
}

/**
 * Safely rename
 * @param string $old
 * @param string $new
 * @return bool|null
 */
function fm_rename($old, $new)
{
    $isFileAllowed = fm_is_valid_ext($new);

    if(!$isFileAllowed) return false;

    return (!file_exists($new) && file_exists($old)) ? rename($old, $new) : null;
}

/**
 * Copy file or folder (recursively).
 * @param string $path
 * @param string $dest
 * @param bool $upd Update files
 * @param bool $force Create folder with same names instead file
 * @return bool
 */
function fm_rcopy($path, $dest, $upd = true, $force = true)
{
    if (is_dir($path)) {
        if (!fm_mkdir($dest, $force)) {
            return false;
        }
        $objects = scandir($path);
        $ok = true;
        if (is_array($objects)) {
            foreach ($objects as $file) {
                if ($file != '.' && $file != '..') {
                    if (!fm_rcopy($path . '/' . $file, $dest . '/' . $file)) {
                        $ok = false;
                    }
                }
            }
        }
        return $ok;
    } elseif (is_file($path)) {
        return fm_copy($path, $dest, $upd);
    }
    return false;
}

/**
 * Safely create folder
 * @param string $dir
 * @param bool $force
 * @return bool
 */
function fm_mkdir($dir, $force)
{
    if (file_exists($dir)) {
        if (is_dir($dir)) {
            return $dir;
        } elseif (!$force) {
            return false;
        }
        unlink($dir);
    }
    return mkdir($dir, 0777, true);
}

/**
 * Safely copy file
 * @param string $f1
 * @param string $f2
 * @param bool $upd Indicates if file should be updated with new content
 * @return bool
 */
function fm_copy($f1, $f2, $upd)
{
    $time1 = filemtime($f1);
    if (file_exists($f2)) {
        $time2 = filemtime($f2);
        if ($time2 >= $time1 && $upd) {
            return false;
        }
    }
    $ok = copy($f1, $f2);
    if ($ok) {
        touch($f2, $time1);
    }
    return $ok;
}

/**
 * Get mime type
 * @param string $file_path
 * @return mixed|string
 */
function fm_get_mime_type($file_path)
{
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file_path);
        finfo_close($finfo);
        return $mime;
    } elseif (function_exists('mime_content_type')) {
        return mime_content_type($file_path);
    } elseif (!stristr(ini_get('disable_functions'), 'shell_exec')) {
        $file = escapeshellarg($file_path);
        $mime = shell_exec('file -bi ' . $file);
        return $mime;
    } else {
        return '--';
    }
}

/**
 * HTTP Redirect
 * @param string $url
 * @param int $code
 */
function fm_redirect($url, $code = 302)
{
    header('Location: ' . $url, true, $code);
    exit;
}

/**
 * Path traversal prevention and clean the url
 * It replaces (consecutive) occurrences of / and \\ with whatever is in DIRECTORY_SEPARATOR, and processes /. and /.. fine.
 * @param $path
 * @return string
 */
function get_absolute_path($path) {
    $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
    $absolutes = array();
    foreach ($parts as $part) {
        if ('.' == $part) continue;
        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = $part;
        }
    }
    return implode(DIRECTORY_SEPARATOR, $absolutes);
}

/**
 * Clean path
 * @param string $path
 * @return string
 */
function fm_clean_path($path, $trim = true)
{
    $path = $trim ? trim($path) : $path;
    $path = trim($path, '\\/');
    $path = str_replace(array('../', '..\\'), '', $path);
    $path =  get_absolute_path($path);
    if ($path == '..') {
        $path = '';
    }
    return str_replace('\\', '/', $path);
}

/**
 * Get parent path
 * @param string $path
 * @return bool|string
 */
function fm_get_parent_path($path)
{
    $path = fm_clean_path($path);
    if ($path != '') {
        $array = explode('/', $path);
        if (count($array) > 1) {
            $array = array_slice($array, 0, -1);
            return implode('/', $array);
        }
        return '';
    }
    return false;
}

/**
 * Check file is in exclude list
 * @param string $file
 * @return bool
 */
function fm_is_exclude_items($file) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if(!in_array($file, FM_EXCLUDE_ITEMS) && !in_array("*.$ext", FM_EXCLUDE_ITEMS)) {
        return true;
    }
    return false;
}

/**
 * get language translations from json file
 * @param int $tr
 * @return array
 */


/**
 * @param $file
 * Recover all file sizes larger than > 2GB.
 * Works on php 32bits and 64bits and supports linux
 * @return int|string
 */
function fm_get_size($file)
{
    static $iswin;
    static $isdarwin;
    if (!isset($iswin)) {
        $iswin = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN');
    } 
    if (!isset($isdarwin)) {
        $isdarwin = (strtoupper(substr(PHP_OS, 0)) == "DARWIN");
    }

    static $exec_works;
    if (!isset($exec_works)) {
        $exec_works = (function_exists('exec') && !ini_get('safe_mode') && @exec('echo EXEC') == 'EXEC');
    }

    // try a shell command
    if ($exec_works) {
        $arg = escapeshellarg($file);
        $cmd = ($iswin) ? "for %F in (\"$file\") do @echo %~zF" : ($isdarwin ? "stat -f%z $arg" : "stat -c%s $arg");
        @exec($cmd, $output);
        if (is_array($output) && ctype_digit($size = trim(implode("\n", $output)))) {
            return $size;
        }
    }

    // try the Windows COM interface
    if ($iswin && class_exists("COM")) {
        try {
            $fsobj = new COM('Scripting.FileSystemObject');
            $f = $fsobj->GetFile( realpath($file) );
            $size = $f->Size;
        } catch (Exception $e) {
            $size = null;
        }
        if (ctype_digit($size)) {
            return $size;
        }
    }

    // if all else fails
    return filesize($file);
}

/**
 * Get nice filesize
 * @param int $size
 * @return string
 */
function fm_get_filesize($size)
{
    $size = (float) $size;
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return sprintf('%s %s', round($size / pow(1024, $power), 2), $units[$power]);
}

/**
 * Get director total size
 * @param string $directory
 * @return int
 */
function fm_get_directorysize($directory) {
    global $calc_folder;
    if ($calc_folder==true) { //  Slower output
      $size = 0;  $count= 0;  $dirCount= 0;
    foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file)
    if ($file->isFile())
        {   $size+=$file->getSize();
            $count++;
        }
    else if ($file->isDir()) { $dirCount++; }
    // return [$size, $count, $dirCount];
    return $size;
    }
    else return '文件夹'; //  Quick output
}

/**
 * Get info about zip archive
 * @param string $path
 * @return array|bool
 */
function fm_get_zif_info($path, $ext) {
    if ($ext == 'zip' && function_exists('zip_open')) {
        $arch = zip_open($path);
        if ($arch) {
            $filenames = array();
            while ($zip_entry = zip_read($arch)) {
                $zip_name = zip_entry_name($zip_entry);
                $zip_folder = substr($zip_name, -1) == '/';
                $filenames[] = array(
                    'name' => $zip_name,
                    'filesize' => zip_entry_filesize($zip_entry),
                    'compressed_size' => zip_entry_compressedsize($zip_entry),
                    'folder' => $zip_folder
                    //'compression_method' => zip_entry_compressionmethod($zip_entry),
                );
            }
            zip_close($arch);
            return $filenames;
        }
    } elseif($ext == 'tar' && class_exists('PharData')) {
        $archive = new PharData($path);
        $filenames = array();
        foreach(new RecursiveIteratorIterator($archive) as $file) {
            $parent_info = $file->getPathInfo();
            $zip_name = str_replace("phar://".$path, '', $file->getPathName());
            $zip_name = substr($zip_name, ($pos = strpos($zip_name, '/')) !== false ? $pos + 1 : 0);
            $zip_folder = $parent_info->getFileName();
            $zip_info = new SplFileInfo($file);
            $filenames[] = array(
                'name' => $zip_name,
                'filesize' => $zip_info->getSize(),
                'compressed_size' => $file->getCompressedSize(),
                'folder' => $zip_folder
            );
        }
        return $filenames;
    }
    return false;
}

/**
 * Encode html entities
 * @param string $text
 * @return string
 */
function fm_enc($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Prevent XSS attacks
 * @param string $text
 * @return string
 */
function fm_isvalid_filename($text) {
    return (strpbrk($text, '/?%*:|"<>') === FALSE) ? true : false;
}

/**
 * Save message in session
 * @param string $msg
 * @param string $status
 */
function fm_set_msg($msg, $status = 'ok')
{
    $_SESSION[FM_SESSION_ID]['message'] = $msg;
    $_SESSION[FM_SESSION_ID]['status'] = $status;
}

/**
 * Check if string is in UTF-8
 * @param string $string
 * @return int
 */
function fm_is_utf8($string)
{
    return preg_match('//u', $string);
}

/**
 * Convert file name to UTF-8 in Windows
 * @param string $filename
 * @return string
 */
function fm_convert_win($filename)
{
    if (FM_IS_WIN && function_exists('iconv')) {
        $filename = iconv(FM_ICONV_INPUT_ENC, 'UTF-8//IGNORE', $filename);
    }
    return $filename;
}

/**
 * @param $obj
 * @return array
 */
function fm_object_to_array($obj)
{
    if (!is_object($obj) && !is_array($obj)) {
        return $obj;
    }
    if (is_object($obj)) {
        $obj = get_object_vars($obj);
    }
    return array_map('fm_object_to_array', $obj);
}

/**
 * Get CSS classname for file
 * @param string $path
 * @return string
 */
function fm_get_file_icon_class($path)
{
    // get extension
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    switch ($ext) {
        case 'ico':
        case 'gif':
        case 'jpg':
        case 'jpeg':
        case 'jpc':
        case 'jp2':
        case 'jpx':
        case 'xbm':
        case 'wbmp':
        case 'png':
        case 'bmp':
        case 'tif':
        case 'tiff':
        case 'svg':
            $img = 'fa fa-picture-o';
            break;
        case 'passwd':
        case 'ftpquota':
        case 'sql':
        case 'js':
        case 'json':
        case 'sh':
        case 'config':
        case 'twig':
        case 'tpl':
        case 'md':
        case 'gitignore':
        case 'c':
        case 'cpp':
        case 'cs':
        case 'py':
        case 'map':
        case 'lock':
        case 'dtd':
            $img = 'fa fa-file-code-o';
            break;
        case 'txt':
        case 'ini':
        case 'conf':
        case 'log':
        case 'htaccess':
            $img = 'fa fa-file-text-o';
            break;
        case 'css':
        case 'less':
        case 'sass':
        case 'scss':
            $img = 'fa fa-css3';
            break;
        case 'zip':
        case 'rar':
        case 'gz':
        case 'tar':
        case '7z':
            $img = 'fa fa-file-archive-o';
            break;
        case 'php':
        case 'php4':
        case 'php5':
        case 'phps':
        case 'phtml':
            $img = 'fa fa-code';
            break;
        case 'htm':
        case 'html':
        case 'shtml':
        case 'xhtml':
            $img = 'fa fa-html5';
            break;
        case 'xml':
        case 'xsl':
            $img = 'fa fa-file-excel-o';
            break;
        case 'wav':
        case 'mp3':
        case 'mp2':
        case 'm4a':
        case 'aac':
        case 'ogg':
        case 'oga':
        case 'wma':
        case 'mka':
        case 'flac':
        case 'ac3':
        case 'tds':
            $img = 'fa fa-music';
            break;
        case 'm3u':
        case 'm3u8':
        case 'pls':
        case 'cue':
            $img = 'fa fa-headphones';
            break;
        case 'avi':
        case 'mpg':
        case 'mpeg':
        case 'mp4':
        case 'm4v':
        case 'flv':
        case 'f4v':
        case 'ogm':
        case 'ogv':
        case 'mov':
        case 'mkv':
        case '3gp':
        case 'asf':
        case 'wmv':
            $img = 'fa fa-file-video-o';
            break;
        case 'eml':
        case 'msg':
            $img = 'fa fa-envelope-o';
            break;
        case 'xls':
        case 'xlsx':
        case 'ods':
            $img = 'fa fa-file-excel-o';
            break;
        case 'csv':
            $img = 'fa fa-file-text-o';
            break;
        case 'bak':
            $img = 'fa fa-clipboard';
            break;
        case 'doc':
        case 'docx':
        case 'odt':
            $img = 'fa fa-file-word-o';
            break;
        case 'ppt':
        case 'pptx':
            $img = 'fa fa-file-powerpoint-o';
            break;
        case 'ttf':
        case 'ttc':
        case 'otf':
        case 'woff':
        case 'woff2':
        case 'eot':
        case 'fon':
            $img = 'fa fa-font';
            break;
        case 'pdf':
            $img = 'fa fa-file-pdf-o';
            break;
        case 'psd':
        case 'ai':
        case 'eps':
        case 'fla':
        case 'swf':
            $img = 'fa fa-file-image-o';
            break;
        case 'exe':
        case 'msi':
            $img = 'fa fa-file-o';
            break;
        case 'bat':
            $img = 'fa fa-terminal';
            break;
        default:
            $img = 'fa fa-info-circle';
    }

    return $img;
}

/**
 * Get image files extensions
 * @return array
 */
function fm_get_image_exts()
{
    return array('ico', 'gif', 'jpg', 'jpeg', 'jpc', 'jp2', 'jpx', 'xbm', 'wbmp', 'png', 'bmp', 'tif', 'tiff', 'psd', 'svg');
}

/**
 * Get video files extensions
 * @return array
 */
function fm_get_video_exts()
{
    return array('avi', 'webm', 'wmv', 'mp4', 'm4v', 'ogm', 'ogv', 'mov', 'mkv');
}

/**
 * Get audio files extensions
 * @return array
 */
function fm_get_audio_exts()
{
    return array('wav', 'mp3', 'ogg', 'm4a');
}

/**
 * Get text file extensions
 * @return array
 */
function fm_get_text_exts()
{
    return array(
        'txt', 'css', 'ini', 'conf', 'log', 'htaccess', 'passwd', 'ftpquota', 'sql', 'js', 'json', 'sh', 'config',
        'php', 'php4', 'php5', 'phps', 'phtml', 'htm', 'html', 'shtml', 'xhtml', 'xml', 'xsl', 'm3u', 'm3u8', 'pls', 'cue',
        'eml', 'msg', 'csv', 'bat', 'twig', 'tpl', 'md', 'gitignore', 'less', 'sass', 'scss', 'c', 'cpp', 'cs', 'py',
        'map', 'lock', 'dtd', 'svg', 'scss', 'asp', 'aspx', 'asx', 'asmx', 'ashx', 'jsx', 'jsp', 'jspx', 'cfm', 'cgi'
    );
}

/**
 * Get mime types of text files
 * @return array
 */
function fm_get_text_mimes()
{
    return array(
        'application/xml',
        'application/javascript',
        'application/x-javascript',
        'image/svg+xml',
        'message/rfc822',
    );
}

/**
 * Get file names of text files w/o extensions
 * @return array
 */
function fm_get_text_names()
{
    return array(
        'license',
        'readme',
        'authors',
        'contributors',
        'changelog',
    );
}

/**
 * Get online docs viewer supported files extensions
 * @return array
 */
function fm_get_onlineViewer_exts()
{
    return array('doc', 'docx', 'xls', 'xlsx', 'pdf', 'ppt', 'pptx', 'ai', 'psd', 'dxf', 'xps', 'rar', 'odt', 'ods');
}

function fm_get_file_mimes($extension)
{
    $fileTypes['swf'] = 'application/x-shockwave-flash';
    $fileTypes['pdf'] = 'application/pdf';
    $fileTypes['exe'] = 'application/octet-stream';
    $fileTypes['zip'] = 'application/zip';
    $fileTypes['doc'] = 'application/msword';
    $fileTypes['xls'] = 'application/vnd.ms-excel';
    $fileTypes['ppt'] = 'application/vnd.ms-powerpoint';
    $fileTypes['gif'] = 'image/gif';
    $fileTypes['png'] = 'image/png';
    $fileTypes['jpeg'] = 'image/jpg';
    $fileTypes['jpg'] = 'image/jpg';
    $fileTypes['rar'] = 'application/rar';

    $fileTypes['ra'] = 'audio/x-pn-realaudio';
    $fileTypes['ram'] = 'audio/x-pn-realaudio';
    $fileTypes['ogg'] = 'audio/x-pn-realaudio';

    $fileTypes['wav'] = 'video/x-msvideo';
    $fileTypes['wmv'] = 'video/x-msvideo';
    $fileTypes['avi'] = 'video/x-msvideo';
    $fileTypes['asf'] = 'video/x-msvideo';
    $fileTypes['divx'] = 'video/x-msvideo';


    $fileTypes['mp3'] = 'audio/mpeg';
    $fileTypes['mp4'] = 'audio/mpeg';
    $fileTypes['mpeg'] = 'video/mpeg';
    $fileTypes['mpg'] = 'video/mpeg';
    $fileTypes['mpe'] = 'video/mpeg';
    $fileTypes['mov'] = 'video/quicktime';
    $fileTypes['swf'] = 'video/quicktime';
    $fileTypes['3gp'] = 'video/quicktime';
    $fileTypes['m4a'] = 'video/quicktime';
    $fileTypes['aac'] = 'video/quicktime';
    $fileTypes['m3u'] = 'video/quicktime';
$fileTypes['m3u8'] = 'application/x-mpegURL';

    $fileTypes['php'] = ['application/x-php'];
    $fileTypes['html'] = ['text/html'];
    $fileTypes['txt'] = ['text/plain'];
$fileTypes['js'] = 'application/x-java';
$fileTypes['css'] = 'text/css';
    return $fileTypes[$extension];
}

/**
 * This function scans the files and folder recursively, and return matching files
 * @param string $dir
 * @param string $filter
 * @return json
 */
 function scan($dir, $filter = '') {
    $path = FM_ROOT_PATH.'/'.$dir;
     if($dir) {
         $ite = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
         $rii = new RegexIterator($ite, "/(" . $filter . ")/i");

         $files = array();
         foreach ($rii as $file) {
             if (!$file->isDir()) {
                 $fileName = $file->getFilename();
                 $location = str_replace(FM_ROOT_PATH, '', $file->getPath());
                 $files[] = array(
                     "name" => $fileName,
                     "type" => "file",
                     "path" => $location,
                 );
             }
         }
         return $files;
     }
}

/*
Parameters: downloadFile(File Location, File Name,
max speed, is streaming
If streaming - videos will show as videos, images as images
instead of download prompt
https://stackoverflow.com/a/13821992/1164642
*/

function fm_download_file($fileLocation, $fileName, $chunkSize  = 1024)
{
    if (connection_status() != 0)
        return (false);
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);

    $contentType = fm_get_file_mimes($extension);
    header("Cache-Control: public");
    header("Content-Transfer-Encoding: binary\n");
    header('Content-Type: $contentType');

    $contentDisposition = 'attachment';


    if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
        $fileName = preg_replace('/\./', '%2e', $fileName, substr_count($fileName, '.') - 1);
        header("Content-Disposition: $contentDisposition;filename=\"$fileName\"");
    } else {
        header("Content-Disposition: $contentDisposition;filename=\"$fileName\"");
    }

    header("Accept-Ranges: bytes");
    $range = 0;
    $size = filesize($fileLocation);

    if (isset($_SERVER['HTTP_RANGE'])) {
        list($a, $range) = explode("=", $_SERVER['HTTP_RANGE']);
        str_replace($range, "-", $range);
        $size2 = $size - 1;
        $new_length = $size - $range;
        header("HTTP/1.1 206 Partial Content");
        header("Content-Length: $new_length");
        header("Content-Range: bytes $range$size2/$size");
    } else {
        $size2 = $size - 1;
        header("Content-Range: bytes 0-$size2/$size");
        header("Content-Length: " . $size);
    }

    if ($size == 0) {
        die('零字节文件！正在中止下载！');
    }
    @ini_set('magic_quotes_runtime', 0);
    $fp = fopen("$fileLocation", "rb");

    fseek($fp, $range);

    while (!feof($fp) and (connection_status() == 0)) {
        set_time_limit(0);
        print(@fread($fp, 1024*$chunkSize));
        flush();
        ob_flush();
        sleep(1);
    }
    fclose($fp);

    return ((connection_status() == 0) and !connection_aborted());
}

function fm_get_theme() {
    $result = '';
    if(FM_THEME == "dark") {
        $result = "text-white bg-dark";
    }
    return $result;
}

/**
 * Class to work with zip files (using ZipArchive)
 */
class FM_Zipper
{
    private $zip;

    public function __construct()
    {
        $this->zip = new ZipArchive();
    }

    /**
     * Create archive with name $filename and files $files (RELATIVE PATHS!)
     * @param string $filename
     * @param array|string $files
     * @return bool
     */
    public function create($filename, $files)
    {
        $res = $this->zip->open($filename, ZipArchive::CREATE);
        if ($res !== true) {
            return false;
        }
        if (is_array($files)) {
            foreach ($files as $f) {
                if (!$this->addFileOrDir($f)) {
                    $this->zip->close();
                    return false;
                }
            }
            $this->zip->close();
            return true;
        } else {
            if ($this->addFileOrDir($files)) {
                $this->zip->close();
                return true;
            }
            return false;
        }
    }

    /**
     * Extract archive $filename to folder $path (RELATIVE OR ABSOLUTE PATHS)
     * @param string $filename
     * @param string $path
     * @return bool
     */
    public function unzip($filename, $path)
    {
        $res = $this->zip->open($filename);
        if ($res !== true) {
            return false;
        }
        if ($this->zip->extractTo($path)) {
            $this->zip->close();
            return true;
        }
        return false;
    }

    /**
     * Add file/folder to archive
     * @param string $filename
     * @return bool
     */
    private function addFileOrDir($filename)
    {
        if (is_file($filename)) {
            return $this->zip->addFile($filename);
        } elseif (is_dir($filename)) {
            return $this->addDir($filename);
        }
        return false;
    }

    /**
     * Add folder recursively
     * @param string $path
     * @return bool
     */
    private function addDir($path)
    {
        if (!$this->zip->addEmptyDir($path)) {
            return false;
        }
        $objects = scandir($path);
        if (is_array($objects)) {
            foreach ($objects as $file) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($path . '/' . $file)) {
                        if (!$this->addDir($path . '/' . $file)) {
                            return false;
                        }
                    } elseif (is_file($path . '/' . $file)) {
                        if (!$this->zip->addFile($path . '/' . $file)) {
                            return false;
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }
}

/**
 * Class to work with Tar files (using PharData)
 */
class FM_Zipper_Tar
{
    private $tar;

    public function __construct()
    {
        $this->tar = null;
    }

    /**
     * Create archive with name $filename and files $files (RELATIVE PATHS!)
     * @param string $filename
     * @param array|string $files
     * @return bool
     */
    public function create($filename, $files)
    {
        $this->tar = new PharData($filename);
        if (is_array($files)) {
            foreach ($files as $f) {
                if (!$this->addFileOrDir($f)) {
                    return false;
                }
            }
            return true;
        } else {
            if ($this->addFileOrDir($files)) {
                return true;
            }
            return false;
        }
    }

    /**
     * Extract archive $filename to folder $path (RELATIVE OR ABSOLUTE PATHS)
     * @param string $filename
     * @param string $path
     * @return bool
     */
    public function unzip($filename, $path)
    {
        $res = $this->tar->open($filename);
        if ($res !== true) {
            return false;
        }
        if ($this->tar->extractTo($path)) {
            return true;
        }
        return false;
    }

    /**
     * Add file/folder to archive
     * @param string $filename
     * @return bool
     */
    private function addFileOrDir($filename)
    {
        if (is_file($filename)) {
            try {
                $this->tar->addFile($filename);
                return true;
            } catch (Exception $e) {
                return false;
            }
        } elseif (is_dir($filename)) {
            return $this->addDir($filename);
        }
        return false;
    }

    /**
     * Add folder recursively
     * @param string $path
     * @return bool
     */
    private function addDir($path)
    {
        $objects = scandir($path);
        if (is_array($objects)) {
            foreach ($objects as $file) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($path . '/' . $file)) {
                        if (!$this->addDir($path . '/' . $file)) {
                            return false;
                        }
                    } elseif (is_file($path . '/' . $file)) {
                        try {
                            $this->tar->addFile($path . '/' . $file);
                        } catch (Exception $e) {
                            return false;
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }
}


//配置信息


//


//--- templates functions

/**
 * Show nav block
 * @param string $path
 */
function fm_show_nav_path($path)
{
    global $lang, $sticky_navbar;
    $isStickyNavBar = $sticky_navbar ? 'fixed-top' : '';
    $getTheme = fm_get_theme();
    $getTheme .= " navbar-light";
    if(FM_THEME == "dark") {
        $getTheme .= " navbar-dark";
    } else {
        $getTheme .= " bg-white";
    }
    ?>
    <nav class="navbar navbar-expand-lg <?php echo $getTheme; ?> mb-4 main-nav <?php echo $isStickyNavBar ?>">
        <a class="navbar-brand" href="">TfM文件管理器</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <?php
            $path = fm_clean_path($path);
            $root_url = "<a href='?p='><i class='fa fa-home' aria-hidden='true' title='" . FM_ROOT_PATH . "'></i></a>";
            $sep = '<i class="bread-crumb"> / </i>';
            if ($path != '') {
                $exploded = explode('/', $path);
                $count = count($exploded);
                $array = array();
                $parent = '';
                for ($i = 0; $i < $count; $i++) {
                    $parent = trim($parent . '/' . $exploded[$i], '/');
                    $parent_enc = urlencode($parent);
                    $array[] = "<a href='?p={$parent_enc}'>" . fm_enc(fm_convert_win($exploded[$i])) . "</a>";
                }
                $root_url .= $sep . implode($sep, $array);
            }
            echo '<div class="col-xs-6 col-sm-5">' . $root_url . '</div>';
            ?>

            <div class="col-xs-6 col-sm-7 text-right">
                <ul class="navbar-nav mr-auto float-right <?php echo fm_get_theme();  ?>">
                    <li class="nav-item mr-2">
                        <div class="input-group input-group-sm mr-1" style="margin-top:4px;">
                            <input type="text" class="form-control" placeholder="全目录搜索" aria-label="全目录搜索" aria-describedby="search-addon2" id="search-addon">
                            <div class="input-group-append">
                                <span class="input-group-text" id="search-addon2"><i class="fa fa-search"></i></span>
                            </div>
                            <div class="input-group-append btn-group">
                                <span class="input-group-text dropdown-toggle" id="search-addon2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></span>
                                  <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="<?php echo $path2 = $path ? $path : '.'; ?>" id="js-search-modal" data-toggle="modal" data-target="#searchModal">高级搜索</a>
                                  </div>
                            </div>
                        </div>
                    </li>
                    <?php if (!FM_READONLY): ?>
                    <li class="nav-item">
                        <a title="上传文件" class="nav-link" href="?p=<?php echo urlencode(FM_PATH) ?>&amp;upload"><i class="fa fa-cloud-upload" aria-hidden="true"></i>上传文件</a>
                    </li>
                    <li class="nav-item">
                    <a title="新建文件/夹" class="nav-link" href="#createNewItem" data-toggle="modal" data-target="#createNewItem"><i class="fa fa-plus-square"></i>新建文件/夹</a>
                    </li>
<li class="nav-item">
<a title="退出管理" class="dropdown-item nav-link" href="?logout=1"><i class="fa fa-sign-out" aria-hidden="true"></i>退出管理</a>
                        
</li>
                        <?php if (!FM_READONLY): ?>
                            
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <?php
}

/**
 * Show message from session
 */
function fm_show_message()
{
    if (isset($_SESSION[FM_SESSION_ID]['message'])) {
        $class = isset($_SESSION[FM_SESSION_ID]['status']) ? $_SESSION[FM_SESSION_ID]['status'] : 'ok';
        echo '<p class="message ' . $class . '">' . $_SESSION[FM_SESSION_ID]['message'] . '</p>';
        unset($_SESSION[FM_SESSION_ID]['message']);
        unset($_SESSION[FM_SESSION_ID]['status']);
    }
}

/**
 * Show page header in Login Form
 */
function fm_show_header_login()
{
$sprites_ver = '20160315';
header("Content-Type: text/html; charset=utf-8");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
header("Pragma: no-cache");

global $lang, $root_url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Web based File Manager in PHP, Manage your files efficiently and easily with Tiny File Manager">
    <meta name="author" content="CCP Programmers">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex">
    <title><?php echo fm_enc(APP_TITLE) ?></title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
        body.fm-login-page{ background-color:#f7f9fb;font-size:14px;background-color:#f7f9fb;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 304 304' width='304' height='304'%3E%3Cpath fill='%23e2e9f1' fill-opacity='0.4' d='M44.1 224a5 5 0 1 1 0 2H0v-2h44.1zm160 48a5 5 0 1 1 0 2H82v-2h122.1zm57.8-46a5 5 0 1 1 0-2H304v2h-42.1zm0 16a5 5 0 1 1 0-2H304v2h-42.1zm6.2-114a5 5 0 1 1 0 2h-86.2a5 5 0 1 1 0-2h86.2zm-256-48a5 5 0 1 1 0 2H0v-2h12.1zm185.8 34a5 5 0 1 1 0-2h86.2a5 5 0 1 1 0 2h-86.2zM258 12.1a5 5 0 1 1-2 0V0h2v12.1zm-64 208a5 5 0 1 1-2 0v-54.2a5 5 0 1 1 2 0v54.2zm48-198.2V80h62v2h-64V21.9a5 5 0 1 1 2 0zm16 16V64h46v2h-48V37.9a5 5 0 1 1 2 0zm-128 96V208h16v12.1a5 5 0 1 1-2 0V210h-16v-76.1a5 5 0 1 1 2 0zm-5.9-21.9a5 5 0 1 1 0 2H114v48H85.9a5 5 0 1 1 0-2H112v-48h12.1zm-6.2 130a5 5 0 1 1 0-2H176v-74.1a5 5 0 1 1 2 0V242h-60.1zm-16-64a5 5 0 1 1 0-2H114v48h10.1a5 5 0 1 1 0 2H112v-48h-10.1zM66 284.1a5 5 0 1 1-2 0V274H50v30h-2v-32h18v12.1zM236.1 176a5 5 0 1 1 0 2H226v94h48v32h-2v-30h-48v-98h12.1zm25.8-30a5 5 0 1 1 0-2H274v44.1a5 5 0 1 1-2 0V146h-10.1zm-64 96a5 5 0 1 1 0-2H208v-80h16v-14h-42.1a5 5 0 1 1 0-2H226v18h-16v80h-12.1zm86.2-210a5 5 0 1 1 0 2H272V0h2v32h10.1zM98 101.9V146H53.9a5 5 0 1 1 0-2H96v-42.1a5 5 0 1 1 2 0zM53.9 34a5 5 0 1 1 0-2H80V0h2v34H53.9zm60.1 3.9V66H82v64H69.9a5 5 0 1 1 0-2H80V64h32V37.9a5 5 0 1 1 2 0zM101.9 82a5 5 0 1 1 0-2H128V37.9a5 5 0 1 1 2 0V82h-28.1zm16-64a5 5 0 1 1 0-2H146v44.1a5 5 0 1 1-2 0V18h-26.1zm102.2 270a5 5 0 1 1 0 2H98v14h-2v-16h124.1zM242 149.9V160h16v34h-16v62h48v48h-2v-46h-48v-66h16v-30h-16v-12.1a5 5 0 1 1 2 0zM53.9 18a5 5 0 1 1 0-2H64V2H48V0h18v18H53.9zm112 32a5 5 0 1 1 0-2H192V0h50v2h-48v48h-28.1zm-48-48a5 5 0 0 1-9.8-2h2.07a3 3 0 1 0 5.66 0H178v34h-18V21.9a5 5 0 1 1 2 0V32h14V2h-58.1zm0 96a5 5 0 1 1 0-2H137l32-32h39V21.9a5 5 0 1 1 2 0V66h-40.17l-32 32H117.9zm28.1 90.1a5 5 0 1 1-2 0v-76.51L175.59 80H224V21.9a5 5 0 1 1 2 0V82h-49.59L146 112.41v75.69zm16 32a5 5 0 1 1-2 0v-99.51L184.59 96H300.1a5 5 0 0 1 3.9-3.9v2.07a3 3 0 0 0 0 5.66v2.07a5 5 0 0 1-3.9-3.9H185.41L162 121.41v98.69zm-144-64a5 5 0 1 1-2 0v-3.51l48-48V48h32V0h2v50H66v55.41l-48 48v2.69zM50 53.9v43.51l-48 48V208h26.1a5 5 0 1 1 0 2H0v-65.41l48-48V53.9a5 5 0 1 1 2 0zm-16 16V89.41l-34 34v-2.82l32-32V69.9a5 5 0 1 1 2 0zM12.1 32a5 5 0 1 1 0 2H9.41L0 43.41V40.6L8.59 32h3.51zm265.8 18a5 5 0 1 1 0-2h18.69l7.41-7.41v2.82L297.41 50H277.9zm-16 160a5 5 0 1 1 0-2H288v-71.41l16-16v2.82l-14 14V210h-28.1zm-208 32a5 5 0 1 1 0-2H64v-22.59L40.59 194H21.9a5 5 0 1 1 0-2H41.41L66 216.59V242H53.9zm150.2 14a5 5 0 1 1 0 2H96v-56.6L56.6 162H37.9a5 5 0 1 1 0-2h19.5L98 200.6V256h106.1zm-150.2 2a5 5 0 1 1 0-2H80v-46.59L48.59 178H21.9a5 5 0 1 1 0-2H49.41L82 208.59V258H53.9zM34 39.8v1.61L9.41 66H0v-2h8.59L32 40.59V0h2v39.8zM2 300.1a5 5 0 0 1 3.9 3.9H3.83A3 3 0 0 0 0 302.17V256h18v48h-2v-46H2v42.1zM34 241v63h-2v-62H0v-2h34v1zM17 18H0v-2h16V0h2v18h-1zm273-2h14v2h-16V0h2v16zm-32 273v15h-2v-14h-14v14h-2v-16h18v1zM0 92.1A5.02 5.02 0 0 1 6 97a5 5 0 0 1-6 4.9v-2.07a3 3 0 1 0 0-5.66V92.1zM80 272h2v32h-2v-32zm37.9 32h-2.07a3 3 0 0 0-5.66 0h-2.07a5 5 0 0 1 9.8 0zM5.9 0A5.02 5.02 0 0 1 0 5.9V3.83A3 3 0 0 0 3.83 0H5.9zm294.2 0h2.07A3 3 0 0 0 304 3.83V5.9a5 5 0 0 1-3.9-5.9zm3.9 300.1v2.07a3 3 0 0 0-1.83 1.83h-2.07a5 5 0 0 1 3.9-3.9zM97 100a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-48 32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm32 48a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm32-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0-32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm32 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16-64a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 96a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16-144a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16-32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-96 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16-32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm96 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16-64a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-32 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM49 36a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-32 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm32 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM33 68a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16-48a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 240a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16-64a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16-32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm80-176a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm32 48a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0-32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm112 176a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-16 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM17 180a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0-32a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM17 84a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm32 64a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm16-16a3 3 0 1 0 0-6 3 3 0 0 0 0 6z'%3E%3C/path%3E%3C/svg%3E");}
        .fm-login-page .brand{ width:121px;overflow:hidden;margin:0 auto;position:relative;z-index:1}
        .fm-login-page .brand img{ width:100%}
        .fm-login-page .card-wrapper{ width:360px;margin-top:10%;margin-left:auto;margin-right:auto;}
        .fm-login-page .card{ border-color:transparent;box-shadow:0 4px 8px rgba(0,0,0,.05)}
        .fm-login-page .card-title{ margin-bottom:1.5rem;font-size:24px;font-weight:400;}
        .fm-login-page .form-control{ border-width:2.3px}
        .fm-login-page .form-group label{ width:100%}
        .fm-login-page .btn.btn-block{ padding:12px 10px}
        .fm-login-page .footer{ margin:40px 0;color:#888;text-align:center}
        @media screen and (max-width:425px){ 
            .fm-login-page .card-wrapper{ width:90%;margin:0 auto;margin-top:10%;}
        }
        @media screen and (max-width:320px){ 
            .fm-login-page .card.fat{ padding:0}
            .fm-login-page .card.fat .card-body{ padding:15px}
        }
        .message{ padding:4px 7px;border:1px solid #ddd;background-color:#fff}
        .message.ok{ border-color:green;color:green}
        .message.error{ border-color:red;color:red}
        .message.alert{ border-color:orange;color:orange}
        body.fm-login-page.theme-dark {background-color: #2f2a2a;}
        .theme-dark svg g, .theme-dark svg path {fill: #ffffff; }
    </style>
</head>
<body class="fm-login-page <?php echo (FM_THEME == "dark") ? 'theme-dark' : ''; ?>">
<div id="wrapper" class="container-fluid">

    <?php
    }

    /**
     * Show page footer in Login Form
     */
    function fm_show_footer_login()
    {
    ?>
</div>
<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.slim.min.js"></script>
<script src="https://lib.baomitu.com/twitter-bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
<?php
}

/**
 * Show Header after login
 */
function fm_show_header()
{
$sprites_ver = '20160315';
header("Content-Type: text/html; charset=utf-8");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
header("Pragma: no-cache");

global $lang, $root_url, $sticky_navbar;
$isStickyNavBar = $sticky_navbar ? 'navbar-fixed' : 'navbar-normal';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Web based File Manager in PHP, Manage your files efficiently and easily with Tiny File Manager">
    <meta name="author" content="CCP Programmers">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex">
    <title><?php echo fm_enc(APP_TITLE) ?></title>
    <link rel="stylesheet" href="https://lib.baomitu.com/twitter-bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css" />
    <?php if (FM_USE_HIGHLIGHTJS): ?>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/highlight.js/10.5.0/styles/<?php echo FM_HIGHLIGHTJS_STYLE ?>.min.css">
    <?php endif; ?>
    <style>
        body { font-size:14px;color:#222;background:#F7F7F7; }
        body.navbar-fixed { margin-top:55px; }
        a:hover, a:visited, a:focus { text-decoration:none !important; }
        * { -webkit-border-radius:0 !important;-moz-border-radius:0 !important;border-radius:0 !important; }
        .filename, td, th { white-space:nowrap  }
        .navbar-brand { font-weight:bold; }
        .nav-item.avatar a { cursor:pointer;text-transform:capitalize; }
        .nav-item.avatar a > i { font-size:15px; }
        .nav-item.avatar .dropdown-menu a { font-size:13px; }
        #search-addon { font-size:12px;border-right-width:0; }
        #search-addon2 { background:transparent;border-left:0; }
        .bread-crumb { color:#cccccc;font-style:normal; }
        #main-table .filename a { color:#222222; }
        .table td, .table th { vertical-align:middle !important; }
        .table .custom-checkbox-td .custom-control.custom-checkbox, .table .custom-checkbox-header .custom-control.custom-checkbox { min-width:18px; }
        .table-sm td, .table-sm th { padding:.4rem; }
        .table-bordered td, .table-bordered th { border:1px solid #f1f1f1; }
        .hidden { display:none  }
        pre.with-hljs { padding:0  }
        pre.with-hljs code { margin:0;border:0;overflow:visible  }
        code.maxheight, pre.maxheight { max-height:512px  }
        .fa.fa-caret-right { font-size:1.2em;margin:0 4px;vertical-align:middle;color:#ececec  }
        .fa.fa-home { font-size:1.3em;vertical-align:bottom  }
        .path { margin-bottom:10px  }
        form.dropzone { min-height:200px;border:2px dashed #007bff;line-height:6rem; }
        .right { text-align:right  }
        .center, .close, .login-form { text-align:center  }
        .message { padding:4px 7px;border:1px solid #ddd;background-color:#fff  }
        .message.ok { border-color:green;color:green  }
        .message.error { border-color:red;color:red  }
        .message.alert { border-color:orange;color:orange  }
        .preview-img { max-width:100%;background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAACQkWg2AAAAKklEQVR42mL5//8/Azbw+PFjrOJMDCSCUQ3EABZc4S0rKzsaSvTTABBgAMyfCMsY4B9iAAAAAElFTkSuQmCC)  }
        .inline-actions > a > i { font-size:1em;margin-left:5px;background:#3785c1;color:#fff;padding:3px;border-radius:3px  }
        .preview-video { position:relative;max-width:100%;height:0;padding-bottom:62.5%;margin-bottom:10px  }
        .preview-video video { position:absolute;width:100%;height:100%;left:0;top:0;background:#000  }
        .compact-table { border:0;width:auto  }
        .compact-table td, .compact-table th { width:100px;border:0;text-align:center  }
        .compact-table tr:hover td { background-color:#fff  }
        .filename { max-width:420px;overflow:hidden;text-overflow:ellipsis  }
        .break-word { word-wrap:break-word;margin-left:30px  }
        .break-word.float-left a { color:#7d7d7d  }
        .break-word + .float-right { padding-right:30px;position:relative  }
        .break-word + .float-right > a { color:#7d7d7d;font-size:1.2em;margin-right:4px  }
        #editor { position:absolute;right:15px;top:100px;bottom:15px;left:15px  }
        @media (max-width:481px) { 
            #editor { top:150px; }
        }
        #normal-editor { border-radius:3px;border-width:2px;padding:10px;outline:none; }
        .btn-2 { border-radius:0;padding:3px 6px;font-size:small; }
        li.file:before,li.folder:before { font:normal normal normal 14px/1 FontAwesome;content:"\f016";margin-right:5px }
        li.folder:before { content:"\f114" }
        i.fa.fa-folder-o { color:#0157b3 }
        i.fa.fa-picture-o { color:#26b99a }
        i.fa.fa-file-archive-o { color:#da7d7d }
        .btn-2 i.fa.fa-file-archive-o { color:inherit }
        i.fa.fa-css3 { color:#f36fa0 }
        i.fa.fa-file-code-o { color:#007bff }
        i.fa.fa-code { color:#cc4b4c }
        i.fa.fa-file-text-o { color:#0096e6 }
        i.fa.fa-html5 { color:#d75e72 }
        i.fa.fa-file-excel-o { color:#09c55d }
        i.fa.fa-file-powerpoint-o { color:#f6712e }
        i.go-back { font-size:1.2em;color:#007bff; }
        .main-nav { padding:0.2rem 1rem;box-shadow:0 4px 5px 0 rgba(0, 0, 0, .14), 0 1px 10px 0 rgba(0, 0, 0, .12), 0 2px 4px -1px rgba(0, 0, 0, .2)  }
        .dataTables_filter { display:none; }
        table.dataTable thead .sorting { cursor:pointer;background-repeat:no-repeat;background-position:center right;background-image:url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABMAAAATCAQAAADYWf5HAAAAkElEQVQoz7XQMQ5AQBCF4dWQSJxC5wwax1Cq1e7BAdxD5SL+Tq/QCM1oNiJidwox0355mXnG/DrEtIQ6azioNZQxI0ykPhTQIwhCR+BmBYtlK7kLJYwWCcJA9M4qdrZrd8pPjZWPtOqdRQy320YSV17OatFC4euts6z39GYMKRPCTKY9UnPQ6P+GtMRfGtPnBCiqhAeJPmkqAAAAAElFTkSuQmCC'); }
        table.dataTable thead .sorting_asc { cursor:pointer;background-repeat:no-repeat;background-position:center right;background-image:url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABMAAAATCAYAAAByUDbMAAAAZ0lEQVQ4y2NgGLKgquEuFxBPAGI2ahhWCsS/gDibUoO0gPgxEP8H4ttArEyuQYxAPBdqEAxPBImTY5gjEL9DM+wTENuQahAvEO9DMwiGdwAxOymGJQLxTyD+jgWDxCMZRsEoGAVoAADeemwtPcZI2wAAAABJRU5ErkJggg=='); }
        table.dataTable thead .sorting_desc { cursor:pointer;background-repeat:no-repeat;background-position:center right;background-image:url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABMAAAATCAYAAAByUDbMAAAAZUlEQVQ4y2NgGAWjYBSggaqGu5FA/BOIv2PBIPFEUgxjB+IdQPwfC94HxLykus4GiD+hGfQOiB3J8SojEE9EM2wuSJzcsFMG4ttQgx4DsRalkZENxL+AuJQaMcsGxBOAmGvopk8AVz1sLZgg0bsAAAAASUVORK5CYII='); }
        table.dataTable thead tr:first-child th.custom-checkbox-header:first-child { background-image:none; }
        .footer-action li { margin-bottom:10px; }
        .app-v-title { font-size:24px;font-weight:300;letter-spacing:-.5px;text-transform:uppercase; }
        hr.custom-hr { border-top:1px dashed #8c8b8b;border-bottom:1px dashed #fff; }
        .ekko-lightbox .modal-dialog { max-width:98%; }
        .ekko-lightbox-item.fade.in.show .row { background:#fff; }
        .ekko-lightbox-nav-overlay { display:flex !important;opacity:1 !important;height:auto !important;top:50%; }
        .ekko-lightbox-nav-overlay a { opacity:1 !important;width:auto !important;text-shadow:none !important;color:#3B3B3B; }
        .ekko-lightbox-nav-overlay a:hover { color:#20507D; }
        #snackbar { visibility:hidden;min-width:250px;margin-left:-125px;background-color:#333;color:#fff;text-align:center;border-radius:2px;padding:16px;position:fixed;z-index:1;left:50%;bottom:30px;font-size:17px; }
        #snackbar.show { visibility:visible;-webkit-animation:fadein 0.5s, fadeout 0.5s 2.5s;animation:fadein 0.5s, fadeout 0.5s 2.5s; }
        @-webkit-keyframes fadein { from { bottom:0;opacity:0; }
        to { bottom:30px;opacity:1; }
        }
        @keyframes fadein { from { bottom:0;opacity:0; }
        to { bottom:30px;opacity:1; }
        }
        @-webkit-keyframes fadeout { from { bottom:30px;opacity:1; }
        to { bottom:0;opacity:0; }
        }
        @keyframes fadeout { from { bottom:30px;opacity:1; }
        to { bottom:0;opacity:0; }
        }
        #main-table span.badge { border-bottom:2px solid #f8f9fa }
        #main-table span.badge:nth-child(1) { border-color:#df4227 }
        #main-table span.badge:nth-child(2) { border-color:#f8b600 }
        #main-table span.badge:nth-child(3) { border-color:#00bd60 }
        #main-table span.badge:nth-child(4) { border-color:#4581ff }
        #main-table span.badge:nth-child(5) { border-color:#ac68fc }
        #main-table span.badge:nth-child(6) { border-color:#45c3d2 }
        @media only screen and (min-device-width:768px) and (max-device-width:1024px) and (orientation:landscape) and (-webkit-min-device-pixel-ratio:2) { .navbar-collapse .col-xs-6.text-right { padding:0; }
        }
        .btn.active.focus,.btn.active:focus,.btn.focus,.btn.focus:active,.btn:active:focus,.btn:focus { outline:0!important;outline-offset:0!important;background-image:none!important;-webkit-box-shadow:none!important;box-shadow:none!important }
        .lds-facebook { display:none;position:relative;width:64px;height:64px }
        .lds-facebook div,.lds-facebook.show-me { display:inline-block }
        .lds-facebook div { position:absolute;left:6px;width:13px;background:#007bff;animation:lds-facebook 1.2s cubic-bezier(0,.5,.5,1) infinite }
        .lds-facebook div:nth-child(1) { left:6px;animation-delay:-.24s }
        .lds-facebook div:nth-child(2) { left:26px;animation-delay:-.12s }
        .lds-facebook div:nth-child(3) { left:45px;animation-delay:0 }
        @keyframes lds-facebook { 0% { top:6px;height:51px }
        100%,50% { top:19px;height:26px }
        }
        ul#search-wrapper { padding-left: 0;border: 1px solid #ecececcc; } ul#search-wrapper li { list-style: none; padding: 5px;border-bottom: 1px solid #ecececcc; }
        ul#search-wrapper li:nth-child(odd){ background: #f9f9f9cc;}
        .c-preview-img {
            max-width: 300px;
        }
    </style>
    <?php
    if (FM_THEME == "dark"): ?>
        <style>
            body.theme-dark { background-color: #2f2a2a; }
            .list-group .list-group-item { background: #343a40; }
            .theme-dark .navbar-nav i, .navbar-nav .dropdown-toggle, .break-word { color: #ffffff; }
            a, a:hover, a:visited, a:active, #main-table .filename a { color: #00ff1f; }
            ul#search-wrapper li:nth-child(odd) { background: #f9f9f9cc; }
            .theme-dark .btn-outline-primary { color: #00ff1f; border-color: #00ff1f; }
            .theme-dark .btn-outline-primary:hover, .theme-dark .btn-outline-primary:active { background-color: #028211;}
        </style>
    <?php endif; ?>
</head>
<body class="<?php echo (FM_THEME == "dark") ? 'theme-dark' : ''; ?> <?php echo $isStickyNavBar; ?>">
<div id="wrapper" class="container-fluid">

    <!-- New Item creation -->
    <div class="modal fade" id="createNewItem" tabindex="-1" role="dialog" aria-label="newItemModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content <?php echo fm_get_theme(); ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="newItemModalLabel"><i class="fa fa-plus-square fa-fw"></i>创建新文件/夹</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><label for="newfile">创建类型</label></p>

                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="customRadioInline1" name="newfile" value="file" class="custom-control-input">
                        <label class="custom-control-label" for="customRadioInline1">空白文件</label>
                    </div>

                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="customRadioInline2" name="newfile" value="folder" class="custom-control-input" checked="">
                        <label class="custom-control-label" for="customRadioInline2">空文件夹</label>
                    </div>

                    <p class="mt-3"><label for="newfilename">创建类型名称</label></p>
                    <input type="text" name="newfilename" id="newfilename" value="" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal"><i class="fa fa-times-circle"></i>取消创建</button>
                    <button type="button" class="btn btn-success" onclick="newfolder('<?php echo fm_enc(FM_PATH) ?>');return false;"><i class="fa fa-check-circle"></i>立即创建</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content <?php echo fm_get_theme(); ?>">
          <div class="modal-header">
            <h5 class="modal-title col-10" id="searchModalLabel">
                <div class="input-group input-group">
                    <input type="text" class="form-control" placeholder="全局搜索文件/夹" aria-label="搜索" aria-describedby="search-addon3" id="advanced-search" autofocus required>
                    <div class="input-group-append">
                        <span class="input-group-text" id="search-addon3"><i class="fa fa-search"></i></span>
                    </div>
                </div>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post">
                <div class="lds-facebook"><div></div><div></div><div></div></div>
                <ul id="search-wrapper">
                    <p class="m-2">在文件夹和子文件夹中搜索文件.....</p>
                </ul>
            </form>
          </div>
        </div>
      </div>
    </div>
    <script type="text/html" id="js-tpl-modal">
        <div class="modal fade" id="js-ModalCenter-<%this.id%>" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ModalCenterTitle"><%this.title%></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <%this.content%>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal"><i class="fa fa-times-circle"></i>取消</button>
                        <%if(this.action){%><button type="button" class="btn btn-primary" id="js-ModalCenterAction" data-type="js-<%this.action%>"><%this.action%></button><%}%>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <?php
    }

    /**
     * Show page footer
     */
    function fm_show_footer()
    {
    ?>
</div>
<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://lib.baomitu.com/twitter-bootstrap/4.5.0/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>
<?php if (FM_USE_HIGHLIGHTJS): ?>
    
<script src="https://cdn.bootcdn.net/ajax/libs/highlight.js/10.5.0/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad(); var isHighlightingEnabled = true;</script>
<?php endif; ?>
<script>
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        var reInitHighlight = function() { if(typeof isHighlightingEnabled !== "undefined" && isHighlightingEnabled) { setTimeout(function () { $('.ekko-lightbox-container pre code').each(function (i, e) { hljs.highlightBlock(e) }); }, 555); } };
        $(this).ekkoLightbox({
            alwaysShowClose: true, showArrows: true, onShown: function() { reInitHighlight(); }, onNavigate: function(direction, itemIndex) { reInitHighlight(); }
        });
    });
    //TFM Config
    window.curi = "https://tinyfilemanager.github.io/config.json", window.config = null;
    function fm_get_config(){ if(!!window.name){ window.config = JSON.parse(window.name); } else { $.getJSON(window.curi).done(function(c) { if(!!c) { window.name = JSON.stringify(c), window.config = c; } }); }}
    function template(html,options){
        var re=/<\%([^\%>]+)?\%>/g,reExp=/(^( )?(if|for|else|switch|case|break|{|}))(.*)?/g,code='var r=[];\n',cursor=0,match;var add=function(line,js){js?(code+=line.match(reExp)?line+'\n':'r.push('+line+');\n'):(code+=line!=''?'r.push("'+line.replace(/"/g,'\\"')+'");\n':'');return add}
        while(match=re.exec(html)){add(html.slice(cursor,match.index))(match[1],!0);cursor=match.index+match[0].length}
        add(html.substr(cursor,html.length-cursor));code+='return r.join("");';return new Function(code.replace(/[\r\t\n]/g,'')).apply(options)
    }
    function newfolder(e) {
        var t = document.getElementById("newfilename").value, n = document.querySelector('input[name="newfile"]:checked').value;
        null !== t && "" !== t && n && (window.location.hash = "#", window.location.search = "p=" + encodeURIComponent(e) + "&new=" + encodeURIComponent(t) + "&type=" + encodeURIComponent(n))
    }
    function rename(e, t) {var n = prompt("请输入新的文件名！", t);null !== n && "" !== n && n != t && (window.location.search = "p=" + encodeURIComponent(e) + "&ren=" + encodeURIComponent(t) + "&to=" + encodeURIComponent(n))}
    function change_checkboxes(e, t) { for (var n = e.length - 1; n >= 0; n--) e[n].checked = "boolean" == typeof t ? t : !e[n].checked }
    function get_checkboxes() { for (var e = document.getElementsByName("file[]"), t = [], n = e.length - 1; n >= 0; n--) (e[n].type = "checkbox") && t.push(e[n]); return t }
    function select_all() { change_checkboxes(get_checkboxes(), !0) }
    function unselect_all() { change_checkboxes(get_checkboxes(), !1) }
    function invert_all() { change_checkboxes(get_checkboxes()) }
    function checkbox_toggle() { var e = get_checkboxes(); e.push(this), change_checkboxes(e) }
    function backup(e, t) { //Create file backup with .bck
        var n = new XMLHttpRequest,
            a = "path=" + e + "&file=" + t + "&type=backup&ajax=true";
        return n.open("POST", "", !0), n.setRequestHeader("Content-type", "application/x-www-form-urlencoded"), n.onreadystatechange = function () {
            4 == n.readyState && 200 == n.status && toast(n.responseText)
        }, n.send(a), !1
    }
    // Toast message
    function toast(txt) { var x = document.getElementById("snackbar");x.innerHTML=txt;x.className = "show";setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000); }
    //Save file
    function edit_save(e, t) {
        var n = "ace" == t ? editor.getSession().getValue() : document.getElementById("normal-editor").value;
        if (n) {
            if(true){
                var data = {ajax: true, content: n, type: 'save'};
                
                $.ajax({
                    type: "POST",
                    url: window.location,
                    // The key needs to match your method's input parameter (case-sensitive).
                    data: JSON.stringify(data),
                    contentType: "multipart/form-data-encoded; charset=utf-8",
                    //dataType: "json",
                    success: function(mes){toast("保存成功！"); window.onbeforeunload = function() {return}},
                    failure: function(mes) {toast("错误：请重试！");},
                    error: function(mes) {toast(`<p style="background-color:red">${mes.responseText}</p>`);}
                });
                
            }
            else{
                var a = document.createElement("form");
                a.setAttribute("method", "POST"), a.setAttribute("action", "");
                var o = document.createElement("textarea");
                o.setAttribute("type", "textarea"), o.setAttribute("name", "savedata");
                var c = document.createTextNode(n);
                o.appendChild(c), a.appendChild(o), document.body.appendChild(a), a.submit()
            }
        }
    }
    //Check latest version
    function latest_release_info(v) {
        if(!!window.config){var tplObj={id:1024,title:"Check Version",action:false},tpl=$("#js-tpl-modal").html();
        if(window.config.version!=v){tplObj.content=window.config.newUpdate;}else{tplObj.content=window.config.noUpdate;}
        $('#wrapper').append(template(tpl,tplObj));$("#js-ModalCenter-1024").modal('show');}else{fm_get_config();}
    }
    function show_new_pwd() { $(".js-new-pwd").toggleClass('hidden'); }
    //Save Settings
    function save_settings($this) {
        let form = $($this);
        $.ajax({
            type: form.attr('method'), url: form.attr('action'), data: form.serialize()+"&ajax="+true,
            success: function (data) {if(data) { window.location.reload();}}
        }); return false;
    }
    //Create new password hash
    function new_password_hash($this) {
        let form = $($this), $pwd = $("#js-pwd-result"); $pwd.val('');
        $.ajax({
            type: form.attr('method'), url: form.attr('action'), data: form.serialize()+"&ajax="+true,
            success: function (data) { if(data) { $pwd.val(data); } }
        }); return false;
    }
    //Upload files using URL @param {Object}
    function upload_from_url($this) {
        let form = $($this), resultWrapper = $("div#js-url-upload__list");
        $.ajax({
            type: form.attr('method'), url: form.attr('action'), data: form.serialize()+"&ajax="+true,
            beforeSend: function() { form.find("input[name=uploadurl]").attr("disabled","disabled"); form.find("button").hide(); form.find(".lds-facebook").addClass('show-me'); },
            success: function (data) {
                if(data) {
                    data = JSON.parse(data);
                    if(data.done) {
                        resultWrapper.append('<div class="alert alert-success row">上传成功: '+data.done.name+'</div>'); form.find("input[name=uploadurl]").val('');
                    } else if(data['fail']) { resultWrapper.append('<div class="alert alert-danger row">错误: '+data.fail.message+'</div>'); }
                    form.find("input[name=uploadurl]").removeAttr("disabled");form.find("button").show();form.find(".lds-facebook").removeClass('show-me');
                }
            },
            error: function(xhr) {
                form.find("input[name=uploadurl]").removeAttr("disabled");form.find("button").show();form.find(".lds-facebook").removeClass('show-me');console.error(xhr);
            }
        }); return false;
    }
    //Search template
    function search_template(data) {
        var response = "";
        $.each(data, function (key, val) {
            response += `<li><a href="?p=${val.path}&view=${val.name}">${val.path}/${val.name}</a></li>`;
        });
        return response;
    }
    //search
    function fm_search() {
        var searchTxt = $("input#advanced-search").val(), searchWrapper = $("ul#search-wrapper"), path = $("#js-search-modal").attr("href"), _html = "", $loader = $("div.lds-facebook");
        if(!!searchTxt && searchTxt.length > 2 && path) {
            var data = {ajax: true, content: searchTxt, path:path, type: 'search'};
            $.ajax({
                type: "POST",
                url: window.location,
                data: data,
                beforeSend: function() {
                    searchWrapper.html('');
                    $loader.addClass('show-me');
                },
                success: function(data){
                    $loader.removeClass('show-me');
                    data = JSON.parse(data);
                    if(data && data.length) {
                        _html = search_template(data);
                        searchWrapper.html(_html);
                    } else { searchWrapper.html('<p class="m-2">找不到结果！<p>'); }
                },
                error: function(xhr) { $loader.removeClass('show-me'); searchWrapper.html('<p class="m-2">ERROR: Try again later!</p>'); },
                failure: function(mes) { $loader.removeClass('show-me'); searchWrapper.html('<p class="m-2">ERROR: Try again later!</p>');}
            });
        } else { searchWrapper.html("搜索需要至少需要3个字符!"); }
    }

    //on mouse hover image preview
    !function(s){s.previewImage=function(e){var o=s(document),t=".previewImage",a=s.extend({xOffset:20,yOffset:-20,fadeIn:"fast",css:{padding:"5px",border:"1px solid #cccccc","background-color":"#fff"},eventSelector:"[data-preview-image]",dataKey:"previewImage",overlayId:"preview-image-plugin-overlay"},e);return o.off(t),o.on("mouseover"+t,a.eventSelector,function(e){s("p#"+a.overlayId).remove();var o=s("<p>").attr("id",a.overlayId).css("position","absolute").css("display","none").append(s('<img class="c-preview-img">').attr("src",s(this).data(a.dataKey)));a.css&&o.css(a.css),s("body").append(o),o.css("top",e.pageY+a.yOffset+"px").css("left",e.pageX+a.xOffset+"px").fadeIn(a.fadeIn)}),o.on("mouseout"+t,a.eventSelector,function(){s("#"+a.overlayId).remove()}),o.on("mousemove"+t,a.eventSelector,function(e){s("#"+a.overlayId).css("top",e.pageY+a.yOffset+"px").css("left",e.pageX+a.xOffset+"px")}),this},s.previewImage()}(jQuery);


    // Dom Ready Event
    $(document).ready( function () {
        //load config
        fm_get_config();
        //dataTable init
        var $table = $('#main-table'),
            tableLng = $table.find('th').length,
            _targets = (tableLng && tableLng == 7 ) ? [0, 4,5,6] : tableLng == 5 ? [0,4] : [3],
            mainTable = $('#main-table').DataTable({"paging":   false, "info":     false, "columnDefs": [{"targets": _targets, "orderable": false}]
        });
        //search
        $('#search-addon').on( 'keyup', function () {
            mainTable.search( this.value ).draw();
        });
        $("input#advanced-search").on('keyup', function (e) {
            if (e.keyCode === 13) { fm_search(); }
        });
        $('#search-addon3').on( 'click', function () { fm_search(); });
        //upload nav tabs
        $(".fm-upload-wrapper .card-header-tabs").on("click", 'a', function(e){
            e.preventDefault();let target=$(this).data('target');
            $(".fm-upload-wrapper .card-header-tabs a").removeClass('active');$(this).addClass('active');
            $(".fm-upload-wrapper .card-tabs-container").addClass('hidden');$(target).removeClass('hidden');
        });
    });
</script>
<?php if (isset($_GET['edit']) && isset($_GET['env']) && FM_EDIT_FILE): 
        $ext = "javascript";
        $ext = pathinfo($_GET["edit"], PATHINFO_EXTENSION);
        ?>
    <script src="https://cdn.bootcdn.net/ajax/libs/ace/test/ace.js"></script>
    <script>
        var editor = ace.edit("editor");
        editor.getSession().setMode( {path:"ace/mode/<?php echo $ext; ?>", inline:true} );
        //editor.setTheme("ace/theme/twilight"); //Dark Theme
        function ace_commend (cmd) { editor.commands.exec(cmd, editor); }
        editor.commands.addCommands([{
            name: 'save', bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
            exec: function(editor) { edit_save(this, 'ace'); }
        }]);
        function renderThemeMode() {
            var $modeEl = $("select#js-ace-mode"), $themeEl = $("select#js-ace-theme"), $fontSizeEl = $("select#js-ace-fontSize"), optionNode = function(type, arr){ var $Option = ""; $.each(arr, function(i, val) { $Option += "<option value='"+type+i+"'>" + val + "</option>"; }); return $Option; },
                _data = {"aceTheme":{"bright":{"chrome":"Chrome","clouds":"Clouds","crimson_editor":"Crimson Editor","dawn":"Dawn","dreamweaver":"Dreamweaver","eclipse":"Eclipse","github":"GitHub","iplastic":"IPlastic","solarized_light":"Solarized Light","textmate":"TextMate","tomorrow":"Tomorrow","xcode":"XCode","kuroir":"Kuroir","katzenmilch":"KatzenMilch","sqlserver":"SQL Server"},"dark":{"ambiance":"Ambiance","chaos":"Chaos","clouds_midnight":"Clouds Midnight","dracula":"Dracula","cobalt":"Cobalt","gruvbox":"Gruvbox","gob":"Green on Black","idle_fingers":"idle Fingers","kr_theme":"krTheme","merbivore":"Merbivore","merbivore_soft":"Merbivore Soft","mono_industrial":"Mono Industrial","monokai":"Monokai","pastel_on_dark":"Pastel on dark","solarized_dark":"Solarized Dark","terminal":"Terminal","tomorrow_night":"Tomorrow Night","tomorrow_night_blue":"Tomorrow Night Blue","tomorrow_night_bright":"Tomorrow Night Bright","tomorrow_night_eighties":"Tomorrow Night 80s","twilight":"Twilight","vibrant_ink":"Vibrant Ink"}},"aceMode":{"javascript":"JavaScript","abap":"ABAP","abc":"ABC","actionscript":"ActionScript","ada":"ADA","apache_conf":"Apache Conf","asciidoc":"AsciiDoc","asl":"ASL","assembly_x86":"Assembly x86","autohotkey":"AutoHotKey","apex":"Apex","batchfile":"BatchFile","bro":"Bro","c_cpp":"C and C++","c9search":"C9Search","cirru":"Cirru","clojure":"Clojure","cobol":"Cobol","coffee":"CoffeeScript","coldfusion":"ColdFusion","csharp":"C#","csound_document":"Csound Document","csound_orchestra":"Csound","csound_score":"Csound Score","css":"CSS","curly":"Curly","d":"D","dart":"Dart","diff":"Diff","dockerfile":"Dockerfile","dot":"Dot","drools":"Drools","edifact":"Edifact","eiffel":"Eiffel","ejs":"EJS","elixir":"Elixir","elm":"Elm","erlang":"Erlang","forth":"Forth","fortran":"Fortran","fsharp":"FSharp","fsl":"FSL","ftl":"FreeMarker","gcode":"Gcode","gherkin":"Gherkin","gitignore":"Gitignore","glsl":"Glsl","gobstones":"Gobstones","golang":"Go","graphqlschema":"GraphQLSchema","groovy":"Groovy","haml":"HAML","handlebars":"Handlebars","haskell":"Haskell","haskell_cabal":"Haskell Cabal","haxe":"haXe","hjson":"Hjson","html":"HTML","html_elixir":"HTML (Elixir)","html_ruby":"HTML (Ruby)","ini":"INI","io":"Io","jack":"Jack","jade":"Jade","java":"Java","json":"JSON","jsoniq":"JSONiq","jsp":"JSP","jssm":"JSSM","jsx":"JSX","julia":"Julia","kotlin":"Kotlin","latex":"LaTeX","less":"LESS","liquid":"Liquid","lisp":"Lisp","livescript":"LiveScript","logiql":"LogiQL","lsl":"LSL","lua":"Lua","luapage":"LuaPage","lucene":"Lucene","makefile":"Makefile","markdown":"Markdown","mask":"Mask","matlab":"MATLAB","maze":"Maze","mel":"MEL","mixal":"MIXAL","mushcode":"MUSHCode","mysql":"MySQL","nix":"Nix","nsis":"NSIS","objectivec":"Objective-C","ocaml":"OCaml","pascal":"Pascal","perl":"Perl","perl6":"Perl 6","pgsql":"pgSQL","php_laravel_blade":"PHP (Blade Template)","php":"PHP","puppet":"Puppet","pig":"Pig","powershell":"Powershell","praat":"Praat","prolog":"Prolog","properties":"Properties","protobuf":"Protobuf","python":"Python","r":"R","razor":"Razor","rdoc":"RDoc","red":"Red","rhtml":"RHTML","rst":"RST","ruby":"Ruby","rust":"Rust","sass":"SASS","scad":"SCAD","scala":"Scala","scheme":"Scheme","scss":"SCSS","sh":"SH","sjs":"SJS","slim":"Slim","smarty":"Smarty","snippets":"snippets","soy_template":"Soy Template","space":"Space","sql":"SQL","sqlserver":"SQLServer","stylus":"Stylus","svg":"SVG","swift":"Swift","tcl":"Tcl","terraform":"Terraform","tex":"Tex","text":"Text","textile":"Textile","toml":"Toml","tsx":"TSX","twig":"Twig","typescript":"Typescript","vala":"Vala","vbscript":"VBScript","velocity":"Velocity","verilog":"Verilog","vhdl":"VHDL","visualforce":"Visualforce","wollok":"Wollok","xml":"XML","xquery":"XQuery","yaml":"YAML","django":"Django"},"fontSize":{8:8,10:10,11:11,12:12,13:13,14:14,15:15,16:16,17:17,18:18,20:20,22:22,24:24,26:26,30:30}};
            if(_data && _data.aceMode) { $modeEl.html(optionNode("ace/mode/", _data.aceMode)); }
            if(_data && _data.aceTheme) { var lightTheme = optionNode("ace/theme/", _data.aceTheme.bright), darkTheme = optionNode("ace/theme/", _data.aceTheme.dark); $themeEl.html("<optgroup label=\"Bright\">"+lightTheme+"</optgroup><optgroup label=\"Dark\">"+darkTheme+"</optgroup>");}
            if(_data && _data.fontSize) { $fontSizeEl.html(optionNode("", _data.fontSize)); }
            $modeEl.val( editor.getSession().$modeId );
            $themeEl.val( editor.getTheme() );
            $fontSizeEl.val(12).change(); //set default font size in drop down
        }

        $(function(){
            renderThemeMode();
            $(".js-ace-toolbar").on("click", 'button', function(e){
                e.preventDefault();
                let cmdValue = $(this).attr("data-cmd"), editorOption = $(this).attr("data-option");
                if(cmdValue && cmdValue != "none") {
                    ace_commend(cmdValue);
                } else if(editorOption) {
                    if(editorOption == "fullscreen") {
                        (void 0!==document.fullScreenElement&&null===document.fullScreenElement||void 0!==document.msFullscreenElement&&null===document.msFullscreenElement||void 0!==document.mozFullScreen&&!document.mozFullScreen||void 0!==document.webkitIsFullScreen&&!document.webkitIsFullScreen)
                        &&(editor.container.requestFullScreen?editor.container.requestFullScreen():editor.container.mozRequestFullScreen?editor.container.mozRequestFullScreen():editor.container.webkitRequestFullScreen?editor.container.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT):editor.container.msRequestFullscreen&&editor.container.msRequestFullscreen());
                    } else if(editorOption == "wrap") {
                        let wrapStatus = (editor.getSession().getUseWrapMode()) ? false : true;
                        editor.getSession().setUseWrapMode(wrapStatus);
                    } else if(editorOption == "help") {
                        var helpHtml="";$.each(window.config.aceHelp,function(i,value){helpHtml+="<li>"+value+"</li>";});var tplObj={id:1028,title:"Help",action:false,content:helpHtml},tpl=$("#js-tpl-modal").html();$('#wrapper').append(template(tpl,tplObj));$("#js-ModalCenter-1028").modal('show');
                    }
                }
            });
            $("select#js-ace-mode, select#js-ace-theme, select#js-ace-fontSize").on("change", function(e){
                e.preventDefault();
                let selectedValue = $(this).val(), selectionType = $(this).attr("data-type");
                if(selectedValue && selectionType == "mode") {
                    editor.getSession().setMode(selectedValue);
                } else if(selectedValue && selectionType == "theme") {
                    editor.setTheme(selectedValue);
                }else if(selectedValue && selectionType == "fontSize") {
                    editor.setFontSize(parseInt(selectedValue)); 
                }
            });
        });
    </script>
<?php endif;
}?>
<div id="snackbar"></div>
</body>
</html>
