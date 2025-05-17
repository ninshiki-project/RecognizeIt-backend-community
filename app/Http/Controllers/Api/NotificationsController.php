<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationsController extends Controller
{
    /**
     * Get all notifications
     *
     * @param  Request  $request
     * @return AnonymousResourceCollection<LengthAwarePaginator<NotificationResource>>
     */
    #[QueryParameter('user', description: 'User ID. If not part of request the authenticated user will be used.', type: 'int', default: null)]
    #[QueryParameter('unread', description: 'Get all unread notification.', type: 'int', default: 0, example: 1)]
    #[QueryParameter('read', description: 'Get all read notification.', type: 'int', default: 0, example: 0)]
    public function index(Request $request)
    {
        $request->validate([
            'user' => ['nullable', 'int'],
            'unread' => ['nullable', 'boolean', 'prohibited_if:read,true'],
            'read' => ['nullable', 'boolean', 'prohibited_if:unread,true'],
        ]);

        $request->mergeIfMissing([
            'user' => auth()->user()->id,
        ]);
        $user = User::find($request->user)->load('notifications');

        if ($request->unread) {
            /** @var User $user */
            return NotificationResource::collection($user->notifications()->unread()->get());
        }

        if ($request->read) {
            /** @var User $user */
            return NotificationResource::collection($user->notifications()->read()->get());
        }

        /** @var User $user */
        return NotificationResource::collection($user->notifications()->get());
    }

    /**
     * Mark the notification as read
     *
     * @param  Request  $request
     * @param  string  $id
     * @return JsonResponse
     */
    #[QueryParameter('user', description: 'User ID. If not part of request the authenticated user will be used.', type: 'int', default: null)]
    #[PathParameter('id', description: 'Notification ID', required: true, type: 'string', example: 'uuid-string')]
    public function markAsRead(Request $request, string $id)
    {
        $request->mergeIfMissing([
            'user' => auth()->user()->id,
        ]);
        $user = User::find($request->user)->load('notifications');

        /** @var User $user */
        $notification = $user->unreadNotifications()->where('id', $id)->first();

        if (! $notification) {
            /** @status 404 */
            return response()->json([
                'success' => false,
                'message' => 'Notification record not found',
            ], 404);
        }

        /** @var DatabaseNotification $notification */
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read
     *
     * This will mark all notifications as read for the authenticated user.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    #[QueryParameter('user', description: 'User ID. If not part of request the authenticated user will be used.', type: 'int', default: null)]
    public function markAllAsRead(Request $request)
    {
        $request->mergeIfMissing([
            'user' => auth()->user()->id,
        ]);
        $user = User::find($request->user)->load('notifications');

        /** @var User $user */
        $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);

    }
}
