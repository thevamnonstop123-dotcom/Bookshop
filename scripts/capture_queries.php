<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

$mode = $argv[1] ?? 'home';
$queries = [];
DB::listen(function($q) use (&$queries){
    $queries[] = ['sql'=>$q->sql,'bindings'=>$q->bindings,'time'=>$q->time];
});

if ($mode === 'home') {
    echo "--- Running HomeController (home) ---\n";
    $home = new App\Http\Controllers\HomeController();
    try { $home->index(); } catch (Throwable $e) { echo "HomeController error: " . $e->getMessage() . "\n"; }
} elseif ($mode === 'books') {
    echo "--- Running BookController::index (books) ---\n";
    $bookController = new App\Http\Controllers\Customer\BookController(new App\Services\Customer\BookService());
    $req = Request::create('/books','GET', []);
    try { $bookController->index($req); } catch (Throwable $e) { echo "BookController error: " . $e->getMessage() . "\n"; }
} else {
    echo "Unknown mode: $mode\n";
    exit(1);
}

// Output
echo "Captured Query Count: " . count($queries) . "\n\n";
$map = [];
foreach($queries as $i=>$q){
    $sql = $q['sql'];
    $map[$sql] = ($map[$sql] ?? 0) + 1;
    echo ($i+1) . ". [" . number_format($q['time'],2) . "ms] " . $sql . " -- " . json_encode($q['bindings']) . PHP_EOL;
}

echo "\nPotential N+1 candidates (repeated identical SQL):\n";
$found = false;
foreach($map as $sql => $count){
    if($count > 1){
        echo "- {$count}x -> " . $sql . PHP_EOL;
        $found = true;
    }
}
if (! $found) echo "None detected.\n";
