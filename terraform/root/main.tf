data "aws_availability_zones" "available" {}

module "vpc" {
  source = "terraform-aws-modules/vpc/aws"
  version = "~> 5.0"

  name = "${var.project_name}-vpc"
  cidr = var.vpc_cidr

  azs             = slice(data.aws_availability_zones.available.names, 0, 2)
  public_subnets  = [var.pub_sub_1a_cidr, var.pub_sub_1b_cidr]
  private_subnets = [var.priv_sub_2a_cidr,var.priv_sub_2b_cidr,var.priv_sub_3a_cidr,var.priv_sub_3b_cidr]

  enable_nat_gateway = true

  single_nat_gateway = true

  enable_dns_hostnames = true
  enable_dns_support   = true

  tags = {
    Project = "${var.project_name}-vpc"
    Environment = terraform.workspace
  }
}

module "key" {
  source = "../modules/key"
}

module "security-group" {
  source = "../modules/security-group"
  vpc_id = module.vpc.vpc_id
}

/*
module "bastion_host" {
  source  = "terraform-aws-modules/ec2-instance/aws"
  version = "~> 5.0"

  name = "${var.project_name}-bastion"

  ami                    = var.ami_id
  instance_type          = "t3.micro"
  subnet_id              = element(module.vpc.public_subnets, 0)
  associate_public_ip_address = true

  key_name = module.key.key_name

  vpc_security_group_ids = [module.security-group.web_sg_id]

  user_data = templatefile("bastion-setup.sh.tpl", {
    aws_access_key_id     = var.aws_access_key_id
    aws_secret_access_key = var.aws_secret_access_key
    aws_region            = var.region
  })

  tags = {
    Name        = "${var.project_name}-bastion"
    Environment = terraform.workspace
    Project     = var.project_name
  }
}

*/


module "eks" {
  source = "terraform-aws-modules/eks/aws"
  version = "~> 20.24"
  cluster_name    = "${var.project_name}-cluster"
  cluster_version = "1.31"

  vpc_id     = module.vpc.vpc_id
  subnet_ids = slice(module.vpc.private_subnets, 0, 2)

  create_iam_role = true


  # Access entry (full admin)
  access_entries = {
    johnson = {
      principal_arn = "arn:aws:iam::869868778582:user/johnson"
      policy_associations = {
        admin = {
          policy_arn = "arn:aws:eks::aws:cluster-access-policy/AmazonEKSClusterAdminPolicy"
          access_scope = { type = "cluster" }
        }
      }
    }
  }

  eks_managed_node_groups = {
    app_nodes = {
      desired_size = 1
      max_size     = 1
      min_size     = 1
      instance_types = ["t2.medium"]
      capacity_type  = "ON_DEMAND"
      vpc_security_group_ids = [module.security-group.app_sg_id]
    }
  }

  enable_irsa = true
  cluster_endpoint_private_access = true
  cluster_endpoint_public_access  = true
  cluster_additional_security_group_ids = [module.security-group.app_sg_id]


  tags = { Project = "${var.project_name}-cluster" }
}


module "rds" {
  source  = "terraform-aws-modules/rds/aws"
  version = "~> 6.0"

  identifier        = "jjhealth-db"
  engine            = "postgres"
  engine_version    = "16"
  family            = "postgres16"
  instance_class    = "db.t3.micro"

  allocated_storage     = 20
  max_allocated_storage = 100

  db_name  = var.db_name
  username = "jjhealth_admin"

  # 🔐 Let AWS Secrets Manager handle the password
    manage_master_user_password = true

  port = 5432
  multi_az = false

  vpc_security_group_ids = [module.security-group.db_sg_id]

  subnet_ids             = slice(module.vpc.private_subnets, 2, 4)
  create_db_subnet_group = true

  publicly_accessible = false
  skip_final_snapshot = true

  tags = {
    Project = "${var.project_name}-rds"
  }
}


resource "aws_elasticache_subnet_group" "redis" {
  name       = "${var.project_name}-redis-subnet"
  subnet_ids = slice(module.vpc.private_subnets, 0, 2)

  tags = {
    Project = "${var.project_name}-redis"
  }
}

resource "aws_elasticache_replication_group" "redis" {
  replication_group_id = "${var.project_name}-redis"
  description          = "Redis cache for ${var.project_name}"

  engine               = "redis"
  engine_version       = "7.0"
  node_type            = "cache.t3.micro"

  port                 = 6379

  num_cache_clusters   = 2
  automatic_failover_enabled = true
  multi_az_enabled     = true

  subnet_group_name    = aws_elasticache_subnet_group.redis.name
  security_group_ids   = [module.security-group.redis_sg_id]

  # 🔐 Recommended for production
  at_rest_encryption_enabled = true
  transit_encryption_enabled = true

  tags = {
    Project = "${var.project_name}-redis"
  }
}















