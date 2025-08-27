<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Models\Cart;
use App\Models\MediaFile;
use App\Models\Notification;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\CustomerRegistration;
use App\Notifications\EmailVerification;

class AuthController extends Controller
{
    # new sign up
    public function signup(Request $request)
    {
        if ($request->phone && User::where('phone', $request->phone)->first() != null) {
            return response()->json([
                'success' => false,
                'message' => translate('User already exists'),
                'data' => null,
                'authStatus' => 'user_exists_phone'
            ], 409);
        }

        if ($request->email && User::where('email', $request->email)->first() != null) {
            return response()->json([
                'success' => false,
                'message' => translate('User already exists'),
                'data' => null,
                'authStatus' => 'user_exists_email'
            ], 409);
        }


        $user = new User([
            'name' => $request->name,
            'email' => $request->email ? $request->email : null,
            'phone' => $request->phone ? $request->phone : null,
            'password' => Hash::make($request->password),
            'verification_code' => rand(100000, 999999)
        ]);

        if (getSetting('emailVerification') != 1) {
            $user->email_or_otp_verified = 1;
            $user->email_verified_at     = date('Y-m-d H:m:s');
        } else {
            if ($request->authVia == "phone") {
                sendSMSViaBulkSmsBd($request->phone, "Your verification code is " . $user->verification_code . ". " . env('APP_NAME'));
            } else {
                try {
                    $user->notify(new EmailVerification());
                } catch (\Exception $e) {
                    //dd($e->getMessage());
                }
            }
        }
        $user->save();

        if ($request->has('guestUserId') && $request->guestUserId != null) {
            Cart::where('guest_user_id', $request->guestUserId)->update(
                [
                    'user_id' => $user->id,
                    'guest_user_id' => null
                ]
            );
        }

        if (getSetting('emailVerification') == 1) {
            if ($request->authVia == "phone") {
                return response()->json([
                    'success' => true,
                    'message' => translate('A verification code has been sent to your phone'),
                    'authStatus' => 'verify'
                ], 200);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => translate('A verification code has been sent to your email'),
                    'authStatus' => 'verify'
                ], 200);
            }
        }

        // email notification  
        try {
            $user->notify(new CustomerRegistration());
        } catch (\Throwable $th) {
            //throw $th;
        }

        // notification
        Notification::create([
            'shop_id'   => 1,
            'for'       => 'admin',
            'type'      => 'customer-registration',
            'text'      => 'Customer Registration',
            'link_info' => $user->email ? $user->email : $user->phone,
        ]);


        $tokenResult = $user->createToken('Personal Access Token')->plainTextToken;
        return $this->loginSuccess($tokenResult, $user);
    }

    # login
    public function login(Request $request)
    {
        if ($request->authVia == "email") {
            $request->validate([
                'email' => 'required|string',
                'password' => 'required|string',
            ]);
            $credentials = request(['email', 'password']);
        } else {
            $request->validate([
                'phone' => 'required|string',
                'password' => 'required|string',
            ]);
            $credentials = request(['phone', 'password']);
        }
        if (!Auth::attempt($credentials))
            return response()->json([
                'success' => false,
                'message' => translate('Invalid login information'),
                'authStatus' => 'invalid_credentials'
            ], 401);

        $user = $request->user();
        if ($user->banned == 1) {
            return response()->json([
                'success' => false,
                'message' => translate('You are banned & can not login'),
                'authStatus' => 'banned'
            ], 403);
        }
        if ($request->has('guestUserId') && $request->guestUserId != null) {
            Cart::where('guest_user_id', $request->guestUserId)->update(
                [
                    'user_id' => $user->id,
                    'guest_user_id' => null
                ]
            );
        }


        if ($user->user_type == 'customer') {
            if (getSetting('emailVerification') == 1 && $user->email_verified_at == null) {
                if ($request->authVia == "phone") {
                    try {
                        sendSMSViaBulkSmsBd($request->phone, "Your verification code is " . $user->verification_code . ". " . env('APP_NAME'));
                    } catch (\Exception $e) {
                        //dd($e->getMessage());
                    }
                } else {
                    try {
                        $user->notify(new EmailVerification());
                    } catch (\Exception $e) {
                        //dd($e->getMessage());
                    }
                }
                return response()->json([
                    'success' => true,
                    'verified' => false,
                    'message' => translate('Please verify your account'),
                    'authStatus' => 'verify'

                ], 200);
            }
            $tokenResult = $user->createToken('Personal Access Token')->plainTextToken;
            return $this->loginSuccess($tokenResult, $user);
        } else {
            return response()->json([
                'success' => false,
                'message' => translate('Only customers can login here'),
                'authStatus' => 'only_customer_login'
            ], 403);
        }
    }

    # verify account - email
    public function verify(Request $request)
    {
        $user = null;
        if ($request->authVia == "phone") {
            if ($request->phone) {
                $user = User::where('phone', $request->phone)->first();
            }
        } else {
            if ($request->email) {
                $user = User::where('email', $request->email)->first();
            }
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => translate('No user found with your details'),
                'authStatus' => 'user_not_found'
            ], 404);
        }
        if ($user->verification_code != $request->code) {
            return response()->json([
                'success' => false,
                'message' => translate('Code does not match'),
                'authStatus' => 'code_mismatch'
            ], 401);
        } else {
            $user->email_verified_at = date('Y-m-d H:m:s');
            $user->save();
            $tokenResult = $user->createToken('Personal Access Token')->plainTextToken;
            return $this->loginSuccess($tokenResult, $user);
        }
    }

    # resend verification code
    public function resend_code(Request $request)
    {
        $user = null;
        if ($request->authVia == "phone") {
            if ($request->phone) {
                $user = User::where('phone', $request->phone)->first();
            }
        } else {
            if ($request->email) {
                $user = User::where('email', $request->email)->first();
            }
        }
        if ($user != null) {
            $user->verification_code = rand(100000, 999999);
            $user->save();

            if ($request->authVia == "phone") {
                try {
                    sendSMSViaBulkSmsBd($request->phone, "Your verification code is " . $user->verification_code . ". " . env('APP_NAME'));
                    return response()->json([
                        'success'       => true,
                        'status'        => 200,
                        'message'       => translate('A verification code has been sent to your phone'),
                        'authStatus'    => 'verification_code_sent'
                    ], 200);
                } catch (\Exception $e) {
                    //dd($e->getMessage());
                }
            } else {
                try {
                    $user->notify(new EmailVerification());
                    return response()->json([
                        'success'       => true,
                        'status'        => 200,
                        'message'       => translate('A verification code has been sent to your email'),
                        'authStatus'    => 'verification_code_sent'
                    ], 200);
                } catch (\Exception $e) {
                    //dd($e->getMessage());
                }
            }
        } else {
            return response()->json([
                'success'       => false,
                'status'        => 400,
                'message'       => translate('No user found with the provided information'),
                'authStatus'    => 'user_not_found'
            ], 404);
        }
    }

    # verify code
    public function verifyCode(Request $request)
    {
        $user = null;
        if ($request->authVia == "phone") {
            if ($request->phone) {
                $user = User::where('phone', $request->phone)->first();
            }
        } else {
            if ($request->email) {
                $user = User::where('email', $request->email)->first();
            }
        }
        if (!$user) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => translate('No user found with the provided information.'),
                'authStatus' => 'user_not_found'
            ], 404);
        }
        if ($user->verification_code != $request->code) {
            return response()->json([
                'success' => false,
                'status' => 401,
                'message' => translate('Code does not match.'),
                'authStatus' => 'code_mismatch'
            ], 401);
        } else {
            return response()->json([
                'success'   => true,
                'status'    => 200,
                'message'   => translate('Your code has been matched.')
            ], 200);
        }
    }

    # return logged in user
    public function user(Request $request)
    {
        try {
            $user = $request->user();
            return $this->loginSuccess('', $user);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'status' => 401,
                'message' => $th->getMessage(),
            ], 200);
        }
    }

    # logout
    public function logout(Request $request)
    {
        $user = auth('sanctum')->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    # login success response
    protected function loginSuccess($token, $user)
    {
        // carts
        $cartItems = $user->carts(session('WarehouseIds'));

        // user timeline 
        $count = 0;
        $unit  =  translate('Years');
        if (now()->diffInYears($user->created_at) == 0) {
            if (now()->diffInMonths($user->created_at) == 0) {
                $count = now()->diffInDays($user->created_at);
                $unit =  translate('Days');
            } else {
                $count = now()->diffInMonths($user->created_at);
                $unit =  translate('Months');
            }
        } else {
            $count = now()->diffInYears($user->created_at);
        }

        // total orders
        $totalOrders    = $user->orders()->count();
        $totalExpense   = $user->orders()->where('payment_status', 'paid')->whereNot('delivery_status', 'cancelled')->sum('total_amount');

        // total products
        $totalProducts = OrderItem::whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->sum('qty');

        return response()->json([
            'success'       => true,
            'authStatus'    => 'auth_success',
            'access_token'  => $token,
            'token_type'    => 'Bearer',
            'expires_at'    => Carbon::parse(Carbon::now()->addWeeks(100))->toDateTimeString(),
            'user'          => new UserResource($user),
            'carts'         => CartResource::collection($cartItems),
            'dashboardInfo' => [
                'timeline'  => [
                    'count'     => $count < 10 ? '0' . $count : $count,
                    'unit'      => $unit
                ],
                'totalOrders'   => (float) $totalOrders,
                'totalExpense'  => (float) $totalExpense,
                'totalProducts' => (float) $totalProducts
            ],
            'message'       => 'Logged In'
        ]);
    }

    # update info
    public function updateInfo(Request $request)
    {
        $user           = apiUser();
        $user->name     = $request->name;
        $user->phone    = $request->phone;

        if ($request->hasFile('avatar')) {
            $mediaFile = new MediaFile;
            $mediaFile->media_name = null;

            $arr = explode('.', $request->file('avatar')->getClientOriginalName());

            for ($i = 0; $i < count($arr) - 1; $i++) {
                if ($i == 0) {
                    $mediaFile->media_name .= $arr[$i];
                } else {
                    $mediaFile->media_name .= "." . $arr[$i];
                }
            }

            $mediaFile->media_file = $request->file('avatar')->store('uploads/all');
            $mediaFile->user_id = Auth::user()->id;
            $mediaFile->media_extension = $request->file('avatar')->getClientOriginalExtension();
            if (isset($type[$mediaFile->media_extension])) {
                $mediaFile->media_type = $type[$mediaFile->media_extension];
            } else {
                $mediaFile->media_type = "others";
            }
            $mediaFile->media_size = $request->file('avatar')->getSize();
            $mediaFile->save();

            $user->update([
                'avatar' => $mediaFile->id,
            ]);
        }

        $user->save();

        return response()->json([
            'success'       => true,
            'status'        => 200,
            'message'       => translate('Profile updated successfully'),
            'result'        => [
                'user'    => new UserResource($user),
            ]
        ], 200);
    }

    #  update password
    public function updatePassword(Request $request)
    {
        $user = apiUser();
        if ($request->password != $request->passwordConfirmation) {
            return response()->json([
                'success'       => false,
                'status'        => 404,
                'message'       => translate('Password confirmation does not match'),
                'result'        => null
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success'       => true,
            'status'        => 200,
            'message'       => translate('Password updated successfully'),
            'result'        => null
        ], 200);
    }
}
