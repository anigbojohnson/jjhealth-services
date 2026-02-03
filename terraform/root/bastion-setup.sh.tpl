#!/bin/bash
set -e

echo "=== Step 0: Update packages ==="
apt update -y
apt install -y curl unzip tar gzip bash-completion

# -------------------------------
# Step 1: Install AWS CLI v2
# -------------------------------
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip -q awscliv2.zip
./aws/install
rm -rf awscliv2.zip aws
aws --version

# -------------------------------
# Step 2: Set AWS environment variables
# -------------------------------
aws configure set aws_access_key_id "${aws_access_key_id}"
aws configure set aws_secret_access_key "${aws_secret_access_key}"
aws configure set default.region "${aws_region}"
aws configure set default.output "json"

echo "AWS environment variables set."

# -------------------------------
# Step 3: Install eksctl
# -------------------------------
curl --silent --location "https://github.com/weaveworks/eksctl/releases/latest/download/eksctl_$(uname -s)_amd64.tar.gz" -o eksctl.tar.gz
tar -xzf eksctl.tar.gz
mv eksctl /usr/local/bin/
chmod +x /usr/local/bin/eksctl
rm -f eksctl.tar.gz
eksctl version
eksctl completion bash | tee /etc/bash_completion.d/eksctl > /dev/null

# -------------------------------
# Step 4: Install kubectl
# -------------------------------
curl -LO "https://dl.k8s.io/release/$(curl -L -s https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl"
chmod +x kubectl
mv kubectl /usr/local/bin/
rm -f kubectl
kubectl version --client --short
kubectl completion bash | tee /etc/bash_completion.d/kubectl > /dev/null

source /etc/bash_completion
source /etc/profile.d/aws-env.sh


# ==========================
# Install Helm
# ==========================
echo "📦 Installing Helm..."
sudo apt install apt-transport-https curl -y
curl -fsSL https://raw.githubusercontent.com/helm/helm/main/scripts/get-helm-3 | bash


echo "✅ Helm installed successfully."
helm version

echo "=== All tools installed and AWS environment configured successfully! ==="
