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

namespace Fabiang\Xmpp\EventListener\Stream;

use Fabiang\Xmpp\Event\XMLEvent;
use Fabiang\Xmpp\EventListener\AbstractEventListener;
use Fabiang\Xmpp\EventListener\BlockingEventListenerInterface;
use Fabiang\Xmpp\Protocol\Presence as PresenceProtocol;

/**
 * Listener
 *
 * @package Xmpp\EventListener
 */
class Presence extends AbstractEventListener implements BlockingEventListenerInterface
{
    /**
     * Blocking.
     *
     * @var boolean
     */
    protected $blocking = false;

    /**
     * {@inheritDoc}
     */
    public function attachEvents()
    {
        $output = $this->getOutputEventManager();
        $output->attach('{jabber:client}presence',  [$this, 'query']);
        $input =  $this->getInputEventManager();
        $input->attach('{jabber:client}presence',  [$this, 'result']);
    }

    /**
     * Sending a query request for roster sets listener to blocking mode.
     *
     * @return void
     */
    public function query()
    {
        $this->blocking = true;
    }

    /**
     * Result received.
     *
     * @param \Fabiang\Xmpp\Event\XMLEvent $event
     * @return void
     */
    public function result(XMLEvent $event)
    {
        if ($event->isEndTag()) {
            /** @var \DOMElement $element */
            $element = $event->getParameter(0);
            /** @var PresenceProtocol $protocol */
            if ($protocol = $this->getOptions()->getLastSentProtocol(PresenceProtocol::class)) {
                if ($element->hasAttribute('type') && PresenceProtocol::TYPE_ERROR == $element->getAttribute('type')) {
                    foreach ($element->childNodes as $node) {
                        if ('error' == $node->nodeName) {
                            $protocol->setResult($node->nodeValue);
                            break;
                        }
                    }
                }
            }

            $this->blocking = false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isBlocking()
    {
        return $this->blocking;
    }
}
