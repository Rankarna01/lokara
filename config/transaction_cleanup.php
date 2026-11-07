<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Kredensial Menu Hapus Riwayat Transaksi (Super Admin)
    |--------------------------------------------------------------------------
    |
    | Dipakai untuk membuka menu "Hapus Riwayat Transaksi" di dashboard
    | super admin. Jika salah satu nilai kosong, menu tetap terkunci.
    |
    */

    'username' => env('TRANSACTION_CLEANUP_USERNAME'),

    'password' => env('TRANSACTION_CLEANUP_PASSWORD'),

];
