<?php
/*
+--------------------------------------------------------------------------
|   WeCenter [#RELEASE_VERSION#]
|   ========================================
|   by WeCenter Software
|   © 2011 - 2014 WeCenter. All Rights Reserved
|   http://www.wecenter.com
|   ========================================
|   Support: WeCenter@qq.com
|
+---------------------------------------------------------------------------
*/

class core_db
{
	private $db;
	private $current_db_object;
	public function __construct()
	{
		if (load_class('core_config')->get('system')->debug)
		{
			$start_time = microtime(TRUE);
		}

		if (load_class('core_config')->get('database')->charset)
		{
			load_class('core_config')->get('database')->master['charset'] = load_class('core_config')->get('database')->charset;

			if (load_class('core_config')->get('database')->slave)
			{
				load_class('core_config')->get('database')->slave['charset'] = load_class('core_config')->get('database')->charset;
			}
		}

		$this->db['master'] = Zend_Db::factory(load_class('core_config')->get('database')->driver, load_class('core_config')->get('database')->master);
        try
		{
			$this->db['master']->query("SET sql_mode = ''");
		}
		catch (Exception $e)
		{
			throw new Zend_Exception('Can\'t connect master database: ' . $e->getMessage());
		}

		if (load_class('core_config')->get('system')->debug AND class_exists('AWS_APP', false))
		{
			AWS_APP::debug_log('database', (microtime(TRUE) - $start_time), 'Connect Master DB');
		}

		if (load_class('core_config')->get('database')->slave)
		{
			if (load_class('core_config')->get('system')->debug)
			{
				$start_time = microtime(TRUE);
			}

			$this->db['slave'] = Zend_Db::factory(load_class('core_config')->get('database')->driver, load_class('core_config')->get('database')->slave);
			try
			{
				$this->db['slave']->query("SET sql_mode = ''");
			}
			catch (Exception $e)
			{
				throw new Zend_Exception('Can\'t connect slave database: ' . $e->getMessage());
			}

			if (load_class('core_config')->get('system')->debug AND class_exists('AWS_APP', false))
			{
				AWS_APP::debug_log('database', (microtime(TRUE) - $start_time), 'Connect Slave DB');
			}
		}
		else
		{
			$this->db['slave'] =& $this->db['master'];
		}

		if (!defined('MYSQL_VERSION'))
		{
			define('MYSQL_VERSION', $this->db['master']->getServerVersion());
		}
		$this->setObject();
	}

	public function setObject($db_object_name = 'master')
	{
		if (isset($this->db[$db_object_name]))
		{
            Zend_Registry::set('dbAdapter', $this->db[$db_object_name]);
            $frontendOptions = array('automatic_serialization' => TRUE);
            if(get_setting('cache_type') == 'redis')
            {
                $backendName = 'Redis';
                $backendOptions = ['servers' =>AWS_APP::config()->get('cache')->Redis];
            }
            else if(get_setting('cache_type') == 'memcache')
            {
                $backendName = 'Memcache';
                $backendOptions = ['servers' =>AWS_APP::config()->get('cache')->Memcache];
            }
            else if(get_setting('cache_type') == 'memcached')
            {
                $backendName = 'Memcached';
                $backendOptions = ['servers' =>AWS_APP::config()->get('cache')->Memcached];
            }else
            {
                $backendName = 'File';
                $backendOptions = array(
                    'cache_dir' => PUB_PATH . 'cache/',
                    'hashed_directory_level' => 1,
                    'read_control_type' => 'adler32',
                    'file_name_prefix' => substr(md5(G_SECUKEY), 0, 6)
                );
            }
            $cache = Zend_Cache::factory(
                'Core',
                $backendName,
                $frontendOptions,
                $backendOptions
            );
            Zend_Db_Table_Abstract::setDefaultmetadataCache($cache);
            Zend_Db_Table_Abstract::setDefaultAdapter($this->db[$db_object_name]);
			$this->current_db_object = $db_object_name;
			return $this->db[$db_object_name];
		}
		throw new Zend_Exception('Can\'t find this db object: ' . $db_object_name);
	}
}