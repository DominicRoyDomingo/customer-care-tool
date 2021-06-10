<?php

namespace App\Notifications\Backend\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InviteUser extends Notification
{
    use Queueable;

    /**
     * @var
     */
    protected $confirmation_code;
    protected $organization;
    protected $organization_exec_code;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($organization, $confirmation_code, $organization_exec_code)
    {
        $this->confirmation_code        = $confirmation_code;
        $this->organization             = $organization;
        $this->organization_exec_code   = $organization_exec_code;
        // dd($this->id);
        // dd($this->organization);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('You are invited in an organization.')
                    ->action('Accept', route('admin.auth.user.invite.confirm',  ['organization' => $this->organization->id, 'token' => $this->confirmation_code, 'organization_exec_code' => $this->organization_exec_code, 'notif_id' => $this->id]));
    }

    public function toDatabase()
    {
        return [
            'organization_name' => $this->organization->name,
            'link' => route('admin.auth.user.invite.confirm',  ['organization' => $this->organization->id, 'token' => $this->confirmation_code, 'organization_exec_code' => $this->organization_exec_code, 'notif_id' => $this->id]),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
