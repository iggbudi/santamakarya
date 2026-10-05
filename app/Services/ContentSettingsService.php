<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\Rules\GoogleMapsUrl;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ContentSettingsService
{
    public function updateContent(array $data): void
    {
        $groups = array_intersect(array_keys(ContentSchema::groups()), array_keys($data));
        if (! $groups) {
            throw ValidationException::withMessages(['content' => 'Pilih bagian konten yang akan disimpan.']);
        }
        $this->save(function (array $draft) use ($groups, $data) {
            Validator::make($data, ContentSchema::rules($groups))->validate();
            foreach ($groups as $key) {
                foreach (ContentSchema::groups()[$key]['fields'] as $path => $field) {
                    $value = Arr::get($data, $key.'.'.$path);
                    Arr::set($draft, $key.'.'.$path, $value);
                }
            }

            return $draft;
        });
    }

    public static function contactRules(): array
    {
        return [
            'whatsapp_name' => 'nullable|string|max:50',
            'whatsapp_number' => ['required', 'regex:/^[1-9][0-9]{7,14}$/'],
            'whatsapp_name_2' => 'nullable|string|max:50|required_with:whatsapp_number_2',
            'whatsapp_number_2' => ['nullable', 'regex:/^[1-9][0-9]{7,14}$/', 'required_with:whatsapp_name_2'],
            'phone_display_2' => 'nullable|string|max:100',
            'consultation_message' => 'required|string|max:800', 'address' => 'required|string|max:800', 'opening_hours' => 'required|string|max:100',
            'maps_url' => ['required', 'string', 'max:2048', new GoogleMapsUrl],
            'maps_embed_url' => ['required', 'string', 'max:2048', new GoogleMapsUrl(true)],
        ];
    }

    public static function seoRules(): array
    {
        return [
            'title' => 'required|string|max:70', 'description' => 'required|string|max:160', 'og_image_media_id' => 'nullable|integer|min:1|exists:media_assets,id',
        ];
    }

    public function updateContact(array $data): void
    {
        $values = Validator::make($data, self::contactRules())->validate();
        $this->save(function (array $draft) use ($values) {
            $normalize = fn ($v) => $v === '' ? null : $v;
            $draft['contact']['whatsapp_name'] = $normalize($draft['contact']['whatsapp_name'] ?? null);
            $draft['contact']['whatsapp_number_2'] = $normalize($draft['contact']['whatsapp_number_2'] ?? null);
            $draft['contact']['whatsapp_name_2'] = $normalize($draft['contact']['whatsapp_name_2'] ?? null);
            $draft['contact']['phone_display_2'] = $normalize($draft['contact']['phone_display_2'] ?? null);
            $contact = [...$draft['contact'], ...$values];
            $contact['whatsapp_name'] = $normalize($contact['whatsapp_name'] ?? null);
            $contact['phone_display'] = $draft['contact']['whatsapp_number'] === $values['whatsapp_number'] ? $draft['contact']['phone_display'] : '+'.$values['whatsapp_number'];
            $number2 = $normalize($contact['whatsapp_number_2'] ?? null);
            $name2 = $normalize($contact['whatsapp_name_2'] ?? null);
            if (empty($number2)) {
                $contact['whatsapp_number_2'] = null;
                $contact['whatsapp_name_2'] = null;
                $contact['phone_display_2'] = null;
            } else {
                $contact['whatsapp_number_2'] = $number2;
                $contact['whatsapp_name_2'] = $name2;
                if (($draft['contact']['whatsapp_number_2'] ?? null) !== $number2 || empty($draft['contact']['phone_display_2'] ?? null)) {
                    $contact['phone_display_2'] = '+'.$number2;
                }
            }
            $contact['footer_address'] = $values['address'];
            $contact['consultation_message_enabled'] = true;
            foreach (array_keys($contact['messages']) as $key) {
                $contact['messages'][$key] = $values['consultation_message'];
            }
            $draft['contact'] = $contact;

            return $draft;
        });
    }

    public function updateSeo(array $data): void
    {
        $this->save(function (array $draft) use ($data) {
            $values = Validator::make($data, self::seoRules())->validate();
            $draft['seo'] = [...$draft['seo'], ...$values];

            return $draft;
        });
    }

    private function save(callable $mutate): void
    {
        DB::transaction(function () use ($mutate) {
            $setting = SiteSetting::lockForUpdate()->findOrFail(1);
            // Media deletion uses this same lock: validate selections only after acquiring it.
            $setting->update(['draft_payload' => $mutate($setting->draft_payload)]);
        });
    }
}
