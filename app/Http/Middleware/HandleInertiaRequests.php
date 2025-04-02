<?php

namespace App\Http\Middleware;

use App\Helpers\Debug;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware {
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request) {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request) {
        // Debug::info($request->path());
        // Debug::info($request->all());
        // Debug::info($request->isMethod('get'));
        // Debug::info(!$request->ajax());
        // Debug::info(!$request->wantsJson());
        $user = $request->user();

        $pageProps = array_merge(parent::share($request), [
            'flash' => [
                'message' => $request->session()->get('message'),
                'error' => $request->session()->get('error'),
            ]]);

        if (Auth::check() && $request->isMethod('get')
            // && !$request->ajax()
             && !$request->wantsJson()

        ) {
            $menus = Menu::getMenu(true);
            $roles = $user->roles;
            unset($user->roles);
            $user->rol_selected = $request->session()->get('rol_id');

            $pageProps = array_merge($pageProps,
                ['menus_layout' => $menus],
                ['auth' => ['user' => $user, 'roles' => $roles, 'rol_selected' => $user->rol_selected]]
            );
        }

        return $pageProps;
    }
}
