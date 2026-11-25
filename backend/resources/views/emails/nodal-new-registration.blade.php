@extends('emails.layout')

@section('title', 'Opportunity Hub - New Registration')
@section('preheader', 'A new '.$registrationTypeLabel.' registration is awaiting review.')
@section('heading', 'New Registration Awaiting Review')

@section('status')
    @include('emails.partials.status', ['label' => 'Pending Review', 'tone' => 'warning'])
@endsection

@section('content')
    <p style="margin:0 0 12px;">Dear {{ $officerName }},</p>

    <p style="margin:0;">A new <strong>{{ $registrationTypeLabel }}</strong> registration has been submitted on Opportunity Hub and is awaiting review.</p>

    @include('emails.partials.details', ['rows' => [
        'Registration ID' => $registrationId,
        'Name' => $applicantName,
        'Registration Type' => $registrationTypeLabel,
        'Email' => $applicantEmail,
        'Mobile' => $applicantPhone ?: 'Not provided',
        'Submitted on' => $submissionDate,
    ]])

    @include('emails.partials.button', ['url' => $reviewUrl, 'label' => 'Open Dashboard'])
@endsection
