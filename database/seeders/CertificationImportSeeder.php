<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class CertificationImportSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('data/certifications.json');

        if (!file_exists($jsonPath)) {
            $this->command->error('File not found: ' . $jsonPath);
            return;
        }

        $rows = json_decode(file_get_contents($jsonPath), true);

        if (!$rows) {
            $this->command->error('Failed to parse JSON file.');
            return;
        }

        $this->command->info('Deleting existing certifications...');
        DB::table('certifications')->delete();

        $this->command->info('Inserting ' . count($rows) . ' certifications...');

        $now = now()->toDateTimeString();
        $chunks = array_chunk($rows, 100);

        foreach ($chunks as $i => $chunk) {
            $insert = array_map(function ($row) use ($now) {
                return [
                    'title'          => $row['title'],
                    'slug'           => $row['slug'],
                    'source_url'     => Arr::get($row, 'source_url'),
                    'description'    => Arr::get($row, 'description'),
                    'provider'       => Arr::get($row, 'provider'),
                    'logo'           => Arr::get($row, 'logo'),
                    'type'           => $row['type'],
                    'level'          => Arr::get($row, 'level'),
                    'durasi'         => Arr::get($row, 'durasi'),
                    'durasi_detik'   => $this->toInt(Arr::get($row, 'durasi_detik')),
                    'tanggal_rilis'  => Arr::get($row, 'tanggal_rilis'),
                    'rating'         => $this->toFloatOrNull(Arr::get($row, 'rating')),
                    'jumlah_rating'  => $this->toIntOrNull(Arr::get($row, 'jumlah_rating')),
                    'jumlah_learner' => $this->toIntOrNull(Arr::get($row, 'jumlah_learner')),
                    'skills'         => Arr::get($row, 'skills'),
                    'konten_path'    => Arr::get($row, 'konten_path'),
                    'jumlah_course'  => $this->toInt(Arr::get($row, 'jumlah_course', 1)),
                    'is_published'   => (bool) Arr::get($row, 'is_published', true),
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }, $chunk);

            DB::table('certifications')->insert($insert);
            $this->command->line('  Chunk ' . ($i + 1) . '/' . count($chunks) . ' inserted.');
        }

        $total = DB::table('certifications')->count();
        $this->command->info("Done! Total certifications in DB: {$total}");
    }

    private function toInt(mixed $value): int
    {
        return (int) preg_replace('/[^0-9-]/', '', (string) $value);
    }

    private function toIntOrNull(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $this->toInt($value);
    }

    private function toFloatOrNull(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }
}
