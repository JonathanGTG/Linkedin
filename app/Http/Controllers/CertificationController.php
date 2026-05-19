<?php
namespace App\Http\Controllers;

use App\Models\Certification;
use Illuminate\Support\Str;

class CertificationController extends Controller
{
    public function index()
    {
        $sections = collect($this->typeMap())->map(function (array $meta) {
            $providers = Certification::published()
                ->where('type', $meta['db'])
                ->selectRaw('provider, COUNT(*) as total')
                ->groupBy('provider')
                ->orderBy('provider')
                ->get()
                ->map(fn ($row) => [
                    'name' => $row->provider,
                    'slug' => Str::slug($row->provider),
                    'total' => (int) $row->total,
                ]);

            return [
                'title' => $meta['title'],
                'desc' => $meta['desc'],
                'type_slug' => $meta['slug'],
                'providers' => $providers,
            ];
        })->values();

        return view('certifications.index', compact('sections'));
    }

    public function type(string $type)
    {
        $meta = $this->typeMeta($type);

        $certifications = Certification::published()
            ->where('type', $meta['db'])
            ->orderBy('provider')
            ->orderBy('title')
            ->get();

        $providers = $certifications
            ->groupBy('provider')
            ->map(fn ($items, $provider) => [
                'name' => $provider,
                'slug' => Str::slug($provider),
                'total' => $items->count(),
            ])
            ->values()
            ->sortBy('name')
            ->values();

        return view('certifications.type', [
            'meta' => $meta,
            'providers' => $providers,
            'certifications' => $certifications,
        ]);
    }

    public function provider(string $type, string $provider)
    {
        $meta = $this->typeMeta($type);

        $providers = Certification::published()
            ->where('type', $meta['db'])
            ->select('provider')
            ->distinct()
            ->orderBy('provider')
            ->get()
            ->pluck('provider');

        $providerName = $providers->first(fn ($name) => Str::slug($name) === $provider);

        abort_if(! $providerName, 404);

        $certifications = Certification::published()
            ->where('type', $meta['db'])
            ->where('provider', $providerName)
            ->orderBy('title')
            ->get();

        return view('certifications.provider', [
            'meta' => $meta,
            'providerName' => $providerName,
            'providerSlug' => $provider,
            'certifications' => $certifications,
        ]);
    }

    private function typeMap(): array
    {
        return [
            [
                'db' => 'professional_certificate',
                'slug' => 'professional-certificates',
                'title' => 'Professional Certificates',
                'desc' => 'Tunjukkan keahlian dengan sertifikat yang dikembangkan bersama Microsoft, Zendesk, Twilio, dan lainnya.',
            ],
            [
                'db' => 'certification_prep',
                'slug' => 'certification-preparation',
                'title' => 'Certification Preparation',
                'desc' => 'Persiapkan ujian sertifikasi dari CompTIA, Microsoft, AWS, (ISC)², Six Sigma, dan banyak lagi.',
            ],
            [
                'db' => 'practice_exam',
                'slug' => 'practice-exams',
                'title' => 'Certification Practice Exams',
                'desc' => 'Latihan soal untuk mengukur kesiapan dan membangun kepercayaan diri sebelum ujian sesungguhnya.',
            ],
            [
                'db' => 'continuing_education',
                'slug' => 'continuing-education',
                'title' => 'Continuing Education (CEU)',
                'desc' => 'Kredit pendidikan berkelanjutan untuk menjaga sertifikasi Anda tetap aktif.',
            ],
        ];
    }

    private function typeMeta(string $type): array
    {
        $meta = collect($this->typeMap())->firstWhere('slug', $type);
        abort_if(! $meta, 404);
        return $meta;
    }
}
