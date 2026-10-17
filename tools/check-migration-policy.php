<?php

declare(strict_types=1);

// Ihambing ang migration files ng kasalukuyang PR sa base branch.
// Default ay origin/main, pero puwede ring magbigay ng ibang branch bilang argument.
$baseRef = $argv[1] ?? 'origin/main';

$command = 'git diff --name-status --find-renames '
    . escapeshellarg($baseRef)
    . '...HEAD -- database/migrations';

exec($command, $changedFiles, $exitCode);

if ($exitCode !== 0) {
    fwrite(STDERR, "ERROR: Hindi maikumpara ang migrations sa {$baseRef}.\n");
    fwrite(STDERR, "Tiyaking na-fetch ang base branch at tama ang pangalan nito.\n");
    exit(2);
}

$violations = [];

foreach ($changedFiles as $line) {
    if (trim($line) === '') {
        continue;
    }

    // Karaniwang format: M<TAB>path, A<TAB>path, o R100<TAB>old<TAB>new
    $parts = explode("\t", $line);
    $status = $parts[0] ?? '';
    $changeType = $status[0] ?? '';

    // Ipagbawal ang pagbabago, pagbura, o pagpapalit ng pangalan
    // ng migration file na dati nang nasa base branch.
    if (in_array($changeType, ['M', 'D', 'R', 'T'], true)) {
        $paths = implode(' -> ', array_slice($parts, 1));
        $violations[] = "Bawal baguhin, burahin, o palitan ang pangalan ng lumang migration: {$paths}";
        continue;
    }

    // Suriin ang laman ng mga bagong migration file.
    if ($changeType !== 'A') {
        continue;
    }

    $path = $parts[1] ?? '';
    $source = $path !== '' ? @file_get_contents($path) : false;

    if ($source === false) {
        $violations[] = "Hindi mabasa ang bagong migration file: {$path}";
        continue;
    }

    $blockedPatterns = [
        '/->\s*change\s*\(/i'
        => 'May ->change(); baguhin ang existing column sa bagong migration ay bawal.',
        '/->\s*(dropColumn|renameColumn|dropConstrainedForeignId|dropForeign|dropIndex|dropUnique|dropPrimary|dropTimestamps|dropSoftDeletes)\s*\(/i'
        => 'May drop o rename operation; kailangan itong tanggihan o ipa-review.',
        '/Schema::\s*(drop|dropIfExists|rename)\s*\(/i'
        => 'May operation na nagtatanggal o nagpapalit ng pangalan ng table.',
        '/\bALTER\s+TABLE\b/i'
        => 'May raw ALTER TABLE SQL; kailangan itong tanggihan o ipa-review.',
        '/DB::\s*(statement|unprepared)\s*\(/i'
        => 'May raw SQL; ipa-review ito bago payagan.',
    ];

    foreach ($blockedPatterns as $pattern => $reason) {
        if (preg_match($pattern, $source) === 1) {
            $violations[] = "{$path}: {$reason}";
            break;
        }
    }
}

if ($violations !== []) {
    fwrite(STDERR, "Migration policy check FAILED:\n");

    foreach ($violations as $violation) {
        fwrite(STDERR, " - {$violation}\n");
    }

    exit(1);
}

echo "Migration policy check passed.\n";
