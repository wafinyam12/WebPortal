<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\QontakSevices;
use App\Mail\VehicleExpiredNotificationEmail;
use App\Models\Notifications;
use App\Models\Vehicle;
use App\Models\User;
use Carbon\Carbon;

class SendNotificationVehicle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-notification-vehicle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command sends vehicle notifications expired to users based on intervals';

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
            $this->vehicleNotifications($intervals);
            $this->info('Notifications sent successfully');
        } catch (\Exception $e) {
            Log::error('Vehicle Notification Error: ' . $e->getMessage());
            $this->error('Failed to send notifications: ' . $e->getMessage());
        }
    }

    private function vehicleNotifications($intervals)
    {
        foreach ($intervals as $interval) {
            $targetDate = now()->addDays($interval);
            $vehicles = $this->getVehiclesByInterval($targetDate);

            if ($vehicles->isEmpty()) {
                continue;
            }

            foreach ($vehicles as $vehicle) {
                $this->processVehicleNotifications($vehicle, $targetDate);
            }
        }
    }

    private function getVehiclesByInterval($targetDate)
    {
        return Vehicle::with('assigned.employe')
            ->where(function ($query) use ($targetDate) {
                $query->whereDate('tax_year', $targetDate)
                    ->orWhereDate('tax_five_year', $targetDate)
                    ->orWhereDate('inspected', $targetDate)
                    ->orWhere('tax_year', '<', now())
                    ->orWhere('tax_five_year', '<', now())
                    ->orWhere('inspected', '<', now());
            })
            ->get();
    }

    private function getContactsForVehicle(Vehicle $vehicle)
    {
        $contacts = [];

        // Ambil satu template dari notifikasi
        $notification = Notifications::where('name', 'like', '%vehicle%')->first();
        if (!$notification) {
            return $contacts;
        }

        $template = $notification->template;
        $roles = json_decode($notification->roles, true) ?? [];

        // Ambil semua user berdasarkan roles
        $users = User::with('employe', 'roles')
            ->whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('name', $roles);
            })->get();

        // Loop untuk setiap user
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

        // Tambahkan nomor HP dari last assigned (jika ada)
        if ($vehicle->assigned->isNotEmpty()) {
            $lastAssigned = $vehicle->assigned->last();
            if (!empty($lastAssigned->employe->phone)) {
                $contacts[] = [
                    'type' => 'whatsapp',
                    'contact' => $lastAssigned->employe->phone,
                    'template' => $template  // Gunakan template yang sama untuk last assigned
                ];
            }
        }

        // Hapus duplikat
        return array_unique($contacts, SORT_REGULAR);
    }

    private function getNotificationType($vehicle, $targetDate)
    {
        if (!empty($vehicle->tax_year) && Carbon::parse($vehicle->tax_year)->isSameDay($targetDate)) {
            return [
                'type' => 'Pajak Satu Tahun',
                'date' => Carbon::parse($vehicle->tax_year)->format('d M Y'),
            ];
        } elseif (!empty($vehicle->tax_five_year) && Carbon::parse($vehicle->tax_five_year)->isSameDay($targetDate)) {
            return [
                'type' => 'Pajak Lima Tahun',
                'date' => Carbon::parse($vehicle->tax_five_year)->format('d M Y'),
            ];
        } elseif (!empty($vehicle->inspected) && Carbon::parse($vehicle->inspected)->isSameDay($targetDate)) {
            return [
                'type' => 'UJI KIR',
                'date' => Carbon::parse($vehicle->inspected)->format('d M Y'),
            ];
        }

        return null;
    }

    /**
     * @param mixed $vehicle Instance of Vehicle model
     * @param Carbon|string $targetDate
     */
    private function processVehicleNotifications($vehicle, $targetDate)
    {
        if (!$vehicle instanceof Vehicle) {
            return;
        }

        $notificationType = $this->getNotificationType($vehicle, $targetDate);
        if (!$notificationType) {
            return;
        }

        $data = $this->prepareNotificationData($vehicle, $notificationType);
        $contacts = $this->getContactsForVehicle($vehicle);

        foreach ($contacts as $contact) {
            try {
                if ($contact['type'] == 'whatsapp' && isset($contact['template'])) {
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

    private function prepareNotificationData($vehicle, array $notificationType)
    {
        return [
            'type' => $notificationType['type'],
            'nopol' => $vehicle->license_plate,
            'model' => $vehicle->model,
            'expired' => $notificationType['date']
        ];
    }

    private function sendWhatsAppNotification(string $phone, array $data, string $templateId)
    {
        $name = 'PT Utomodeck Metal Works';
        $body = [
            [
                'key' => '1',
                'value_text' => $data['type'] ?? 'N/A',
                'value' => 'type'
            ],
            [
                'key' => '2',
                'value_text' => $data['nopol'] ?? 'N/A',
                'value' => 'nopol'
            ],
            [
                'key' => '3',
                'value_text' => $data['model'] ?? 'N/A',
                'value' => 'model'
            ],
            [
                'key' => '4',
                'value_text' => $data['expired'] ?? 'N/A',
                'value' => 'expired'
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
                ->send(new VehicleExpiredNotificationEmail($data));
        } catch (\Exception $e) {
            Log::error('Email Notification Error: ' . $e->getMessage());
        }
    }
}
