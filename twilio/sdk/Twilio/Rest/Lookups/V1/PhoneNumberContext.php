<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Lookups\V1;

use Twilio\InstanceContext;
use Twilio\Options;
use Twilio\Serialize;
use Twilio\Values;
use Twilio\Version;

class PhoneNumberContext extends InstanceContext {
    /**
     * Initialize the PhoneNumberContext
     * 
     * @param \Twilio\Version $version Version that contains the resource
     * @param string $phoneNumber The phone_number
     * @return \Twilio\Rest\Lookups\V1\PhoneNumberContext 
     */
    public function __construct(Version $version, $phoneNumber) {
        parent::__construct($version);
        
        // Path Solution
        $this->solution = array(
            'phoneNumber' => $phoneNumber,
        );
        
        $this->uri = '/PhoneNumbers/' . $phoneNumber . '';
    }

    /**
     * Fetch a PhoneNumberInstance
     * 
     * @param array|Options $options Optional Arguments
     * @return PhoneNumberInstance Fetched PhoneNumberInstance
     */
    public function fetch($options = array()) {
        $options = new Values($options);
        
        $params = Values::of(array(
            'CountryCode' => $options['countryCode'],
            'Type' => $options['type'],
            'AddOns' => $options['addOns'],
        ));
        
        $params = array_merge($params, Serialize::prefixedCollapsibleMap($options['addOnsData'], 'AddOns'));
        $payload = $this->version->fetch(
            'GET',
            $this->uri,
            $params
        );
        
        return new PhoneNumberInstance(
            $this->version,
            $payload,
            $this->solution['phoneNumber']
        );
    }

    /**
     * Provide a friendly representation
     * 
     * @return string Machine friendly representation
     */
    public function __toString() {
        $context = array();
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Lookups.V1.PhoneNumberContext ' . implode(' ', $context) . ']';
    }
}