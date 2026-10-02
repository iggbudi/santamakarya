<?php

namespace Database\Seeders;

use App\Models\PageVersion;
use App\Models\PortfolioCategory;
use App\Models\PortfolioProject;
use App\Models\SiteSetting;
use App\Models\Slide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CmsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            if (SiteSetting::whereKey(1)->exists()) {
                return;
            }
            $payload = json_decode(file_get_contents(database_path('seeders/data/landing-content.json')), true, 512, JSON_THROW_ON_ERROR);
            foreach ($payload['slides'] as $location => &$slides) {
                foreach ($slides as &$slide) {
                    $model = Slide::create(['location' => $location, ...$slide]);
                    $slide['id'] = $model->id;
                }
                unset($slide);
            }
            unset($slides);
            $categories = [];
            foreach ($payload['portfolio']['categories'] as &$category) {
                $model = PortfolioCategory::create($category);
                $category['id'] = $model->id;
                $categories[$model->slug] = $model->id;
            }
            unset($category);
            foreach ($payload['portfolio']['projects'] as &$project) {
                $attributes = $project;
                unset($attributes['category_slug'], $attributes['category_name']);
                $model = PortfolioProject::create([...$attributes, 'category_id' => $categories[$project['category_slug']]]);
                $project['id'] = $model->id;
                $project['category_id'] = $model->category_id;
            }
            unset($project);
            $version = PageVersion::create(['payload' => $payload]);
            $setting = new SiteSetting(['draft_payload' => $payload['settings'], 'published_version_id' => $version->id]);
            $setting->id = 1;
            $setting->save();
        });
    }
}
