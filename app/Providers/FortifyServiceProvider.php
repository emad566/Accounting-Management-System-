<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Controllers\adminsController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
// use Laravel\Fortify\Actions\AttemptToAuthenticate;
// use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use App\Actions\Fortify\AttemptToAuthenticate;
use App\Actions\Fortify\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use Illuminate\Support\Facades\Auth;

use Laravel\Fortify\Http\Requests\LoginRequest;
use App\Models\User;
use Hash;


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Fortify::ignoreRoutes();
        $this->app->when([
                    adminsController::class,
                    AttemptToAuthenticate::class,
                    RedirectIfTwoFactorAuthenticatable::class
                    ])
                    ->needs(StatefulGuard::class)
                    ->give(function(){
                        return Auth::guard('admin');
                    });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::authenticateUsing(function (LoginRequest $request) {
            $user = User::where('phone', $request->email)->where('is_active', 1)->first();
            if (!$user)  $user = User::where('email', $request->email)->where('is_active', 1)->first();

            if (
                $user &&
                Hash::check($request->password, $user->password)
            ) {
                return $user;
            }
            else return NULL;
            
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(20)->by($request->email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(20)->by($request->session()->get('login.id'));
        });
    }
}
