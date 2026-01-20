<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\fck;

// اختبار الاستعلام
$result = fck::where('moh_id', 1)->where('fctypesid', 1)->get();
echo "Found " . $result->count() . " institutions\n";

if ($result->count() > 0) {
    echo "First institution: " . $result->first()->Fckname . "\n";
    echo "Sample data: " . json_encode($result->first()->toArray()) . "\n";
}

// اختبار مع مؤشرات مختلفة
$all_mohs = App\Models\mohs::all();
echo "\nAvailable MOHs:\n";
foreach ($all_mohs->take(5) as $moh) {
    echo "ID: {$moh->id}, Name: {$moh->mohname}\n";
}

$all_fctypes = App\Models\fctypes::all();
echo "\nAvailable FCTypes:\n";
foreach ($all_fctypes->take(5) as $fctype) {
    echo "ID: {$fctype->id}, Name: {$fctype->Fname}\n";
}
