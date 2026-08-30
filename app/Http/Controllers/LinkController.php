<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\KategoriCreator;
use App\Models\KategoriKonten;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCampaignAccess;
use Exception;

class LinkController extends Controller
{

    
    /**
     * Menampilkan daftar link konten dan form input operasional
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $kategoriKonten = KategoriKonten::orderBy('nama', 'asc')->get();
        $kategoriCreator = KategoriCreator::orderBy('nama', 'asc')->get();

        if ($user->hasRole('Admin Master') || $user->role === 'Admin Master') {
            $campaigns = Campaign::orderBy('nama_campaign', 'asc')->get();
        } elseif ($user->hasRole('Admin') || $user->role === 'Admin') {
            $accessIds = UserCampaignAccess::where('user_id', $user->id)->pluck('campaign_id');
            $campaigns = Campaign::whereIn('id', $accessIds)->orderBy('nama_campaign', 'asc')->get();
        } elseif ($user->hasRole('Client') || $user->role === 'Client') {
            $campaigns = Campaign::where('client_id', $user->id)->orderBy('nama_campaign', 'asc')->get();
        } else {
            $campaigns = collect();
        }

        $campaignIds = $campaigns->pluck('id');

        $query = Link::with(['campaign', 'kategoriKonten', 'kategoriCreator']);

        // Scope data link sesuai campaign yang bisa diakses user
        if (!($user->hasRole('Admin Master') || $user->role === 'Admin Master')) {
            $query->whereIn('campaign_id', $campaignIds);
        }

        $sortDir = strtolower($request->query('sort_dir', 'desc'));
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }

        // Urutkan otomatis berdasarkan Tanggal Upload (default: dari terbaru ke terlama)
        $query->orderByRaw("COALESCE(tanggal_upload, DATE(updated_at)) {$sortDir}")
              ->orderBy('id', $sortDir);

        // Filter Campaign jika dipilih
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }

        // Filter Platform jika dipilih
        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        // Filter Search / Pencarian jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('url', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('caption', 'like', "%{$search}%");
            });
        }

        // Filter Tanggal
        if ($request->filled('start_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('tanggal_upload', '>=', $request->start_date)
                  ->orWhere(function ($sub) use ($request) {
                      $sub->whereNull('tanggal_upload')
                          ->whereDate('updated_at', '>=', $request->start_date);
                  });
            });
        }

        if ($request->filled('end_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('tanggal_upload', '<=', $request->end_date)
                  ->orWhere(function ($sub) use ($request) {
                      $sub->whereNull('tanggal_upload')
                          ->whereDate('updated_at', '<=', $request->end_date);
                  });
            });
        }

        $perPage = (int) $request->query('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        $links = $query->paginate($perPage)->withQueryString();

        $pendingCountQuery = Link::where('status_scraping', 'Pending');
        if (!($user->hasRole('Admin Master') || $user->role === 'Admin Master')) {
            $pendingCountQuery->whereIn('campaign_id', $campaignIds);
        }
        $pendingCount = $pendingCountQuery->count();

        return view('operasional-konten.index', compact('campaigns', 'kategoriKonten', 'kategoriCreator', 'links', 'pendingCount'));
    }

    /**
     * Menyimpan data link konten (Single, Bulk, CSV)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validasi akses ke campaign
        if (!($user->hasRole('Admin Master') || $user->role === 'Admin Master')) {
            if ($user->hasRole('Admin') || $user->role === 'Admin') {
                $allowedIds = UserCampaignAccess::where('user_id', $user->id)->pluck('campaign_id')->toArray();
            } elseif ($user->hasRole('Client') || $user->role === 'Client') {
                $allowedIds = Campaign::where('client_id', $user->id)->pluck('id')->toArray();
            } else {
                $allowedIds = [];
            }

            if (!in_array($request->campaign_id, $allowedIds)) {
                return redirect()->back()->with('error', 'Maaf, kamu masih belum ditugaskan campaign ini.');
            }
        }

        $request->validate([
            'type' => 'required|in:single,bulk,csv',
            'campaign_id' => 'required|exists:campaigns,id',
            'kategori_konten_id' => 'required|exists:kategori_konten,id',
            'kategori_creator_id' => 'required|exists:kategori_creator,id',
        ]);

        $insertedCount = 0;

        if ($request->type === 'single') {
            $request->validate([
                'url' => 'required|string',
            ]);

            $url = $this->sanitizeUrl($request->url);
            if (!$url) {
                return redirect()->back()->with('error', 'URL Konten tidak valid atau bukan format TikTok/Instagram.');
            }

            $campaignObj = \App\Models\Campaign::find($request->campaign_id);
            $platform = $this->detectPlatform($url, $campaignObj->platform ?? null);

            Link::create([
                'campaign_id' => $request->campaign_id,
                'kategori_konten_id' => $request->kategori_konten_id,
                'kategori_creator_id' => $request->kategori_creator_id,
                'url' => $url,
                'platform' => $platform,
                'tanggal_upload' => now()->toDateString(),
                'status_scraping' => 'Pending'
            ]);

            $insertedCount = 1;
        } elseif ($request->type === 'bulk') {
            $request->validate([
                'urls' => 'required|string',
            ]);

            $campaignObj = \App\Models\Campaign::find($request->campaign_id);
            $urls = explode("\n", str_replace("\r", "", $request->urls));

            foreach ($urls as $rawUrl) {
                $url = $this->sanitizeUrl($rawUrl);
                if ($url) {
                    $platform = $this->detectPlatform($url, $campaignObj->platform ?? null);

                    Link::create([
                        'campaign_id' => $request->campaign_id,
                        'kategori_konten_id' => $request->kategori_konten_id,
                        'kategori_creator_id' => $request->kategori_creator_id,
                        'url' => $url,
                        'platform' => $platform,
                        'tanggal_upload' => now()->toDateString(),
                        'status_scraping' => 'Pending'
                    ]);
                    $insertedCount++;
                }
            }
        } elseif ($request->type === 'csv') {
            $request->validate([
                'file' => 'required|file|max:10240',
            ]);

            $campaignObj = \App\Models\Campaign::find($request->campaign_id);
            $campaignPlatform = trim($campaignObj->platform ?? '');

            $filePath = $request->file('file')->getRealPath();
            $fileContents = file_get_contents($filePath);

            $rowsData = [];
            $unzippedTextPool = '';

            // 1. Parse Native .xlsx Zip Archive if available
            if (str_starts_with($fileContents, "PK\x03\x04") && class_exists('ZipArchive')) {
                $zip = new \ZipArchive();
                if ($zip->open($filePath) === true) {
                    $sharedStrings = [];
                    $ssXml = null;
                    $sheetXmls = [];

                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $entryName = $zip->getNameIndex($i);
                        $entryContent = $zip->getFromIndex($i);
                        if (!empty($entryContent)) {
                            $unzippedTextPool .= " " . $entryContent;
                        }

                        if (preg_match('/xl\/sharedStrings\.xml$/i', $entryName)) {
                            $ssXml = $entryContent;
                        } elseif (preg_match('/xl\/worksheets\/sheet\d*\.xml$/i', $entryName)) {
                            $sheetXmls[] = $entryContent;
                        }
                    }

                    if ($ssXml) {
                        $ssObj = @simplexml_load_string($ssXml);
                        if ($ssObj && isset($ssObj->si)) {
                            foreach ($ssObj->si as $si) {
                                if (isset($si->t)) {
                                    $sharedStrings[] = html_entity_decode((string)$si->t, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                } elseif (isset($si->r)) {
                                    $text = '';
                                    foreach ($si->r as $rItem) {
                                        $text .= (string)($rItem->t ?? '');
                                    }
                                    $sharedStrings[] = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                } else {
                                    $sharedStrings[] = '';
                                }
                            }
                        } else {
                            preg_match_all('/<t[^>]*>(.*?)<\/t>/s', $ssXml, $ssMatches);
                            if (!empty($ssMatches[1])) {
                                foreach ($ssMatches[1] as $val) {
                                    $sharedStrings[] = html_entity_decode(strip_tags($val), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                }
                            }
                        }
                    }

                    foreach ($sheetXmls as $sheetXml) {
                        $sheetObj = @simplexml_load_string($sheetXml);
                        if ($sheetObj && isset($sheetObj->sheetData->row)) {
                            $headerMap = [];
                            foreach ($sheetObj->sheetData->row as $rowNode) {
                                $rowCells = [];
                                foreach ($rowNode->c as $cNode) {
                                    $rAttr = (string)($cNode['r'] ?? '');
                                    $colIdx = null;

                                    if (!empty($rAttr) && preg_match('/^([A-Z]+)(\d+)$/i', $rAttr, $rMatches)) {
                                        $colStr = strtoupper($rMatches[1]);
                                        $cVal = 0;
                                        for ($charIdx = 0; $charIdx < strlen($colStr); $charIdx++) {
                                            $cVal = $cVal * 26 + (ord($colStr[$charIdx]) - ord('A') + 1);
                                        }
                                        $colIdx = $cVal - 1;
                                    }

                                    $t = (string)($cNode['t'] ?? '');
                                    $v = (string)($cNode->v ?? '');

                                    $cellVal = '';
                                    if ($t === 's' && is_numeric($v) && isset($sharedStrings[(int)$v])) {
                                        $cellVal = $sharedStrings[(int)$v];
                                    } elseif ($t === 'inlineStr' && isset($cNode->is->t)) {
                                        $cellVal = (string)$cNode->is->t;
                                    } else {
                                        $cellVal = trim($v);
                                    }

                                    if ($colIdx !== null) {
                                        $rowCells[$colIdx] = $cellVal;
                                    } else {
                                        $rowCells[] = $cellVal;
                                    }
                                }

                                if (empty($rowCells)) continue;

                                $rowText = strtolower(implode(' ', $rowCells));
                                if (empty($headerMap) && (str_contains($rowText, 'link') || str_contains($rowText, 'url') || str_contains($rowText, 'views') || str_contains($rowText, 'tgl upload') || str_contains($rowText, 'account'))) {
                                    foreach ($rowCells as $idx => $cellText) {
                                        $lowerHeader = strtolower((string)$cellText);
                                        if (str_contains($lowerHeader, 'link') || str_contains($lowerHeader, 'url') || str_contains($lowerHeader, 'post') || str_contains($lowerHeader, 'content')) {
                                            if (!isset($headerMap['url'])) $headerMap['url'] = $idx;
                                        } elseif (str_contains($lowerHeader, 'account') || str_contains($lowerHeader, 'akun') || str_contains($lowerHeader, 'username') || str_contains($lowerHeader, 'creator')) {
                                            $headerMap['username'] = $idx;
                                        } elseif (str_contains($lowerHeader, 'tgl') || str_contains($lowerHeader, 'tanggal') || str_contains($lowerHeader, 'date')) {
                                            $headerMap['tanggal_upload'] = $idx;
                                        } elseif (str_contains($lowerHeader, 'view')) {
                                            $headerMap['views'] = $idx;
                                        } elseif (str_contains($lowerHeader, 'like')) {
                                            $headerMap['likes'] = $idx;
                                        } elseif (str_contains($lowerHeader, 'comment') || str_contains($lowerHeader, 'komentar')) {
                                            $headerMap['comments'] = $idx;
                                        } elseif (str_contains($lowerHeader, 'save') || str_contains($lowerHeader, 'simpan')) {
                                            $headerMap['saves'] = $idx;
                                        } elseif (str_contains($lowerHeader, 'share') || str_contains($lowerHeader, 'bagikan')) {
                                            $headerMap['shares'] = $idx;
                                        }
                                    }
                                    continue;
                                }

                                $extractedRow = $this->extractRowDataFromCells($rowCells, $headerMap);
                                if ($extractedRow && !empty($extractedRow['url'])) {
                                    $rowsData[] = $extractedRow;
                                }
                            }
                        }
                    }

                    $zip->close();
                }
            }

            // Clean XML/HTML metadata tags (<head>, <xml>, <style>, <annotation>) to prevent w3.org namespace URLs from being read
            $cleanContents = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $fileContents);
            $cleanContents = preg_replace('/<xml[^>]*>.*?<\/xml>/is', '', $cleanContents);
            $cleanContents = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $cleanContents);

            // 2. Parse HTML Table (.xls format exported from Excel / Google Sheets)
            if (empty($rowsData) && (str_contains($cleanContents, '<tr') || str_contains($cleanContents, '<td'))) {
                preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $cleanContents, $trMatches);
                if (!empty($trMatches[1])) {
                    $headerMap = [];
                    foreach ($trMatches[1] as $trContent) {
                        preg_match_all('/<(?:td|th)[^>]*>(.*?)<\/(?:td|th)>/is', $trContent, $cellMatches);
                        if (empty($cellMatches[1])) continue;

                        $rowCells = array_map(function($c) {
                            $decoded = html_entity_decode($c, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            return trim(preg_replace('/\s+/', ' ', strip_tags($decoded)));
                        }, $cellMatches[1]);

                        $rowText = strtolower(implode(' ', $rowCells));
                        if (empty($headerMap) && (str_contains($rowText, 'link') || str_contains($rowText, 'url') || str_contains($rowText, 'views') || str_contains($rowText, 'tgl upload') || str_contains($rowText, 'account'))) {
                            foreach ($rowCells as $idx => $cellText) {
                                $lowerHeader = strtolower($cellText);
                                if (str_contains($lowerHeader, 'link') || str_contains($lowerHeader, 'url') || str_contains($lowerHeader, 'post') || str_contains($lowerHeader, 'content')) {
                                    if (!isset($headerMap['url'])) $headerMap['url'] = $idx;
                                } elseif (str_contains($lowerHeader, 'account') || str_contains($lowerHeader, 'akun') || str_contains($lowerHeader, 'username') || str_contains($lowerHeader, 'creator')) {
                                    $headerMap['username'] = $idx;
                                } elseif (str_contains($lowerHeader, 'tgl') || str_contains($lowerHeader, 'tanggal') || str_contains($lowerHeader, 'date')) {
                                    $headerMap['tanggal_upload'] = $idx;
                                } elseif (str_contains($lowerHeader, 'view')) {
                                    $headerMap['views'] = $idx;
                                } elseif (str_contains($lowerHeader, 'like')) {
                                    $headerMap['likes'] = $idx;
                                } elseif (str_contains($lowerHeader, 'comment') || str_contains($lowerHeader, 'komentar')) {
                                    $headerMap['comments'] = $idx;
                                } elseif (str_contains($lowerHeader, 'save') || str_contains($lowerHeader, 'simpan')) {
                                    $headerMap['saves'] = $idx;
                                } elseif (str_contains($lowerHeader, 'share') || str_contains($lowerHeader, 'bagikan')) {
                                    $headerMap['shares'] = $idx;
                                }
                            }
                            continue;
                        }

                        $extractedRow = $this->extractRowDataFromCells($rowCells, $headerMap);
                        if ($extractedRow && !empty($extractedRow['url'])) {
                            $rowsData[] = $extractedRow;
                        }
                    }
                }
            }

            // 3. Parse Delimited CSV / TSV
            if (empty($rowsData)) {
                $lines = explode("\n", str_replace("\r", "", $fileContents));
                $headerMap = [];

                foreach ($lines as $line) {
                    if (empty(trim($line))) continue;
                    if (str_starts_with(trim($line), 'sep=') || str_starts_with(trim($line), '<?xml')) continue;

                    $delimiter = str_contains($line, ';') ? ';' : (str_contains($line, "\t") ? "\t" : ',');
                    $rawCells = str_getcsv($line, $delimiter);
                    $rowCells = array_map(function($c) {
                        $decoded = html_entity_decode($c, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        return trim(preg_replace('/\s+/', ' ', strip_tags($decoded)));
                    }, $rawCells);

                    $rowText = strtolower(implode(' ', $rowCells));
                    if (empty($headerMap) && (str_contains($rowText, 'link') || str_contains($rowText, 'url') || str_contains($rowText, 'views') || str_contains($rowText, 'tgl upload') || str_contains($rowText, 'account'))) {
                        foreach ($rowCells as $idx => $cellText) {
                            $lowerHeader = strtolower($cellText);
                            if (str_contains($lowerHeader, 'link') || str_contains($lowerHeader, 'url') || str_contains($lowerHeader, 'post') || str_contains($lowerHeader, 'content')) {
                                if (!isset($headerMap['url'])) $headerMap['url'] = $idx;
                            } elseif (str_contains($lowerHeader, 'account') || str_contains($lowerHeader, 'akun') || str_contains($lowerHeader, 'username') || str_contains($lowerHeader, 'creator')) {
                                $headerMap['username'] = $idx;
                            } elseif (str_contains($lowerHeader, 'tgl') || str_contains($lowerHeader, 'tanggal') || str_contains($lowerHeader, 'date')) {
                                $headerMap['tanggal_upload'] = $idx;
                            } elseif (str_contains($lowerHeader, 'view')) {
                                $headerMap['views'] = $idx;
                            } elseif (str_contains($lowerHeader, 'like')) {
                                $headerMap['likes'] = $idx;
                            } elseif (str_contains($lowerHeader, 'comment') || str_contains($lowerHeader, 'komentar')) {
                                $headerMap['comments'] = $idx;
                            } elseif (str_contains($lowerHeader, 'save') || str_contains($lowerHeader, 'simpan')) {
                                $headerMap['saves'] = $idx;
                            } elseif (str_contains($lowerHeader, 'share') || str_contains($lowerHeader, 'bagikan')) {
                                $headerMap['shares'] = $idx;
                            }
                        }
                        continue;
                    }

                    $extractedRow = $this->extractRowDataFromCells($rowCells, $headerMap);
                    if ($extractedRow && !empty($extractedRow['url'])) {
                        $rowsData[] = $extractedRow;
                    }
                }
            }

            // 4. Ultimate Fallback: scan all text contents (raw, unzipped, clean) for ANY TikTok / Instagram URL
            if (empty($rowsData)) {
                $searchPool = $fileContents . " " . $cleanContents . " " . $unzippedTextPool;
                preg_match_all('/(?:https?:\/\/|www\.|vt\.|vm\.|[a-z0-9\.\-]+\.(?:tiktok|instagram)\.com\/)[^\s"<>\'\,\;\r\n]+/i', $searchPool, $matches);
                if (!empty($matches[0])) {
                    foreach ($matches[0] as $rawUrl) {
                        $cleanUrl = $this->sanitizeUrl($rawUrl);
                        if ($cleanUrl) {
                            $rowsData[] = [
                                'url' => $cleanUrl,
                                'username' => null,
                                'tanggal_upload' => now()->toDateString(),
                                'views' => 0,
                                'likes' => 0,
                                'comments' => 0,
                                'saves' => 0,
                                'shares' => 0,
                            ];
                        }
                    }
                }
            }

            $seenUrls = [];
            $insertedCount = 0;
            $skippedPlatformCount = 0;

            foreach ($rowsData as $r) {
                $url = $r['url'];
                if (in_array($url, $seenUrls)) continue;
                $seenUrls[] = $url;

                $detectedPlatform = $this->detectPlatform($url, $campaignPlatform);

                // Check platform compatibility with selected campaign
                if (!empty($campaignPlatform)) {
                    $lowerCampPlat = strtolower($campaignPlatform);
                    $isCampTiktok = str_contains($lowerCampPlat, 'tiktok') && !str_contains($lowerCampPlat, 'instagram');
                    $isCampIg = str_contains($lowerCampPlat, 'instagram') && !str_contains($lowerCampPlat, 'tiktok');

                    if ($isCampTiktok && strtolower($detectedPlatform) !== 'tiktok') {
                        $skippedPlatformCount++;
                        continue;
                    }

                    if ($isCampIg && strtolower($detectedPlatform) !== 'instagram') {
                        $skippedPlatformCount++;
                        continue;
                    }
                }

                $views = (int)($r['views'] ?? 0);
                $likes = (int)($r['likes'] ?? 0);
                $comments = (int)($r['comments'] ?? 0);
                $saves = (int)($r['saves'] ?? 0);
                $shares = (int)($r['shares'] ?? 0);

                $er = ($views > 0) ? (($likes + $comments + $shares) / $views) * 100 : 0;
                $statusScraping = 'Completed';

                Link::updateOrCreate(
                    [
                        'campaign_id' => $request->campaign_id,
                        'url' => $url,
                    ],
                    [
                        'kategori_konten_id' => $request->kategori_konten_id,
                        'kategori_creator_id' => $request->kategori_creator_id,
                        'username' => !empty($r['username']) ? $r['username'] : null,
                        'platform' => $detectedPlatform,
                        'tanggal_upload' => !empty($r['tanggal_upload']) ? $r['tanggal_upload'] : now()->toDateString(),
                        'views' => $views,
                        'likes' => $likes,
                        'comments' => $comments,
                        'saves' => $saves,
                        'shares' => $shares,
                        'engagement_rate' => min(100, round($er, 2)),
                        'status_scraping' => $statusScraping,
                        'updated_at' => now(),
                    ]
                );

                $insertedCount++;
            }

            if ($insertedCount > 0) {
                $this->calculateSawScoresForCampaign($request->campaign_id);
                $msg = "Berhasil memproses & menyimpan {$insertedCount} data link konten dari file Excel.";
                if ($skippedPlatformCount > 0) {
                    $msg .= " ({$skippedPlatformCount} link dilewati karena jenis platform tidak sesuai dengan Campaign '{$campaignObj->nama_campaign}' - Platform: {$campaignObj->platform}).";
                }
                return redirect()->route('operasional-konten.index')->with('success', $msg);
            } elseif ($skippedPlatformCount > 0) {
                return redirect()->route('operasional-konten.index')->with('warning', "Tidak ada link yang diimpor. Total {$skippedPlatformCount} link dalam file dilewati karena tidak sesuai dengan platform Campaign '{$campaignObj->nama_campaign}' (Platform: {$campaignObj->platform}).");
            } else {
                return redirect()->route('operasional-konten.index')->with('error', 'Tidak ditemukan link video yang valid dalam file Excel/CSV yang diupload.');
            }
        }

        return redirect()->route('operasional-konten.index')
            ->with('success', "Berhasil memproses & menyimpan {$insertedCount} data link konten dari file.");
    }

    /**
     * Download template format Excel / CSV untuk upload bulk
     */
    public function downloadTemplate(Request $request)
    {
        $format = strtolower($request->query('format', 'excel'));

        $filename = ($format === 'excel') ? 'template_upload_link.csv' : 'template_upload_link.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM & sep directive for seamless Microsoft Excel opening
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fwrite($file, "sep=,\n");

            fputcsv($file, ['No', 'Tgl Upload', 'Account', 'Link Content (TikTok / Instagram) *Wajib', 'Views', 'Likes', 'Comment', 'Save', 'Share']);
            fputcsv($file, ['1', 'Sabtu, 08 Agustus 2026', 'Ruang Bercerita', 'https://vt.tiktok.com/ZS4sj89A1/', '398900', '5329', '69', '284', '463']);
            fputcsv($file, ['2', 'Minggu, 09 Agustus 2026', 'Area Cerita', 'https://www.tiktok.com/@haloiniakimm/video/7677116573420621076', '7381', '120', '15', '8', '12']);
            fputcsv($file, ['3', 'Rabu, 12 Agustus 2026', 'Inspirasi Harian', 'https://www.instagram.com/p/Cxyz123456/', '45200', '1850', '42', '110', '95']);
            
            for ($i = 4; $i <= 10; $i++) {
                fputcsv($file, [(string)$i, '', '', '', '', '', '', '', '']);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Menampilkan detail data link & metrik scraping
     */
    public function show($id)
    {
        $link = Link::with(['campaign', 'kategoriKonten', 'kategoriCreator'])->findOrFail($id);
        if (request()->has('debug')) {
            dd('Masuk ke halaman show!', $link->toArray());
        }
        return view('operasional-konten.show', compact('link'));
    }

    /**
     * Update metrik engagement link secara manual (Views, Likes, Comments, Shares, Saves)
     */
    public function update(Request $request, $id)
    {
        $link = Link::findOrFail($id);

        // Security check: status Gagal cannot be edited
        if ($link->status_scraping === 'Gagal') {
            return redirect()->back()->with('error', 'Link dengan status Gagal tidak dapat diedit secara manual.');
        }

        // Check user access to this campaign
        $user = auth()->user();
        if ($user->hasRole('Admin')) {
            $allowedIds = UserCampaignAccess::where('user_id', $user->id)->pluck('campaign_id')->toArray();
            if (!in_array($link->campaign_id, $allowedIds)) {
                return redirect()->back()->with('error', 'Maaf, Anda tidak memiliki akses ke campaign ini.');
            }
        }

        $validated = $request->validate([
            'username' => 'nullable|string|max:255',
            'tanggal_upload' => 'nullable|date',
            'views' => 'required|numeric|min:0',
            'likes' => 'required|numeric|min:0',
            'comments' => 'required|numeric|min:0',
            'shares' => 'required|numeric|min:0',
            'saves' => 'required|numeric|min:0',
        ]);

        $views = (int) $validated['views'];
        $likes = (int) $validated['likes'];
        $comments = (int) $validated['comments'];
        $shares = (int) $validated['shares'];
        $saves = (int) $validated['saves'];

        // Calculate Engagement Rate: (Likes + Comments + Shares) / Views * 100
        $er = 0;
        if ($views > 0) {
            $er = (($likes + $comments + $shares) / $views) * 100;
        }

        // If status was Pending, update status to Completed because metrics are now entered manually
        $statusScraping = in_array($link->status_scraping, ['Pending']) ? 'Completed' : $link->status_scraping;

        $link->update([
            'username' => $validated['username'] ?? $link->username,
            'tanggal_upload' => $validated['tanggal_upload'] ?? $link->tanggal_upload,
            'views' => $views,
            'likes' => $likes,
            'comments' => $comments,
            'shares' => $shares,
            'saves' => $saves,
            'engagement_rate' => min(100, round($er, 2)),
            'status_scraping' => $statusScraping,
            'updated_at' => now(),
        ]);

        // Recalculate SAW score for the campaign
        $this->calculateSawScoresForCampaign($link->campaign_id);

        return redirect()->route('operasional-konten.index')
            ->with('success', "Berhasil memperbarui metrik engagement untuk link secara manual!");
    }

    /**
     * Menghapus record link
     */
    public function destroy($id)
    {
        $link = Link::findOrFail($id);
        $campaignId = $link->campaign_id;
        $link->delete();

        // Hitung ulang SAW untuk campaign tersebut setelah data dihapus
        $this->calculateSawScoresForCampaign($campaignId);

        return redirect()->back()->with('success', 'Link konten berhasil dihapus.');
    }

    /**
     * Menghapus multiple record link (Bulk Delete)
     */
    public function destroyBulk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:links,id'
        ]);

        $links = Link::whereIn('id', $request->ids)->get();
        $count = $links->count();

        if ($count === 0) {
            return redirect()->back()->with('error', 'Pilih minimal satu link untuk dihapus.');
        }

        $campaignIds = $links->pluck('campaign_id')->unique();
        Link::whereIn('id', $request->ids)->delete();

        foreach ($campaignIds as $cId) {
            $this->calculateSawScoresForCampaign($cId);
        }

        return redirect()->back()->with('success', "Berhasil menghapus {$count} link konten.");
    }

    /**
     * Menjalankan Scraping Apify API untuk Link berstatus Pending dan Kalkulasi SAW
     */
    public function refreshData(Request $request)
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '512M');

        $query = Link::where('status_scraping', 'Pending');

        // Jika refresh dipicu untuk campaign tertentu
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }

        $pendingLinks = $query->get();

        if ($pendingLinks->isEmpty()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'processed_count' => 0,
                    'message' => 'Tidak ada link dengan status Pending.',
                    'remaining_pending' => 0
                ]);
            }
            return redirect()->back()->with('info', 'Tidak ada link dengan status Pending yang perlu diproses.');
        }

        $token = env('APIFY_TOKEN', env('APIFY_API_TOKEN'));
        $rawTiktokActor = env('APIFY_TIKTOK_ACTOR', env('APIFY_TIKTOK_ACTOR_ID', 'clockworks/free-tiktok-scraper'));
        $rawIgActor = env('APIFY_IG_ACTOR', env('APIFY_INSTAGRAM_ACTOR_ID', 'apify/instagram-scraper'));

        $tiktokActorId = str_replace('/', '~', $rawTiktokActor);
        $igActorId = str_replace('/', '~', $rawIgActor);

        if (empty($token)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'API Token Apify belum dikonfigurasi di file .env.'
                ], 400);
            }
            return redirect()->back()->with('error', 'API Token Apify belum dikonfigurasi di file .env.');
        }

        $successCount = 0;
        $errors = [];
        $affectedCampaigns = [];

        foreach ($pendingLinks as $link) {
            try {
                $cleanUrl = strtok($link->url, '?');
                $isTikTok = strtolower($link->platform) === 'tiktok';

                if ($isTikTok) {
                    $endpoint = "https://api.apify.com/v2/acts/{$tiktokActorId}/run-sync-get-dataset-items?token={$token}";
                    $input = [
                        'postURLs' => [$cleanUrl],
                        'resultsPerPage' => 1,
                        'shouldDownloadVideos' => false
                    ];
                } else {
                    $endpoint = "https://api.apify.com/v2/acts/{$igActorId}/run-sync-get-dataset-items?token={$token}";
                    $input = [
                        'directUrls' => [$cleanUrl],
                        'resultsType' => 'posts',
                        'resultsLimit' => 1
                    ];
                }

                $response = Http::timeout(180)->post($endpoint, $input);

                if ($response->successful()) {
                    $data = $response->json();

                    if (!empty($data) && isset($data[0])) {
                        $item = $data[0];

                        $views = 0;
                        $likes = 0;
                        $comments = 0;
                        $shares = 0;
                        $saves = 0;
                        $reposts = 0;
                        $username = null;
                        $caption = null;
                        $postDate = null;
                        $postType = null;

                        if ($isTikTok) {
                            $views = $item['playCount'] ?? $item['viewsCount'] ?? 0;
                            $likes = $item['diggCount'] ?? $item['likesCount'] ?? 0;
                            $comments = $item['commentCount'] ?? $item['commentsCount'] ?? 0;
                            $shares = $item['shareCount'] ?? $item['sharesCount'] ?? 0;
                            $saves = $item['collectCount'] ?? $item['bookmarksCount'] ?? 0;
                            $reposts = $item['repostCount'] ?? 0;
                            $username = $item['authorMeta']['name'] ?? $item['authorMeta']['nickName'] ?? ($item['author']['uniqueId'] ?? 'TikTok User');
                            $caption = $item['text'] ?? null;
                            $postDate = isset($item['createTime']) ? date('Y-m-d H:i:s', is_numeric($item['createTime']) ? $item['createTime'] : strtotime($item['createTime'])) : null;
                            $postType = 'Video';
                        } else {
                            // Instagram Logic
                            $views = $item['playCount'] ?? $item['videoViewCount'] ?? $item['videoPlayCount'] ?? $item['viewCount'] ?? 0;
                            $likes = $item['likesCount'] ?? $item['likeCount'] ?? 0;
                            $comments = $item['commentsCount'] ?? $item['commentCount'] ?? 0;
                            $shares = $item['sharesCount'] ?? 0;
                            $saves = $item['savesCount'] ?? 0;
                            
                            $postType = $item['type'] ?? $item['product_type'] ?? null;
                            $caption = $item['caption'] ?? $item['text'] ?? null;
                            
                            if (isset($item['timestamp'])) {
                                $postDate = date('Y-m-d H:i:s', strtotime($item['timestamp']));
                            } elseif (isset($item['created_at'])) {
                                $postDate = date('Y-m-d H:i:s', is_numeric($item['created_at']) ? $item['created_at'] : strtotime($item['created_at']));
                            }

                            $username = $item['ownerUsername'] ?? $item['ownerFullName'] ?? 'IG User';
                        }

                        // Hitung Engagement Rate: (Likes + Comments + Shares) / Views * 100
                        $er = 0;
                        if ($views > 0) {
                            $er = (($likes + $comments + $shares) / $views) * 100;
                        }

                        // Calculate SAW Score (dummy logic)
                        $sawScore = ($er * 0.5) + (($likes > 100 ? 10 : ($likes / 10)) * 0.3) + (($comments > 50 ? 10 : ($comments / 5)) * 0.2);

                        $link->update([
                            'status_scraping' => 'Completed',
                            'views' => $views,
                            'likes' => $likes,
                            'comments' => $comments,
                            'shares' => $shares,
                            'saves' => $saves,
                            'reposts' => $reposts,
                            'engagement_rate' => min(100, $er),
                            'saw_score' => min(10, $sawScore),
                            'username' => $username,
                            'post_type' => $postType,
                            'caption' => $caption,
                            'post_date' => $postDate,
                            'tanggal_upload' => $postDate ? date('Y-m-d', strtotime($postDate)) : date('Y-m-d'),
                            'updated_at' => now(),
                        ]);

                        $affectedCampaigns[$link->campaign_id] = true;
                        $successCount++;
                    } else {
                        $link->update(['status_scraping' => 'Gagal']);
                        $errors[] = "Data kosong untuk link: {$link->url}";
                    }
                } else {
                    $link->update(['status_scraping' => 'Gagal']);
                    $errors[] = "Gagal memproses {$link->url} (Status: {$response->status()})";
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $link->update(['status_scraping' => 'Gagal']);
                $errors[] = "Timeout saat memproses link: {$link->url}";
            } catch (Exception $e) {
                $link->update(['status_scraping' => 'Gagal']);
                $errors[] = "Error sistem pada {$link->url}: {$e->getMessage()}";
            }
        }

        // Kalkulasi Skor SAW untuk setiap campaign yang datanya baru terupdate
        foreach (array_keys($affectedCampaigns) as $campaignId) {
            $this->calculateSawScoresForCampaign($campaignId);
        }

        $remainingPending = Link::where('status_scraping', 'Pending')->count();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => $successCount > 0,
                'processed_count' => $successCount,
                'errors' => $errors,
                'remaining_pending' => $remainingPending,
                'message' => "Memproses {$successCount} link."
            ]);
        }

        if (count($errors) > 0 && $successCount === 0) {
            return redirect()->back()->with('error', 'Semua link gagal diproses: ' . implode(' | ', $errors));
        }

        if (count($errors) > 0 && $successCount > 0) {
            return redirect()->back()->with('warning', "Berhasil memproses {$successCount} link, namun beberapa link gagal: " . implode(' | ', $errors));
        }

        return redirect()->back()->with('success', "Sukses memproses {$successCount} link dan memperbarui ranking SAW!");
    }

    /**
     * Cek koneksi token Apify
     */
    public function testApifyConnection()
    {
        $token = env('APIFY_TOKEN', env('APIFY_API_TOKEN'));

        if (empty($token)) {
            return redirect()->back()->with('error', 'Token Apify belum diatur di file .env!');
        }

        try {
            $response = Http::timeout(10)->get("https://api.apify.com/v2/users/me?token={$token}");

            if ($response->successful()) {
                $user = $response->json()['data']['username'] ?? 'User Apify';
                return redirect()->back()->with('success', "Koneksi Apify Berhasil! Terhubung ke akun: {$user}");
            }

            return redirect()->back()->with('error', 'Koneksi Apify Gagal! Pastikan API Token valid.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Koneksi Apify Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Helper pembersihan & pemastian format URL (mendukung vt.tiktok.com, vm.tiktok.com, dll)
     */
    private function sanitizeUrl(?string $rawUrl): ?string
    {
        if (empty($rawUrl)) return null;

        // 1. Decode HTML entities (&nbsp;, &amp;, dll)
        $str = html_entity_decode((string)$rawUrl, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 2. Strip tags
        $str = strip_tags($str);

        // 3. Ganti non-breaking spaces (\xC2\xA0 / &nbsp;), BOM (\xEF\xBB\xBF), dan karakter kontrol dengan spasi biasa
        $str = preg_replace('/[\x00-\x1F\x7F\xC2\xA0\xEF\xBB\xBF]/u', ' ', $str);

        // 4. Trim spasi dan tanda petik/kurung
        $trimmed = trim($str, " \t\n\r\0\x0B\"'`()[]{}<>");
        if (empty($trimmed)) return null;

        $lowerTrimmed = strtolower($trimmed);

        // Filter out W3C/Microsoft XML schema metadata URLs
        if (str_contains($lowerTrimmed, 'w3.org') || str_contains($lowerTrimmed, 'schemas.microsoft.com') || str_contains($lowerTrimmed, 'schemas-microsoft-com')) {
            return null;
        }

        // 5. Cari pola URL TikTok / Instagram
        if (preg_match('/(?:https?:\/\/|[a-z0-9\.\-]*tiktok\.com|[a-z0-9\.\-]*instagram\.com|instagr\.am|ig\.me)[^\s"<>\'\,\;\r\n]+/i', $trimmed, $match)) {
            $extracted = $match[0];

            // Tambahkan https:// jika belum ada
            if (!preg_match('/^https?:\/\//i', $extracted)) {
                $extracted = 'https://' . ltrim($extracted, '/');
            }

            $cleanUrl = strtok($extracted, '?');
            $cleanUrl = rtrim($cleanUrl, '.,;');
            $lowerClean = strtolower($cleanUrl);

            if (str_contains($lowerClean, 'tiktok') || str_contains($lowerClean, 'instagram') || str_contains($lowerClean, 'instagr.am') || str_contains($lowerClean, 'ig.me')) {
                return $cleanUrl;
            }
        }

        return null;
    }

    /**
     * Helper deteksi platform berdasarkan string URL
     */
    private function detectPlatform(string $url, ?string $fallbackPlatform = null): string
    {
        $lower = strtolower($url);
        if (str_contains($lower, 'tiktok') || str_contains($lower, 'vt.tiktok') || str_contains($lower, 'vm.tiktok')) {
            return 'TikTok';
        } elseif (str_contains($lower, 'instagram') || str_contains($lower, 'instagr.am') || str_contains($lower, '/p/') || str_contains($lower, '/reel/')) {
            return 'Instagram';
        }

        if (!empty($fallbackPlatform) && in_array(ucfirst(strtolower($fallbackPlatform)), ['TikTok', 'Instagram'])) {
            return ucfirst(strtolower($fallbackPlatform));
        }

        return 'Unknown';
    }

    /**
     * Helper parse angka dari format string (misal: "7.381" -> 7381, "398.900" -> 398900)
     */
    private function parseFormattedNumber($raw): int
    {
        if (empty($raw)) return 0;
        $str = trim((string)$raw);
        // Hapus semua karakter bukan angka (misal titik ribuan, koma, spasi)
        $cleaned = preg_replace('/[^\d]/', '', $str);
        return (int)$cleaned;
    }

    /**
     * Helper parse tanggal upload dari string (misal: "Sabtu, 08 Agustus 2026")
     */
    private function parseUploadedDate($dateStr): string
    {
        if (empty($dateStr)) {
            return now()->toDateString();
        }

        $clean = trim((string)$dateStr);

        $indoMonths = [
            'Januari' => 'January', 'Februari' => 'February', 'Maret' => 'March',
            'April' => 'April', 'Mei' => 'May', 'Juni' => 'June',
            'Juli' => 'July', 'Agustus' => 'August', 'September' => 'September',
            'Oktober' => 'October', 'November' => 'November', 'Desember' => 'December',
            'Agus' => 'August', 'Okto' => 'October', 'Nop' => 'November', 'Des' => 'December'
        ];

        foreach ($indoMonths as $indo => $eng) {
            $clean = str_ireplace($indo, $eng, $clean);
        }

        $clean = preg_replace('/^(senin|selasa|rabu|kamis|jumat|sabtu|minggu)\,?\s*/i', '', $clean);

        $timestamp = strtotime($clean);
        if ($timestamp && $timestamp > 0) {
            return date('Y-m-d', $timestamp);
        }

        return now()->toDateString();
    }

    /**
     * Helper mengekstrak data 1 baris sel tabel Excel / CSV
     */
    private function extractRowDataFromCells(array $rowCells, array $headerMap): ?array
    {
        $url = null;

        // Cek sel URL berdasarkan kolom terpetakan dari header
        if (isset($headerMap['url']) && isset($rowCells[$headerMap['url']])) {
            $url = $this->sanitizeUrl($rowCells[$headerMap['url']]);
        }

        // Jika tidak ditemukan di kolom terpetakan, cari di semua sel baris ini
        if (!$url) {
            foreach ($rowCells as $cell) {
                $foundUrl = $this->sanitizeUrl($cell);
                if ($foundUrl) {
                    $url = $foundUrl;
                    break;
                }
            }
        }

        if (!$url) {
            return null; // Lewati jika tidak ada URL TikTok / Instagram
        }

        $username = isset($headerMap['username']) && isset($rowCells[$headerMap['username']]) 
            ? trim(strip_tags($rowCells[$headerMap['username']])) 
            : null;

        $tglRaw = isset($headerMap['tanggal_upload']) && isset($rowCells[$headerMap['tanggal_upload']]) 
            ? $rowCells[$headerMap['tanggal_upload']] 
            : null;
        $tanggalUpload = $this->parseUploadedDate($tglRaw);

        $views = isset($headerMap['views']) && isset($rowCells[$headerMap['views']]) 
            ? $this->parseFormattedNumber($rowCells[$headerMap['views']]) 
            : 0;

        $likes = isset($headerMap['likes']) && isset($rowCells[$headerMap['likes']]) 
            ? $this->parseFormattedNumber($rowCells[$headerMap['likes']]) 
            : 0;

        $comments = isset($headerMap['comments']) && isset($rowCells[$headerMap['comments']]) 
            ? $this->parseFormattedNumber($rowCells[$headerMap['comments']]) 
            : 0;

        $saves = isset($headerMap['saves']) && isset($rowCells[$headerMap['saves']]) 
            ? $this->parseFormattedNumber($rowCells[$headerMap['saves']]) 
            : 0;

        $shares = isset($headerMap['shares']) && isset($rowCells[$headerMap['shares']]) 
            ? $this->parseFormattedNumber($rowCells[$headerMap['shares']]) 
            : 0;

        return [
            'url' => $url,
            'username' => $username,
            'tanggal_upload' => $tanggalUpload,
            'views' => $views,
            'likes' => $likes,
            'comments' => $comments,
            'saves' => $saves,
            'shares' => $shares,
        ];
    }

    /**
     * Perhitungan Algoritma SAW (Simple Additive Weighting) per Campaign
     * Bobot: Share 35%, Comment 25%, Like 20%, View 10%, Save 10%
     */
    private function calculateSawScoresForCampaign(int $campaignId): void
    {
        $links = Link::where('campaign_id', $campaignId)
            ->whereIn('status_scraping', ['Completed', 'Berhasil'])
            ->get();

        if ($links->isEmpty()) {
            return;
        }

        // Ambil nilai maksimum kriteria benefit
        $maxShare = $links->max('shares') ?: 1;
        $maxComment = $links->max('comments') ?: 1;
        $maxLike = $links->max('likes') ?: 1;
        $maxView = $links->max('views') ?: 1;
        $maxSave = $links->max('saves') ?: 1;

        foreach ($links as $link) {
            // Normalisasi Matriks R
            $rShare = $link->shares / $maxShare;
            $rComment = $link->comments / $maxComment;
            $rLike = $link->likes / $maxLike;
            $rView = $link->views / $maxView;
            $rSave = $link->saves / $maxSave;

            // Hitung Preferensi SAW (V_i)
            $sawScore = (0.35 * $rShare) + 
                        (0.25 * $rComment) + 
                        (0.20 * $rLike) + 
                        (0.10 * $rView) + 
                        (0.10 * $rSave);

            $link->update([
                'saw_score' => round($sawScore, 4)
            ]);
        }
    }

    /**
     * Cek Saldo & Limit Kuota Apify Account
     */
    public function getApifyStatus()
    {
        $token = env('APIFY_TOKEN', env('APIFY_API_TOKEN'));
        if (empty($token)) {
            return response()->json([
                'configured' => false,
                'message' => 'Token Apify belum dikonfigurasi di .env'
            ]);
        }

        try {
            $response = Http::timeout(8)
                ->withToken($token)
                ->get('https://api.apify.com/v2/users/me');

            if ($response->successful()) {
                $userData = $response->json('data', []);

                $username = $userData['username'] ?? ($userData['email'] ?? 'User Apify');
                $planName = $userData['plan']['name'] ?? ($userData['plan']['id'] ?? 'Free');

                $maxUsageUsd = (float)($userData['plan']['maxMonthlyUsageUsd'] 
                    ?? ($userData['limits']['monthlyUsageUsd'] ?? 5.00));
                    
                $currentUsageUsd = (float)($userData['monthlyUsage']['totalUsageUsd'] 
                    ?? ($userData['stats']['usageUsd'] ?? 0.00));

                if (!isset($userData['monthlyUsage']['totalUsageUsd'])) {
                    $usageRes = Http::timeout(5)->withToken($token)->get('https://api.apify.com/v2/users/me/usage/monthly');
                    if ($usageRes->successful()) {
                        $currentUsageUsd = (float)$usageRes->json('data.totalUsageUsd', $currentUsageUsd);
                    }
                }

                $remainingUsd = max(0, $maxUsageUsd - $currentUsageUsd);
                $percentageUsed = $maxUsageUsd > 0 ? min(100, round(($currentUsageUsd / $maxUsageUsd) * 100, 1)) : 0;

                return response()->json([
                    'configured' => true,
                    'username' => $username,
                    'plan_name' => $planName,
                    'usage_usd' => number_format($currentUsageUsd, 2),
                    'limit_usd' => number_format($maxUsageUsd, 2),
                    'remaining_usd' => number_format($remainingUsd, 2),
                    'percentage_used' => $percentageUsed
                ]);
            }

            return response()->json([
                'configured' => false,
                'message' => 'Gagal mengontak Apify API (Status: ' . $response->status() . ')'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'configured' => false,
                'message' => 'Koneksi Apify error: ' . $e->getMessage()
            ]);
        }
    }
}