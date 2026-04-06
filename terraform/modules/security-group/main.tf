resource "aws_security_group" "alb_sg" {
  name        = "app-lb-sg"
  description = "Public access to app load balancer"
  vpc_id      = var.vpc_id


  ingress {
    description = "HTTPS from internet"
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }
  ingress {
    description = "HTTPS from internet"
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    description = "all outbound"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks       = ["0.0.0.0/0"]


  }
}

resource "aws_security_group" "web_sg" {
  name        = "alb security group"
  description = "enable http/https access on port 80/443"
  vpc_id      = var.vpc_id

  ingress {
    description = "http access"
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
     security_groups = [aws_security_group.alb_sg.id]
  }

  ingress {
    description = "https access"
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
     security_groups = [aws_security_group.alb_sg.id]
  }

  ingress {
    description = "ssh access"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    security_groups = [aws_security_group.alb_sg.id]
  }


  egress {
    description = "allow all outbound traffic"
    from_port   = 0
    to_port     = 0
    protocol    = -1
    cidr_blocks       = ["0.0.0.0/0"]

  }

  tags = {
    Name = "web_sg"
  }
}


# create security group for the Database
resource "aws_security_group" "app_sg" {
  name        = "app_sg"
  description = "Contain app logic "
  vpc_id      = var.vpc_id

  ingress {
    description     = "http access"
    from_port       = 80
    to_port         = 80
    protocol        = "tcp"
    security_groups = [aws_security_group.web_sg.id]
  }

    ingress {
    description = "https access"
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    security_groups = [aws_security_group.web_sg.id]
  }

  ingress {
    description     = "ssh access"
    from_port       = 22
    to_port         = 22
    protocol        = "tcp"
    security_groups = [aws_security_group.web_sg.id]
  }

  egress {
    description = "allow all outbound traffic"
    from_port   = 0
    to_port     = 0
    protocol    = -1
    cidr_blocks       = ["0.0.0.0/0"]

  }

  tags = {
    Name = "app_sg"
  }
}

# create security group for the Database
resource "aws_security_group" "db_sg" {
  name        = "db_sg"
  description = "enable mysql access on port 3305 from client-sg"
  vpc_id      = var.vpc_id

  ingress {
    description     = "psql access"
    from_port       = 5432
    to_port         = 5432
    protocol        = "tcp"
    security_groups = [aws_security_group.app_sg.id]
  }

  egress {
    description = "allow all outbound traffic"
    from_port   = 0
    to_port     = 0
    protocol    = -1
    cidr_blocks       = ["0.0.0.0/0"]

  }

  tags = {
    Name = "db_sg"
  }
}


resource "aws_security_group" "redis_sg" {
  name   = "redis-sg"
  vpc_id = var.vpc_id

  ingress {
    description     = "Allow Redis from app nodes"
    from_port       = 6379
    to_port         = 6379
    protocol        = "tcp"
    security_groups = [aws_security_group.app_sg.id] # 👈 restrict to EKS
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name = "redis-sg"
  }
}

resource "aws_security_group" "alb_sg_CKV2_AWS_5" {
  # checkov:skip=CKV2_AWS_5:SG attached to ALB in another module
  description = "checkov:skip=CKV2_AWS_5:SG attached to ALB in another module"

}

resource "aws_security_group" "web_sg_CKV2_AWS_5" {
  # checkov:skip=CKV2_AWS_5:Used by app_sg which is attached to EKS
  description = "checkov:skip=CKV2_AWS_5:Used by app_sg which is attached to EKS"

}

resource "aws_security_group" "app_sg_CKV2_AWS_5" {
  # checkov:skip=CKV2_AWS_5:Attached to EKS node group
  description = "checkov:skip=CKV2_AWS_5:Attached to EKS node group"
}

resource "aws_security_group" "db_sg_CKV2_AWS_5" {
  # checkov:skip=CKV2_AWS_5:Attached to RDS via vpc_security_group_ids
  description = "checkov:skip=CKV2_AWS_5:Attached to RDS via vpc_security_group_ids"

}
