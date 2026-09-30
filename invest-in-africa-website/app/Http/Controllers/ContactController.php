<?php

namespace App\Http\Controllers;

use App\Enums\ContactSubject;
use App\Http\Requests\StoreContactRequest;
use App\Models\ContactMessage;
use App\Models\Setting;
use App\Services\Notifier;
use App\Support\Countries;
use App\Support\Locales;
use App\Support\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController
{
    public function __construct(private readonly Seo $seo) {}

    public function create(Request $request): View
    {
        $this->seo->page('contact')->crumb(__('site.nav.contact'));

        $contact = Setting::get('contact', []);

        if (! empty($contact['email']) || ! empty($contact['phone'])) {
            $this->seo->schema(array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => config('site.name'),
                'url' => lroute('home'),
                'contactPoint' => [array_filter([
                    '@type' => 'ContactPoint',
                    'contactType' => 'customer support',
                    'email' => $contact['email'] ?? null,
                    'telephone' => $contact['phone'] ?? null,
                    'availableLanguage' => ['English', 'French', 'Chinese'],
                ])],
            ]));
        }

        $subject = $request->query('subject');

        return view('pages.contact', [
            'contact' => $contact,
            'socials' => array_filter(Setting::get('socials', [])),
            'countries' => Countries::all(),
            'subjects' => ContactSubject::options(),
            'selectedSubject' => ContactSubject::tryFrom((string) $subject)?->value,
        ]);
    }

    public function store(StoreContactRequest $request, Notifier $notifier): RedirectResponse
    {
        $message = ContactMessage::create([
            ...$request->safe()->except('consent'),
            'locale' => Locales::current(),
            'consent_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        $notifier->contactReceived($message);

        return redirect()->to(lroute('confirmation'))->with('confirmation', [
            'type' => 'contact',
            'reference' => $message->reference,
            'email' => $message->email,
        ]);
    }
}
