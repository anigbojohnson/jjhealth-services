#!/usr/bin/env bash
set -e

helm repo add external-secrets https://charts.external-secrets.io
helm repo update

helm upgrade --install external-secrets external-secrets/external-secrets \
  --namespace external-secrets \
  --create-namespace \
  --set serviceAccount.create=true \
  --set "serviceAccount.annotations.eks\.amazonaws\.com/role-arn=arn:aws:iam::869868778582:role/jjhealth-backend-secrets-role"