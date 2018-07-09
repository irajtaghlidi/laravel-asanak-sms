# Asanak notifications channel for Laravel 5.3+



This package makes it easy to send notifications using [asanak.com](//asanak.com) with Laravel 5.3+.

* You can use Queue with Redis to speed up your application.

## Contents

- [Installation](#installation)
    - [Setting up the AsanakSms service](#setting-up-the-AsanakSms-service)
- [Usage](#usage)
    - [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Credits](#credits)
- [License](#license)


## Installation

You can install the package via composer:

```bash
composer require irajtaghlidi/laravel-asanak-sms
```

Then you must install the service provider:
```php
// config/app.php
'providers' => [
    ...
    NotificationChannels\AsanakSms\AsanakSmsServiceProvider::class,
],
```

### Setting up the AsanakSms service

Add your AsanakSms login, username/password and default sender number to your `config/services.php`:

```php
// config/services.php
...
'asanaksms' => [
    'username' => env('ASANAK_USERNAME'),
    'password' => env('ASANAK_PASSWORD'),
    'from'     => env('ASANAK_FROM'),
    'apiurl'   => env('ASANAK_APIURL'),
],
...
```

## Usage

You can use the channel in your `via()` method inside the notification:

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\AsanakSms\AsanakSmsMessage;
use NotificationChannels\AsanakSms\AsanakSmsChannel;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [AsanakSmsChannel::class];
    }

    public function toAsanakSms($notifiable)
    {
        return AsanakSmsMessage::create("Task #{$notifiable->id} is complete!");
    }
}
```


You can add a alias to `config/app.php` :

```php
// config/app.php
'aliases' => [
    ...
    'asanaksms' => NotificationChannels\AsanakSms\AsanakSmsChannel::class,
],
```
You can use alias instead of class name in `via()` method:

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\AsanakSms\AsanakSmsMessage;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return ['asanaksms'];
    }

    public function toAsanakSms($notifiable)
    {
        return AsanakSmsMessage::create("Task #{$notifiable->id} is complete!");
    }
}
```



In your notifiable model (for example App/User Model), make sure to include a `routeNotificationForAsanaksms()` method, which returns a phone number
or an array of phone numbers.

```php
public function routeNotificationForAsanaksms()
{
    return $this->phone;
}
```

### Available methods

`from()`: Sets the sender's phone number.

`content()`: Set a content of the notification message.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email irajtaghlidi@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Iraj Taghlidi](https://github.com/irajtaghlidi)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.