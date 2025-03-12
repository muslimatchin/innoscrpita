<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponse;
use App\Jobs\SendPasswordResetEmail;
use App\Jobs\SendVerificationEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,NULL,id,email_verified_at,NULL', //added unique validation here excluding unverified email eventhough we are manually checking it again, to prevent race condition
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Check if an unverified email exists
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if ($user->email_verified_at) {
                return response()->json(["email" => ["email is already taken"]], 422);
            }
            // Update name and re-send verification email
            $user->name = $request->name;
            $user->save();
            dispatch(new SendVerificationEmail($user));
            return new ApiResponse([
                "message" => "user registered. Please check email to verify your email",
                "data" => null
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return new ApiResponse([
            "message" => "user registered. Please check email to verify your email",
            "data" => null
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid login details'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        if (!$user->email_verified_at) {
            return response()->json(['message' => 'Please verify your email'], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return new ApiResponse([
            "message" => "Logged in succesfully",
            "data" => [
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    public function usereDetails(Request $request)
    {
        return new ApiResponse([
            "message" => "User data retrieved",
            "data" => $request->user()
        ]);
    }

    public function verifyEmail($id, Request $request)
    {
        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        $user->email_verified_at = now();
        $user->save();

        return response()->json(['message' => 'Email verified successfully'], 200);
    }

    public function logout(Request $request)
    {
        // Revoke the current access token
        $request->user()->currentAccessToken()->delete();
        return new ApiResponse([
            "message"=>"Logged out successfully",
            "data" => null
        ]);
    }

    public function sendPasswordResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Find the user
        $user = User::where('email', $request->email)->first();

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Please verify your email before resetting your password.'], 403);
        }

        // Generate a unique reset token
        $token = Str::random(60);

        // Store token in password_resets table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['email' => $user->email, 'token' => Hash::make($token), 'created_at' => now()]
        );

        // Dispatch job to send email
        dispatch(new SendPasswordResetEmail($user, $token));

        return new ApiResponse([
            "message"=>"Password reset email sent. Check your email.",
            "data" => null
        ]);

    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return response()->json(['message' => 'Invalid or expired token.'], 400);
        }

        // Reset the password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return new ApiResponse([
            "message"=>"Password reset successfully. You can now log in.",
            "data" => null
        ]);
    }
}
