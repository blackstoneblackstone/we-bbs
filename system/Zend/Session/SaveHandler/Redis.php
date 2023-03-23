<?php
/**
 * Copyright (c) 2011-2013, Carl Oscar Aaro
 * All rights reserved.
 *
 * New BSD License
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  * Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 *  * Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 *  * Neither the name of Carl Oscar Aaro nor the names of its
 *    contributors may be used to endorse or promote products derived from this
 *    software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 **/

/**
 * Redis cache backend for Zend Framework. Extends Zend_Cache_Backend
 * Supports tags and cleaning modes (except CLEANING_MODE_NOT_MATCHING_TAG)
 * Uses the PHP module phpredis by Nicolas Favre-Felix available at https://github.com/nicolasff/phpredis
 *
 * @category Zend
 * @author Carl Oscar Aaro <carloscar@agigen.se>
 */

/**
 * @see Zend_Cache_Backend_Interface
 */
//require_once 'Zend/Cache/Backend/ExtendedInterface.php';

/**
 * @see Zend_Cache_Backend
 */
//require_once 'Zend/Cache/Backend.php';

class Zend_Session_SaveHandler_Redis implements Zend_Session_SaveHandler_Interface
{
    /**
     * Default Values
     */
    const DEFAULT_HOST = '127.0.0.1';
    const DEFAULT_PORT =  6379;
    const DEFAULT_PERSISTENT = false;
    const DEFAULT_DBINDEX = 0;

    protected $_options = array(
        'servers' => array(
            array(
                'host' => self::DEFAULT_HOST,
                'port' => self::DEFAULT_PORT,
                'persistent' => self::DEFAULT_PERSISTENT,
                'dbindex' => self::DEFAULT_DBINDEX,
            ),
        ),
        'key_prefix' => 'session_',
    );

    /**
     * Redis object
     *
     * @var mixed redis object
     */
    protected $_redis = null;

    public function __construct(array $options = array())
    {
        if (!extension_loaded('redis')) {
            Zend_Cache::throwException('The redis extension must be loaded for using this backend !');
        }

        $this->_redis = new Redis;
        foreach ($this->_options['servers'] as $server) {
           $server = array_merge($server,$options);
            if (!array_key_exists('port', $server)) {
                $server['port'] = self::DEFAULT_PORT;
            }
            if (!array_key_exists('host', $server)) {
                $server['host'] = self::DEFAULT_HOST;
            }
            if (!array_key_exists('persistent', $server)) {
                $server['persistent'] = self::DEFAULT_PERSISTENT;
            }
            if (!array_key_exists('dbindex', $server)) {
                $server['dbindex'] = self::DEFAULT_DBINDEX;
            }
            if ($server['persistent']) {
                $result = $this->_redis->pconnect($server['host'], $server['port']);
            } else {
                $result = $this->_redis->connect($server['host'], $server['port']);
            }
            if($server['password'])
            {
                $result = $this->_redis->auth($server['password']);
            }
            if ($result)
                $this->_redis->select($server['dbindex']);
            else
                $this->_redis = null;
        }
    }

    /**
     * Returns status on if cache backend is connected to Redis
     *
     * @return bool true if cache backend is connected to Redis server.
     */
    public function isConnected()
    {
        if ($this->_redis)
            return true;
        return false;
    }


    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * @param string $id cache i
     * @return string|false cached datas
     */
    public function read($id)
    {
        if (!$this->_redis)
            return false;
        $result = $this->_redis->get($this->_keyFromId($id));
        return $result ? $result : serialize($result);
    }

    public function transactionBegin()
    {
        return $this->_redis->multi();
    }

    public function transactionEnd()
    {
        return $this->_redis->exec();
    }

    public function open($save_path,$name){
        return true;
    }

    public function write($id,$data, $tags = array(), $specificLifetime = false)
    {
        if (!$this->_redis)
            return false;

        $lifetime = $this->getLifetime($specificLifetime);

        if (!$tags || !count($tags))
            $tags = array('');
        if (is_string($tags))
            $tags = array($tags);

        if (!count($tags)) {
            $this->_redis->del($this->_keyFromItemTags($id));
            if ($lifetime === null) {
                $return = $this->_redis->set($this->_keyFromId($id), $data);
            } else {
                $return = $this->_redis->setex($this->_keyFromId($id), $lifetime, $data);
            }
            $this->_redis->sAdd($this->_keyFromItemTags($id), '');
            if ($lifetime !== null)
                $this->_redis->expire($this->_keyFromItemTags($id), $lifetime);
            else
                $redis = $this->_redis->persist($this->_keyFromItemTags($id));

            return $return;
        }

        $tagsTTL = array();
        foreach ($tags as $tag) {
            if ($tag) {
                if (!$this->_redis->exists($this->_keyFromTag($tag)))
                    $tagsTTL[$tag] = false;
                else
                    $tagsTTL[$tag] = $this->_redis->ttl($this->_keyFromTag($tag));
            }
        }

        $redis = $this->_redis->multi();
        $return = array();
        if (!$redis)
            $return[] = $this->_redis->del($this->_keyFromItemTags($id));
        else
            $redis = $redis->del($this->_keyFromItemTags($id));

        if ($lifetime === null) {
            if (!$redis)
                $return[] = $this->_redis->set($this->_keyFromId($id), $data);
            else
                $redis = $redis->set($this->_keyFromId($id), $data);
        } else {
            if (!$redis)
                $return[] = $this->_redis->setex($this->_keyFromId($id), $lifetime, $data);
            else
                $redis = $redis->setex($this->_keyFromId($id), $lifetime, $data);
        }

        $itemTags = array($this->_keyFromItemTags($id));
        foreach ($tags as $tag) {
            $itemTags[] = $tag;
            if ($tag) {
                if (!$redis)
                    $return[] = $this->_redis->sAdd($this->_keyFromTag($tag), $id);
                else
                    $redis = $redis->sAdd($this->_keyFromTag($tag), $id);

            }
        }
        if (count($itemTags) > 1) {
            if (!$redis)
                $return[] = call_user_func_array(array($this->_redis, 'sAdd'), $itemTags);
            else
                $redis = call_user_func_array(array($redis, 'sAdd'), $itemTags);
        }

        if ($lifetime !== null) {
            if (!$redis)
                $return[] = $this->_redis->expire($this->_keyFromItemTags($id), $lifetime);
            else
                $redis = $redis->expire($this->_keyFromItemTags($id), $lifetime);
        } else {
            if (!$redis)
                $return[] = $this->_redis->persist($this->_keyFromItemTags($id));
            else
                $redis = $redis->persist($this->_keyFromItemTags($id));
        }

        if ($redis)
            $return = $redis->exec();
        if (!count($return))
            return false;

        foreach ($tags as $tag) {
            if ($tag) {
                $ttl = $tagsTTL[$tag];
                if ($lifetime === null && $ttl !== false && $ttl != -1) {
                    $this->_redis->persist($this->_keyFromTag($tag));
                } else if ($lifetime !== null && ($ttl === false || ($ttl < $lifetime && $ttl != -1))) {
                    $this->_redis->expire($this->_keyFromTag($tag), $lifetime);
                }
            }
        }

        foreach ($return as $value) {
            if ($value === false)
                return false;
        }
        return true;
    }

    protected function _storeKey($data, $id, $specificLifetime = false)
    {
        if (!$this->_redis)
            return false;

        $lifetime = $this->getLifetime($specificLifetime);

        if ($lifetime === null) {
            return $this->_redis->set($this->_keyFromId($id), $data);
        } else {
            return $this->_redis->setex($this->_keyFromId($id), $lifetime, $data);
        }
    }

    /**
     * Prefixes key ID
     *
     * @param string $id cache key
     * @return string prefixed id
     */
    protected function _keyFromId($id)
    {
        return $this->_options['key_prefix'] . 'item__' . $id;
    }

    protected function _keyFromTag($id)
    {
        return $this->_options['key_prefix'] . 'tag__' . $id;
    }

    protected function _keyFromItemTags($id)
    {
        return $this->_options['key_prefix'] . 'item_tags__' . $id;
    }

    public function destroy($id, $hardReset = false)
    {
        if (!$this->_redis)
            return false;

        if (!$id)
            return false;
        if (is_string($id))
            $id = array($id);
        if (!count($id))
            return false;
        $deleteIds = array();
        foreach ($id as $i) {
            $deleteIds[] = $this->_keyFromItemTags($i);
            if ($hardReset)
                $deleteIds[] = $this->_keyFromId($i);
        }
        $this->_redis->del($deleteIds);

        return true;
    }

    public function gc($maxlifetime){
        return true;
    }

    public function existsInSet($member, $set)
    {
        if (!$this->_redis)
            return null;

        if (!$this->_redis->sIsMember($this->_keyFromId($set), $member))
            return false;
        return true;
    }

    public function close()
    {
        return true;
    }

    public function getIds()
    {
        Zend_Cache::throwException('Not possible to get available IDs on Redis cache');
    }

    public function getTags()
    {
        Zend_Cache::throwException('Not possible to get available tags on Redis cache');
    }

    public function getIdsMatchingTags($tags = array())
    {
        if (!$this->_redis)
            return array();

        if (!$tags)
            return array();
        if ($tags && is_string($tags))
            $tags = array($tags);

        $matchTags = array();
        foreach ($tags as $tag) {
            $matchTags[] = $this->_keyFromTag($tag);
        }
        if (count($matchTags) == 1)
            return $this->_redis->sMembers($matchTags[0]);

        return $this->_redis->sInter($matchTags);
    }

    public function getIdsNotMatchingTags($tags = array())
    {
        Zend_Cache::throwException('Not possible to get IDs not matching tags on Redis cache');
    }

    public function getIdsMatchingAnyTags($tags = array())
    {
        if (!$this->_redis)
            return array();

        if (!$tags)
            return array();
        if ($tags && is_string($tags))
            $tags = array($tags);

        $return = array();
        foreach ($tags as $tag) {
            foreach ($this->_redis->sMembers($this->_keyFromTag($tag)) as $id) {
                $return[] = $id;
            }
        }
        return $return;
    }

    public function getFillingPercentage()
    {
        Zend_Cache::throwException('getFillingPercentage not implemented on Redis cache');
    }

    public function getMetadatas($id)
    {
        Zend_Cache::throwException('Metadata not implemented on Redis cache');
    }

    public function touch($id, $extraLifetime)
    {
        if (!$this->_redis)
            return false;

        $tags = $this->_redis->sMembers($this->_keyFromItemTags($id));

        $lifetime = $this->getLifetime($extraLifetime);
        $return = false;
        if ($lifetime !== null) {
            $this->_redis->expire($this->_keyFromItemTags($id), $lifetime);
            $return = $this->_redis->expire($this->_keyFromId($id), $lifetime);
        } else {
            $this->_redis->persist($this->_keyFromItemTags($id));
            $return = $this->_redis->persist($this->_keyFromId($id));
        }

        if ($tags) {
            foreach ($tags as $tag) {
                if ($tag) {
                    $ttl = $this->_redis->ttl($this->_keyFromTag($tag));
                    if ($ttl !== false && $ttl !== -1 && $ttl < $lifetime && $lifetime !== null)
                        $this->_redis->expire($this->_keyFromTag($tag), $lifetime);
                    else if ($ttl !== false && $ttl !== -1 && $lifetime === null)
                        $this->_redis->persist($this->_keyFromTag($tag));
                }
            }
        }

        return $return;
    }

    public function getCapabilities()
    {
        return array(
            'automatic_cleaning' => true,
            'tags' => true,
            'expired_read' => false,
            'priority' => false,
            'infinite_lifetime' => true,
            'get_list' => false
        );
    }

    public function getLifetime($specificLifetime)
    {
        if ($specificLifetime === false) {
            return 60 * 60 * 24 * 30;
        }
        return $specificLifetime;
    }

}
