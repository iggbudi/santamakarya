<?php

namespace App\Services;

use App\Models\MediaAsset;
use App\Models\PageVersion;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PublicationService
{
    public function publish(User $actor): PageVersion
    {
        $this->authorize($actor);

        return DB::transaction(function () use ($actor) {
            $settings = SiteSetting::lockForUpdate()->findOrFail(1);
            $payload = app(CmsDraftService::class)->build();
            $this->validate($payload);

            return $this->activate($settings, $payload, $actor);
        });
    }

    public function restore(PageVersion $version, User $actor): PageVersion
    {
        $this->authorize($actor);

        return DB::transaction(function () use ($version, $actor) {
            $settings = SiteSetting::lockForUpdate()->findOrFail(1);
            $payload = PageVersion::findOrFail($version->id)->payload;
            $this->validate($payload);

            return $this->activate($settings, $payload, $actor);
        });
    }

    private function authorize(User $actor): void
    {
        if (! User::whereKey($actor->id)->where('is_admin', true)->exists()) {
            throw new AuthorizationException('Hanya admin dapat memublikasikan konten.');
        }
    }

    private function activate(SiteSetting $settings, array $payload, User $actor): PageVersion
    {
        $version = PageVersion::create(['payload' => $payload, 'created_by' => $actor->id]);
        $settings->update(['published_version_id' => $version->id]);

        return $version;
    }

    public function validate(array $payload): void
    {
        $rules = [
            'schema_version' => 'required|integer|in:1', 'settings' => 'required|array', 'slides' => 'required|array', 'portfolio' => 'required|array', 'media' => 'present|array',
            'slides.hero' => 'required|array|min:1|max:10', 'slides.about' => 'required|array|min:1|max:10',
            'slides.*.*.image_url' => ['required', 'string', 'max:2048'],
            'slides.*.*.alt_text' => 'present|nullable|string|max:255', 'slides.*.*.position_x' => 'required|integer|between:0,100', 'slides.*.*.position_y' => 'required|integer|between:0,100',
            'settings.hero.interval_ms' => 'required|integer|between:3000,10000', 'settings.about.interval_ms' => 'required|integer|between:3000,10000',
            'portfolio.projects' => 'present|array', 'portfolio.categories' => 'present|array',
            'portfolio.projects.*.title' => 'required|string|max:80', 'portfolio.projects.*.category_slug' => ['required', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'portfolio.projects.*.category_name' => 'required|string|max:80', 'portfolio.projects.*.image_url' => 'required|string|max:2048',
            'portfolio.projects.*.alt_text' => 'present|nullable|string|max:255', 'portfolio.projects.*.caption' => 'present|nullable|string|max:100',
            'portfolio.categories.*.name' => 'required|string|max:80', 'portfolio.categories.*.slug' => ['required', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'settings.contact.whatsapp_number' => ['required', 'regex:/^[1-9][0-9]{7,14}$/'],
        ];
        // Schema v1 follows the structured seed and the indexed fields required by Blade.
        $template = json_decode(file_get_contents(database_path('seeders/data/landing-content.json')), true, 512, JSON_THROW_ON_ERROR)['settings'];
        $this->settingRules($template, 'settings', $rules);
        foreach (ContentSchema::rules(array_keys(ContentSchema::groups())) as $key => $rule) {
            $rules['settings.'.$key] = $rule;
        }
        foreach (ContentSettingsService::contactRules() as $key => $rule) {
            $rules['settings.contact.'.$key] = $rule;
        }
        foreach (ContentSettingsService::seoRules() as $key => $rule) {
            $rules['settings.seo.'.$key] = $rule;
        }
        Validator::make($payload, $rules, [
            'required' => 'Bagian :attribute wajib diisi sebelum publikasi.',
            'array' => 'Bagian :attribute harus berupa daftar yang valid.',
            'size.array' => 'Bagian :attribute harus memuat :size item sesuai struktur halaman.',
            'max.array' => 'Bagian :attribute maksimal :max item.',
            'between.numeric' => 'Nilai :attribute harus di antara :min dan :max.',
        ], ['slides.hero' => 'foto Hero aktif', 'slides.about' => 'foto Tentang aktif'])->validate();
        $ids = app(MediaService::class)->referencedIds($payload);
        foreach ($ids as $id) {
            $asset = MediaAsset::find($id);
            if (! $asset || ! Storage::disk('public')->exists($asset->path)) {
                throw ValidationException::withMessages(['media' => 'Aset gambar #'.$id.' tidak tersedia. Unggah dan pilih gambar pengganti.']);
            }
        }
        $this->validateUrls($payload);
        $categories = array_column($payload['portfolio']['categories'], 'slug');
        foreach ($payload['portfolio']['projects'] as $project) {
            if (! in_array($project['category_slug'], $categories, true)) {
                throw ValidationException::withMessages(['portfolio' => 'Kategori proyek tidak ditemukan dalam snapshot.']);
            }
        }
    }

    private function settingRules(array $template, string $prefix, array &$rules): void
    {
        foreach ($template as $key => $value) {
            $path = $prefix.'.'.$key;
            if (isset($rules[$path])) {
                continue;
            }
            if (is_array($value)) {
                if (array_is_list($value)) {
                    // Section card counts belong to schema v1 and preserve the reference layout.
                    $rules[$path] = 'required|array|list|size:'.count($value);
                    $rules[$path.'.*'] = 'required|array';
                    $this->settingRules($value[0], $path.'.*', $rules);
                } else {
                    $rules[$path] = 'required|array';
                    $this->settingRules($value, $path, $rules);
                }
            } elseif (str_ends_with((string) $key, '_media_id')) {
                $rules[$path] = 'present|nullable|integer';
            } elseif ($key === 'brand_suffix') {
                $rules[$path] = 'present|nullable|string|max:100';
            } else {
                $rules[$path] = 'required|string|max:5000';
            }
        }
    }

    private function validateUrls(array $data, string $prefix = ''): void
    {
        foreach ($data as $key => $value) {
            $path = ltrim($prefix.'.'.$key, '.');
            if (is_array($value)) {
                $this->validateUrls($value, $path);

                continue;
            }
            if (! is_string($key) || ! str_ends_with($key, 'url') || ! $value) {
                continue;
            }
            $assetUrl = str_starts_with($value, rtrim(Storage::disk('public')->url(''), '/').'/media/');
            if ((! $assetUrl && ! filter_var($value, FILTER_VALIDATE_URL)) || preg_match('/[\x00-\x20\x7f\x27\x22\\\\]/', $value) || (! $assetUrl && ! str_starts_with($value, 'https://'))) {
                throw ValidationException::withMessages([$path => 'Gunakan URL HTTPS yang valid atau aset dari Pustaka Media.']);
            }
        }
    }
}
