<?php

namespace App\Services;

use OpenTelemetry\API\Metrics\MeterInterface;

class MetricsService
{
    // Counters (RED model - simplified)
    private $certificates;
    private $validation;
    private $payments;
    private $emails;
    private $forms;
    private $studyMc;
    private $secretKey;
    private $fileUpload;

    // Histograms
    private $requestDuration;
    private $dbQueryDuration;
    private $storeMcDuration;

    public function __construct(MeterInterface $meter)
    {
        // ── Counters (ONE per domain) ─────────────────────────

        $this->certificates = $meter->createCounter('certificates.total');

        $this->validation = $meter->createCounter('validation.total');

        $this->payments = $meter->createCounter('payment.total');

        $this->emails = $meter->createCounter('emails.total');

        $this->forms = $meter->createCounter('form.submissions.total');

        $this->studyMc = $meter->createCounter('studies.store_mc.total');

        $this->secretKey = $meter->createCounter('studies.secret_key.total');

        $this->fileUpload = $meter->createCounter('fileupload.total');

        // ── Histograms ─────────────────────────────────────────

        $this->requestDuration = $meter->createHistogram('http_request_duration_ms');

        $this->dbQueryDuration = $meter->createHistogram('db_query_duration_ms');

        $this->storeMcDuration = $meter->createHistogram('studies_store_mc_duration_ms');
    }

    // ─────────────────────────────────────────────
    // CERTIFICATES
    // ─────────────────────────────────────────────

    public function certificateCreated(string $reason): void
    {
        $this->certificates->add(1, [
            'event' => 'created',
            'reason' => $reason
        ]);
    }

    // ─────────────────────────────────────────────
    // PAYMENTS
    // ─────────────────────────────────────────────

    public function paymentSucceeded(): void
    {
        $this->payments->add(1, [
            'status' => 'success'
        ]);
    }

    public function paymentFailed(string $reason): void
    {
        $this->payments->add(1, [
            'status' => 'failed',
            'reason' => $reason
        ]);
    }

    // ─────────────────────────────────────────────
    // EMAILS
    // ─────────────────────────────────────────────

    public function emailSent(): void
    {
        $this->emails->add(1, [
            'status' => 'sent'
        ]);
    }

    public function emailFailed(string $reason): void
    {
        $this->emails->add(1, [
            'status' => 'failed',
            'reason' => $reason
        ]);
    }

    // ─────────────────────────────────────────────
    // VALIDATION
    // ─────────────────────────────────────────────

    public function validationSucceeded(string $form): void
    {
        $this->validation->add(1, [
            'status' => 'success',
            'form.name' => $form
        ]);
    }

    public function validationFailed(string $form, array $errors): void
    {
        // ONE event per failed submission (not per field)
        $this->validation->add(1, [
            'status' => 'failed',
            'form.name' => $form,
            'error.count' => count($errors)
        ]);
    }

    // ─────────────────────────────────────────────
    // FORMS
    // ─────────────────────────────────────────────

    public function formSubmitted(string $form): void
    {
        $this->forms->add(1, [
            'form.name' => $form,
            'status' => 'submitted'
        ]);
    }

    // ─────────────────────────────────────────────
    // STUDIES / MC
    // ─────────────────────────────────────────────

    public function storeMcSuccess(string $type): void
    {
        $this->studyMc->add(1, [
            'status' => 'success',
            'mc.type' => $type
        ]);
    }

    public function storeMcFailed(string $type): void
    {
        $this->studyMc->add(1, [
            'status' => 'failed',
            'mc.type' => $type
        ]);
    }

    // ─────────────────────────────────────────────
    // SECRET KEY
    // ─────────────────────────────────────────────

    public function secretKeyRequested(): void
    {
        $this->secretKey->add(1, [
            'status' => 'requested'
        ]);
    }

    public function secretKeyFailed(string $reason): void
    {
        $this->secretKey->add(1, [
            'status' => 'failed',
            'reason' => $reason
        ]);
    }

    // ─────────────────────────────────────────────
    // FILE UPLOAD
    // ─────────────────────────────────────────────

    public function fileUploadedSucceeded(): void
    {
        $this->fileUpload->add(1, [
            'status' => 'success'
        ]);
    }

    public function fileUploadFailed(string $reason): void
    {
        $this->fileUpload->add(1, [
            'status' => 'failed',
            'reason' => $reason
        ]);
    }

    // ─────────────────────────────────────────────
    // HISTOGRAMS
    // ─────────────────────────────────────────────

    public function recordRequestDuration(float $ms, string $route): void
    {
        $this->requestDuration->record($ms, [
            'http.route' => $route
        ]);
    }

    public function recordDbQueryDuration(float $ms, string $table): void
    {
        $this->dbQueryDuration->record($ms, [
            'db.table' => $table
        ]);
    }

    public function storeMcDuration(float $ms, array $tags = []): void
    {
        $this->storeMcDuration->record($ms, $tags);
    }
}