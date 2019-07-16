<?php

namespace App\Guards;

use Tymon\JWTAuth\JWTGuard;
use Tymon\JWTAuth\JWT;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Contracts\Events\Dispatcher;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\Events\Login as DefaultLogin;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtEventGuard extends JWTGuard
{
    /**
     * The name of the Guard. Typically "jwt_event".
     *
     * Corresponds to guard name in authentication configuration.
     *
     * @var string
     */
    protected $name;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * Instantiate the class.
     *
     * @param  \Tymon\JWTAuth\JWT  $jwt
     * @param  \Illuminate\Contracts\Auth\UserProvider  $provider
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(JWT $jwt, UserProvider $provider, Request $request, Dispatcher $events)
    {
        $this->name = 'jwt_event';
        $this->jwt = $jwt;
        $this->provider = $provider;
        $this->request = $request;
        $this->events = $events;
    }

    /**
     * Create a token for a user.
     *
     * @param  \Tymon\JWTAuth\Contracts\JWTSubject  $user
     *
     * @return string
     */
    public function login(JWTSubject $user)
    {
        $token = $this->jwt->fromUser($user);

        $this->setToken($token)->setUser($user);
        if (! is_null($user->token)) {
            if (! empty($user->token)) {
                JWTAuth::setToken($user->token)->invalidate();
            }
            $user->token = $token;
        }

        $this->fireLoginEvent($user);

        return $token;
    }

    /**
     * Fire the login event if the dispatcher is set.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  bool  $remember
     * @return void
     */
    protected function fireLoginEvent($user, $remember = false)
    {
        if (isset($this->events)) {
            $this->events->dispatch(new DefaultLogin(
                $this->name,
                $user,
                $remember
            ));
        }
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return \Illuminate\Contracts\Events\Dispatcher
     */
    public function getDispatcher()
    {
        return $this->events;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function setDispatcher(Dispatcher $events)
    {
        $this->events = $events;
    }
}
