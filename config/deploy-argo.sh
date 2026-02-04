#!/bin/bash
set -e

ARGO_NAMESPACE="argocd"
ARGO_APP_PATH="..\argocd\applications\argocd-dev.yaml"
CLUSTER_NAME="jjhealth-services-cluster"
AWS_REGION="eu-west-2"
AWS_ACCOUNT_ID="869868778582"


echo "🔁 Updating kubeconfig..."
aws eks update-kubeconfig \
  --region ${AWS_REGION} \
  --name ${CLUSTER_NAME}

kubectl get nodes

echo "📦 Installing Argo CD..."

# Check required tools
for cmd in kubectl; do
  if ! command -v $cmd >/dev/null 2>&1; then
    echo "❌ $cmd not found. Install it first."
    exit 1
  fi
done

# Create namespace
kubectl create namespace $ARGO_NAMESPACE --dry-run=client -o yaml | kubectl apply -f -

# Install Argo CD manifests
kubectl create -n $ARGO_NAMESPACE -f https://raw.githubusercontent.com/argoproj/argo-cd/stable/manifests/install.yaml


kubectl create -n $ARGO_NAMESPACE -f "$ARGO_APP_PATH"

# Port-forward Argo CD
echo "🔁 Starting port-forwarding for Argo CD..."
kubectl port-forward -n $ARGO_NAMESPACE svc/argocd-server 8080:443 >/dev/null 2>&1 &

sleep 3

# Output login info
echo ""
echo "✅ Argo CD setup complete!"
echo "🔗 Argo CD → https://localhost:8080"
echo ""
echo "🔐 Argo CD Login:"
echo "Username: admin"
echo -n "Password: "
kubectl get secret argocd-initial-admin-secret \
  -n $ARGO_NAMESPACE -o jsonpath="{.data.password}" | base64 --decode
echo ""
