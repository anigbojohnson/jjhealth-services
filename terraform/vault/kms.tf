resource "aws_kms_key" "vault_unseal" {
  description             = "Vault auto-unseal key"
  deletion_window_in_days = 10
  enable_key_rotation     = true

  tags = {
    Name    = "vault-unseal"
    Project = "jjhealth-services"
  }
}

resource "aws_kms_alias" "vault_unseal" {
  name          = "alias/vault-unseal"
  target_key_id = aws_kms_key.vault_unseal.key_id
}

resource "aws_iam_role_policy" "vault_kms_unseal" {
  name = "vault-kms-unseal"
  role = aws_iam_role.public.id

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



