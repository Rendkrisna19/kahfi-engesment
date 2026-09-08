<?php

namespace App\Http\Controllers;

use App\Models\LandingItem;
use App\Models\LandingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingCmsController extends Controller
{
    /**
     * Display public landing page.
     */
    /**
     * Display public landing page.
     */
    public function welcome()
    {
        $settings = LandingSetting::allKeyValues();

        $whyJoins = LandingItem::ofType('why_join')->active()->ordered()->get();
        $notFors = LandingItem::ofType('not_for')->active()->ordered()->get();
        $howSteps = LandingItem::ofType('how_step')->active()->ordered()->get();
        $ctaChips = LandingItem::ofType('cta_chip')->active()->ordered()->get();
        $clients = LandingItem::ofType('client_logo')->active()->ordered()->get();
        $portfolios = LandingItem::ofType('portfolio')->active()->ordered()->get();

        // Real dynamic database metrics (from links, creators, campaigns)
        $totalViews = \App\Models\Link::sum('views') ?: 0;
        $totalCreators = \App\Models\Link::whereNotNull('username')->where('username', '!=', '')->distinct('username')->count('username');
        if ($totalCreators === 0) {
            $totalCreators = \App\Models\User::where('role', 'Creator')->count() ?: 1;
        }
        $totalCampaigns = \App\Models\Campaign::count() ?: 0;
        $totalBrands = max($totalCampaigns, \App\Models\User::where('role', 'Client')->count(), 1);

        return view('welcome', compact(
            'settings',
            'whyJoins',
            'notFors',
            'howSteps',
            'ctaChips',
            'clients',
            'portfolios',
            'totalViews',
            'totalCreators',
            'totalBrands'
        ));
    }

    /**
     * Admin CMS Dashboard.
     */
    public function index()
    {
        $settings = LandingSetting::allKeyValues();

        $whyJoins = LandingItem::ofType('why_join')->ordered()->get();
        $notFors = LandingItem::ofType('not_for')->ordered()->get();
        $howSteps = LandingItem::ofType('how_step')->ordered()->get();
        $ctaChips = LandingItem::ofType('cta_chip')->ordered()->get();
        $clients = LandingItem::ofType('client_logo')->ordered()->get();
        $portfolios = LandingItem::ofType('portfolio')->ordered()->get();

        return view('admin.cms.index', compact(
            'settings',
            'whyJoins',
            'notFors',
            'howSteps',
            'ctaChips',
            'clients',
            'portfolios'
        ));
    }

    /**
     * Update landing settings.
     */
    public function updateSettings(Request $request)
    {
        $data = $request->except(['_token', '_method', 'hero_image', 'brand_logo', 'remove_brand_logo']);

        // Handle brand logo upload or removal
        if ($request->hasFile('brand_logo')) {
            $request->validate([
                'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            ]);
            $oldLogo = LandingSetting::get('brand_logo');
            if ($oldLogo && file_exists(public_path($oldLogo))) {
                @unlink(public_path($oldLogo));
            } elseif ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $file = $request->file('brand_logo');
            $fileName = 'logo_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $dest = public_path('uploads/landing');
            if (!file_exists($dest)) {
                @mkdir($dest, 0755, true);
            }
            $file->move($dest, $fileName);
            $path = 'uploads/landing/' . $fileName;
            LandingSetting::set('brand_logo', $path, 'general');
        } elseif ($request->boolean('remove_brand_logo')) {
            $oldLogo = LandingSetting::get('brand_logo');
            if ($oldLogo && file_exists(public_path($oldLogo))) {
                @unlink(public_path($oldLogo));
            } elseif ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            LandingSetting::set('brand_logo', null, 'general');
        }

        // Handle hero image upload if present
        if ($request->hasFile('hero_image')) {
            $request->validate([
                'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            ]);
            $oldHero = LandingSetting::get('hero_image');
            if ($oldHero && file_exists(public_path($oldHero))) {
                @unlink(public_path($oldHero));
            } elseif ($oldHero && Storage::disk('public')->exists($oldHero)) {
                Storage::disk('public')->delete($oldHero);
            }
            $file = $request->file('hero_image');
            $fileName = 'hero_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $dest = public_path('uploads/landing');
            if (!file_exists($dest)) {
                @mkdir($dest, 0755, true);
            }
            $file->move($dest, $fileName);
            $path = 'uploads/landing/' . $fileName;
            LandingSetting::set('hero_image', $path, 'hero');
        }

        foreach ($data as $key => $val) {
            // Determine group based on key prefix
            $group = 'general';
            if (str_starts_with($key, 'hero_')) $group = 'hero';
            elseif (str_starts_with($key, 'why_join_')) $group = 'why_join';
            elseif (str_starts_with($key, 'not_for_')) $group = 'not_for';
            elseif (str_starts_with($key, 'portfolio_')) $group = 'portfolio';
            elseif (str_starts_with($key, 'client_')) $group = 'client';
            elseif (str_starts_with($key, 'how_')) $group = 'how_it_works';
            elseif (str_starts_with($key, 'efficiency_')) $group = 'efficiency';
            elseif (str_starts_with($key, 'cta_')) $group = 'cta';
            elseif (str_starts_with($key, 'payment_')) $group = 'payment';

            LandingSetting::set($key, $val, $group);
        }

        $tab = $request->input('active_tab', 'hero');

        return redirect()->route('admin.cms.index', ['tab' => $tab])
            ->with('success', 'Pengaturan Landing Page berhasil diperbarui.');
    }

    /**
     * Store new repeatable item.
     */
    public function storeItem(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'sort_order' => 'nullable|integer',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = $request->type . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $dest = public_path('uploads/landing');
            if (!file_exists($dest)) {
                @mkdir($dest, 0755, true);
            }
            $file->move($dest, $fileName);
            $imagePath = 'uploads/landing/' . $fileName;
        }

        $extraMeta = null;
        if ($request->has('extra_meta') && is_array($request->input('extra_meta'))) {
            $meta = $request->input('extra_meta');
            // Format pros & cons if provided as multiline text
            if (isset($meta['pros_text'])) {
                $meta['pros'] = array_values(array_filter(array_map('trim', explode("\n", $meta['pros_text']))));
                unset($meta['pros_text']);
            }
            if (isset($meta['cons_text'])) {
                $meta['cons'] = array_values(array_filter(array_map('trim', explode("\n", $meta['cons_text']))));
                unset($meta['cons_text']);
            }
            if (isset($meta['is_winner'])) {
                $meta['is_winner'] = (bool)$meta['is_winner'];
            }
            $extraMeta = $meta;
        }

        LandingItem::create([
            'type' => $request->type,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'link' => $request->link,
            'image' => $imagePath,
            'extra_meta' => $extraMeta,
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $tab = $this->getTabForType($request->type);

        return redirect()->route('admin.cms.index', ['tab' => $tab])
            ->with('success', 'Item berhasil ditambahkan.');
    }

    /**
     * Update an item.
     */
    public function updateItem(Request $request, LandingItem $item)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'sort_order' => 'nullable|integer',
        ]);

        $imagePath = $item->image;
        if ($request->hasFile('image')) {
            if ($item->image && file_exists(public_path($item->image))) {
                @unlink(public_path($item->image));
            } elseif ($item->image && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }
            $file = $request->file('image');
            $fileName = $item->type . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $dest = public_path('uploads/landing');
            if (!file_exists($dest)) {
                @mkdir($dest, 0755, true);
            }
            $file->move($dest, $fileName);
            $imagePath = 'uploads/landing/' . $fileName;
        } elseif ($request->boolean('remove_image')) {
            if ($item->image && file_exists(public_path($item->image))) {
                @unlink(public_path($item->image));
            } elseif ($item->image && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }
            $imagePath = null;
        }

        $extraMeta = $item->extra_meta ?? [];
        if ($request->has('extra_meta') && is_array($request->input('extra_meta'))) {
            $meta = $request->input('extra_meta');
            if (isset($meta['pros_text'])) {
                $meta['pros'] = array_values(array_filter(array_map('trim', explode("\n", $meta['pros_text']))));
                unset($meta['pros_text']);
            }
            if (isset($meta['cons_text'])) {
                $meta['cons'] = array_values(array_filter(array_map('trim', explode("\n", $meta['cons_text']))));
                unset($meta['cons_text']);
            }
            if (isset($meta['is_winner'])) {
                $meta['is_winner'] = (bool)$meta['is_winner'];
            }
            $extraMeta = array_merge($extraMeta, $meta);
        }

        $item->update([
            'title' => $request->input('title', $item->title),
            'subtitle' => $request->input('subtitle', $item->subtitle),
            'description' => $request->input('description', $item->description),
            'link' => $request->input('link', $item->link),
            'image' => $imagePath,
            'extra_meta' => $extraMeta,
            'sort_order' => $request->input('sort_order', $item->sort_order),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $item->is_active,
        ]);

        $tab = $this->getTabForType($item->type);

        return redirect()->route('admin.cms.index', ['tab' => $tab])
            ->with('success', 'Item berhasil diperbarui.');
    }

    /**
     * Delete an item.
     */
    public function destroyItem(LandingItem $item)
    {
        $type = $item->type;
        if ($item->image && file_exists(public_path($item->image))) {
            @unlink(public_path($item->image));
        } elseif ($item->image && Storage::disk('public')->exists($item->image)) {
            Storage::disk('public')->delete($item->image);
        }
        $item->delete();

        $tab = $this->getTabForType($type);

        return redirect()->route('admin.cms.index', ['tab' => $tab])
            ->with('success', 'Item berhasil dihapus.');
    }

    private function getTabForType(string $type): string
    {
        return match ($type) {
            'why_join' => 'why_join',
            'not_for' => 'not_for',
            'how_step' => 'how_it_works',
            'efficiency_card' => 'efficiency',
            'cta_chip' => 'cta',
            'client_logo' => 'clients',
            'portfolio' => 'portfolios',
            'payment_proof' => 'payments',
            default => 'hero',
        };
    }
}
