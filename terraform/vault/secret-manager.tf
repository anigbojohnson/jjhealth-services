locals {
  vault_secrets = {
    root_token    = "jjhealth-services/vault/root-token-17"
    recovery_keys = "jjhealth-services/vault/recovery-keys-17"
  }
}


resource "aws_secretsmanager_secret" "vault" {
  for_each = local.vault_secrets

  name = each.value
}

resource "aws_secretsmanager_secret_version" "vault" {
  for_each = local.vault_secrets

  secret_id     = aws_secretsmanager_secret.vault[each.key].id
  secret_string = "placeholder"
}
