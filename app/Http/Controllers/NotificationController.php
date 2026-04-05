<?php

namespace App\Http\Controllers;

use App\Models\notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Récupère la liste des 10 dernières notifications de l'utilisateur connecté via AJAX.
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest('date_envoi')->limit(5)->get();
        $unreadCount = $user->notifications()->where('lu', false)->count();
        
        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);
    }

    /**
     * Marque une notification spécifique comme "lue".
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->update(['lu' => true]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Marque toutes les notifications non lues de l'utilisateur comme "lues".
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()->where('lu', false)->update(['lu' => true]);
        
        return response()->json(['success' => true]);
    }
}
