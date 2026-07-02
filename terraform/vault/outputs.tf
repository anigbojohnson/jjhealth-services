output "vpc_id" {
  value = aws_vpc.main.id
}

output "public_subnet_id" {
  value = aws_subnet.public.id
}

output "private_subnet_id" {
  value = aws_subnet.private.id
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

output "vault_private_ip" {
   value = aws_instance.private.private_ip
}

output "ssm_transfer_bucket_name" {
  value       = aws_s3_bucket.ssm_transfer.id
  description = "Set this as ansible_aws_ssm_bucket_name in your inventory"
}

output "ssm_transfer_access_policy_arn" {
  value       = aws_iam_policy.ssm_transfer_access.arn
  description = "Attach this to the IAM role/group used by your Vault terraform-role mapping"
}


output "bastion_public_ip" {
  value = aws_instance.public.public_ip
}


