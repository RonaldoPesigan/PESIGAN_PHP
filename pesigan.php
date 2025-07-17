<?php
$input = "sample-log.txt";
$output = "output.txt";

if (!file_exists($input)) {
    die("Input file not found.\n");
}

$rawLines = file($input, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$entries = [];
$ids = [];
$usernames = [];

foreach ($rawLines as $entry) {
    $parts = preg_split('/\s+/', trim($entry));
    
    if (count($parts) < 5) {
        continue;
    }

    list($code, $user, $tx, $rx, $d, $t) = array_pad($parts, 6, null);

    $tx = number_format((int) $tx);
    $rx = number_format((int) $rx);

    $fullTime = "$d $t";
    $unix = strtotime($fullTime);
    $prettyTime = date("D, d F Y H:i:s", $unix);

    $entries[] = "$user|$tx|$rx|$prettyTime|$code";

    $ids[] = $code;
    $usernames[$user] = true;
}

sort($ids, SORT_NATURAL);
$sortedUsers = array_keys($usernames);
sort($sortedUsers, SORT_NATURAL);

$file = fopen($output, "w");

foreach ($entries as $e) {
    fwrite($file, $e . PHP_EOL);
}
fwrite($file, PHP_EOL);

foreach ($ids as $i) {
    fwrite($file, $i . PHP_EOL);
}
fwrite($file, PHP_EOL);

foreach ($sortedUsers as $i => $u) {
    fwrite($file, "[" . ($i + 1) . "] $u" . PHP_EOL);
}

fclose($file);
echo "Successfully wrote to output.txt\n";
?>
