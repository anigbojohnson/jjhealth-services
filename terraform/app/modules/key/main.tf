resource "aws_key_pair" "client_key" {
    key_name = "jjhealth-service"
    public_key = file("../modules/key/jjhealth-service.pub")
}