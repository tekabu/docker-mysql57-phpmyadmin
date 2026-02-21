<?php
declare(strict_types=1);

// Generate a strong random secret: openssl rand -base64 32
$cfg['blowfish_secret'] = 'REPLACE_WITH_32+_RANDOM_CHARS_HERE';

$i = 0;
$i++;
$cfg['Servers'][$i]['auth_type']       = 'cookie';
$cfg['Servers'][$i]['host']            = 'mysql';
$cfg['Servers'][$i]['port']            = 3306;
$cfg['Servers'][$i]['connect_type']    = 'tcp';
$cfg['Servers'][$i]['compress']        = false;
$cfg['Servers'][$i]['AllowNoPassword'] = false;

$cfg['UploadDir'] = '';
$cfg['SaveDir']   = '';
$cfg['TempDir']   = '/tmp';
