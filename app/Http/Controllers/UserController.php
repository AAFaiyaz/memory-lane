<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function profile(User $user) {
        // $thePosts = $pizza->posts()->get();
        // return $thePosts;
        return view('profile-posts',['username' => $user->username,  'posts' => $user->posts()->latest()->get(), 'postCount' => $user->posts()->count()]);
    }
    public function logout() {
        auth() ->logout();
        return redirect('/')->with('success','You are now logged out');
    }
    public function showCorrectHomepage() {
        if(auth()->check()) {
            return view('homepage-feed');
        } else {
            return view('homepage');
        }
        
    }
    public function login(Request $request) {
        $incomingfields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);

        if (auth()->attempt(['username' => $incomingfields['loginusername'], 'password'=> $incomingfields['loginpassword']])) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'You have successfully logged in');
        } else {
            return redirect('/')->with('failure', 'Invalid login');
        }
    }
    public function register(Request $request) {
        
        $incomingfields = $request->validate([
            'username' => ['required','min:3','max:20',Rule::unique('users','username')],
            'email' => ['required', 'email', Rule::unique('users','email')],
            'password' => ['required','min:3', 'confirmed']
        ]);
        $incomingfields['password'] = bcrypt($incomingfields['password']);
        $user =User::create($incomingfields);
        auth()->login($user);
        return redirect('/')->with('success','Thank you for creating the account');
    }
}
