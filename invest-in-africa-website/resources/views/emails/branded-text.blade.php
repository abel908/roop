{{ $heading }}

{{ $body }}
@if ($next)

{{ $next }}
@endif
@if ($details)

@foreach ($details as $label => $value)
{{ $label }}: {{ $value }}
@endforeach
@endif
@if ($action && $actionUrl)

{{ $action }}: {{ $actionUrl }}
@endif

{{ __('emails.signature') }}
— {{ config('site.name') }}
