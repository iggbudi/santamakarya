<?php

namespace App\Services;

class ContentSchema
{
    public static function groups(): array
    {
        $text = fn ($label, $max = 100, $type = 'text') => compact('label', 'max', 'type');
        $media = fn ($label) => ['label' => $label, 'type' => 'media', 'max' => null];
        $groups = [
            'hero' => ['label' => 'Hero', 'fields' => [
                'headline' => $text('Judul utama'), 'description' => $text('Paragraf utama', 800, 'textarea'), 'badge' => $text('Badge'),
                'consultation_label' => $text('Label konsultasi'), 'visit_label' => $text('Label kunjungan'),
            ]],
            'about' => ['label' => 'Profil Tentang', 'fields' => [
                'heading' => $text('Judul'), 'eyebrow' => $text('Label bagian'), 'description' => $text('Paragraf utama', 800, 'textarea'), 'quote' => $text('Kutipan', 800, 'textarea'),
                'location_label' => $text('Label lokasi'), 'location_description' => $text('Keterangan lokasi', 300),
                'metrics.0.value' => $text('Nilai metrik pertama', 40), 'metrics.0.label' => $text('Label metrik pertama'),
                'metrics.1.value' => $text('Nilai metrik kedua', 40), 'metrics.1.label' => $text('Label metrik kedua'),
            ]],
            'services' => ['label' => 'Layanan', 'count' => 6, 'fields' => ['heading' => $text('Judul'), 'eyebrow' => $text('Label bagian'), 'description' => $text('Paragraf utama', 800, 'textarea')]],
            'advantages' => ['label' => 'Keunggulan', 'count' => 4, 'fields' => ['heading' => $text('Judul'), 'eyebrow' => $text('Label bagian')]],
            'workflow' => ['label' => 'Alur Kerja', 'count' => 5, 'fields' => ['heading' => $text('Judul'), 'eyebrow' => $text('Label bagian')]],
            'contact' => ['label' => 'CTA & Lokasi', 'fields' => [
                'cta_heading' => $text('Judul CTA'), 'cta_description' => $text('Paragraf CTA', 800, 'textarea'),
                'schedule_label' => $text('Label jadwal konsultasi'), 'visit_label' => $text('Label kunjungan'),
                'background_media_id' => $media('Gambar latar CTA'), 'location_heading' => $text('Judul lokasi'), 'location_eyebrow' => $text('Label bagian lokasi'),
                'location_description' => $text('Paragraf lokasi', 800, 'textarea'), 'business_label' => $text('Jenis usaha'), 'maps_label' => $text('Label tombol Maps'),
            ]],
            'footer' => ['label' => 'Footer', 'fields' => [
                'tagline' => $text('Tagline'), 'description' => $text('Paragraf footer', 800, 'textarea'), 'maps_name' => $text('Nama kantor di Maps'),
                'copyright' => $text('Teks hak cipta', 300),
            ]],
            'portfolio' => ['label' => 'Portofolio', 'fields' => ['heading' => $text('Judul'), 'eyebrow' => $text('Label bagian')]],
        ];
        foreach (['services' => 6, 'advantages' => 4, 'workflow' => 5] as $group => $count) {
            for ($i = 0; $i < $count; $i++) {
                $groups[$group]['fields']["items.$i.title"] = $text('Judul kartu '.($i + 1), 80);
                $groups[$group]['fields']["items.$i.description"] = $text('Deskripsi kartu '.($i + 1), 300, 'textarea');
                if ($group === 'services') {
                    $groups[$group]['fields']["items.$i.image_media_id"] = $media('Foto layanan '.($i + 1));
                    $groups[$group]['fields']["items.$i.alt_text"] = $text('Deskripsi gambar '.($i + 1), 255);
                }
            }
        }

        return $groups;
    }

    public static function rules(array $groups): array
    {
        $rules = [];
        foreach (self::groups() as $key => $group) {
            if (! in_array($key, $groups, true)) {
                continue;
            }
            if (isset($group['count'])) {
                $rules[$key.'.items'] = 'required|array|list|size:'.$group['count'];
            }
            if ($key === 'about') {
                $rules['about.metrics'] = 'required|array|list|size:2';
            }
            foreach ($group['fields'] as $path => $field) {
                $rules[$key.'.'.$path] = $field['type'] === 'media' ? 'nullable|integer|min:1|exists:media_assets,id' : 'required|string|max:'.$field['max'];
            }
        }

        return $rules;
    }
}
