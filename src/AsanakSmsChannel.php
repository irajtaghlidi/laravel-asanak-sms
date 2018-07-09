<?php

namespace NotificationChannels\AsanakSms;

use Illuminate\Notifications\Notification;
use NotificationChannels\AsanakSms\Exceptions\CouldNotSendNotification;
use NotificationChannels\AsanakSms\AsanakSmsApi;
use NotificationChannels\AsanakSms\AsanakSmsMessage;


class AsanakSmsChannel
{
    /** @var \NotificationChannels\AsanakSms\AsanakSmsApi */
    protected $smsc;

    public function __construct(AsanakSmsApi $smsc)
    {
        $this->smsc = $smsc;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     *
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! ($to = $this->getRecipients($notifiable, $notification))) {
            return;
        }

        $message = $notification->toAsanakSms($notifiable);

        if (\is_string($message)) {
            $message = new AsanakSmsMessage($message);
        }

        $this->sendMessage($to, $message);
    }

    /**
     * Gets a list of phones from the given notifiable.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     *
     * @return string[]
     */
    protected function getRecipients($notifiable, Notification $notification)
    {
        $to = $notifiable->routeNotificationFor('asanaksms', $notification);

        if ($to === null || $to === false || $to === '') {
            return [];
        }

        return is_array($to) ? $to : [$to];
    }

    protected function sendMessage($recipients, AsanakSmsMessage $message)
    {
        if (\mb_strlen($message->content) > 800) {
            throw CouldNotSendNotification::contentLengthLimitExceeded();
        }

        $params = [
            'destination'  => \implode(',', $recipients),
            'message'     => $message->content,
            'source'  => $message->from,
        ];

        // if ($message->sendAt instanceof \DateTimeInterface) {
        //     $params['time'] = '0'.$message->sendAt->getTimestamp();
        // }

        $this->smsc->send($params);
    }
}
