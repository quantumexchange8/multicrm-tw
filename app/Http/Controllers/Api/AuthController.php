<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;
use App\Services\Auth\UserLoginDto;
use App\Http\Controllers\Controller;
use App\Services\Auth\CreateAccount;
use App\Services\Auth\CreateUserDto;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\UserLogin;
use App\Http\Requests\Auth\UserRegister;
use App\Http\Resources\User\UserResource;
use App\Exceptions\Auth\InvalidCredentialException;
use App\Http\Requests\Request;
use App\Services\MetaTrader5\User\CreateUser as CreateMetaTraderUser;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard(): Guard
    {
        return Auth::guard('api');
    }

    public function register(UserRegister $request, JWTAuth $auth)
    {

        $inputArray = $request->validated();
        $inputArray += [
            'register_ip' => $request->ip(),
        ];
        $userDto = CreateUserDto::fromValidatedRequest($inputArray);
        $user = (new CreateAccount)->execute($userDto);
        (new CreateMetaTraderUser)->execute($user);

        return $this->createNewToken($auth->fromUser($user), $user);
    }


    public function login(UserLogin $request)
    {
        $credentialDto = UserLoginDto::fromValidatedRequest($request->validated());

        if ($token = $this->guard()->attempt($credentialDto->toArray())) {
            return $this->createNewToken($token, $this->guard()->user());
        }

        throw new InvalidCredentialException;
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(
            $this->guard()->refresh(),
            $this->guard()->user()
        );
    }

    public function logout()
    {
        $this->guard()->logout();

        return response()->json([
            'message' => 'successfully signed out'
        ]);
    }

    protected function createNewToken($token, User $user)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL(),
            'user' => UserResource::make($user)
        ]);
    }

    /*   public function getAuthenticatedUser()
    {
        try {

            if (!$user = FacadesJWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {

            return response()->json(['token_expired'], $e);
        } catch (TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e);
        } catch (JWTException $e) {

            return response()->json(['token_absent'], $e);
        }

        return response()->json(compact('user'));
    } */
}
