<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute harus diterima.',
    'active_url'           => ':attribute bukan URL yang valid.',
    'after'                => ':attribute harus tanggal setelah :date.',
    'after_or_equal'       => ':attribute harus tanggal setelah atau sama dengan :date.',
    'alpha'                => ':attribute hanya boleh berisi huruf.',
    'alpha_dash'           => ':attribute hanya boleh berisi huruf, angka, dan strip.',
    'alpha_num'            => ':attribute hanya boleh berisi huruf dan angka.',
    'array'                => ':attribute harus berupa sebuah array.',
    'before'               => ':attribute harus tanggal sebelum :date.',
    'before_or_equal'      => ':attribute harus tanggal sebelum atau sama dengan :date.',
    'between'              => [
        'numeric' => ':attribute harus antara :min dan :max.',
        'file'    => ':attribute harus antara :min dan :max kilobytes.',
        'string'  => ':attribute harus antara :min dan :max karakter.',
        'array'   => ':attribute harus antara :min dan :max item.',
    ],
    'boolean'              => 'Kolom :attribute harus bernilai true atau false',
    'confirmed'            => 'Konfirmasi :attribute tidak cocok.',
    'date'                 => ':attribute bukan tanggal yang valid.',
    'date_equals'          => ':attribute harus tanggal yang sama dengan :date.',
    'date_format'          => ':attribute tidak cocok dengan format :format.',
    'different'            => ':attribute dan :other harus berbeda.',
    'digits'               => ':attribute harus berupa :digits angka.',
    'digits_between'       => ':attribute harus antara :min dan :max angka.',
    'dimensions'           => ':attribute memiliki dimensi gambar yang tidak valid.',
    'distinct'             => 'Kolom :attribute memiliki nilai yang duplikat.',
    'email'                => ':attribute harus berupa alamat email yang valid.',
    'ends_with'            => ':attribute harus diakhiri salah satu dari berikut: :values',
    'exists'               => ':attribute yang dipilih tidak valid.',
    'file'                 => ':attribute harus berupa sebuah file.',
    'filled'               => 'Kolom :attribute harus memiliki nilai.',
    'gt'                   => [
        'numeric' => ':attribute harus lebih besar dari :value.',
        'file'    => ':attribute harus lebih besar dari :value kilobytes.',
        'string'  => ':attribute harus lebih besar dari :value karakter.',
        'array'   => ':attribute harus memiliki lebih dari :value item.',
    ],
    'gte'                  => [
        'numeric' => ':attribute harus lebih besar dari atau sama dengan :value.',
        'file'    => ':attribute harus lebih besar dari atau sama dengan :value kilobytes.',
        'string'  => ':attribute harus lebih besar dari atau sama dengan :value karakter.',
        'array'   => ':attribute harus memiliki :value item atau lebih.',
    ],
    'image'                => ':attribute harus berupa gambar.',
    'in'                   => ':attribute yang dipilih tidak valid.',
    'in_array'             => 'Kolom :attribute tidak ada di :other.',
    'integer'              => ':attribute harus berupa bilangan bulat.',
    'ip'                   => ':attribute harus berupa alamat IP yang valid.',
    'ipv4'                 => ':attribute harus berupa alamat IPv4 yang valid.',
    'ipv6'                 => ':attribute harus berupa alamat IPv6 yang valid.',
    'json'                 => ':attribute harus berupa string JSON yang valid.',
    'lt'                   => [
        'numeric' => ':attribute harus kurang dari :value.',
        'file'    => ':attribute harus kurang dari :value kilobytes.',
        'string'  => ':attribute harus kurang dari :value karakter.',
        'array'   => ':attribute harus memiliki kurang dari :value item.',
    ],
    'lte'                  => [
        'numeric' => ':attribute harus kurang dari atau sama dengan :value.',
        'file'    => ':attribute harus kurang dari atau sama dengan :value kilobytes.',
        'string'  => ':attribute harus kurang dari atau sama dengan :value karakter.',
        'array'   => ':attribute tidak boleh memiliki lebih dari :value item.',
    ],
    'max'                  => [
        'numeric' => ':attribute tidak boleh lebih dari :max.',
        'file'    => ':attribute tidak boleh lebih dari :max kilobytes.',
        'string'  => ':attribute tidak boleh lebih dari :max karakter.',
        'array'   => ':attribute tidak boleh memiliki lebih dari :max item.',
    ],
    'mimes'                => ':attribute harus berupa file berjenis: :values.',
    'mimetypes'            => ':attribute harus berupa file berjenis: :values.',
    'min'                  => [
        'numeric' => ':attribute harus minimal :min.',
        'file'    => ':attribute harus minimal :min kilobytes.',
        'string'  => ':attribute harus minimal :min karakter.',
        'array'   => ':attribute harus memiliki minimal :min item.',
    ],
    'not_in'               => ':attribute yang dipilih tidak valid.',
    'not_regex'            => 'Format :attribute tidak valid.',
    'numeric'              => ':attribute harus berupa angka.',
    'password'             => 'Kata sandi salah.',
    'present'              => 'Kolom :attribute harus ada.',
    'regex'                => 'Format :attribute tidak valid.',
    'required'             => 'Kolom :attribute wajib diisi.',
    'required_if'          => 'Kolom :attribute wajib diisi bila :other adalah :value.',
    'required_unless'      => 'Kolom :attribute wajib diisi kecuali :other memiliki nilai :values.',
    'required_with'        => 'Kolom :attribute wajib diisi bila terdapat :values.',
    'required_with_all'    => 'Kolom :attribute wajib diisi bila terdapat :values.',
    'required_without'     => 'Kolom :attribute wajib diisi bila tidak terdapat :values.',
    'required_without_all' => 'Kolom :attribute wajib diisi bila tidak terdapat satupun :values.',
    'same'                 => ':attribute dan :other harus sama.',
    'size'                 => [
        'numeric' => ':attribute harus berukuran :size.',
        'file'    => ':attribute harus berukuran :size kilobyte.',
        'string'  => ':attribute harus berukuran :size karakter.',
        'array'   => ':attribute harus mengandung :size item.',
    ],
    'starts_with'          => ':attribute harus dimulai dengan: :values.',
    'string'               => ':attribute harus berupa string.',
    'timezone'             => ':attribute harus berupa zona waktu yang valid.',
    'unique'               => ':attribute sudah digunakan.',
    'uploaded'             => ':attribute gagal diunggah.',
    'url'                  => 'Format :attribute tidak valid.',
    'uuid'                 => ':attribute harus berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        // Umum
        'name' => 'Nama',
        'username' => 'Nama Pengguna',
        'email' => 'Email',
        'password' => 'Kata Sandi',
        'address' => 'Alamat',
        'notes' => 'Catatan',
        'contact' => 'Kontak',
        'phone_number' => 'Nomor Telepon',
        'phone_prefix' => 'Kode Telepon',
        'image' => 'Gambar',
        'status' => 'Status',
        'type' => 'Tipe',
        'role_id' => 'Peran (Role)',
        
        // Produk & Kategori
        'category_code' => 'Kategori',
        'supplier_code' => 'Supplier',
        'purchase_price' => 'Harga Beli',
        'selling_price' => 'Harga Jual',
        'minimum_stock' => 'Stok Minimum',
        'unit' => 'Satuan',
        'description' => 'Deskripsi',

        // Hutang (Debt)
        'debtor_name' => 'Nama Penghutang',
        'debtor_contact' => 'Kontak Penghutang',
        'debtor_address' => 'Alamat Penghutang',
        'total_amount' => 'Total Hutang',
        'amount' => 'Nominal Pembayaran',
        'debt_date' => 'Tanggal Hutang',
        'due_date' => 'Tanggal Jatuh Tempo',
        'payment_date' => 'Tanggal Pembayaran',
        'payment_method' => 'Metode Pembayaran',

        // Service Repairs
        'customer_name' => 'Nama Pelanggan',
        'customer_phone' => 'No. Telepon Pelanggan',
        'items' => 'Item',
        'items.*.name' => 'Nama Perangkat',
        'items.*.brand' => 'Merek',
        'items.*.series' => 'Seri/Model',
        'items.*.complaint' => 'Keluhan',
        'items.*.service_fee' => 'Biaya Jasa',
        'items.*.images.*' => 'Gambar',
        'items.*.diagnosis_result' => 'Hasil Diagnosis',
        'technician_id' => 'Teknisi',
        'down_payment' => 'Uang Muka (DP)',
        'parent_id' => 'Perangkat',
        'sparepart_type' => 'Tipe Sparepart',
        'product_code' => 'Produk',
        'item_name' => 'Nama Item',
        'quantity' => 'Jumlah',
        'unit_price' => 'Harga Satuan',

        // Product Purchases
        'source' => 'Sumber Pengadaan',
        'purchase_date' => 'Tanggal Pembelian',
        'estimated_arrival_date' => 'Estimasi Tiba',
        'items.*.product_code' => 'Produk',
        'items.*.temp_product_name' => 'Nama Produk Baru',
        'items.*.notes' => 'Catatan Produk',
        'items.*.quantity' => 'Jumlah',
        'items.*.purchase_price' => 'Harga Beli',
        'repair_item_id' => 'Item Service',
        'marketplace_name' => 'Nama Marketplace',
        'marketplace_seller' => 'Nama Toko/Seller',
        'marketplace_order_id' => 'Nomor Pesanan',
        'marketplace_notes' => 'Catatan Marketplace',
        'store_name' => 'Nama Toko',
        'receipt_number' => 'Nomor Resi/Struk',
        'offline_notes' => 'Catatan Offline',
        'other_source' => 'Sumber Lainnya',
        'other_notes' => 'Catatan Lainnya',
    ],

];
