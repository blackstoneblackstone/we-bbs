<?php

$config['Memcache'] = array (
  'host' => '127.0.0.1',
  'port' => 11211,
  'persistent' => true,
  'timeout' => 60,
  'compression' => false,
  'compatibility' => false,
);
$config['Memcached'] = array (
  'host' => '127.0.0.1',
  'port' => 11211,
  'weight' => 1,
);
$config['Redis'] = array (
  'host' => '127.0.0.1',
  'port' => 6379,
  'timeout' => 0,
  'dbindex' => 1,
  'auth' => '',
);