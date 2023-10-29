<?php

namespace Modules\Payment\Contracts;


use Illuminate\Http\Request;

interface GatewayContract
{
    public function request($amount, $description);
    public function verify(Request $request);
    public function redirect();
    public function getName();

}
