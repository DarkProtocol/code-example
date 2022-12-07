<?php

declare(strict_types=1);

namespace App\Domains\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Channels\MailChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $confirmationLink,
        protected string $lang
    ) {
    }

    /**
     * Get the notification channels.
     *
     * @param mixed $notifiable
     * @return string[]
     */
    public function via($notifiable)
    {
        return [
            MailChannel::class,
        ];
    }

    /**
     * Determine which queues should be used for each notification channel.
     *
     * @return array<string, string>
     */
    public function viaQueues()
    {
        return [
            MailChannel::class => 'notifications',
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->view('mails.auth.email-confirmation', [
                'confirmationLink' => $this->confirmationLink,
                'lang' => $this->lang,
            ])
            ->subject(__('mails.email-confirmation.subject', [], $this->lang));
    }
}
