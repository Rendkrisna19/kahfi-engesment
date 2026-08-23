<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserCampaignAccessController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Halaman Utama
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Dashboard Utama
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {

    $role = Auth::user()->role;

    return match ($role) {
        'Admin Master' => redirect()->route('dashboard.admin-master'),
        'Admin' => redirect()->route('dashboard.admin'),
        'Client' => redirect()->route('dashboard.client'),

        default => abort(403, 'Role tidak dikenali.'),
    };
})->middleware('auth')->name('dashboard');


/*
|--------------------------------------------------------------------------
| Dashboard Admin Master
|--------------------------------------------------------------------------
*/

Route::get('/dashboard/admin-master', [\App\Http\Controllers\DashboardController::class, 'adminMaster'])
    ->middleware(['auth', 'can:dashboard.view'])
    ->name('dashboard.admin-master');


/*
|--------------------------------------------------------------------------
| Dashboard Admin
|--------------------------------------------------------------------------
*/

Route::get('/dashboard/admin', [\App\Http\Controllers\DashboardController::class, 'admin'])
    ->middleware(['auth', 'can:dashboard.view'])
    ->name('dashboard.admin');


/*
|--------------------------------------------------------------------------
| Dashboard Client
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:dashboard.view'])->group(function () {
    Route::get('/dashboard/client', [\App\Http\Controllers\DashboardController::class, 'client'])->name('dashboard.client');
    Route::get('/export/client/pdf', [\App\Http\Controllers\ExportController::class, 'clientPdf'])->name('export.client.pdf');
    Route::get('/export/client/excel', [\App\Http\Controllers\ExportController::class, 'clientExcel'])->name('export.client.excel');
});

Route::middleware(['auth', 'can:laporan.view'])->group(function () {
    Route::get('/laporan', [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/{laporan}', [\App\Http\Controllers\LaporanController::class, 'show'])->name('laporan.show');
});


/*
|--------------------------------------------------------------------------
| Kelola User - Master Data
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:users.view'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    
    Route::middleware('can:users.create')->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });

    Route::middleware('can:users.edit')->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}', [UserController::class, 'update']);
    });

    Route::middleware('can:users.delete')->group(function () {
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});

Route::middleware(['auth', 'can:roles.view'])->group(function () {
    Route::get('/roles', [\App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');
    
    Route::middleware('can:roles.create')->group(function () {
        Route::get('/roles/create', [\App\Http\Controllers\RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [\App\Http\Controllers\RoleController::class, 'store'])->name('roles.store');
    });

    Route::middleware('can:roles.edit')->group(function () {
        Route::get('/roles/{role}/edit', [\App\Http\Controllers\RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [\App\Http\Controllers\RoleController::class, 'update'])->name('roles.update');
        Route::patch('/roles/{role}', [\App\Http\Controllers\RoleController::class, 'update']);
    });

    Route::middleware('can:roles.delete')->group(function () {
        Route::delete('/roles/{role}', [\App\Http\Controllers\RoleController::class, 'destroy'])->name('roles.destroy');
    });
});

Route::middleware(['auth', 'can:master-data.view'])->group(function () {
    Route::resource('kategori-konten', \App\Http\Controllers\KategoriKontenController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['kategori-konten' => 'kategori_konten']);

    Route::resource('kategori-creator', \App\Http\Controllers\KategoriCreatorController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['kategori-creator' => 'kategori_creator']);
});


/*
|--------------------------------------------------------------------------
| Kelola Campaign
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:campaigns.view'])->group(function () {
    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    
    Route::middleware('can:campaigns.create')->group(function () {
        Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create');
        Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    });

    Route::middleware('can:campaigns.edit')->group(function () {
        Route::get('/campaigns/{campaign}/edit', [CampaignController::class, 'edit'])->name('campaigns.edit');
        Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
        Route::patch('/campaigns/{campaign}', [CampaignController::class, 'update']);
    });

    Route::middleware('can:campaigns.delete')->group(function () {
        Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');
    });

    Route::prefix('dashboard/admin-master/campaign-access')->group(function () {
        Route::get('/', [UserCampaignAccessController::class, 'index'])->name('campaign-access.index');
        Route::get('/{campaign}/edit', [UserCampaignAccessController::class, 'edit'])->name('campaign-access.edit');
        Route::put('/{campaign}', [UserCampaignAccessController::class, 'update'])->name('campaign-access.update');
    });
});


/*
|--------------------------------------------------------------------------
| Operasional Konten
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:operasional-konten.view'])
    ->prefix('operasional-konten')
    ->group(function () {
        Route::get('/template', [\App\Http\Controllers\LinkController::class, 'downloadTemplate'])->name('operasional-konten.template');
        Route::get('/test-apify', [\App\Http\Controllers\LinkController::class, 'testApifyConnection'])->name('operasional-konten.test-apify');
        Route::get('/', [\App\Http\Controllers\LinkController::class, 'index'])->name('operasional-konten.index');
        Route::get('/{operasional_konten}', [\App\Http\Controllers\LinkController::class, 'show'])->name('operasional-konten.show');

        Route::middleware('can:operasional-konten.create')->group(function () {
            Route::post('/store', [\App\Http\Controllers\LinkController::class, 'store'])->name('operasional-konten.store');
            Route::post('/refresh', [\App\Http\Controllers\LinkController::class, 'refreshData'])->name('operasional-konten.refresh');
        });

        Route::middleware('can:operasional-konten.delete')->group(function () {
            Route::delete('/bulk-delete', [\App\Http\Controllers\LinkController::class, 'destroyBulk'])->name('operasional-konten.destroy-bulk');
            Route::delete('/{operasional_konten}', [\App\Http\Controllers\LinkController::class, 'destroy'])->name('operasional-konten.destroy');
        });
    });

Route::middleware(['auth', 'can:update-saw.view'])
    ->prefix('update-saw')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\UpdateSawController::class, 'index'])->name('update-saw.index');
        Route::get('/{id}', [\App\Http\Controllers\UpdateSawController::class, 'show'])->name('update-saw.show');
        Route::post('/{id}/process', [\App\Http\Controllers\UpdateSawController::class, 'process'])
            ->middleware('can:update-saw.process')
            ->name('update-saw.process');
    });

Route::middleware(['auth', 'role:Admin'])
    ->prefix('my-campaigns')
    ->group(function () {

        Route::get('/', [
            CampaignController::class,
            'adminIndex'
        ])->name('admin.campaigns.index');


        Route::get('/{campaign}', [
            CampaignController::class,
            'adminShow'
        ])->name('admin.campaigns.show');


        Route::put('/{campaign}/content', [
            CampaignController::class,
            'updateContent'
        ])->name('admin.campaigns.content.update');
    });


/*
|--------------------------------------------------------------------------
| Client - Campaign Miliknya
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Client'])
    ->prefix('my-campaigns')
    ->group(function () {

        Route::get('/', [
            CampaignController::class,
            'clientIndex'
        ])->name('client.campaigns.index');


        Route::get('/{campaign}', [
            CampaignController::class,
            'clientShow'
        ])->name('client.campaigns.show');
    });


/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:profile.edit'])->group(function () {

    Route::get('/profile', [
        ProfileController::class,
        'edit'
    ])->name('profile.edit');


    Route::patch('/profile', [
        ProfileController::class,
        'update'
    ])->name('profile.update');


    Route::delete('/profile', [
        ProfileController::class,
        'destroy'
    ])->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
