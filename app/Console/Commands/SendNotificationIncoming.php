<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\IncomingExpiredNotificationEmail;
use App\Services\QontakSevices;
use App\Models\IncomingShipments;
use App\Models\Notifications;
use App\Models\User;
use Carbon\Carbon;

class SendNotificationIncoming extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-notification-incoming';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will send notification for incoming shipments';

    /**
     *  @var QontakSevices
     */

    protected $qontakServices;

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct(QontakSevices $qontakServices)
    {
        parent::__construct();
        $this->qontakServices = $qontakServices;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $intervals = [30, 21, 14, 7, 3, 0];
            $this->incomingsNotifications($intervals);
            $this->info('Notifications sent successfully');
        } catch (\Exception $e) {
            Log::error('Vehicle Notification Error: ' . $e->getMessage());
            $this->error('Failed to send notifications: ' . $e->getMessage());
        }
    }

    private function incomingsNotifications($intervals)
    {
        foreach ($intervals as $interval) {
            $targetDate = now()->addDays($interval);
            $incomings = $this->getIncomingsByInterval($targetDate);

            if ($incomings->isEmpty()) {
                continue;
            }

            foreach ($incomings as $incoming) {
                $this->processIncomingNotifications($incoming, $interval);
            }
        }
    }

    private function getIncomingsByInterval($targetDate)
    {
        return IncomingShipments::with(['item', 'drop', 'branch'])
            ->whereDate('eta', '=', $targetDate)
            ->get();
    }

    public function getContactsForIncoming($incoming)
    {
        $contacts = [];

        $notifications = Notifications::where('name', 'like', '%incoming%')->first();

        if (!$notifications) {
            return $contacts;
        }

        $template = $notifications->template;
        $roles = json_decode($notifications->roles, true) ?? [];

        $users = User::with('employe', 'roles')
            ->whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('name', $roles);
            })->get();

        foreach ($users as $user) {
            // Jika user punya nomor HP
            if ($user->employe && !empty($user->employe->phone)) {
                $contacts[] = [
                    'type' => 'whatsapp',
                    'contact' => $user->employe->phone,
                    'template' => $template  // Gunakan template yang sama
                ];
            }

            // Jika user punya email
            if (!empty($user->email)) {
                $contacts[] = [
                    'type' => 'email',
                    'contact' => $user->email
                ];
            }
        }

        // branch
        if ($incoming->branch) {
            if (!empty($incoming->branch->phone)) {
                $contacts[] = [
                    'type' => 'whatsapp',
                    'contact' => $incoming->branch->phone,
                    'template' => $template
                ];
            }
        }

        // branch
        if ($incoming->branch) {
            if (!empty($incoming->branch->email)) {
                $contacts[] = [
                    'type' => 'email',
                    'contact' => $incoming->branch->email,
                ];
            }
        }

        // warehouse
        if ($incoming->drop) {
            if (!empty($incoming->drop->phone)) {
                $contacts[] = [
                    'type' => 'whatsapp',
                    'contact' => $incoming->drop->phone ?? '',
                    'template' => $template
                ];
            }
        }

        // warehouse
        if ($incoming->drop) {
            if (!empty($incoming->drop->email)) {
                $contacts[] = [
                    'type' => 'email',
                    'contact' => $incoming->drop->email ?? '',
                ];
            }
        }

        // project
        if (!empty($incoming->phone)) {
            $contacts[] = [
                'type' => 'whatsapp',
                'contact' => $incoming->phone_drop_site ?? '',
                'template' => $template
            ];
        }

        // project
        if (!empty($incoming->email)) {
            $contacts[] = [
                'type' => 'email',
                'contact' => $incoming->email_drop_site ?? '',
            ];
        }

        return array_unique($contacts, SORT_REGULAR);
    }

    private function processIncomingNotifications($incoming, int $interval)
    {
        if (!$incoming instanceof IncomingShipments) {
            return;
        }

        $data = $this->prepareNotificationData($incoming, $interval);
        $contacts = $this->getContactsForIncoming($incoming);

        foreach ($contacts as $contact) {
            try {
                if ($contact['type'] == 'whatsapp') {
                    $this->sendWhatsAppNotification(
                        $contact['contact'],
                        $data,
                        $contact['template']
                    );
                } elseif ($contact['type'] == 'email') {
                    $this->sendEmailNotification(
                        $contact['contact'],
                        $data
                    );
                }
            } catch (\Exception $e) {
                Log::error('Failed to send ' . $contact['type'] . ' notification: ' . $e->getMessage());
            }
        }
    }

    private function prepareNotificationData(IncomingShipments $incoming, int $interval)
    {
        $itemDetails = $incoming->item->map(function ($item) use ($incoming) {
            return sprintf(
                "%s %s; ETA %s; Drop Site %s",
                $item->item_name ?? 'Unknown Name',
                $item->quantity ?? 'Unknown Quantity',
                Carbon::parse($incoming->eta)->format('d F Y') ?? 'Unknown ETA',
                $incoming->drop_site ?? $incoming->drop->first()->name ?? 'Unknown Drop Site'
            );
        })->implode(' || ') ?: 'No items available.';

        return [
            'branch' => $incoming->branch->name ?? 'N/A',
            'item' => $itemDetails,
            'expired' => Carbon::parse($incoming->eta)->format('d F Y') ?? 'N/A',
            'attachment' => $incoming->attachment ?? 'N/A',
            'remaining_days' => $interval
        ];
    }

    private function sendWhatsAppNotification(string $phone, array $data, string $templateId)
    {
        $name = 'PT Utomodeck Metal Works';
        $body = [
            [
                'key' => '1',
                'value_text' => $data['branch'],
                'value' => 'branch',
            ],
            [
                'key' => '2',
                'value_text' => $data['item'],
                'value' => 'item',
            ],
        ];

        try {
            return $this->qontakServices->sendMessage($phone, $name, $templateId, $body);
        } catch (\Exception $e) {
            Log::error('WhatsApp Notification Error: ' . $e->getMessage());
            return false;
        }
    }

    private function sendEmailNotification(string $email, array $data)
    {
        // Fungsi untuk memproses email CC
        $getEmailCc = function ($email) {
            // Filter email kosong dan validasi format
            return array_filter(explode(',', $email), function ($email) {
                return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
            });
        };

        // Dapatkan dan bersihkan list CC email
        $ccEmails = $getEmailCc($email);

        // Kirim email
        try {
            Mail::to(config('mail.from.address'))
                ->cc($ccEmails)
                ->send(new IncomingExpiredNotificationEmail($data));
        } catch (\Exception $e) {
            Log::error('Email Notification Error: ' . $e->getMessage());
        }
    }
}
