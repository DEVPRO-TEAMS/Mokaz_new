<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    
    public function indexReservations()
    {
        return view('pages.reservation');
    }
    public function indexApropos()
    {
        return view('pages.apropos');
    }
    public function indexContact()
    {
        return view('pages.contact');
    }
    public function indexFaq()
    {
        return view('pages.faq');
    }
    public function indexPolitiq()
    {
        return view('pages.politiq');
    }
}
