#!/bin/bash
set -e

PROM_NAMESPACE="monitoring"

echo "📊 Installing Prometheus + Grafana..."

# Check required tools
for cmd in kubectl helm; do
  if ! command -v $cmd >/dev/null 2>&1; then
    echo "❌ $cmd not found. Install it first."
    exit 1
  fi
done

# Create namespace
kubectl create namespace $PROM_NAMESPACE --dry-run=client -o yaml | kubectl apply -f -

# Add Helm repo
helm repo add prometheus-community https://prometheus-community.github.io/helm-charts
helm repo update

# Install kube-prometheus-stack
helm upgrade --install monitoring prometheus-community/kube-prometheus-stack \
  --namespace $PROM_NAMESPACE

# Port-forward Prometheus and Grafana
echo "🔁 Starting port-forwarding for Prometheus and Grafana..."
kubectl port-forward -n $PROM_NAMESPACE svc/monitoring-kube-prometheus-prometheus 9090:9090 >/dev/null 2>&1 &
kubectl port-forward -n $PROM_NAMESPACE svc/monitoring-grafana 3000:80 >/dev/null 2>&1 &

sleep 3

echo ""
echo "✅ Prometheus + Grafana setup complete!"
echo "🔗 Prometheus → http://localhost:9090"
echo "🔗 Grafana    → http://localhost:3000"
