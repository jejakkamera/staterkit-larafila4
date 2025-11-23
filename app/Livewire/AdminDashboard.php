<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class AdminDashboard extends Component
{
    public $users;
    public $totalUsers;
    public $adminCount;
    public $userCount;

    public function mount()
    {
        $this->users = User::all();
        $this->totalUsers = User::count();
        $this->adminCount = User::where('role', 'admin')->count();
        $this->userCount = User::where('role', 'user')->count();
    }

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
