output "private_subnets" {
  value = module.vpc.private_subnets
}

output "alb-id" {
  value = module.security-group.alb_sg_id
}
