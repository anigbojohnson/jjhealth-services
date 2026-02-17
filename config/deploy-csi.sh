#!/usr/bin/env bash
set -e

### ===============================
### CONFIGURATION
### ===============================

CLUSTER_NAME="jjhealth-services-cluster"
AWS_REGION="eu-west-2"
AWS_ACCOUNT_ID="869868778582"
NAMESPACE="development"
BACKEND_SA_NAME="laravel-sa"
BACKEND_ROLE_NAME="jjhealth-backend-secrets-role-2"
BACKEND_POLICY_NAME="jjhealth-backend-secrets-policy"




helm repo add external-secrets https://charts.external-secrets.io
helm repo update

helm upgrade --install external-secrets external-secrets/external-secrets \
  --namespace external-secrets \
  --create-namespace
