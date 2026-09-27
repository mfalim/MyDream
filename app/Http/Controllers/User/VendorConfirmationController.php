<?php

namespace App\Http\Controllers\User;

use App\Data\User\VendorConfirmationData;
use App\Http\Controllers\Controller;

class VendorConfirmationController extends Controller
{
    public function index()
    {
        return view('user.vendor-confirmation.index', [
            'eventDateLabel' => '25 Oktober 2025',
            'venue' => 'Grand Ballroom Hotel Mulia Senayan',
            'stats' => VendorConfirmationData::stats(),
            'vendors' => VendorConfirmationData::all(),
        ]);
    }
}
