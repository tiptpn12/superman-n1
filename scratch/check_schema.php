<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$tableName = 'master_cetak_bukti_kas';
$columns = DB::select("SHOW COLUMNS FROM $tableName");

echo json_encode($columns, JSON_PRETTY_PRINT);
