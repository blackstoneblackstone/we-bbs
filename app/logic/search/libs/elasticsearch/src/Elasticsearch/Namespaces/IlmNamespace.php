<?php
/**
 * Elasticsearch PHP client
 *
 * @link      https://github.com/elastic/elasticsearch-php/
 * @copyright Copyright (c) Elasticsearch B.V (https://www.elastic.co)
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license   https://www.gnu.org/licenses/lgpl-2.1.html GNU Lesser General Public License, Version 2.1 
 * 
 * Licensed to Elasticsearch B.V under one or more agreements.
 * Elasticsearch B.V licenses this file to you under the Apache 2.0 License or
 * the GNU Lesser General Public License, Version 2.1, at your option.
 * See the LICENSE file in the project root for more information.
 */
declare(strict_types = 1);

namespace Elasticsearch\Namespaces;

use Elasticsearch\Namespaces\AbstractNamespace;

/**
 * Class IlmNamespace
 *
 * NOTE: this file is autogenerated using util/GenerateEndpoints.php
 * and Elasticsearch 7.15.0-SNAPSHOT (9fb2eb1c5228090f825b0a28287b80a0e446b2a8)
 */
class IlmNamespace extends AbstractNamespace
{

    /**
     * Deletes the specified lifecycle policy definition. A currently used policy cannot be deleted.
     *
     * $params['policy'] = (string) The name of the index lifecycle policy
     *
     * @param array $params Associative array of parameters
     * @return array
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/ilm-delete-lifecycle.html
     */
    public function deleteLifecycle(array $params = [])
    {
        $policy = $this->extractArgument($params, 'policy');

        $endpointBuilder = $this->endpoints;
        $endpoint = $endpointBuilder('Ilm\DeleteLifecycle');
        $endpoint->setParams($params);
        $endpoint->setPolicy($policy);

        return $this->performRequest($endpoint);
    }
    /**
     * Retrieves information about the index's current lifecycle state, such as the currently executing phase, action, and step.
     *
     * $params['index']        = (string) The name of the index to explain
     * $params['only_managed'] = (boolean) filters the indices included in the response to ones managed by ILM
     * $params['only_errors']  = (boolean) filters the indices included in the response to ones in an ILM error state, implies only_managed
     *
     * @param array $params Associative array of parameters
     * @return array
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/ilm-explain-lifecycle.html
     */
    public function explainLifecycle(array $params = [])
    {
        $index = $this->extractArgument($params, 'index');

        $endpointBuilder = $this->endpoints;
        $endpoint = $endpointBuilder('Ilm\ExplainLifecycle');
        $endpoint->setParams($params);
        $endpoint->setIndex($index);

        return $this->performRequest($endpoint);
    }
    /**
     * Returns the specified policy definition. Includes the policy version and last modified date.
     *
     * $params['policy'] = (string) The name of the index lifecycle policy
     *
     * @param array $params Associative array of parameters
     * @return array
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/ilm-get-lifecycle.html
     */
    public function getLifecycle(array $params = [])
    {
        $policy = $this->extractArgument($params, 'policy');

        $endpointBuilder = $this->endpoints;
        $endpoint = $endpointBuilder('Ilm\GetLifecycle');
        $endpoint->setParams($params);
        $endpoint->setPolicy($policy);

        return $this->performRequest($endpoint);
    }
    /**
     * Retrieves the current index lifecycle management (ILM) status.
     *
     *
     * @param array $params Associative array of parameters
     * @return array
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/ilm-get-status.html
     */
    public function getStatus(array $params = [])
    {

        $endpointBuilder = $this->endpoints;
        $endpoint = $endpointBuilder('Ilm\GetStatus');
        $endpoint->setParams($params);

        return $this->performRequest($endpoint);
    }
    /**
     * Migrates the indices and ILM policies away from custom node attribute allocation routing to data tiers routing
     *
     * $params['dry_run'] = (boolean) If set to true it will simulate the migration, providing a way to retrieve the ILM policies and indices that need to be migrated. The default is false
     * $params['body']    = (array) Optionally specify a legacy index template name to delete and optionally specify a node attribute name used for index shard routing (defaults to "data")
     *
     * @param array $params Associative array of parameters
     * @return array
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/ilm-migrate-to-data-tiers.html
     */
    public function migrateToDataTiers(array $params = [])
    {
        $body = $this->extractArgument($params, 'body');

        $endpointBuilder = $this->endpoints;
        $endpoint = $endpointBuilder('Ilm\MigrateToDataTiers');
        $endpoint->setParams($params);
        $endpoint->setBody($body);

        return $this->performRequest($endpoint);
    }
    /**
     * Manually moves an index into the specified step and executes that step.
     *
     * $params['index'] = (string) The name of the index whose lifecycle step is to change
     * $params['body']  = (array) The new lifecycle step to move to
     *
     * @param array $params Associative array of parameters
     * @return array
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/ilm-move-to-step.html
     */
    public function moveToStep(array $params = [])
    {
        $index = $this->extractArgument($params, 'index');
        $body = $this->extractArgument($params, 'body');

        $endpointBuilder = $this->endpoints;
        $endpoint = $endpointBuilder('Ilm\MoveToStep');
        $endpoint->setParams($params);
        $endpoint->setIndex($index);
        $endpoint->setBody($body);

        return $this->performRequest($endpoint);
    }
    /**
     * Creates a lifecycle policy
     *
     * $params['policy'] = (string) The name of the index lifecycle policy
     * $params['body']   = (array) The lifecycle policy definition to register
     *
     * @param array $params Associative array of parameters
     * @return array
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/ilm-put-lifecycle.html
     */
    public function putLifecycle(array $params = [])
    {
        $policy = $this->extractArgument($params, 'policy');
        $body = $this->extractArgument($params, 'body');

        $endpointBuilder = $this->endpoints;
        $endpoint = $endpointBuilder('Ilm\PutLifecycle');
        $endpoint->setParams($params);
        $endpoint->setPolicy($policy);
        $endpoint->setBody($body);

        return $this->performRequest($endpoint);
    }
    /**
     * Removes the assigned lifecycle policy and stops managing the specified index
     *
     * $params['index'] = (string) The name of the index to remove policy on
     *
     * @param array $params Associative array of parameters
     * @return array
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/ilm-remove-policy.html
     */
    public function removePolicy(array $params = [])
    {
        $index = $this->extractArgument($params, 'index');

        $endpointBuilder = $this->endpoints;
        $endpoint = $endpointBuilder('Ilm\RemovePolicy');
        $endpoint->setParams($params);
        $endpoint->setIndex($index);

        return $this->performRequest($endpoint);
    }
    /**
     * Retries executing the policy for an index that is in the ERROR step.
     *
     * $params['index'] = (string) The name of the indices (comma-separated) whose failed lifecycle step is to be retry
     *
     * @param array $params Associative array of parameters
     * @return array
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/ilm-retry-policy.html
     */
    public function retry(array $params = [])
    {
        $index = $this->extractArgument($params, 'index');

        $endpointBuilder = $this->endpoints;
        $endpoint = $endpointBuilder('Ilm\Retry');
        $endpoint->setParams($params);
        $endpoint->setIndex($index);

        return $this->performRequest($endpoint);
    }
    /**
     * Start the index lifecycle management (ILM) plugin.
     *
     *
     * @param array $params Associative array of parameters
     * @return array
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/ilm-start.html
     */
    public function start(array $params = [])
    {

        $endpointBuilder = $this->endpoints;
        $endpoint = $endpointBuilder('Ilm\Start');
        $endpoint->setParams($params);

        return $this->performRequest($endpoint);
    }
    /**
     * Halts all lifecycle management operations and stops the index lifecycle management (ILM) plugin
     *
     *
     * @param array $params Associative array of parameters
     * @return array
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/ilm-stop.html
     */
    public function stop(array $params = [])
    {

        $endpointBuilder = $this->endpoints;
        $endpoint = $endpointBuilder('Ilm\Stop');
        $endpoint->setParams($params);

        return $this->performRequest($endpoint);
    }
}
