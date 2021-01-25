# stanx/fabiang-xmpp

Library for XMPP protocol connections (Jabber) for PHP.

Forked from https://github.com/tohenk/xmpp (forked from https://github.com/fabiang/xmpp)

## SYSTEM REQUIREMENTS

- PHP >= 7.0
- psr/log
- (optional) psr/log-implementation - like monolog/monolog for logging

## INSTALLATION

New to Composer? Read the [introduction](https://getcomposer.org/doc/00-intro.md#introduction). Add the following to your composer file:

```bash
composer require stanx/fabiang-xmpp
```

## DOCUMENTATION

This library uses an object to hold options:

```php
use Fabiang\Xmpp\Options;
$options = new Options($address);
$options->setUsername($username)
        ->setPassword($password)
        ->setTimeout(10);
```

The server address must be in the format `tcp://myjabber.com:5222`.  
If the server supports TLS the connection will automatically be encrypted.

You can also pass a PSR-2-compatible object to the options object:

```php
$options->setLogger($logger)
```

The client manages the connection to the Jabber server and requires the options object:

```php
use Fabiang\Xmpp\Client;
$client = new Client($options);
// optional connect manually
$client->connect();
```

For sending data you just need to pass a object that implements `Fabiang\Xmpp\Protocol\ProtocolImplementationInterface`:

```php
use Fabiang\Xmpp\Protocol\Roster;
use Fabiang\Xmpp\Protocol\Presence;
use Fabiang\Xmpp\Protocol\Message;

// fetch roster list; users and their groups
$client->send(new Roster);
// set status to online
$client->send(new Presence);

// send a message to another user
$message = new Message;
$message->setMessage('test')
        ->setTo('nickname@myjabber.com');
$client->send($message);

// join a channel
$channel = new Presence;
$channel->setTo('channelname@conference.myjabber.com')
        ->setPassword('channelpassword')
        ->setNickName('mynick');
$client->send($channel);

// send a message to the above channel
$message = new Message;
$message->setMessage('test')
        ->setTo('channelname@conference.myjabber.com')
        ->setType(Message::TYPE_GROUPCHAT);
$client->send($message);
```

After all you should disconnect:

```php
$client->disconnect();
```

BSD-2-Clause. See the [LICENSE](LICENSE.md).
