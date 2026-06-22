resource "aws_key_pair" "client_key" {
    key_name = "vault-key"
    public_key = file("${path.module}/vault-key.pem")
}