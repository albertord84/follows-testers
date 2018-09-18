<?php

define('ROOT_DIR', __DIR__ . '/..');

define('USERS_SOURCE', ROOT_DIR . '/etc/users.json');
define('LOGIN_TEST_DATA', ROOT_DIR . '/etc/login.test.json');
define('LOGIN_TEST_LOG', ROOT_DIR . '/log/login.test.log');

define('DIRECTS_PID_FILE', '/tmp/sender.pid');
define('DIRECTS_POOL_DIR', ROOT_DIR . '/var');

function time_str() {
  $d = date('j');
  return sprintf("%s %s %s", date('M'),
    strlen($d) === 2 ? $d : ' ' . $d,
    date('G:i:s'));
}

function is_cli()
{
  return (PHP_SAPI === 'cli' OR defined('STDIN'));
}

function directory_map($source_dir, $directory_depth = 0, $hidden = FALSE)
{
  if ($fp = @opendir($source_dir))
  {
    $filedata	= array();
    $new_depth	= $directory_depth - 1;
    $source_dir	= rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

    while (FALSE !== ($file = readdir($fp)))
    {
      // Remove '.', '..', and hidden files [optional]
      if ($file === '.' OR $file === '..' OR ($hidden === FALSE && $file[0] === '.'))
      {
        continue;
      }

      is_dir($source_dir.$file) && $file .= DIRECTORY_SEPARATOR;

      if (($directory_depth < 1 OR $new_depth > 0) && is_dir($source_dir.$file))
      {
        $filedata[$file] = directory_map($source_dir.$file, $new_depth, $hidden);
      }
      else
      {
        $filedata[] = $file;
      }
    }

    closedir($fp);
    return $filedata;
  }

  return FALSE;
}

function read_file($file)
{
  return @file_get_contents($file);
}

function write_file($path, $data, $mode = 'wb')
{
  if ( ! $fp = @fopen($path, $mode))
  {
    return FALSE;
  }

  flock($fp, LOCK_EX);

  for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result)
  {
    if (($result = fwrite($fp, substr($data, $written))) === FALSE)
    {
      break;
    }
  }

  flock($fp, LOCK_UN);
  fclose($fp);

  return is_int($result);
}