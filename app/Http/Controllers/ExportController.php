<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Link;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    private function getExportLinks()
    {
        $user = Auth::user();

        if ($user->hasRole('Admin Master') || $user->role === 'Admin Master') {
            $campaignIds = Campaign::pluck('id');
        } elseif ($user->hasRole('Admin') || $user->role === 'Admin') {
            $accessIds = \App\Models\UserCampaignAccess::where('user_id', $user->id)->pluck('campaign_id');
            if ($accessIds->count() > 0) {
                $campaignIds = $accessIds;
            } else {
                $campaignIds = Campaign::pluck('id');
            }
        } elseif ($user->hasRole('Client') || $user->role === 'Client') {
            $campaignIds = Campaign::where('client_id', $user->id)->pluck('id');
        } else {
            $campaignIds = collect();
        }

        $query = Link::whereIn('campaign_id', $campaignIds)->with('campaign', 'kategoriKonten');

        if (request()->has('campaign_id') && request()->campaign_id != '') {
            if ($campaignIds->contains(request()->campaign_id)) {
                $query->where('campaign_id', request()->campaign_id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if (request()->has('platform') && request()->platform != '') {
            $query->where('platform', request()->platform);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function clientPdf()
    {
        $links = $this->getExportLinks();
        
        $data = [
            'title' => 'Laporan Engagement Konten - ' . Auth::user()->name,
            'date' => date('d M Y'),
            'links' => $links,
        ];

        // Ensure you have run: composer require barryvdh/laravel-dompdf
        $pdf = Pdf::loadView('exports.client-pdf', $data)->setPaper('a4', 'landscape');
        
        return $pdf->download('laporan-engagement-'.date('Ymd').'.pdf');
    }

    public function clientExcel()
    {
        $links = $this->getExportLinks();

        $filename = "laporan-engagement-" . date('Ymd') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Platform', 'Campaign', 'Kategori', 'URL', 'Views', 'Likes', 'Comments', 'Engagement Rate (%)', 'Status'];

        $callback = function() use($links, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($links as $link) {
                fputcsv($file, [
                    $link->platform,
                    $link->campaign->nama_campaign ?? '-',
                    $link->kategoriKonten->nama ?? '-',
                    $link->url,
                    $link->views,
                    $link->likes,
                    $link->comments,
                    $link->engagement_rate,
                    $link->status_scraping
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
