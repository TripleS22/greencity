<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceThresholdReached extends Notification implements ShouldQueue
{
    use Queueable;

    protected $unitCode;
    protected $currentKm;
    protected $targetKm;

    public function __construct($unitCode, $currentKm, $targetKm)
    {
        $this->unitCode = $unitCode;
        $this->currentKm = $currentKm;
        $this->targetKm = $targetKm;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Peringatan Service berkala - ' . $this->unitCode)
                    ->line('Unit ' . $this->unitCode . ' telah mencapai atau mendekati ambang batas service.')
                    ->line('KM Realtime: ' . number_format($this->currentKm, 0) . ' KM')
                    ->line('Target Service: ' . number_format($this->targetKm, 0) . ' KM')
                    ->action('Lihat Monitoring', url('/admin/monitoring-periodik-service'))
                    ->line('Harap segera melakukan penjadwalan service.');
    }
}
