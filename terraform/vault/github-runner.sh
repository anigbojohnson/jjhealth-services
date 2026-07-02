#!/bin/bash
set -e

apt-get update

apt-get install -y \
    curl \
    unzip \
    jq \
    git \
    ca-certificates


# Install Terraform
TERRAFORM_VERSION="1.13.2"

curl -LO https://releases.hashicorp.com/terraform/$${TERRAFORM_VERSION}/terraform_$${TERRAFORM_VERSION}_linux_amd64.zip

unzip terraform_$${TERRAFORM_VERSION}_linux_amd64.zip

mv terraform /usr/local/bin/

# Create runner user
useradd -m -s /bin/bash runner || true

mkdir -p /home/runner/actions-runner && cd /home/runner/actions-runner

# Download GitHub Runner
curl -o actions-runner-linux-x64-2.335.1.tar.gz -L https://github.com/actions/runner/releases/download/v2.335.1/actions-runner-linux-x64-2.335.1.tar.gz

echo "4ef2f25285f0ae4477f1fe1e346db76d2f3ebf03824e2ddd1973a2819bf6c8cf  actions-runner-linux-x64-2.335.1.tar.gz" | shasum -a 256 -c


tar xzf actions-runner-linux-x64-2.335.1.tar.gz

chown -R runner:runner /home/runner/actions-runner


# Download the AWS CLI
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"

# Extract it
unzip awscliv2.zip

# Install it
sudo ./aws/install
aws secretsmanager create-secret \
  --name github-runner-pat \
  --secret-string "YOUR_NEW_GITHUB_PAT"
  
GITHUB_PAT=$(aws secretsmanager get-secret-value \
    --secret-id github-runner-pat \
    --query SecretString \
    --output text)

RUNNER_TOKEN=$(curl -s -X POST \
  -H "Accept: application/vnd.github+json" \
  -H "Authorization: Bearer $GITHUB_PAT" \
  https://api.github.com/repos/anigbojohnson/jjhealth-services/actions/runners/registration-token \
  | jq -r '.token')

# Create the runner and start the configuration experience
sudo -u runner ./config.sh \
    --url "${github_repo}" \
    --token "$RUNNER_TOKEN" \
    --name "${runner_name}" \
    --labels self-hosted,linux,x64 \
    --unattended \
    --replace

sudo ./svc.sh install runner
sudo ./svc.sh start

