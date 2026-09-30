<?php

namespace App\Http\Controllers;

use App\Support\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/** Confirmation page with file number after each form (§3.4, §6.3). */
class ConfirmationController
{
    public function __invoke(Seo $seo): View|RedirectResponse
    {
        $confirmation = session('confirmation');

        if (! $confirmation) {
            return redirect()->to(lroute('home'));
        }

        $seo->title(__('confirmation.title'));
        $seo->noindex = true;

        return view('pages.confirmation', ['confirmation' => $confirmation]);
    }
}
