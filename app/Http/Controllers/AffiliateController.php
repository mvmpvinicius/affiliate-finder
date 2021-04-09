<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    private $affiliate;
    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        Affiliate $affiliate
    ) {
        $this->affiliate = $affiliate;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $affiliates = $this->affiliate->getAffiliates();
        if ($affiliates['status'] === 'failed') {
            return response()->json(['status' => 'failed', 'data' => $affiliates['data']], 400);
        }
        return response()->json(['status' => 'success', 'data' => $affiliates], 200);
    }
}
