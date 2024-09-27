<?php

namespace Swapnil\StarterKit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class HomeController extends Controller
{

    /**
     * Displays the home page with user information.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Return the home page view with the user information
        return view('swapnil-starterkit::index', compact('user'));
    }

    /**
     * Displays a paginated list of active users.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator The paginated list of active users.
     */
    public function users()
    {
        // Fetch active users from the database and paginate them
        $users = User::where(['deleted' => '0'])->paginate(5);

        // Return the view for displaying the list of users with the paginated data
        return view('swapnil-starterkit::user.index', compact('users'));
    }


    /**
     * Displays the user's profile page.
     *
     * This function retrieves the authenticated user's information and displays it on the profile page.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     * @return \Illuminate\View\View The view for displaying the user's profile with the user information.
     */
    public function profile($id = null)
    {
        // Get the authenticated user
        if(!empty($id)) {
            $user = User::findOrFail($id);
            if(!$user) {
                return redirect()->back()->with(['error' => "User not found.!"]);
            }
        } else {
            $user = Auth::user();
        }

        // Return the view for displaying the user's profile with the user information
        return view('swapnil-starterkit::user.profile', compact('user'));
    }

    /**
     * Updates user profile with name, email, and password.
     *
     * @param \Illuminate\Http\Request $request The incoming request.
     * @return \Illuminate\Http\RedirectResponse Redirects back with success or error message.
     */
    public function updateProfile(Request $request,$id = null)
    {
        // Define validation messages
        $messages = [
            'name.regex' => 'Name should only contain alphabets.',
            'email.email' => 'Email is not in a valid format.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Confirm Password did not match with new password.'
        ];

        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'bail|sometimes|nullable|regex:/^[a-z A-Z]+$/',
            'email' => 'bail|sometimes|nullable|email:dns,rfc',
            'password' => ['bail', 'sometimes','nullable','confirmed:password_confirmation',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(100)
            ],
        ], $messages);

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        // Get the authenticated user
        if(!empty($id)) {
            $user = User::findOrFail($id);
            $route = redirect()->route('profile',['id' => $user->id]);
            if(!$user) {
                return redirect()->back()->with(['error' => "User not found.!"]);
            }
        } else {
            $user = Auth::user();
            $route = redirect()->route('profile');
        }

        // Update user name and email if provided
        if ($request->filled('name')) {
            $user->name = $request->input('name');
        }

        if ($request->filled('email')) {
            $user->email = $request->input('email');
        }

        // If password and confirm password are provided, update the password
        if ($request->filled(['password', 'password_confirmation'])) {
            if(Hash::check($request->input('password'),$user->password)) {
                return redirect()->back()->with(['error' => 'You cannot update the same password.']);
            } else {
                $user->password = Hash::make($request->input('password'));
                if($user->saveOrFail()) {
                    if(!$id) {
                        Auth::logout();
                        return redirect()->route('login')->with(['success' => 'Password updated successfully. Please login again.']);
                    } else {
                        return $route->with(['success' => 'Password updated successfully for user '.$user->name]);
                    }
                }
            }
        }

        // Save the user and redirect back with success or error message
        if ($user->saveOrFail()) {
            return $route->with(['success' => 'Profile updated successfully.']);
        }
        return redirect()->back()->with(['error' => 'Failed to update profile.']);
    }

    /**
     * Deletes a user from the system.
     *
     * This function takes a user ID as a parameter, finds the user in the database,
     * and marks them as deleted. It then redirects the user back to the list of users
     * with a success or error message.
     *
     * @param int $id The ID of the user to be deleted.
     * @return \Illuminate\Http\RedirectResponse Redirects back with success or error message.
     */
    public function delete_user($id)
    {
        // Find the user by ID
        $checkUser = User::findOrFail($id);

        // Mark the user as deleted
        if ($checkUser->update(['deleted' => '1'])) {
            // Redirect back with success message
            return redirect()->route('users')->with(['success' => 'User deleted successfully.']);
        } else {
            // Redirect back with error message
            return redirect()->route('users')->with(['error' => 'Failed to delete user.']);
        }
    }
}
