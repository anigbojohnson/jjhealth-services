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
BACKEND_ROLE_NAME="jjhealth-backend-secrets-role-1"
BACKEND_POLICY_NAME="jjhealth-backend-secrets-policy"

### ===============================
### 1. UPDATE KUBECONFIG
### ===============================

echo "🔁 Updating kubeconfig..."
aws eks update-kubeconfig \
  --region ${AWS_REGION} \
  --name ${CLUSTER_NAME}

kubectl get nodes

### ===============================
### 2. CREATE IAM ROLE + SERVICE ACCOUNT (IRSA)
### ===============================

eksctl utils associate-iam-oidc-provider \
  --cluster "${CLUSTER_NAME}" \
  --region "${AWS_REGION}" \
  --approve

echo "🔐 Creating IAM ServiceAccount: ${BACKEND_SA_NAME} in namespace ${NAMESPACE}"



eksctl create iamserviceaccount \
  --cluster "${CLUSTER_NAME}" \
  --namespace "${NAMESPACE}" \
  --name "${BACKEND_SA_NAME}" \
  --role-name "${BACKEND_ROLE_NAME}" \
  --attach-policy-arn "arn:aws:iam::${AWS_ACCOUNT_ID}:policy/${BACKEND_POLICY_NAME}" \
  --override-existing-serviceaccounts \
  --approve



### ===============================
### 3. INSTALL / UPDATE CSI DRIVER
### ===============================

echo "📦 Installing Secrets Store CSI Driver..."

helm repo add secrets-store-csi-driver https://kubernetes-sigs.github.io/secrets-store-csi-driver/charts
helm repo update

helm upgrade --install csi-secrets-store \
  secrets-store-csi-driver/secrets-store-csi-driver \
  --namespace kube-system

### ===============================
### 4. INSTALL AWS CSI PROVIDER
### ===============================

kubectl apply -f https://raw.githubusercontent.com/aws/secrets-store-csi-driver-provider-aws/main/deployment/aws-provider-installer.yaml

echo "✅ CSI Driver setup completed!"
