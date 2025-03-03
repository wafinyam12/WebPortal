<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\QontakSevices;
use App\Mail\ContractExpiredNotificationEmail;
use App\Models\Contract;
use App\Models\Notifications;
use App\Models\User;
use Carbon\Carbon;

class SendNotificationContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-notification-contract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command sends notifications based on contract expirations.';

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
            $this->contractNotifications($intervals);
            $this->info('Notifications sent successfully');
        } catch (\Exception $e) {
            Log::error('Contract Notification Error: ' . $e->getMessage());
            $this->error('Failed to send notifications: ' . $e->getMessage());
        }
    }

    private function contractNotifications(array $intervals)
    {
        foreach ($intervals as $interval) {
            $targetDate = now()->addDays($interval);
            $contracts = $this->getContractByInterval($targetDate);

            if ($contracts->isEmpty()) {
                continue;
            }

            foreach ($contracts as $contract) {
                if ($contract instanceof Contract) {
                    $this->processContractNotifications($contract, $interval);
                }
            }
        }
    }

    private function getContractByInterval($targetDate)
    {
        return Contract::whereDate('masa_berlaku', $targetDate)
            ->orWhere('masa_berlaku', '<', now())
            ->get();
    }

    private function getContactForContract(Contract $contract)
    {
        $contacts = [];

        $notifications = Notifications::where('name', 'like', '%contract%')->get();
        foreach ($notifications as $notification) {
            $roles = json_decode($notification->roles, true) ?? [];
            $users = User::with('employe', 'roles')
                ->whereHas('roles', function ($query) use ($roles) {
                    $query->whereIn('name', $roles);
                })->get();

            foreach ($users as $user) {
                if ($user->employe && !empty($user->employe->phone)) {
                    $contacts[] = [
                        'type' => 'whatsapp',
                        'contact' => $user->employe->phone ?? null,
                        'template' => $notification->template ?? null
                    ];
                }

                if (!empty($user->email)) {
                    $contacts[] = [
                        'type' => 'email',
                        'contact' => $user->email ?? null
                    ];
                }
            }
        }

        return array_unique($contacts, SORT_REGULAR);
    }

    private function processContractNotifications($contract, int $interval)
    {
        if (!$contract instanceof Contract) {
            return;
        }

        $data = $this->prepareNotificationData($contract, $interval);
        $contacts = $this->getContactForContract($contract);

        foreach ($contacts as $contact) {
            try {
                if ($contact['type'] === 'whatsapp') {
                    $this->sendWhatsAppNotification(
                        $contact['contact'],
                        $data,
                        $contact['template']
                    );
                    Log::info('WhatsApp notification sent to ' . $contact['contact']);
                } else if ($contact['type'] === 'email') {
                    $this->sendEmailNotification(
                        $contact['contact'],
                        $data
                    );
                    Log::info('Email notification sent to ' . $contact['contact']);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send ' . $contact['type'] . ' notification: ' . $e->getMessage());
            }
        }
    }

    private function prepareNotificationData($contract, int $interval): array
    {
        return [
            'name' => $contract->name ?? 'N/A',
            'company' => $contract->nama_perusahaan ?? 'N/A',
            'expired' => Carbon::parse($contract->masa_berlaku)->format('d F Y') ?? 'N/A',
            'remaining_days' => $interval
        ];
    }

    private function sendWhatsAppNotification(string $phone, array $data, string $templateId)
    {
        $name = 'PT Utomodeck Metal Works';
        $body = [
            [
                'key' => '1',
                'value_text' => $data['name'],
                'value' => 'name',
            ],
            [
                'key' => '2',
                'value_text' => $data['company'],
                'value' => 'company',
            ],
            [
                'key' => '3',
                'value_text' => $data['expired'],
                'value' => 'expired',
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
                ->send(new ContractExpiredNotificationEmail($data));
        } catch (\Exception $e) {
            Log::error('Email Notification Error: ' . $e->getMessage());
        }
    }
}
