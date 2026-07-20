#!/bin/bash
set -e

sudo apt-get update

sudo apt-get install -y \
    curl \
    unzip \
    jq \
    git \
    ca-certificates


CA_SECRET="${ca_secret}"
AWS_REGION="${aws_region}"
TLS_DIR="${tls_dir}"
CA_CERT="$${TLS_DIR}/ca.crt"

# Install Terraform
TERRAFORM_VERSION="1.13.2"

curl -LO https://releases.hashicorp.com/terraform/$${TERRAFORM_VERSION}/terraform_$${TERRAFORM_VERSION}_linux_amd64.zip

sudo unzip terraform_$${TERRAFORM_VERSION}_linux_amd64.zip

sudo mv terraform /usr/local/bin/

# Create runner user
sudo useradd -m -s /bin/bash runner || true

sudo mkdir -p /home/runner/actions-runner && cd /home/runner/actions-runner

# Download GitHub Runner
curl -o actions-runner-linux-x64-2.335.1.tar.gz -L https://github.com/actions/runner/releases/download/v2.335.1/actions-runner-linux-x64-2.335.1.tar.gz


tar xzf actions-runner-linux-x64-2.335.1.tar.gz

sudo chown -R runner:runner /home/runner/actions-runner


# Download the AWS CLI
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"

# Extract it
unzip awscliv2.zip

# Install it
sudo ./aws/install


mkdir -p "$${TLS_DIR}"

aws secretsmanager get-secret-value \
    --secret-id "$${CA_SECRET}" \
    --region "$${AWS_REGION}" \
    --query SecretString \
    --output text \
> "$${CA_CERT}"
cp "$${CA_CERT}" /usr/local/share/ca-certificates/vault-bootstrap-ca.crt
update-ca-certificates


chmod 700 "$${TLS_DIR}"
chmod 644 "$${CA_CERT}"


# ============================================================================
# Create vault user if it doesn't exist
# ============================================================================

if ! id vault >/dev/null 2>&1; then
    log "Creating vault user..."

    sudo useradd \
        --system \
        --home /etc/vault.d \
        --shell /usr/sbin/nologin \
        --comment "HashiCorp Vault" \
        vault
fi

sudo chown -R vault:vault "$${TLS_DIR}"

test -f "$${CA_CERT}"

GITHUB_PAT=$(aws secretsmanager get-secret-value \
    --secret-id github-runner-pat \
    --region "$${AWS_REGION}" \
    --query SecretString \
    --output text)

RUNNER_TOKEN=$(curl -s -X POST \
  -H "Accept: application/vnd.github+json" \
  -H "Authorization: Bearer $GITHUB_PAT" \
  https://api.github.com/repos/anigbojohnson/jjhealth-services/actions/runners/registration-token \
  | jq -r '.token')




grep -q "BEGIN CERTIFICATE" "$${CA_CERT}"

# Create the runner and start the configuration experience
sudo -u runner ./config.sh \
    --url "$${github_repo}" \
    --token "$RUNNER_TOKEN" \
    --name "$${runner_name}" \
    --labels self-hosted,linux,x64 \
    --unattended \
    --replace

sudo ./svc.sh install runner
sudo ./svc.sh start

