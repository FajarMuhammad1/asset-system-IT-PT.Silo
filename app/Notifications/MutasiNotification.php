<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\MutasiAsset;

class MutasiNotification extends Notification
{
    use Queueable;

    protected $mutasi;
    protected $title;
    protected $message;
    protected $link;

    public function __construct(MutasiAsset $mutasi, string $title, string $message, string $link)
    {
        $this->mutasi  = $mutasi;
        $this->title   = $title;
        $this->message = $message;
        $this->link    = $link;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'mutasi_id' => $this->mutasi->id,
            'title'     => $this->title,
            'judul'     => $this->title,
            'pesan'     => $this->message,
            'link'      => $this->link,
        ];
    }
}
