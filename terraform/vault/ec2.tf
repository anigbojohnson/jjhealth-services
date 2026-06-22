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


# Bastion host SG — accepts SSH from the internet (or restrict to your IP)
resource "aws_security_group" "bastion" {
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
resource "aws_security_group" "ec2" {
  name        = "ec2-private-sg"
  description = "Security group for private EC2 instance"
  vpc_id      = aws_vpc.main.id

  ingress {
    description     = "SSH from bastion only"
    from_port       = 22
    to_port         = 22
    protocol        = "tcp"
    security_groups = [aws_security_group.bastion.id]  # SG reference, not CIDR
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = { Name = "ec2-private-sg" }
}

resource "aws_iam_role" "ec2" {
  name = "ec2-kms-role"
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
  role = aws_iam_role.ec2.id

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

resource "aws_iam_instance_profile" "ec2" {
  name = "ec2-kms-instance-profile"
  role = aws_iam_role.ec2.name
}


resource "aws_instance" "private" {
  ami                    = data.aws_ami.ubuntu.id
  instance_type          = var.instance_type
  subnet_id              = aws_subnet.private.id
  vpc_security_group_ids = [aws_security_group.ec2.id]
  iam_instance_profile   = aws_iam_instance_profile.ec2.name
  key_name = var.key_name
  tags = { Name = "private-ec2" }
}



resource "aws_instance" "public" {
  ami                    = data.aws_ami.ubuntu.id
  instance_type          = var.instance_type
  subnet_id              = aws_subnet.public.id
  vpc_security_group_ids = [aws_security_group.bastion.id]
  iam_instance_profile   = aws_iam_instance_profile.ec2.name
  key_name = var.key_name

  tags = { Name = "public-ec2" }
}

resource "aws_iam_role_policy_attachment" "ec2_ssm" {
  role       = aws_iam_role.ec2.name
  policy_arn = "arn:aws:iam::aws:policy/AmazonSSMManagedInstanceCore"
}

resource "aws_iam_role_policy" "vault_aws_secrets_engine" {
  name = "vault-aws-secrets-engine"
  role = aws_iam_role.ec2.id

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
      Resource = aws_iam_instance_profile.ec2.arn

    }]
  })
} 

