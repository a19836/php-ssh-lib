# PHP SSH Lib

> Original Repos:   
> - PHP SSH Lib: https://github.com/a19836/php-ssh-lib/   
> - Bloxtor: https://github.com/a19836/bloxtor/

## Overview

**PHP SSH Lib** is a PHP library that provides a high-level API for managing SSH connections and performing remote operations securely and efficiently.  
It supports multiple authentication methods and exposes a unified interface for executing commands, managing files, and creating SSH-based resources.

The library allows you to connect to remote servers using **password authentication**, **SSH key files**, or **SSH key strings**, with optional fingerprint verification to ensure server authenticity.

Once connected, you can:
- Execute remote shell commands
- Open interactive SSH shells
- Create SSH tunnels
- Access SFTP resources
- Transfer files between local and remote systems
- Manage remote files and directories
- Inspect remote file metadata
- Safely disconnect and clean up resources

Authentication keys can be loaded from files or dynamically generated from strings and stored temporarily, making the library suitable for both static configurations and dynamic environments.

This library is ideal for server automation, deployment tools, remote file management, and secure infrastructure operations directly from PHP.

To see a working example, open [index.php](index.php) on your server.

---

## Usage

```php
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
```

