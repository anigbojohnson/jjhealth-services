##############################################
# S3 bucket for the Ansible aws_ssm connection
# plugin's file transfers (module code only —
# not secrets, so objects expire after 1 day)
##############################################

data "aws_caller_identity" "current" {}
# NOTE: remove this data block if you already declare
# aws_caller_identity.current elsewhere in the codebase.

locals {
  ssm_transfer_bucket_name = "jjhealth-services-ssm-transfer-${data.aws_caller_identity.current.account_id}"
}

resource "aws_s3_bucket" "ssm_transfer" {
  bucket = local.ssm_transfer_bucket_name

  tags = {
    Name      = local.ssm_transfer_bucket_name
    Project   = "jjhealth-services"
    Purpose   = "ansible-aws-ssm-file-transfer"
    ManagedBy = "terraform"
  }
}

resource "aws_s3_bucket_public_access_block" "ssm_transfer" {
  bucket = aws_s3_bucket.ssm_transfer.id

  block_public_acls       = true
  block_public_policy     = true
  ignore_public_acls      = true
  restrict_public_buckets = true
}

resource "aws_s3_bucket_server_side_encryption_configuration" "ssm_transfer" {
  bucket = aws_s3_bucket.ssm_transfer.id

  rule {
    apply_server_side_encryption_by_default {
      sse_algorithm = "AES256"
    }
    bucket_key_enabled = true
  }
}

resource "aws_s3_bucket_lifecycle_configuration" "ssm_transfer" {
  bucket = aws_s3_bucket.ssm_transfer.id

  rule {
    id     = "expire-transfer-objects"
    status = "Enabled"

    filter {}

    expiration {
      days = 1
    }

    abort_incomplete_multipart_upload {
      days_after_initiation = 1
    }
  }
}

resource "aws_s3_bucket_versioning" "ssm_transfer" {
  bucket = aws_s3_bucket.ssm_transfer.id

  versioning_configuration {
    status = "Disabled"
  }
}

##############################################
# IAM policy granting access to the bucket.
# Attach this to whichever IAM role/group backs
# your Vault AWS secrets engine "terraform-role"
# mapping — the rotating iam_user credentials
# need this for ansible_connection: amazon.aws.aws_ssm
# to transfer module files.
##############################################

data "aws_iam_policy_document" "ssm_transfer_access" {
  statement {
    sid    = "SSMTransferBucketAccess"
    effect = "Allow"

    actions = [
      "s3:PutObject",
      "s3:GetObject",
      "s3:GetEncryptionConfiguration",
      "s3:DeleteObject",
    ]

    resources = [
      "${aws_s3_bucket.ssm_transfer.arn}/*",
    ]
  }

  statement {
    sid    = "SSMTransferBucketList"
    effect = "Allow"

    actions = [
      "s3:ListBucket",
    ]

    resources = [
      aws_s3_bucket.ssm_transfer.arn,
    ]
  }
}

resource "aws_iam_policy" "ssm_transfer_access" {
  name        = "jjhealth-services-ssm-transfer-access"
  description = "Allows file transfer to/from the S3 bucket used by the aws_ssm Ansible connection plugin"
  policy      = data.aws_iam_policy_document.ssm_transfer_access.json
}

# Example — point this at your existing Vault-backed IAM group/role resource:
# resource "aws_iam_group_policy_attachment" "ssm_transfer" {
#   group      = aws_iam_group.vault_terraform_group.name
#   policy_arn = aws_iam_policy.ssm_transfer_access.arn
# }




