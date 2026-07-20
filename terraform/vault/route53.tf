resource "aws_route53_zone" "private" {
  name = "internal"

  vpc {
    vpc_id = aws_vpc.main.id
  }
}

resource "aws_route53_record" "vault" {
  zone_id = aws_route53_zone.private.zone_id
  name    = "backed"
  type    = "A"
  ttl     = 300

  records = [
    aws_instance.private.private_ip
  ]
}


