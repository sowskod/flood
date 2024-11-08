<?php

/**
 * This code was generated by
 * ___ _ _ _ _ _    _ ____    ____ ____ _    ____ ____ _  _ ____ ____ ____ ___ __   __
 *  |  | | | | |    | |  | __ |  | |__| | __ | __ |___ |\ | |___ |__/ |__|  | |  | |__/
 *  |  |_|_| | |___ | |__|    |__| |  | |    |__] |___ | \| |___ |  \ |  |  | |__| |  \
 *
 * Twilio - Marketplace
 * This is the public Twilio REST API.
 *
 * NOTE: This class is auto generated by OpenAPI Generator.
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */


namespace Twilio\Rest\Marketplace\V1;

use Twilio\Exceptions\TwilioException;
use Twilio\Options;
use Twilio\Values;
use Twilio\Version;
use Twilio\InstanceContext;


class ModuleDataManagementContext extends InstanceContext
    {
    /**
     * Initialize the ModuleDataManagementContext
     *
     * @param Version $version Version that contains the resource
     * @param string $sid The unique identifier of a Listing.
     */
    public function __construct(
        Version $version,
        $sid
    ) {
        parent::__construct($version);

        // Path Solution
        $this->solution = [
        'sid' =>
            $sid,
        ];

        $this->uri = '/Listing/' . \rawurlencode($sid)
        .'';
    }

    /**
     * Fetch the ModuleDataManagementInstance
     *
     * @return ModuleDataManagementInstance Fetched ModuleDataManagementInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function fetch(): ModuleDataManagementInstance
    {

        $headers = Values::of(['Content-Type' => 'application/x-www-form-urlencoded' ]);
        $payload = $this->version->fetch('GET', $this->uri, [], [], $headers);

        return new ModuleDataManagementInstance(
            $this->version,
            $payload,
            $this->solution['sid']
        );
    }


    /**
     * Update the ModuleDataManagementInstance
     *
     * @param array|Options $options Optional Arguments
     * @return ModuleDataManagementInstance Updated ModuleDataManagementInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function update(array $options = []): ModuleDataManagementInstance
    {

        $options = new Values($options);

        $data = Values::of([
            'ModuleInfo' =>
                $options['moduleInfo'],
            'Description' =>
                $options['description'],
            'Documentation' =>
                $options['documentation'],
            'Policies' =>
                $options['policies'],
            'Support' =>
                $options['support'],
            'Configuration' =>
                $options['configuration'],
        ]);

        $headers = Values::of(['Content-Type' => 'application/x-www-form-urlencoded' ]);
        $payload = $this->version->update('POST', $this->uri, [], $data, $headers);

        return new ModuleDataManagementInstance(
            $this->version,
            $payload,
            $this->solution['sid']
        );
    }


    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string
    {
        $context = [];
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Marketplace.V1.ModuleDataManagementContext ' . \implode(' ', $context) . ']';
    }
}