<?php

namespace App\Services;

use OpenTelemetry\API\Metrics\MeterInterface;

class MetricsService
{
    // counters — things you count up
    private $certificatesCreated;
    private $validationErrors;
    private $paymentSuccess;
    private $paymentFailed;
    private $emailsSent;
    private $validationSucceded;
    private $formSubmissions;
    private $storeMcSuccess;
    private $emailsFailed;
    private $secretKeyRequests;
    private $secretKeyErrors;
    private $validationSucceeded;
    private $fileUploadedSucceded;
    private $fileUploadFailed;
    // histograms — things you measure duration of
    private $requestDuration;
    private $dbQueryDuration;
    private $storeMcDuration;

    public function __construct(MeterInterface $meter)
    {
        // ── counters ───────────────────────────────────────────
        $this->certificatesCreated = $meter->createCounter(
            'certificates.created',        // metric name
            'certificates',                // unit
            'Total medical certificates created'  // description
        );

        $this->validationErrors = $meter->createCounter(
            'validation.errors',
            'errors',
            'Total validation errors across all forms'
        );

        $this->paymentSuccess = $meter->createCounter(
            'payment.success',
            'payments',
            'Total successful payments'
        );

        $this->paymentFailed = $meter->createCounter(
            'payment.failed',
            'payments',
            'Total failed payments'
        );

        $this->emailsSent = $meter->createCounter(
            'emails.sent',
            'emails',
            'Total confirmation emails sent'
        );

        $this->validationSucceded = $meter->createCounter(
            'validation.success',
            'success',
            'Total validation success across all forms'
        );

        $this->formSubmissions = $meter->createCounter(
            'form.submissions',
            'requests',
            'Total form submissions'
        );

        $this->storeMcSuccess = $meter->createCounter(
            'studies.store_mc.success',
            'requests',
            'Successful MC store operations' 
        );

        $this->emailsFailed = $meter->createCounter(
            'emails.failed',
            'emails',
            'Total failed emails'
        );

        $this->secretKeyRequests = $meter->createCounter(
            'studies.payment.secret_key_requests',
            'requests',
            'Total payment secret key requests'
        );

        $this->secretKeyErrors = $meter->createCounter(
            'studies.payment.secret_key_errors',
            'errors',
            'Total payment secret key generation failures'
        );

        $this->validationSucceeded = $meter->createCounter(
            'validation.success',
            'success',
            'Total validation success across all forms'
        );
       $this->fileUploadedSucceded = $meter->createCounter(
            'fileupload.success',
            'fileupload',
            'Total file upload success'
        );
        $this->fileUploadFailed=$meter->createCounter(
                'fileupload.failed',
                'fileupload',
                'Total file upload failed'
            );
        // ── histograms ─────────────────────────────────────────
        $this->requestDuration = $meter->createHistogram(
            'http.request.duration',
            'ms',
            'Duration of HTTP requests'
        );

        $this->dbQueryDuration = $meter->createHistogram(
            'db.query.duration',
            'ms',
            'Duration of database queries'
        );

        $this->storeMcDuration = $meter->createHistogram(
            'studies.store_mc.duration',
            'ms',
            'Duration of store MC operation'
        );
    }


    // ── counter methods ────────────────────────────────────────

    public function certificateCreated(string $reason): void
    {
        $this->certificatesCreated->add(1, [
            'medical.reason' => $reason   // tag by reason
        ]);
    }

    public function paymentSucceeded(): void
    {
        $this->paymentSuccess->add(1);
    }

    public function paymentFailed(string $reason): void
    {
        $this->paymentFailed->add(1, [
            'failure.reason' => $reason
        ]);
    }

    public function emailSent(): void
    {
        $this->emailsSent->add(1);
    }



    public function validationSucceeded(string $form): void
    {
        $this->validationSucceeded->add(1, [
            'form.name' => $form
        ]);
    }

    public function validationFailed(string $form, array $errors): void
    {
        foreach (array_keys($errors) as $field) {
            $this->validationErrors->add(1, [
                'validation.errors' => $form,
                'field'     => $field
            ]);
        }
    }

    public function storeMcSuccess(string $type): void
    {
        $this->storeMcSuccess->add(1, [
            'mc.type' => $type
        ]);
    }

        public function emailFailed(string $reason): void
    {
        $this->emailsFailed->add(1, [
            'failure.reason' => $reason
        ]);
    }

    public function secretKeyRequested(): void
    {
        $this->secretKeyRequests->add(1);
    }

    public function secretKeyFailed(string $reason): void
    {
        $this->secretKeyErrors->add(1, [
            'error.reason' => $reason
        ]);
    }

    public function fileUploadedSucceded(): void
    {
        $this->fileUploadedSucceded->add(1);
    }

    
        public function fileUploadFailed(string $reason): void
    {
        $this->fileUploadFailed->add(1, [
            'failure.reason' => $reason
        ]);
    }

      // ── histograms ─────────────────────────────────────────


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