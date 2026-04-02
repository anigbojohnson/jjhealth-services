# JJ Health Services

A full-stack telehealth platform that enables patients to access a wide range of healthcare services online — including **medical certificates**, **specialist referrals**, **pathology referrals**, **weight loss treatment**, and **telehealth consultations** — through a secure, end-to-end system with user registration, authentication, and integrated payment processing.

> 🚧 **Upcoming Features:** Prescription management and Patient Dashboard are currently under active development.

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
  - [Authentication & Account Management](#authentication--account-management)
  - [Medical Certificates](#medical-certificates)
  - [Specialist Referrals](#specialist-referrals)
  - [Pathology Referrals](#pathology-referrals)
  - [Weight Loss Treatment](#weight-loss-treatment)
  - [Telehealth Consultations](#telehealth-consultations)
  - [Upcoming Features](#upcoming-features)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Repository Structure](#repository-structure)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Local Development](#local-development)
  - [Running with Docker](#running-with-docker)
- [Infrastructure (Terraform)](#infrastructure-terraform)
- [Kubernetes & Kustomize](#kubernetes--kustomize)
- [CI/CD Pipeline](#cicd-pipeline)
- [GitOps with ArgoCD](#gitops-with-argocd)
- [Security](#security)
- [Environment Variables](#environment-variables)
- [Contributing](#contributing)
- [Author](#author)

---

## Overview

JJ Health Services is a comprehensive digital health platform built to remove barriers between patients and the care they need. Patients register once, then access a full suite of healthcare services from any device — without needing to visit a clinic for routine requests.

The platform is deployed on **AWS** using a containerised, cloud-native architecture, provisioned entirely with **Terraform** and managed through **GitOps** workflows using **ArgoCD** and **GitHub Actions**.

---

## Features

### Authentication & Account Management

The platform provides a flexible and secure authentication system, supporting multiple registration and login methods so patients can get started in whichever way suits them.

**Registration & Login methods:**

| Method | Description |
|---|---|
| **Email & Password** | Standard form-based registration and login |
| **Google OAuth** | One-click sign-up and sign-in using a Google account |
| **Microsoft OAuth** | One-click sign-up and sign-in using a Microsoft account |

All OAuth flows use **OAuth 2.0** for secure, token-based authentication. No third-party passwords are stored by the platform.

**Forgotten Password:**
Patients who registered with email and password can reset their password at any time via the forgot password flow — a secure reset link is sent to their registered email address using SMTP.

---

### Medical Certificates

Patients can request medical certificates online for a variety of purposes. The platform supports the following certificate types:

| Certificate Type | Description |
|---|---|
| **Work** | Certificates for employer-required absence documentation |
| **Student** | Certificates for university or school absence |
| **Carer** | Certificates for patients who are caring for an ill dependent |

Each certificate request supports both **single-day** and **multi-day** coverage, with the patient specifying the relevant date or date range at the time of request.

---

### Specialist Referrals

Patients can request a referral to a medical specialist directly through the platform, without needing an in-person GP visit. The process involves:

- Selecting the type of specialist required
- Providing a brief description of symptoms or reason for referral
- Completing payment
- Receiving the referral document digitally

---

### Pathology Referrals

Patients can request pathology referrals (blood tests, urine tests, and other lab investigations) entirely online. The workflow includes:

- Selecting the required pathology tests
- Submitting relevant medical history or symptoms
- Completing payment
- Receiving the pathology request form digitally, ready to take to any pathology lab

---

### Weight Loss Treatment

The platform offers a structured weight loss treatment service, allowing patients to:

- Complete an initial health and lifestyle assessment
- Receive a personalised weight loss plan or medication referral from a registered practitioner
- Access ongoing support and follow-up consultations through the platform

---

### Telehealth Consultations

Patients can book and attend virtual consultations with healthcare practitioners. Key capabilities include:

- Online booking with available time slots
- Secure consultation delivery
- Post-consultation documentation delivered digitally
- Integrated payment at the time of booking

---

### Upcoming Features

The following features are currently under active development:

| Feature | Status |
|---|---|
| **Prescription Management** | 🚧 In Development |
| **Patient Dashboard** | 🚧 In Development |

The **Patient Dashboard** will give patients a centralised view of all their requests, certificates, referrals, consultation history, and upcoming appointments in one place.

The **Prescription Management** module will allow patients to request and manage prescriptions, and track their prescription history end-to-end.

---

## Tech Stack

### Application
| Layer | Technology |
|---|---|
| Backend | PHP (Laravel) |
| Frontend | Blade, JavaScript, CSS, HTML, Bootstrap |
| Database | PostgreSQL (AWS RDS) |
| Authentication | Form-based, Google OAuth 2.0, Microsoft OAuth 2.0 |
| Payments | Stripe |

### DevOps & Infrastructure
| Tool | Purpose |
|---|---|
| Docker | Containerisation |
| Kubernetes (EKS) | Container orchestration |
| Kustomize | Kubernetes manifest management |
| Terraform (HCL) | Infrastructure as Code |
| GitHub Actions | CI/CD pipelines |
| ArgoCD | GitOps continuous delivery |
| AWS | Cloud platform (VPC, EC2, EKS, RDS, Route 53, S3, etc.) |
| Gitleaks | Secret scanning (pre-commit) |
| HashiCorp Vault | Secrets management (production) |

---

## Architecture

```
┌─────────────────────────────────────────────────────────┐
│                        AWS Cloud                        │
│                                                         │
│   ┌──────────┐    ┌──────────────────────────────────┐  │
│   │ Route 53 │───▶│            VPC                   │  │
│   └──────────┘    │  ┌──────────────────────────────┐│  │
│                   │  │        EKS Cluster            ││  │
│                   │  │  ┌──────────┐  ┌──────────┐  ││  │
│                   │  │  │ App Pod  │  │ App Pod  │  ││  │
│                   │  │  └──────────┘  └──────────┘  ││  │
│                   │  └──────────────────────────────┘│  │
│                   │  ┌──────────┐   ┌─────────────┐  │  │
│                   │  │   RDS    │   │     S3      │  │  │
│                   │  │(Postgres)│   │  (Storage)  │  │  │
│                   │  └──────────┘   └─────────────┘  │  │
│                   └──────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘

CI/CD & GitOps Flow:
Developer → Push to GitHub
                │
                ▼
        GitHub Actions
        ┌───────────────────────────────────┐
        │ 1. Gitleaks secret scan           │
        │ 2. PHP lint & tests               │
        │ 3. Docker build & push to ECR     │
        │ 4. Update Kubernetes manifests    │
        └───────────────────────────────────┘
                │
                ▼
        ArgoCD detects manifest change
                │
                ▼
        Deploys to EKS (via Kustomize)
```

---

## Repository Structure

```
jjhealth-services/
├── .devcontainer/              # Dev container configuration
├── .github/
│   └── workflows/              # GitHub Actions CI/CD pipelines
├── application/                # Laravel PHP application source code
│   ├── app/
│   ├── resources/views/        # Blade templates
│   ├── routes/                 # Application routes
│   └── ...
├── argocd/
│   └── applications/           # ArgoCD Application manifests
├── config/                     # Application configuration files
├── docker/                     # Dockerfiles and Docker Compose
├── kubernetes/
│   └── kustomize/              # Kubernetes manifests (base + overlays)
├── terraform/                  # AWS infrastructure as Code (HCL)
├── .gitignore
└── .pre-commit-config.yaml     # Pre-commit hooks (Gitleaks v8.30.0)
```

---

## Getting Started

### Prerequisites

- [PHP 8.x](https://www.php.net/) and [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) and npm
- [Docker](https://www.docker.com/) and Docker Compose
- [PostgreSQL](https://www.postgresql.org/) (or use the Docker Compose setup)

### Local Development

1. **Clone the repository**
   ```bash
   git clone https://github.com/anigbojohnson/jjhealth-services.git
   cd jjhealth-services
   ```

2. **Install PHP dependencies**
   ```bash
   cd application
   composer install
   ```

3. **Install frontend dependencies**
   ```bash
   npm install && npm run dev
   ```

4. **Set up environment variables**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Update `.env` with your database credentials, Stripe keys, and mail settings (see [Environment Variables](#environment-variables)).

5. **Run database migrations**
   ```bash
   php artisan migrate --seed
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```
   The app will be available at `http://localhost:8000`.

### Running with Docker

```bash
cd docker
docker compose up --build
```

The app will be available at `http://localhost:8080`.

---

## Infrastructure (Terraform)

All AWS infrastructure is defined as code inside the `terraform/` directory.

```bash
cd terraform

# Initialise Terraform
terraform init

# Preview changes
terraform plan

# Apply infrastructure
terraform apply
```

Resources provisioned include: VPC, subnets, security groups, EC2, EKS cluster, RDS (PostgreSQL), S3, Route 53, IAM roles, and more.

> **Note:** Ensure your AWS credentials are configured via `aws configure` or environment variables before running Terraform.

---

## Kubernetes & Kustomize

Kubernetes manifests are managed with **Kustomize** for environment-specific configuration (dev, staging, production).

```bash
# Preview rendered manifests
kubectl kustomize kubernetes/kustomize/overlays/production

# Apply directly
kubectl apply -k kubernetes/kustomize/overlays/production
```

---

## CI/CD Pipeline

The CI/CD pipeline is defined in `.github/workflows/` and triggers automatically on push and pull request events.

**Pipeline stages:**
1. **Secret Scanning** — Gitleaks scans the full repo for exposed credentials
2. **Code Lint & Test** — PHP unit tests and code style checks
3. **Docker Build** — Builds and tags the application Docker image
4. **Push to Registry** — Pushes the image to AWS ECR
5. **Update Manifests** — Bumps the Kubernetes image tag in the repo
6. **ArgoCD Sync** — ArgoCD detects the change and deploys to EKS automatically

---

## GitOps with ArgoCD

This project follows the **GitOps** pattern — the Git repository is the single source of truth for both application and infrastructure state. ArgoCD continuously monitors the `argocd/applications/` directory and syncs any changes to the EKS cluster automatically.

```bash
# Check application status
argocd app list

# Manually trigger a sync
argocd app sync jjhealth-services
```

---

## Security

- **Gitleaks v8.30.0** runs as a pre-commit hook, scanning the full repository for secrets before every commit
- **HashiCorp Vault** manages secrets in production — no credentials are hardcoded
- All traffic is encrypted via HTTPS (TLS via Route 53 + ACM)
- **Stripe** handles all payment data — no card details are stored in the application database
- AWS IAM roles follow the principle of least privilege

Set up the pre-commit hook locally:

```bash
pip install pre-commit
pre-commit install
```

---

## Environment Variables

Create a `.env` file in the `application/` directory based on `.env.example`. Key variables:

| Variable | Description |
|---|---|
| `APP_KEY` | Laravel application key |
| `DB_CONNECTION` | Set to `pgsql` for PostgreSQL |
| `DB_HOST` | Database host |
| `DB_PORT` | Database port (default: `5432`) |
| `DB_DATABASE` | Database name |
| `DB_USERNAME` | Database username |
| `DB_PASSWORD` | Database password |
| `STRIPE_KEY` | Stripe publishable key |
| `STRIPE_SECRET` | Stripe secret key |
| `MAIL_HOST` | SMTP mail host |
| `MAIL_USERNAME` | SMTP username |
| `MAIL_PASSWORD` | SMTP password |
| `GOOGLE_CLIENT_ID` | Google OAuth client ID |
| `GOOGLE_CLIENT_SECRET` | Google OAuth client secret |
| `GOOGLE_REDIRECT_URI` | Google OAuth redirect URI |
| `MICROSOFT_CLIENT_ID` | Microsoft OAuth client ID |
| `MICROSOFT_CLIENT_SECRET` | Microsoft OAuth client secret |
| `MICROSOFT_REDIRECT_URI` | Microsoft OAuth redirect URI |
| `AWS_ACCESS_KEY_ID` | AWS access key |
| `AWS_SECRET_ACCESS_KEY` | AWS secret key |
| `AWS_DEFAULT_REGION` | AWS region |

> **Never commit your `.env` file.** It is listed in `.gitignore` and Gitleaks will flag any accidental secret exposure.

---

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature-name`
3. Commit your changes: `git commit -m 'Add some feature'`
4. Push to the branch: `git push origin feature/your-feature-name`
5. Open a Pull Request

Please ensure all pre-commit hooks pass before submitting a PR.

---

## Author

**Johnson Azubuike Anigbo**
- GitHub: [@anigbojohnson](https://github.com/anigbojohnson)
- Email: anigbojohnsona@gmail.com
