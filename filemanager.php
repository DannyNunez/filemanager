<?
////////////////////////////////////////////////////////////////////////
// Fist try at an open source project so be easy on me!               //
// change username and password to what ever you would like           //
// feel free to remove it if you would like everyone to see your code //
// for everything on your server. you can change cookie set time if   //
// you would only like it session bassed.                             //
// MIT license http://www.opensource.org/licenses/mit-license.php     //
////////////////////////////////////////////////////////////////////////
/*
<svg xmlns="http://www.w3.org/2000/svg" version="1.1">
   <circle cx="100" cy="50" r="40" stroke="green" stroke-width="2" fill="transparent" />
   <line y2="76" x2="95" y1="50" x1="72" style="stroke:green;stroke-width:2"/>
<line x1="125" y1="23" x2="94" y2="76" style="stroke:green;stroke-width:2"/>
</svg> 
*/
// time page load
$time = microtime(true);
// page build functions
function _layout_header_($script = null){
	$pagelayout = '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>Fast File manager</title><script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script><link href="http://google-code-prettify.googlecode.com/svn/trunk/src/prettify.css" type="text/css" rel="stylesheet" /><script src="http://google-code-prettify.googlecode.com/svn/trunk/src/prettify.js"></script><script>'.$script.'</script><link href="'.$_SERVER["PHP_SELF"].'?r=style" rel="stylesheet"></head>';
	return $pagelayout;
}
function _layout_body_($content,$dir = null,$nav = null,$right_buttons = null,$notice = null,$error = null){
	if(isset($notice)){
		$extra = '<div class="notice">'.$notice.'</div>';
	}
	if(isset($error)){
		$extra = '<div class="error">'.$error.'</div>';
	}
	$pagelayout = '<body><div id="wrap"><h2>'.$dir.'</h2><div id="nav">'.$nav.'</div><form method="post">'.$extra.'<div id="content">'.$content.'<div id="right_nav">'.$right_buttons.'</div></div></form></div>';
	return $pagelayout;
}
function _layout_footer_($time){
	$footer = '<div id="footer">Time Elapsed: '.(microtime(true) - $time).'s</div></body></html>';
	return $footer;
}

// create the action buttons
function _buttons_($file = null){
if($file != null){
$buttons = '<button name="actions" value="save">Save File</button>';
$buttons .='<button id="rename">Rename File</button>';
$buttons .='<button id="createf">Create File</button>';
$buttons .='<button id="created">Create DIR</button>';
$buttons .='<button name="actions" value="delete">Delete</button>';
}
return $buttons;
}

// build the nav function
function _nav_($dir){
$nav = '<ul>';

if(!is_dir($dir)){
$dir = end(explode('=',$_SERVER[HTTP_REFERER]));
}
if ($handle = opendir($dir)) {
    while (false !== ($entry[] = readdir($handle)));
	sort($entry);
	foreach($entry as $key){
		if ($key == "..") {
			$pops = explode('/',$dir);
			$popscount =count($pops);
			$pops =array_slice( $pops,0, $popscount- ($popscount +1));
			$pops = implode('/',$pops);
			$nav .= '<li><a href="'.$_SERVER["PHP_SELF"].'?dir='.$pops.'">'.$key.'</a></li>'; 
		}
		if ($key != "." && $key != "" && $key != "..") {
			if(is_dir($dir.'/'.$key)){
			$nav .= '<li><a href="'.$_SERVER["PHP_SELF"].'?dir='.$dir.'/'.$key.'">'.$key.'</a></li>'; 
			}else{
			$nav .= '<li><a href="'.$_SERVER["PHP_SELF"].'?dir='.$dir.'&file='.$dir.'/'.$key.'">'.$key.'</a></li>'; 
			}
		}
	}
    closedir($handle);
}
$nav .='</ul>';
return $nav;
}

// Set Variables here.
$username = ""; // leave blank to keep open
$password = ""; // first time login will hash your password, leave blank.
$actions = $_POST['actions'];
$domain = $_SERVER["HTTP_HOST"];
$request = $_GET['r'];
$dir=$_GET['dir'];
$file=$_GET['file'];
$filetype = end(explode('.',$file));
$pfile = $_POST['fname'];
$docroot = $_SERVER["DOCUMENT_ROOT"];
$GLOBALS["dir"] = $dir;


if(isset($_POST['user'])){
$hashed_password = crypt($_POST['pass'],$_POST['user']);
if($_POST['user'] === $username && $password === ''){
$content = 'Your Usernamw is: '.$username;
$content .= '<br/>Your Password is: '.$hashed_password;
$content .= '<br/>Open the file manager and add the hashed password to the password variable.';
}
if($_POST['user'] === $username && $password === $hashed_password && $password != ''){
	setcookie('filemanager',$hashed_password,time()+3600*24*30,'/');
	header('location: '.$_SERVER["PHP_SELF"]);
}else{
	$content = 'Login Failed';
}
}
// cookie check and set for username and password.
if($_COOKIE['filemanager'] != $password && $username != ''){
$content .= '<form method="post">
<label>Username: </label>
<input type="text" placeholder="username" name="user"/>
<label>Password: </label>
<input type="password" placeholder="Password" name="pass"/>
<input type="submit" value="ENTER"/>
</form>';
echo _layout_header_();
echo _layout_body_($content);
echo _layout_footer_($time);
die();
}



$script = <<<END
function action_change(iname){
//window.event.preventDefault()
$(this).after('<input name="'+iname+'"/><button value="'+iname+'">'+iname+'</button>');
}
$(document).ready(function(){
$('#premissions').click(function(e){
e.preventDefault()
$(this).after('<input name="mode" size="4"/><button class="second" name="actions" value="permissions">Change</button>');
});
/* hold off on the line counting
	var codesHeight = $('#codes').height();
	var number = '<ol>';
	for (i=1; i<codesHeight; i++){
		number += "<li>";
	}
	number += "</ol>";
	$('#numbers').append(number);
	*/
});
END;

// this is something new that I am trying. 
// realy not sure if it has been done or if it will fail.

// image request
if($request === 'image'){
if($_GET['type'] === 'jpg'){
$_GET['type'] = 'jpeg';
}
header('Content-type: image/'.$_GET['type']);
include($_GET['image']);
die();
}

// script request
if($request === 'style'){
header('Content-type: text/css');
echo <<<END
body {font: 12px/16px monospace;margin:0; padding:0;}
* {box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;position: relative;}
h1, h2, h3, h4 {margin: 0;padding: 0;}
#wrap {}
#fileheader {height:50px;width:100%;}
#dir_list {width:80%;float:left;}
#dir_list a {border: 1px solid #DDD; display: inline-block;margin: 10px;padding: 10px 0;text-align: center;width: 150px;}
#dir_list a:hover {background:#eee;}
#fileheader span {padding:0 10px 0 0;}
#footer {width:100%;height:50px;float:left;}
#numbers {display: none;float: left;width: 25px;line-height: 18px;margin-top: 5px;}
#numbers ol {margin:0;}
#fimage {max-height:100%;max-width:100%;}
#code_box {float:left;width:80%;min-height:600px;}
#right_nav {width:17%;float:left;padding:0 0 0px 20px;}
#right_nav button {float:left;clear:both;width: 95%;margin-bottom: 5px;}
#right_nav .second {width:45%;background-color: #006DCC;float:none;margin-left:10px;border-radius: 4px;}
#nav {width: 20%;min-height:600px;float: left;}
#nav ul {margin:0 0 0 10px;padding:0;}
#nav li {list-style:none;}
#nav li a {background: none repeat scroll 0 0 #FFFFFF;
    border-width: 1px 1px 0 1px;
	border-color:#DDDDDD;
    border-style:solid;
	display: block;
    line-height: 18px;
	text-decoration: none;
    padding: 5px;}
#nav li:first-child > a {border-radius:7px 7px 0 0;}
#nav li:last-child > a {border-radius:0 0 7px 7px;border-width:1px;}

#nav li a:hover {background:#eee;}
#codes{position: absolute; top: 0;float:left;background:#F7F7F9;border-radius:7px;line-height: 18px;width: 100%;height: 100%;border: 1px solid #333; padding: 4px;}
h2{font-size:120%;}
#content {width: 80%;float: left;padding:10px;}
END;
die();
}

// button actions. should be a switch statement.
// save file
if($actions === 'save'){
$fh = fopen($pfile, 'w') or die("can't open file");
$stringData = stripslashes($_POST['content']);
fwrite($fh, $stringData);
fclose($fh);
header('location: '.$_SERVER["PHP_SELF"].'?dir='.$_POST['paths'].'&file='.$file);
}
// rename file
if($actions === 'rename'){

header('location: '.$_SERVER["PHP_SELF"].'?dir='.$_POST['paths'].'&file='.$file);
}
// delete file
if($actions === 'delete'){
header('location: '.$_SERVER["PHP_SELF"].'?dir='.$_POST['paths'].'&file='.$file);
}
// create file
if($actions === 'createf'){

header('location: '.$_SERVER["PHP_SELF"].'?dir='.$_POST['paths'].'&file='.$file);
}
// create dir
if($actions === 'created'){

header('location: '.$_SERVER["PHP_SELF"].'?dir='.$_POST['paths'].'&file='.$file);
}
// change file/DIR premissions
if($actions === 'premissions'){
if(!chmod($pfile,$_POST['mode'])){
$error = "&error=Permissions change failed";
}
header('location: '.$_SERVER["PHP_SELF"].'?dir='.$_POST['paths'].'&file='.$file.$error);
}

// page layout and nav building
if($dir == ''){ $dir = $docroot; }

// retuns file info and code/contnets.
function _files_($file,$filetype){
$filename = end(explode('/',$file));
$images = array("png", "gif", "jpg", "jpeg");
$filecontent = file_get_contents($file);
	if(is_writable($file)){
		$canwrite = "Is writeable";
	}else{
		$canwrite = "Is not writeable";
	}
	$fileperm = substr(sprintf("%o",fileperms($file)),-4);
	$filestats = '<div id="fileheader"><span>Name: '.$filename.'<input type="hidden" name="fname" value="'.$filename.'"/><input type="hidden" name="paths" value="'.$GLOBALS['dir'].'"/></span><span>Type: '.$filetype.'</span><span>Write Stats: '.$canwrite.'</span><span>Permissions: '.$fileperm.'</span><span>Size: '.filesize($file).'kb</span><span>Last Change: '.date("F d Y H:i:s.", fileatime($file)).'</span></div>';
	if(in_array($filetype, $images)){
	$filecontent = $filestats.'<div id="code_box"><div id="numbers"></div><img id="fimage" src="'.$_SERVER["PHP_SELF"].'?r=image&image='.$file.'&type='.$filetype.'"/></div>';
	}else{
	$filecontent = $filestats.'<div id="code_box"><div id="numbers"></div><textarea class="prettyprint linenums" name="content" id="codes">'.htmlentities($filecontent).'</textarea></div>';
	}
	return $filecontent;
}

function _dir_($dir){
	if(is_dir($dir)){
		$dircontent = '<div id="dir_list">';
		//get all files in specified directory
		$files = glob($dir . "/*");
		//print each file name
		foreach($files as $file){
			if(is_dir($file)){
			$dircontent .= '<a href="'.$_SERVER["PHP_SELF"].'?dir='.$file.'">'.end(explode('/',$file)).'</a>';
			}else{
			$dircontent .= '<a href="'.$_SERVER["PHP_SELF"].'?dir='.$dir.'&file='.$file.'">'.end(explode('/',$file)).'</a>';
			}
		}
		$dircontent .= '</div>';
	}
	return $dircontent;
}

if($file != ''){
$content = _files_($file,$filetype);
}else{
$content = _dir_($dir);
}
$right_buttons = _buttons_();
$nav = _nav_($dir);

// just used for php info
if($request === 'info'){
$content = phpinfo();
}
if(isset($_GET['error'])){
$error = $_GET['error'];
}else{
$error = null;
}
if(isset($_GET['notice'])){
$notice = $_GET['notice'];
}else{
$notice = null;
}
//display page
echo _layout_header_($script);
echo _layout_body_($content,$dir,$nav,$right_buttons,$notice,$error);
echo _layout_footer_($time);
?>