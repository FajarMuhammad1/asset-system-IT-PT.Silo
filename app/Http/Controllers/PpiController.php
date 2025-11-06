<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PpiController extends Controller
{
    public function index()
    {
        $data = array(
            'title' => 'PPI Request',
            'menuPpiAdmin' => 'Active',
            
        );
        return view('admin.ppi.index', $data);
    }
}
