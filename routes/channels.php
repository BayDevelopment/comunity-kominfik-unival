<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('pendaftaran-anggota', function ($user) {
    return $user !== null;
});

Broadcast::channel('kerjasama', function ($user) {
    return $user !== null;
});
