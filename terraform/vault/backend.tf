terraform {
  backend "s3" {
    bucket = "jjhealth-services-1987"
    key    = "state/vault/terraform.tfstate"
    region = "eu-west-2"
    encrypt = true
    use_lockfile = true
  }
}




