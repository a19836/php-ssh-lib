<?php
/*
 * Copyright (c) 2025 Bloxtor (http://bloxtor.com) and Joao Pinto (http://jplpinto.com)
 * 
 * Multi-licensed: BSD 3-Clause | Apache 2.0 | GNU LGPL v3 | HLNC License (http://bloxtor.com/LICENSE_HLNC.md)
 * Choose one license that best fits your needs.
 *
 * Original PHP SSH Lib Repo: https://github.com/a19836/phpsshlib/
 * Original Bloxtor Repo: https://github.com/a19836/bloxtor
 *
 * YOU ARE NOT AUTHORIZED TO MODIFY OR REMOVE ANY PART OF THIS NOTICE!
 */
?>
<style>
h1 {margin-bottom:0; text-align:center;}
h5 {font-size:1em; margin:40px 0 10px; font-weight:bold;}
p {margin:0 0 20px; text-align:center;}

.note {text-align:center;}
.note span {text-align:center; margin:0 20px 20px; padding:10px; color:#aaa; border:1px solid #ccc; background:#eee; display:inline-block; border-radius:3px;}
.note li {margin-bottom:5px;}

.code {display:block; margin:10px 0; padding:0; background:#eee; border:1px solid #ccc; border-radius:3px; position:relative;}
.code:before {content:"php"; position:absolute; top:5px; left:5px; display:block; font-size:80%; opacity:.5;}
.code textarea {width:100%; height:300px; padding:30px 10px 10px; display:inline-block; background:transparent; border:0; resize:vertical; font-family:monospace;}
</style>
<h1>PHP SSH Lib</h1>
<p>Handle SSH connections</p>
<div class="note">
		<span>
		This library provides a high-level API for managing SSH connections and performing remote operations securely and efficiently.<br/>
		It supports multiple authentication methods and exposes a unified interface for executing commands, managing files, and creating SSH-based resources.<br/>
		<br/>
		The library allows you to connect to remote servers using <b>password authentication</b>, <b>SSH key files</b>, or <b>SSH key strings</b>, with optional fingerprint verification to ensure server authenticity.<br/>
		<br/>
		Once connected, you can:<br/>
		<ul style="display:inline-block; text-align:left;">
			<li>Execute remote shell commands</li>
			<li>Open interactive SSH shells</li>
			<li>Create SSH tunnels</li>
			<li>Access SFTP resources</li>
			<li>Transfer files between local and remote systems</li>
			<li>Manage remote files and directories</li>
			<li>Inspect remote file metadata</li>
			<li>Safely disconnect and clean up resources</li>
		</ul>
		<br/>
		Authentication keys can be loaded from files or dynamically generated from strings and stored temporarily, making the library suitable for both static configurations and dynamic environments.<br/>
		<br/>
		This library is ideal for server automation, deployment tools, remote file management, and secure infrastructure operations directly from PHP.
		</span>
</div>
<div style="text-align:center;">
	<h3>
		<a href="examples/" target="examples">Click here to check an example</a>
	</h3>
</div>

<div>
	<h5>Usage</h5>
	<div class="code">
		<textarea readonly>
include_once __DIR__ . "/lib/SSHHandler.php";

$SSHHandler = new SSHHandler();
$SSHHandler->setSSHAuthKeyTmpFolderPath($tmp_folder);

$ssh_settings = array(
	"host" => "shell.example.com",
	"port" => "22",
	"username" => "test",
	"fingerprint" => "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx", //fingerprint verification to ensure server authenticity
);

$authentication_type = $_GET["authentication_type"] || "password";

switch ($authentication_type) {
	case "key_files":
		$ssh_settings["ssh_auth_pub_file"] = "RSA public file path";
		$ssh_settings["ssh_auth_priv_file"] = "RSA private file path";
		$ssh_settings["ssh_auth_passphrase"] = "my passphrase when created the priv RSA";
		break;
	
	case "key_strings":
		$ssh_settings["ssh_auth_pub_string"] = "RSA public file string";
		$ssh_settings["ssh_auth_priv_string"] = "RSA private file string";
		$ssh_settings["ssh_auth_passphrase"] = "my passphrase when created the priv RSA";
		break;
	
	default:
		$ssh_settings["password"] = isset($server_properties["password"]) ? $server_properties["password"] : null;
}

$connected = $SSHHandler->connect($ssh_settings);
//$connected = $SSHHandler->isConnected();

if ($connected) {
	//resource methods
	$ssh_resource = $SSHHandler->getConnection();
	$sftp_resource = $SSHHandler->sshToSftp();
	$stream_resource = $SSHHandler->sshToShell($term_type = "vanilla", $env = null, $width = 80, $height = 25, $width_height_type = SSH2_TERM_UNIT_CHARS); //$SSHHandler->sshToShell("xterm");
	$tunnel_resource = $SSHHandler->sshToTunnel($host, $port);
	
	//auth key methods
	$auth_key_file_path = $SSHHandler->createSSHAuthKeyFile($string); //creates a auth file inside of the ssh_auth_key_tmp_folder if defined, otherwise at sys_get_temp_dir().
	
	//remote file methods
	$exists = $SSHHandler->exists($remote_file_path);
	$info = $SSHHandler->getFileInfo($remote_file_path);
	$status = $SSHHandler->renameRemoteFile($remote_file_path, $new_name);
	$status = $SSHHandler->moveRemoteFile($remote_file_src_path, $remote_file_dst_path);
	$status = $SSHHandler->removeRemoteFile($remote_file_path);
	$status = $SSHHandler->createRemoteFolder($remote_folder_path, $mode = 0777, $create_parents = false);
	$status = $SSHHandler->isDir($remote_folder_path);
	$sub_files = $SSHHandler->scanRemoteDir($remote_folder_path);
	
	//remote commands
	$response = $SSHHandler->exec($cmd); //$cmd = "ls -l $remote_folder_path"
	
	//local file methods
	$status = $SSHHandler->copyLocalToRemoteFile($local_file_path, $remote_file_path, $create_parents = false, $file_create_mode = 0644, $folder_create_mode = 0755);
	$status = $SSHHandler->copyRemoteToLocalFile($remote_file_path, $local_file_path, $create_parents = false, $file_create_mode = 0644, $folder_create_mode = 0755);
}

//disconnect methods
$SSHHandler->disconnect(); //call disconnect even if not connected
		</textarea>
	</div>
</div>

