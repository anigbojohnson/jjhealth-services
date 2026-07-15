data "aws_ami" "ubuntu" {
  most_recent = true
  owners      = ["099720109477"] # Canonical

  filter {
    name   = "name"
    values = ["ubuntu/images/hvm-ssd-gp3/ubuntu-resolute-26.04-amd64-server-*"]
  }

  filter {
    name   = "virtualization-type"
    values = ["hvm"]
  }


  filter {
    name   = "root-device-type"
    values = ["ebs"]
  }
}

locals {
  bootstrap_tls_script = templatefile(
    "${path.module}/scripts/bootstrap-vault-tls.sh.tftpl",
    {
      tls_secret = var.vault_key_certificate_secret
      ca_secret  = var.ca_certificate_secret
      aws_region = var.region
    }
  )
}

# Bastion host SG — accepts SSH from the internet (or restrict to your IP)
resource "aws_security_group" "public" {
  name        = "bastion-sg"
  description = "Security group for bastion host"
  vpc_id      = aws_vpc.main.id

  ingress {
    description = "SSH from allowed IPs"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]   # tighten to your IP: ["x.x.x.x/32"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = { Name = "bastion-sg" }
}

# Private EC2 SG — accepts SSH only from the bastion SG (not a CIDR)
resource "aws_security_group" "private" {
  name        = "ec2-private-sg"
  description = "Security group for private EC2 instance"
  vpc_id      = aws_vpc.main.id

  ingress {
    description     = "SSH from bastion only"
    from_port       = 22
    to_port         = 22
    protocol        = "tcp"
    security_groups = [aws_security_group.public.id]  # SG reference, not CIDR
  }

    ingress {
    description     = "SSH from bastion only"
    from_port       = 8200
    to_port         = 8200
    protocol        = "tcp"
    security_groups = [aws_security_group.public.id]  # SG reference, not CIDR
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = { Name = "ec2-private-sg" }
}

resource "aws_iam_role" "private" {
  name = "backend-instance-role"
  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [{
      Effect    = "Allow"
      Principal = { Service = "ec2.amazonaws.com" }
      Action    = "sts:AssumeRole"
    }]
  })
}


resource "aws_iam_role" "public" {
  name = "public-ec2-kms-role"
  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [{
      Effect    = "Allow"
      Principal = { Service = "ec2.amazonaws.com" }
      Action    = "sts:AssumeRole"
    }]
  })
}

resource "aws_iam_role_policy" "ec2_kms" {
  name = "ec2-kms-policy"
  role = aws_iam_role.private.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [{
      Effect = "Allow"
      Action = [
        "kms:Encrypt",
        "kms:Decrypt",
        "kms:DescribeKey"
      ]
      Resource = aws_kms_key.vault_unseal.arn
    }]
  })
}

resource "aws_iam_instance_profile" "private" {
  name = "backend-instance-profile"
  role = aws_iam_role.private.name
}

resource "aws_iam_instance_profile" "public" {
  name = "public-ec2-kms-instance-profile"
  role = aws_iam_role.public.name
}


resource "aws_instance" "private" {
  ami                    = data.aws_ami.ubuntu.id
  instance_type          = var.instance_type
  subnet_id              = aws_subnet.private.id
  vpc_security_group_ids = [aws_security_group.private.id]
  iam_instance_profile   = aws_iam_instance_profile.private.name
  key_name = var.key_name

  user_data = <<-EOF
    #!/bin/bash
    set -e

    cat >/usr/local/bin/bootstrap-vault-tls.sh <<'SCRIPT'
    ${local.bootstrap_tls_script}
    SCRIPT

    chmod +x /usr/local/bin/bootstrap-vault-tls.sh

    /usr/local/bin/bootstrap-vault-tls.sh
    EOF

  tags = { Name = "private-ec2" }
}

resource "aws_instance" "public" {
  ami                    = data.aws_ami.ubuntu.id
  instance_type          = var.instance_type
  subnet_id              = aws_subnet.public.id
  associate_public_ip_address = true
  vpc_security_group_ids = [aws_security_group.public.id]
  iam_instance_profile   = aws_iam_instance_profile.public.name
  user_data_replace_on_change = true

  key_name = var.key_name

  user_data = templatefile("${path.module}/scripts/github-runner.sh", {
    github_repo  = var.github_repo
    runner_name  = var.github_runner_name
    ca_secret   = var.ca_certificate_secret
    aws_region  = var.region
  })

  tags = { Name = "public-ec2" }
}

resource "aws_iam_role_policy_attachment" "private_ec2_ssm" {
  role       = aws_iam_role.private.name
  policy_arn = "arn:aws:iam::aws:policy/AmazonSSMManagedInstanceCore"
}


resource "aws_iam_role_policy_attachment" "public_ec2_ssm" {
  role       = aws_iam_role.public.name
  policy_arn = "arn:aws:iam::aws:policy/AmazonSSMManagedInstanceCore"
}


resource "aws_iam_role_policy" "vault-bootstrap-tls" {
  name = "vault-bootstrap-tls"
  role = aws_iam_role.private.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [{       
      "Sid": "VaultBootstrapTLS",
      "Effect": "Allow",
      "Action": [
        "secretsmanager:DescribeSecret",
        "secretsmanager:GetSecretValue"
      ],
      "Resource":["arn:aws:secretsmanager:eu-west-2:869868778582:secret:jjhealth-services/vault/bootstrap-tls*"]
    }]
  })
} 

resource "aws_iam_role_policy" "vault-aws-root-recovery-token-secrets-engine" {
  name = "vault-aws-root-recovery-token-secrets-engine"
  role = aws_iam_role.private.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [{       
      "Sid": "VaultRootAndRecoveryTokenSecretsManager",
      "Effect": "Allow",
      "Action": [
        "secretsmanager:DescribeSecret",
        "secretsmanager:GetSecretValue",
        "secretsmanager:CreateSecret",
        "secretsmanager:UpdateSecret",
        "secretsmanager:PutSecretValue",
        "secretsmanager:TagResource",
        "secretsmanager:GetResourcePolicy",
        "secretsmanager:DeleteSecret",
        "secretsmanager:RestoreSecret"
      ],
      "Resource":[ "arn:aws:secretsmanager:eu-west-2:869868778582:secret:jjhealth-services/vault/root-token*",
                  "arn:aws:secretsmanager:eu-west-2:869868778582:secret:jjhealth-services/vault/recovery-keys*"
      ]
    }]
  })
} 



resource "aws_iam_role_policy" "github-runner-pat" {
  name = "github-runner-pat"
  role = aws_iam_role.public.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [{       
      "Sid": "GitHubRunnerPAT",
      "Effect": "Allow",
      "Action": [
        "secretsmanager:DescribeSecret",
        "secretsmanager:GetSecretValue"
      ],
      "Resource":[ "arn:aws:secretsmanager:eu-west-2:869868778582:secret:github-runner-pat*",
              "arn:aws:secretsmanager:eu-west-2:869868778582:secret:jjhealth-services/vault/bootstrap-ca*",
              "arn:aws:secretsmanager:eu-west-2:869868778582:secret:jjhealth-services/vault/root-ca*"
      ]
    }]
  })
} 

resource "aws_iam_role_policy" "vault_aws_secrets_engine" {
  name = "vault-aws-secrets-engine"
  role = aws_iam_role.private.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [{
      Effect = "Allow"
      Action = [
        "iam:CreateUser",
        "iam:DeleteUser",
        "iam:CreateAccessKey",
        "iam:DeleteAccessKey",
        "iam:ListAccessKeys",
        "iam:AttachUserPolicy",
        "iam:PutUserPolicy",
        "iam:DeleteUserPolicy",
        "sts:AssumeRole"
      ]
      Resource = "*"
    }]
  })
} 



