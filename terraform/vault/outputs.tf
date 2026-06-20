output "vpc_id" {
  value = aws_vpc.main.id
}

output "public_subnet_id" {
  value = aws_subnet.public.id
}

output "private_subnet_id" {
  value = aws_subnet.private.id
}

output "private_instance_id" {
  value = aws_instance.private.id
}

output "vault_kms_key_arn" {
  value = aws_kms_key.vault_unseal.arn
}

output "vault_kms_key_id" {
  value = aws_kms_key.vault_unseal.key_id
}

output "vault_secret_arns" {
  value = {
    for k, v in aws_secretsmanager_secret.vault :
    k => v.arn
  }
}

output "vault_instance_id" {
  value = aws_instance.private.id
}