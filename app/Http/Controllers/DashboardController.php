<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\SettingHighlight;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session as FacadesSession;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::now()->today();

        $firstTimeLogin = FacadesSession::get('first_time_logged_in');
        $announcements = Announcement::query()
            ->with('media')
            ->where('status', 'Active')
            ->where('popup', true)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->latest()
            ->get()
            ->map(function ($announcement) {
                return [
                    'title' => $announcement->title,
                    'content' => $announcement->content,
                    'announcement_image' => $announcement->getFirstMediaUrl('announcement_image'),
                ];
            });

        $highlights = SettingHighlight::query()->latest()->first();

        return Inertia::render('Dashboard', [
            'firstTimeLogin' => $firstTimeLogin,
            'announcements' => $announcements,
            'highlights' => $highlights->getMedia('highlights'),
        ]);
    }

    public function markAsRead(Request $request)
    {
        $user = \Auth::user();
        $notifications = $user->unreadNotifications;

        foreach ($notifications as $notification) {
            if ($notification->id == $request->id && $notification->read_at == null) {
                $notification->markAsRead();
            }
        }

        return redirect()->back();
    }
}
