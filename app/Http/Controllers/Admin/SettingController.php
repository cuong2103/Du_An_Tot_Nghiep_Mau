<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $settingsCollection = SystemSetting::all();
        $settings = [];
        foreach ($settingsCollection as $setting) {
            $settings[$setting->setting_key] = $setting->setting_value;
        }

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token', '_method');
        $userId = Auth::id();

        foreach ($data['settings'] as $key => $value) {
            // value could be null if empty, convert to empty string if needed
            SystemSetting::updateOrCreate(
                ['setting_key' => $key],
                [
                    'setting_value' => $value ?? '',
                    'updated_by' => $userId
                ]
            );
        }

        SystemLog::create([
            'user_id' => $userId,
            'action' => 'SETTINGS_UPDATED',
            'module' => 'settings',
            'description' => 'Cập nhật cài đặt hệ thống',
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.settings.index')->with('success', 'Đã lưu cài đặt thành công.');
    }

    public function logs(Request $request)
    {
        $query = SystemLog::with('user')->latest();

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('action_search')) {
            $query->where('action', 'like', '%' . $request->action_search . '%');
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(30)->withQueryString();
        $users = User::where('is_active', true)->orderBy('full_name')->get();

        $modules = [
            'auth', 'users', 'doctors', 'specialties', 'rooms', 
            'work-schedules', 'appointments', 'cms', 'faq', 
            'chatbot', 'notifications', 'settings'
        ];

        return view('admin.settings.logs', compact('logs', 'users', 'modules'));
    }
}
