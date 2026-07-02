variable "region" {
  description = "AWS region"
  type        = string
  default     = "eu-west-2"
}

variable "vpc_cidr" {
  description = "CIDR block for the VPC"
  type        = string
  default     = "10.0.0.0/16"
}

variable "public_subnet_cidr" {
  description = "CIDR block for the public subnet"
  type        = string
  default     = "10.0.1.0/24"
}

variable "private_subnet_cidr" {
  description = "CIDR block for the private subnet"
  type        = string
  default     = "10.0.2.0/24"
}

variable "instance_type" {
  description = "EC2 instance type for the private instance"
  type        = string
  default     = "t3.micro"
}

variable "key_name" {
  description = "key pair for ec2 instances at both private and public"
  type        = string
  default     = "vault-key"
}


variable "github_repo" {
  description = "GitHub repository URL"
  type        = string
  default     = "https://github.com/anigbojohnson/jjhealth-services"
}

variable "github_runner_name" {
  description = "GitHub Actions runner name"
  type        = string
  default     = "jjhealth-services-runner"
}

