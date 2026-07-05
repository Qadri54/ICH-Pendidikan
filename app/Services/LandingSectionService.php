<?php

namespace App\Services;

use App\Models\LandingSection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class LandingSectionService
{
    public function getAllActive(): Collection
    {
        return LandingSection::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->keyBy('key');
    }

    public function getByKey(string $key): ?LandingSection
    {
        return LandingSection::where('key', $key)->first();
    }

    public function getAllForAdmin(): Collection
    {
        return LandingSection::orderBy('order')->get();
    }

    public function updateContent(string $key, array $content): LandingSection
    {
        $section = LandingSection::where('key', $key)->firstOrFail();
        $section->update(['content' => $content]);

        return $section;
    }

    public function toggleActive(string $key): LandingSection
    {
        $section = LandingSection::where('key', $key)->firstOrFail();
        $section->update(['is_active' => !$section->is_active]);

        return $section;
    }

    public function storeImage($file, string $folder = 'landing'): string
    {
        return $file->store($folder, 'public');
    }

    public function buildOrgTree(array $members): array
    {
        $map = [];
        foreach ($members as $m) {
            $m['children'] = [];
            $map[$m['id']] = $m;
        }

        $tree = [];
        foreach ($map as &$m) {
            if (!empty($m['parent_id']) && isset($map[$m['parent_id']])) {
                $map[$m['parent_id']]['children'][] = &$m;
            } else {
                $tree[] = &$m;
            }
        }
        unset($m);

        $this->sortTree($tree);

        return $tree;
    }

    private function sortTree(array &$nodes): void
    {
        usort($nodes, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
        foreach ($nodes as &$node) {
            if (!empty($node['children'])) {
                $this->sortTree($node['children']);
            }
        }
    }
}
