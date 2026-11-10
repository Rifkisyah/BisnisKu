<?php

namespace Database\Seeders;

use App\Models\Debt;
use App\Models\Store;
use Illuminate\Database\Seeder;

class DebtSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $debts = [
            // ─── STATUS: UNPAID (17 data) ─────────────────────────────────────────────
            ['code'=>'DBT00001','name'=>'Bu Yanti (Tetangga Depan)',       'contact'=>'081234110001','address'=>'Jl. Pahlawan No. 120',             'total'=>85000, 'paid'=>0,'days_ago'=>14,'due_days'=>14,'notes'=>'Hutang kuota Telkomsel 35GB'],
            ['code'=>'DBT00002','name'=>'Linda (Karyawan Minimarket)',      'contact'=>'081234110002','address'=>'Jl. Pahlawan No. 110',             'total'=>110000,'paid'=>0,'days_ago'=>7, 'due_days'=>21,'notes'=>'Hutang powerbank 10000mAh'],
            ['code'=>'DBT00003','name'=>'Pak Bambang (Ketua RT 03)',        'contact'=>'081234110003','address'=>'Jl. Pahlawan No. 1',               'total'=>350000,'paid'=>0,'days_ago'=>10,'due_days'=>7, 'notes'=>'Hutang servis ganti konektor cas & pulsa bulanan'],
            ['code'=>'DBT00004','name'=>'Sari (Kasir Butik Depan)',         'contact'=>'081234110004','address'=>'Jl. Pahlawan No. 98',              'total'=>120000,'paid'=>0,'days_ago'=>5, 'due_days'=>14,'notes'=>'Hutang headset bluetooth & kabel data Type-C'],
            ['code'=>'DBT00005','name'=>'Dimas (Kurir Ekspedisi)',          'contact'=>'081234110005','address'=>'Basecamp Kurir Pahlawan',          'total'=>195000,'paid'=>0,'days_ago'=>18,'due_days'=>-3,'notes'=>'Hutang ganti baterai Xiaomi Redmi Note 9'],
            ['code'=>'DBT00006','name'=>'Bu Siti (Warung Nasi Uduk)',       'contact'=>'081234110006','address'=>'Jl. Pahlawan Gg. 1 No. 3',         'total'=>450000,'paid'=>0,'days_ago'=>12,'due_days'=>10,'notes'=>'Hutang pulsa data & paket nelpon keluarga'],
            ['code'=>'DBT00007','name'=>'Reza (Mahasiswa Teknik)',          'contact'=>'081234110007','address'=>'Asrama Putra Teknik No. 14',       'total'=>180000,'paid'=>0,'days_ago'=>8, 'due_days'=>12,'notes'=>'Hutang TWS Bluetooth & antigores hydrogel matte'],
            ['code'=>'DBT00008','name'=>'Pak RT 05 (Warga Komplek)',        'contact'=>'081234110008','address'=>'Jl. Merdeka Komplek RT 05 No. 2',  'total'=>290000,'paid'=>0,'days_ago'=>11,'due_days'=>9, 'notes'=>'Hutang servis mic HP & kuota Telkomsel 50GB'],
            ['code'=>'DBT00009','name'=>'Toni (Penjaga Warnet)',            'contact'=>'081234110009','address'=>'Warnet Cyber Jl. Pahlawan 15',     'total'=>75000, 'paid'=>0,'days_ago'=>3, 'due_days'=>7, 'notes'=>'Hutang kartu perdana & pulsa reguler'],
            ['code'=>'DBT00010','name'=>'Nani (Ibu Rumah Tangga)',          'contact'=>'081234110010','address'=>'Jl. Mawar Gg. 3 No. 7',            'total'=>65000, 'paid'=>0,'days_ago'=>6, 'due_days'=>14,'notes'=>'Hutang token listrik 50rb'],
            ['code'=>'DBT00011','name'=>'Pandu (Driver Ojol Baru)',         'contact'=>'081234110011','address'=>'Kos Abadi Jl. Melati No. 5',       'total'=>200000,'paid'=>0,'days_ago'=>9, 'due_days'=>5, 'notes'=>'Hutang holder motor & kabel Type-C'],
            ['code'=>'DBT00012','name'=>'Cici (Pelayan Kafe)',              'contact'=>'081234110012','address'=>'Kafe Senja Jl. Pahlawan 40',       'total'=>130000,'paid'=>0,'days_ago'=>4, 'due_days'=>10,'notes'=>'Hutang earphone & tempered glass'],
            ['code'=>'DBT00013','name'=>'Pak Udin (Tukang Las)',            'contact'=>'081234110013','address'=>'Bengkel Las Jl. Pahlawan No. 22',  'total'=>95000, 'paid'=>0,'days_ago'=>15,'due_days'=>-5,'notes'=>'Hutang pulsa 50rb & kabel charger'],
            ['code'=>'DBT00014','name'=>'Rahma (Guru TK)',                  'contact'=>'081234110014','address'=>'Jl. Anggrek No. 4',               'total'=>55000, 'paid'=>0,'days_ago'=>2, 'due_days'=>14,'notes'=>'Hutang voucher internet 5GB Indosat'],
            ['code'=>'DBT00015','name'=>'Doni (Pelajar SMK)',               'contact'=>'081234110015','address'=>'Jl. Merdeka No. 21',              'total'=>40000, 'paid'=>0,'days_ago'=>1, 'due_days'=>7, 'notes'=>'Hutang pulsa 20rb & ring holder HP'],
            ['code'=>'DBT00016','name'=>'Pak Hasan (Penjahit)',             'contact'=>'081234110016','address'=>'Butik Hasan Jl. Kenanga No. 8',   'total'=>160000,'paid'=>0,'days_ago'=>20,'due_days'=>-10,'notes'=>'Hutang baterai double power & konektor charger'],
            ['code'=>'DBT00017','name'=>'Suci (Mahasiswi)',                 'contact'=>'081234110017','address'=>'Kos Putri Anggrek No. 3',          'total'=>85000, 'paid'=>0,'days_ago'=>7, 'due_days'=>14,'notes'=>'Hutang softcase & tempered glass privacy'],

            // ─── STATUS: PARTIAL (17 data) ────────────────────────────────────────────
            ['code'=>'DBT00018','name'=>'Agus Supriyanto (Warung Kopi)',    'contact'=>'081234110018','address'=>'Jl. Pahlawan Gg. 2 No. 5',         'total'=>150000,'paid'=>50000, 'days_ago'=>20,'due_days'=>10,'notes'=>'Hutang pulsa & servis LCD Samsung A50'],
            ['code'=>'DBT00019','name'=>'Rizki (Mahasiswa Langganan)',      'contact'=>'081234110019','address'=>'Kost Putra Pahlawan No. 8',         'total'=>250000,'paid'=>100000,'days_ago'=>25,'due_days'=>5, 'notes'=>'Hutang aksesoris TWS & charger fast charging'],
            ['code'=>'DBT00020','name'=>'Kang Maman (Pedagang Sate)',       'contact'=>'081234110020','address'=>'Jl. Pahlawan Depan Indomaret',      'total'=>500000,'paid'=>200000,'days_ago'=>16,'due_days'=>14,'notes'=>'Hutang pembelian speaker portable & charger motor'],
            ['code'=>'DBT00021','name'=>'Maya (Mahasiswi Kos Mawar)',       'contact'=>'081234110021','address'=>'Kos Mawar Kamar 04',                'total'=>300000,'paid'=>150000,'days_ago'=>9, 'due_days'=>5, 'notes'=>'Hutang servis mic HP & antigores matte'],
            ['code'=>'DBT00022','name'=>'Pak Joko (Satpam Komplek)',        'contact'=>'081234110022','address'=>'Pos Satpam Komplek Pahlawan',        'total'=>275000,'paid'=>100000,'days_ago'=>22,'due_days'=>-2,'notes'=>'Hutang paket internet 50GB & senter LED HP'],
            ['code'=>'DBT00023','name'=>'Bayu (Warnet Gaming Sebelah)',     'contact'=>'081234110023','address'=>'Jl. Pahlawan No. 125',              'total'=>650000,'paid'=>350000,'days_ago'=>15,'due_days'=>7, 'notes'=>'Hutang servis PC/modem & kabel LAN RJ45'],
            ['code'=>'DBT00024','name'=>'Hendra (Karyawan Laundry)',        'contact'=>'081234110024','address'=>'Jl. Merdeka Gg. Laundry No. 3',    'total'=>400000,'paid'=>150000,'days_ago'=>12,'due_days'=>8, 'notes'=>'Hutang ganti baterai Xiaomi & tempered glass premium'],
            ['code'=>'DBT00025','name'=>'Siti Aminah (Pedagang Warteg)',    'contact'=>'081234110025','address'=>'Warteg Bahari Jl. Pahlawan No. 44', 'total'=>220000,'paid'=>100000,'days_ago'=>14,'due_days'=>6, 'notes'=>'Hutang pulsa & kabel charger fast charging Type-C'],
            ['code'=>'DBT00026','name'=>'Bowo (Sopir Angkot)',              'contact'=>'081234110026','address'=>'Terminal Pahlawan Pool 3',           'total'=>180000,'paid'=>80000, 'days_ago'=>18,'due_days'=>12,'notes'=>'Hutang pulsa 3 operator & voucher internet XL'],
            ['code'=>'DBT00027','name'=>'Leni (Kasir Apotek)',              'contact'=>'081234110027','address'=>'Apotek Sehat Jl. Pahlawan No. 50', 'total'=>320000,'paid'=>120000,'days_ago'=>10,'due_days'=>10,'notes'=>'Hutang powerbank 10k & earphone TWS wireless'],
            ['code'=>'DBT00028','name'=>'Pak Wahid (Tukang Tambal Ban)',    'contact'=>'081234110028','address'=>'Tambal Ban Jl. Pahlawan Gg. 4',     'total'=>70000, 'paid'=>30000, 'days_ago'=>8, 'due_days'=>7, 'notes'=>'Hutang pulsa Telkomsel & token listrik'],
            ['code'=>'DBT00029','name'=>'Robi (Staf Minimarket)',           'contact'=>'081234110029','address'=>'Minimarket ABC Jl. Merdeka No. 10','total'=>145000,'paid'=>60000, 'days_ago'=>13,'due_days'=>7, 'notes'=>'Hutang kabel data Type-C & holder motor'],
            ['code'=>'DBT00030','name'=>'Lilis (Penjual Gorengan)',         'contact'=>'081234110030','address'=>'Gerobak Gorengan Jl. Pahlawan 55', 'total'=>55000, 'paid'=>20000, 'days_ago'=>5, 'due_days'=>5, 'notes'=>'Hutang voucher Tri AON & softcase HP'],
            ['code'=>'DBT00031','name'=>'Pak Darmin (Penjaga Pos RT)',      'contact'=>'081234110031','address'=>'Pos RT 02 Jl. Anggrek',             'total'=>120000,'paid'=>50000, 'days_ago'=>17,'due_days'=>-3,'notes'=>'Hutang pulsa & kabel charger micro USB'],
            ['code'=>'DBT00032','name'=>'Sinta (SPG Supermarket)',          'contact'=>'081234110032','address'=>'Supermarket Indah Lt. 1',           'total'=>200000,'paid'=>75000, 'days_ago'=>11,'due_days'=>9, 'notes'=>'Hutang earphone 3.5mm & tempered glass bening'],
            ['code'=>'DBT00033','name'=>'Roni (Pedagang Bakso)',            'contact'=>'081234110033','address'=>'Warung Bakso Jl. Melati No. 12',   'total'=>90000, 'paid'=>35000, 'days_ago'=>6, 'due_days'=>7, 'notes'=>'Hutang pulsa 50rb & ring holder'],
            ['code'=>'DBT00034','name'=>'Tari (Kasir Rumah Makan)',         'contact'=>'081234110034','address'=>'RM Sederhana Jl. Pahlawan 30',     'total'=>160000,'paid'=>60000, 'days_ago'=>9, 'due_days'=>11,'notes'=>'Hutang voucher internet & charger kepala'],

            // ─── STATUS: PAID (16 data) ───────────────────────────────────────────────
            ['code'=>'DBT00035','name'=>'Pak Hadi (Driver Ojol)',           'contact'=>'081234110035','address'=>'Pangkalan Ojol Pahlawan',           'total'=>70000, 'paid'=>70000, 'days_ago'=>30,'due_days'=>-5,'notes'=>'Hutang holder motor & pulsa internet'],
            ['code'=>'DBT00036','name'=>'Wahyu (Pelajar SMA)',              'contact'=>'081234110036','address'=>'Jl. Merdeka No. 15',               'total'=>45000, 'paid'=>45000, 'days_ago'=>40,'due_days'=>-10,'notes'=>'Hutang antigores hydrogel & casing HP'],
            ['code'=>'DBT00037','name'=>'Bu Dewi (Guru SDN 01)',            'contact'=>'081234110037','address'=>'Jl. Merdeka Barat No. 12',          'total'=>220000,'paid'=>220000,'days_ago'=>35,'due_days'=>-15,'notes'=>'Hutang charger original Samsung & memory card 64GB'],
            ['code'=>'DBT00038','name'=>'Anton (Supir Travel)',             'contact'=>'081234110038','address'=>'Pool Travel Pahlawan',              'total'=>135000,'paid'=>135000,'days_ago'=>28,'due_days'=>-10,'notes'=>'Hutang pulsa darurat & holder dashboard mobil'],
            ['code'=>'DBT00039','name'=>'Farah (Freelance Designer)',       'contact'=>'081234110039','address'=>'Jl. Pahlawan No. 88',              'total'=>890000,'paid'=>890000,'days_ago'=>45,'due_days'=>-20,'notes'=>'Hutang servis ganti LCD iPhone 11 & tempered glass'],
            ['code'=>'DBT00040','name'=>'Pak Ujang (Pedagang Buah)',        'contact'=>'081234110040','address'=>'Pasar Pahlawan Kios 12',            'total'=>175000,'paid'=>175000,'days_ago'=>50,'due_days'=>-25,'notes'=>'Hutang kabel USB Lightning & pulsa token listrik'],
            ['code'=>'DBT00041','name'=>'Gilang (Anak Band)',               'contact'=>'081234110041','address'=>'Studio Musik Merdeka No. 7',        'total'=>320000,'paid'=>320000,'days_ago'=>32,'due_days'=>-12,'notes'=>'Hutang ganti LCD Xiaomi & kabel audio aux premium'],
            ['code'=>'DBT00042','name'=>'Ririn (Karyawati Kafe)',           'contact'=>'081234110042','address'=>'Kafe Kopi Senja Jl. Pahlawan No. 18','total'=>95000,'paid'=>95000, 'days_ago'=>27,'due_days'=>-8,'notes'=>'Hutang casing silikon & headset kabel original'],
            ['code'=>'DBT00043','name'=>'Pak Sukri (Tukang Bangunan)',      'contact'=>'081234110043','address'=>'Jl. Kenanga Gg. 5 No. 2',           'total'=>60000, 'paid'=>60000, 'days_ago'=>22,'due_days'=>-7,'notes'=>'Hutang pulsa Indosat & voucher internet'],
            ['code'=>'DBT00044','name'=>'Dina (Bidan Puskesmas)',           'contact'=>'081234110044','address'=>'Puskesmas Pahlawan Lt. 1',           'total'=>150000,'paid'=>150000,'days_ago'=>18,'due_days'=>-6,'notes'=>'Hutang softcase & tempered glass iPhone XR'],
            ['code'=>'DBT00045','name'=>'Agung (Pegawai Bank)',             'contact'=>'081234110045','address'=>'Bank BRI Jl. Merdeka No. 5',        'total'=>200000,'paid'=>200000,'days_ago'=>25,'due_days'=>-9,'notes'=>'Hutang servis speaker Samsung & baterai'],
            ['code'=>'DBT00046','name'=>'Endang (Pedagang Sembako)',        'contact'=>'081234110046','address'=>'Toko Sembako Jl. Pahlawan No. 62', 'total'=>75000, 'paid'=>75000, 'days_ago'=>20,'due_days'=>-5,'notes'=>'Hutang token listrik & voucher Smartfren'],
            ['code'=>'DBT00047','name'=>'Feri (Montir Bengkel)',            'contact'=>'081234110047','address'=>'Bengkel Motor Jaya Jl. Kenanga',    'total'=>185000,'paid'=>185000,'days_ago'=>33,'due_days'=>-13,'notes'=>'Hutang earphone & kabel data Type-C fast charge'],
            ['code'=>'DBT00048','name'=>'Gita (Pegawai Toko Kain)',         'contact'=>'081234110048','address'=>'Toko Batik Mawar Jl. Pahlawan 77', 'total'=>90000, 'paid'=>90000, 'days_ago'=>15,'due_days'=>-5,'notes'=>'Hutang kartu perdana Indosat & pulsa 20rb'],
            ['code'=>'DBT00049','name'=>'Herman (Security Kantor)',         'contact'=>'081234110049','address'=>'Kantor PT. Maju Jl. Industri No. 1','total'=>120000,'paid'=>120000,'days_ago'=>38,'due_days'=>-18,'notes'=>'Hutang charger kepala & kabel micro USB nylon'],
            ['code'=>'DBT00050','name'=>'Ira (Perawat RS)',                 'contact'=>'081234110050','address'=>'RS Sehat Jl. Pahlawan No. 1',       'total'=>250000,'paid'=>250000,'days_ago'=>42,'due_days'=>-17,'notes'=>'Hutang servis ganti LCD Xiaomi & sparepart konektor'],
        ];

        foreach ($debts as $d) {
            $debtDate = now()->subDays($d['days_ago'])->setTime(14, 0, 0);
            $dueDate  = (clone $debtDate)->addDays($d['days_ago'] + $d['due_days']);
            $status   = $d['paid'] >= $d['total'] ? 'paid' : ($d['paid'] > 0 ? 'partial' : 'unpaid');

            Debt::updateOrCreate(
                ['debt_code' => $d['code']],
                [
                    'store_id'         => $store?->id,
                    'transaction_code' => null,
                    'debtor_name'      => $d['name'],
                    'debtor_contact'   => $d['contact'],
                    'debtor_address'   => $d['address'],
                    'total_amount'     => $d['total'],
                    'paid_amount'      => $d['paid'],
                    'remaining_amount' => $d['total'] - $d['paid'],
                    'debt_date'        => $debtDate,
                    'due_date'         => $dueDate,
                    'status'           => $status,
                    'notes'            => $d['notes'] ?? null,
                ]
            );
        }

        app()->forgetInstance('current_store');
    }
}
