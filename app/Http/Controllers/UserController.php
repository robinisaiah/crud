<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserDetails;
use App\UserImages;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function createUser(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:user_details,email,'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'result' => 'Validation failed',
                'errors' => $validator->errors(),
            ]);
        }
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        UserDetails::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
            'city' => $request->city,
            'state' => $request->state,
        ]);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('images', 'public');

            UserImages::create([
                'user_id' => $user->id,
                'imagepath' => $imagePath,
            ]);
        }
        return response()->json(['success' => true, 'result' => 'User created successfully',  'status' => 200,]);
    }
    public function getUser(){
        $users = User::with('user_details', 'user_images')->get();
        return response()->json([
            'success' => true,
            'result' => $users,
        ]);
    }

    public function updateUser(Request $request){
        $user = User::findOrFail($request->id);
        if($user){
            $user->update([
                'username' => $request->username ?? $user->username,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
            ]);
        }
        $userDetails = $user->user_details;
        if ($userDetails) {
            $userDetails->update([
                'name' => $request->name ?? $userDetails->name,
                'email' => $request->email ?? $userDetails->email,
                'phone' => $request->phone ?? $userDetails->phone,
                'country' => $request->country ?? $userDetails->country,
                'city' => $request->city ?? $userDetails->city,
                'state' => $request->state ?? $userDetails->state,
            ]);
        } else {
            UserDetails::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country' => $request->country,
                'city' => $request->city,
                'state' => $request->state,
            ]);
        }

        if ($request->hasFile('image')) {
            $user->images()->delete();
            $image = $request->file('image');
            $imagePath = $image->store('images', 'public');

            UserImages::create([
                'user_id' => $user->id,
                'imagepath' => $imagePath,
            ]);
        }
        return response()->json([
            'success' => true,
            'result' => $user,
        ]);
    }

    public function deleteUser($id){
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'result' => 'User not found',
            ]);
        }
        $user->delete();

        if ($user->user_details) {
            $user->user_details->delete();
        }

        $user->user_images()->delete();

        return response()->json([
            'success' => true,
            'result' => 'User deleted successfully',
        ]);
    }
}
