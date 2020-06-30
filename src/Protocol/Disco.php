<?php

/**
 * Copyright 2014 Fabian Grutschus. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are those
 * of the authors and should not be interpreted as representing official policies,
 * either expressed or implied, of the copyright holders.
 *
 * @author    Fabian Grutschus <f.grutschus@lubyte.de>
 * @copyright 2014 Fabian Grutschus. All rights reserved.
 * @license   BSD
 * @link      http://github.com/fabiang/xmpp
 */

namespace Fabiang\Xmpp\Protocol;

/**
 * Protocol setting for Xmpp.
 *
 * @package Xmpp\Protocol
 */
class Disco extends Protocol
{
    /**
     * Info discovery.
     */
    const INFO = 'info';

    /**
     * Items discovery.
     */
    const ITEMS = 'items';

    /**
     * Discovery type.
     *
     * @var string
     */
    protected $type = self::ITEMS;

    /**
     * Set disco receiver.
     *
     * @var string
     */
    protected $to;

    /**
     * Discovered identity.
     *
     * @var \stdClass
     */
    protected $identity = null;

    /**
     * Discovered items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Discovered features.
     *
     * @var array
     */
    protected $features = [];

    public function __construct($to = null, $type = null)
    {
        $this->setTo($to);
        if (null !== $type) {
            $this->setType($type);
        }
    }

    /**
     * Get message receiver.
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set message receiver.
     *
     * @param string $to
     * @return $this
     */
    public function setTo($to)
    {
        $this->to = (string) $to;
        return $this;
    }

    /**
     * Get discovery type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set dicovery type.
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = (string) $type;
        return $this;
    }

    /**
     * Add discovered item.
     *
     * @param \stdClass $item
     * @return \Fabiang\Xmpp\Protocol\Disco
     */
    public function addItem($item)
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * Get discovered items.
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Add discovered feature.
     *
     * @param string $feature
     * @return \Fabiang\Xmpp\Protocol\Disco
     */
    public function addFeature($feature)
    {
        $this->features[] = $feature;
        return $this;
    }

    /**
     * Get discovered features.
     *
     * @return array
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * Set discovered identity.
     *
     * @param \stdClass $identity
     * @return \Fabiang\Xmpp\Protocol\Disco
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
        return $this;
    }

    /**
     * Get discovered identity.
     *
     * @return stdClass
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * {@inheritDoc}
     */
    public function toString()
    {
        return '<iq to="' . $this->to . '" type="get" id="' . $this->getId() . '"><query xmlns="http://jabber.org/protocol/disco#' . $this->type . '"/></iq>';
    }
}
