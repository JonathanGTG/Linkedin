<?php

declare(strict_types=1);

final class XlsxWorkbookLite
{
    private const MAIN_NS = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';

    private ZipArchive $zip;
    private array $sheetPaths = [];
    private array $sharedStrings = [];

    public function __construct(private readonly string $path)
    {
        $this->zip = new ZipArchive();
        if ($this->zip->open($this->path) !== true) {
            throw new RuntimeException("Tidak bisa membuka workbook: {$this->path}");
        }

        $this->loadSheetPaths();
        $this->loadSharedStrings();
    }

    public function sheetNames(): array
    {
        return array_keys($this->sheetPaths);
    }

    public function headers(string $sheetName): array
    {
        foreach ($this->rawRows($sheetName) as $row) {
            if ($row !== []) {
                return $row;
            }
        }

        return [];
    }

    public function sampleDataRow(string $sheetName): array
    {
        $headers = null;
        foreach ($this->rawRows($sheetName) as $row) {
            if ($row === []) {
                continue;
            }

            if ($headers === null) {
                $headers = $row;
                continue;
            }

            return $row;
        }

        return [];
    }

    public function countDataRows(string $sheetName): int
    {
        $headers = null;
        $count = 0;

        foreach ($this->rawRows($sheetName) as $row) {
            if ($headers === null) {
                $headers = $row;
                continue;
            }

            if ($row === []) {
                continue;
            }

            $count++;
        }

        return $count;
    }

    private function rawRows(string $sheetName): iterable
    {
        $path = $this->sheetPaths[$sheetName] ?? null;
        if (! $path) {
            throw new RuntimeException("Sheet tidak ditemukan: {$sheetName}");
        }

        $stream = $this->zip->getStream($path);
        if (! $stream) {
            throw new RuntimeException("Tidak bisa membaca sheet: {$sheetName}");
        }

        $reader = new XMLReader();
        $reader->XML(stream_get_contents($stream));

        while ($reader->read()) {
            if ($reader->nodeType !== XMLReader::ELEMENT || $reader->name !== 'row') {
                continue;
            }

            yield $this->readRow($reader);
        }

        $reader->close();
    }

    private function loadSheetPaths(): void
    {
        $workbookXml = $this->xml('xl/workbook.xml');
        $relsXml = $this->xml('xl/_rels/workbook.xml.rels');

        $rels = [];
        foreach ($relsXml->Relationship as $relationship) {
            $attributes = $relationship->attributes();
            $rels[(string) $attributes['Id']] = $this->normalizeRelationshipTarget((string) $attributes['Target']);
        }

        foreach ($workbookXml->sheets->sheet as $sheet) {
            $attributes = $sheet->attributes();
            $relAttributes = $sheet->attributes('r', true);
            $this->sheetPaths[(string) $attributes['name']] = $rels[(string) $relAttributes['id']] ?? null;
        }
    }

    private function loadSharedStrings(): void
    {
        $xml = $this->zip->getFromName('xl/sharedStrings.xml');
        if ($xml === false) {
            return;
        }

        $reader = new XMLReader();
        $reader->XML($xml);
        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::ELEMENT && $reader->name === 'si') {
                $node = new SimpleXMLElement($reader->readOuterXML());
                $this->sharedStrings[] = $this->flattenText($node);
            }
        }
        $reader->close();
    }

    private function normalizeRelationshipTarget(string $target): string
    {
        $target = str_replace('\\', '/', $target);

        if (str_starts_with($target, '/')) {
            return ltrim($target, '/');
        }

        return 'xl/'.ltrim($target, '/');
    }

    private function readRow(XMLReader $reader): array
    {
        $rowXml = $reader->readOuterXML();
        $row = new SimpleXMLElement($rowXml);
        $values = [];

        $row->registerXPathNamespace('m', self::MAIN_NS);

        foreach ($row->xpath('m:c') ?: [] as $cell) {
            $attributes = $cell->attributes();
            $reference = (string) ($attributes['r'] ?? '');
            $index = $this->columnIndex($reference);
            $values[$index] = $this->cellValue($cell);
        }

        if ($values === []) {
            return [];
        }

        $max = max(array_keys($values));
        $normalized = [];
        for ($i = 0; $i <= $max; $i++) {
            $normalized[] = $values[$i] ?? null;
        }

        return $normalized;
    }

    private function cellValue(SimpleXMLElement $cell): mixed
    {
        $attributes = $cell->attributes();
        $type = (string) ($attributes['t'] ?? '');

        if ($type === 's') {
            $index = (int) $cell->v;
            return $this->sharedStrings[$index] ?? null;
        }

        if ($type === 'inlineStr') {
            return $this->flattenText($cell);
        }

        $cell->registerXPathNamespace('m', self::MAIN_NS);
        $values = $cell->xpath('m:v');
        $value = $values ? (string) $values[0] : null;
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? $value + 0 : $value;
    }

    private function columnIndex(string $reference): int
    {
        preg_match('/^[A-Z]+/', $reference, $matches);
        $letters = $matches[0] ?? 'A';
        $index = 0;
        foreach (str_split($letters) as $letter) {
            $index = $index * 26 + (ord($letter) - 64);
        }

        return $index - 1;
    }

    private function flattenText(SimpleXMLElement $node): string
    {
        $node->registerXPathNamespace('m', self::MAIN_NS);
        $parts = [];
        foreach ($node->xpath('.//m:t') ?: [] as $text) {
            $parts[] = (string) $text;
        }

        return implode('', $parts);
    }

    private function xml(string $path): SimpleXMLElement
    {
        $xml = $this->zip->getFromName($path);
        if ($xml === false) {
            throw new RuntimeException("File XLSX tidak lengkap: {$path}");
        }

        return new SimpleXMLElement($xml);
    }
}

function parseDbmlTables(string $dbmlPath): array
{
    $content = file_get_contents($dbmlPath);
    if ($content === false) {
        throw new RuntimeException("Tidak bisa membaca DBML: {$dbmlPath}");
    }

    preg_match_all('/^Table\\s+([A-Za-z0-9_]+)\\s*\\{/m', $content, $matches);

    return array_values(array_unique($matches[1] ?? []));
}

function normalizeSheetNameToTables(string $sheetName): array
{
    $base = preg_replace('/^\\d+_/', '', $sheetName) ?: $sheetName;

    return match ($base) {
        'course_skill' => ['course_skills'],
        'course_instructor' => ['course_instructors'],
        'videos' => ['lessons'],
        'includes' => ['include_types', 'course_includes'],
        default => [$base],
    };
}


// Only run CLI when called directly
if (basename($argv[0] ?? '') === basename(__FILE__)) {

$args = $argv;
array_shift($args);

$inspect = false;
if (($args[0] ?? null) === '--inspect') {
    $inspect = true;
    array_shift($args);
}

[$dbmlPath, $xlsxPath] = array_pad($args, 2, null);
if (! is_string($dbmlPath) || $dbmlPath === '' || ! is_string($xlsxPath) || $xlsxPath === '') {
    fwrite(STDERR, "Usage: php audit_dbml_xlsx.php [--inspect] <path_dbml> <path_xlsx>\n");
    exit(2);
}

$dbmlPath = str_replace('\\', '/', $dbmlPath);
$xlsxPath = str_replace('\\', '/', $xlsxPath);

if (! is_file($dbmlPath)) {
    fwrite(STDERR, "DBML tidak ditemukan: {$dbmlPath}\n");
    exit(2);
}

if (! is_file($xlsxPath)) {
    fwrite(STDERR, "XLSX tidak ditemukan: {$xlsxPath}\n");
    exit(2);
}

$tables = parseDbmlTables($dbmlPath);
sort($tables);

$wb = new XlsxWorkbookLite($xlsxPath);
$sheetNames = $wb->sheetNames();
sort($sheetNames);

if ($inspect) {
    foreach ($sheetNames as $sheetName) {
        $headers = $wb->headers($sheetName);
        $sample = $wb->sampleDataRow($sheetName);
        echo "Sheet: {$sheetName}\n";
        echo "Headers: ".implode(' | ', array_map(static fn ($v) => (string) $v, $headers))."\n";
        echo "Sample: ".implode(' | ', array_map(static fn ($v) => (string) $v, $sample))."\n\n";
    }
}

$coveredTables = [];
$coveredButEmptyTables = [];

foreach ($sheetNames as $sheetName) {
    $rows = $wb->countDataRows($sheetName);
    $mappedTables = normalizeSheetNameToTables($sheetName);
    foreach ($mappedTables as $table) {
        if ($rows > 0) {
            $coveredTables[$table] = true;
        } else {
            $coveredButEmptyTables[$table] = true;
        }
    }
}

$coveredTableNames = array_keys($coveredTables);
sort($coveredTableNames);

$missingTables = array_values(array_diff($tables, array_unique(array_merge(array_keys($coveredTables), array_keys($coveredButEmptyTables)))));
sort($missingTables);

$coveredButEmptyTableNames = array_keys($coveredButEmptyTables);
sort($coveredButEmptyTableNames);

echo "DBML tables: ".count($tables)."\n";
echo "XLSX sheets: ".count($sheetNames)."\n\n";

echo "Tabel DBML yang punya data di XLSX (covered): ".count($coveredTableNames)."\n";
foreach ($coveredTableNames as $name) {
    echo "- {$name}\n";
}
echo "\n";

echo "Tabel DBML yang ter-cover tapi 0 data row (empty): ".count($coveredButEmptyTableNames)."\n";
foreach ($coveredButEmptyTableNames as $name) {
    echo "- {$name}\n";
}
echo "\n";

echo "Tabel DBML yang belum ada data di XLSX (missing): ".count($missingTables)."\n";
foreach ($missingTables as $name) {
    echo "- {$name}\n";
}

} // end if CLI guard
