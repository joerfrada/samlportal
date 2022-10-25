<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use App\Models\Usuario;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('Aacotroneo\Saml2\Events\Saml2LogoutEvent', function ($event) {
            \Auth::logout();
        });

        Event::listen('Aacotroneo\Saml2\Events\Saml2LoginEvent', function (Saml2LoginEvent $event) {
            $user = $event->getSaml2User();
            $userData = [
                'id' => $user->getUserId(),
                'attributes' => $user->getAttributes(),
                'assertion' => $user->getRawSamlAssertion(),
                'sessionIndex' => $user->getSessionIndex(),
                'nameId' => $user->getNameId()
            ];
            
            //check if email already exists and fetch user
            $user = \App\Usuario::where('email', $userData['attributes']['EmailAddress'][0])->first();
            
            //if email doesn't exist, create new user
            if ($user === null)
            {
                $email = $userData['attributes']['EmailAddress'][0];
                $user = new \App\Usuario;
                $user->usuario = substr($email, 0, stripos($email, '@'));
                $user->nombres = $userData['attributes']['FirstName'][0];
                $user->apellidos = $userData['attributes']['LastName'][0];
                $user->email = $email;
                $user->activo = 'S';
                $user->crud_usuarios($user);
            }
            
            //insert sessionIndex and nameId into session
            session(['sessionIndex' => $userData['sessionIndex']]);
            session(['nameId' => $userData['nameId']]);

            //login user
            \Auth::login($user);
        });
    }
}
