<?php

declare(strict_types=1);

use App\Http\Controllers\Teams\AcceptTeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function (): void {
        Route::inertia('dashboard', 'dashboard')->name('dashboard');
    });

Route::middleware(['auth'])->group(function (): void {
    Route::get('invitations/{invitation}/accept', AcceptTeamInvitationController::class)->name('invitations.accept');
});

require __DIR__.'/settings.php';
