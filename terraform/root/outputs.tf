output "private_subnets" {
  value = module.vpc.private_subnets
}

output "alb-id" {
  value = module.security-group.alb_sg_id
}

output "redis_primary_endpoint" {
  description = "Primary endpoint for Redis replication group"
  value       = aws_elasticache_replication_group.redis.primary_endpoint_address
}