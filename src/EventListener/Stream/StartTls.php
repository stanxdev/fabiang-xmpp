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
use Fabiang\Xmpp\Connection\SocketConnectionInterface;
use Fabiang\Xmpp\Exception\Stream\StreamErrorException;
use Fabiang\Xmpp\Exception\SocketException;

/**
 * Listener
 *
 * @package Xmpp\EventListener
 */
class StartTls extends AbstractEventListener implements BlockingEventListenerInterface
{

    /**
     * Listener blocks stream.
     *
     * @var boolean
     */
    protected $blocking = false;

    /**
     * {@inheritDoc}
     */
    public function attachEvents()
    {
        $input = $this->getInputEventManager();
        $input->attach('{urn:ietf:params:xml:ns:xmpp-tls}starttls', [$this, 'starttlsEvent']);
        $input->attach('{urn:ietf:params:xml:ns:xmpp-tls}proceed', [$this, 'proceed']);
        $input->attach('{urn:ietf:params:xml:ns:xmpp-tls}failure', [$this, 'failure']);
    }

    /**
     * Send start tls command.
     *
     * @param XMLEvent $event XMLEvent object
     */
    public function starttlsEvent(XMLEvent $event)
    {
        $connection = $this->getConnection();
        if (!$connection->getOptions()->isAuthenticated() && false === $event->isStartTag()) {
            $this->blocking = true;

            $connection->setReady(false);
            $connection->send('<starttls xmlns="urn:ietf:params:xml:ns:xmpp-tls"/>');
        }
    }

    /**
     * Start TLS response.
     *
     * @param XMLEvent $event XMLEvent object
     * @return void
     */
    public function proceed(XMLEvent $event)
    {
        if (false === $event->isStartTag()) {
            $this->blocking = false;

            $res = null;
            $connection = $this->getConnection();
            if ($connection instanceof SocketConnectionInterface) {
                foreach ([STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT] as $crypt) {
                    $res = $connection->getSocket()->crypto(true, $crypt);
                    $params = stream_context_get_params($connection->getSocket()->getResource());
                    $connection->log('Context parameters "'.var_export($params, true).'".');
                    if (false === $res) {
                        throw new SocketException('Unable to activate secure connection to "'.$connection->getSocket()->getAddress().'" using "'.$this->getCryptName($crypt).'".');
                    } else {
                        break;
                    }
                }
            }
            $connection->resetStreams();
            $connection->connect();
        }
    }

    /**
     * TLS failed.
     *
     * @param XMLEvent $event
     * @throws StreamErrorException
     */
    public function failure(XMLEvent $event)
    {
        if (false === $event->isStartTag()) {
            $this->blocking = false;
            throw StreamErrorException::createFromEvent($event);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isBlocking()
    {
        return $this->blocking;
    }

    protected function getCryptName($crypt)
    {
        switch ($crypt) {
            case STREAM_CRYPTO_METHOD_ANY_CLIENT:
                return 'STREAM_CRYPTO_METHOD_ANY_CLIENT';
            case STREAM_CRYPTO_METHOD_SSLv2_CLIENT:
                return 'STREAM_CRYPTO_METHOD_SSLv2_CLIENT';
            case STREAM_CRYPTO_METHOD_SSLv3_CLIENT:
                return 'STREAM_CRYPTO_METHOD_SSLv3_CLIENT';
            case STREAM_CRYPTO_METHOD_SSLv23_CLIENT:
                return 'STREAM_CRYPTO_METHOD_SSLv23_CLIENT';
            case STREAM_CRYPTO_METHOD_TLS_CLIENT:
                return 'STREAM_CRYPTO_METHOD_TLS_CLIENT';
            case STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT:
                return 'STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT';
            case STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT:
                return 'STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT';
            case STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT:
                return 'STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT';
            case STREAM_CRYPTO_METHOD_ANY_SERVER:
                return 'STREAM_CRYPTO_METHOD_ANY_SERVER';
            case STREAM_CRYPTO_METHOD_SSLv2_SERVER:
                return 'STREAM_CRYPTO_METHOD_SSLv2_SERVER';
            case STREAM_CRYPTO_METHOD_SSLv3_SERVER:
                return 'STREAM_CRYPTO_METHOD_SSLv3_SERVER';
            case STREAM_CRYPTO_METHOD_SSLv23_SERVER:
                return 'STREAM_CRYPTO_METHOD_SSLv23_SERVER';
            case STREAM_CRYPTO_METHOD_TLS_SERVER:
                return 'STREAM_CRYPTO_METHOD_TLS_SERVER';
            case STREAM_CRYPTO_METHOD_TLSv1_0_SERVER:
                return 'STREAM_CRYPTO_METHOD_TLSv1_0_SERVER';
            case STREAM_CRYPTO_METHOD_TLSv1_1_SERVER:
                return 'STREAM_CRYPTO_METHOD_TLSv1_1_SERVER';
            case STREAM_CRYPTO_METHOD_TLSv1_2_SERVER:
                return 'STREAM_CRYPTO_METHOD_TLSv1_2_SERVER';
        }
    }
}
