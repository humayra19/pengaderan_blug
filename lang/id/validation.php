<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Baris Bahasa untuk Validasi
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut berisi pesan standar yang digunakan oleh kelas
    | validasi. Beberapa aturan memiliki beberapa versi seperti aturan ukuran.
    |
    */

    'accepted'             => ':Attribute harus diterima.',
    'accepted_if'          => ':Attribute harus diterima ketika :other adalah :value.',
    'active_url'           => ':Attribute bukan URL yang valid.',
    'after'                => ':Attribute harus tanggal setelah :date.',
    'after_or_equal'       => ':Attribute harus tanggal setelah atau sama dengan :date.',
    'alpha'                => ':Attribute hanya boleh berisi huruf.',
    'alpha_dash'           => ':Attribute hanya boleh berisi huruf, angka, setrip, dan garis bawah.',
    'alpha_num'            => ':Attribute hanya boleh berisi huruf dan angka.',
    'array'                => ':Attribute harus berupa array.',
    'ascii'                => ':Attribute hanya boleh berisi karakter alfanumerik dan simbol byte tunggal.',
    'before'               => ':Attribute harus tanggal sebelum :date.',
    'before_or_equal'      => ':Attribute harus tanggal sebelum atau sama dengan :date.',
    'between'              => [
        'array'   => ':Attribute harus memiliki antara :min dan :max item.',
        'file'    => ':Attribute harus berukuran antara :min dan :max kilobita.',
        'numeric' => ':Attribute harus bernilai antara :min dan :max.',
        'string'  => ':Attribute harus berisi antara :min dan :max karakter.',
    ],
    'boolean'              => 'Field :attribute harus bernilai benar atau salah.',
    'can'                  => 'Field :attribute berisi nilai yang tidak diizinkan.',
    'confirmed'            => 'Konfirmasi :attribute tidak cocok.',
    'contains'             => 'Field :attribute tidak memiliki nilai yang dibutuhkan.',
    'current_password'     => 'Password lama tidak sesuai.',
    'date'                 => ':Attribute bukan tanggal yang valid.',
    'date_equals'          => ':Attribute harus berupa tanggal yang sama dengan :date.',
    'date_format'          => ':Attribute tidak cocok dengan format :format.',
    'decimal'              => ':Attribute harus memiliki :decimal tempat desimal.',
    'declined'             => ':Attribute harus ditolak.',
    'declined_if'          => ':Attribute harus ditolak ketika :other adalah :value.',
    'different'            => ':Attribute dan :other harus berbeda.',
    'digits'               => ':Attribute harus terdiri dari :digits digit.',
    'digits_between'       => ':Attribute harus terdiri dari :min sampai :max digit.',
    'dimensions'           => ':Attribute memiliki dimensi gambar yang tidak valid.',
    'distinct'             => 'Field :attribute memiliki nilai duplikat.',
    'doesnt_end_with'      => ':Attribute tidak boleh diakhiri dengan salah satu dari: :values.',
    'doesnt_start_with'    => ':Attribute tidak boleh diawali dengan salah satu dari: :values.',
    'email'                => ':Attribute harus berupa alamat email yang valid.',
    'ends_with'            => ':Attribute harus diakhiri salah satu dari berikut: :values.',
    'enum'                 => ':Attribute yang dipilih tidak valid.',
    'exists'               => ':Attribute yang dipilih tidak valid.',
    'extensions'           => 'Field :attribute harus memiliki salah satu ekstensi: :values.',
    'file'                 => ':Attribute harus berupa sebuah berkas.',
    'filled'               => 'Field :attribute harus memiliki nilai.',
    'gt'                   => [
        'array'   => ':Attribute harus memiliki lebih dari :value item.',
        'file'    => ':Attribute harus berukuran lebih dari :value kilobita.',
        'numeric' => ':Attribute harus lebih besar dari :value.',
        'string'  => ':Attribute harus berisi lebih dari :value karakter.',
    ],
    'gte'                  => [
        'array'   => ':Attribute harus memiliki :value item atau lebih.',
        'file'    => ':Attribute harus berukuran lebih besar dari atau sama dengan :value kilobita.',
        'numeric' => ':Attribute harus lebih besar dari atau sama dengan :value.',
        'string'  => ':Attribute harus berisi lebih dari atau sama dengan :value karakter.',
    ],
    'hex_color'            => 'Field :attribute harus berupa warna heksadesimal yang valid.',
    'image'                => ':Attribute harus berupa gambar.',
    'in'                   => ':Attribute yang dipilih tidak valid.',
    'in_array'             => 'Field :attribute tidak ada di dalam :other.',
    'integer'              => ':Attribute harus berupa bilangan bulat.',
    'ip'                   => ':Attribute harus berupa alamat IP yang valid.',
    'ipv4'                 => ':Attribute harus berupa alamat IPv4 yang valid.',
    'ipv6'                 => ':Attribute harus berupa alamat IPv6 yang valid.',
    'json'                 => ':Attribute harus berupa string JSON yang valid.',
    'list'                 => 'Field :attribute harus berupa daftar.',
    'lowercase'            => ':Attribute harus berupa huruf kecil.',
    'lt'                   => [
        'array'   => ':Attribute harus memiliki kurang dari :value item.',
        'file'    => ':Attribute harus berukuran kurang dari :value kilobita.',
        'numeric' => ':Attribute harus kurang dari :value.',
        'string'  => ':Attribute harus berisi kurang dari :value karakter.',
    ],
    'lte'                  => [
        'array'   => ':Attribute tidak boleh memiliki lebih dari :value item.',
        'file'    => ':Attribute harus berukuran kurang dari atau sama dengan :value kilobita.',
        'numeric' => ':Attribute harus kurang dari atau sama dengan :value.',
        'string'  => ':Attribute harus berisi kurang dari atau sama dengan :value karakter.',
    ],
    'mac_address'          => ':Attribute harus berupa alamat MAC yang valid.',
    'max'                  => [
        'array'   => ':Attribute tidak boleh memiliki lebih dari :max item.',
        'file'    => ':Attribute tidak boleh berukuran lebih dari :max kilobita.',
        'numeric' => ':Attribute tidak boleh lebih besar dari :max.',
        'string'  => ':Attribute maksimal :max karakter.',
    ],
    'max_digits'           => ':Attribute tidak boleh memiliki lebih dari :max digit.',
    'mimes'                => ':Attribute harus berupa berkas berformat: :values.',
    'mimetypes'            => ':Attribute harus berupa berkas berformat: :values.',
    'min'                  => [
        'array'   => ':Attribute harus memiliki minimal :min item.',
        'file'    => ':Attribute harus berukuran minimal :min kilobita.',
        'numeric' => ':Attribute harus minimal :min.',
        'string'  => ':Attribute minimal :min karakter.',
    ],
    'min_digits'           => ':Attribute harus memiliki minimal :min digit.',
    'missing'              => 'Field :attribute harus tidak ada.',
    'missing_if'           => 'Field :attribute harus tidak ada ketika :other adalah :value.',
    'missing_unless'       => 'Field :attribute harus tidak ada kecuali :other adalah :value.',
    'missing_with'         => 'Field :attribute harus tidak ada ketika :values ada.',
    'missing_with_all'     => 'Field :attribute harus tidak ada ketika :values ada.',
    'multiple_of'          => ':Attribute harus merupakan kelipatan dari :value.',
    'not_in'               => ':Attribute yang dipilih tidak valid.',
    'not_regex'            => 'Format :attribute tidak valid.',
    'numeric'              => ':Attribute harus berupa angka.',
    'password'             => [
        'letters'       => ':Attribute harus berisi setidaknya satu huruf.',
        'mixed'         => ':Attribute harus berisi setidaknya satu huruf besar dan satu huruf kecil.',
        'numbers'       => ':Attribute harus berisi setidaknya satu angka.',
        'symbols'       => ':Attribute harus berisi setidaknya satu simbol.',
        'uncompromised' => ':Attribute yang diberikan telah bocor dalam kebocoran data. Silakan pilih :attribute yang berbeda.',
    ],
    'present'              => 'Field :attribute harus ada.',
    'present_if'           => 'Field :attribute harus ada ketika :other adalah :value.',
    'present_unless'       => 'Field :attribute harus ada kecuali :other adalah :value.',
    'present_with'         => 'Field :attribute harus ada ketika :values ada.',
    'present_with_all'     => 'Field :attribute harus ada ketika :values ada.',
    'prohibited'           => 'Field :attribute tidak diizinkan.',
    'prohibited_if'        => 'Field :attribute tidak diizinkan ketika :other adalah :value.',
    'prohibited_unless'    => 'Field :attribute tidak diizinkan kecuali :other ada di dalam :values.',
    'prohibits'            => 'Field :attribute melarang :other untuk ada.',
    'regex'                => 'Format :attribute tidak valid.',
    'required'             => ':Attribute wajib diisi.',
    'required_array_keys'  => 'Field :attribute harus berisi entri untuk: :values.',
    'required_if'          => ':Attribute wajib diisi ketika :other adalah :value.',
    'required_if_accepted' => ':Attribute wajib diisi ketika :other diterima.',
    'required_if_declined' => ':Attribute wajib diisi ketika :other ditolak.',
    'required_unless'      => ':Attribute wajib diisi kecuali :other ada di dalam :values.',
    'required_with'        => ':Attribute wajib diisi ketika :values ada.',
    'required_with_all'    => ':Attribute wajib diisi ketika :values ada.',
    'required_without'     => ':Attribute wajib diisi ketika :values tidak ada.',
    'required_without_all' => ':Attribute wajib diisi ketika tidak ada satu pun dari :values yang ada.',
    'same'                 => ':Attribute dan :other harus cocok.',
    'size'                 => [
        'array'   => ':Attribute harus mengandung :size item.',
        'file'    => ':Attribute harus berukuran :size kilobita.',
        'numeric' => ':Attribute harus berukuran :size.',
        'string'  => ':Attribute harus berukuran :size karakter.',
    ],
    'starts_with'          => ':Attribute harus diawali salah satu dari berikut: :values.',
    'string'               => ':Attribute harus berupa string.',
    'timezone'             => ':Attribute harus berupa zona waktu yang valid.',
    'unique'               => ':Attribute sudah digunakan oleh akun lain.',
    'uploaded'             => ':Attribute gagal diunggah.',
    'uppercase'            => ':Attribute harus berupa huruf besar.',
    'url'                  => 'Format :attribute tidak valid.',
    'ulid'                 => ':Attribute harus berupa ULID yang valid.',
    'uuid'                 => ':Attribute harus berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Pesan Validasi Kustom
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat menentukan pesan validasi kustom untuk atribut
    | menggunakan konvensi "attribute.rule" untuk menamai baris.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Nama Atribut Kustom
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut digunakan untuk menukar placeholder atribut kami
    | dengan sesuatu yang lebih mudah dibaca pembaca seperti "Alamat E-Mail"
    | daripada "email". Ini sangat membantu membuat pesan lebih bersih.
    |
    */

    'attributes' => [
        'name'                  => 'nama lengkap',
        'email'                 => 'email',
        'password'              => 'password',
        'password_confirmation' => 'konfirmasi password',
        'current_password'      => 'password lama',
        'phone'                 => 'nomor WhatsApp',
        'nim'                   => 'NIM',
        'study_program'         => 'program studi',
        'batch_year'            => 'angkatan',
        'interest'              => 'bidang minat',
        'motivation'            => 'motivasi bergabung',
        'experience'            => 'pengalaman atau skill',
        'decision'              => 'keputusan',
        'admin_note'            => 'catatan admin',
    ],

];

