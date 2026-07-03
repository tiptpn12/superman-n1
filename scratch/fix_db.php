<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement("ALTER TABLE master_cetak_bukti_kas MODIFY dibuat_sub_bagian VARCHAR(255) NULL");
    DB::statement("ALTER TABLE master_cetak_bukti_kas MODIFY dibuat_sub_bagian_nama VARCHAR(255) NULL");
    echo "SUCCESS: Columns are now nullable.";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
