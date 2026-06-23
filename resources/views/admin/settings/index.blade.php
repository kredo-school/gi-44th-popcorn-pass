@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card card-dark p-3">
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-general" type="button">General</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-email" type="button">Email</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-payment" type="button">Payment</button>
            </li>
        </ul>

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-general" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Site Name</label>
                            <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $settings->site_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Timezone</label>
                            <input type="text" name="timezone" class="form-control" value="{{ old('timezone', $settings->timezone) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Support Email</label>
                            <input type="email" name="support_email" class="form-control" value="{{ old('support_email', $settings->support_email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Contact Phone</label>
                            <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $settings->contact_phone) }}">
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-email" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">SMTP Host</label>
                            <input type="text" name="smtp_host" class="form-control" value="{{ old('smtp_host', $settings->smtp_host) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">SMTP Port</label>
                            <input type="number" name="smtp_port" class="form-control" value="{{ old('smtp_port', $settings->smtp_port) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">SMTP Username</label>
                            <input type="text" name="smtp_username" class="form-control" value="{{ old('smtp_username', $settings->smtp_username) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">SMTP Encryption</label>
                            <select name="smtp_encryption" class="form-select">
                                <option value="">None</option>
                                <option value="tls" {{ old('smtp_encryption', $settings->smtp_encryption) == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ old('smtp_encryption', $settings->smtp_encryption) == 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Notification Email</label>
                            <input type="email" name="notification_email" class="form-control" value="{{ old('notification_email', $settings->notification_email) }}">
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-payment" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Payment Gateway</label>
                            <select name="payment_gateway" class="form-select" required>
                                <option value="stripe" {{ old('payment_gateway', $settings->payment_gateway) == 'stripe' ? 'selected' : '' }}>Stripe</option>
                                <option value="paypal" {{ old('payment_gateway', $settings->payment_gateway) == 'paypal' ? 'selected' : '' }}>PayPal</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Currency</label>
                            <input type="text" name="currency" class="form-control" value="{{ old('currency', $settings->currency) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Tax Rate (%)</label>
                            <input type="number" step="0.01" name="tax_rate" class="form-control" value="{{ old('tax_rate', $settings->tax_rate) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Stripe Publishable Key</label>
                            <input type="text" name="stripe_publishable_key" class="form-control" value="{{ old('stripe_publishable_key', $settings->stripe_publishable_key) }}">
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-secondary mt-4">

            <button type="submit" class="btn btn-warning">Save Settings</button>
        </form>
    </div>

@endsection