@extends('emails.layout')

@section('title', 'Opportunity Hub - Registration Received')
@section('preheader', 'Your registration has been received and is pending review.')
@section('heading', 'Registration Received')

@section('status')
    @include('emails.partials.status', ['label' => $statusLabel, 'tone' => 'warning'])
@endsection

@section('content')
    <p style="margin:0 0 12px;">Dear {{ $applicantName }},</p>

    <p style="margin:0;">Thank you for registering on Opportunity Hub. Your application has been received and is now pending review by the Zonal Cultural Centre (ZCC).</p>

    @include('emails.partials.details', ['rows' => [
        'Registration ID' => $registrationId,
        'Name' => $applicantName,
        'Registration Type' => $registrationTypeLabel,
        'Submitted on' => $submissionDate,
    ]])

    <p style="margin:0;">Submitting an application does not by itself mean acceptance or listing on the portal. You will be informed of the outcome by email.</p>
@endsection
