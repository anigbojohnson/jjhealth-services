locals {
  vault_secrets = {
    root_token    = "jjhealth-services/vault/root-token"
    recovery_keys = "jjhealth-services/vault/recovery-keys"
  }
}

resource "aws_secretsmanager_secret" "vault" {
  for_each = local.vault_secrets

  name = each.value

lifecycle {
    prevent_destroy = true
  }
}

