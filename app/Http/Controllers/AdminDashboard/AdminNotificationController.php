<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminNotificationController extends Controller
{
    function index() {
        $admin = Admin::find(auth()->guard('admin')->id());
        return response()->json([
            'notification' => $admin->notifications,
        ]);
    }
    function unread () {
        $admin = Admin::find(auth()->guard('admin')->id());
        return response()->json([
            'notification' => $admin->unreadNotifications,
        ]);
    }
    function markReadAll () {
        $admin = Admin::find(auth()->guard('admin')->id());
        foreach ($admin->unreadNotifications as $notification) {
            $notification->markAsRead();
        }
        return response()->json([
            'message' => 'successfully read all posts',
        ]);
    }

    function deleteAll () {
        $admin = Admin::find(auth()->guard('admin')->id());
        $admin->notifications()->delete();
        return response()->json([
            'message' => 'successfully deleted all posts read '
        ],200);
    }
    function delete ($id) {
        try{
            DB::table('notifications')->where('id',$id)->delete();
        return response()->json([
            'message' => 'successfully deleted  posts read '
        ],200);
        }catch(Exception $e) {
            return response()->json([
                'error'=>$e->getMessage()
            ]);
        }
    }
}
