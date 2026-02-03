#!/usr/bin/env bash
set -e

### ===============================
### CONFIGURATION
### ===============================

CLUSTER_NAME="jjhealth-services-cluster"
AWS_REGION="eu-west-2"
AWS_ACCOUNT_ID="869868778582"
ALB_NAMESPACE="kube-system"
ALB_SA_NAME="aws-load-balancer-controller"

### ===============================
### 1. UPDATE KUBECONFIG
### ===============================

echo "🔁 Updating kubeconfig..."
aws eks update-kubeconfig \
  --region ${AWS_REGION} \
  --name ${CLUSTER_NAME}

kubectl get nodes

### ===============================
### 2. CREATE ALB CONTROLLER SERVICE ACCOUNT
### ===============================

echo "🌐 Creating IAM ServiceAccount for ALB Controller..."

eksctl create iamserviceaccount \
  --cluster ${CLUSTER_NAME} \
  --namespace ${ALB_NAMESPACE} \
  --name ${ALB_SA_NAME} \
  --attach-policy-arn arn:aws:iam::${AWS_ACCOUNT_ID}:policy/AWSLoadBalancerControllerIAMPolicy \
  --override-existing-serviceaccounts \
  --approve

### ===============================
### 3. INSTALL / UPDATE AWS LOAD BALANCER CONTROLLER
### ===============================

echo "⚖️ Installing AWS Load Balancer Controller..."

helm repo add eks https://aws.github.io/eks-charts
helm repo update

helm upgrade --install aws-load-balancer-controller eks/aws-load-balancer-controller \
  -n ${ALB_NAMESPACE} \
  --set clusterName=${CLUSTER_NAME} \
  --set serviceAccount.create=false \
  --set serviceAccount.name=${ALB_SA_NAME}

echo "✅ ALB Controller setup completed!"
