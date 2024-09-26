<?php
namespace Swapnil\StarterKit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function index() {
        $user = Auth::user();
        return view('swapnil-starterkit::index',compact('user'));
    }

    public function users() {
        $users = User::where(['deleted' => '0'])->paginate(5);
        return view('swapnil-starterkit::user.index',compact('users'));
    }

    public function delete_user($id) {
        $checkUser = User::findOrFail($id);
        if($checkUser->update(['deleted' => '1'])) {
            return redirect()->route('users')->with(['success' => 'User deleted successfully.']);
        } else {
            return redirect()->route('users')->with(['error' => 'Failed to delete user.']);
        }
    }
}
