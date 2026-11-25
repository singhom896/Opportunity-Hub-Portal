<?php

namespace App\Http\Middleware;

use App\Models\Module;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, $action)
    {  
        $backendPrefix = trim((string) config('app.backend_prefix'), '/');

        // Get the current URL segments and remove the backend prefix if present.
        $segments = $request->segments();

        if ($backendPrefix !== '' && ($segments[0] ?? null) === $backendPrefix) {
            array_shift($segments);
        }

        // Preserve the module name for permission messages and special handling.
        $moduleName = $segments[0] ?? '';

        if ($moduleName === 'permissions' && Gate::allows('manage-permissions')) {
            return $next($request);
        }

        // Match the most specific active module URL first, e.g.
        // /artist/pending-registrations, then fall back to its parent /artist.
        $modulePath = '/' . implode('/', $segments);

        $module = Module::where('page_url', $modulePath)
            ->where('active', 1)
            ->first();

        if (!$module && $moduleName !== '') {
            $module = Module::where('page_url', '/' . $moduleName)
                ->where('active', 1)
                ->first();
        }

        if (!$module) {
            abort(404, "Module '{$modulePath}' not found.");
        }

        // Support one or multiple permissions, e.g. view or create|edit.
        $actions = array_filter(array_map('trim', explode('|', $action)));
        $hasPermission = false;

        foreach ($actions as $permission) {
            if (Gate::allows('has-permission', [
                $module->module_id,
                $permission,
            ])) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "You do not have permission to access {$modulePath}.",
                ], 403);
            }

            return response()->view('errors.permission', [
                'action' => $action,
                'module' => $moduleName,
            ], 403);
        }

        return $next($request);
    }
}
