<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LandingSectionService;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function __construct(private LandingSectionService $service) {}

    public function index()
    {
        $sections = $this->service->getAllForAdmin();

        return view('admin.landing.index', compact('sections'));
    }

    public function edit(string $key)
    {
        $section = $this->service->getByKey($key);
        abort_unless($section, 404);

        if ($key === 'struktur') {
            $tree = $this->service->buildOrgTree($section->content['members'] ?? []);

            return view('admin.landing.edit', compact('section', 'tree'));
        }

        return view('admin.landing.edit', compact('section'));
    }

    public function update(Request $request, string $key)
    {
        $section = $this->service->getByKey($key);
        abort_unless($section, 404);

        $method = 'update' . ucfirst($key);
        if (method_exists($this, $method)) {
            return $this->$method($request, $section);
        }

        return $this->updateGeneric($request, $section);
    }

    public function toggle(string $key)
    {
        $this->service->toggleActive($key);

        return back()->with('success', 'Status section berhasil diubah.');
    }

    private function updateGeneric(Request $request, $section)
    {
        $content = $section->content;

        foreach ($request->except(['_token', '_method']) as $field => $value) {
            if ($request->hasFile($field)) {
                $content[$field] = $this->service->storeImage($request->file($field));
            } else {
                $content[$field] = $value;
            }
        }

        $section->update(['content' => $content]);

        return back()->with('success', 'Section berhasil diperbarui.');
    }

    private function updateHero(Request $request, $section)
    {
        $content = $section->content;
        $content['badge'] = $request->input('badge', $content['badge']);
        $content['title'] = $request->input('title', $content['title']);
        $content['subtitle'] = $request->input('subtitle', $content['subtitle']);

        if ($request->hasFile('image')) {
            $content['image'] = $this->service->storeImage($request->file('image'));
        }

        $stats = [];
        foreach ($request->input('stats', []) as $stat) {
            $stats[] = [
                'icon' => $stat['icon'] ?? 'book',
                'title' => $stat['title'] ?? '',
                'subtitle' => $stat['subtitle'] ?? '',
            ];
        }
        if (!empty($stats)) {
            $content['stats'] = $stats;
        }

        $section->update(['content' => $content]);

        return back()->with('success', 'Hero berhasil diperbarui.');
    }

    private function updateTentang(Request $request, $section)
    {
        $content = $section->content;
        $content['label'] = $request->input('label', $content['label']);
        $content['title'] = $request->input('title', $content['title']);
        $content['description'] = $request->input('description', $content['description']);
        $content['badge_number'] = $request->input('badge_number', $content['badge_number']);
        $content['badge_text'] = $request->input('badge_text', $content['badge_text']);

        if ($request->hasFile('image')) {
            $content['image'] = $this->service->storeImage($request->file('image'));
        }

        $features = [];
        foreach ($request->input('features', []) as $f) {
            if (!empty($f['title'])) {
                $features[] = ['title' => $f['title'], 'description' => $f['description'] ?? ''];
            }
        }
        if (!empty($features)) {
            $content['features'] = $features;
        }

        $section->update(['content' => $content]);

        return back()->with('success', 'Tentang Kami berhasil diperbarui.');
    }

    private function updateStruktur(Request $request, $section)
    {
        $content = $section->content;
        $content['label'] = $request->input('label', $content['label'] ?? 'Organisasi');
        $content['title'] = $request->input('title', $content['title']);
        $content['subtitle'] = $request->input('subtitle', $content['subtitle']);

        $members = [];
        foreach ($request->input('members', []) as $idx => $m) {
            if (empty($m['name'])) {
                continue;
            }

            $member = [
                'id' => (int) ($m['id'] ?? ($idx + 1)),
                'parent_id' => !empty($m['parent_id']) ? (int) $m['parent_id'] : null,
                'position' => $m['position'] ?: null,
                'name' => $m['name'],
                'photo' => $m['existing_photo'] ?? null,
                'type' => $m['type'] ?? 'default',
                'order' => (int) ($m['order'] ?? ($idx + 1)),
            ];

            $photoKey = "members.{$idx}.photo_file";
            if ($request->hasFile($photoKey)) {
                $member['photo'] = $this->service->storeImage($request->file($photoKey), 'landing/struktur');
            }

            $members[] = $member;
        }

        $content['members'] = $members;
        $section->update(['content' => $content]);

        return back()->with('success', 'Struktur Organisasi berhasil diperbarui.');
    }

    private function updateProgram(Request $request, $section)
    {
        $content = $section->content;
        $content['label'] = $request->input('label', $content['label']);
        $content['title'] = $request->input('title', $content['title']);
        $content['subtitle'] = $request->input('subtitle', $content['subtitle']);

        $items = [];
        foreach ($request->input('items', []) as $item) {
            if (!empty($item['title'])) {
                $items[] = [
                    'title' => $item['title'],
                    'description' => $item['description'] ?? '',
                    'highlighted' => !empty($item['highlighted']),
                ];
            }
        }
        if (!empty($items)) {
            $content['items'] = $items;
        }

        $section->update(['content' => $content]);

        return back()->with('success', 'Program berhasil diperbarui.');
    }

    private function updateAktivitas(Request $request, $section)
    {
        $content = $section->content;
        $content['label'] = $request->input('label', $content['label']);
        $content['title'] = $request->input('title', $content['title']);
        $content['subtitle'] = $request->input('subtitle', $content['subtitle']);

        $items = [];
        foreach ($request->input('items', []) as $idx => $item) {
            if (empty($item['title'])) {
                continue;
            }

            $entry = [
                'image' => $item['existing_image'] ?? '',
                'tag' => $item['tag'] ?? '',
                'title' => $item['title'],
                'meta' => $item['meta'] ?? '',
            ];

            $imageKey = "items.{$idx}.image_file";
            if ($request->hasFile($imageKey)) {
                $entry['image'] = $this->service->storeImage($request->file($imageKey), 'landing/aktivitas');
            }

            $items[] = $entry;
        }

        if (!empty($items)) {
            $content['items'] = $items;
        }

        $section->update(['content' => $content]);

        return back()->with('success', 'Aktivitas berhasil diperbarui.');
    }

    private function updateTestimoni(Request $request, $section)
    {
        $content = $section->content;
        $content['label'] = $request->input('label', $content['label'] ?? 'Testimoni');
        $content['title'] = $request->input('title', $content['title']);

        $items = [];
        foreach ($request->input('items', []) as $idx => $item) {
            if (empty($item['name'])) {
                continue;
            }

            $entry = [
                'text' => $item['text'] ?? '',
                'name' => $item['name'],
                'role' => $item['role'] ?? '',
                'avatar' => $item['existing_avatar'] ?? null,
            ];

            $avatarKey = "items.{$idx}.avatar_file";
            if ($request->hasFile($avatarKey)) {
                $entry['avatar'] = $this->service->storeImage($request->file($avatarKey), 'landing/testimoni');
            }

            $items[] = $entry;
        }

        if (!empty($items)) {
            $content['items'] = $items;
        }

        $section->update(['content' => $content]);

        return back()->with('success', 'Testimoni berhasil diperbarui.');
    }

    private function updateCta(Request $request, $section)
    {
        $content = $section->content;
        $content['title'] = $request->input('title', $content['title']);
        $content['subtitle'] = $request->input('subtitle', $content['subtitle']);
        $content['whatsapp'] = $request->input('whatsapp', $content['whatsapp'] ?? '');

        $section->update(['content' => $content]);

        return back()->with('success', 'CTA berhasil diperbarui.');
    }

    private function updateFooter(Request $request, $section)
    {
        $content = $section->content;
        $content['description'] = $request->input('description', $content['description']);
        $content['email'] = $request->input('email', $content['email']);
        $content['address'] = $request->input('address', $content['address']);
        $content['hours'] = $request->input('hours', $content['hours']);
        $content['phone'] = $request->input('phone', $content['phone']);
        $content['whatsapp'] = $request->input('whatsapp', $content['whatsapp']);
        $content['copyright'] = $request->input('copyright', $content['copyright']);

        $section->update(['content' => $content]);

        return back()->with('success', 'Footer berhasil diperbarui.');
    }
}
