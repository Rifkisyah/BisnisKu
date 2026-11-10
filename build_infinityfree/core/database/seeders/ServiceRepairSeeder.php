<?php

namespace Database\Seeders;

use App\Models\ServiceRepair;
use App\Models\ServiceRepairItem;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServiceRepairSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $teknisiUser = User::whereHas('role', function ($q) { $q->where('name', 'teknisi'); })
            ->where('store_id', $store?->id)->first();
        if (!$teknisiUser) return;

        $repairs = [
            // ─── DRAFT (7 data) ──────────────────────────────────────────────────────
            ['code'=>'SRV202607010001','customer'=>'Budi Santoso',      'phone'=>'081234560001','fee'=>150000,'status'=>'draft',        'days_ago'=>60,'brand'=>'Xiaomi',  'series'=>'Redmi Note 10','complaint'=>'Layar retak, masih draf penerimaan'],
            ['code'=>'SRV202607030002','customer'=>'Siti Rahma',        'phone'=>'081234560002','fee'=>85000, 'status'=>'draft',        'days_ago'=>58,'brand'=>'Samsung', 'series'=>'Galaxy A03s',  'complaint'=>'Kamera buram, perlu pengecekan awal'],
            ['code'=>'SRV202607050003','customer'=>'Ahmad Fauzi',       'phone'=>'081234560003','fee'=>95000, 'status'=>'draft',        'days_ago'=>56,'brand'=>'Oppo',    'series'=>'A5s',          'complaint'=>'Speaker bawah suara kecil macet'],
            ['code'=>'SRV202607060004','customer'=>'Dewi Lestari',      'phone'=>'081234560004','fee'=>75000, 'status'=>'draft',        'days_ago'=>54,'brand'=>'Vivo',    'series'=>'Y20',          'complaint'=>'Tombol volume tidak berfungsi'],
            ['code'=>'SRV202607070005','customer'=>'Eko Prasetyo',      'phone'=>'081234560005','fee'=>60000, 'status'=>'draft',        'days_ago'=>52,'brand'=>'Realme',  'series'=>'C2',           'complaint'=>'HP sering mati sendiri'],
            ['code'=>'SRV202607090006','customer'=>'Fitri Handayani',   'phone'=>'081234560006','fee'=>80000, 'status'=>'draft',        'days_ago'=>50,'brand'=>'Samsung', 'series'=>'Galaxy A10',   'complaint'=>'Layar berkedip dan tidak responsif'],
            ['code'=>'SRV202607100007','customer'=>'Galih Susanto',     'phone'=>'081234560007','fee'=>55000, 'status'=>'draft',        'days_ago'=>48,'brand'=>'Oppo',    'series'=>'A3s',          'complaint'=>'Mic rusak suara tidak terdengar'],

            // ─── WAITING DP (6 data) ─────────────────────────────────────────────────
            ['code'=>'SRV202607120008','customer'=>'Rina Wati',         'phone'=>'081234560008','fee'=>250000,'status'=>'waiting_dp',  'days_ago'=>46,'brand'=>'iPhone',  'series'=>'XR',           'complaint'=>'Ganti LCD original, menunggu DP'],
            ['code'=>'SRV202607140009','customer'=>'Dedy Kurniawan',    'phone'=>'081234560009','fee'=>300000,'status'=>'waiting_dp',  'days_ago'=>44,'brand'=>'Vivo',    'series'=>'Y20',          'complaint'=>'Mati total konslet jalur mesin, tunggu DP'],
            ['code'=>'SRV202607150010','customer'=>'Hendra Pratama',    'phone'=>'081234560010','fee'=>180000,'status'=>'waiting_dp',  'days_ago'=>42,'brand'=>'Realme',  'series'=>'8 Pro',        'complaint'=>'Konektor charge rusak dan baterai kembung'],
            ['code'=>'SRV202607170011','customer'=>'Intan Permata',     'phone'=>'081234560011','fee'=>220000,'status'=>'waiting_dp',  'days_ago'=>40,'brand'=>'Samsung', 'series'=>'Galaxy A50',   'complaint'=>'LCD retak dan ada garis vertikal, estimasi disetujui'],
            ['code'=>'SRV202607180012','customer'=>'Joko Prabowo',      'phone'=>'081234560012','fee'=>175000,'status'=>'waiting_dp',  'days_ago'=>38,'brand'=>'Xiaomi',  'series'=>'Poco X3',      'complaint'=>'Kamera depan pecah dan speaker tidak bunyi'],
            ['code'=>'SRV202607200013','customer'=>'Kartika Susila',    'phone'=>'081234560013','fee'=>320000,'status'=>'waiting_dp',  'days_ago'=>36,'brand'=>'iPhone',  'series'=>'11',           'complaint'=>'Face ID tidak berfungsi, tunggu persetujuan biaya'],

            // ─── DIAGNOSING (6 data) ─────────────────────────────────────────────────
            ['code'=>'SRV202607220014','customer'=>'Maya Sari',         'phone'=>'081234560014','fee'=>60000, 'status'=>'diagnosing',  'days_ago'=>34,'brand'=>'Realme',  'series'=>'C2',           'complaint'=>'HP sering restart sendiri saat buka aplikasi'],
            ['code'=>'SRV202607230015','customer'=>'Deny Saputra',      'phone'=>'081234560015','fee'=>75000, 'status'=>'diagnosing',  'days_ago'=>32,'brand'=>'Samsung', 'series'=>'Galaxy A54',   'complaint'=>'Sinyal hilang timbul pengecekan antena'],
            ['code'=>'SRV202607240016','customer'=>'Lestari Putri',     'phone'=>'081234560016','fee'=>100000,'status'=>'diagnosing',  'days_ago'=>30,'brand'=>'Xiaomi',  'series'=>'Poco X3',      'complaint'=>'Sound mati dan kamera depan tidak bisa dibuka'],
            ['code'=>'SRV202607250017','customer'=>'Muhamad Rizal',     'phone'=>'081234560017','fee'=>80000, 'status'=>'diagnosing',  'days_ago'=>28,'brand'=>'Oppo',    'series'=>'Reno 5',       'complaint'=>'Layar berkedip saat disentuh sudut kiri'],
            ['code'=>'SRV202607260018','customer'=>'Novi Andini',       'phone'=>'081234560018','fee'=>90000, 'status'=>'diagnosing',  'days_ago'=>26,'brand'=>'Vivo',    'series'=>'V15',          'complaint'=>'Fingerprint tidak terbaca dan tombol home macet'],
            ['code'=>'SRV202607270019','customer'=>'Oscar Wirawan',     'phone'=>'081234560019','fee'=>70000, 'status'=>'diagnosing',  'days_ago'=>24,'brand'=>'Samsung', 'series'=>'Galaxy M20',   'complaint'=>'Charging lambat, dicek konektor dan IC'],

            // ─── WAITING PARTS (6 data) ──────────────────────────────────────────────
            ['code'=>'SRV202607280020','customer'=>'Pita Sari',         'phone'=>'081234560020','fee'=>220000,'status'=>'waiting_parts','days_ago'=>22,'brand'=>'Samsung', 'series'=>'Galaxy S21',   'complaint'=>'Backglass pecah, sedang memesan sparepart'],
            ['code'=>'SRV202607290021','customer'=>'Qori Hamdan',       'phone'=>'081234560021','fee'=>140000,'status'=>'waiting_parts','days_ago'=>20,'brand'=>'Oppo',    'series'=>'Reno 5',       'complaint'=>'Tombol volume dan power lepas, menunggu part'],
            ['code'=>'SRV202607300022','customer'=>'Rizki Akbar',       'phone'=>'081234560022','fee'=>190000,'status'=>'waiting_parts','days_ago'=>18,'brand'=>'iPhone',  'series'=>'11',           'complaint'=>'Kamera getar fokus rusak, part kamera dipesan'],
            ['code'=>'SRV202608010023','customer'=>'Sari Munawaroh',    'phone'=>'081234560023','fee'=>165000,'status'=>'waiting_parts','days_ago'=>16,'brand'=>'Xiaomi',  'series'=>'Redmi Note 9', 'complaint'=>'LCD fullset dipesan dari supplier sparepart'],
            ['code'=>'SRV202608020024','customer'=>'Tommy Santoso',     'phone'=>'081234560024','fee'=>200000,'status'=>'waiting_parts','days_ago'=>14,'brand'=>'Vivo',    'series'=>'Y20i',         'complaint'=>'Konektor cas dan baterai rusak bersamaan'],
            ['code'=>'SRV202608030025','customer'=>'Umi Kulsum',        'phone'=>'081234560025','fee'=>110000,'status'=>'waiting_parts','days_ago'=>12,'brand'=>'Realme',  'series'=>'5i',           'complaint'=>'Flexibel kamera belakang rusak, menunggu stok'],

            // ─── REPAIRING (6 data) ──────────────────────────────────────────────────
            ['code'=>'SRV202608040026','customer'=>'Vandi Prastyo',     'phone'=>'081234560026','fee'=>150000,'status'=>'repairing',   'days_ago'=>11,'brand'=>'Vivo',    'series'=>'Y50',          'complaint'=>'Sedang proses penggantian layar LCD oleh teknisi'],
            ['code'=>'SRV202608050027','customer'=>'Windi Rahayu',      'phone'=>'081234560027','fee'=>110000,'status'=>'repairing',   'days_ago'=>10,'brand'=>'Xiaomi',  'series'=>'Redmi 9',      'complaint'=>'Sedang pengerjaan solder konektor charge baru'],
            ['code'=>'SRV202608060028','customer'=>'Xander Siregar',    'phone'=>'081234560028','fee'=>175000,'status'=>'repairing',   'days_ago'=>9, 'brand'=>'Infinix', 'series'=>'Hot 10',       'complaint'=>'Sedang proses instal ulang software dan backup'],
            ['code'=>'SRV202608070029','customer'=>'Yolanda Fitri',     'phone'=>'081234560029','fee'=>200000,'status'=>'repairing',   'days_ago'=>8, 'brand'=>'Samsung', 'series'=>'Galaxy A50',   'complaint'=>'Baterai kembung sedang dilepas dan diganti baru'],
            ['code'=>'SRV202608080030','customer'=>'Zulfa Maulana',     'phone'=>'081234560030','fee'=>130000,'status'=>'repairing',   'days_ago'=>7, 'brand'=>'Oppo',    'series'=>'A54',          'complaint'=>'Speaker buzzer putus sedang diganti komponen baru'],
            ['code'=>'SRV202608090031','customer'=>'Andi Wijaya',       'phone'=>'081234560031','fee'=>165000,'status'=>'repairing',   'days_ago'=>6, 'brand'=>'iPhone',  'series'=>'XR',           'complaint'=>'IC charging soak sedang dilakukan rework di board'],

            // ─── READY (7 data) ──────────────────────────────────────────────────────
            ['code'=>'SRV202608100032','customer'=>'Bambang Irawan',    'phone'=>'081234560032','fee'=>130000,'status'=>'ready',       'days_ago'=>5, 'brand'=>'Samsung', 'series'=>'Galaxy M20',   'complaint'=>'Baterai baru sudah terpasang rapi, siap diambil'],
            ['code'=>'SRV202608110033','customer'=>'Citra Dewi',        'phone'=>'081234560033','fee'=>95000, 'status'=>'ready',       'days_ago'=>5, 'brand'=>'Oppo',    'series'=>'A3s',          'complaint'=>'Flashing sukses HP sudah normal siap diambil'],
            ['code'=>'SRV202608120034','customer'=>'Diana Susanto',     'phone'=>'081234560034','fee'=>160000,'status'=>'ready',       'days_ago'=>4, 'brand'=>'Realme',  'series'=>'5i',           'complaint'=>'Layar sentuh baru sudah dites normal siap diambil'],
            ['code'=>'SRV202608130035','customer'=>'Erik Saputra',      'phone'=>'081234560035','fee'=>180000,'status'=>'ready',       'days_ago'=>4, 'brand'=>'Xiaomi',  'series'=>'Redmi Note 10','complaint'=>'LCD fullset baru terpasang sempurna siap diambil'],
            ['code'=>'SRV202608140036','customer'=>'Fauzi Rahman',      'phone'=>'081234560036','fee'=>200000,'status'=>'ready',       'days_ago'=>3, 'brand'=>'Samsung', 'series'=>'Galaxy A50',   'complaint'=>'Ganti baterai selesai, performa optimal siap diambil'],
            ['code'=>'SRV202608140037','customer'=>'Gina Pratiwi',      'phone'=>'081234560037','fee'=>120000,'status'=>'ready',       'days_ago'=>3, 'brand'=>'Vivo',    'series'=>'Y15',          'complaint'=>'Konektor cas baru sudah tersolder rapi siap diambil'],
            ['code'=>'SRV202608150038','customer'=>'Hadi Nugroho',      'phone'=>'081234560038','fee'=>145000,'status'=>'ready',       'days_ago'=>2, 'brand'=>'Oppo',    'series'=>'Reno 5',       'complaint'=>'Speaker baru terpasang dan fingerprint berfungsi'],

            // ─── DONE (7 data) ───────────────────────────────────────────────────────
            ['code'=>'SRV202608060039','customer'=>'Irma Wulansari',    'phone'=>'081234560039','fee'=>150000,'status'=>'done',        'days_ago'=>16,'brand'=>'Xiaomi',  'series'=>'Redmi Note 10','complaint'=>'Layar LCD baru selesai dipasang dan sudah dibayar'],
            ['code'=>'SRV202608070040','customer'=>'Jefri Gunawan',     'phone'=>'081234560040','fee'=>80000, 'status'=>'done',        'days_ago'=>15,'brand'=>'Samsung', 'series'=>'Galaxy A10',   'complaint'=>'Konektor charger sudah diganti dan berfungsi baik'],
            ['code'=>'SRV202608080041','customer'=>'Kurnia Hapsari',    'phone'=>'081234560041','fee'=>120000,'status'=>'done',        'days_ago'=>14,'brand'=>'iPhone',  'series'=>'XR',           'complaint'=>'Ganti baterai health 100% selesai'],
            ['code'=>'SRV202608090042','customer'=>'Lukman Hakim',      'phone'=>'081234560042','fee'=>90000, 'status'=>'done',        'days_ago'=>13,'brand'=>'Oppo',    'series'=>'A5s',          'complaint'=>'Flashing dan reset pabrik berhasil, HP normal'],
            ['code'=>'SRV202608100043','customer'=>'Mega Setiani',      'phone'=>'081234560043','fee'=>175000,'status'=>'done',        'days_ago'=>12,'brand'=>'Samsung', 'series'=>'Galaxy A54',   'complaint'=>'LCD dan touch sudah diganti, kamera normal'],
            ['code'=>'SRV202608110044','customer'=>'Nanda Pratama',     'phone'=>'081234560044','fee'=>200000,'status'=>'done',        'days_ago'=>11,'brand'=>'iPhone',  'series'=>'11',           'complaint'=>'Baterai dan konektor cas diganti selesai'],
            ['code'=>'SRV202608120045','customer'=>'Oki Setiawan',      'phone'=>'081234560045','fee'=>130000,'status'=>'done',        'days_ago'=>10,'brand'=>'Vivo',    'series'=>'V15',          'complaint'=>'Speaker dan mic baru, suara jernih sempurna'],

            // ─── CANCELLED (5 data) ──────────────────────────────────────────────────
            ['code'=>'SRV202607200046','customer'=>'Putri Wulandari',   'phone'=>'081234560046','fee'=>0,     'status'=>'cancelled',   'days_ago'=>33,'brand'=>'Oppo',    'series'=>'F11',          'complaint'=>'Dibatalkan oleh pelanggan karena pertimbangan biaya'],
            ['code'=>'SRV202607250047','customer'=>'Rendra Setiawan',   'phone'=>'081234560047','fee'=>0,     'status'=>'cancelled',   'days_ago'=>28,'brand'=>'Vivo',    'series'=>'Y12',          'complaint'=>'Dibatalkan karena sparepart discontinued'],
            ['code'=>'SRV202607300048','customer'=>'Suci Amalia',       'phone'=>'081234560048','fee'=>0,     'status'=>'cancelled',   'days_ago'=>23,'brand'=>'Xiaomi',  'series'=>'Mi 8',         'complaint'=>'Mesin mati total IC emmc rusak tidak dilanjutkan'],
            ['code'=>'SRV202608050049','customer'=>'Tiara Andini',      'phone'=>'081234560049','fee'=>0,     'status'=>'cancelled',   'days_ago'=>17,'brand'=>'Samsung', 'series'=>'Galaxy J2',    'complaint'=>'Pelanggan membatalkan karena beli HP baru'],
            ['code'=>'SRV202608120050','customer'=>'Udin Baharudin',    'phone'=>'081234560050','fee'=>0,     'status'=>'cancelled',   'days_ago'=>10,'brand'=>'Realme',  'series'=>'C3',           'complaint'=>'Tidak ada konfirmasi dari pelanggan selama 2 minggu'],
        ];

        // Sparepart usage mapping
        $sparepartUsage = [
            'SRV202607280020' => ['code'=>'PRD00044','name'=>'LCD Samsung A03s','price'=>220000,'qty'=>1,'status'=>'pending'],
            'SRV202607290021' => ['code'=>'PRD00045','name'=>'Konektor Cas Type-C','price'=>25000,'qty'=>1,'status'=>'pending'],
            'SRV202607300022' => ['code'=>'PRD00043','name'=>'LCD Redmi Note 10','price'=>250000,'qty'=>1,'status'=>'pending'],
            'SRV202608010023' => ['code'=>'PRD00043','name'=>'LCD Redmi Note 10','price'=>250000,'qty'=>1,'status'=>'pending'],
            'SRV202608020024' => ['code'=>'PRD00045','name'=>'Konektor Cas Type-C','price'=>25000,'qty'=>1,'status'=>'pending'],
            'SRV202608030025' => ['code'=>'PRD00046','name'=>'Flexible Tombol On/Off','price'=>15000,'qty'=>1,'status'=>'pending'],
            'SRV202608040026' => ['code'=>'PRD00044','name'=>'LCD Samsung A03s','price'=>220000,'qty'=>1,'status'=>'used'],
            'SRV202608050027' => ['code'=>'PRD00045','name'=>'Konektor Cas Type-C','price'=>25000,'qty'=>2,'status'=>'used'],
            'SRV202608060028' => ['code'=>'PRD00039','name'=>'Baterai Double Power','price'=>75000,'qty'=>1,'status'=>'used'],
            'SRV202608070029' => ['code'=>'PRD00039','name'=>'Baterai Double Power','price'=>75000,'qty'=>1,'status'=>'used'],
            'SRV202608080030' => ['code'=>'PRD00050','name'=>'Speaker Buzzer','price'=>25000,'qty'=>1,'status'=>'used'],
            'SRV202608090031' => ['code'=>'PRD00049','name'=>'Ic Cas Universal','price'=>35000,'qty'=>1,'status'=>'used'],
            'SRV202608100032' => ['code'=>'PRD00039','name'=>'Baterai Double Power','price'=>75000,'qty'=>1,'status'=>'used'],
            'SRV202608120034' => ['code'=>'PRD00044','name'=>'LCD Samsung A03s','price'=>220000,'qty'=>1,'status'=>'used'],
            'SRV202608130035' => ['code'=>'PRD00043','name'=>'LCD Redmi Note 10','price'=>250000,'qty'=>1,'status'=>'used'],
            'SRV202608140036' => ['code'=>'PRD00039','name'=>'Baterai Double Power','price'=>75000,'qty'=>1,'status'=>'used'],
            'SRV202608140037' => ['code'=>'PRD00045','name'=>'Konektor Cas Type-C','price'=>25000,'qty'=>1,'status'=>'used'],
            'SRV202608060039' => ['code'=>'PRD00043','name'=>'LCD Redmi Note 10','price'=>250000,'qty'=>1,'status'=>'used'],
            'SRV202608070040' => ['code'=>'PRD00045','name'=>'Konektor Cas Type-C','price'=>25000,'qty'=>1,'status'=>'used'],
            'SRV202608080041' => ['code'=>'PRD00041','name'=>'Baterai iPhone Original','price'=>150000,'qty'=>1,'status'=>'used'],
            'SRV202608090042' => ['code'=>'PRD00046','name'=>'Flexible Tombol On/Off','price'=>15000,'qty'=>1,'status'=>'used'],
            'SRV202608100043' => ['code'=>'PRD00044','name'=>'LCD Samsung A03s','price'=>220000,'qty'=>1,'status'=>'used'],
            'SRV202608110044' => ['code'=>'PRD00041','name'=>'Baterai iPhone Original','price'=>150000,'qty'=>1,'status'=>'used'],
            'SRV202608120045' => ['code'=>'PRD00050','name'=>'Speaker Buzzer','price'=>25000,'qty'=>1,'status'=>'used'],
        ];

        foreach ($repairs as $r) {
            $startDate = now()->subDays($r['days_ago'])->setTime(rand(10, 16), 0, 0);
            $completionDate = $r['status'] === 'done' ? (clone $startDate)->addDays(rand(1, 3)) : null;

            $repair = ServiceRepair::updateOrCreate(
                ['repair_code' => $r['code']],
                [
                    'store_id'        => $store?->id,
                    'technician_id'   => $teknisiUser->id,
                    'customer_name'   => $r['customer'],
                    'customer_phone'  => $r['phone'],
                    'service_fee'     => $r['fee'],
                    'component_cost'  => 0,
                    'total_cost'      => $r['fee'],
                    'payment_method'  => 'cash',
                    'down_payment'    => 0,
                    'status'          => $r['status'],
                    'start_date'      => $startDate,
                    'completion_date' => $completionDate,
                ]
            );

            $deviceItem = ServiceRepairItem::updateOrCreate(
                [
                    'repair_code' => $r['code'],
                    'parent_id'   => null,
                    'name'        => $r['brand'] . ' ' . $r['series'],
                ],
                [
                    'brand'       => $r['brand'],
                    'series'      => $r['series'],
                    'complaint'   => $r['complaint'],
                    'quantity'    => 1,
                    'service_fee' => $r['fee'],
                    'subtotal'    => $r['fee'],
                ]
            );

            if (isset($sparepartUsage[$r['code']])) {
                $sp = $sparepartUsage[$r['code']];
                ServiceRepairItem::updateOrCreate(
                    [
                        'repair_code'    => $r['code'],
                        'component_code' => $sp['code'],
                    ],
                    [
                        'parent_id'        => $deviceItem->id,
                        'name'             => $sp['name'],
                        'quantity'         => $sp['qty'],
                        'service_fee'      => 0,
                        'subtotal'         => $sp['price'] * $sp['qty'],
                        'sparepart_type'   => 'from_stock',
                        'sparepart_status' => $sp['status'],
                    ]
                );
            }

            $repair->calculateTotalCost();
        }

        app()->forgetInstance('current_store');
    }
}
